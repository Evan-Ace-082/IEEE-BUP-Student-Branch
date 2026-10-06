@extends('layouts.dashboard')
@section('title', $committee->name)
@section('heading', $committee->name)
@section('content')
<form class="form-card mb-4" method="POST" action="{{ route('admin.committee.update', $committee) }}">
    @csrf @method('PUT')
    <div class="row g-2">
        <div class="col-md-5"><input class="form-control" name="name" value="{{ $committee->name }}" aria-label="Name" required></div>
        <div class="col-md-2"><input class="form-control" name="year" value="{{ $committee->year }}" aria-label="Year" required></div>
        <div class="col-md-3 d-flex align-items-center"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_current" value="1" id="is_current" @checked($committee->is_current)><label class="form-check-label" for="is_current">Current committee</label></div></div>
        <div class="col-12"><textarea class="form-control" name="description" rows="2" aria-label="Description">{{ $committee->description }}</textarea></div>
    </div>
    <button class="btn btn-ieee mt-2">Save committee</button>
</form>
<form class="d-inline" method="POST" action="{{ route('admin.committee.destroy', $committee) }}" data-confirm="Archive this committee? Historical records stay in the database.">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm mb-4">Archive committee</button></form>
<h2 class="h5">Members</h2>
@foreach ($committee->members as $person)
    <form class="form-card mb-3" method="POST" action="{{ route('admin.committee.members.update', $person) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="row g-2">
            <div class="col-md-4"><input class="form-control" name="name" value="{{ $person->name }}" aria-label="Name" required></div>
            <div class="col-md-3"><select class="form-select" name="committee_position_id" aria-label="Position">@foreach ($positions as $position)<option value="{{ $position->id }}" @selected($person->committee_position_id === $position->id)>{{ $position->name }}</option>@endforeach</select></div>
            <div class="col-md-3"><input class="form-control" name="department" value="{{ $person->department }}" placeholder="Department" aria-label="Department"></div>
            <div class="col-md-2"><input class="form-control" name="batch" value="{{ $person->batch }}" placeholder="Batch" aria-label="Batch"></div>
            <div class="col-12"><textarea class="form-control" name="bio" rows="2" aria-label="Bio">{{ $person->bio }}</textarea></div>
            <div class="col-md-4"><input class="form-control" name="linkedin" value="{{ $person->linkedin }}" placeholder="LinkedIn URL" aria-label="LinkedIn"></div>
            <div class="col-md-4"><input class="form-control" name="facebook" value="{{ $person->facebook }}" placeholder="Facebook URL" aria-label="Facebook"></div>
            <div class="col-md-4"><input class="form-control" type="file" name="photo" accept="image/png,image/jpeg,image/webp,image/gif" aria-label="Photo"></div>
        </div>
        <button class="btn btn-outline-primary btn-sm mt-2">Save member</button>
    </form>
    <form method="POST" action="{{ route('admin.committee.members.destroy', $person) }}" data-confirm="Remove this person from the committee term?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger mb-3">Remove</button></form>
@endforeach
<form class="form-card" method="POST" action="{{ route('admin.committee.members.store', $committee) }}" enctype="multipart/form-data">
    @csrf
    <h2 class="h6">Add member</h2>
    <div class="row g-2">
        <div class="col-md-4"><input class="form-control" name="name" placeholder="Name" required aria-label="Name"></div>
        <div class="col-md-4"><select class="form-select" name="committee_position_id" required aria-label="Position">@foreach ($positions as $position)<option value="{{ $position->id }}">{{ $position->name }}</option>@endforeach</select></div>
        <div class="col-md-4"><input class="form-control" name="department" placeholder="Department" aria-label="Department"></div>
        <div class="col-md-4"><input class="form-control" name="batch" placeholder="Batch" aria-label="Batch"></div>
        <div class="col-md-8"><input class="form-control" name="bio" placeholder="Short bio" aria-label="Bio"></div>
    </div>
    <button class="btn btn-ieee mt-3">Add member</button>
</form>
@endsection
