<?php

namespace App\Services;

use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class NotificationServices
{

    public static function errorDetails($strUserMess)
    {
        Notification::make()
            ->title('Please check the Errors List')
            ->body($strUserMess)
            ->icon('heroicon-o-hand-raised')
            ->iconColor('danger')
            ->danger()
            ->persistent()
            ->actions([
                Action::make('view')
                    ->label('View Errors')
                    ->button()
                    ->color('danger')
                    ->url('/admin/error-details')
            ])
            ->send();
    }


    public static function warning($title, $body='', $persist=false)
    {
        $notification = Notification::make()
            ->title($title)
            ->body(fn ():string => strlen($body) ? $body : '')
            ->icon('heroicon-o-information-circle')
            ->iconColor('warning')
            ->warning();
        if ($persist) {
            $notification->persistent();
        }
        $notification->send();
    }

    public static function success($title, $body='', $persist=false)
    {
        $notification = Notification::make()
            ->title($title)
            ->body(fn ():string => strlen($body) ? $body : '')
            ->icon('heroicon-o-check-circle')
            ->iconColor('success')
            ->success();
        if ($persist) {
            $notification->persistent();
        }
        $notification->send();
    }

    public static function successWithAction($title, $body='', $route='', $persist=true)
    {
        $notification = Notification::make()
            ->title($title)
            ->body(fn ():string => strlen($body) ? $body : '')
            ->icon('heroicon-o-check-circle')
            ->iconColor('success')
            ->success();
        if (strlen($route)) {
            $notification->actions([
                Action::make('view')
                    ->button()
                    ->url(route($route))
            ]);
        }
        if ($persist) {
            $notification->persistent();
        }
        $notification->send();
    }

    public static function danger($strUserMess, $strBodyMess='', $blnPersMess=true)
    {
        $notification = Notification::make()
            ->title($strUserMess)
            ->body(fn ():string => strlen($strBodyMess) ? $strBodyMess : '')
            ->icon('heroicon-o-hand-raised')
            ->iconColor('danger')
            ->danger();
        if ($blnPersMess) {
            $notification->persistent();
        }
        $notification->send();
    }

}
