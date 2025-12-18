<?php

namespace App\Domain\LoanDetail;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum FeeStatusEnum: string implements HasLabel, HasColor, HasIcon
{
    case Pending = 'pending';
    case Paid    = 'paid';
    case Late    = 'late';


    public static function getValues(): array
    {
        // Extract just the values
        return array_map(fn($case) => $case->value, FeeStatusEnum::cases());
    }

    public static function getOptions(): array
    {
        return [
            'pending' => __('Pending'),
            'paid'    => __('Paid'),
            // 'late'    => __('Late'),
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => __('Pending'),
            self::Paid    => __('Paid'),
            self::Late    => __('Late'),
        };
    }

    public function getColor(): string | array | null
    {
        return match($this) {
            self::Pending  => 'warning',
            self::Paid     => 'success',
            self::Late     => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::Pending => 'heroicon-o-clock',
            self::Late    => 'heroicon-o-archive-box-x-mark',
            self::Paid    => 'heroicon-o-check-circle',
        };
    }


}
