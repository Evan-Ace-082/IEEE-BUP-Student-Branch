<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AnnouncementRequest;
use App\Models\Announcement;
use App\Services\ActivityLogger;
use App\Services\BranchNotifier;
use App\Services\SecureUpload;
use App\Support\SettingsStore;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::query()->with('author')->latest();

        if ($search = trim($request->string('q')->toString())) {
            $query->where('title', 'like', like_term($search));
        }

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        return view('admin.announcements.index', [
            'announcements' => $query->paginate(12)->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('admin.announcements.form', [
            'announcement' => new Announcement(['status' => 'draft', 'category' => 'announcement']),
            'categories' => Announcement::categories(),
            'statuses' => Announcement::statuses(),
        ]);
    }

    public function store(AnnouncementRequest $request)
    {
        $data = $this->payload($request);
        $data['slug'] = unique_slug(Announcement::class, $data['title']);
        $data['author_id'] = $request->user()->id;

        if ($request->file('featured_image')) {
            $data['featured_image'] = SecureUpload::image($request->file('featured_image'), 'announcements');
        }

        $announcement = Announcement::query()->create($data);
        $this->afterSave($announcement, null);
        ActivityLogger::log('create', 'announcements', $announcement->id, 'Created announcement');
        SettingsStore::bust();

        return redirect()->route('admin.announcements.edit', $announcement)->with('status', 'Announcement created.');
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.form', [
            'announcement' => $announcement,
            'categories' => Announcement::categories(),
            'statuses' => Announcement::statuses(),
        ]);
    }

    public function update(AnnouncementRequest $request, Announcement $announcement)
    {
        $previous = $announcement->status;
        $data = $this->payload($request, $announcement);
        $data['slug'] = unique_slug(Announcement::class, $data['title'], $announcement->id);

        if ($request->boolean('remove_image') && $announcement->featured_image) {
            SecureUpload::delete($announcement->featured_image);
            $data['featured_image'] = null;
        }

        if ($request->file('featured_image')) {
            SecureUpload::delete($announcement->featured_image);
            $data['featured_image'] = SecureUpload::image($request->file('featured_image'), 'announcements');
        }

        $announcement->update($data);
        $this->afterSave($announcement, $previous);
        ActivityLogger::log('update', 'announcements', $announcement->id, 'Updated announcement');
        SettingsStore::bust();

        return redirect()->route('admin.announcements.edit', $announcement)->with('status', 'Announcement saved.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        ActivityLogger::log('delete', 'announcements', $announcement->id, 'Deleted announcement');
        SettingsStore::bust();

        return redirect()->route('admin.announcements.index')->with('status', 'Announcement removed.');
    }

    private function payload(AnnouncementRequest $request, ?Announcement $existing = null): array
    {
        $data = $request->safe()->only(['title', 'content', 'category', 'status']);

        if ($data['status'] === 'published') {
            $data['published_at'] = ($existing && $existing->status === 'published' && $existing->published_at)
                ? $existing->published_at
                : now();
        } else {
            $data['published_at'] = null;
        }

        return $data;
    }

    private function afterSave(Announcement $announcement, ?string $previous): void
    {
        if ($previous !== 'published' && $announcement->status === 'published') {
            if (! $announcement->published_at) {
                $announcement->published_at = now();
                $announcement->save();
            }
            ActivityLogger::log('publish', 'announcements', $announcement->id, 'Published announcement');
            BranchNotifier::toActiveUsers(
                $announcement->title,
                'A new '.$announcement->category.' was published.',
                route('announcements.show', $announcement)
            );
        }

        if ($previous === 'published' && $announcement->status !== 'published') {
            ActivityLogger::log('unpublish', 'announcements', $announcement->id, 'Unpublished announcement');
        }
    }
}
