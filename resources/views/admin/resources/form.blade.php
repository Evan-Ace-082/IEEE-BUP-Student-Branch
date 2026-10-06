@extends('layouts.dashboard')
@section('title', 'Resource')
@section('heading', $resource->exists ? 'Edit resource' : 'New resource')
@section('content')
<form class="form-card" method="POST" action="{{ $resource->exists ? route('admin.resources.update', $resource) : route('admin.resources.store') }}" enctype="multipart/form-data">
    @csrf @if($resource->exists) @method('PUT') @endif
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label" for="title">Title</label><input class="form-control" id="title" name="title" value="{{ old('title', $resource->title) }}" required></div>
        <div class="col-md-3"><label class="form-label" for="category">Category</label><select class="form-select" id="category" name="category">@foreach ($categories as $value => $label)<option value="{{ $value }}" @selected(old('category', $resource->category) === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label" for="type">Type</label><select class="form-select" id="type" name="type">@foreach ($types as $value => $label)<option value="{{ $value }}" @selected(old('type', $resource->type) === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-6"><label class="form-label" for="external_url">Link or video URL</label><input class="form-control" id="external_url" name="external_url" value="{{ old('external_url', $resource->external_url) }}"></div>
        <div class="col-md-3"><label class="form-label" for="visibility">Visibility</label><select class="form-select" id="visibility" name="visibility"><option value="public" @selected(old('visibility', $resource->visibility) === 'public')>Public</option><option value="members" @selected(old('visibility', $resource->visibility) === 'members')>Members</option></select></div>
        <div class="col-md-3"><label class="form-label" for="published_on">Date</label><input class="form-control" type="date" id="published_on" name="published_on" value="{{ old('published_on', $resource->published_on?->format('Y-m-d')) }}"></div>
        <div class="col-md-6"><label class="form-label" for="author">Author</label><input class="form-control" id="author" name="author" value="{{ old('author', $resource->author) }}"></div>
        <div class="col-md-6"><label class="form-label" for="file">File (PDF, DOCX, PPTX)</label><input class="form-control" id="file" type="file" name="file"></div>
        <div class="col-12"><label class="form-label" for="description">Description</label><textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $resource->description) }}</textarea></div>
        <div class="col-md-6"><label class="form-label" for="thumbnail">Thumbnail</label><input class="form-control" id="thumbnail" type="file" name="thumbnail" accept="image/png,image/jpeg,image/webp,image/gif"></div>
    </div>
    <button class="btn btn-ieee mt-3">Save resource</button>
</form>
@endsection
