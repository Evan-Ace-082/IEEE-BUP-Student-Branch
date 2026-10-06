@extends('layouts.public')
@section('title', 'Events')
@section('content')
<header class="page-hero"><div class="container"><p class="eyebrow">Programs</p><h1>Events</h1><p>Workshops, seminars, competitions, and other branch activities.</p></div></header>
<section class="section">
    <div class="container">
        <form class="filter-bar row g-2 mb-4" method="GET">
            <div class="col-md-4"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Search events" aria-label="Search events"></div>
            <div class="col-md-3">
                <select class="form-select" name="filter" aria-label="Time">
                    @foreach (['all' => 'All published', 'upcoming' => 'Upcoming', 'ongoing' => 'Ongoing', 'past' => 'Past'] as $value => $label)
                        <option value="{{ $value }}" @selected($filter === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="category" aria-label="Category">
                    <option value="">All categories</option>
                    @foreach ($categories as $value => $label)
                        <option value="{{ $value }}" @selected(request('category') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-ieee w-100" type="submit">Filter</button></div>
        </form>
        <div class="row g-4">
            @forelse ($events as $event)
                <div class="col-md-6 col-lg-4">@include('partials.event-card', ['event' => $event])</div>
            @empty
                <div class="col-12"><div class="empty-state card-soft">No events match this filter.</div></div>
            @endforelse
        </div>
        <div class="mt-4">{{ $events->links() }}</div>
    </div>
</section>
@endsection
