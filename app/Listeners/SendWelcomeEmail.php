<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;
use App\Notifications\WelcomeEmailNotification;

class SendWelcomeEmail
{
    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        $user = $event->user;

        // Only send welcome email to customer users
        if ($user instanceof \App\Models\User && $user->type === 'customer') {
            // Send welcome email
            $user->notify(new WelcomeEmailNotification());

            // Add welcome flash message (using flash instead of push to avoid duplication)
            session()->flash('flash_success', [
                __('Welcome to :company! Your email has been verified successfully.', [
                    'company' => config('app.name')
                ])
            ]);
        }
    }
}
