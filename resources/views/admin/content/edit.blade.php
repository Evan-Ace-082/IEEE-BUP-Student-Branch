@extends('layouts.dashboard')
@section('title', 'Website content')
@section('heading', 'Website content')
@section('content')
<p class="muted">Faculty and club leadership profiles are managed on <a href="{{ route('admin.leadership.index') }}">Faculty leadership</a>. The introduction shown above those profiles is edited with the other About Us text below.</p>
<form method="POST" action="{{ route('admin.content.update') }}">
    @csrf @method('PUT')
    @foreach ($contents as $group => $items)
        <h2 class="h5 text-capitalize">{{ $group }}</h2>
        @foreach ($items as $item)
            <div class="form-card mb-3">
                <label class="form-label" for="body-{{ $item['key'] }}">{{ $item['title'] }}</label>
                <textarea class="form-control" id="body-{{ $item['key'] }}" name="bodies[{{ $item['key'] }}]" rows="4">{{ old('bodies.'.$item['key'], $item['body']) }}</textarea>
            </div>
        @endforeach
    @endforeach
    <h2 class="h5">Contact details</h2>
    <div class="form-card">
        <div class="row g-3">
            @foreach (['official_email' => 'Official email', 'phone' => 'Phone', 'address' => 'Address', 'facebook' => 'Facebook', 'linkedin' => 'LinkedIn', 'instagram' => 'Instagram', 'website' => 'Website', 'map_embed_url' => 'Map embed URL'] as $key => $label)
                <div class="col-md-6"><label class="form-label" for="{{ $key }}">{{ $label }}</label><input class="form-control" id="{{ $key }}" name="{{ $key }}" value="{{ old($key, $contact[$key]) }}"></div>
            @endforeach
        </div>
    </div>
    <button class="btn btn-ieee mt-3">Save content</button>
</form>
@endsection
