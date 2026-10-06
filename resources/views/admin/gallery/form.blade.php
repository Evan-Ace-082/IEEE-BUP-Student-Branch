@extends('layouts.dashboard')
@section('title', 'Album')
@section('heading', $album->exists ? 'Edit album' : 'New album')
@section('content')
<form class="form-card" method="POST" action="{{ $album->exists ? route('admin.gallery.update', $album) : route('admin.gallery.store') }}" enctype="multipart/form-data">
    @csrf @if($album->exists) @method('PUT') @endif
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label" for="title">Title</label><input class="form-control" id="title" name="title" value="{{ old('title', $album->title) }}" required></div>
        <div class="col-md-3"><label class="form-label" for="gallery_category_id">Category</label><select class="form-select" id="gallery_category_id" name="gallery_category_id"><option value="">None</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected((string) old('gallery_category_id', $album->gallery_category_id) === (string) $category->id)>{{ $category->name }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label" for="new_category">Or new category</label><input class="form-control" id="new_category" name="new_category"></div>
        <div class="col-md-6"><label class="form-label" for="event_id">Related event</label><select class="form-select" id="event_id" name="event_id"><option value="">None</option>@foreach ($events as $event)<option value="{{ $event->id }}" @selected((string) old('event_id', $album->event_id) === (string) $event->id)>{{ $event->title }}</option>@endforeach</select></div>
        <div class="col-md-6"><label class="form-label" for="images">Photos</label><input class="form-control" id="images" type="file" name="images[]" accept="image/png,image/jpeg,image/webp,image/gif" multiple></div>
        <div class="col-12"><label class="form-label" for="description">Description</label><textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $album->description) }}</textarea></div>
    </div>
    <button class="btn btn-ieee mt-3">Save album</button>
</form>
@if ($album->exists)
    <div class="row g-3 mt-2">
        @foreach ($album->items as $item)
            <div class="col-md-4">
                <div class="form-card">
                    <img src="{{ public_file_url($item->image) }}" alt="{{ $item->caption }}" class="rounded mb-2" style="height:140px;width:100%;object-fit:cover">
                    <form method="POST" action="{{ route('admin.gallery.caption', $item) }}">
                        @csrf @method('PUT')
                        <input class="form-control mb-2" name="caption" value="{{ $item->caption }}" aria-label="Caption">
                        <button class="btn btn-sm btn-outline-primary">Save caption</button>
                    </form>
                    <form method="POST" action="{{ route('admin.gallery.items.destroy', $item) }}" data-confirm="Delete this photo?">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger mt-2">Delete photo</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
