<?php

namespace App\Domain\Deposit;

enum DepositStatusEnum: string
{
    case Initiate = 'initiate';
    case Pending = 'pending';
    case Success = 'success';
    case Reject = 'reject';


    public static function getValues($except = []): array
    {
        if (!is_array($except)) {
            $except = [$except];
        }
        // Extract just the values
        return array_values(array_map(
            fn($case) => $case->value,
            array_filter(
                self::cases(),
                fn($case) => !in_array($case->value, $except, true)
            )
        ));
    }

    public static function getOptions(): array
    {
        return [
            'initiate' => __('Initiate'),
            'pending' => __('Pending'),
            'success' => __('Success'),
            'reject' => __('Reject'),
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Initiate => __('Initiate'),
            self::Pending => __('Pending'),
            self::Success => __('Success'),
            self::Reject => __('Reject'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Initiate => 'info',
            self::Pending => 'warning',
            self::Success => 'success',
            self::Reject => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Initiate => 'heroicon-o-hand-thumb-up',
            self::Pending => 'heroicon-o-clock',
            self::Success => 'heroicon-o-check-badge',
            self::Reject => 'heroicon-o-x-mark',
        };
    }


}
