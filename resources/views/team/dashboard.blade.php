@extends('layouts.app')
@section('title', 'Team Dashboard')

@section('content')
@include('partials.navbar')

<div class="container py-5">
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card card-stadium text-center p-3">
                <img src="{{ $team->logo_url }}" class="rounded-circle mx-auto mb-2" width="90" height="90" style="object-fit:cover;">
                <h5 class="fw-bold mb-0">{{ $team->team_name }}</h5>
                <small class="text-muted">{{ $team->captain_name }}</small>
                <span class="badge mt-2 {{ $team->status === 'approved' ? 'bg-success' : ($team->status === 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                    {{ ucfirst($team->status) }}
                </span>
            </div>
        </div>
        <div class="col-md-3"><div class="stat-card green"><h2 class="fw-bold">{{ $team->total_players_drafted }}</h2><p class="mb-0">Players Drafted</p></div></div>
        <div class="col-md-3"><div class="stat-card gold"><h2 class="fw-bold">{{ $matchRecord['wins'] }}</h2><p class="mb-0">Match Wins</p></div></div>
        <div class="col-md-3"><div class="stat-card navy"><h2 class="fw-bold">{{ $matchRecord['losses'] }}</h2><p class="mb-0">Match Losses</p></div></div>
    </div>

    @if($team->status !== 'approved')
        <div class="alert alert-warning">
            <i class="fa-solid fa-clock me-2"></i>Your team registration is <strong>{{ $team->status }}</strong>.
            @if($team->status === 'rejected') Reason: {{ $team->rejection_reason }} @endif
        </div>
    @endif

    @if($draftSession && $team->status === 'approved')
        <div class="alert alert-info d-flex justify-content-between align-items-center">
            <span><i class="fa-solid fa-circle-dot text-danger me-2"></i>Live draft in progress!</span>
            <a href="{{ route('team.draft-room') }}" class="btn btn-stadium btn-sm">Enter Draft Room</a>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-md-7">
            <div class="card card-stadium p-4">
                <h5 class="font-heading mb-3">Drafted Players by Category</h5>
                @forelse($categoryBreakdown as $catName => $players)
                    <h6 class="mt-2">{{ $catName ?? 'Uncategorized' }} <span class="badge bg-secondary">{{ $players->count() }}</span></h6>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @foreach($players as $p)
                            <span class="badge bg-light text-dark border">{{ $p->name }} ({{ $p->role_display }})</span>
                        @endforeach
                    </div>
                @empty
                    <p class="text-muted">No players drafted yet.</p>
                @endforelse
            </div>
        </div>
        <div class="col-md-5">
            <div class="card card-stadium p-4">
                <h5 class="font-heading mb-3">Quick Actions</h5>
                <a href="{{ route('team.players') }}" class="btn btn-outline-secondary w-100 mb-2"><i class="fa-solid fa-list me-2"></i>View My Squad</a>
                <a href="{{ route('team.matches') }}" class="btn btn-outline-secondary w-100 mb-2"><i class="fa-solid fa-calendar me-2"></i>Match History</a>
                <a href="{{ route('team.matches.create') }}" class="btn btn-outline-secondary w-100"><i class="fa-solid fa-plus me-2"></i>Add Match Result</a>
            </div>
        </div>
    </div>
</div>

@include('partials.footer')
@endsection
