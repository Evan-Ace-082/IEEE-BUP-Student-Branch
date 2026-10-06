<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\SubmitAchievementRequest;
use App\Models\Achievement;
use App\Services\ActivityLogger;
use App\Services\SecureUpload;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function index(Request $request)
    {
        $achievements = Achievement::query()
            ->where('submitted_by', $request->user()->id)
            ->latest()
            ->paginate(12);

        return view('member.achievements', [
            'achievements' => $achievements,
            'categories' => Achievement::categories(),
        ]);
    }

    public function store(SubmitAchievementRequest $request)
    {
        $data = $request->safe()->only(['title', 'description', 'person_or_team', 'category', 'achieved_on', 'link']);
        $data['person_or_team'] = $data['person_or_team'] ?: $request->user()->name;
        $data['link'] = safe_url($data['link'] ?? null);
        $data['status'] = 'pending';
        $data['submitted_by'] = $request->user()->id;
        $data['is_featured'] = false;

        if ($request->file('image')) {
            $data['image'] = SecureUpload::image($request->file('image'), 'achievements');
        }

        $achievement = Achievement::query()->create($data);
        ActivityLogger::log('create', 'achievements', $achievement->id, 'Member submitted an achievement');

        return redirect()->route('member.achievements')->with('status', 'Achievement submitted for review.');
    }
}
