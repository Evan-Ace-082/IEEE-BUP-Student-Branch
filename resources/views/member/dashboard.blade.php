@extends('layouts.dashboard')
@section('title', 'Member dashboard')
@section('heading', 'Member dashboard')
@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="metric"><span>Registrations</span><strong>{{ $user->registrations->count() }}</strong></div></div>
    <div class="col-md-4"><div class="metric"><span>Achievements submitted</span><strong>{{ $user->submittedAchievements->count() }}</strong></div></div>
    <div class="col-md-4"><div class="metric"><span>Profile</span><strong>{{ $user->profile?->directory_visible ? 'Visible' : 'Hidden' }}</strong></div></div>
</div>
<div class="row g-4">
    <div class="col-lg-6">
        <h2 class="h5">Upcoming events</h2>
        @forelse ($upcoming as $event)
            <p><a href="{{ route('events.show', $event) }}">{{ $event->title }}</a><br><span class="muted">{{ $event->starts_at?->format('d M Y, h:i A') }}</span></p>
        @empty
            <p class="muted">No upcoming events.</p>
        @endforelse
    </div>
    <div class="col-lg-6">
        <h2 class="h5">Your recent registrations</h2>
        @forelse ($registrations as $registration)
            <p>{{ $registration->event?->title }} · <span class="badge status-{{ $registration->status }}">{{ $registration->status }}</span></p>
        @empty
            <p class="muted">You have not registered for an event yet.</p>
        @endforelse
    </div>
</div>
@endsection
