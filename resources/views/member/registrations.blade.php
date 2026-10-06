@extends('layouts.dashboard')
@section('title', 'My registrations')
@section('heading', 'My registrations')
@section('content')
<div class="table-card"><table class="table align-middle">
    <thead><tr><th>Event</th><th>When</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @forelse ($registrations as $registration)
        <tr>
            <td>{{ $registration->event?->title }}</td>
            <td>{{ $registration->event?->starts_at?->format('d M Y') }}</td>
            <td><span class="badge status-{{ $registration->status }}">{{ $registration->status }}</span></td>
            <td>
                @if ($registration->status === 'registered')
                    <form method="POST" action="{{ route('member.registrations.cancel', $registration) }}" data-confirm="Cancel this registration?">
                        @csrf
                        <button class="btn btn-sm btn-outline-danger">Cancel</button>
                    </form>
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="4" class="empty-state">No registrations yet.</td></tr>
    @endforelse
    </tbody>
</table></div>
<div class="mt-3">{{ $registrations->links() }}</div>
@endsection
