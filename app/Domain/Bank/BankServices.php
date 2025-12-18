<?php

namespace App\Domain\Bank;

use App\Models\Bank;
use App\Models\User;
use App\Models\Seller;
use App\Classes\PResponse;
use App\Models\BankLottery;
use Illuminate\Support\Carbon;
use App\Domain\User\UserTypeEnum;
use App\Models\ConsortiumLottery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class BankServices
{

    public static function generateName(int $consortiumId): string
    {
        $next = Bank::where('consortium_id', $consortiumId)->count() + 1;
        return 'B' . str_pad($next, 3, '0', STR_PAD_LEFT) . 'C' . $consortiumId;
    }

    public static function assignProfile(array $ids, int $profileId, string $profileType): PResponse
    {
        $response = new PResponse();
        $qty = 0;
        try {
            if ($profileType == 'commission') {
                $qty = Bank::whereIn('id', $ids)
                    ->update([
                        'commission_id' => $profileId,
                        'commission_assigned_at' => now(),
                    ]);
            }
            if ($profileType == 'limit') {
                $qty = Bank::whereIn('id', $ids)
                    ->update([
                        'limit_id' => $profileId,
                        'limit_assigned_at' => now(),
                    ]);
            }
            if ($profileType == 'payment') {
                $qty = Bank::whereIn('id', $ids)
                    ->update([
                        'payment_id' => $profileId,
                        'payment_assigned_at' => now(),
                    ]);
            }
            if ($profileType == 'restriction') {
                $qty = Bank::whereIn('id', $ids)
                    ->update([
                        'restriction_id' => $profileId,
                        'restriction_assigned_at' => now(),
                    ]);
            }
            $response->userMess = $qty . ' Record(s) updated successfully';
            $response->qtyProcessed = $qty;
        } catch (\Error $e) {
            $response->indiOkey = false;
            $response->qtyErrors = 1;
            $response->userMess = 'Error: ' . $e->getMessage();
            Log::error($response->userMess);
        }
        return $response;
    }



    public static function assignLotteries(Bank $bank): PResponse
    {
        info('');
        info('BankServices::assignLotteries');
        info('=============================');

        $response = new PResponse();
        //---------------------------------------------------
        // Inheriting lottery selection from the Consortium
        //---------------------------------------------------
        $consortiumLotteries = ConsortiumLottery::where('consortium_id', $bank->consortium_id)->get();
        $bankLotteries = [];
        foreach ($consortiumLotteries as $consortiumLottery) {
            $bankLotteries[] = [
                'bank_id' => $bank->id,
                'lottery_id' => $consortiumLottery->lottery->id,
                'raffle_time' => $consortiumLottery->raffle_time,
                'is_active' => $consortiumLottery->is_active,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        info('Trying to insert: ' . count($bankLotteries) . ' BankLottery records');
        try {
            BankLottery::insertOrIgnore($bankLotteries);
            $qty = $bank->lotteries()->count();
            $response->userMess = $qty . ' Lotteries assigned to the Bank';
            info($response->userMess);
        } catch (\Error $e) {
            Log::error('Assigning Lotteries: ' . $e->getMessage());
            $response->indiOkey = false;
            $response->userMess = 'Error: ' . $e->getMessage();
            $response->qtyErrors = 1;
        }

        return $response;
    }


    public static function createFirstSeller(Bank $bank): PResponse
    {
        $response = new PResponse();

        $qty = Seller::byBank($bank->id)->count();
        if ($qty == 0) {
            try {
                Seller::create([
                    'name' => $bank->contact_name,
                    'email' => $bank->email,
                    'bank_id' => $bank->id,
                    'consortium_id' => $bank->consortium_id,
                    'password' => Hash::make('password'),
                    'type' => UserTypeEnum::Seller->value,
                    'is_active' => true,
                    'failed_attempts' => 0,
                ]);
                $response->userMess = 'First Seller Created Successfully';
            } catch (\Error $e) {
                Log::error('Creating First Seller' . $e->getMessage());
                $response->indiOkey = false;
                $response->userMess = 'Error: ' . $e->getMessage();
                $response->qtyErrors = 0;
            }
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
            return Bank::query();
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            $consortiumId = $user->consortium_id;
            return Bank::byConsortium($consortiumId);
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            $supervisorId = $user->id;
            return Bank::bySupervisor($supervisorId);
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Banker->value,
            ])
        ) {
            $bankerId = Auth::user()->banker_id;
            $bankIds = Bank::where('group_id', $bankerId)->pluck('id')->toArray();
            return Bank::whereIn('id', $bankIds);
        }
        return Bank::query()->where('id', '<', 0);
    }


    public static function getRaffleTimes(int $bankId): array
    {
        $raffleTimes = BankLottery::byBank($bankId)
            ->active()
            ->pluck('raffle_time', 'raffle_time')->toArray();
        return $raffleTimes;
    }


    public static function generateCode(int $consortiumId): string
    {
        $next = Bank::byConsortium($consortiumId)->count() + 1;
        return 'B' . $consortiumId . '-' . $next;
    }



}
