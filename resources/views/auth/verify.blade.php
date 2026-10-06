@extends('layouts.public')
@section('title', 'Verify email')
@section('content')
<section class="section"><div class="container col-lg-7">
    <div class="form-card">
        <h1 class="h3">Verify your email</h1>
        <p>Open the verification link we sent to {{ auth()->user()->email }}. The member dashboard stays closed until the email is verified and an administrator approves the account.</p>
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="btn btn-ieee" type="submit">Resend verification email</button>
        </form>
    </div>
</div></section>
@endsection
