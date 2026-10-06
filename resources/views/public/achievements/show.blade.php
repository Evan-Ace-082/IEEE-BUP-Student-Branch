@extends('layouts.public')
@section('title', $achievement->title)
@section('content')
<header class="page-hero"><div class="container">
    <p class="eyebrow">{{ \App\Models\Achievement::categories()[$achievement->category] ?? 'Achievement' }}</p>
    <h1>{{ $achievement->title }}</h1>
    <p>{{ $achievement->person_or_team }} @if($achievement->achieved_on) · {{ $achievement->achieved_on->format('d M Y') }} @endif</p>
</div></header>
<article class="section"><div class="container col-lg-8">
    @if ($achievement->image)<img class="rounded mb-4" src="{{ public_file_url($achievement->image) }}" alt="">@endif
    @foreach (content_paragraphs($achievement->description) as $line)<p>{{ $line }}</p>@endforeach
    @if (safe_url($achievement->link))<p><a href="{{ safe_url($achievement->link) }}" target="_blank" rel="noopener noreferrer">Supporting link</a></p>@endif
</div></article>
@endsection
