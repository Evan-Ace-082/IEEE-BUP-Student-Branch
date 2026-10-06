@extends('layouts.public')
@section('title', 'Gallery')
@section('content')
<header class="page-hero"><div class="container"><p class="eyebrow">Photos</p><h1>Gallery</h1></div></header>
<section class="section"><div class="container">
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a class="btn btn-sm {{ request('category') ? 'btn-outline-secondary' : 'btn-ieee' }}" href="{{ route('gallery.index') }}">All</a>
        @foreach ($categories as $category)
            <a class="btn btn-sm {{ (string) request('category') === (string) $category->id ? 'btn-ieee' : 'btn-outline-secondary' }}" href="{{ route('gallery.index', ['category' => $category->id]) }}">{{ $category->name }}</a>
        @endforeach
    </div>
    <div class="row g-4">
        @forelse ($albums as $album)
            <div class="col-md-6 col-lg-4">
                <a class="info-card d-block text-reset" href="{{ route('gallery.show', $album) }}">
                    @if ($album->cover)<img class="album-cover" src="{{ public_file_url($album->cover) }}" alt="">@else<div class="avatar-fallback" style="height:180px">Album</div>@endif
                    <div class="body">
                        <h2 class="h5">{{ $album->title }}</h2>
                        <p class="muted mb-0">{{ $album->category?->name }} · {{ $album->items_count }} photos</p>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12"><div class="empty-state card-soft">No albums in this category yet.</div></div>
        @endforelse
    </div>
    <div class="mt-4">{{ $albums->links() }}</div>
</div></section>
@endsection
