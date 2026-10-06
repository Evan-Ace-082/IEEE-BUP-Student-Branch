@extends('layouts.dashboard')
@section('title', $adminUser->name)
@section('heading', $adminUser->name)
@section('content')
<div class="form-card mb-3">
    <p><strong>Email:</strong> {{ $adminUser->email }}</p>
    <p><strong>Status:</strong> {{ $adminUser->status }}</p>
    <p><strong>Created:</strong> {{ $adminUser->created_at?->format('d M Y H:i') }}</p>
    <p class="mb-0"><strong>Last login:</strong> {{ $adminUser->last_login_at?->format('d M Y H:i') ?? 'Never' }} @if($adminUser->last_login_ip) from {{ $adminUser->last_login_ip }} @endif</p>
</div>
<h2 class="h5">Recent activity</h2>
<div class="table-card"><table class="table table-sm"><tbody>
@forelse ($logs as $log)<tr><td>{{ $log->created_at?->format('d M Y H:i') }}</td><td>{{ $log->action }}</td><td>{{ $log->module }}</td><td>{{ $log->description }}</td></tr>@empty<tr><td class="empty-state">No activity recorded.</td></tr>@endforelse
</tbody></table></div>
@endsection
