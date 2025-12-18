<?php

namespace App\Domain\CreditNote;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum CreditNoteStatusEnum: string implements HasLabel, HasColor, HasIcon
{
    case Available     = 'available';
    case PartiallyUsed = 'partially-used';
    case Used          = 'used';


    public static function getValues(): array
    {
        // Extract just the values
        return array_map(fn($case) => $case->value, CreditNoteStatusEnum::cases());
    }

    public static function getOptions(): array
    {
        return [
            'available'      => __('Available'),
            'partially-used' => __('Partially-Used'),
            'used'           => __('Used'),
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Available     => __('Available'),
            self::PartiallyUsed => __('Partially-Used'),
            self::Used          => __('Used'),
        };
    }

    public function getColor(): string | array | null
    {
        return match($this) {
            self::Available     => 'info',
            self::PartiallyUsed => 'success',
            self::Used          => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::Available     => 'heroicon-o-clock',
            self::PartiallyUsed => 'heroicon-o-chart-pie',
            self::Used          => 'heroicon-o-check-circle',
        };
    }


}
