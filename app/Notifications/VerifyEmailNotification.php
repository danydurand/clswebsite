<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;

class VerifyEmailNotification extends BaseVerifyEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        $companyName = sett('platform-name', 'string', 'DreamBet');

        return (new MailMessage)
            ->subject(__('Verify Your Email Address'))
            ->view('emails.verify-email', [
                'verificationUrl' => $verificationUrl,
                'user' => $notifiable,
                'companyName' => $companyName,
            ]);
    }
}
