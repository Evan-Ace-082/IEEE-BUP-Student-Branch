<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Announcement;
use App\Models\CommitteeMember;
use App\Models\Event;
use App\Models\ExecutiveCommittee;
use App\Models\GalleryItem;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function __invoke()
    {
        $payload = Cache::remember('home.payload', 60, function () {
            $current = ExecutiveCommittee::query()->where('is_current', true)->first();

            $committee = $current
                ? CommitteeMember::query()
                    ->with('position')
                    ->where('executive_committee_id', $current->id)
                    ->get()
                    ->sortBy(fn ($member) => sprintf('%05d-%05d', $member->position?->sort_order ?? 999, $member->sort_order))
                    ->take(4)
                    ->values()
                : collect();

            $eventCard = function (Event $event): array {
                return [
                    'title' => $event->title,
                    'slug' => $event->slug,
                    'starts_at' => $event->starts_at?->toIso8601String(),
                    'venue' => $event->venue,
                    'description' => $event->description,
                    'banner' => $event->banner,
                    'category' => $event->category,
                    'temporal' => $event->temporalStatus(),
                ];
            };

            $featured = Achievement::published()->where('is_featured', true)->latest('achieved_on')->first()
                ?? Achievement::published()->latest('achieved_on')->first();

            return [
                'upcoming' => Event::upcoming()->orderBy('starts_at')->limit(3)->get()->map($eventCard)->all(),
                'announcements' => Announcement::published()->latest('published_at')->limit(3)->get()->map(fn (Announcement $item) => [
                    'title' => $item->title,
                    'slug' => $item->slug,
                    'category' => $item->category,
                    'published_at' => $item->published_at?->toIso8601String(),
                ])->all(),
                'featured' => $featured ? [
                    'id' => $featured->id,
                    'title' => $featured->title,
                    'description' => $featured->description,
                    'image' => $featured->image,
                    'category' => $featured->category,
                ] : null,
                'activities' => Event::past()->latest('ends_at')->limit(3)->get()->map($eventCard)->all(),
                'committee' => $committee->map(fn (CommitteeMember $member) => [
                    'name' => $member->name,
                    'photo' => $member->photo,
                    'position' => $member->position?->name,
                ])->all(),
                'gallery' => GalleryItem::query()->with('album')->whereHas('album')->latest()->limit(6)->get()->map(fn (GalleryItem $item) => [
                    'image' => $item->image,
                    'caption' => $item->caption,
                    'album_slug' => $item->album?->slug,
                    'album_title' => $item->album?->title,
                ])->all(),
                'stats' => [
                    'members' => User::members()->where('status', 'active')->count(),
                    'events' => Event::published()->count(),
                    'workshops' => Event::published()->where('category', 'workshop')->count(),
                    'achievements' => Achievement::published()->count(),
                    'committee' => $current ? CommitteeMember::query()->where('executive_committee_id', $current->id)->count() : 0,
                ],
            ];
        });

        return view('public.home', $payload);
    }
}
