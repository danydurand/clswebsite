<?php

namespace App\Domain\Ticket;

//use Filament\Support\Contracts\HasColor;
//use Filament\Support\Contracts\HasIcon;
//use Filament\Support\Contracts\HasLabel;

enum PaymentStatusEnum: string //implements HasLabel, HasColor, HasIcon
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => __('Pending'),
            self::Accepted => __('Accepted'),
            self::Rejected => __('Rejected'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending => 'danger',
            self::Accepted => 'success',
            self::Rejected => 'warning',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Pending => 'heroicon-o-chat-bubble-oval-left-ellipsis',
            self::Accepted => 'heroicon-o-check-badge',
            self::Rejected => 'heroicon-o-x-circle',
        };
    }


}
