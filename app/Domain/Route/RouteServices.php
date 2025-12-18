<?php

namespace App\Domain\Route;

use App\Domain\BankExpense\ExpenseStatusEnum;
use App\Domain\Transaction\TransactionDoneEnum;
use App\Models\Bank;
use App\Models\BankExpense;
use App\Models\Expense;
use App\Models\Group;
use App\Models\Route;
use App\Classes\PResponse;
use App\Models\Supervisor;
use App\Models\Transaction;
use App\Models\CashSummary2;
use App\Domain\User\UserTypeEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Domain\LoanDetail\FeeStatusEnum;
use App\Domain\Transaction\TransactionTypeEnum;


class RouteServices
{
    public static function unLinkTransactions(Route $route): PResponse
    {
        $response = new PResponse();

        info('');
        info('RouteServices::unLinkTransactions');
        info('==================================');

        try {
            DB::beginTransaction();

            $routeId = $route->id;

            info('Processing route: ' . $route->id);

            // 0. Drop sale and prizes transactions related to the route
            $droppedCount = Transaction::where('route_id', $routeId)
                ->where('type', TransactionTypeEnum::Sale->value)
                ->orWhere('type', TransactionTypeEnum::Prize->value)
                ->delete();

            info('Dropped ' . $droppedCount . ' transactions (sale and prizes)');

            // 1. Unlink transactions - set route_id to null
            $transactionCount = Transaction::where('route_id', $routeId)
                ->update(['route_id' => null]);

            info('Unlinked ' . $transactionCount . ' transactions');

            // 2. Unlink expenses - set route_id to null
            $expenseCount = BankExpense::where('route_id', $routeId)
                ->update(['route_id' => null]);

            info('Unlinked ' . $expenseCount . ' expenses');

            // 3. Restore forwarded transactions to their original state
            $forwardedCount = Transaction::where('done', TransactionDoneEnum::Forwarded->value)
                ->whereRaw("comments LIKE '%(forwarded)%'")
                ->update([
                    'done' => TransactionDoneEnum::No->value,
                    'comments' => DB::raw("REPLACE(comments, ' (forwarded)', '')")
                ]);

            info('Restored ' . $forwardedCount . ' forwarded transactions');

            // 4. Unlink loan details - set route_id to null
            $loanDetailCount = DB::table('loan_details')
                ->where('route_id', $routeId)
                ->update(['route_id' => null]);

            info('Unlinked ' . $loanDetailCount . ' loan details');

            DB::commit();

            $totalUnlinked = $transactionCount + $expenseCount + $loanDetailCount;
            $response->userMess = 'Successfully unlinked ' . $totalUnlinked . ' records from route';
            $response->qtyProcessed = $totalUnlinked;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error unlinking route records: ' . $e->getMessage());
            $response->indiOkey = false;
            $response->qtyErrors = 1;
            $response->userMess = 'Error unlinking route records: ' . $e->getMessage();
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
            return Route::query();
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            $supervisorIds = Supervisor::byConsortium($user->consortium_id)->pluck('id');
            return Route::query()
                ->whereIn('supervisor_id', $supervisorIds);
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            return Route::query()
                ->bySupervisor($user->id);
        }
        return Route::query()->latest()->take(1);
    }


