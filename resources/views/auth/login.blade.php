@extends('layouts.public')
@section('title', 'Login')
@section('content')
<section class="section"><div class="container col-md-6 col-lg-5">
    <div class="form-card">
        <h1 class="h3">Login</h1>
        <p class="muted">Use the email and password for your branch account.</p>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus></div>
            <div class="mb-3"><label class="form-label" for="password">Password</label><input class="form-control" id="password" type="password" name="password" required></div>
            <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="remember" id="remember" value="1"><label class="form-check-label" for="remember">Remember this browser</label></div>
            <button class="btn btn-ieee w-100" type="submit">Login</button>
        </form>
        <p class="mt-3 mb-0"><a href="{{ route('password.request') }}">Forgot password</a> · <a href="{{ route('register') }}">Create an account</a></p>
    </div>
</div></section>
@endsection
