<?php

namespace App\Domain\Bet;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum BetStatusEnum: string implements HasLabel, HasColor, HasIcon
{
    case Win = 'win';
    case Pending = 'pending';
    case Lose = 'lose';
    case Refunded = 'refunded';
    case Nullified = 'nullified';
    case AutoNullified = 'auto-nullified';
    case Paid = 'paid';


    public static function getValues($except): array
    {
        if (!is_array($except)) {
            $except = [$except];
        }
        // Extract just the values
        return array_values(array_map(
            fn($case) => $case->value,
            array_filter(
                self::cases(),
                fn($case) => !in_array($case->value, $except, true)
            )
        ));
    }

    public static function getOptions(): array
    {
        return [
            'win' => __('Win'),
            'pending' => __('Pending'),
            'lose' => __('Lose'),
            'refunded' => __('Refunded'),
            'nullified' => __('Nullified'),
            'auto-nullified' => __('Auto Nullified'),
            'paid' => __('Paid'),
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Win => __('Win'),
            self::Pending => __('Pending'),
            self::Lose => __('Lose'),
            self::Refunded => __('Refunded'),
            self::Nullified => __('Nullified'),
            self::AutoNullified => __('Auto Nullified'),
            self::Paid => __('Paid'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Win => 'success',
            self::Pending => 'warning',
            self::Lose => 'danger',
            self::Refunded => 'info',
            self::Nullified => 'secondary',
            self::AutoNullified => 'secondary',
            self::Paid => 'success',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Win => 'heroicon-o-hand-thumb-up',
            self::Pending => 'heroicon-o-clock',
            self::Lose => 'heroicon-o-hand-thumb-down',
            self::Refunded => 'heroicon-o-currency-dollar',
            self::Nullified => 'heroicon-o-no-symbol',
            self::AutoNullified => 'heroicon-o-no-symbol',
            self::Paid => 'heroicon-o-check-badge',
        };
    }


}
