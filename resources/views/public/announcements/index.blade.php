@extends('layouts.public')
@section('title', 'News & Announcements')
@section('content')
<header class="page-hero"><div class="container"><p class="eyebrow">Updates</p><h1>News & Announcements</h1></div></header>
<section class="section"><div class="container">
    <form class="filter-bar row g-2 mb-4" method="GET">
        <div class="col-md-5"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Search" aria-label="Search announcements"></div>
        <div class="col-md-4">
            <select class="form-select" name="category" aria-label="Category">
                <option value="">All categories</option>
                @foreach ($categories as $value => $label)
                    <option value="{{ $value }}" @selected(request('category') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3"><button class="btn btn-ieee w-100">Filter</button></div>
    </form>
    <div class="row g-4">
        @forelse ($announcements as $item)
            <div class="col-md-6 col-lg-4">
                <article class="info-card h-100">
                    @if ($item->featured_image)<img class="achieve-img" src="{{ public_file_url($item->featured_image) }}" alt="">@endif
                    <div class="body">
                        <span class="badge badge-soft">{{ $categories[$item->category] ?? $item->category }}</span>
                        <h2 class="h5 mt-2"><a href="{{ route('announcements.show', $item) }}">{{ $item->title }}</a></h2>
                        <p class="muted mb-0">{{ $item->published_at?->format('d M Y') }} · {{ $item->author?->name ?? 'Branch team' }}</p>
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12"><div class="empty-state card-soft">Nothing is published in this category.</div></div>
        @endforelse
    </div>
    <div class="mt-4">{{ $announcements->links() }}</div>
</div></section>
@endsection
