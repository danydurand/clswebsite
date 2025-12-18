<?php

namespace App\Domain\InvoicePayment;

use App\Classes\PResponse;
use App\Models\CreditNote;
use App\Models\InvoicePayment;
use Illuminate\Support\Carbon;
use App\Domain\User\UserTypeEnum;
use Illuminate\Support\Facades\DB;
use App\Domain\Ticket\PaymentStatusEnum;
use App\Domain\Invoice\InvoiceStatusEnum;
use App\Domain\CreditNote\CreditNoteServices;
use App\Domain\CreditNote\CreditNoteStatusEnum;

class InvoicePaymentServices
{

    public static function queryRecords($user)
    {
        if (
            in_array($user->type, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            return InvoicePayment::query();
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            $consortiumId = $user->consortium_id;
            return InvoicePayment::byConsortium($consortiumId);
        }
        return InvoicePayment::query()->where('id', '<', 0)
            ->take(1);
    }


    public static function generateReference(): string
    {
        $seq = InvoicePayment::query()->count() + 1;
        return 'PYM-' . str_pad($seq, 6, '0', STR_PAD_LEFT);
    }

    public static function updatePaidAmount(int $paymentId): void
    {
        DB::statement("CALL spu_update_paid_amount(?)", [$paymentId]);
    }


    public static function approvePayment(InvoicePayment $payment): PResponse
    {
        // dd($payment);

        $response = new PResponse();

        info('');
        info('InvoicePaymentServices::approvePayment');
        info('======================================');

        $creditNote = null;
        $payAmount = $payment->amount;
        info('The Payment Amount: ' . $payAmount);
        //------------------------------------------------
        // Related invoices' paid amount must be updated
        //------------------------------------------------
        $paymentDetails = $payment->invoicePaymentDetails();
        DB::beginTransaction();
        try {
            info('The Payment has: ' . $payment->invoicePaymentDetails()->count() . ' details');
            $paymentDetails->each(function ($detail) use (&$payAmount, &$response) {
                $invoice = $detail->invoice;
                info('The detail payment amount: ' . $detail->amount);
                $payAmount -= $detail->amount;
                info('After substrac the debt of the Invoice, the payment amount is: ' . $payAmount);
                $invoice->paid_amount += $detail->amount;
                if ($invoice->paid_amount > $invoice->total_amount) {
                    $difference = $invoice->paid_amount - $invoice->total_amount;
                    $invoice->paid_amount = $invoice->total_amount;
                    $payAmount += $difference;
                    info('The paid amount is greater than the total amount.  The difference is: ' . $difference);
                    info('The payment amount is: ' . $payAmount);
                }
                $invoice->save();
                $invoice->refresh();
                info('The invoice paid_amount is: ' . $invoice->paid_amount . ' and the pending_amount is: ' . $invoice->pending_amount);
                if ($invoice->pending_amount == 0) {
                    info('Invoice totally paid');
                    $invoice->update([
                        'invoice_status' => InvoiceStatusEnum::Paid->value,
                        'totally_paid_at' => Carbon::now(),
                    ]);
                } else {
                    info('Invoice Status: PartiallyPaid');
                    $invoice->update(['invoice_status' => InvoiceStatusEnum::PartiallyPaid->value]);
                }
                $response->qtyProcessed++;
            });
            //-----------------------------------------------------------------------------
            // If at the end, there is an excess amount, we need to create a Credit Note.
            // This applies when the payment method is not already a Credit Note
            //-----------------------------------------------------------------------------
            $creditNoteMess = '';
            if ($payment->invoicePaymentMethod->code != 'CRN') {
                info('After processing all the invoice the payment amount is: ' . $payAmount);
                if ($payAmount > 0) {
                    info('There is an excedent. We need to create a Credit Note');
                    $creditNote = CreditNoteServices::createFromPayment($payment, $payAmount);
                    $creditNoteMess = __('There is an excedent amount. A new credit note was created') . ' (' . $creditNote->reference . ')';
                }
            } else {
                info('The payment method is a Credit Note. The payment amount is: ' . $payAmount);
                $creditNote = CreditNote::findByReference($payment->document);
                CreditNoteServices::updateBalance($creditNote, $payment, 'do');
            }
            //----------------------------
            // Updating Customer Balance
            //----------------------------
            // CustomerServices::updateBalance();
            $payment->update([
                'payment_status' => InvoicePaymentStatusEnum::Approved->value,
                'excess_amount' => $payAmount,
            ]);
            DB::commit();
            $response->userMess = __('The payment has been approved.') . ' ' . $creditNoteMess;
        } catch (\Exception $e) {
            DB::rollBack();
            $response->qtyErrors = 1;
            $response->indiOkey = false;
            $response->userMess = __('Approving the payment: ' . $e->getMessage());
        }

        return $response;
    }

    public static function deleting(InvoicePayment $payment, ?int $paymentDetailId): PResponse
    {
        $response = new PResponse();

        info('');
        info('InvoicePaymentServices::deleting');
        info('================================');

        $savedStatus = $payment->payment_status->value;
        if (!is_null($paymentDetailId)) {
            $paymentDetails = $payment->invoicePaymentDetails()->where('id', $paymentDetailId);
        } else {
            $paymentDetails = $payment->invoicePaymentDetails();
        }
        $paymentDetails->each(function ($detail) use ($savedStatus, &$response) {
            info('Deleting a Payment that was: ' . $savedStatus);
            if ($savedStatus == InvoicePaymentStatusEnum::Approved->value) {
                info('Was Approved so I need to update the related invoices');
                //-----------------------------------------------
                // Related invoices' paid amount must be updated
                //-----------------------------------------------
                $invoice = $detail->invoice;
                $invoice->paid_amount = bcsub($invoice->paid_amount, $detail->amount, 2);
                $invoice->save();
                $invoice->refresh();
                if (bccomp($invoice->paid_amount, 0, 2) == 0) {
                    info('Paid Amount is 0.  Invoice Status: Pending');
                    $invoice->update([
                        'invoice_status' => InvoiceStatusEnum::Pending->value,
                        'totally_paid_at' => null,
                    ]);
                } else {
                    info('There is some Paid Amount.  Invoice Status: PartiallyPaid');
                    $invoice->update([
                        'invoice_status' => InvoiceStatusEnum::PartiallyPaid,
                        'totally_paid_at' => null,
                    ]);
                }
            }
            info('Deleting detail');
            $detail->delete();
            $response->qtyProcessed++;
        });
        //--------------------------------------------------------------------------------------
        // If the payment method was a Credit Note, we need to revert the original transaction
        //--------------------------------------------------------------------------------------
        // if ($payment->by_credit_note) {
        //     $creditNote = CreditNote::findByReference($payment->document);
        //     CreditNoteServices::updateBalance($creditNote, $payment, 'undo');
        //     $userMess = __('Credit note reverted !!');
        // }
        //----------------------------
        // Updating Customer Balance
        //----------------------------
        // CustomerServices::updateBalance();

        return $response;
    }

    public static function revertApprovedPayment(InvoicePayment $payment): PResponse
    {
        $response = new PResponse();

        info('');
        info('InvoicePaymentServices::revertApprovedPayment');
        info('============================================');

        //-----------------------------------------
        // Only approved payments can be reverted
        //-----------------------------------------
        if ($payment->payment_status->value !== InvoicePaymentStatusEnum::Approved->value) {
            $response->indiOkey = false;
            $response->userMess = __('Only approved payments can be reverted');
            return $response;
        }
        //-----------------------------------------------------
        // If there is a Credit Note, it must be not used yet
        //-----------------------------------------------------
        $description = 'Excess Amount from Payment: ' . $payment->reference;
        $creditNote = CreditNote::where('description', $description)
            ->where('invoice_payment_id', $payment->id)
            ->first();
        if ($creditNote) {
            if ($creditNote->status->value != CreditNoteStatusEnum::Available->value) {
                $response->indiOkey = false;
                $response->userMess = __('The related Credit Note was already used. The payment cannot be reverted');
                return $response;
            }
        }

        DB::beginTransaction();
        try {
            $paymentDetails = $payment->invoicePaymentDetails();
            info('The Payment has: ' . $paymentDetails->count() . ' details to revert');

            $paymentDetails->each(function ($detail) use (&$response) {
                $invoice = $detail->invoice;
                info('Reverting payment of ' . $detail->amount . ' for invoice ' . $invoice->reference);

                // Subtract the paid amount
                $invoice->paid_amount -= $detail->amount;
                if ($invoice->paid_amount < 0) {
                    $invoice->paid_amount = 0;
                }
                $invoice->save();
                $invoice->refresh();

                info('The invoice paid_amount is now: ' . $invoice->paid_amount . ' and the pending_amount is: ' . $invoice->pending_amount);

                // Update invoice status
                if ($invoice->paid_amount == 0) {
                    info('Invoice Status: Pending');
                    $invoice->update([
                        'invoice_status' => InvoiceStatusEnum::Pending->value,
                        'totally_paid_at' => null,
                    ]);
                } else {
                    info('Invoice Status: PartiallyPaid');
                    $invoice->update([
                        'invoice_status' => InvoiceStatusEnum::PartiallyPaid->value,
                        'totally_paid_at' => null,
                    ]);
                }

                $response->qtyProcessed++;
            });

            // Handle credit note if applicable
            if ($payment->invoicePaymentMethod->code == 'CRN') {
                info('The payment method is a Credit Note. Reverting credit note balance.');
                $creditNote = CreditNote::findByReference($payment->document);
                CreditNoteServices::updateBalance($creditNote, $payment, 'undo');
            } else {
                // Check if a credit note was created from excess payment
                $description = 'Excess Amount from Payment: ' . $payment->reference;
                $creditNote = CreditNote::where('description', $description)
                    ->where('invoice_payment_id', $payment->id)
                    ->first();

                if ($creditNote) {
                    info('Reverting credit note created from excess payment: ' . $creditNote->reference);
                    // If the credit note has been used, we can't revert the payment
                    if ($creditNote->used_amount > 0) {
                        DB::rollBack();
                        $response->indiOkey = false;
                        $response->userMess = __('Cannot revert payment because the generated credit note has been used');
                        return $response;
                    }

                    // Delete the credit note if it hasn't been used
                    $payment->excess_amount = 0;
                    $payment->save();
                    $creditNote->delete();
                    info('Credit note deleted: ' . $creditNote->reference);
                }
            }

            // Update payment status
            $payment->update(['payment_status' => InvoicePaymentStatusEnum::WaitingValidation->value]);

            DB::commit();
            $response->userMess = __('The payment approval has been reverted');
        } catch (\Exception $e) {
            DB::rollBack();
            $response->qtyErrors = 1;
            $response->indiOkey = false;
            $response->userMess = __('Reverting the payment approval: ' . $e->getMessage());
        }

        return $response;
    }


}
