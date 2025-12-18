<?php

namespace App\Domain\Consortium;

use App\Models\Bank;
use App\Models\Group;
use App\Models\Seller;
use App\Models\Lottery;
use App\Classes\PResponse;
use App\Models\Consortium;
use App\Models\Supervisor;
use App\Models\BankLottery;
use Illuminate\Support\Carbon;
use App\Models\ConsortiumLottery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConsortiumServices
{

    public static function deleteRelatedRecords(Consortium $consortium): void
    {
        $consortium->staff()->delete();
        $consortium->lotteries()->delete();
    }

    public static function assignRate(array $ids, int $rateId): PResponse
    {
        $response = new PResponse();
        $qty = 0;
        try {
            $qty = Consortium::whereIn('id', $ids)
                ->update([
                    'rate_id' => $rateId,
                    'rate_assigned_at' => now(),
                ]);
            $response->userMess = $qty . ' Consortium(s) updated successfully';
            $response->qtyProcessed = $qty;
        } catch (\Error $e) {
            $response->indiOkey = false;
            $response->qtyErrors = 1;
            $response->userMess = 'Error: ' . $e->getMessage();
            Log::error($response->userMess);
        }

        return $response;
    }


    public static function propagateIsActive(Consortium $consortium): PResponse
    {
        $response = new PResponse();
        $isActive = $consortium->is_active;
        try {
            $qtySup = Supervisor::byConsortium($consortium->id)->update(['is_active' => $isActive]);
            $qtyGrp = Group::byConsortium($consortium->id)->update(['is_active' => $isActive]);
            $qtyBnk = Bank::byConsortium($consortium->id)->update(['is_active' => $isActive]);
            $bankIds = Bank::byConsortium($consortium->id)->pluck('id')->toArray();
            $qtySel = Seller::whereIn('bank_id', $bankIds)->update(['is_active' => $isActive]);
            $response->userMess = $qtySup . ' Supervisors<br>' . $qtyGrp . ' Groups<br> ' .
                $qtyBnk . ' Banks and<br> ' . $qtySel . ' Sellers were updated';
        } catch (\Error $e) {
            Log::error('Propagating Consortium IsActive: ' . $e->getMessage());
            $response->indiOkey = false;
            $response->userMess = 'Error: ' . $e->getMessage();
            $response->qtyErrors = 0;
        }

        return $response;
    }


    public static function assignLotterySelection(Consortium $consortium): PResponse
    {
        $response = new PResponse();

        $lotteries = Lottery::all();
        $consortiumLotteries = [];
        foreach ($lotteries as $lottery) {
            $raffleTimes = $lottery->raffle_times;
            foreach ($raffleTimes as $raffleTime) {
                $consortiumLotteries[] = [
                    'consortium_id' => $consortium->id,
                    'lottery_id' => $lottery->id,
                    'raffle_time' => $lottery->code . '-' . trim($raffleTime),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        try {
            ConsortiumLottery::insertOrIgnore($consortiumLotteries);
            $qty = $consortium->lotteries()->count();
            $response->userMess = $qty . ' Lotteries Selection assigned to the Consortium';
        } catch (\Error $e) {
            Log::error('Assigning Lottery Selection to the Consortium: ' . $e->getMessage());
            $response->indiOkey = false;
            $response->userMess = 'Error: ' . $e->getMessage();
            $response->qtyErrors = 0;
        }

        return $response;
    }


    public static function generateCode(): string
    {
        $next = Consortium::count() + 1;
        return 'C' . $next;
    }

    public static function getGroupIds(Consortium $consortium): array
    {
        $groupIds = $consortium->groups->pluck('id')->toArray();
        return $groupIds;
    }

    public static function getRaffleTimes(int $consortiumId): array
    {
        $raffleTimes = ConsortiumLottery::byConsortium($consortiumId)
            // ->active()
            ->pluck('raffle_time', 'raffle_time')->toArray();
        return $raffleTimes;
    }


    public static function updateLotterySelection(ConsortiumLottery $consortiumLottery): PResponse
    {
        $action = '';
        $response = new PResponse();
        try {
            $qty = 0;
            $banks = Bank::byConsortium($consortiumLottery->consortium_id)->get();
            foreach ($banks as $bank) {
                $bankLottery = BankLottery::findByBankLotteryTime($bank->id, $consortiumLottery->lottery_id, $consortiumLottery->raffle_time);
                if ($bankLottery) {
                    $bankLottery->is_active = $consortiumLottery->is_active;
                    $bankLottery->save();
                    $action = 'Updated';
                } else {
                    BankLottery::create([
                        'bank_id' => $bank->id,
                        'lottery_id' => $consortiumLottery->lottery_id,
                        'raffle_time' => $consortiumLottery->raffle_time,
                        'is_active' => $consortiumLottery->is_active,
                    ]);
                    $action = 'Created';
                }
                $qty++;
            }
            $response->userMess = $qty . ' Lottery Selections ' . $action;
            $response->qtyProcessed = $qty;
        } catch (\Error $e) {
            $response->indiOkey = false;
            $response->qtyErrors = 1;
            $response->userMess = 'Error: ' . $e->getMessage();
            Log::error($response->userMess);
        }
        return $response;
    }


    public static function inheritLotteriesSelection(Consortium $consortium): PResponse
    {
        $response = new PResponse();
        try {
            //------------------------------------------------------------------------------------------
            // Deleting any existing selections in the bank_lotteries table related to the consortium
            //------------------------------------------------------------------------------------------
            $bankIds = Bank::byConsortium($consortium->id)->pluck('id');
            BankLottery::whereIn('bank_id', $bankIds)->delete();
            //---------------------------------------------------------
            // Get the lottery selections from the parent consortium
            //---------------------------------------------------------
            $lotteriesSelection = ConsortiumLottery::byConsortium($consortium->id)->get();
            $banks = Bank::byConsortium($consortium->id)->get();
            $qty = 0;
            foreach ($banks as $bank) {
                $bankLotteries = [];
                foreach ($lotteriesSelection as $lotterySelection) {
                    $bankLotteries[] = [
                        'bank_id' => $bank->id,
                        'lottery_id' => $lotterySelection->lottery_id,
                        'raffle_time' => $lotterySelection->raffle_time,
                        'is_active' => $lotterySelection->is_active,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
                DB::table('bank_lotteries')->insert($bankLotteries);
                $qty += count($bankLotteries);
            }
            $response->userMess = $qty . ' Lottery Selections inherited';
            $response->qtyProcessed = $qty;
        } catch (\Error $e) {
            $response->indiOkey = false;
            $response->qtyErrors = 1;
            $response->userMess = 'Error: ' . $e->getMessage();
            Log::error($response->userMess);
        }
        return $response;
    }

}
