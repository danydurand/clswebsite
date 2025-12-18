<?php

namespace App\Domain\InvoicePayment;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum InvoicePaymentStatusEnum: string implements HasLabel, HasColor, HasIcon
{
    case WaitingValidation = 'waiting-validation';
    case Approved = 'approved';
    case Rejected = 'rejected';


    public static function getValues(): array
    {
        // Extract just the values
        return array_map(fn($case) => $case->value, InvoicePaymentStatusEnum::cases());
    }

    public static function getOptions(): array
    {
        return [
            'waiting-validation' => __('Waiting validation'),
            'approved' => __('Approved'),
            'rejected' => __('Rejected'),
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::WaitingValidation => __('Waiting Validation'),
            self::Approved    => __('Approved'),
            self::Rejected    => __('Rejected'),
        };
    }

    public function getColor(): string | array | null
    {
        return match($this) {
            self::WaitingValidation => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::WaitingValidation => 'heroicon-o-clock',
            self::Rejected => 'heroicon-o-archive-box-x-mark',
            self::Approved => 'heroicon-o-check-circle',
        };
    }


}
