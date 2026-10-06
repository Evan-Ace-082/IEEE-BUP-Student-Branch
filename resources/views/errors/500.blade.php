@extends('layouts.public')
@section('title', 'Something went wrong')
@section('content')
<section class="section"><div class="container col-lg-7"><div class="form-card"><h1 class="h3">500 — Something went wrong</h1><p>The branch website hit an unexpected problem. Please try again shortly.</p><a href="{{ route('home') }}">Back to the homepage</a></div></div></section>
@endsection
