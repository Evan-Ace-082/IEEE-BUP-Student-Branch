@extends('layouts.dashboard')
@section('title', 'System settings')
@section('heading', 'System settings')
@section('content')
<form class="form-card" method="POST" action="{{ route('super.settings.update') }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="row g-3">
        @foreach ([
            'branch_name' => 'Branch name',
            'tagline' => 'Tagline',
            'official_email' => 'Official email',
            'phone' => 'Phone',
            'address' => 'Address',
            'facebook' => 'Facebook',
            'linkedin' => 'LinkedIn',
            'instagram' => 'Instagram',
            'website' => 'Website',
            'ieee_join_url' => 'IEEE join URL',
            'map_embed_url' => 'Map embed URL',
        ] as $key => $label)
            <div class="col-md-6"><label class="form-label" for="{{ $key }}">{{ $label }}</label><input class="form-control" id="{{ $key }}" name="{{ $key }}" value="{{ old($key, $settings[$key]) }}"></div>
        @endforeach
        <div class="col-md-6"><label class="form-label" for="logo">Logo</label><input class="form-control" id="logo" type="file" name="logo" accept="image/png,image/jpeg,image/webp,image/gif"></div>
        <div class="col-md-6"><label class="form-label" for="favicon">Favicon</label><input class="form-control" id="favicon" type="file" name="favicon" accept="image/png,image/jpeg,image/webp,image/gif"></div>
    </div>
    <button class="btn btn-ieee mt-3">Save settings</button>
</form>
@endsection
