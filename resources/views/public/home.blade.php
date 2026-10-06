@extends('layouts.public')
@section('title', 'Home')
@section('content')
<section class="hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <p class="eyebrow mb-2">Bangladesh University of Professionals</p>
                <p class="tagline mb-1">{{ setting('tagline') }}</p>
                <h1>{{ setting('branch_name') }}</h1>
                <p class="lead">{{ page_content('home.hero_intro') }}</p>
                <div class="d-flex flex-wrap gap-2 mt-4">
                    @if (safe_url(setting('ieee_join_url')))
                        <a class="btn btn-gold btn-lg" href="{{ safe_url(setting('ieee_join_url')) }}" target="_blank" rel="noopener noreferrer">Join IEEE</a>
                    @endif
                    <a class="btn btn-outline-light btn-lg" href="{{ route('events.index') }}">Explore Events</a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="hero-panel">
                    <p class="eyebrow">Branch snapshot</p>
                    @foreach ([['Members', $stats['members']], ['Events', $stats['events']], ['Workshops', $stats['workshops']], ['Achievements', $stats['achievements']]] as [$label, $value])
                        <div class="hero-stat"><span>{{ $label }}</span><strong>{{ $value }}</strong></div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-7">
                <p class="eyebrow text-secondary">About the branch</p>
                <h2>A student community for technical work and leadership</h2>
                <p class="muted mt-3">{{ page_content('home.about_preview') }}</p>
                <a class="btn btn-ieee mt-2" href="{{ route('about') }}">Read More</a>
            </div>
            <div class="col-lg-5">
                <div class="row g-3">
                    @foreach ([['Total Members', $stats['members']], ['Total Events', $stats['events']], ['Total Workshops', $stats['workshops']], ['Total Achievements', $stats['achievements']], ['Active Committee Members', $stats['committee']]] as [$label, $value])
                        <div class="col-6"><div class="stat-card"><span>{{ $label }}</span><strong>{{ $value }}</strong></div></div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section surface">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <h2 class="mb-0">Upcoming events</h2>
            <a href="{{ route('events.index', ['filter' => 'upcoming']) }}">View all</a>
        </div>
        <div class="row g-4">
            @forelse ($upcoming as $event)
                <div class="col-md-4">
                    <article class="event-card">
                        @if ($event['banner'])
                            <img src="{{ public_file_url($event['banner']) }}" alt="">
                        @else
                            <div class="avatar-fallback" style="height:180px;font-size:1rem">{{ \App\Models\Event::categories()[$event['category']] ?? 'Event' }}</div>
                        @endif
                        <div class="body">
                            <span class="badge status-{{ $event['temporal'] }}">{{ ucfirst($event['temporal']) }}</span>
                            <h3 class="h5 mt-2">{{ $event['title'] }}</h3>
                            <p class="mb-1"><strong>{{ \Carbon\Carbon::parse($event['starts_at'])->format('d M Y') }}</strong> · {{ \Carbon\Carbon::parse($event['starts_at'])->format('h:i A') }}</p>
                            @if ($event['venue'])<p class="mb-1 muted">{{ $event['venue'] }}</p>@endif
                            <p class="muted">{{ \Illuminate\Support\Str::limit(strip_tags($event['description']), 120) }}</p>
                            <a class="btn btn-outline-primary btn-sm" href="{{ route('events.show', $event['slug']) }}">View Details</a>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12"><div class="empty-state card-soft">No upcoming events are published yet.</div></div>
            @endforelse
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-7">
                <h2>Latest announcements</h2>
                @forelse ($announcements as $announcement)
                    <article class="info-card mb-3">
                        <div class="body">
                            <span class="badge badge-soft">{{ \App\Models\Announcement::categories()[$announcement['category']] ?? $announcement['category'] }}</span>
                            <h3 class="h5 mt-2"><a href="{{ route('announcements.show', $announcement['slug']) }}">{{ $announcement['title'] }}</a></h3>
                            <p class="muted mb-0">{{ $announcement['published_at'] ? \Carbon\Carbon::parse($announcement['published_at'])->format('d M Y') : '' }}</p>
                        </div>
                    </article>
                @empty
                    <div class="empty-state card-soft">No announcements are published yet.</div>
                @endforelse
            </div>
            <div class="col-lg-5">
                <h2>Featured achievement</h2>
                @if ($featured)
                    <article class="info-card">
                        @if ($featured['image'])
                            <img class="achieve-img" src="{{ public_file_url($featured['image']) }}" alt="">
                        @endif
                        <div class="body">
                            <span class="badge badge-gold">{{ \App\Models\Achievement::categories()[$featured['category']] ?? $featured['category'] }}</span>
                            <h3 class="h4 mt-2">{{ $featured['title'] }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit($featured['description'], 180) }}</p>
                            <a class="btn btn-outline-primary btn-sm" href="{{ route('achievements.show', $featured['id']) }}">View achievement</a>
                        </div>
                    </article>
                @else
                    <div class="empty-state card-soft">Achievements will appear here after they are published.</div>
                @endif
            </div>
        </div>
    </div>
