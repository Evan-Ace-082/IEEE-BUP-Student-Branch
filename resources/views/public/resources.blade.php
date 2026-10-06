@extends('layouts.public')
@section('title', 'Resources')
@section('content')
<header class="page-hero"><div class="container"><p class="eyebrow">Library</p><h1>Resources</h1><p>Files and links shared by the branch. Member-only items require an active account.</p></div></header>
<section class="section"><div class="container">
    <form class="filter-bar row g-2 mb-4" method="GET">
        <div class="col-md-5"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Search resources" aria-label="Search resources"></div>
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
    <div class="row g-3">
        @forelse ($resources as $resource)
            <div class="col-md-6">
                <article class="info-card"><div class="body">
                    <span class="badge badge-soft">{{ $categories[$resource->category] ?? $resource->category }}</span>
                    @if ($resource->visibility === 'members')<span class="badge status-pending">Members</span>@endif
                    <h2 class="h5 mt-2">{{ $resource->title }}</h2>
                    @if ($resource->description)<p class="muted">{{ $resource->description }}</p>@endif
                    <p class="small muted">{{ $resource->author }} @if($resource->published_on) · {{ $resource->published_on->format('d M Y') }} @endif</p>
                    @if ($resource->type === 'file')
                        <a class="btn btn-outline-primary btn-sm" href="{{ route('resources.download', $resource) }}">Download</a>
                    @elseif (safe_url($resource->external_url))
                        <a class="btn btn-outline-primary btn-sm" href="{{ safe_url($resource->external_url) }}" target="_blank" rel="noopener noreferrer">Open {{ $resource->type === 'video' ? 'video' : 'link' }}</a>
                    @endif
                </div></article>
            </div>
        @empty
            <div class="col-12"><div class="empty-state card-soft">No resources are available for this view.</div></div>
        @endforelse
    </div>
    <div class="mt-4">{{ $resources->links() }}</div>
</div></section>
@endsection
