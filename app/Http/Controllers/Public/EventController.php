<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRegistrationRequest;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Notifications\EventRegistrationNotification;
use App\Notifications\GuestEventRegistrationNotification;
use App\Services\ActivityLogger;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->string('filter')->toString();
        $query = Event::query()->where('status', 'published');

        $query = match ($filter) {
            'upcoming' => Event::upcoming(),
            'ongoing' => Event::ongoing(),
            'past' => Event::past(),
            default => $query,
        };

        if ($search = trim($request->string('q')->toString())) {
            $query->where(function ($inner) use ($search) {
                $inner->where('title', 'like', like_term($search))
                    ->orWhere('venue', 'like', like_term($search));
            });
        }

        if ($category = $request->string('category')->toString()) {
            if (array_key_exists($category, Event::categories())) {
                $query->where('category', $category);
            }
        }

        $events = $query->orderByDesc('starts_at')->paginate(9)->withQueryString();

        return view('public.events.index', [
            'events' => $events,
            'categories' => Event::categories(),
            'filter' => $filter ?: 'all',
        ]);
    }

    public function show(Event $event)
    {
        if ($event->status !== 'published' && ! auth()->user()?->hasAdminAccess()) {
            abort(404);
        }

        $event->load(['albums.items', 'creator'])->loadCount('registrations');

        $mine = null;
        if (auth()->check()) {
            $mine = EventRegistration::query()
                ->where('event_id', $event->id)
                ->where('user_id', auth()->id())
                ->first();
        }

        return view('public.events.show', compact('event', 'mine'));
    }

    public function register(StoreEventRegistrationRequest $request, Event $event)
    {
        if ($event->status !== 'published' || ! $event->registrationOpen()) {
            throw ValidationException::withMessages([
                'email' => 'Registration is closed for this event.',
            ]);
        }

        $data = $request->safe()->only([
            'name', 'student_id', 'email', 'phone', 'department', 'batch', 'ieee_membership_status',
        ]);
        $data['email'] = mb_strtolower($data['email']);
        foreach (['student_id', 'phone', 'department', 'batch'] as $field) {
            $data[$field] = ($data[$field] ?? '') === '' ? null : $data[$field];
        }

        $user = $request->user();
        if ($user && $user->isActiveAccount()) {
            $data['email'] = mb_strtolower($user->email);
            $data['user_id'] = $user->id;
        }

        try {
            $registration = DB::transaction(function () use ($event, $data, $user) {
                $existing = EventRegistration::query()
                    ->where('event_id', $event->id)
                    ->where('email', $data['email'])
                    ->lockForUpdate()
                    ->first();

                if ($existing && $existing->status !== 'cancelled') {
                    throw ValidationException::withMessages([
                        'email' => 'This email is already registered for the event.',
                    ]);
                }

                if ($user && $user->isActiveAccount()) {
                    $byUser = EventRegistration::query()
                        ->where('event_id', $event->id)
                        ->where('user_id', $user->id)
                        ->lockForUpdate()
                        ->first();
                    if ($byUser && $byUser->status !== 'cancelled' && (! $existing || $byUser->id !== $existing->id)) {
                        throw ValidationException::withMessages([
                            'email' => 'You are already registered for this event.',
                        ]);
                    }
                    $existing = $byUser ?: $existing;
                }

                if ($existing) {
                    $existing->fill($data);
                    $existing->status = 'registered';
                    $existing->save();

                    return $existing;
                }

                return EventRegistration::query()->create([
                    ...$data,
                    'event_id' => $event->id,
                    'user_id' => $data['user_id'] ?? null,
                    'status' => 'registered',
                ]);
            });
        } catch (QueryException $exception) {
            throw ValidationException::withMessages([
                'email' => 'This email is already registered for the event.',
            ]);
        }

        if ($registration->user) {
            $registration->user->notify(new EventRegistrationNotification($event));
        } else {
            Notification::route('mail', $registration->email)
                ->notify(new GuestEventRegistrationNotification($event, $registration->name));
        }

        ActivityLogger::log('create', 'event_registrations', $registration->id, 'Event registration for '.$event->title);

        return redirect()
            ->route('events.show', $event)
            ->with('status', 'You are registered for this event. A confirmation will be sent to '.$registration->email.'.');
    }
}
