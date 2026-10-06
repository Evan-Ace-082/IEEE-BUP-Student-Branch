@extends('layouts.public')
@section('title', 'Forbidden')
@section('content')
<section class="section"><div class="container col-lg-7"><div class="form-card"><h1 class="h3">403 — You cannot open this page</h1><p>Your account does not have permission for this area.</p><a href="{{ route('home') }}">Back to the homepage</a></div></div></section>
@endsection
