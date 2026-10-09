@extends('layouts.public')
@section('title', 'Research Papers')
@section('content')
<header class="page-hero"><div class="container">
    <p class="eyebrow">Research</p>
    <h1>Research Papers</h1>
    <p>The branch shares approved research so students can read published work and submit their own papers for review. A paper appears in this list only after an administrator approves and publishes it.</p>
    <a class="btn btn-ieee mt-2" href="{{ route('research-papers.create') }}">Submit Research Paper</a>
</div></header>
<section class="section">
    <div class="container">
        <form class="filter-bar row g-2 mb-4" method="GET">
            <div class="col-md-4"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Search title, author, or keyword" aria-label="Search research papers"></div>
            <div class="col-md-3">
                <select class="form-select" name="category" aria-label="Research category">
                    <option value="">All categories</option>
                    @foreach ($categories as $value => $label)
                        <option value="{{ $value }}" @selected(request('category') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="year" aria-label="Publication year">
                    <option value="">All years</option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}" @selected((string) request('year') === (string) $year)>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="publication_status" aria-label="Publication status">
                    <option value="">Any status</option>
                    @foreach ($publicationStatuses as $value => $label)
                        <option value="{{ $value }}" @selected(request('publication_status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1"><button class="btn btn-ieee w-100" type="submit">Filter</button></div>
        </form>
        @forelse ($papers as $paper)
            <article class="info-card mb-3"><div class="body">
                <h2 class="h5 mb-1">{{ $paper->title }}</h2>
                <p class="muted mb-2">{{ $paper->authors }}</p>
                <p>{{ \Illuminate\Support\Str::limit($paper->abstract, 320) }}</p>
                <p class="small mb-2">{{ $categories[$paper->category] ?? $paper->category }} · {{ $paper->publication_year }} · {{ $publicationStatuses[$paper->publication_status] ?? $paper->publication_status }}@if ($paper->published_at) · {{ $paper->published_at->format('d M Y') }}@endif</p>
                @if ($paper->keywords)<p class="small muted">{{ $paper->keywords }}</p>@endif
                <a class="btn btn-ieee btn-sm" href="{{ route('research-papers.show', $paper->slug) }}">Read Paper</a>
                @if ($paper->pdf_path)
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('research-papers.pdf', ['paper' => $paper->slug, 'download' => 1]) }}">Download PDF</a>
                @endif
            </div></article>
        @empty
            <div class="empty-state card-soft">No research papers have been published yet.</div>
        @endforelse
        <div class="mt-4">{{ $papers->links() }}</div>
    </div>
</section>
@endsection
