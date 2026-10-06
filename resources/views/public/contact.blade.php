@extends('layouts.public')
@section('title', 'Contact')
@section('content')
<header class="page-hero"><div class="container"><p class="eyebrow">Contact</p><h1>Talk to the branch</h1></div></header>
<section class="section"><div class="container">
    <div class="row g-4">
        <div class="col-lg-5">
            <article class="form-card">
                <h2 class="h5">Branch desk</h2>
                @if (setting('official_email'))<p><strong>Email</strong><br><a href="mailto:{{ setting('official_email') }}">{{ setting('official_email') }}</a></p>@else<p class="muted">The official email can be added from the admin panel.</p>@endif
                @if (setting('phone'))<p><strong>Phone</strong><br>{{ setting('phone') }}</p>@endif
                @if (setting('address'))<p><strong>Location</strong><br>{{ setting('address') }}</p>@endif
                <div class="d-flex flex-wrap gap-3">
                    @foreach (['facebook' => 'Facebook', 'linkedin' => 'LinkedIn', 'instagram' => 'Instagram'] as $key => $label)
                        @if (safe_url(setting($key)))<a href="{{ safe_url(setting($key)) }}" target="_blank" rel="noopener noreferrer">{{ $label }}</a>@endif
                    @endforeach
                </div>
            </article>
            @if (safe_embed(setting('map_embed_url')))
                <iframe class="mt-3 rounded w-100" title="Map of the branch location" src="{{ safe_embed(setting('map_embed_url')) }}" height="280" style="border:0" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            @endif
        </div>
        <div class="col-lg-7">
            <form class="form-card" method="POST" action="{{ route('contact.store') }}">
                @csrf
                <div class="mb-3"><label class="form-label" for="name">Name</label><input class="form-control" id="name" name="name" value="{{ old('name') }}" required></div>
                <div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}" required></div>
                <div class="mb-3"><label class="form-label" for="subject">Subject</label><input class="form-control" id="subject" name="subject" value="{{ old('subject') }}" required></div>
                <div class="mb-3"><label class="form-label" for="message">Message</label><textarea class="form-control" id="message" name="message" rows="6" required>{{ old('message') }}</textarea></div>
                <x-human-check />
                <button class="btn btn-ieee" type="submit">Send message</button>
            </form>
        </div>
    </div>
</div></section>
@endsection
