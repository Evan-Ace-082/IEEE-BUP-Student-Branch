@extends('layouts.dashboard')
@section('title', 'My profile')
@section('heading', 'My profile')
@section('content')
<form class="form-card" method="POST" action="{{ route('member.profile.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label" for="name">Name</label><input class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required></div>
        <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" value="{{ $user->email }}" disabled></div>
        <div class="col-md-4"><label class="form-label" for="student_id">Student ID</label><input class="form-control" id="student_id" name="student_id" value="{{ old('student_id', $profile->student_id) }}"></div>
        <div class="col-md-4"><label class="form-label" for="department">Department</label><input class="form-control" id="department" name="department" value="{{ old('department', $profile->department) }}"></div>
        <div class="col-md-2"><label class="form-label" for="batch">Batch</label><input class="form-control" id="batch" name="batch" value="{{ old('batch', $profile->batch) }}"></div>
        <div class="col-md-2"><label class="form-label" for="session">Session</label><input class="form-control" id="session" name="session" value="{{ old('session', $profile->session) }}"></div>
        <div class="col-md-4"><label class="form-label" for="ieee_membership_id">IEEE membership ID</label><input class="form-control" id="ieee_membership_id" name="ieee_membership_id" value="{{ old('ieee_membership_id', $profile->ieee_membership_id) }}"></div>
        <div class="col-md-4">
            <label class="form-label" for="ieee_membership_status">IEEE status</label>
            <select class="form-select" id="ieee_membership_status" name="ieee_membership_status">
                @foreach (['none' => 'Not a member', 'student' => 'Student member', 'graduate' => 'Graduate student member', 'professional' => 'Professional member'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('ieee_membership_status', $profile->ieee_membership_status) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4"><label class="form-label" for="phone">Phone</label><input class="form-control" id="phone" name="phone" value="{{ old('phone', $profile->phone) }}"></div>
        <div class="col-md-6"><label class="form-label" for="skills">Skills</label><input class="form-control" id="skills" name="skills" value="{{ old('skills', $profile->skillList()) }}" placeholder="Comma separated"></div>
        <div class="col-md-6"><label class="form-label" for="interests">Interests</label><input class="form-control" id="interests" name="interests" value="{{ old('interests', $profile->interestList()) }}"></div>
        <div class="col-md-6"><label class="form-label" for="linkedin">LinkedIn</label><input class="form-control" id="linkedin" name="linkedin" value="{{ old('linkedin', $profile->linkedin) }}"></div>
        <div class="col-md-6"><label class="form-label" for="facebook">Facebook</label><input class="form-control" id="facebook" name="facebook" value="{{ old('facebook', $profile->facebook) }}"></div>
        <div class="col-12"><label class="form-label" for="bio">Bio</label><textarea class="form-control" id="bio" name="bio" rows="4">{{ old('bio', $profile->bio) }}</textarea></div>
        <div class="col-md-6"><label class="form-label" for="photo">Profile photo</label><input class="form-control" id="photo" type="file" name="photo" accept="image/png,image/jpeg,image/webp,image/gif"></div>
        <div class="col-md-6 d-flex align-items-end"><div class="form-check"><input class="form-check-input" type="checkbox" name="remove_photo" value="1" id="remove_photo"><label class="form-check-label" for="remove_photo">Remove current photo</label></div></div>
        <div class="col-12 d-flex flex-wrap gap-3">
            <div class="form-check"><input class="form-check-input" type="checkbox" name="directory_visible" value="1" id="directory_visible" @checked(old('directory_visible', $profile->directory_visible))><label class="form-check-label" for="directory_visible">Show me in the directory</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="show_email" value="1" id="show_email" @checked(old('show_email', $profile->show_email))><label class="form-check-label" for="show_email">Show email</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="show_phone" value="1" id="show_phone" @checked(old('show_phone', $profile->show_phone))><label class="form-check-label" for="show_phone">Show phone</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="show_social" value="1" id="show_social" @checked(old('show_social', $profile->show_social))><label class="form-check-label" for="show_social">Show social links</label></div>
        </div>
    </div>
    <button class="btn btn-ieee mt-3" type="submit">Save profile</button>
</form>
@endsection
