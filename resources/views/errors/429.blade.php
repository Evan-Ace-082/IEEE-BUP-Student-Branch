@extends('layouts.public')
@section('title', 'Too many requests')
@section('content')
<section class="section"><div class="container col-lg-7"><div class="form-card"><h1 class="h3">429 — Slow down</h1><p>Too many requests were sent in a short time. Wait a moment and try again.</p><a href="{{ route('home') }}">Back to the homepage</a></div></div></section>
@endsection
