@extends('layouts.dashboard')
@section('title', 'Password')
@section('heading', 'Change password')
@section('content')
<form class="form-card col-lg-6" method="POST" action="{{ route('admin.account.update') }}">
    @csrf @method('PUT')
    <div class="mb-3"><label class="form-label" for="current_password">Current password</label><input class="form-control" id="current_password" type="password" name="current_password" required></div>
    <div class="mb-3"><label class="form-label" for="password">New password</label><input class="form-control" id="password" type="password" name="password" required></div>
    <div class="mb-3"><label class="form-label" for="password_confirmation">Confirm new password</label><input class="form-control" id="password_confirmation" type="password" name="password_confirmation" required></div>
    <button class="btn btn-ieee">Update password</button>
</form>
@endsection
