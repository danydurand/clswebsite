<?php

namespace App\Domain\BankExpense;

use App\Models\Bank;
use App\Models\User;
use App\Models\Seller;
use App\Models\Concept;
use App\Classes\PResponse;
use App\Models\BankExpense;
use App\Models\BankLottery;
use Illuminate\Support\Carbon;
use App\Domain\User\UserTypeEnum;
use App\Models\ConsortiumLottery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Domain\BankExpense\ExpenseStatusEnum;

class BankExpenseServices
{


    public static function createExpenses($data, Bank $bank): PResponse
    {
        $response = new PResponse();

        $concept = Concept::find($data['concept_id']);
        $amount = $data['amount'];
        $qty = $data['qty'];

        $qtyBefore = $bank->expenses()->count();
        $expenses = [];
        for ($i = 0; $i < $qty; $i++) {
            $paymentDate = now()->addDays($concept->frequency->value * $i);
            $expenses[] = [
                'bank_id' => $bank->id,
                'concept_id' => $concept->id,
                'amount' => $amount,
                'payment_date' => $paymentDate,
                'status' => ExpenseStatusEnum::Pending->value,
                'route_id' => null,
                'comments' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        try {
            BankExpense::insertOrIgnore($expenses);
            $qtyAfter = $bank->expenses()->count();
            $quantity = $qtyAfter - $qtyBefore;
            $response->userMess = $quantity . ' Expense(s) created';
        } catch (\Error $e) {
            Log::error('Creating expenses for the Bank: ' . $e->getMessage());
            $response->indiOkey = false;
            $response->userMess = 'Error: ' . $e->getMessage();
            $response->qtyErrors = 0;
        }

        return $response;
    }


    public static function queryRecords($user)
    {
        if (
            in_array($user->type, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            return BankExpense::query();
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            $consortiumId = $user->consortium_id;
            $bankIds = Bank::byConsortium($consortiumId)->pluck('id');
            return BankExpense::whereIn('bank_id', $bankIds);
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            $supervisorId = $user->id;
            $bankIds = Bank::bySupervisor($supervisorId)->pluck('id');
            return BankExpense::whereIn('bank_id', $bankIds);
        }
        return [];
    }

}