    public static function generateTransactions(Route $route): PResponse
    {
        $response = new PResponse();

        info('');
        info('RouteServices::generateTransactions');
        info('===================================');

        $initDate = $route->init_date->format('Y-m-d');
        $endDate = $route->end_date->format('Y-m-d');
        $supervisorId = $route->supervisor_id;
        $groups = Group::bySupervisor($supervisorId)->get();

        info('There are: ' . count($groups) . ' groups');

        $transacs = [];
        try {

            $data['initDate'] = $initDate;
            $data['endDate'] = $endDate;
            $data['routeId'] = $route->id;
            $qty = 0;
            foreach ($groups as $group) {
                $banks = $group->banks;

                info('Procession group: ' . $group->name . ' with: ' . count($banks) . ' banks');

                foreach ($banks as $bank) {
                    info('');
                    info('Processing bank: ' . $bank->name);

                    $data['bankId'] = $bank->id;
                    $data['transacs'] = $transacs;
                    $data['supervisorId'] = $supervisorId;
                    $data['groupId'] = $group->id;

                    info('');
                    info('Going to existent transactions');
                    $existentResult = self::tranExistent($data);
                    info('existent indi-okey: ' . $existentResult->indiOkey);
                    info('existent user-mess: ' . $existentResult->userMess);
                    if ($existentResult->indiOkey) {
                        $qty += $existentResult->qtyProcessed;
                        info('So far qty: ' . $qty);
                    }

                    info('');
                    info('Going to sold and prizes');
                    $soldResult = self::tranSoldAndPrizes($data);
                    info('sold and prizes indi-okey: ' . $soldResult->indiOkey);
                    info('sold and prizes user-mess: ' . $soldResult->userMess);
                    if ($soldResult->indiOkey) {
                        $qty += $soldResult->qtyProcessed;
                        info('So far qty: ' . $qty);
                    }

                    info('');
                    info('Going to expenses');
                    $expensesResult = self::tranExpenses($data);
                    info('expenses indi-okey: ' . $expensesResult->indiOkey);
                    info('expenses user-mess: ' . $expensesResult->userMess);
                    if ($expensesResult->indiOkey) {
                        $qty += $expensesResult->qtyProcessed;
                        info('So far qty: ' . $qty);
                    }

                    info('');
                    info('Going to old transactions');
                    $oldResult = self::tranOldTransactions($data);
                    info('old indi-okey: ' . $oldResult->indiOkey);
                    info('old user-mess: ' . $oldResult->userMess);
                    if ($oldResult->indiOkey) {
                        $qty += $oldResult->qtyProcessed;
                        info('So far qty: ' . $qty);
                    }

                    info('Going to loans');
                    $loansResult = self::tranLoans($data);
                    info('loans indi-okey: ' . $loansResult->indiOkey);
                    info('loans user-mess: ' . $loansResult->userMess);
                    if ($loansResult->indiOkey) {
                        $qty += $loansResult->qtyProcessed;
                        info('So far qty: ' . $qty);
                    }
                }
            }
            $response->userMess = 'Route generated with (' . $qty . ') transactions';
        } catch (\Error $e) {
            $response->qtyErrors = 1;
            $response->userMess = $e->getMessage();
        }

        return $response;
    }

    public static function tranExistent($data): PResponse
    {
        $response = new PResponse();

        $initDate = $data['initDate'];
        $endDate = $data['endDate'];
        $bankId = $data['bankId'];
        $routeId = $data['routeId'];
        //---------------------------------------------------------------------------------
        // If there are existent transactions, then we will include them in the new route
        //---------------------------------------------------------------------------------
        try {
            $existent = Transaction::whereBetween('transaction_date', [$initDate, $endDate])
                ->where('bank_id', $bankId)
                ->whereNull('route_id')
                ->update(['route_id' => $routeId]);

            info('Existent: ' . json_encode($existent));

            $response->qtyProcessed = $existent;
        } catch (\Error $e) {
            Log::error('Existent: ' . $e->getMessage());
            $response->indiOkey = false;
            $response->qtyErrors = 1;
            $response->userMess = $e->getMessage();
        }

        return $response;
    }

