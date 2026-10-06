@extends('layouts.public')
@section('title', 'Page expired')
@section('content')
<section class="section"><div class="container col-lg-7"><div class="form-card"><h1 class="h3">419 — Page expired</h1><p>The form timed out. Go back and submit it again.</p><a href="{{ route('home') }}">Back to the homepage</a></div></div></section>
@endsection
