@extends('layouts.dashboard')
@section('title', 'Review research paper')
@section('heading', 'Review research paper')
@section('content')
<div class="form-card mb-3">
    <p class="mb-1"><span class="badge status-{{ $paper->review_status }}">{{ $paper->reviewLabel() }}</span> @if($paper->is_published)<span class="badge status-published">Published</span>@else<span class="badge status-draft">Unpublished</span>@endif</p>
    <p class="mb-1">Submitted by {{ $paper->submitter?->name ?? 'a removed account' }} on {{ $paper->created_at?->format('d M Y') }}.</p>
    @if ($paper->reviewer)<p class="mb-1">Reviewed by {{ $paper->reviewer->name }}@if($paper->reviewed_at) on {{ $paper->reviewed_at->format('d M Y H:i') }}@endif.</p>@endif
    @if ($paper->pdf_path)<p class="mb-0"><a href="{{ route('research-papers.pdf', ['paper' => $paper->slug, 'download' => 1]) }}">Download submitted PDF</a></p>@endif
</div>
<form class="form-card mb-3" method="POST" action="{{ route('admin.research-papers.approve', $paper) }}">
    @csrf
    <label class="form-label" for="review_note">Review note</label>
    <textarea class="form-control" id="review_note" name="review_note" rows="3">{{ old('review_note', $paper->review_note) }}</textarea>
    <div class="d-flex flex-wrap gap-2 mt-3">
        <button class="btn btn-ieee" type="submit">Approve</button>
        <button class="btn btn-outline-danger" type="submit" formaction="{{ route('admin.research-papers.reject', $paper) }}">Reject</button>
    </div>
</form>
<div class="d-flex flex-wrap gap-2 mb-3">
    @if ($paper->review_status === 'approved' && ! $paper->is_published)
        <form method="POST" action="{{ route('admin.research-papers.publish', $paper) }}">@csrf<button class="btn btn-ieee">Publish</button></form>
    @endif
    @if ($paper->is_published)
        <form method="POST" action="{{ route('admin.research-papers.unpublish', $paper) }}">@csrf<button class="btn btn-outline-secondary">Unpublish</button></form>
    @endif
    <form method="POST" action="{{ route('admin.research-papers.destroy', $paper) }}" data-confirm="Remove this research paper?">@csrf @method('DELETE')<button class="btn btn-outline-danger">Delete</button></form>
</div>
<form class="form-card" method="POST" action="{{ route('admin.research-papers.update', $paper) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <h2 class="h5">Edit details</h2>
    <div class="row g-3">
        <div class="col-12"><label class="form-label" for="title">Paper title</label><input class="form-control" id="title" name="title" value="{{ old('title', $paper->title) }}" required></div>
        <div class="col-12"><label class="form-label" for="authors">Author name(s)</label><input class="form-control" id="authors" name="authors" value="{{ old('authors', $paper->authors) }}" required></div>
        <div class="col-12"><label class="form-label" for="abstract">Abstract</label><textarea class="form-control" id="abstract" name="abstract" rows="6" required>{{ old('abstract', $paper->abstract) }}</textarea></div>
        <div class="col-md-4"><label class="form-label" for="category">Research category</label><select class="form-select" id="category" name="category">@foreach ($categories as $value => $label)<option value="{{ $value }}" @selected(old('category', $paper->category) === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-4"><label class="form-label" for="publication_year">Publication year</label><input class="form-control" id="publication_year" type="number" name="publication_year" min="1900" max="{{ (int) date('Y') + 1 }}" value="{{ old('publication_year', $paper->publication_year) }}" required></div>
        <div class="col-md-4"><label class="form-label" for="publication_status">Publication status</label><select class="form-select" id="publication_status" name="publication_status">@foreach ($publicationStatuses as $value => $label)<option value="{{ $value }}" @selected(old('publication_status', $paper->publication_status) === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-12"><label class="form-label" for="keywords">Keywords</label><input class="form-control" id="keywords" name="keywords" value="{{ old('keywords', $paper->keywords) }}"></div>
        <div class="col-md-6"><label class="form-label" for="doi">DOI</label><input class="form-control" id="doi" name="doi" value="{{ old('doi', $paper->doi) }}"></div>
        <div class="col-md-6"><label class="form-label" for="external_url">External publication URL</label><input class="form-control" id="external_url" type="url" name="external_url" value="{{ old('external_url', $paper->external_url) }}"></div>
        <div class="col-12"><label class="form-label" for="citation">Citation</label><textarea class="form-control" id="citation" name="citation" rows="2">{{ old('citation', $paper->citation) }}</textarea></div>
        <div class="col-12"><label class="form-label" for="supplementary">Supplementary information</label><textarea class="form-control" id="supplementary" name="supplementary" rows="3">{{ old('supplementary', $paper->supplementary) }}</textarea></div>
        <div class="col-12"><label class="form-label" for="pdf">Replace PDF</label><input class="form-control" id="pdf" type="file" name="pdf" accept="application/pdf,.pdf"><div class="form-text">Leave empty to keep the current PDF.</div></div>
    </div>
    <button class="btn btn-ieee mt-3" type="submit">Save details</button>
</form>
@endsection
