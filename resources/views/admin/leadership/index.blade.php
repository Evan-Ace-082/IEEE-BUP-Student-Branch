@extends('layouts.dashboard')
@section('title', 'Faculty leadership')
@section('heading', 'Faculty leadership')
@section('content')
<div class="d-flex justify-content-between mb-3">
    <p class="muted mb-0">These profiles appear in Faculty &amp; Club Leadership on the About Us page.</p>
    <a class="btn btn-ieee" href="{{ route('admin.leadership.create') }}">New profile</a>
</div>
<div class="table-card"><table class="table align-middle"><thead><tr><th>Order</th><th>Name</th><th>Role</th><th>Status</th><th></th></tr></thead><tbody>
@forelse ($profiles as $profile)
<tr>
    <td>{{ $profile->sort_order }}</td>
    <td>{{ $profile->name }}</td>
    <td>{{ $roles[$profile->role] ?? $profile->role }}</td>
    <td><span class="badge status-{{ $profile->is_published ? 'published' : 'draft' }}">{{ $profile->is_published ? 'published' : 'unpublished' }}</span></td>
    <td class="text-nowrap"><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.leadership.edit', $profile) }}">Edit</a>
        <form class="d-inline" method="POST" action="{{ route('admin.leadership.destroy', $profile) }}" data-confirm="Delete this leadership profile?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>
    </td>
</tr>
@empty
<tr><td colspan="5" class="empty-state">No leadership profiles yet. The About Us page shows placeholders until a profile is published.</td></tr>
@endforelse
</tbody></table></div>
<div class="mt-3">{{ $profiles->links() }}</div>
@endsection
