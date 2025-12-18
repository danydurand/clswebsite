<?php

namespace App\Domain\FinancialTransaction;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TrxTypeEnum: string implements HasLabel, HasColor, HasIcon
{
    case Bet = 'bet';
    case Prize = 'prize';
    case Deposit = 'deposit';
    case Withdraw = 'withdraw';
    case Debit = 'debit';


    public static function getValues(): array
    {
        // Extract just the values
        return array_map(fn($case) => $case->value, TrxTypeEnum::cases());
    }

    public static function getOptions(): array
    {
        return [
            'bet'      => __('Bet'),
            'prize'    => __('Prize'),
            'deposit'  => __('Deposit'),
            'withdraw' => __('Withdraw'),
            'debit'   => __('Debit'),
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Bet      => __('Bet'),
            self::Prize    => __('Prize'),
            self::Deposit  => __('Deposit'),
            self::Withdraw => __('Withdraw'),
            self::Debit    => __('Debit'),
        };
    }

    public function getColor(): string | array | null
    {
        return match($this) {
            self::Bet      => 'success',
            self::Prize    => 'info',
            self::Deposit  => 'primary',
            self::Withdraw => 'danger',
            self::Debit    => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::Bet      => 'heroicon-o-document',
            self::Prize    => 'heroicon-o-document',
            self::Deposit  => 'heroicon-o-document',
            self::Withdraw => 'heroicon-o-document',
            self::Debit    => 'heroicon-o-document',
        };
    }


}
