<?php

namespace App\Domain\Route;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum RouteStatusEnum: string implements HasLabel, HasColor, HasIcon
{
    case Pending = 'pending';
    case InProgress = 'in-progress';
    case Finished = 'finished';

    public static function getValues(): array
    {
        // Extract just the values
        return array_map(fn($case) => $case->value, RouteStatusEnum::cases());
    }

    public static function getOptions(): array
    {
        return [
            'pending'     => __('Pending'),
            'in-progress' => __('InProgress'),
            'finished'    => __('Finished'),
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending    => __('Pending'),
            self::InProgress => __('InProgress'),
            self::Finished   => __('Finished'),
        };
    }

    public function getColor(): string | array | null
    {
        return match($this) {
            self::Pending    => 'danger',
            self::InProgress => 'info',
            self::Finished   => 'success',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::Pending    => 'heroicon-o-clock',
            self::InProgress => 'heroicon-o-truck',
            self::Finished   => 'heroicon-o-check-badge',
        };
    }


}
