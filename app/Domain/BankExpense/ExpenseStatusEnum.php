<?php

namespace App\Domain\BankExpense;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ExpenseStatusEnum: string implements HasLabel, HasColor, HasIcon
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Late = 'late';

    public static function getOptions(): array
    {
        return [
            'pending' => __('Pending'),
            'paid'    => __('Paid'),
            'late'    => __('Late'),
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => __('Pending'),
            self::Paid    => __('Paid'),
            self::Late    => __('Late'),
        };
    }

    public function getColor(): string | array | null
    {
        return match($this) {
            self::Pending  => 'danger',
            self::Paid     => 'success',
            self::Late     => 'warning',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::Pending   => 'heroicon-o-clock',
            self::Paid      => 'heroicon-o-check-badge',
            self::Late      => 'heroicon-o-exclamation-triangle',
        };
    }


}
