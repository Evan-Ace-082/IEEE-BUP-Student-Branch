<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\BranchNoticeNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class SendBranchNoticeChunk implements ShouldQueue
{
    use Queueable;

    /**
     * @param  list<int>  $userIds
     */
    public function __construct(
        public array $userIds,
        public string $title,
        public string $body,
        public string $url
    ) {}

    public function handle(): void
    {
        $users = User::query()->whereIn('id', $this->userIds)->where('status', 'active')->get();

        if ($users->isEmpty()) {
            return;
        }

        Notification::sendNow($users, new BranchNoticeNotification($this->title, $this->body, $this->url));
    }
}
