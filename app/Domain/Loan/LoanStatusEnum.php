<?php

namespace App\Domain\Loan;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum LoanStatusEnum: string implements HasLabel, HasColor, HasIcon
{
    case Pending     = 'pending';
    case InProgress = 'in-progress';
    case Paid       = 'paid';

    public static function getOptions(): array
    {
        return [
            'pending'     => __('Pending'),
            'in-progress' => __('InProgress'),
            'paid'        => __('Paid'),
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending    => __('Pending'),
            self::InProgress => __('InProgress'),
            self::Paid       => __('Paid'),
        };
    }

    public function getColor(): string | array | null
    {
        return match($this) {
            self::Pending    => 'danger',
            self::InProgress => 'info',
            self::Paid       => 'success',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::Pending    => 'heroicon-o-clock',
            self::InProgress => 'heroicon-o-truck',
            self::Paid       => 'heroicon-o-check-circle',
        };
    }


}
