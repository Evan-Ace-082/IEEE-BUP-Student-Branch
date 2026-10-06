@extends('layouts.dashboard')
@section('title', 'Activity log')
@section('heading', 'Activity log')
@section('content')
<p class="muted">Audit entries are kept for review. This screen does not delete them.</p>
<form class="d-flex gap-2 mb-3" method="GET">
    <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Description" aria-label="Search">
    <input class="form-control" name="module" value="{{ request('module') }}" placeholder="Module" aria-label="Module">
    <input class="form-control" name="action" value="{{ request('action') }}" placeholder="Action" aria-label="Action">
    <button class="btn btn-outline-secondary">Filter</button>
</form>
<div class="table-card"><table class="table table-sm"><thead><tr><th>When</th><th>User</th><th>Action</th><th>Module</th><th>Description</th><th>IP</th></tr></thead><tbody>
@forelse ($logs as $log)
<tr>
    <td>{{ $log->created_at?->format('d M Y H:i') }}</td>
    <td>{{ $log->user?->name ?? '—' }}</td>
    <td>{{ $log->action }}</td>
    <td>{{ $log->module }}</td>
    <td>{{ $log->description }}</td>
    <td>{{ $log->ip_address }}</td>
</tr>
@empty<tr><td colspan="6" class="empty-state">No log entries.</td></tr>@endforelse
</tbody></table></div>
<div class="mt-3">{{ $logs->links() }}</div>
@endsection