</section>

<section class="section surface">
    <div class="container">
        <h2>Why join IEEE?</h2>
        <p class="muted col-lg-8">{{ page_content('home.why_join') }}</p>
        <div class="row g-3 mt-1">
            @foreach (benefit_cards(page_content('home.benefits')) as $card)
                <div class="col-md-6 col-xl-3">
                    <article class="info-card"><div class="body"><h3 class="h5">{{ $card['title'] }}</h3><p class="mb-0 muted">{{ $card['text'] }}</p></div></article>
                </div>
            @endforeach
        </div>
        <a class="btn btn-navy mt-4" href="{{ route('membership') }}">Become a Member</a>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2>Recent activities</h2>
        <div class="row g-3 mt-1">
            @forelse ($activities as $event)
                <div class="col-md-4">
                    <article class="info-card"><div class="body">
                        <span class="badge status-past">Past</span>
                        <h3 class="h5 mt-2"><a href="{{ route('events.show', $event['slug']) }}">{{ $event['title'] }}</a></h3>
                        <p class="mb-0 muted">{{ \Carbon\Carbon::parse($event['starts_at'])->format('d M Y') }} · {{ $event['venue'] }}</p>
                    </div></article>
                </div>
            @empty
                <div class="col-12"><div class="empty-state card-soft">Recent activities will show here after past events are published.</div></div>
            @endforelse
        </div>
    </div>
</section>

<section class="section surface">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <h2 class="mb-0">Executive committee</h2>
            <a href="{{ route('committee.index') }}">Full committee</a>
        </div>
        <div class="row g-4">
            @forelse ($committee as $person)
                <div class="col-6 col-lg-3">
                    <article class="person-card">
                        @if ($person['photo'])
                            <img src="{{ public_file_url($person['photo']) }}" alt="Photo of {{ $person['name'] }}">
                        @else
                            <div class="avatar-fallback initials" style="height:240px">{{ strtoupper(substr($person['name'], 0, 1)) }}</div>
                        @endif
                        <div class="body">
                            <h3 class="h6 mb-1">{{ $person['name'] }}</h3>
                            <p class="mb-0 muted">{{ $person['position'] }}</p>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12"><div class="empty-state card-soft">The current committee has not been published yet.</div></div>
            @endforelse
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <h2 class="mb-0">Gallery</h2>
            <a href="{{ route('gallery.index') }}">Open gallery</a>
        </div>
        <div class="gallery-grid">
            @forelse ($gallery as $item)
                <a href="{{ route('gallery.show', $item['album_slug']) }}">
                    <img src="{{ public_file_url($item['image']) }}" alt="{{ $item['caption'] ?: $item['album_title'] }}" loading="lazy">
                </a>
            @empty
                <div class="empty-state card-soft">Gallery photos will appear after albums are uploaded.</div>
            @endforelse
        </div>
    </div>
</section>

<section class="section pt-0">
    <div class="container">
        <div class="hero-panel" style="background: var(--navy); border-radius: 24px; padding: 2rem;">
            <div class="row align-items-center g-3">
                <div class="col-lg-8">
                    <h2 class="text-white">Take the next step with the branch</h2>
                    <p class="mb-0" style="color: rgba(255,255,255,.8)">Join IEEE through the official membership page, explore events, or create a campus account for the member dashboard.</p>
                </div>
                <div class="col-lg-4 d-flex flex-wrap gap-2">
                    @if (safe_url(setting('ieee_join_url')))
                        <a class="btn btn-gold" href="{{ safe_url(setting('ieee_join_url')) }}" target="_blank" rel="noopener noreferrer">Join IEEE</a>
                    @endif
                    <a class="btn btn-outline-light" href="{{ route('events.index') }}">Explore Events</a>
                    <a class="btn btn-outline-light" href="{{ route('membership') }}">Become a Member</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
