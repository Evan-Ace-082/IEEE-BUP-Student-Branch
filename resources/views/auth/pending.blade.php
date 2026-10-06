@extends('layouts.public')
@section('title', 'Waiting for approval')
@section('content')
<section class="section"><div class="container col-lg-7">
    <div class="form-card">
        <h1 class="h3">Account pending approval</h1>
        @if (! auth()->user()->hasVerifiedEmail())
            <p>Your email is not verified yet. <a href="{{ route('verification.notice') }}">Verify it first.</a></p>
        @endif
        <p class="mb-0">{{ auth()->user()->name }}, an administrator needs to approve this account before you can use the member dashboard. You can still browse the public website.</p>
    </div>
</div></section>
@endsection
