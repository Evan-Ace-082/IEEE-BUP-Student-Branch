@extends('layouts.dashboard')
@section('title', 'Members')
@section('heading', 'Members')
@section('content')
<form class="d-flex gap-2 mb-3" method="GET"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Name, email, student ID"><select class="form-select" name="status" aria-label="Status"><option value="">All</option>@foreach (['pending','active','suspended','inactive'] as $status)<option @selected(request('status') === $status)>{{ $status }}</option>@endforeach</select><button class="btn btn-outline-secondary">Search</button></form>
<div class="table-card"><table class="table align-middle"><thead><tr><th>Name</th><th>Email</th><th>Department</th><th>Status</th><th></th></tr></thead><tbody>
@forelse ($members as $member)
<tr>
    <td>{{ $member->name }}</td><td>{{ $member->email }}</td><td>{{ $member->profile?->department }}</td>
    <td><span class="badge status-{{ $member->status }}">{{ $member->status }}</span></td>
    <td class="text-nowrap">
        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.members.edit', $member) }}">Edit</a>
        @if ($member->status !== 'active')
            <form class="d-inline" method="POST" action="{{ route('admin.members.approve', $member) }}">@csrf<button class="btn btn-sm btn-success">Approve</button></form>
        @endif
        @if ($member->status !== 'suspended')
            <form class="d-inline" method="POST" action="{{ route('admin.members.suspend', $member) }}" data-confirm="Suspend this member?">@csrf<button class="btn btn-sm btn-outline-danger">Suspend</button></form>
        @endif
    </td>
</tr>
@empty<tr><td colspan="5" class="empty-state">No members.</td></tr>@endforelse
</tbody></table></div>
<div class="mt-3">{{ $members->links() }}</div>
@endsection
