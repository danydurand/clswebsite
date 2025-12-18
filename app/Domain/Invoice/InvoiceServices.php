<?php

namespace App\Domain\Invoice;

use App\Models\BillableItem;
use App\Models\Invoice;
use App\Classes\PResponse;
use App\Models\Consortium;
use App\Models\Supervisor;
use App\Models\InvoiceLine;
use Illuminate\Support\Str;
use App\Models\InvoiceConcept;
use Illuminate\Support\Carbon;
use App\Domain\User\UserTypeEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use LaravelDaily\Invoices\Invoice as PrintInvoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;

class InvoiceServices
{

    public static function queryRecords($user)
    {
        if (
            in_array($user->type, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            return Invoice::query();
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            $consortiumId = $user->consortium_id;
            return Invoice::byConsortium($consortiumId);
        }
        return Invoice::query()->where('id', '<', 0)
            ->take(1);
    }


    public static function print(Collection $invoices)
    {
        info('Getting into InvoiceServices::print');
        info('===================================');

        $ids = $invoices->pluck('id');
        info('There are: ' . count($ids) . ' invoices to be printed');

        //-----------------
        // Company's Data
        //-----------------
        $company = new Buyer([
            'name' => 'DreamBet Company',
            'address1' => 'DreamBet Address 1',
            'address2' => 'DreamBet Address 2',
            'phone' => 'DreamBet Phone',
            'custom_fields' => [
                'website' => 'DreamBet Website',
            ],
        ]);
        $strTempName = 'default';
        $strLogoName = 'img/DBLogo_BluePurple.png';


        foreach ($invoices as $record) {
            info('Printing: ' . $record->reference);
            //---------------
            // Customer data
            //---------------
            $consortium = $record->consortium;
            $customer = new Buyer([
                'name' => $record->business_name . ' (' . $record->business_number . ') ',
                'address1' => $record->business_address,
                'address2' => '',
                'phone' => $record->business_phone,
            ]);
            //---------------------
            // Invoice items data
            //---------------------
            $items = [];
            foreach ($record->invoiceLines()->get() as $line) {
                $items[] = (new InvoiceItem())
                    ->title($line->description)
                    ->pricePerUnit($line->amount);
            }
            // $notes = [
            //     '<b>Zelle:</b> '.SS::getValue('ZELLE-ADDRESS', 'ZELLE-ADDRESS'),
            //     '<b>Bank:</b> '.SS::getValue('BANK', 'BANK'),
            //     '<b>Beneficiary:</b> '.SS::getValue('BENEFICIARY', 'BENEFICIARY'),
            //     '<b>Account Number:</b> '.SS::getValue('ACCOUNT-NUMBER', 'ACCOUNT-NUMBER'),
            //     '<b>SWIFT:</b> '.SS::getValue('SWIFT', 'SWIFT'),
            //     '<b>ABA/Ruta:</b> '.SS::getValue('ABA-ROUTE', 'ABA-ROUTE'),
            //     '<b>Beneficiary Address:</b> '.SS::getValue('BENEFICIARY-ADDRESS', 'BENEFICIARY-ADDRESS').'<br><br>',
            //     '“<u>Shipments will be scheduled according to the order of payments received.</u>”.',
            // ];
            // $notes = implode("<br>", $notes);

            $strFileName = 'invoice_' . $record->reference;

            $invoice = PrintInvoice::make()
                ->template($strTempName)
                ->serialNumberFormat($record->reference)
                ->dateFormat($record->created_at->format('Y-m-d'))
                ->seller($company)
                ->buyer($customer)
                ->addItems($items)
                ->status($record->invoice_status->getLabel())
                ->logo(public_path($strLogoName))
                // ->notes($notes)
                ->currencySymbol('USD')
                ->currencyCode('USD')
                // ->setCustomData([
                //     'awb'     => Awb::where('invoice_id',$record->id)->first(),
                //     'company' => $company,
                //     'invoice' => $record,
                // ])
                ->filename($strFileName)
                // ->save();
                // ->save('local');
                // ->save('storage');
                ->save('public');

            $link = $invoice->url();
            info('Invoice Link: ' . $link);

            return $invoice->url();
        }
    }

    public static function generateInvoices(array $consortiumIds, int $year, int $month): PResponse
    {
        $procName = 'Generate-Invoices: ' . date('Y-m-d H:i:s');
        $response = new PResponse(processName: $procName);

        DB::select("call spu_fill_billable_items($year, $month)");

        $consortiums = Consortium::query()->whereIn('id', $consortiumIds)->get();
        $qtyInvoices = 0;
        foreach ($consortiums as $consortium) {
            $billableItemsCount = DB::table('billable_items')
                ->where('consortium_id', $consortium->id)
                ->where('billable_year', $year)
                ->where('billable_month', $month)
                ->count();
            if ($billableItemsCount == 0) {
                $response->userMess = 'No billable items found for consortium ' . $consortium->name . ' in year ' . $year . ' and month ' . $month;
                info($response->userMess);
                continue;
            }
            info('The Consortium: ' . $consortium->name . ' has ' . $billableItemsCount . ' billable items');
            $invPayCond = $consortium->invoicePaymentCondition;
            $dueDate = now()->addDays($invPayCond->credit_days);
            try {
                DB::beginTransaction();
                //-----------------
                // Create invoice
                //-----------------
                $invoice = Invoice::create([
                    'consortium_id' => $consortium->id,
                    'year' => $year,
                    'month' => $month,
                    'total_amount' => 0,
                    'paid_amount' => 0,
                    'reference' => self::generateReference(),
                    'business_number' => $consortium->business_number,
                    'business_name' => $consortium->name,
                    'business_address' => $consortium->address,
                    'business_phone' => $consortium->phone,
                    'invoice_status' => InvoiceStatusEnum::Pending->value,
                    'invoice_payment_condition_id' => $invPayCond->id,
                    'due_date' => $dueDate,
                    'totally_paid_at' => null,
                    'is_sent_by_email' => false,
                    'sent_by_email_at' => null,
                    'country_id' => $consortium->country_id,
                ]);
                //-----------------------------
                // Creating the invoice lines
                //-----------------------------
                $concepts = InvoiceConcept::active()
                    ->fix()
                    ->byCountry($consortium->country_id)
                    ->get();
                foreach ($concepts as $concept) {
                    $deepResponse = self::handleConcept($concept, $consortium, $invoice);
                    if (!$deepResponse->indiOkey) {
                        Log::error($deepResponse->userMess);
                    }
                }
                info('Updating the total invoice');
                DB::select("call spu_update_invoice_total($invoice->id)");
                $invoice->refresh();
                //-------------------------------------------------------------------------------
                // If the invoice has a total amount of 0, then the invoice we will not save it
                //-------------------------------------------------------------------------------
                if ($invoice->total_amount > 0) {
                    DB::commit();
                    $qtyInvoices++;
                } else {
                    DB::rollBack();
                }
            } catch (\Error $e) {
                DB::rollBack();
                $response->userMess = 'Error: ' . $e->getMessage();
                $response->indiOkey = false;
                $response->qtyErrors++;
            }
        }

        $response->qtyProcessed = $qtyInvoices;
        $response->userMess = $qtyInvoices . ' Invoices generated';

        $response->close();

        return $response;
    }

    public static function handleConcept(InvoiceConcept $concept, Consortium $consortium, Invoice $invoice): PResponse
    {
        info('');
        info('InvoiceServices::handelConcept');
        info('==============================');

        info('Concept: ' . $concept->name);

        $response = new PResponse();

        try {
            $deepResponse = self::calculateConcept($concept, $consortium, $invoice);
            // $deepResponse = self::feePerBank($invoice);

            info('This comes from the concept calculation ' . $deepResponse);

            $amount = $deepResponse->getData('conceptAmount');

            info('The amount is: ' . $amount);

            if ($amount > 0) {
                info('Registering the invoice lines...');
                $invoice->invoiceLines()->save(new InvoiceLine([
                    'invoice_concept_id' => $concept->id,
                    'description' => $concept->showing_description,
                    'amount' => $amount,
                ]));
            }
        } catch (\Exception $e) {
            $response->indiOkey = false;
            $response->userMess = 'Calculating ' . $concept->name . ': ' . $e->getMessage();
            Log::error($response->userMess);
        }

        return $response;
    }


    public static function calculateConcept(InvoiceConcept $concept, Consortium $consortium, Invoice $invoice): PResponse
    {

        info('');
        info('InvoiceService::calculateConcept');
        info('================================');

        $response = new PResponse();

        $conceptAmount = 0;
        $calcExplanation = '';

        info('Calculating: ' . $concept->name);
        $conceptId = $concept->id;
        info('The concept id is: ' . $conceptId);
        //--------------------------------------------------------------------------------------------
        // If the concept has a previous condition that needs to be considered, here we evaluate it
        //--------------------------------------------------------------------------------------------
        $strMetoCond = trim($concept->condition);
        if (strlen($strMetoCond) > 0) {
            info('Condition to calculate the concept: ' . $strMetoCond);
            if (!self::$strMetoCond()) {
                info('Condition not true');
                $calcExplanation = 'Condition not true: ' . $strMetoCond;
                $response->setData('conceptAmount', $conceptAmount);
                $response->setData('calcExplanation', $calcExplanation);
                return $response;
            } else {
                info('Condition true');
            }
        }
        //---------------------------------------------------------------------
        // The "value" of the concept indicates how the amount is calculated
        //---------------------------------------------------------------------
        // if (is_numeric($concept->value)) {
        //     info('It is a numeric value');
        //     $decBaseAmou = self::find_concept_amount($concept, $awb);
        //     info('The value ' . $concept->type . ' is: ' . $decBaseAmou);
        //     list($conceptAmount, $calcExplanation) = self::apply_as_qty_percent($concept, $decBaseAmou);
        // }
        if ($concept->value == 'METHOD') {
            info('It is a method');
            $strMethName = trim($concept->method_name);
            if (!empty($strMethName)) {
                try {
                    info("Calculating method: $strMethName");
                    $deepResponse = self::$strMethName($invoice);

                    info('deepResponse: ' . $deepResponse);

                    $conceptAmount = $deepResponse->getData('conceptAmount');
                    $calcExplanation = $deepResponse->getData('calcExplanation');
                } catch (\Error $e) {
                    Log::error("Error:" . $e->getMessage());
                    Log::error("The method $strMethName does not exist.");
                    $calcExplanation = "Indefined method: $strMethName";
                    $response->qtyErrors = 1;
                }
            } else {
                Log::error("The method $strMethName has not been declared.");
                $conceptAmount = 0;
                $calcExplanation = "Method $strMethName... no declared";
                $response->qtyErrors = 1;
            }
        }
        info('Getting out, after calculate the concept...');

        $response->setData('conceptAmount', $conceptAmount);
        $response->setData('calcExplanation', $calcExplanation);

        return $response;
    }

    public static function feePerBank(Invoice $invoice): PResponse
    {

        info('');
        info('InvoiceService::feePerBank');
        info('==========================');

        $response = new PResponse();

        $consortiumId = $invoice->consortium_id;
        // $consortium = Consortium::find($consortiumId);
        $consortium = $invoice->consortium;
        if (!$consortium) {
            $response->indiOkey = false;
            $response->userMess = 'Consortium not found';
            Log::error('Consortium with Id: ' . $consortiumId . ' not found');
            return $response;
        }
        $bankIds = $consortium->billableItems()
            ->where('billable_year', $invoice->year)
            ->where('billable_month', $invoice->month)
            ->whereNull('invoice_id')
            ->pluck('bank_id');
        try {
            BillableItem::whereIn('bank_id', $bankIds)
                ->update(['invoice_id' => $invoice->id]);
            info('Billable items updated');
            $qtyBanks = $bankIds->count();
            $rate = $consortium->rate;
            info('Rate: ' . $rate->name);
            $feePerBank = $rate->price * $qtyBanks;
            info('The fee per bank is: ' . $rate->price . '. Qty Banks: ' . $qtyBanks . ' Result: ' . $feePerBank);
            $response->setData('conceptAmount', $feePerBank);
            $response->setData('calcExplanation', 'Qty Active Banks: ' . $qtyBanks . ' x ' . $rate->price . ' Result: ' . $feePerBank);
        } catch (\Error $e) {
            $response->qtyErrors = 1;
            $response->indiOkey = false;
            $response->userMess = 'Error updating billable items: ' . $e->getMessage();
            Log::error($response->userMess);
        }

        info('Getting out of feePerBank with: ' . $response);

        return $response;
    }

    public static function generateReference(): string
    {
        $next = Invoice::query()->count() + 1;
        return 'INV' . '-' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    public static function nullify(Collection $invoices, string $nullReason, int $userId): PResponse
    {
        $processName = 'Nullify-Invoices: ' . date('Y-m-d H:i:s');
        $response = new PResponse(processName: $processName);

        $invoiceIds = $invoices->pluck('id');
        // info('');
        // info('Getting into InvoiceServices::nullify, with: '.count($invoiceIds).' records');
        $qtyNull = 0;
        $qtyCrdn = 0;

        if (count($invoiceIds)) {
            $deepResponse = self::nullifyInvoices($invoiceIds, $nullReason, $userId);
            $nullInvoices = $deepResponse->getData('nullInvoices');
            $liveInvoices = $deepResponse->getData('liveInvoices');
            info('Nullified: ' . count($nullInvoices) . ' Live: ' . count($liveInvoices));
            $qtyNull = count($nullInvoices);
            self::breakRelationWithBillableItems(collect($nullInvoices));
            // if (count($liveInvoices)) {
            // info('Reversing the live Invoices with a CRN');
            //---------------------------------------------------------------------------------
            // Live invoices are those that have payments and as result couldn't be nullified
            //---------------------------------------------------------------------------------
            // $reversedInvoices = self::markAsReversed($liveInvoices);
            // $qtyCrdn = CreditNoteServices::createForPaidInvoices($reversedInvoices);
            // }
        }
        $response->userMess = $qtyNull . ' nullified ';
        $response->setData('qtyNull', $qtyNull);
        $response->setData('qtyCrdn', $qtyCrdn);
        $response->close();

        return $response;
    }

    public static function breakRelationWithBillableItems(?Collection $invoiceIds): int
    {
        info('Breaking the relation between the Invoice and the Billable Items');
        //--------------------------------------------------
        // Breaking the relation with the Billable Items
        //--------------------------------------------------
        if (is_null($invoiceIds)) {
            info('No invoice ids');
            return 0;
        }
        return BillableItem::whereIn('invoice_id', $invoiceIds)
            ->update(['invoice_id' => null]);
    }


    public static function nullifyInvoices(Collection $invoiceIds, string $nullReason, int $userId): PResponse
    {
        $response = new PResponse();
        //------------------------------------------------------------------------
        // Nullifying the invoices not previously nullified and with no payments
        //------------------------------------------------------------------------
        Invoice::whereIn('id', $invoiceIds)
            ->where('invoice_status', '!=', InvoiceStatusEnum::Nullified->value)
            ->where('paid_amount', 0)
            ->update([
                'nullified_by' => $userId,
                'nullified_motive' => strtoupper($nullReason),
                'nullified_at' => Carbon::now(),
                'invoice_status' => InvoiceStatusEnum::Nullified->value,
            ]);
        //--------------------------------------------
        // Getting the actually nullified invoices
        //--------------------------------------------

        $nullInvoices = Invoice::whereIn('id', $invoiceIds)
            ->where('invoice_status', InvoiceStatusEnum::Nullified->value)
            ->pluck('id');

        $liveInvoices = Invoice::whereIn('id', $invoiceIds)
            ->where('paid_amount', '>', 0)
            ->pluck('id');

        $response->setData('nullInvoices', $nullInvoices);
        $response->setData('liveInvoices', $liveInvoices);

        return $response;
    }


}
