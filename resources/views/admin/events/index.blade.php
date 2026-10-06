@extends('layouts.dashboard')
@section('title', 'Events')
@section('heading', 'Events')
@section('content')
<div class="d-flex justify-content-between mb-3"><form class="d-flex gap-2" method="GET"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Search"><button class="btn btn-outline-secondary">Search</button></form><a class="btn btn-ieee" href="{{ route('admin.events.create') }}">New event</a></div>
<div class="table-card"><table class="table align-middle"><thead><tr><th>Title</th><th>When</th><th>Status</th><th>Registrations</th><th></th></tr></thead><tbody>
@forelse ($events as $event)
<tr>
    <td>{{ $event->title }}</td>
    <td>{{ $event->starts_at?->format('d M Y H:i') }}</td>
    <td><span class="badge status-{{ $event->status }}">{{ $event->status }}</span></td>
    <td>{{ $event->registrations_count }}</td>
    <td class="text-nowrap"><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.events.edit', $event) }}">Edit</a>
        <form class="d-inline" method="POST" action="{{ route('admin.events.destroy', $event) }}" data-confirm="Delete this event?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>
    </td>
</tr>
@empty
<tr><td colspan="5" class="empty-state">No events yet.</td></tr>
@endforelse
</tbody></table></div>
<div class="mt-3">{{ $events->links() }}</div>
@endsection
