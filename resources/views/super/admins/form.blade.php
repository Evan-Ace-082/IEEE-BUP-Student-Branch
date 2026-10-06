@extends('layouts.dashboard')
@section('title', $adminUser->exists ? 'Edit admin' : 'Create admin')
@section('heading', $adminUser->exists ? 'Edit admin' : 'Create admin')
@section('content')
<form class="form-card col-lg-7" method="POST" action="{{ $adminUser->exists ? route('super.admins.update', $adminUser) : route('super.admins.store') }}">
    @csrf @if($adminUser->exists) @method('PUT') @endif
    <div class="mb-3"><label class="form-label" for="name">Name</label><input class="form-control" id="name" name="name" value="{{ old('name', $adminUser->name) }}" required></div>
    <div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control" id="email" type="email" name="email" value="{{ old('email', $adminUser->email) }}" required></div>
    <div class="mb-3"><label class="form-label" for="password">Password {{ $adminUser->exists ? '(leave blank to keep)' : '' }}</label><input class="form-control" id="password" type="password" name="password" {{ $adminUser->exists ? '' : 'required' }}></div>
    <div class="mb-3"><label class="form-label" for="password_confirmation">Confirm password</label><input class="form-control" id="password_confirmation" type="password" name="password_confirmation"></div>
    <div class="mb-3"><label class="form-label" for="status">Status</label><select class="form-select" id="status" name="status">@foreach (['active','suspended','inactive'] as $status)<option @selected(old('status', $adminUser->status ?: 'active') === $status)>{{ $status }}</option>@endforeach</select></div>
    <p class="muted">New accounts are always created as Admin. This form cannot assign Super Admin.</p>
    <button class="btn btn-ieee">Save</button>
</form>
@endsection
