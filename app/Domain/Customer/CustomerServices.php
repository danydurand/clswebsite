<?php

namespace App\Domain\Customer;

use App\Models\Bet;
use App\Models\Bank;
use App\Models\User;
use App\Models\Customer;
use App\Classes\PResponse;
use App\Mail\BetNullified;
use App\Mail\CustomerBanned;
use App\Mail\CustomerUnbanned;
use App\Domain\User\UserTypeEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CustomerServices
{

    public static function ban(PResponse $response, $customers, string $reason): PResponse
    {
        if (!is_array($customers) && !($customers instanceof Collection)) {
            $customers = collect([$customers]);
        }

        $customerIds = $customers->pluck('id');

        DB::beginTransaction();
        try {
            $qtyCustomers = Customer::query()
                ->whereIn('id', $customerIds)
                ->whereNull('banned_at')
                ->update([
                    'ban_reason' => $reason,
                    'banned_at' => now()
                ]);
            $qtyUsers = User::query()
                ->whereIn('customer_id', $customerIds)
                ->where('is_active', true)
                ->update([
                    'banned_reason' => $reason,
                    'is_active' => false
                ]);
            $response->qtyProcessed = $qtyCustomers;
        } catch (\Exception $e) {
            Log::error('Error banning customers: ' . $e->getMessage());
            $response->qtyErrors++;
            $response->errors[] = [
                'reference' => 'N/A',
                'message' => 'Error Banning Customers',
                'comment' => $e->getMessage(),
            ];
        }
        DB::commit();

        $response->okay = $response->qtyErrors === 0;
        info('Finished banning customers');

        // Notify affected customers
        CustomerServices::sendBannedNotification($customers);

        $response->userMess = $response->qtyProcessed . " Customers Banned.";
        if ($response->qtyErrors > 0) {
            $response->userMess .= ' <br> ' . $response->qtyErrors . " Errors";
        }

        return $response;
    }


    public static function unban(PResponse $response, $customers): PResponse
    {
        if (!is_array($customers) && !($customers instanceof Collection)) {
            $customers = collect([$customers]);
        }

        $customerIds = $customers->pluck('id');

        DB::beginTransaction();
        try {
            $qtyCustomers = Customer::query()
                ->whereIn('id', $customerIds)
                ->whereNotNull('banned_at')
                ->update([
                    'ban_reason' => null,
                    'banned_at' => null
                ]);
            $qtyUsers = User::query()
                ->whereIn('customer_id', $customerIds)
                ->where('is_active', false)
                ->update([
                    'banned_reason' => null,
                    'is_active' => true
                ]);
            $response->qtyProcessed = $qtyCustomers;
        } catch (\Exception $e) {
            Log::error('Error unbanning customers: ' . $e->getMessage());
            $response->qtyErrors++;
            $response->errors[] = [
                'reference' => 'N/A',
                'message' => 'Error Unbanning Customers',
                'comment' => $e->getMessage(),
            ];
        }
        DB::commit();

        $response->okay = $response->qtyErrors === 0;
        info('Finished unbanning customers');

        // Notify affected customers
        CustomerServices::sendUnbannedNotification($customers);

        $response->userMess = $response->qtyProcessed . " Customers Unbanned.";
        if ($response->qtyErrors > 0) {
            $response->userMess .= ' <br> ' . $response->qtyErrors . " Errors";
        }

        return $response;
    }


    public static function sendBannedNotification(Collection $customers)
    {

        info('There are ' . count($customers) . ' customerIds to notify');

        foreach ($customers as $customer) {
            try {

                // Mail::to($customer->email)->send(new BetNullified($bet));
                Mail::to('daniel.durand@dreambet.ht')->send(new CustomerBanned($customer));

                info("Customer ID: {$customer->id} notified");

            } catch (\Exception $e) {
                Log::error('Error notifying customer id' . $customer->id . ': ' . $e->getMessage());
            }
        }
        info('Finished banning customers notification');

        // return $response;
    }


    public static function sendUnbannedNotification(Collection $customers)
    {

        info('There are ' . count($customers) . ' customerIds to notify');

        foreach ($customers as $customer) {
            try {

                // Mail::to($customer->email)->send(new BetNullified($bet));
                Mail::to('daniel.durand@dreambet.ht')->send(new CustomerUnbanned($customer));

                info("Customer ID: {$customer->id} notified");

            } catch (\Exception $e) {
                Log::error('Error notifying customer id' . $customer->id . ': ' . $e->getMessage());
            }
        }
        info('Finished unbanning customers notification');

        // return $response;
    }



    public static function queryRecords($user)
    {
        if (
            in_array($user->type, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            return Customer::query();
        }
        return Customer::query()->where('id', '-1');

    }


    public static function sendNullifyNotification(Collection $bets)
    {

        info('There are ' . count($bets) . ' customerIds to notify');

        foreach ($bets as $bet) {
            try {

                // Mail::to($bet->customer->email)->send(new BetNullified($bet));
                Mail::to('daniel.durand@dreambet.ht')->send(new BetNullified($bet));

                info("Customer ID: {$bet->customer->id} notified");

            } catch (\Exception $e) {
                Log::error('Error notifying customer id' . $bet->customer->id . ': ' . $e->getMessage());
            }
        }
        info('Finished nullifying bets');

        // return $response;
    }



    public static function totalWinnerBets()
    {
        return Customer::winner()->count();
    }

    public static function totalLoserBets()
    {
        return Customer::loser()->count();
    }

    public static function totalPendingBets()
    {
        return Customer::pending()->count();
    }

    public static function totalRefundedBets()
    {
        return Customer::refunded()->count();
    }

    public static function winnerBets()
    {
        $userType = Auth::user()->type;
        $winnerQty = 0;
        if (
            in_array($userType, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            $winnerQty = Customer::today()
                ->winner()
                ->count();
        }
        if (
            in_array($userType, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            $consortiumId = Auth::user()->consortium_id;
            $winnerQty = Customer::today()
                ->byConsortium($consortiumId)
                ->winner()
                ->count();
        }
        if (
            in_array($userType, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            $supervisorId = Auth::user()->id;
            $winnerQty = Customer::today()
                ->bySupervisor($supervisorId)
                ->winner()
                ->count();
        }
        if (
            in_array($userType, [
                UserTypeEnum::Banker->value,
            ])
        ) {
            $bankerId = Auth::user()->banker_id;
            $bankIds = Bank::where('group_id', $bankerId)->pluck('id')->toArray();
            $winnerQty = Customer::today()
                ->whereIn('bank_id', $bankIds)
                ->winner()
                ->count();
        }

        return $winnerQty;

    }


    public static function cancelledBets()
    {
        $userType = Auth::user()->type;
        $cancelledQty = 0;

        if (
            in_array($userType, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            $cancelledQty = Customer::today()
                ->cancelled()
                ->count();
        }
        if (
            in_array($userType, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            $consortiumId = Auth::user()->consortium_id;
            $cancelledQty = Customer::today()
                ->byConsortium($consortiumId)
                ->cancelled()
                ->count();
        }
        if (
            in_array($userType, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            $supervisorId = Auth::user()->id;
            $cancelledQty = Customer::today()
                ->bySupervisor($supervisorId)
                ->cancelled()
                ->count();
        }
        if (
            in_array($userType, [
                UserTypeEnum::Banker->value,
            ])
        ) {
            $bankerId = Auth::user()->banker_id;
            $bankIds = Bank::where('group_id', $bankerId)->pluck('id')->toArray();
            $cancelledQty = Customer::today()
                ->whereIn('bank_id', $bankIds)
                ->cancelled()
                ->count();
        }

        return $cancelledQty;

    }

    public static function soldBets()
    {
        $userType = Auth::user()->type;
        $soldQty = 0;

        if (
            in_array($userType, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            $soldQty = Customer::today()
                ->count();
        }
        if (
            in_array($userType, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            $consortiumId = Auth::user()->consortium_id;
            $soldQty = Customer::today()
                ->byConsortium($consortiumId)
                ->count();
        }
        if (
            in_array($userType, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            $supervisorId = Auth::user()->id;
            $soldQty = Customer::today()
                ->bySupervisor($supervisorId)
                ->count();
        }
        if (
            in_array($userType, [
                UserTypeEnum::Banker->value,
            ])
        ) {
            $bankerId = Auth::user()->banker_id;
            $bankIds = Bank::where('group_id', $bankerId)->pluck('id')->toArray();
            $soldQty = Customer::today()
                ->whereIn('bank_id', $bankIds)
                ->count();
        }

        return $soldQty;

    }


    public static function todaySoldAmount()
    {
        $userType = Auth::user()->type;
        $soldAmount = 0;
        if (
            in_array($userType, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            $soldAmount = Customer::today()
                ->notCancelled()
                ->sum('stake_amount') / 100;
        }
        if (
            in_array($userType, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            $consortiumId = Auth::user()->consortium_id;
            $soldAmount = Customer::today()
                ->byConsortium($consortiumId)
                ->notCancelled()
                ->sum('stake_amount') / 100;
        }
        if (
            in_array($userType, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            $supervisorId = Auth::user()->id;
            $soldAmount = Customer::today()
                ->bySupervisor($supervisorId)
                ->notCancelled()
                ->sum('stake_amount') / 100;
        }
        if (
            in_array($userType, [
                UserTypeEnum::Banker->value,
            ])
        ) {
            $bankerId = Auth::user()->banker_id;
            $bankIds = Bank::where('group_id', $bankerId)->pluck('id')->toArray();
            $soldAmount = Customer::today()
                ->whereIn('bank_id', $bankIds)
                ->notCancelled()
                ->sum('stake_amount') / 100;
        }

        return $soldAmount;
    }

    public static function todayPrizeAmount()
    {
        $userType = Auth::user()->type;
        $prizeAmount = 0;
        if (
            in_array($userType, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            $prizeAmount = Customer::today()
                ->notCancelled()
                ->sum('prize_amount') / 100;
        }
        if (
            in_array($userType, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            $consortiumId = Auth::user()->consortium_id;
            $prizeAmount = Customer::today()
                ->byConsortium($consortiumId)
                ->notCancelled()
                ->sum('prize_amount') / 100;
        }
        if (
            in_array($userType, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            $supervisorId = Auth::user()->id;
            $prizeAmount = Customer::today()
                ->bySupervisor($supervisorId)
                ->notCancelled()
                ->sum('prize_amount') / 100;
        }
        if (
            in_array($userType, [
                UserTypeEnum::Banker->value,
            ])
        ) {
            $bankerId = Auth::user()->banker_id;
            $bankIds = Bank::where('group_id', $bankerId)->pluck('id')->toArray();
            $prizeAmount = Customer::today()
                ->whereIn('bank_id', $bankIds)
                ->notCancelled()
                ->sum('prize_amount') / 100;
        }

        return $prizeAmount;
    }

    public static function todayProfit()
    {
        $userType = Auth::user()->type;
        $profitAmount = 0;
        if (
            in_array($userType, [
                UserTypeEnum::Master->value,
                UserTypeEnum::Admin->value,
            ])
        ) {
            $profitAmount = Customer::today()
                ->notCancelled()
                ->sum('profit') / 100;
        }
        if (
            in_array($userType, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            $consortiumId = Auth::user()->consortium_id;
            $profitAmount = Customer::today()
                ->byConsortium($consortiumId)
                ->notCancelled()
                ->sum('profit') / 100;
        }
        if (
            in_array($userType, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            $supervisorId = Auth::user()->id;
            $profitAmount = Customer::today()
                ->bySupervisor($supervisorId)
                ->notCancelled()
                ->sum('profit') / 100;
        }
        if (
            in_array($userType, [
                UserTypeEnum::Banker->value,
            ])
        ) {
            $bankerId = Auth::user()->banker_id;
            $bankIds = Bank::where('group_id', $bankerId)->pluck('id')->toArray();
            $profitAmount = Customer::today()
                ->whereIn('bank_id', $bankIds)
                ->notCancelled()
                ->sum('profit') / 100;
        }

        return $profitAmount;
    }



}
