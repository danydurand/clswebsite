<?php

namespace App\Domain\Bet;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum BetActionEnum: string implements HasLabel, HasColor, HasIcon
{
    case Printed   = 'printed';
    case Paid      = 'paid';
    case Cancelled = 'cancelled';
    case AutoCancelled = 'auto-cancelled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Printed       => __('Printed'),
            self::Paid          => __('Paid'),
            self::Cancelled     => __('Cancelled'),
            self::AutoCancelled => __('AutoCancelled'),
        };
    }

    public function getColor(): string | array | null
    {
        return match($this) {
            self::Printed       => 'warning',
            self::Paid          => 'success',
            self::Cancelled     => 'danger',
            self::AutoCancelled => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::Printed       => 'heroicon-o-printed',
            self::Paid          => 'heroicon-o-check-badge',
            self::Cancelled     => 'heroicon-o-x-circle',
            self::AutoCancelled => 'heroicon-o-x-circle',
        };
    }


}
