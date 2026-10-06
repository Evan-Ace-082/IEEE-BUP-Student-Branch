@extends('layouts.dashboard')
@section('title', 'Committee')
@section('heading', 'Executive committees')
@section('content')
<div class="row g-4">
    <div class="col-lg-7">
        <div class="table-card"><table class="table"><thead><tr><th>Committee</th><th>Year</th><th>Members</th><th></th></tr></thead><tbody>
        @foreach ($committees as $committee)
            <tr><td>{{ $committee->name }} @if($committee->is_current)<span class="badge status-published">Current</span>@endif</td><td>{{ $committee->year }}</td><td>{{ $committee->members_count }}</td><td><a href="{{ route('admin.committee.show', $committee) }}">Open</a></td></tr>
        @endforeach
        </tbody></table></div>
        <form class="form-card mt-3" method="POST" action="{{ route('admin.committee.store') }}">
            @csrf
            <h2 class="h5">New committee term</h2>
            <div class="row g-2">
                <div class="col-md-5"><input class="form-control" name="name" placeholder="Name" required aria-label="Committee name"></div>
                <div class="col-md-3"><input class="form-control" name="year" type="number" placeholder="Year" required aria-label="Year"></div>
                <div class="col-md-4 d-flex align-items-center"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_current" value="1" id="is_current"><label class="form-check-label" for="is_current">Set as current</label></div></div>
            </div>
            <button class="btn btn-ieee mt-3">Create committee</button>
        </form>
    </div>
    <div class="col-lg-5">
        <div class="form-card">
            <h2 class="h5">Positions</h2>
            @foreach ($positions as $position)
                <form class="d-flex gap-2 mb-2" method="POST" action="{{ route('admin.positions.update', $position) }}">
                    @csrf @method('PUT')
                    <input class="form-control" name="name" value="{{ $position->name }}" aria-label="Position name">
                    <input class="form-control" style="max-width:90px" name="sort_order" value="{{ $position->sort_order }}" aria-label="Sort order">
                    <button class="btn btn-outline-primary btn-sm">Save</button>
                </form>
            @endforeach
            <form class="d-flex gap-2 mt-3" method="POST" action="{{ route('admin.positions.store') }}">
                @csrf
                <input class="form-control" name="name" placeholder="New position" required aria-label="New position">
                <input class="form-control" style="max-width:90px" name="sort_order" placeholder="Order" aria-label="Order">
                <button class="btn btn-ieee btn-sm">Add</button>
            </form>
        </div>
    </div>
</div>
@endsection
