@extends('layouts.public')
@section('title', $announcement->title)
@section('content')
<header class="page-hero"><div class="container">
    <p class="eyebrow">{{ \App\Models\Announcement::categories()[$announcement->category] ?? 'Notice' }}</p>
    <h1>{{ $announcement->title }}</h1>
    <p>{{ $announcement->published_at?->format('d M Y') }} · {{ $announcement->author?->name ?? 'Branch team' }}</p>
</div></header>
<article class="section"><div class="container col-lg-8">
    @if ($announcement->featured_image)<img class="rounded mb-4 w-100" src="{{ public_file_url($announcement->featured_image) }}" alt="">@endif
    @foreach (content_paragraphs($announcement->content) as $line)<p>{{ $line }}</p>@endforeach
    <a href="{{ route('announcements.index') }}">Back to announcements</a>
</div></article>
@endsection
