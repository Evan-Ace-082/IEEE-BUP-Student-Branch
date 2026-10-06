@extends('layouts.public')
@section('title', 'Members')
@section('content')
<header class="page-hero"><div class="container"><p class="eyebrow">Directory</p><h1>Members</h1><p>Public profiles show only what each member chooses to share. Email and phone stay hidden unless the member turns them on.</p></div></header>
<section class="section"><div class="container">
    <form class="filter-bar row g-2 mb-4" method="GET">
        <div class="col-md-3"><input class="form-control" name="name" value="{{ request('name') }}" placeholder="Name" aria-label="Name"></div>
        @auth
            <div class="col-md-3"><input class="form-control" name="student_id" value="{{ request('student_id') }}" placeholder="Student ID" aria-label="Student ID"></div>
        @endauth
        <div class="col-md-2">
            <select class="form-select" name="department" aria-label="Department"><option value="">Department</option>@foreach ($departments as $department)<option @selected(request('department') === $department)>{{ $department }}</option>@endforeach</select>
        </div>
        <div class="col-md-2">
            <select class="form-select" name="batch" aria-label="Batch"><option value="">Batch</option>@foreach ($batches as $batch)<option @selected(request('batch') === $batch)>{{ $batch }}</option>@endforeach</select>
        </div>
        <div class="col-md-2"><input class="form-control" name="skill" value="{{ request('skill') }}" placeholder="Skill" aria-label="Skill"></div>
        <div class="col-md-2"><input class="form-control" name="interest" value="{{ request('interest') }}" placeholder="Interest" aria-label="Interest"></div>
        <div class="col-md-3">
            <select class="form-select" name="ieee_membership_status" aria-label="IEEE status">
                <option value="">IEEE status</option>
                @foreach (['none' => 'Not a member', 'student' => 'Student member', 'graduate' => 'Graduate student member', 'professional' => 'Professional member'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('ieee_membership_status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-ieee w-100">Search</button></div>
    </form>
    <div class="row g-4">
        @forelse ($members as $profile)
            <div class="col-md-6 col-lg-4">
                <article class="person-card">
                    <div class="body d-flex gap-3">
                        @if ($profile->photo)
                            <img src="{{ public_file_url($profile->photo) }}" alt="" style="width:72px;height:72px;border-radius:12px;object-fit:cover">
                        @else
                            <div class="initials" style="width:72px;height:72px;border-radius:12px;flex:none">{{ strtoupper(substr($profile->user->name, 0, 1)) }}</div>
                        @endif
                        <div>
                            <h2 class="h5 mb-1"><a href="{{ route('members.show', $profile->user) }}">{{ $profile->user->name }}</a></h2>
                            <p class="muted small mb-1">{{ collect([$profile->department, $profile->batch])->filter()->implode(' · ') }}</p>
                            <p class="small mb-0">{{ $profile->skillList() }}</p>
                        </div>
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12"><div class="empty-state card-soft">No members match these filters.</div></div>
        @endforelse
    </div>
    <div class="mt-4">{{ $members->links() }}</div>
</div></section>
@endsection
