<?php

namespace App\Domain\Transaction;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TransactionDoneEnum: string implements HasLabel, HasColor, HasIcon
{
    case Yes       = 'yes';
    case No        = 'no';
    case Forwarded = 'forwarded';

    public static function getValues(): array
    {
        // Extract just the values
        return array_map(fn($case) => $case->value, TransactionDoneEnum::cases());
    }

    public static function getYesNotOptions(): array
    {
        return [
            'yes'       => __('Yes'),
            'no'        => __('No'),
        ];
    }

    public static function getOptions(): array
    {
        return [
            'yes'       => __('Yes'),
            'no'        => __('No'),
            'forwarded' => __('Forwarded'),
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Yes       => __('Yes'),
            self::No        => __('No'),
            self::Forwarded => __('Forwarded'),
        };
    }

    public function getColor(): string | array | null
    {
        return match($this) {
            self::Yes        => 'success',
            self::No         => 'danger',
            self::Forwarded  => 'warning',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::Yes       => 'heroicon-o-check-circle',
            self::No        => 'heroicon-o-x-circle',
            self::Forwarded => 'heroicon-o-arrow-right-circle',
        };
    }


}
