@extends('layouts.dashboard')
@section('title', 'Messages')
@section('heading', 'Contact messages')
@section('content')
<form class="d-flex gap-2 mb-3" method="GET"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Search"><select class="form-select" name="status" aria-label="Status"><option value="">All</option>@foreach ($statuses as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select><button class="btn btn-outline-secondary">Filter</button></form>
<div class="table-card mb-4"><table class="table"><thead><tr><th>From</th><th>Subject</th><th>Message</th><th>Status</th></tr></thead><tbody>
@forelse ($messages as $message)
<tr>
    <td>{{ $message->name }}<div class="muted small">{{ $message->email }}</div></td>
    <td>{{ $message->subject }}</td>
    <td>{{ \Illuminate\Support\Str::limit($message->message, 140) }}</td>
    <td>
        <form method="POST" action="{{ route('admin.messages.update', $message) }}">
            @csrf @method('PUT')
            <select class="form-select form-select-sm mb-1" name="status" aria-label="Message status">@foreach ($statuses as $value => $label)<option value="{{ $value }}" @selected($message->status === $value)>{{ $label }}</option>@endforeach</select>
            <button class="btn btn-sm btn-outline-primary">Update</button>
        </form>
    </td>
</tr>
@empty<tr><td colspan="4" class="empty-state">No messages.</td></tr>@endforelse
</tbody></table></div>
<div class="mb-4">{{ $messages->links() }}</div>
<h2 class="h5">Membership interest forms</h2>
<div class="table-card"><table class="table"><thead><tr><th>Name</th><th>Email</th><th>Status</th></tr></thead><tbody>
@forelse ($applications as $application)
<tr>
    <td>{{ $application->name }}<div class="muted small">{{ $application->department }} {{ $application->batch }}</div></td>
    <td>{{ $application->email }}</td>
    <td>
        <form class="d-flex gap-2" method="POST" action="{{ route('admin.applications.update', $application) }}">
            @csrf @method('PUT')
            <select class="form-select form-select-sm" name="status" aria-label="Application status">@foreach (['submitted','reviewing','closed'] as $status)<option @selected($application->status === $status)>{{ $status }}</option>@endforeach</select>
            <button class="btn btn-sm btn-outline-primary">Save</button>
        </form>
    </td>
</tr>
@empty<tr><td colspan="3" class="empty-state">No interest forms.</td></tr>@endforelse
</tbody></table></div>
@endsection
