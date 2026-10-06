@extends('layouts.public')
@section('title', 'Reset password')
@section('content')
<section class="section"><div class="container col-md-6">
    <form class="form-card" method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <h1 class="h3">Choose a new password</h1>
        <div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control" id="email" type="email" name="email" value="{{ old('email', $email) }}" required></div>
        <div class="mb-3"><label class="form-label" for="password">Password</label><input class="form-control" id="password" type="password" name="password" required></div>
        <div class="mb-3"><label class="form-label" for="password_confirmation">Confirm password</label><input class="form-control" id="password_confirmation" type="password" name="password_confirmation" required></div>
        <button class="btn btn-ieee" type="submit">Reset password</button>
    </form>
</div></section>
@endsection
