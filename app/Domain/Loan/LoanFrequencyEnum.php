<?php

namespace App\Domain\Loan;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum LoanFrequencyEnum: int implements HasLabel, HasColor, HasIcon
{
    case Once = 0;
    case Daily = 1;
    case Weekly = 7;
    case Biweekly = 15;
    case Monthly = 30;

    public static function getOptions(): array
    {
        return [
            0  => __('Once'),
            1  => __('Daily'),
            7  => __('Weekly'),
            15 => __('Biweekly'),
            30 => __('Monthly'),
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Once     => __('Once'),
            self::Daily    => __('Daily'),
            self::Weekly   => __('Weekly'),
            self::Biweekly => __('Biweekly'),
            self::Monthly  => __('Monthly'),
        };
    }

    public function getColor(): string | array | null
    {
        return match($this) {
            self::Once     => 'primary',
            self::Daily    => 'primary',
            self::Weekly   => 'info',
            self::Biweekly => 'success',
            self::Monthly  => 'warning',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::Once     => 'heroicon-o-clock',
            self::Daily    => 'heroicon-o-clock',
            self::Weekly   => 'heroicon-o-clock',
            self::Biweekly => 'heroicon-o-clock',
            self::Monthly  => 'heroicon-o-clock',
        };
    }


}
