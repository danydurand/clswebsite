<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use App\Notifications\VerifyEmailNotification;

class SendEmailVerificationNotification
{
    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $user = $event->user;

        if ($user instanceof \App\Models\User && !$user->hasVerifiedEmail()) {
            $user->notify(new VerifyEmailNotification());
        }
    }
}
