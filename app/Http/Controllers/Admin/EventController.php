<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EventRequest;
use App\Models\Event;
use App\Services\ActivityLogger;
use App\Services\BranchNotifier;
use App\Services\SecureUpload;
use App\Support\SettingsStore;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::query()->withCount('registrations')->latest('starts_at');

        if ($search = trim($request->string('q')->toString())) {
            $query->where('title', 'like', like_term($search));
        }

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        return view('admin.events.index', [
            'events' => $query->paginate(12)->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('admin.events.form', [
            'event' => new Event(['status' => 'draft', 'category' => 'workshop']),
            'categories' => Event::categories(),
        ]);
    }

    public function store(EventRequest $request)
    {
        [$start, $end] = $this->timing($request->validated());
        $data = $this->payload($request, $start, $end);
        $data['slug'] = unique_slug(Event::class, $data['title']);
        $data['created_by'] = $request->user()->id;

        if ($request->file('banner')) {
            $data['banner'] = SecureUpload::image($request->file('banner'), 'events');
        }

        $event = Event::query()->create($data);
        $this->afterSave($event, null);
        ActivityLogger::log('create', 'events', $event->id, 'Created event '.$event->title);
        SettingsStore::bust();

        return redirect()->route('admin.events.edit', $event)->with('status', 'Event created.');
    }

    public function edit(Event $event)
    {
        return view('admin.events.form', [
            'event' => $event,
            'categories' => Event::categories(),
        ]);
    }

    public function update(EventRequest $request, Event $event)
    {
        $previous = $event->status;
        [$start, $end] = $this->timing($request->validated());
        $data = $this->payload($request, $start, $end);
        $data['slug'] = unique_slug(Event::class, $data['title'], $event->id);

        if ($request->boolean('remove_banner') && $event->banner) {
            SecureUpload::delete($event->banner);
            $data['banner'] = null;
        }

        if ($request->file('banner')) {
            SecureUpload::delete($event->banner);
            $data['banner'] = SecureUpload::image($request->file('banner'), 'events');
        }

        $event->update($data);
        $this->afterSave($event, $previous);
        ActivityLogger::log('update', 'events', $event->id, 'Updated event '.$event->title);
        SettingsStore::bust();

        return redirect()->route('admin.events.edit', $event)->with('status', 'Event saved.');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        ActivityLogger::log('delete', 'events', $event->id, 'Deleted event '.$event->title);
        SettingsStore::bust();

        return redirect()->route('admin.events.index')->with('status', 'Event moved to trash.');
    }

    private function payload(EventRequest $request, Carbon $start, Carbon $end): array
    {
        $data = $request->safe()->only([
            'title', 'description', 'venue', 'speaker', 'organizer', 'category', 'status', 'registration_deadline',
        ]);
        $data['starts_at'] = $start;
        $data['ends_at'] = $end;
        $data['registration_enabled'] = $request->boolean('registration_enabled');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['registration_deadline'] = $data['registration_deadline'] ?: null;
        foreach (['venue', 'speaker', 'organizer'] as $field) {
            $data[$field] = $data[$field] ?: null;
        }

        return $data;
    }

    private function timing(array $data): array
    {
        $start = Carbon::parse($data['start_date'].' '.substr($data['start_time'] ?? '09:00', 0, 5));
        $end = Carbon::parse(($data['end_date'] ?: $data['start_date']).' '.substr($data['end_time'] ?? '17:00', 0, 5));

        if ($end->lessThanOrEqualTo($start)) {
            throw ValidationException::withMessages([
                'end_date' => 'The end date and time must be after the start.',
            ]);
        }

        return [$start, $end];
    }

    private function afterSave(Event $event, ?string $previous): void
    {
        if ($previous !== 'published' && $event->status === 'published') {
            ActivityLogger::log('publish', 'events', $event->id, 'Published event '.$event->title);
            BranchNotifier::toActiveUsers(
                'New event: '.$event->title,
                'A branch event was published.',
                route('events.show', $event)
            );
        }

        if ($previous === 'published' && $event->status !== 'published') {
            ActivityLogger::log('unpublish', 'events', $event->id, 'Unpublished event '.$event->title);
        }
    }
}
