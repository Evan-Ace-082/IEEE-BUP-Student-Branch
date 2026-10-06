@extends('layouts.dashboard')
@section('title', $event->exists ? 'Edit event' : 'New event')
@section('heading', $event->exists ? 'Edit event' : 'New event')
@section('content')
<form class="form-card" method="POST" action="{{ $event->exists ? route('admin.events.update', $event) : route('admin.events.store') }}" enctype="multipart/form-data">
    @csrf
    @if ($event->exists) @method('PUT') @endif
    <div class="row g-3">
        <div class="col-md-8"><label class="form-label" for="title">Title</label><input class="form-control" id="title" name="title" value="{{ old('title', $event->title) }}" required></div>
        <div class="col-md-4"><label class="form-label" for="category">Category</label><select class="form-select" id="category" name="category">@foreach ($categories as $value => $label)<option value="{{ $value }}" @selected(old('category', $event->category) === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label" for="start_date">Date</label><input class="form-control" id="start_date" type="date" name="start_date" value="{{ old('start_date', $event->starts_at?->format('Y-m-d')) }}" required></div>
        <div class="col-md-3"><label class="form-label" for="start_time">Start time</label><input class="form-control" id="start_time" type="time" name="start_time" value="{{ old('start_time', $event->starts_at?->format('H:i')) }}"></div>
        <div class="col-md-3"><label class="form-label" for="end_date">End date</label><input class="form-control" id="end_date" type="date" name="end_date" value="{{ old('end_date', $event->ends_at?->format('Y-m-d')) }}"></div>
        <div class="col-md-3"><label class="form-label" for="end_time">End time</label><input class="form-control" id="end_time" type="time" name="end_time" value="{{ old('end_time', $event->ends_at?->format('H:i')) }}"></div>
        <div class="col-md-4"><label class="form-label" for="venue">Venue</label><input class="form-control" id="venue" name="venue" value="{{ old('venue', $event->venue) }}"></div>
        <div class="col-md-4"><label class="form-label" for="speaker">Speaker</label><input class="form-control" id="speaker" name="speaker" value="{{ old('speaker', $event->speaker) }}"></div>
        <div class="col-md-4"><label class="form-label" for="organizer">Organizer</label><input class="form-control" id="organizer" name="organizer" value="{{ old('organizer', $event->organizer) }}"></div>
        <div class="col-md-4"><label class="form-label" for="status">Status</label><select class="form-select" id="status" name="status">@foreach (['draft' => 'Draft', 'published' => 'Published', 'cancelled' => 'Cancelled'] as $value => $label)<option value="{{ $value }}" @selected(old('status', $event->status) === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-4"><label class="form-label" for="registration_deadline">Registration deadline</label><input class="form-control" id="registration_deadline" type="datetime-local" name="registration_deadline" value="{{ old('registration_deadline', $event->registration_deadline?->format('Y-m-d\TH:i')) }}"></div>
        <div class="col-md-4 d-flex align-items-end gap-3">
            <div class="form-check"><input class="form-check-input" type="checkbox" name="registration_enabled" value="1" id="registration_enabled" @checked(old('registration_enabled', $event->registration_enabled))><label class="form-check-label" for="registration_enabled">Registration open</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" @checked(old('is_featured', $event->is_featured))><label class="form-check-label" for="is_featured">Featured</label></div>
        </div>
        <div class="col-12"><label class="form-label" for="description">Description</label><textarea class="form-control" id="description" name="description" rows="8" required>{{ old('description', $event->description) }}</textarea></div>
        <div class="col-md-6"><label class="form-label" for="banner">Banner</label><input class="form-control" id="banner" type="file" name="banner" accept="image/png,image/jpeg,image/webp,image/gif">@if($event->banner)<div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="remove_banner" value="1" id="remove_banner"><label class="form-check-label" for="remove_banner">Remove banner</label></div>@endif</div>
    </div>
    <button class="btn btn-ieee mt-3" type="submit">Save event</button>
</form>
@endsection
