@extends('layouts.dashboard')
@section('title', 'Edit member')
@section('heading', 'Edit '.$member->name)
@section('content')
<form class="form-card" method="POST" action="{{ route('admin.members.update', $member) }}">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label" for="name">Name</label><input class="form-control" id="name" name="name" value="{{ old('name', $member->name) }}" required></div>
        <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" value="{{ $member->email }}" disabled></div>
        <div class="col-md-4"><label class="form-label" for="status">Status</label><select class="form-select" id="status" name="status">@foreach (['pending','active','suspended','inactive'] as $status)<option @selected(old('status', $member->status) === $status)>{{ $status }}</option>@endforeach</select></div>
        <div class="col-md-4"><label class="form-label" for="department">Department</label><input class="form-control" id="department" name="department" value="{{ old('department', $member->profile?->department) }}"></div>
        <div class="col-md-4"><label class="form-label" for="batch">Batch</label><input class="form-control" id="batch" name="batch" value="{{ old('batch', $member->profile?->batch) }}"></div>
        <div class="col-md-4"><label class="form-label" for="student_id">Student ID</label><input class="form-control" id="student_id" name="student_id" value="{{ old('student_id', $member->profile?->student_id) }}"></div>
    </div>
    <p class="muted mt-3 mb-0">Role changes are not available here. A member cannot be promoted to admin from this form.</p>
    <button class="btn btn-ieee mt-3">Save member</button>
</form>
@endsection
