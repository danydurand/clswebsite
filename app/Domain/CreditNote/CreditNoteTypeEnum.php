<?php

namespace App\Domain\CreditNote;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum CreditNoteTypeEnum: string implements HasLabel, HasColor, HasIcon
{
    case Automatic = 'automatic';
    case Manual    = 'manual';


    public static function getValues(): array
    {
        // Extract just the values
        return array_map(fn($case) => $case->value, CreditNoteTypeEnum::cases());
    }

    public static function getOptions(): array
    {
        return [
            'automatic' => __('Automatic'),
            'manual'    => __('Manual'),
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Automatic => __('Automatic'),
            self::Manual    => __('Manual'),
        };
    }

    public function getColor(): string | array | null
    {
        return match($this) {
            self::Automatic => 'info',
            self::Manual    => 'success',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::Automatic => 'heroicon-o-cursor-arrow-rays',
            self::Manual    => 'heroicon-o-wrench',
        };
    }


}
