<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\ActivityLog;
use App\Models\Announcement;
use App\Models\ContactMessage;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\GalleryItem;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $stats = Cache::remember('admin.stats', 30, function () {
            return [
                'members' => User::members()->count(),
                'active_members' => User::members()->where('status', 'active')->count(),
                'events' => Event::query()->count(),
                'upcoming' => Event::upcoming()->count(),
                'announcements' => Announcement::query()->count(),
                'achievements' => Achievement::query()->count(),
                'gallery' => GalleryItem::query()->count(),
                'registrations' => EventRegistration::query()->count(),
                'unread_messages' => ContactMessage::query()->where('status', 'unread')->count(),
                'active_admins' => User::admins()->where('status', 'active')->count(),
                'admins' => User::admins()->count(),
                'suspended_admins' => User::admins()->where('status', 'suspended')->count(),
                'pending_achievements' => Achievement::query()->whereIn('status', ['pending', 'submitted'])->count(),
                'pending_members' => User::members()->where('status', 'pending')->count(),
            ];
        });

        return view('admin.dashboard', [
            'stats' => $stats,
            'activity' => ActivityLog::query()->with('user')->latest('created_at')->limit(8)->get(),
            'upcoming' => Event::upcoming()->orderBy('starts_at')->limit(5)->get(),
            'registrations' => EventRegistration::query()->with('event')->latest()->limit(5)->get(),
            'pendingAchievements' => Achievement::query()->with('submitter')->whereIn('status', ['pending', 'submitted'])->latest()->limit(5)->get(),
            'system' => [
                'php' => PHP_VERSION,
                'laravel' => app()->version(),
                'cache' => config('cache.default'),
                'queue' => config('queue.default'),
                'database' => config('database.default'),
                'environment' => app()->environment(),
                'debug' => (bool) config('app.debug'),
            ],
        ]);
    }
}
