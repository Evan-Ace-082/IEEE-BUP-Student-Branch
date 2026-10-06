@extends('layouts.public')
@section('title', 'Executive Committee')
@section('content')
<header class="page-hero"><div class="container"><p class="eyebrow">People</p><h1>Executive Committee</h1><p>Current officers and previous committees stay on record when a new term begins.</p></div></header>
<section class="section">
    <div class="container">
        @forelse ($committees as $committee)
            <div class="d-flex flex-wrap justify-content-between align-items-end mb-3 mt-4">
                <h2 class="mb-0">{{ $committee->name }}</h2>
                @if ($committee->is_current)<span class="badge status-published">Current</span>@else<span class="badge status-past">{{ $committee->year }}</span>@endif
            </div>
            @if ($committee->description)<p class="muted">{{ $committee->description }}</p>@endif
            <div class="row g-4">
                @foreach ($committee->members as $person)
                    <div class="col-md-6 col-lg-4">
                        <article class="person-card">
                            <div class="row g-0">
                                <div class="col-4">
                                    @if ($person->photo)
                                        <img src="{{ public_file_url($person->photo) }}" alt="Photo of {{ $person->name }}" style="height:100%;min-height:140px">
                                    @else
                                        <div class="avatar-fallback initials" style="min-height:140px;height:100%">{{ strtoupper(substr($person->name, 0, 1)) }}</div>
                                    @endif
                                </div>
                                <div class="col-8"><div class="body">
                                    <h3 class="h5 mb-1">{{ $person->name }}</h3>
                                    <p class="mb-1"><strong>{{ $person->position?->name }}</strong></p>
                                    <p class="muted small mb-2">{{ collect([$person->department, $person->batch])->filter()->implode(' · ') }}</p>
                                    @if ($person->bio)<p class="small">{{ $person->bio }}</p>@endif
                                    <div class="d-flex gap-2">
                                        @if (safe_url($person->linkedin))<a href="{{ safe_url($person->linkedin) }}" target="_blank" rel="noopener noreferrer">LinkedIn</a>@endif
                                        @if (safe_url($person->facebook))<a href="{{ safe_url($person->facebook) }}" target="_blank" rel="noopener noreferrer">Facebook</a>@endif
                                        @if (safe_url($person->other_link))<a href="{{ safe_url($person->other_link) }}" target="_blank" rel="noopener noreferrer">Link</a>@endif
                                    </div>
                                </div></div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        @empty
            <div class="empty-state card-soft">No committee has been published yet.</div>
        @endforelse
    </div>
</section>
@endsection
