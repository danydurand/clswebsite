<?php

namespace App\Domain\Transaction;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TransactionTypeEnum: string implements HasLabel, HasColor, HasIcon
{
    case Adjustment = 'adjustment';
    case Charge     = 'charge';
    case Sale       = 'sale';
    case Prize      = 'prize';
    case Expense    = 'expense';
    case Loan       = 'loan';
    case Withdrawal = 'withdrawal';

    public static function getValues(): array
    {
        // Extract just the values
        return array_map(fn($case) => $case->value, TransactionTypeEnum::cases());
    }

    //-------------------------------------------------------------------------------
    // The "sign" is the type of transaction from the perspective of the Consortium
    //-------------------------------------------------------------------------------
    public static function getSign(): array
    {
        return [
            'adjustment' => 'DEBIT',
            'charge'     => 'CREDIT',
            'sale'       => 'CREDIT',
            'prize'      => 'DEBIT',
            'expense'    => 'DEBIT',
            'loan'       => 'CREDIT',
            'withdrawal' => 'CREDIT',
        ];
    }

    public static function getCreateOptions(): array
    {
        return [
            'adjustment' => __('Adjustment'),
            'charge'     => __('Charge'),
            'expense'    => __('Expense'),
            'withdrawal' => __('Withdrawal'),
        ];
    }

    public static function getOptions(): array
    {
        return [
            'adjustment' => __('Adjustment'),
            'charge'     => __('Charge'),
            'sale'       => __('Prize'),
            'prize'      => __('Sale'),
            'expense'    => __('Expense'),
            'loan'       => __('Loan'),
            'withdrawal' => __('Withdrawal'),
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Adjustment => __('Adjustment'),
            self::Charge     => __('Charge'),
            self::Sale       => __('Sale'),
            self::Prize      => __('Prize'),
            self::Expense    => __('Expense'),
            self::Loan       => __('Loan'),
            self::Withdrawal => __('Withdrawal'),
        };
    }

    public function getColor(): string | array | null
    {
        return match($this) {
            self::Adjustment  => 'warning',
            self::Charge      => 'info',
            self::Sale        => 'info',
            self::Prize       => 'danger',
            self::Expense     => 'danger',
            self::Loan        => 'info',
            self::Withdrawal  => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::Adjustment => 'heroicon-o-adjustments-horizontal',
            self::Charge     => 'heroicon-o-currency-dollar',
            self::Sale       => 'heroicon-o-code-bracket-square',
            self::Prize      => 'heroicon-o-banknotes',
            self::Expense    => 'heroicon-o-bars-arrow-down',
            self::Loan       => 'heroicon-o-book-open',
            self::Withdrawal => 'heroicon-o-arrow-right-start-on-rectangle',
        };
    }


}
