@extends('layouts.dashboard')
@section('title', 'Reports')
@section('heading', 'Reports')
@section('content')
<div class="d-flex gap-2 mb-3 no-print">
    <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.reports.members') }}">Members CSV</a>
    <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.reports.registrations') }}">Registrations CSV</a>
    <button class="btn btn-outline-secondary btn-sm" type="button" data-print>Print</button>
</div>
<div class="row g-4">
    <div class="col-lg-6"><div class="form-card"><h2 class="h5">Members by department</h2>@forelse ($membersByDepartment as $row)<p class="mb-1">{{ $row->department }} · {{ $row->total }}</p>@empty<p class="muted">No department data.</p>@endforelse</div></div>
    <div class="col-lg-6"><div class="form-card"><h2 class="h5">Announcements</h2>@foreach ($announcements as $status => $total)<p class="mb-1">{{ $status }} · {{ $total }}</p>@endforeach</div></div>
    <div class="col-lg-6"><div class="form-card"><h2 class="h5">Achievements</h2>@forelse ($achievements as $category => $total)<p class="mb-1">{{ $category }} · {{ $total }}</p>@empty<p class="muted">None.</p>@endforelse</div></div>
    <div class="col-lg-6"><div class="form-card"><h2 class="h5">Admin activity</h2>@foreach ($adminActivity as $row)<p class="mb-1">{{ $row->action }} · {{ $row->total }}</p>@endforeach</div></div>
    <div class="col-12"><div class="form-card"><h2 class="h5">Event participation</h2>
        <table class="table"><thead><tr><th>Event</th><th>Registrations</th><th>Attended</th></tr></thead><tbody>
        @foreach ($registrationsByEvent as $event)<tr><td>{{ $event->title }}</td><td>{{ $event->registrations_count }}</td><td>{{ $event->attended_count }}</td></tr>@endforeach
        </tbody></table>
    </div></div>
    <div class="col-12"><div class="form-card"><h2 class="h5">Committee history</h2>@foreach ($committees as $committee)<p class="mb-1">{{ $committee->year }} · {{ $committee->name }} · {{ $committee->members_count }} members @if($committee->is_current)(current)@endif</p>@endforeach</div></div>
</div>
@endsection
