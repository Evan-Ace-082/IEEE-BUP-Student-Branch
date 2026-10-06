@extends('layouts.dashboard')
@section('title', 'Admin dashboard')
@section('heading', auth()->user()->isSuperAdmin() ? 'Super Admin dashboard' : 'Admin dashboard')
@section('content')
<div class="row g-3 mb-4">
    @foreach ([
        'Total members' => $stats['members'],
        'Active members' => $stats['active_members'],
        'Pending members' => $stats['pending_members'],
        'Total events' => $stats['events'],
        'Upcoming events' => $stats['upcoming'],
        'Announcements' => $stats['announcements'],
        'Achievements' => $stats['achievements'],
        'Gallery items' => $stats['gallery'],
        'Registrations' => $stats['registrations'],
        'Unread messages' => $stats['unread_messages'],
        'Active admins' => $stats['active_admins'],
        'Pending achievements' => $stats['pending_achievements'],
    ] as $label => $value)
        <div class="col-6 col-md-4 col-xl-3"><div class="metric"><span>{{ $label }}</span><strong>{{ $value }}</strong></div></div>
    @endforeach
    @if (auth()->user()->isSuperAdmin())
        <div class="col-6 col-md-4 col-xl-3"><div class="metric"><span>Total admins</span><strong>{{ $stats['admins'] }}</strong></div></div>
        <div class="col-6 col-md-4 col-xl-3"><div class="metric"><span>Suspended admins</span><strong>{{ $stats['suspended_admins'] }}</strong></div></div>
    @endif
</div>
<div class="row g-4">
    <div class="col-lg-6">
        <h2 class="h5">Recent activity</h2>
        <div class="table-card"><table class="table table-sm"><tbody>
            @forelse ($activity as $log)
                <tr><td>{{ $log->created_at?->format('d M H:i') }}</td><td>{{ $log->user?->name ?? 'System' }}</td><td>{{ $log->action }} · {{ $log->module }}</td></tr>
            @empty
                <tr><td class="empty-state">No activity yet.</td></tr>
            @endforelse
        </tbody></table></div>
    </div>
    <div class="col-lg-6">
        <h2 class="h5">Upcoming events</h2>
        @forelse ($upcoming as $event)<p class="mb-1"><a href="{{ route('admin.events.edit', $event) }}">{{ $event->title }}</a> <span class="muted">{{ $event->starts_at?->format('d M') }}</span></p>@empty<p class="muted">None scheduled.</p>@endforelse
        <h2 class="h5 mt-4">Recent registrations</h2>
        @forelse ($registrations as $registration)<p class="mb-1">{{ $registration->name }} · {{ $registration->event?->title }}</p>@empty<p class="muted">No registrations yet.</p>@endforelse
        <h2 class="h5 mt-4">Pending achievement approvals</h2>
        @forelse ($pendingAchievements as $achievement)<p class="mb-1"><a href="{{ route('admin.achievements.edit', $achievement) }}">{{ $achievement->title }}</a></p>@empty<p class="muted">Nothing waiting for review.</p>@endforelse
    </div>
</div>
@if (auth()->user()->isSuperAdmin())
    <div class="form-card mt-4">
        <h2 class="h5">System status</h2>
        <p class="mb-1">PHP {{ $system['php'] }} · Laravel {{ $system['laravel'] }}</p>
        <p class="mb-1">Database {{ $system['database'] }} · Cache {{ $system['cache'] }} · Queue {{ $system['queue'] }}</p>
        <p class="mb-0">Environment {{ $system['environment'] }} · Debug {{ $system['debug'] ? 'on' : 'off' }}</p>
        @if ($system['debug'] && $system['environment'] === 'production')
            <div class="alert alert-danger mt-3 mb-0">APP_DEBUG is on in production. Turn it off.</div>
        @endif
    </div>
@endif
@endsection
