<?php

namespace App\Domain\Ticket;

use App\Models\Bank;
use App\Models\Ticket;
use App\Classes\PResponse;
use Illuminate\Support\Str;
use App\Models\TicketAction;
use App\Domain\User\UserTypeEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Domain\Ticket\TicketStatusEnum;

class TicketServices
{

    public static function markAsWinner(Collection $ticketIds, int $userId, string $scrutinyCode): PResponse
    {
        $response = new PResponse();

        try {
            $qtyMarked = Ticket::query()
                ->whereIn('id', $ticketIds)
                ->update([
                    'status' => TicketStatusEnum::Winner->value,
                ]);
            info('Marked ' . $qtyMarked . ' tickets as winner');
        } catch (\Error $e) {
            $response->indiOkey = false;
            $response->userMess = 'Error: ' . $e->getMessage();
            Log::error('Marking tickets as winner. ' . $response->userMess);
            return $response;
        }
        //-------------------------------------------------------------
        // The action must be registered in the ticket actions table
        //-------------------------------------------------------------
        $ticketActions = [];
        foreach ($ticketIds as $ticketId) {
            $ticketActions[] = [
                'ticket_id' => $ticketId,
                'action' => 'Marked as Winner',
                'executed_by' => $userId,
                'executed_at' => now(),
                'security_code' => null,
                'comments' => 'Scrutiny Code: ' . $scrutinyCode,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        try {
            TicketAction::insert($ticketActions);
        } catch (\Error $e) {
            $response->indiOkey = false;
            $response->userMess = 'Error: ' . $e->getMessage();
            Log::error('Registering the action of "change status of winner tickets". ' . $response->userMess);
            return $response;
        }

        return $response;
    }


    public static function markAsLooser(Collection $ticketIds, int $userId, string $scrutinyCode): PResponse
    {
        $response = new PResponse();

        try {
            $qtyMarked = Ticket::query()
                ->whereIn('id', $ticketIds)
                ->update([
                    'status' => TicketStatusEnum::Looser->value,
                ]);
            info('Marked ' . $qtyMarked . ' tickets as looser');
        } catch (\Error $e) {
            $response->indiOkey = false;
            $response->userMess = 'Error: ' . $e->getMessage();
            Log::error('Marking tickets as looser. ' . $response->userMess);
            return $response;
        }
        //-------------------------------------------------------------
        // The action must be registered in the ticket actions table
        //-------------------------------------------------------------
        $ticketActions = [];
        foreach ($ticketIds as $ticketId) {
            $ticketActions[] = [
                'ticket_id' => $ticketId,
                'action' => 'Marked as Looser',
                'executed_by' => $userId,
                'executed_at' => now(),
                'security_code' => null,
                'comments' => 'Scrutiny Code: ' . $scrutinyCode,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        try {
            TicketAction::insert($ticketActions);
        } catch (\Error $e) {
            $response->indiOkey = false;
            $response->userMess = 'Error: ' . $e->getMessage();
            Log::error('Registering the action of "change status of looser tickets". ' . $response->userMess);
            return $response;
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
            return Ticket::query();
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Owner->value,
                UserTypeEnum::Secretary->value,
            ])
        ) {
            return Ticket::query()
                ->byConsortium($user->consortium_id);
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Supervisor->value,
            ])
        ) {
            return Ticket::query()
                ->bySupervisor($user->id);
        }
        if (
            in_array($user->type, [
                UserTypeEnum::Banker->value,
            ])
        ) {
            $bankerId = Auth::user()->banker_id;
            $bankIds = Bank::where('group_id', $bankerId)->pluck('id')->toArray();
            return Ticket::query()
                ->whereIn('bank_id', $bankIds);
        }
        return Ticket::query()->where('id', '<', -1);

    }


    public static function thisMonthSoldAmount()
    {
        return bcdiv(Ticket::thisMonth()->sum('stake_amount') / 100, 2);
    }

    public static function thisMonthPrizeAmount()
    {
        return bcdiv(Ticket::thisMonth()->winner()->sum('prize_amount') / 100, 2);
    }

    public static function thisMonthProfit()
    {
        return bcsub(TicketServices::thisMonthSoldAmount(), TicketServices::thisMonthPrizeAmount(), 2);
    }

    public static function generateCode(): string
    {
        return (string) Str::uuid();
        // return strtoupper((string) Str::uuid());
    }



    public static function totalize(Ticket $ticket): PResponse
    {
        $response = new PResponse();
        info('');
        info('TicketServices::totalize');
        info('========================');
        try {
            $sqlQuery = "call lottery.spu_totalize_ticket($ticket->id)";
            DB::statement($sqlQuery);
            $response->indiOkey = true;
            $response->userMess = __('Ticket totalized');
        } catch (\Exception $e) {
            $response->indiOkey = false;
            $response->userMess = 'Error totalizing the ticket: ' . $e->getMessage();
            Log::error($response->userMess);
        }

        return $response;
    }






}
