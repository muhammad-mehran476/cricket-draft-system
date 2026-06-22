@extends('layouts.app')
@section('title', 'Home')

@section('content')
@include('partials.navbar')

<header class="hero-stadium text-center">
    <div class="container">
        <h1 class="display-3 font-heading fw-bold mb-3">Cricket Drafting Ceremony</h1>
        <p class="lead mb-4">Register as a Player or a Team and be part of the most transparent live drafting experience.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ route('register') }}?role=player" class="btn btn-gold btn-lg px-4"><i class="fa-solid fa-person-running me-2"></i>Register as Player</a>
            <a href="{{ route('register') }}?role=team_captain" class="btn btn-outline-light btn-lg px-4"><i class="fa-solid fa-people-group me-2"></i>Register a Team</a>
        </div>

        @if($activeDraft)
        <div class="alert alert-warning mt-5 d-inline-block px-4 py-2">
            <i class="fa-solid fa-circle-dot me-2 text-danger"></i> Live Draft in progress: <strong>{{ $activeDraft->title }}</strong>
            — currently drafting <strong>{{ $activeDraft->currentCategory->name ?? '-' }}</strong>
        </div>
        @endif
    </div>
</header>

<section class="py-5 bg-white">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-md-4">
                <div class="stat-card green">
                    <i class="fa-solid fa-person-running fa-2x mb-2"></i>
                    <h2 class="fw-bold">{{ $stats['total_players'] }}</h2>
                    <p class="mb-0">Approved Players</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card gold">
                    <i class="fa-solid fa-shield-halved fa-2x mb-2"></i>
                    <h2 class="fw-bold">{{ $stats['total_teams'] }}</h2>
                    <p class="mb-0">Registered Teams</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card navy">
                    <i class="fa-solid fa-layer-group fa-2x mb-2"></i>
                    <h2 class="fw-bold">{{ $stats['categories'] }}</h2>
                    <p class="mb-0">Draft Categories</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <h2 class="font-heading text-center mb-4">Participating Teams</h2>
        <div class="row g-4">
            @forelse($teams as $team)
            <div class="col-md-3 col-6">
                <div class="card card-stadium text-center p-3 h-100">
                    <img src="{{ $team->logo_url }}" class="rounded-circle mx-auto mb-2" width="70" height="70" style="object-fit:cover;">
                    <h6 class="fw-bold mb-0">{{ $team->team_name }}</h6>
                    <small class="text-muted">{{ $team->captain_name }}</small>
                </div>
            </div>
            @empty
            <p class="text-center text-muted">No teams approved yet. Be the first to register!</p>
            @endforelse
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <h2 class="font-heading text-center mb-4">How the Draft Works</h2>
        <div class="row g-4">
            <div class="col-md-3 text-center">
                <i class="fa-solid fa-user-plus fa-2x text-success mb-2"></i>
                <h6 class="fw-bold">1. Register</h6>
                <p class="small text-muted">Players & teams sign up and submit documents.</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fa-solid fa-circle-check fa-2x text-success mb-2"></i>
                <h6 class="fw-bold">2. Get Approved</h6>
                <p class="small text-muted">Admin reviews and approves registrations.</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fa-solid fa-tags fa-2x text-success mb-2"></i>
                <h6 class="fw-bold">3. Categorization</h6>
                <p class="small text-muted">Players are assigned to draft categories.</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fa-solid fa-gavel fa-2x text-success mb-2"></i>
                <h6 class="fw-bold">4. Live Draft</h6>
                <p class="small text-muted">Teams pick players live, in real time.</p>
            </div>
        </div>
    </div>
</section>

@include('partials.footer')
@endsection
