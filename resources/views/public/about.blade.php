@extends('layouts.public')
@section('title', 'About Us')
@section('content')
<header class="page-hero"><div class="container"><p class="eyebrow">About Us</p><h1>About BUP IEEE Student Branch</h1><p>{{ page_content('home.about_preview') }}</p></div></header>
<section class="section">
    <div class="container">
        <div class="row g-4">
            @foreach ([
                'about.branch' => 'About the branch',
                'about.ieee' => 'About IEEE',
                'about.vision' => 'Vision',
                'about.mission' => 'Mission',
                'about.what_we_do' => 'What we do',
                'about.why' => 'Why BUP IEEE?',
                'about.history' => 'Branch history',
            ] as $key => $heading)
                <div class="col-lg-6">
                    <article class="info-card"><div class="body">
                        <h2 class="h4">{{ $heading }}</h2>
                        @foreach (content_paragraphs(page_content($key)) as $line)
                            <p class="mb-2">{{ $line }}</p>
                        @endforeach
                    </div></article>
                </div>
            @endforeach
            <div class="col-12">
                <article class="info-card"><div class="body">
                    <h2 class="h4">Objectives</h2>
                    <ul class="mb-0">
                        @foreach (content_paragraphs(page_content('about.objectives')) as $line)
                            <li>{{ $line }}</li>
                        @endforeach
                    </ul>
                </div></article>
            </div>
        </div>
    </div>
</section>
<section class="section">
    <div class="container">
        <h2 class="mb-3">Faculty &amp; Club Leadership</h2>
        @foreach (content_paragraphs(page_content('about.leadership')) as $line)
            <p class="muted">{{ $line }}</p>
        @endforeach
        <div class="row g-4 mt-1">
            @foreach ($leadership as $person)
                <div class="col-md-6 col-lg-4">
                    <article class="person-card">
                        <div class="row g-0">
                            <div class="col-4">
                                @if ($person['photo'])
                                    <img src="{{ public_file_url($person['photo']) }}" alt="Photo of {{ $person['name'] }}" style="height:100%;min-height:140px">
                                @else
                                    <div class="avatar-fallback initials" style="min-height:140px;height:100%">{{ $person['placeholder'] ? '?' : strtoupper(mb_substr($person['name'], 0, 1)) }}</div>
                                @endif
                            </div>
                            <div class="col-8"><div class="body">
                                <h3 class="h5 mb-1">{{ $person['name'] }}</h3>
                                <p class="mb-1"><strong>{{ $person['designation'] }}</strong></p>
                                @if (($person['role_label'] ?? '') !== '' && $person['role_label'] !== $person['designation'])
                                    <p class="muted small mb-1">{{ $person['role_label'] }}</p>
                                @endif
                                @if (! empty($person['department']))
                                    <p class="muted small mb-1">{{ $person['department'] }}</p>
                                @endif
                                @if ($person['bio'])<p class="small">{{ $person['bio'] }}</p>@endif
                                @if ($person['email'] || $person['phone'])
                                    <p class="small mb-0">
                                        @if ($person['email'])<span>{{ $person['email'] }}</span>@endif
                                        @if ($person['email'] && $person['phone'])<span> · </span>@endif
                                        @if ($person['phone'])<span>{{ $person['phone'] }}</span>@endif
                                    </p>
                                @endif
                            </div></div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
