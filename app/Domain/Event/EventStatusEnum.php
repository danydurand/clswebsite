<?php

namespace App\Domain\Event;

// use Filament\Support\Contracts\HasColor;
// use Filament\Support\Contracts\HasIcon;
// use Filament\Support\Contracts\HasLabel;

enum EventStatusEnum: int //implements HasLabel, HasColor, HasIcon
{
    case NotStarted = 0;
    // case InProgress    = 2;
    case Canceled = 90;
    case Postponed = 60;
    case Finished = 100;
    case EndedEat = 110;
    case InProgress = 6;
    case WillContinue = 7;
    case Delayed = 8;
    case Interrupted = 9;
    case Unknown = 10;
    case Preliminary = 11;
    case Closed = 12;
    case NotPlayed = 13;
    case Halftime = 31;


    public static function getValues(): array
    {
        // Extract just the values
        return array_map(fn($case) => $case->value, EventStatusEnum::cases());
    }

    public static function getOptions(): array
    {
        return [
            0 => __('Not Started'),
            // 2   => __('In Progress'),
            90 => __('Canceled'),
            60 => __('Postponed'),
            100 => __('Finished'),
            110 => __('Ended EAT'),
            6 => __('In Progress'),
            7 => __('Will Continue'),
            8 => __('Delayed'),
            9 => __('Interrupted'),
            10 => __('Unknown'),
            11 => __('Preliminary'),
            12 => __('Closed'),
            13 => __('Not Played'),
            31 => __('Halftime'),
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NotStarted => __('Not Started'),
            self::InProgress => __('In Progress'),
            self::Canceled => __('Canceled'),
            self::Postponed => __('Postponed'),
            self::Finished => __('Finished'),
            self::EndedEat => __('Ended EAT'),
                // self::Suspended    => __('Suspended'),
            self::WillContinue => __('Will Continue'),
            self::Delayed => __('Delayed'),
            self::Interrupted => __('Interrupted'),
            self::Unknown => __('Unknown'),
            self::Preliminary => __('Preliminary'),
            self::Closed => __('Closed'),
            self::NotPlayed => __('Not Played'),
            self::Halftime => __('Halftime'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::NotStarted => 'info',
            self::InProgress => 'primary',
            self::Canceled => 'danger',
            self::Postponed => 'warning',
            self::Finished => 'success',
            self::EndedEat => 'success',
                // self::Suspended    => 'danger',
            self::WillContinue => 'info',
            self::Delayed => 'warning',
            self::Interrupted => 'danger',
            self::Unknown => 'secondary',
            self::Preliminary => 'secondary',
            self::Closed => 'danger',
            self::NotPlayed => 'danger',
            self::Halftime => 'primary',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::NotStarted => 'heroicon-o-clock',
            self::InProgress => 'heroicon-o-truck',
            self::Canceled => 'heroicon-o-code-bracket',
            self::Postponed => 'heroicon-o-arrow-turn-right',
            self::Finished => 'heroicon-o-check',
            self::EndedEat => 'heroicon-o-check',
                // self::Suspended    => 'heroicon-o-scissors',
            self::WillContinue => 'heroicon-o-pause-circle',
            self::Delayed => 'heroicon-o-play-pause',
            self::Interrupted => 'heroicon-o-scissors',
            self::Unknown => 'heroicon-o-question-mark-circle',
            self::Preliminary => 'heroicon-o-star',
            self::Closed => 'heroicon-o-lock-closed',
            self::NotPlayed => 'heroicon-o-x-circle',
            self::Halftime => 'heroicon-o-play-pause',
        };
    }


}
