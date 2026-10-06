@extends('layouts.public')
@section('title', 'Membership')
@section('content')
<header class="page-hero"><div class="container"><p class="eyebrow">Membership</p><h1>Join the branch</h1><p>IEEE membership is completed on IEEE's own website. The form on this page only tells the branch you are interested.</p></div></header>
<section class="section"><div class="container">
    <div class="row g-4">
        @foreach ([
            'membership.why' => 'Why join IEEE?',
            'membership.benefits' => 'Membership benefits',
            'membership.eligibility' => 'Eligibility',
            'membership.ieee_info' => 'IEEE membership information',
        ] as $key => $heading)
            <div class="col-md-6">
                <article class="info-card h-100"><div class="body">
                    <h2 class="h4">{{ $heading }}</h2>
                    @foreach (content_paragraphs(page_content($key)) as $line)<p>{{ $line }}</p>@endforeach
                </div></article>
            </div>
        @endforeach
        <div class="col-12">
            <article class="info-card"><div class="body">
                <h2 class="h4">How to join</h2>
                <ol>@foreach (content_paragraphs(page_content('membership.how')) as $line)<li>{{ $line }}</li>@endforeach</ol>
                @if (safe_url(setting('ieee_join_url')))
                    <a class="btn btn-gold" href="{{ safe_url(setting('ieee_join_url')) }}" target="_blank" rel="noopener noreferrer">Open the official IEEE join page</a>
                @endif
            </div></article>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-lg-8">
            <div class="form-card">
                <h2 class="h4">Join BUP IEEE</h2>
                <p>{{ page_content('membership.join') }}</p>
                <form method="POST" action="{{ route('membership.apply') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label" for="name">Name</label><input class="form-control" id="name" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required></div>
                        <div class="col-md-6"><label class="form-label" for="email">Email</label><input class="form-control" id="email" type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required></div>
                        <div class="col-md-4"><label class="form-label" for="student_id">Student ID</label><input class="form-control" id="student_id" name="student_id" value="{{ old('student_id') }}"></div>
                        <div class="col-md-4"><label class="form-label" for="phone">Phone</label><input class="form-control" id="phone" name="phone" value="{{ old('phone') }}"></div>
                        <div class="col-md-4"><label class="form-label" for="batch">Batch</label><input class="form-control" id="batch" name="batch" value="{{ old('batch') }}"></div>
                        <div class="col-md-6"><label class="form-label" for="department">Department</label><input class="form-control" id="department" name="department" value="{{ old('department') }}"></div>
                        <div class="col-md-6">
                            <label class="form-label" for="ieee_membership_status">IEEE membership status</label>
                            <select class="form-select" id="ieee_membership_status" name="ieee_membership_status">
                                @foreach (['none' => 'Not a member yet', 'student' => 'Student member', 'graduate' => 'Graduate student member', 'professional' => 'Professional member'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('ieee_membership_status', 'none') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12"><label class="form-label" for="message">Message</label><textarea class="form-control" id="message" name="message" rows="4">{{ old('message') }}</textarea></div>
                    </div>
                    <x-human-check />
                    <button class="btn btn-ieee" type="submit">Submit interest form</button>
                </form>
            </div>
        </div>
    </div>
</div></section>
@endsection
