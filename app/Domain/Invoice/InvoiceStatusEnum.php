<?php

namespace App\Domain\Invoice;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum InvoiceStatusEnum: string implements HasLabel, HasColor, HasIcon
{
    case Pending       = 'pending';
    case Paid          = 'paid';
    case PartiallyPaid = 'partially-paid';
    case Nullified     = 'nullified';


    public static function getValues(): array
    {
        // Extract just the values
        return array_map(fn($case) => $case->value, InvoiceStatusEnum::cases());
    }

    public static function getOptions(): array
    {
        return [
            'pending'        => __('Pending'),
            'paid'           => __('Paid'),
            'partially-paid' => __('Paid'),
            'nullified'      => __('Nullified'),
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending       => __('Pending'),
            self::Paid          => __('Paid'),
            self::PartiallyPaid => __('Partially Paid'),
            self::Nullified     => __('Nullified'),
        };
    }

    public function getColor(): string | array | null
    {
        return match($this) {
            self::Pending       => 'warning',
            self::Paid          => 'success',
            self::PartiallyPaid => 'info',
            self::Nullified     => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::Pending       => 'heroicon-o-clock',
            self::Paid          => 'heroicon-o-check-circle',
            self::PartiallyPaid => 'heroicon-o-no-symbol',
            self::Nullified     => 'heroicon-o-x-circle',
        };
    }


}
