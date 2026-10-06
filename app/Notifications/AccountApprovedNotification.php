<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your BUP IEEE account is active')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('An administrator approved your BUP IEEE Student Branch account.')
            ->action('Open your dashboard', route('member.dashboard'))
            ->line('You can now manage your profile, register for events, and submit achievements.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Account approved',
            'body' => 'Your member account is active.',
            'url' => route('member.dashboard'),
        ];
    }
}
