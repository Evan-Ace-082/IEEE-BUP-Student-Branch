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
@endsection
