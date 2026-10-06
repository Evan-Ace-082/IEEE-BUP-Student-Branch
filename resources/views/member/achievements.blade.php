@extends('layouts.dashboard')
@section('title', 'My achievements')
@section('heading', 'Submit an achievement')
@section('content')
<form class="form-card mb-4" method="POST" action="{{ route('member.achievements.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
        <div class="col-md-8"><label class="form-label" for="title">Title</label><input class="form-control" id="title" name="title" value="{{ old('title') }}" required></div>
        <div class="col-md-4">
            <label class="form-label" for="category">Category</label>
            <select class="form-select" id="category" name="category">@foreach ($categories as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select>
        </div>
        <div class="col-md-6"><label class="form-label" for="person_or_team">Person or team</label><input class="form-control" id="person_or_team" name="person_or_team" value="{{ old('person_or_team') }}"></div>
        <div class="col-md-3"><label class="form-label" for="achieved_on">Date</label><input class="form-control" id="achieved_on" type="date" name="achieved_on" value="{{ old('achieved_on') }}"></div>
        <div class="col-md-3"><label class="form-label" for="link">Supporting link</label><input class="form-control" id="link" name="link" value="{{ old('link') }}"></div>
        <div class="col-12"><label class="form-label" for="description">Description</label><textarea class="form-control" id="description" name="description" rows="4" required>{{ old('description') }}</textarea></div>
        <div class="col-md-6"><label class="form-label" for="image">Image</label><input class="form-control" id="image" type="file" name="image" accept="image/png,image/jpeg,image/webp,image/gif"></div>
    </div>
    <button class="btn btn-ieee mt-3" type="submit">Submit for review</button>
</form>
<div class="table-card"><table class="table">
    <thead><tr><th>Title</th><th>Status</th><th>Note</th></tr></thead>
    <tbody>
    @forelse ($achievements as $achievement)
        <tr>
            <td>{{ $achievement->title }}</td>
            <td><span class="badge status-{{ $achievement->status }}">{{ $achievement->status }}</span></td>
            <td>{{ $achievement->review_note }}</td>
        </tr>
    @empty
        <tr><td colspan="3" class="empty-state">You have not submitted an achievement yet.</td></tr>
    @endforelse
    </tbody>
</table></div>
<div class="mt-3">{{ $achievements->links() }}</div>
@endsection
