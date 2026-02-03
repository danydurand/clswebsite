<?php

namespace App\Domain\Ticket;

enum TicketActionEnum: string
{
    case Printed = 'printed';
    case Paid = 'paid';
    case Cancelled = 'cancelled';
    case AutoCancelled = 'auto-cancelled';
    case EmailSent = 'email-sent';
    case WhatsAppSent = 'whatsapp-sent';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Printed => __('Printed'),
            self::Paid => __('Paid'),
            self::Cancelled => __('Cancelled'),
            self::AutoCancelled => __('AutoCancelled'),
            self::EmailSent => __('Email Sent'),
            self::WhatsAppSent => __('WhatsApp Sent'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Printed => 'warning',
            self::Paid => 'success',
            self::Cancelled => 'danger',
            self::AutoCancelled => 'danger',
            self::EmailSent => 'info',
            self::WhatsAppSent => 'info',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Printed => 'heroicon-o-printed',
            self::Paid => 'heroicon-o-check-badge',
            self::Cancelled => 'heroicon-o-x-circle',
            self::AutoCancelled => 'heroicon-o-x-circle',
            self::EmailSent => 'heroicon-o-envelope',
            self::WhatsAppSent => 'heroicon-o-chat-bubble-left-right',
        };
    }


}
