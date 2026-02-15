<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $companyName = sett('platform-name', 'string', 'DreamBet');

        return (new MailMessage)
            ->subject(__('Welcome to :company!', ['company' => $companyName]))
            ->view('emails.welcome', [
                'user' => $notifiable,
                'companyName' => $companyName,
            ]);
    }
}
