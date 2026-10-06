@extends('layouts.dashboard')
@section('title', 'Achievements')
@section('heading', 'Achievements')
@section('content')
<div class="d-flex justify-content-between mb-3"><a class="btn btn-ieee" href="{{ route('admin.achievements.create') }}">New achievement</a></div>
<div class="table-card"><table class="table"><thead><tr><th>Title</th><th>Submitted by</th><th>Status</th><th></th></tr></thead><tbody>
@forelse ($achievements as $item)
<tr>
    <td>{{ $item->title }}</td><td>{{ $item->submitter?->name ?? 'Branch' }}</td><td><span class="badge status-{{ $item->status }}">{{ $statuses[$item->status] ?? $item->status }}</span></td>
    <td><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.achievements.edit', $item) }}">Review</a>
        <form class="d-inline" method="POST" action="{{ route('admin.achievements.destroy', $item) }}" data-confirm="Delete this achievement?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td>
</tr>
@empty<tr><td colspan="4" class="empty-state">No achievements.</td></tr>@endforelse
</tbody></table></div>
<div class="mt-3">{{ $achievements->links() }}</div>
@endsection
