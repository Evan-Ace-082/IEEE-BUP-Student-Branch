@extends('layouts.dashboard')
@section('title', 'Registrations')
@section('heading', 'Event registrations')
@section('content')
<form class="filter-bar row g-2 mb-3" method="GET">
    <div class="col-md-3"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Name, email, ID" aria-label="Search"></div>
    <div class="col-md-3"><select class="form-select" name="event_id" aria-label="Event"><option value="">All events</option>@foreach ($events as $event)<option value="{{ $event->id }}" @selected((string) request('event_id') === (string) $event->id)>{{ $event->title }}</option>@endforeach</select></div>
    <div class="col-md-2"><select class="form-select" name="status" aria-label="Status"><option value="">All statuses</option>@foreach ($statuses as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select></div>
    <div class="col-md-2"><button class="btn btn-ieee w-100">Filter</button></div>
    <div class="col-md-2"><a class="btn btn-outline-secondary w-100" href="{{ route('admin.registrations.export', request()->query()) }}">Export CSV</a></div>
</form>
<div class="table-card"><table class="table align-middle"><thead><tr><th>Name</th><th>Event</th><th>Email</th><th>Student ID</th><th>Status</th></tr></thead><tbody>
@forelse ($registrations as $registration)
<tr>
    <td>{{ $registration->name }}<div class="muted small">{{ $registration->department }} {{ $registration->batch }}</div></td>
    <td>{{ $registration->event?->title }}</td>
    <td>{{ $registration->email }}</td>
    <td>{{ $registration->student_id }}</td>
    <td>
        <form method="POST" action="{{ route('admin.registrations.update', $registration) }}" class="d-flex gap-2">
            @csrf @method('PUT')
            <select class="form-select form-select-sm" name="status" aria-label="Update status">@foreach ($statuses as $value => $label)<option value="{{ $value }}" @selected($registration->status === $value)>{{ $label }}</option>@endforeach</select>
            <button class="btn btn-sm btn-outline-primary">Save</button>
        </form>
    </td>
</tr>
@empty
<tr><td colspan="5" class="empty-state">No registrations.</td></tr>
@endforelse
</tbody></table></div>
<div class="mt-3">{{ $registrations->links() }}</div>
@endsection
