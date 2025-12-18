<?php

namespace App\Domain\Loan;

use App\Models\Bank;
use App\Models\Loan;
use App\Models\Group;
use App\Models\Route;
use App\Classes\PResponse;
use App\Models\LoanDetail;
use App\Models\Supervisor;
use App\Models\Transaction;
use App\Domain\User\UserTypeEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Domain\LoanDetail\FeeStatusEnum;
use App\Domain\Transaction\TransactionTypeEnum;


class LoanServices
{

    public static function updateLoanStatus(Loan $loan)
    {
        $feesCount = $loan->fees_count;
        $pendCount = $loan->pending_fees_count;
        $paidCount = $loan->paid_fees_count;

        if ($paidCount == $feesCount) {
            $loan->status = LoanStatusEnum::Paid->value;
            $loan->save();
            return;
        }
        if (($paidCount > 0) && ($pendCount > 0)) {
            $loan->status = LoanStatusEnum::InProgress->value;
            $loan->save();
            return;
        }
        if ($pendCount > 0) {
            $loan->status = LoanStatusEnum::Pending->value;
            $loan->save();
            return;
        }
    }


    public static function calcFeeAmount($data)
    {
        $amount = $data['amount'];
        $qtyFees = $data['qty_fees'];
        $percentage = $data['percentage'];

        $feeAmount = 0;
        if (empty($percentage)) {
            $percentage = 0;
        }
        if ($amount > 0 && $qtyFees > 0) {
            $percentage = $percentage / 100;
            $interest = $amount * $percentage;
            $amount += $interest;
            $feeAmount = $amount / $qtyFees;
        }

        return $feeAmount;
    }

    public static function queryRecords($user)
    {
        if (
            in_array($user->type, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            return Loan::query();
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            $bankIds = Bank::byConsortium($user->consortium_id)->pluck('id');
            return Loan::query()
                ->whereIn('bank_id', $bankIds);
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            $bankIds = Bank::bySupervisor($user->id)->pluck('id');
            return Loan::query()
                ->whereIn('bank_id', $bankIds);
        }
        return [];
    }

    public static function createLoanAndFees(array $data): PResponse
    {
        info('');
        info('LoanServices::createLoanAndFees');
        info('===============================');

        info('data: ' . json_encode($data));

        $response = new PResponse();

        DB::beginTransaction();
        try {
            $loan = Loan::create([
                'route_id' => $data['route_id'],
                'bank_id' => $data['bank_id'],
                'amount' => $data['amount'],
                'qty_fees' => $data['qty_fees'],
                'percentage' => $data['percentage'],
                'fee_amount' => $data['fee_amount'],
                'frequency' => $data['frequency'],
                'status' => LoanStatusEnum::Pending->value,
            ]);

            $result = self::createFees($loan);

            if (!$result->indiOkey) {
                $response->indiOkey = false;
                $response->qtyErrors = $result->qtyErrors;
                $response->userMess = $result->userMess;
                DB::rollback();
                return $response;
            }

            $qty = $loan->loanDetails()->count();
            $response->qtyProcessed = $qty;
            $response->userMess = 'Loan generated with (' . $qty . ') fees';
            DB::commit();
        } catch (\Error $e) {
            DB::rollback();
            Log::error($e->getMessage());
            $response->indiOkey = false;
            $response->qtyErrors = 1;
            $response->userMess = $e->getMessage();
        }

        return $response;
    }

    public static function createFees(Loan $loan): PResponse
    {
        $response = new PResponse();

        $fees = [];
        $qtyDays = $loan->frequency->value;
        for ($i = 0; $i < $loan->qty_fees; $i++) {
            $collectionDate = $loan->created_at->addDays($qtyDays * ($i + 1));
            $fees[] = [
                'loan_id' => $loan->id,
                'amount' => $loan->fee_amount * 100,
                'collection_date' => $collectionDate->format('Y-m-d'),
                'status' => FeeStatusEnum::Pending->value,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        LoanDetail::insert($fees);

        return $response;
    }
}
