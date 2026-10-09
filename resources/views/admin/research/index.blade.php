@extends('layouts.dashboard')
@section('title', 'Research papers')
@section('heading', 'Research papers')
@section('content')
<form class="d-flex gap-2 mb-3" method="GET">
    <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Search title">
    <select class="form-select" name="review_status" aria-label="Review status">
        <option value="">All statuses</option>
        @foreach ($statuses as $value => $label)
            <option value="{{ $value }}" @selected(request('review_status') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <button class="btn btn-outline-secondary">Search</button>
</form>
<div class="table-card"><table class="table align-middle"><thead><tr><th>Title</th><th>Submitter</th><th>Review</th><th>Public</th><th></th></tr></thead><tbody>
@forelse ($papers as $paper)
<tr>
    <td>{{ $paper->title }}</td>
    <td>{{ $paper->submitter?->name ?? 'Removed account' }}</td>
    <td><span class="badge status-{{ $paper->review_status }}">{{ $paper->reviewLabel() }}</span></td>
    <td>{{ $paper->is_published ? 'Published' : 'Unpublished' }}</td>
    <td class="text-nowrap"><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.research-papers.show', $paper) }}">Review</a></td>
</tr>
@empty
<tr><td colspan="5" class="empty-state">No research papers have been submitted.</td></tr>
@endforelse
</tbody></table></div>
<div class="mt-3">{{ $papers->links() }}</div>
@endsection
