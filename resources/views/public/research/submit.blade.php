@extends('layouts.public')
@section('title', 'Submit Research Paper')
@section('content')
<header class="page-hero"><div class="container"><p class="eyebrow">Research</p><h1>Submit Research Paper</h1><p>Submissions stay in Pending Review until an administrator approves them. Approval does not publish a paper on its own.</p></div></header>
<section class="section"><div class="container col-lg-8">
    <form class="form-card" method="POST" action="{{ route('research-papers.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <div class="col-12"><label class="form-label" for="title">Paper title</label><input class="form-control" id="title" name="title" value="{{ old('title') }}" required maxlength="220"></div>
            <div class="col-12"><label class="form-label" for="authors">Author name(s)</label><input class="form-control" id="authors" name="authors" value="{{ old('authors') }}" required maxlength="500"></div>
            <div class="col-12"><label class="form-label" for="abstract">Abstract</label><textarea class="form-control" id="abstract" name="abstract" rows="6" required maxlength="10000">{{ old('abstract') }}</textarea></div>
            <div class="col-md-4"><label class="form-label" for="category">Research category</label><select class="form-select" id="category" name="category" required>@foreach ($categories as $value => $label)<option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>@endforeach</select></div>
            <div class="col-md-4"><label class="form-label" for="publication_year">Publication year</label><input class="form-control" id="publication_year" type="number" name="publication_year" min="1900" max="{{ (int) date('Y') + 1 }}" value="{{ old('publication_year') }}" required></div>
            <div class="col-md-4"><label class="form-label" for="publication_status">Publication status</label><select class="form-select" id="publication_status" name="publication_status" required>@foreach ($publicationStatuses as $value => $label)<option value="{{ $value }}" @selected(old('publication_status', 'preprint') === $value)>{{ $label }}</option>@endforeach</select></div>
            <div class="col-12"><label class="form-label" for="keywords">Keywords</label><input class="form-control" id="keywords" name="keywords" value="{{ old('keywords') }}" maxlength="500"></div>
            <div class="col-md-6"><label class="form-label" for="doi">DOI</label><input class="form-control" id="doi" name="doi" value="{{ old('doi') }}" maxlength="255"></div>
            <div class="col-md-6"><label class="form-label" for="external_url">External publication URL</label><input class="form-control" id="external_url" type="url" name="external_url" value="{{ old('external_url') }}" maxlength="255"></div>
            <div class="col-12"><label class="form-label" for="citation">Citation</label><textarea class="form-control" id="citation" name="citation" rows="2" maxlength="1000">{{ old('citation') }}</textarea></div>
            <div class="col-12"><label class="form-label" for="supplementary">Supplementary information</label><textarea class="form-control" id="supplementary" name="supplementary" rows="3" maxlength="5000">{{ old('supplementary') }}</textarea></div>
            <div class="col-12"><label class="form-label" for="pdf">Research paper PDF</label><input class="form-control" id="pdf" type="file" name="pdf" accept="application/pdf,.pdf" required><div class="form-text">PDF only, up to 8 MB.</div></div>
        </div>
        <button class="btn btn-ieee mt-3" type="submit">Submit for review</button>
    </form>
</div></section>
@endsection
