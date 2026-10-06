<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AchievementRequest;
use App\Models\Achievement;
use App\Notifications\AchievementReviewedNotification;
use App\Services\ActivityLogger;
use App\Services\SecureUpload;
use App\Support\SettingsStore;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function index(Request $request)
    {
        $query = Achievement::query()->with('submitter')->latest();

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($search = trim($request->string('q')->toString())) {
            $query->where('title', 'like', like_term($search));
        }

        return view('admin.achievements.index', [
            'achievements' => $query->paginate(12)->withQueryString(),
            'statuses' => Achievement::statuses(),
        ]);
    }

    public function create()
    {
        return view('admin.achievements.form', [
            'achievement' => new Achievement(['status' => 'published', 'category' => 'branch']),
            'categories' => Achievement::categories(),
            'statuses' => Achievement::statuses(),
        ]);
    }

    public function store(AchievementRequest $request)
    {
        $data = $this->payload($request);
        $data['reviewed_by'] = $request->user()->id;
        $data['reviewed_at'] = now();

        if ($request->file('image')) {
            $data['image'] = SecureUpload::image($request->file('image'), 'achievements');
        }

        $achievement = Achievement::query()->create($data);
        ActivityLogger::log('create', 'achievements', $achievement->id, 'Created achievement');
        SettingsStore::bust();

        return redirect()->route('admin.achievements.edit', $achievement)->with('status', 'Achievement saved.');
    }

    public function edit(Achievement $achievement)
    {
        $achievement->load('submitter');

        return view('admin.achievements.form', [
            'achievement' => $achievement,
            'categories' => Achievement::categories(),
            'statuses' => Achievement::statuses(),
        ]);
    }

    public function update(AchievementRequest $request, Achievement $achievement)
    {
        $previous = $achievement->status;
        $data = $this->payload($request);

        if ($request->boolean('remove_image') && $achievement->image) {
            SecureUpload::delete($achievement->image);
            $data['image'] = null;
        }

        if ($request->file('image')) {
            SecureUpload::delete($achievement->image);
            $data['image'] = SecureUpload::image($request->file('image'), 'achievements');
        }

        if ($previous !== $data['status']) {
            $data['reviewed_by'] = $request->user()->id;
            $data['reviewed_at'] = now();
        }

        $achievement->update($data);
        $this->notify($achievement, $previous);
        ActivityLogger::log('update', 'achievements', $achievement->id, 'Updated achievement status to '.$achievement->status);
        SettingsStore::bust();

        return redirect()->route('admin.achievements.edit', $achievement)->with('status', 'Achievement saved.');
    }

    public function destroy(Achievement $achievement)
    {
        $achievement->delete();
        ActivityLogger::log('delete', 'achievements', $achievement->id, 'Deleted achievement');
        SettingsStore::bust();

        return redirect()->route('admin.achievements.index')->with('status', 'Achievement removed.');
    }

    private function payload(AchievementRequest $request): array
    {
        $data = $request->safe()->only([
            'title', 'description', 'person_or_team', 'category', 'achieved_on', 'link', 'status', 'review_note',
        ]);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['person_or_team'] = $data['person_or_team'] ?: null;
        $data['link'] = safe_url($data['link'] ?? null);
        $data['review_note'] = $data['review_note'] ?: null;
        $data['achieved_on'] = $data['achieved_on'] ?: null;

        return $data;
    }

    private function notify(Achievement $achievement, string $previous): void
    {
        if ($previous === $achievement->status || ! $achievement->submitter) {
            return;
        }

        $action = match ($achievement->status) {
            'approved', 'published' => 'approve',
            'rejected' => 'reject',
            default => 'update',
        };

        ActivityLogger::log($action, 'achievements', $achievement->id, 'Reviewed achievement');
        $achievement->submitter->notify(new AchievementReviewedNotification($achievement));
    }
}
