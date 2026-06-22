@extends('layouts.app')
@section('title', 'Draft Room')

@section('content')
@include('partials.navbar')

<div class="container py-5 text-center">
    <i class="fa-solid fa-hourglass-half fa-3x text-muted mb-3"></i>
    <h3 class="font-heading">No Active Draft Session</h3>
    <p class="text-muted">The admin has not started a draft session yet. Please check back later.</p>
    <a href="{{ route('team.dashboard') }}" class="btn btn-stadium mt-3">Back to Dashboard</a>
</div>

@include('partials.footer')
@endsection