    public static function tranSoldAndPrizes($data): PResponse
    {
        $response = new PResponse();

        $initDate = $data['initDate'];
        $endDate = $data['endDate'];
        $bankId = $data['bankId'];
        $routeId = $data['routeId'];
        $transacs = $data['transacs'];
        $supervisorId = $data['supervisorId'];
        $groupId = $data['groupId'];
        //-------------------
        // Sold and Prizes
        //-------------------
        $result = CashSummary2::whereBetween('created_at', [$initDate, $endDate])
            ->where('bank_id', $bankId)
            ->selectRaw('SUM(prize) as prize, SUM(profit) as profit')
            ->first();

        info('Sold and Prizes: ' . json_encode($result));

        $prize = $result->prize;
        $profit = $result->profit;
        if ($profit > 0) {
            $transacs[] = [
                'route_id' => $routeId,
                'supervisor_id' => $supervisorId,
                'group_id' => $groupId,
                'bank_id' => $bankId,
                'type' => TransactionTypeEnum::Sale->value,
                'debit_amount' => 0,
                'credit_amount' => $profit * 100,
                'real_amount' => 0,
                'transaction_date' => $endDate,
                'done' => TransactionDoneEnum::No->value,
                'description' => 'Sold between ' . $initDate . ' and ' . $endDate,
                'banker_aprovement' => false,
                'consortium_aprovement' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        if ($prize > 0) {
            $transacs[] = [
                'route_id' => $routeId,
                'supervisor_id' => $supervisorId,
                'group_id' => $groupId,
                'bank_id' => $bankId,
                'type' => TransactionTypeEnum::Prize->value,
                'debit_amount' => $prize * 100,
                'credit_amount' => 0,
                'real_amount' => 0,
                'transaction_date' => $endDate,
                'done' => TransactionDoneEnum::No->value,
                'description' => 'Prizes between ' . $initDate . ' and ' . $endDate,
                'banker_aprovement' => false,
                'consortium_aprovement' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (count($transacs) > 0) {
            try {
                Transaction::insert($transacs);
                $response->qtyProcessed = count($transacs);
            } catch (\Error $e) {
                Log::error('Sold and Prizes: ' . $e->getMessage());
                $response->indiOkey = false;
                $response->qtyErrors = 1;
                $response->userMess = $e->getMessage();
            }
        }

        return $response;
    }

    public static function tranExpenses($data): PResponse
    {
        $response = new PResponse();

        $initDate = $data['initDate'];
        $endDate = $data['endDate'];
        $bankId = $data['bankId'];
        $routeId = $data['routeId'];
        $transacs = $data['transacs'];
        $supervisorId = $data['supervisorId'];
        $groupId = $data['groupId'];

        //-----------------------------
        // Expenses in the date range
        //-----------------------------
        $bank = Bank::find($bankId);
        $expenses = $bank->expenses()
            ->whereBetween('payment_date', [$initDate, $endDate])
            ->whereNull('route_id')
            ->where('status', ExpenseStatusEnum::Pending->value)
            ->get();

        info('Expenses: ' . json_encode($expenses));

        $description = '';
        $expenseAmnt = 0;
        $expenseDate = now();
        foreach ($expenses as $expense) {
            $description .= $expense->concept->name . ' (' . $expense->amount . '), ';
            $expenseAmnt += $expense->amount;
            $expenseDate = $expense->payment_date;
            $expense->route_id = $routeId;
            $expense->save();
        }
        if ($expenseAmnt > 0) {
            $transacs[] = [
                'route_id' => $routeId,
                'supervisor_id' => $supervisorId,
                'group_id' => $groupId,
                'bank_id' => $bankId,
                'type' => TransactionTypeEnum::Expense->value,
                'debit_amount' => $expenseAmnt * 100,
                'credit_amount' => 0,
                'real_amount' => 0,
                'transaction_date' => $expenseDate,
                'done' => TransactionDoneEnum::No->value,
                'description' => $description,
                'banker_aprovement' => false,
                'consortium_aprovement' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        try {
            Transaction::insert($transacs);
            $response->qtyProcessed = count($transacs);
        } catch (\Error $e) {
            Log::error('Expenses: ' . $e->getMessage());
            $response->indiOkey = false;
            $response->qtyErrors = 1;
            $response->userMess = $e->getMessage();
        }

        return $response;
    }

    public static function tranOldTransactions($data): PResponse
    {
        $response = new PResponse();

        $initDate = $data['initDate'];
        $bankId = $data['bankId'];
        $routeId = $data['routeId'];
        $transacs = $data['transacs'];
        $supervisorId = $data['supervisorId'];
        $groupId = $data['groupId'];

        //---------------------------------
        // Old-still-pending transactions
        //---------------------------------
        $oldTrans = Transaction::where('bank_id', $bankId)
            ->where('done', TransactionDoneEnum::No->value)
            ->whereDate('created_at', '<=', $initDate)
            ->where('route_id', '!=', $routeId)
            ->get();
        info('There are: ' . count($oldTrans) . ' old-still-pending transactions');

        /** @var Transaction $old */
        foreach ($oldTrans as $old) {

            info('Old transaction: ' . $old->id);
            $oldInitDate = $old->route->init_date->format('Y-m-d');
            $oldEndDate = $old->route->end_date->format('Y-m-d');
            $description = trim($old->description) . ' [' . $oldInitDate . '-' . $oldEndDate . '] (still pending)';
            $transacs[] = [
                'route_id' => $routeId,
                'supervisor_id' => $supervisorId,
                'group_id' => $groupId,
                'bank_id' => $bankId,
                'type' => $old->type->value,
                'debit_amount' => $old->debit_amount * 100,
                'credit_amount' => $old->credit_amount * 100,
                'real_amount' => 0,
                'transaction_date' => now(),
                'done' => TransactionDoneEnum::No->value,
                'description' => $description,
                'banker_aprovement' => false,
                'consortium_aprovement' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            //-----------------------------------------------------
            // Marking the old transaction as forwarded
            //-----------------------------------------------------
            $old->done = TransactionDoneEnum::Forwarded->value;
            $old->comments = trim($old->comment) . ' (forwarded)';
            $old->save();
            //-------------------------------------------------------------------
            // If the route of the old transaction is finished, by
            // mark the old transaction as done, we mark the route as finished
            //-------------------------------------------------------------------
            if ($old->route->status != RouteStatusEnum::Finished->value) {
                $old->route->status = RouteStatusEnum::Finished->value;
                $old->route->comments = $old->route->comments . ' (route finished)';
                $old->route->save();
            }

        }

        try {
            Transaction::insert($transacs);
            $response->qtyProcessed = count($transacs);
        } catch (\Error $e) {
            Log::error('Old Transactions: ' . $e->getMessage());
            $response->indiOkey = false;
            $response->qtyErrors = 1;
            $response->userMess = $e->getMessage();
        }

        return $response;
    }

    public static function tranLoans($data): PResponse
    {
        $response = new PResponse();

        $initDate = $data['initDate'];
        $endDate = $data['endDate'];
        $bankId = $data['bankId'];
        $routeId = $data['routeId'];
        $transacs = $data['transacs'];
        $supervisorId = $data['supervisorId'];
        $groupId = $data['groupId'];

        //---------------------------------
        // Any fees in the date range
        //---------------------------------
        $bank = Bank::find($bankId);
        $fees = $bank->loanDetails()
            ->whereBetween('collection_date', [$initDate, $endDate])
            ->whereIn('loan_details.status', [
                FeeStatusEnum::Pending->value,
                FeeStatusEnum::Late->value,
            ])
            ->get();

        info('Fees from: ' . $initDate . ' to ' . $endDate);
        info('Fees: ' . count($fees));
        info('Fees: ' . json_encode($fees));

        $description = '';
        $totalFee = 0;
        foreach ($fees as $fee) {
            $description .= 'Fee Id: ' . $fee->id . ' (Amnt: ' . $fee->amount . ') [' . $fee->collection_date . '], ';
            $totalFee += $fee->amount;
            $fee->route_id = $routeId;
            $fee->save();
        }
        if ($totalFee > 0) {
            $transacs[] = [
                'route_id' => $routeId,
                'supervisor_id' => $supervisorId,
                'group_id' => $groupId,
                'bank_id' => $bankId,
                'type' => TransactionTypeEnum::Loan->value,
                'debit_amount' => 0,
                'credit_amount' => $totalFee * 100,
                'real_amount' => 0,
                'transaction_date' => $fees[0]->collection_date,
                'done' => TransactionDoneEnum::No->value,
                'description' => $description,
                'banker_aprovement' => false,
                'consortium_aprovement' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            try {
                Transaction::insert($transacs);
                $response->qtyProcessed = count($transacs);
            } catch (\Error $e) {
                Log::error('Loans: ' . $e->getMessage());
                $response->indiOkey = false;
                $response->qtyErrors = 1;
                $response->userMess = $e->getMessage();
            }
        }

        return $response;
    }

}
