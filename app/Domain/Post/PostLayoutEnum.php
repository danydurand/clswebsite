<?php

namespace App\Domain\Post;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PostLayoutEnum: string implements HasLabel, HasColor, HasIcon
{
    case FullWidth = 'full-width';
    case SideBySide = 'side-by-side';
    case TextOverImage = 'text-over-image';
    case TextUnderImage = 'text-under-image';


    public static function getValues(): array
    {
        // Extract just the values
        return array_map(fn($case) => $case->value, PostLayoutEnum::cases());
    }

    public static function getOptions(): array
    {
        return [
            'full-width'       => __('Full Width'),
            'side-by-side'     => __('Side by Side'),
            'text-over-image'  => __('Text Over Image'),
            'text-under-image' => __('Text Under Image'),
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::FullWidth      => __('Full Width'),
            self::SideBySide     => __('Side by Side'),
            self::TextOverImage  => __('Text Over Image'),
            self::TextUnderImage => __('Text Under Image'),
        };
    }

    public function getColor(): string | array | null
    {
        return match($this) {
            self::FullWidth      => 'primary',
            self::SideBySide     => 'success',
            self::TextOverImage  => 'warning',
            self::TextUnderImage => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::FullWidth      => 'heroicon-o-code-bracket-square',
            self::SideBySide     => 'heroicon-o-arrows-right-left',
            self::TextOverImage  => 'heroicon-o-arrow-up-tray',
            self::TextUnderImage => 'heroicon-o-arrow-down-tray',
        };
    }


}
