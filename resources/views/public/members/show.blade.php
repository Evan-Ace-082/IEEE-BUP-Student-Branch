@extends('layouts.public')
@section('title', $user->name)
@section('content')
<header class="page-hero"><div class="container"><p class="eyebrow">Member</p><h1>{{ $user->name }}</h1><p>{{ collect([$profile?->department, $profile?->batch, $profile?->session])->filter()->implode(' · ') }}</p></div></header>
<section class="section"><div class="container col-lg-8">
    <article class="form-card">
        @if ($profile?->photo)<img class="rounded mb-3" src="{{ public_file_url($profile->photo) }}" alt="Photo of {{ $user->name }}" style="width:160px;height:160px;object-fit:cover">@endif
        @if ($profile?->bio)<p>{{ $profile->bio }}</p>@endif
        @if ($profile?->skillList())<p><strong>Skills:</strong> {{ $profile->skillList() }}</p>@endif
        @if ($profile?->interestList())<p><strong>Interests:</strong> {{ $profile->interestList() }}</p>@endif
        @if ($profile?->show_email)<p><strong>Email:</strong> {{ $user->email }}</p>@endif
        @if ($profile?->show_phone && $profile->phone)<p><strong>Phone:</strong> {{ $profile->phone }}</p>@endif
        @if ($profile?->show_social)
            <div class="d-flex gap-3">
                @if (safe_url($profile->linkedin))<a href="{{ safe_url($profile->linkedin) }}" target="_blank" rel="noopener noreferrer">LinkedIn</a>@endif
                @if (safe_url($profile->facebook))<a href="{{ safe_url($profile->facebook) }}" target="_blank" rel="noopener noreferrer">Facebook</a>@endif
            </div>
        @endif
    </article>
</div></section>
@endsection
