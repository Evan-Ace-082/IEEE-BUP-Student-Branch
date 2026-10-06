<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ContactMessage $contactMessage, public bool $byEmail = false) {}

    public function via(object $notifiable): array
    {
        return $this->byEmail ? ['mail'] : ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New contact message: '.$this->contactMessage->subject)
            ->line($this->contactMessage->name.' ('.$this->contactMessage->email.') wrote:')
            ->line($this->contactMessage->message)
            ->action('Open messages', route('admin.messages.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New contact message',
            'body' => $this->contactMessage->subject,
            'url' => route('admin.messages.index'),
        ];
    }
}
