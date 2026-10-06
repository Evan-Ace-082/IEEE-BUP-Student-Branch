<?php

namespace App\Notifications;

use App\Models\Achievement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AchievementReviewedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Achievement $achievement) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Achievement update: '.$this->achievement->title)
            ->line('Your achievement "'.$this->achievement->title.'" is now marked '.$this->achievement->status.'.');

        if ($this->achievement->review_note) {
            $mail->line($this->achievement->review_note);
        }

        return $mail->action('View achievements', route('achievements.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Achievement '.$this->achievement->status,
            'body' => $this->achievement->title,
            'url' => route('member.achievements'),
        ];
    }
}
