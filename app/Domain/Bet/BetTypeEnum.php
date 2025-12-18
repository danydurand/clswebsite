<?php

namespace App\Domain\Bet;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum BetTypeEnum: string implements HasLabel, HasColor, HasIcon
{
    case Single = 'single';
    case Multi = 'multi';


    public static function getValues(): array
    {
        // Extract just the values
        return array_map(fn($case) => $case->value, BetTypeEnum::cases());
    }

    public static function getOptions(): array
    {
        return [
            'single' => __('Single'),
            'multi' => __('Multi'),
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Single => __('Single'),
            self::Multi => __('Multi'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Single => 'primary',
            self::Multi => 'info',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Single => 'heroicon-o-document',
            self::Multi => 'heroicon-o-document-duplicate',
        };
    }


}
