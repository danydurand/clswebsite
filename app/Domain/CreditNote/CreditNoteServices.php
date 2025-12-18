<?php

namespace App\Domain\CreditNote;

use App\Models\BillableItem;
use App\Models\CreditNote;
use App\Models\Invoice;
use App\Classes\PResponse;
use App\Models\Consortium;
use App\Models\InvoicePayment;
use App\Models\Supervisor;
use App\Models\InvoiceLine;
use Illuminate\Support\Str;
use App\Models\InvoiceConcept;
use Illuminate\Support\Carbon;
use App\Domain\User\UserTypeEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreditNoteServices
{

    public static function generateReference(): string
    {
        $next = CreditNote::query()->count() + 1;
        return 'CRN'.'-'.str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    public static function createFromPayment(InvoicePayment $payment, int $amount): CreditNote
    {

        info('Creating a Credit Note from a Payment');

        $creditNote = CreditNote::create([
            'consortium_id'      => $payment->consortium_id,
            'reference'          => self::generateReference(),
            'type'               => CreditNoteTypeEnum::Automatic,
            'description'        => 'Excess Amount from Payment: '.$payment->reference,
            'amount'             => $amount,
            'used_amount'        => 0,
            'status'             => CreditNoteStatusEnum::Available,
            'invoice_payment_id' => $payment->id,
        ]);
        info('NDC created...');

        return $creditNote;
    }

    public static function updateBalance(CreditNote $creditNote, InvoicePayment $payment, string $operation)
    {
        info('Getting into CreditNoteServices::updateBalance');
        info('==============================================');
        if ($operation == 'do') {
            info('Using the Credit Note as a payment method. The balance is: '.$creditNote->balance);
            //-------------------------------------------------------------------------
            // It means we are using the Credit Note as a payment method in a Payment
            //-------------------------------------------------------------------------
            $auxiAmount = bcsub($creditNote->balance, $payment->amount, 2);
            info('Auxi Amount is: '.$auxiAmount);
            $diffAmount = bccomp($auxiAmount, 0, 2);
            $usedAmount = in_array($diffAmount,[0,1]) ? $payment->amount : $creditNote->balance;
            info('The used amount will be increased with: '.$usedAmount);
            $creditNote->used_amount = bcadd($creditNote->used_amount, $usedAmount, 2);
            info('The used amount now is: '.$creditNote->used_amount);
            $creditNote->description = trim($creditNote->description).' | Used on '.$payment->data;
            info('The description is: '.$creditNote->description);
        } else {
            info('Reverting a payment that was done with the Credit Note');
            //-----------------------------------------------------------------------
            // Undo: It means, we are reverting a payment done with the Credit Note
            //-----------------------------------------------------------------------
            info('The used amount will be increased with: '.$payment->amount);
            $creditNote->used_amount = bcadd($creditNote->used_amount, $payment->amount, 2);
            info('The used amount now is: '.$creditNote->used_amount);
            $creditNote->description = trim($creditNote->description).' | Was used on '.$payment->data;
            info('The description is: '.$creditNote->description);
        }
        if ($creditNote->used_amount == 0) {
            info('The used amount is zero, the credit note left "Available"');
            $creditNote->status = CreditNoteStatusEnum::Available;
        } else {
            if ($creditNote->balance > 0) {
                info('The credit note have been used but still has a positive balance.  The credito note left "Partially Used"');
                $creditNote->status = CreditNoteStatusEnum::PartiallyUsed;
            } else {
                info('There is no positive balance.  The credito note left "Used"');
                $creditNote->status = CreditNoteStatusEnum::Used;
            }
        }
        $creditNote->save();
        info('Credit Note updated');
    }


}
