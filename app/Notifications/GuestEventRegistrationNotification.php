<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GuestEventRegistrationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Event $event, public string $name) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Registration confirmed: '.$this->event->title)
            ->greeting('Hello '.$this->name.',')
            ->line('You are registered for '.$this->event->title.'.')
            ->line($this->event->starts_at?->format('d M Y, h:i A').($this->event->venue ? ' · '.$this->event->venue : ''))
            ->action('View event', route('events.show', $this->event));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Event registration confirmed',
            'body' => $this->event->title,
            'url' => route('events.show', $this->event),
        ];
    }
}
