<?php

namespace App\Domain\Ticket;

//use Filament\Support\Contracts\HasColor;
//use Filament\Support\Contracts\HasIcon;
//use Filament\Support\Contracts\HasLabel;

enum TicketStatusEnum: string //implements HasLabel, HasColor, HasIcon
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Cancelled = 'cancelled';
    case Winner = 'winner';
    case Looser = 'looser';
    case AutoCancelled = 'auto-cancelled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => __('Pending'),
            self::Paid => __('Paid'),
            self::Cancelled => __('Cancelled'),
            self::Winner => __('Winner'),
            self::Looser => __('Looser'),
            self::AutoCancelled => __('AutoCancelled'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Paid => 'success',
            self::Cancelled => 'secondary',
            self::Winner => 'success',
            self::Looser => 'danger',
            self::AutoCancelled => 'info',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Pending => 'heroicon-o-chat-bubble-oval-left-ellipsis',
            self::Paid => 'heroicon-o-check-badge',
            self::Cancelled => 'heroicon-o-x-circle',
            self::Winner => 'heroicon-o-check-badge',
            self::Looser => 'heroicon-o-hand-thumb-down',
            self::AutoCancelled => 'heroicon-o-x-circle',
        };
    }


}
