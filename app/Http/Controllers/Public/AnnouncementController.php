<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::published()->with('author');

        if ($category = $request->string('category')->toString()) {
            if (array_key_exists($category, Announcement::categories())) {
                $query->where('category', $category);
            }
        }

        if ($search = trim($request->string('q')->toString())) {
            $query->where('title', 'like', like_term($search));
        }

        $announcements = $query->latest('published_at')->paginate(9)->withQueryString();

        return view('public.announcements.index', [
            'announcements' => $announcements,
            'categories' => Announcement::categories(),
        ]);
    }

    public function show(Announcement $announcement)
    {
        if ($announcement->status !== 'published' && ! auth()->user()?->hasAdminAccess()) {
            abort(404);
        }

        $announcement->load('author');

        return view('public.announcements.show', compact('announcement'));
    }
}
