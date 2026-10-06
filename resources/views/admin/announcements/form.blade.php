@extends('layouts.dashboard')
@section('title', $announcement->exists ? 'Edit announcement' : 'New announcement')
@section('heading', $announcement->exists ? 'Edit announcement' : 'New announcement')
@section('content')
<form class="form-card" method="POST" action="{{ $announcement->exists ? route('admin.announcements.update', $announcement) : route('admin.announcements.store') }}" enctype="multipart/form-data">
    @csrf @if($announcement->exists) @method('PUT') @endif
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label" for="title">Title</label><input class="form-control" id="title" name="title" value="{{ old('title', $announcement->title) }}" required></div>
        <div class="col-md-3"><label class="form-label" for="category">Category</label><select class="form-select" id="category" name="category">@foreach ($categories as $value => $label)<option value="{{ $value }}" @selected(old('category', $announcement->category) === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label" for="status">Status</label><select class="form-select" id="status" name="status">@foreach ($statuses as $value => $label)<option value="{{ $value }}" @selected(old('status', $announcement->status) === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-12"><label class="form-label" for="content">Content</label><textarea class="form-control" id="content" name="content" rows="8" required>{{ old('content', $announcement->content) }}</textarea></div>
        <div class="col-md-6"><label class="form-label" for="featured_image">Featured image</label><input class="form-control" id="featured_image" type="file" name="featured_image" accept="image/png,image/jpeg,image/webp,image/gif"></div>
    </div>
    <button class="btn btn-ieee mt-3">Save</button>
</form>
@endsection
