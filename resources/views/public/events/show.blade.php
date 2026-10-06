@extends('layouts.public')
@section('title', $event->title)
@section('content')
<header class="page-hero">
    <div class="container">
        <p class="eyebrow">{{ \App\Models\Event::categories()[$event->category] ?? 'Event' }}</p>
        <h1>{{ $event->title }}</h1>
        <p>{{ $event->starts_at?->format('d M Y, h:i A') }} @if($event->ends_at) – {{ $event->ends_at->format('d M Y, h:i A') }} @endif</p>
    </div>
</header>
<section class="section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                @if ($event->banner)<img class="rounded mb-3 w-100" style="max-height:420px;object-fit:cover" src="{{ public_file_url($event->banner) }}" alt="">@endif
                @foreach (content_paragraphs($event->description) as $line)
                    <p>{{ $line }}</p>
                @endforeach
                @if ($event->albums->isNotEmpty())
                    <h2 class="h4 mt-4">Event gallery</h2>
                    <div class="gallery-grid">
                        @foreach ($event->albums as $album)
                            @foreach ($album->items as $item)
                                <a href="{{ public_file_url($item->image) }}" data-full="{{ public_file_url($item->image) }}" data-alt="{{ $item->caption ?: $event->title }}" data-caption="{{ $item->caption }}">
                                    <img src="{{ public_file_url($item->image) }}" alt="{{ $item->caption ?: $event->title }}" loading="lazy">
                                </a>
                            @endforeach
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="col-lg-4">
                <aside class="form-card">
                    <span class="badge status-{{ $event->temporalStatus() }}">{{ ucfirst($event->temporalStatus()) }}</span>
                    <dl class="mt-3 mb-0">
                        <dt>Venue</dt><dd>{{ $event->venue ?: 'To be announced' }}</dd>
                        <dt>Speaker</dt><dd>{{ $event->speaker ?: 'To be announced' }}</dd>
                        <dt>Organizer</dt><dd>{{ $event->organizer ?: setting('branch_name') }}</dd>
                        <dt>Registration</dt><dd>{{ $event->registrationOpen() ? 'Open' : 'Closed' }}</dd>
                        @if ($event->registration_deadline)<dt>Deadline</dt><dd>{{ $event->registration_deadline->format('d M Y, h:i A') }}</dd>@endif
                        <dt>Registrations</dt><dd>{{ $event->registrations_count }}</dd>
                    </dl>
                </aside>
                <div class="form-card mt-3">
                    <h2 class="h5">Register</h2>
                    @if (! $event->registrationOpen())
                        <p class="muted mb-0">Registration is not open for this event.</p>
                    @elseif ($mine && $mine->status !== 'cancelled')
                        <p class="mb-0">You are already registered. Status: <strong>{{ $mine->status }}</strong>.</p>
                    @else
                        <form method="POST" action="{{ route('events.register', $event) }}">
                            @csrf
                            <div class="mb-2"><label class="form-label" for="name">Name</label><input class="form-control" id="name" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required></div>
                            <div class="mb-2"><label class="form-label" for="student_id">Student ID</label><input class="form-control" id="student_id" name="student_id" value="{{ old('student_id') }}"></div>
                            <div class="mb-2"><label class="form-label" for="email">Email</label><input class="form-control" id="email" type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required @if(auth()->user()?->isActiveAccount()) readonly @endif></div>
                            <div class="mb-2"><label class="form-label" for="phone">Phone</label><input class="form-control" id="phone" name="phone" value="{{ old('phone') }}"></div>
                            <div class="mb-2"><label class="form-label" for="department">Department</label><input class="form-control" id="department" name="department" value="{{ old('department') }}"></div>
                            <div class="mb-2"><label class="form-label" for="batch">Batch</label><input class="form-control" id="batch" name="batch" value="{{ old('batch') }}"></div>
                            <div class="mb-2">
                                <label class="form-label" for="ieee_membership_status">IEEE membership status</label>
                                <select class="form-select" id="ieee_membership_status" name="ieee_membership_status" required>
                                    @foreach (['none' => 'Not a member', 'student' => 'Student member', 'graduate' => 'Graduate student member', 'professional' => 'Professional member'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('ieee_membership_status') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <x-human-check />
                            <button class="btn btn-ieee w-100" type="submit">Register</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
<div class="lightbox" data-lightbox><button class="btn btn-light position-absolute top-0 end-0 m-3" type="button" data-close>Close</button><div><img alt=""><p></p></div></div>
@endsection
