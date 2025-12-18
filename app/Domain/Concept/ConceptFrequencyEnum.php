<?php

namespace App\Domain\Concept;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ConceptFrequencyEnum: int implements HasLabel, HasColor, HasIcon
{
    case Once = 0;
    case Weekly = 7;
    case Biweekly = 15;
    case Monthly = 30;
    case Annually = 365;

    public static function getOptions(): array
    {
        return [
            0   => __('Once'),
            7   => __('Weekly'),
            15  => __('Biweekly'),
            30  => __('Monthly'),
            365 => __('Anunually'),
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Once     => __('Once'),
            self::Weekly   => __('Weekly'),
            self::Biweekly => __('Biweekly'),
            self::Monthly  => __('Monthly'),
            self::Annually => __('Annually'),
        };
    }

    public function getColor(): string | array | null
    {
        return match($this) {
            self::Once     => 'primary',
            self::Weekly   => 'info',
            self::Biweekly => 'success',
            self::Monthly  => 'warning',
            self::Annually => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::Once     => 'heroicon-o-clock',
            self::Weekly   => 'heroicon-o-clock',
            self::Biweekly => 'heroicon-o-clock',
            self::Monthly  => 'heroicon-o-clock',
            self::Annually => 'heroicon-o-clock',
        };
    }


}
