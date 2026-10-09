@extends('layouts.dashboard')
@section('title', $profile->exists ? 'Edit leadership profile' : 'New leadership profile')
@section('heading', $profile->exists ? 'Edit leadership profile' : 'New leadership profile')
@section('content')
<form class="form-card" method="POST" action="{{ $profile->exists ? route('admin.leadership.update', $profile) : route('admin.leadership.store') }}" enctype="multipart/form-data">
    @csrf @if($profile->exists) @method('PUT') @endif
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label" for="name">Full name</label><input class="form-control" id="name" name="name" value="{{ old('name', $profile->name) }}" required></div>
        <div class="col-md-6"><label class="form-label" for="role">Role</label><select class="form-select" id="role" name="role">@foreach ($roles as $value => $label)<option value="{{ $value }}" @selected(old('role', $profile->role) === $value)>{{ $label }}</option>@endforeach</select><div class="form-text">Choose Chairman and Faculty Advisor separately when they are different people. Use Chairman / Faculty Advisor when one person holds both seats.</div></div>
        <div class="col-md-6"><label class="form-label" for="position_label">Other position</label><input class="form-control" id="position_label" name="position_label" value="{{ old('position_label', $profile->position_label) }}"><div class="form-text">Required only when the role is Other faculty position. Use this for a faculty seat that is not listed above.</div></div>
        <div class="col-md-6"><label class="form-label" for="designation">Official designation</label><input class="form-control" id="designation" name="designation" value="{{ old('designation', $profile->designation) }}"></div>
        <div class="col-md-6"><label class="form-label" for="department">Department or affiliation</label><input class="form-control" id="department" name="department" value="{{ old('department', $profile->department) }}"></div>
        <div class="col-md-3"><label class="form-label" for="sort_order">Display order</label><input class="form-control" id="sort_order" type="number" min="0" max="9999" name="sort_order" value="{{ old('sort_order', $profile->sort_order ?? 0) }}" required></div>
        <div class="col-md-3"><label class="form-label" for="is_published">Status</label><select class="form-select" id="is_published" name="is_published"><option value="0" @selected(old('is_published', $profile->is_published ? '1' : '0') == '0')>Unpublished</option><option value="1" @selected(old('is_published', $profile->is_published ? '1' : '0') == '1')>Published</option></select></div>
        <div class="col-12"><label class="form-label" for="bio">Biography</label><textarea class="form-control" id="bio" name="bio" rows="5">{{ old('bio', $profile->bio) }}</textarea></div>
        <div class="col-md-6"><label class="form-label" for="email">Email</label><input class="form-control" id="email" type="email" name="email" value="{{ old('email', $profile->email) }}"></div>
        <div class="col-md-6"><label class="form-label" for="phone">Phone</label><input class="form-control" id="phone" name="phone" value="{{ old('phone', $profile->phone) }}"></div>
        <div class="col-md-6"><div class="form-check"><input class="form-check-input" id="show_email" type="checkbox" name="show_email" value="1" @checked(old('show_email', $profile->show_email))><label class="form-check-label" for="show_email">Show email on About Us</label></div></div>
        <div class="col-md-6"><div class="form-check"><input class="form-check-input" id="show_phone" type="checkbox" name="show_phone" value="1" @checked(old('show_phone', $profile->show_phone))><label class="form-check-label" for="show_phone">Show phone on About Us</label></div></div>
        <div class="col-md-6"><label class="form-label" for="photo">Photo</label><input class="form-control" id="photo" type="file" name="photo" accept="image/png,image/jpeg,image/webp,image/gif">@if($profile->photo)<div class="form-text">A photo is already saved. Upload a file to replace it.</div>@endif</div>
        @if($profile->photo)
            <div class="col-md-6 d-flex align-items-end"><div class="form-check"><input class="form-check-input" id="remove_photo" type="checkbox" name="remove_photo" value="1" @checked(old('remove_photo'))><label class="form-check-label" for="remove_photo">Remove current photo</label></div></div>
        @endif
    </div>
    <button class="btn btn-ieee mt-3">Save</button>
</form>
@endsection
