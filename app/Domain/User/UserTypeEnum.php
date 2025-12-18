<?php

namespace App\Domain\User;

// use Filament\Support\Contracts\HasColor;
// use Filament\Support\Contracts\HasIcon;
// use Filament\Support\Contracts\HasLabel;

enum UserTypeEnum: string //implements HasLabel, HasColor, HasIcon
{
    case Master = 'master';
    case Admin = 'admin';
    case Owner = 'owner';
    case Banker = 'banker';
    case Internal = 'internal';
    case Customer = 'customer';
    case Supervisor = 'supervisor';
    case Seller = 'seller';
    case Secretary = 'secretary';

    public static function getBankStaff(): array
    {
        return [
            'secretary' => __('Secretary'),
        ];
    }

    public function getInitial(): ?string
    {
        return match ($this) {
            self::Admin => 'A',
            self::Master => 'M',
            self::Owner => 'O',
            self::Banker => 'B',
            self::Internal => 'I',
            self::Customer => 'C',
            self::Supervisor => 'S',
            self::Secretary => 'Y',
        };
    }

    public static function initial($type): ?string
    {

        return match ($type) {
            self::Admin->value => 'A',
            self::Master->value => 'M',
            self::Owner->value => 'O',
            self::Banker->value => 'B',
            self::Internal->value => 'I',
            self::Customer->value => 'C',
            self::Supervisor->value => 'S',
            self::Secretary->value => 'Y',
            default => 'N',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Admin => __('Admin'),
            self::Master => __('Master'),
            self::Owner => __('Owner'),
            self::Banker => __('Baker'),
            self::Internal => __('Internal'),
            self::Customer => __('Customer'),
            self::Supervisor => __('Supervisor'),
            self::Seller => __('Seller'),
            self::Secretary => __('Secretary'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Master => 'danger',
            self::Admin => 'warning',
            self::Owner => 'warning',
            self::Banker => 'warning',
            self::Internal => 'primary',
            self::Customer => 'success',
            self::Supervisor => 'success',
            self::Seller => 'info',
            self::Secretary => 'info',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Master => 'heroicon-o-rocket-launch',
            self::Admin => 'heroicon-o-rocket-launch',
            self::Owner => 'heroicon-o-rocket-launch',
            self::Banker => 'heroicon-o-rocket-launch',
            self::Internal => 'heroicon-o-wrench-screwdriver',
            self::Customer => 'heroicon-o-users',
            self::Supervisor => 'heroicon-o-users',
            self::Seller => 'heroicon-o-users',
            self::Secretary => 'heroicon-o-users',
        };
    }


}
