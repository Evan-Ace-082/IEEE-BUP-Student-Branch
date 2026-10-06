@extends('layouts.public')
@section('title', 'Forgot password')
@section('content')
<section class="section"><div class="container col-md-6">
    <form class="form-card" method="POST" action="{{ route('password.email') }}">
        @csrf
        <h1 class="h3">Forgot password</h1>
        <p class="muted">We will email a reset link if the account exists. In local development the message is written to the Laravel log.</p>
        <div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}" required></div>
        <button class="btn btn-ieee" type="submit">Send reset link</button>
    </form>
</div></section>
@endsection
