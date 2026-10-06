@extends('layouts.public')
@section('title', $album->title)
@section('content')
<header class="page-hero"><div class="container">
    <p class="eyebrow">{{ $album->category?->name ?: 'Gallery' }}</p>
    <h1>{{ $album->title }}</h1>
    @if ($album->description)<p>{{ $album->description }}</p>@endif
    @if ($album->event)<p>Linked event: <a class="text-white" href="{{ route('events.show', $album->event) }}">{{ $album->event->title }}</a></p>@endif
</div></header>
<section class="section"><div class="container">
    <div class="gallery-grid">
        @forelse ($album->items as $item)
            <a href="{{ public_file_url($item->image) }}" data-full="{{ public_file_url($item->image) }}" data-alt="{{ $item->caption ?: $album->title }}" data-caption="{{ $item->caption }}">
                <img src="{{ public_file_url($item->image) }}" alt="{{ $item->caption ?: $album->title }}" loading="lazy">
            </a>
        @empty
            <div class="empty-state card-soft">This album has no photos yet.</div>
        @endforelse
    </div>
</div></section>
<div class="lightbox" data-lightbox>
    <button class="btn btn-light position-absolute top-0 end-0 m-3" type="button" data-close>Close</button>
    <div><img alt=""><p></p></div>
</div>
@endsection
