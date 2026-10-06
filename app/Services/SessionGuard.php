<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SessionGuard
{
    public static function invalidateUser(int $userId): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        DB::table(config('session.table', 'sessions'))->where('user_id', $userId)->delete();
    }
}
