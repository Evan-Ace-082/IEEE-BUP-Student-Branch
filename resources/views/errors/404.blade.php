@extends('layouts.public')
@section('title', 'Not found')
@section('content')
<section class="section"><div class="container col-lg-7"><div class="form-card"><h1 class="h3">404 — Page not found</h1><p>That page is not on the branch website.</p><a href="{{ route('home') }}">Back to the homepage</a></div></div></section>
@endsection
