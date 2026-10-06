@extends('layouts.public')
@section('title', 'Register')
@section('content')
<section class="section"><div class="container col-md-7 col-lg-6">
    <div class="form-card">
        <h1 class="h3">Create a campus account</h1>
        <p class="muted">After you verify your email, an administrator must approve the account before the member dashboard opens.</p>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="mb-3"><label class="form-label" for="name">Name</label><input class="form-control" id="name" name="name" value="{{ old('name') }}" required></div>
            <div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}" required></div>
            <div class="mb-3"><label class="form-label" for="password">Password</label><input class="form-control" id="password" type="password" name="password" required><div class="form-text">At least 8 characters, with upper and lower case letters and a number.</div></div>
            <div class="mb-3"><label class="form-label" for="password_confirmation">Confirm password</label><input class="form-control" id="password_confirmation" type="password" name="password_confirmation" required></div>
            <x-human-check />
            <button class="btn btn-ieee" type="submit">Register</button>
        </form>
    </div>
</div></section>
@endsection
