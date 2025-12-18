<?php

namespace App\Domain\InvoicePaymentMethod;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AvailableOnEnum: string implements HasLabel, HasColor, HasIcon
{
    case Web  = 'web';
    case Physical  = 'physical';
    case Both = 'both';


    public static function getValues(): array
    {
        // Extract just the values
        return array_map(fn($case) => $case->value, AvailableOnEnum::cases());
    }

    public static function getOptions(): array
    {
        return [
            'web'      => __('Web'),
            'physical' => __('Physical'),
            'both'     => __('Both'),
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Web      => __('Web'),
            self::Physical => __('Physical'),
            self::Both     => __('Both'),
        };
    }

    public function getColor(): string | array | null
    {
        return match($this) {
            self::Web      => 'warning',
            self::Physical => 'success',
            self::Both     => 'info',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::Web      => 'heroicon-o-globe-alt',
            self::Physical => 'heroicon-o-building-storefront',
            self::Both     => 'heroicon-o-arrow-path-rounded-square',
        };
    }


}
