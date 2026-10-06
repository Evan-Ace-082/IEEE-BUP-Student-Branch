<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $user->load(['profile', 'registrations.event', 'submittedAchievements']);

        $upcoming = Event::upcoming()->orderBy('starts_at')->limit(3)->get();

        return view('member.dashboard', [
            'user' => $user,
            'upcoming' => $upcoming,
            'registrations' => $user->registrations->sortByDesc('id')->take(5),
            'achievements' => $user->submittedAchievements->sortByDesc('id')->take(5),
        ]);
    }
}
