@extends('layouts.dashboard')
@section('title', 'Notifications')
@section('heading', 'Notifications')
@section('content')
<form method="POST" action="{{ route('notifications.read-all') }}" class="mb-3">@csrf<button class="btn btn-outline-secondary btn-sm">Mark all read</button></form>
<div class="table-card">
    <table class="table">
        <thead><tr><th>Notice</th><th>When</th><th></th></tr></thead>
        <tbody>
        @forelse ($notifications as $notification)
            <tr>
                <td><strong>{{ $notification->data['title'] ?? 'Notice' }}</strong><div class="muted">{{ $notification->data['body'] ?? '' }}</div></td>
                <td>{{ $notification->created_at?->diffForHumans() }}</td>
                <td>@if (! $notification->read_at)<form method="POST" action="{{ route('notifications.read', $notification->id) }}">@csrf<button class="btn btn-sm btn-ieee">Open</button></form>@endif</td>
            </tr>
        @empty
            <tr><td colspan="3" class="empty-state">No notifications yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $notifications->links() }}</div>
@endsection
