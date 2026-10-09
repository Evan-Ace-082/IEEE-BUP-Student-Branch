@extends('layouts.public')
@section('title', $paper->title)
@section('content')
<header class="page-hero"><div class="container">
    <p class="eyebrow">{{ \App\Models\ResearchPaper::categories()[$paper->category] ?? 'Research' }}</p>
    <h1>{{ $paper->title }}</h1>
    <p>{{ $paper->authors }}</p>
</div></header>
<article class="section"><div class="container col-lg-8">
    <p class="small muted">{{ \App\Models\ResearchPaper::publicationStatuses()[$paper->publication_status] ?? $paper->publication_status }} · {{ $paper->publication_year }}@if ($paper->published_at) · Published {{ $paper->published_at->format('d M Y') }}@endif</p>
    <h2 class="h5">Abstract</h2>
    @foreach (content_paragraphs($paper->abstract) as $line)<p>{{ $line }}</p>@endforeach
    @if ($paper->keywords)<p><strong>Keywords:</strong> {{ $paper->keywords }}</p>@endif
    @if ($paper->citation)<p><strong>Citation:</strong> {{ $paper->citation }}</p>@endif
    @if ($paper->doiUrl())<p><strong>DOI:</strong> <a href="{{ $paper->doiUrl() }}" target="_blank" rel="noopener noreferrer">{{ $paper->doi }}</a></p>@elseif ($paper->doi)<p><strong>DOI:</strong> {{ $paper->doi }}</p>@endif
    @if (safe_url($paper->external_url))<p><a href="{{ safe_url($paper->external_url) }}" target="_blank" rel="noopener noreferrer">External publication</a></p>@endif
    @if ($paper->supplementary)
        <h2 class="h5">Supplementary information</h2>
        @foreach (content_paragraphs($paper->supplementary) as $line)<p>{{ $line }}</p>@endforeach
    @endif
    @if (auth()->check() && (auth()->id() === $paper->submitted_by || $canReview) && $paper->review_note)
        <p class="small"><strong>Review note:</strong> {{ $paper->review_note }}</p>
    @endif
    @if ($paper->pdf_path)
        <p class="mt-3"><a class="btn btn-ieee" href="{{ route('research-papers.pdf', ['paper' => $paper->slug, 'download' => 1]) }}">Download PDF</a></p>
        <object data="{{ route('research-papers.pdf', $paper->slug) }}" type="application/pdf" width="100%" height="480" class="mt-3">
            <a href="{{ route('research-papers.pdf', ['paper' => $paper->slug, 'download' => 1]) }}">Download PDF</a>
        </object>
    @endif
    <p class="mt-4"><a href="{{ route('research-papers.index') }}">Back to research papers</a></p>
</div></article>
@endsection
