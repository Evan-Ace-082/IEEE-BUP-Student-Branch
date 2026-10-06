<?php

namespace App\Services;

use App\Jobs\SendBranchNoticeChunk;
use App\Models\User;

class BranchNotifier
{
    public static function toActiveUsers(string $title, string $body, string $url): void
    {
        User::query()
            ->where('status', 'active')
            ->select('id')
            ->orderBy('id')
            ->chunkById(200, function ($users) use ($title, $body, $url) {
                SendBranchNoticeChunk::dispatch(
                    $users->pluck('id')->map(fn ($id) => (int) $id)->all(),
                    $title,
                    $body,
                    $url
                );
            });
    }
}
