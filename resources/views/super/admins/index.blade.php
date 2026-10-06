@extends('layouts.dashboard')
@section('title', 'Admin management')
@section('heading', 'Admin management')
@section('content')
<a class="btn btn-ieee mb-3" href="{{ route('super.admins.create') }}">Create admin</a>
<div class="table-card"><table class="table align-middle"><thead><tr><th>Name</th><th>Email</th><th>Status</th><th>Created</th><th>Last login</th><th></th></tr></thead><tbody>
@forelse ($admins as $admin)
<tr>
    <td>{{ $admin->name }}</td><td>{{ $admin->email }}</td><td><span class="badge status-{{ $admin->status }}">{{ $admin->status }}</span></td>
    <td>{{ $admin->created_at?->format('d M Y') }}</td><td>{{ $admin->last_login_at?->format('d M Y H:i') ?? '—' }}</td>
    <td class="text-nowrap">
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('super.admins.show', $admin) }}">View</a>
        <a class="btn btn-sm btn-outline-primary" href="{{ route('super.admins.edit', $admin) }}">Edit</a>
        @if ($admin->status !== 'active')<form class="d-inline" method="POST" action="{{ route('super.admins.activate', $admin) }}">@csrf<button class="btn btn-sm btn-success">Activate</button></form>@endif
        @if ($admin->status !== 'suspended')<form class="d-inline" method="POST" action="{{ route('super.admins.suspend', $admin) }}" data-confirm="Suspend this administrator?">@csrf<button class="btn btn-sm btn-outline-warning">Suspend</button></form>@endif
        <form class="d-inline" method="POST" action="{{ route('super.admins.destroy', $admin) }}" data-confirm="Remove this administrator? Their content and audit history will be kept.">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Remove</button></form>
    </td>
</tr>
@empty<tr><td colspan="6" class="empty-state">No administrators yet.</td></tr>@endforelse
</tbody></table></div>
<div class="mt-3">{{ $admins->links() }}</div>
@endsection
