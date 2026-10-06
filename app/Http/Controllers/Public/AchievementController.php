<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function index(Request $request)
    {
        $query = Achievement::published();

        if ($category = $request->string('category')->toString()) {
            if (array_key_exists($category, Achievement::categories())) {
                $query->where('category', $category);
            }
        }

        if ($search = trim($request->string('q')->toString())) {
            $query->where(function ($inner) use ($search) {
                $inner->where('title', 'like', like_term($search))
                    ->orWhere('person_or_team', 'like', like_term($search));
            });
        }

        $achievements = $query->orderByDesc('achieved_on')->orderByDesc('id')->paginate(9)->withQueryString();

        return view('public.achievements.index', [
            'achievements' => $achievements,
            'categories' => Achievement::categories(),
        ]);
    }

    public function show(Achievement $achievement)
    {
        if ($achievement->status !== 'published' && ! auth()->user()?->hasAdminAccess()) {
            abort(404);
        }

        return view('public.achievements.show', compact('achievement'));
    }
}
