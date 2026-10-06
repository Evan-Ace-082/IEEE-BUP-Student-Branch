@extends('layouts.dashboard')
@section('title', 'Achievement')
@section('heading', $achievement->exists ? 'Review achievement' : 'New achievement')
@section('content')
<form class="form-card" method="POST" action="{{ $achievement->exists ? route('admin.achievements.update', $achievement) : route('admin.achievements.store') }}" enctype="multipart/form-data">
    @csrf @if($achievement->exists) @method('PUT') @endif
    <div class="row g-3">
        <div class="col-md-8"><label class="form-label" for="title">Title</label><input class="form-control" id="title" name="title" value="{{ old('title', $achievement->title) }}" required></div>
        <div class="col-md-4"><label class="form-label" for="category">Category</label><select class="form-select" id="category" name="category">@foreach ($categories as $value => $label)<option value="{{ $value }}" @selected(old('category', $achievement->category) === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-4"><label class="form-label" for="person_or_team">Person or team</label><input class="form-control" id="person_or_team" name="person_or_team" value="{{ old('person_or_team', $achievement->person_or_team) }}"></div>
        <div class="col-md-4"><label class="form-label" for="achieved_on">Date</label><input class="form-control" type="date" id="achieved_on" name="achieved_on" value="{{ old('achieved_on', $achievement->achieved_on?->format('Y-m-d')) }}"></div>
        <div class="col-md-4"><label class="form-label" for="status">Status</label><select class="form-select" id="status" name="status">@foreach ($statuses as $value => $label)<option value="{{ $value }}" @selected(old('status', $achievement->status ?: 'pending') === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-8"><label class="form-label" for="link">Supporting link</label><input class="form-control" id="link" name="link" value="{{ old('link', $achievement->link) }}"></div>
        <div class="col-md-4 d-flex align-items-end"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" @checked(old('is_featured', $achievement->is_featured))><label class="form-check-label" for="is_featured">Feature on homepage</label></div></div>
        <div class="col-12"><label class="form-label" for="description">Description</label><textarea class="form-control" id="description" name="description" rows="5" required>{{ old('description', $achievement->description) }}</textarea></div>
        <div class="col-12"><label class="form-label" for="review_note">Review note</label><textarea class="form-control" id="review_note" name="review_note" rows="2">{{ old('review_note', $achievement->review_note) }}</textarea></div>
        <div class="col-md-6"><label class="form-label" for="image">Image</label><input class="form-control" id="image" type="file" name="image" accept="image/png,image/jpeg,image/webp,image/gif"></div>
    </div>
    <button class="btn btn-ieee mt-3">Save</button>
</form>
@endsection
