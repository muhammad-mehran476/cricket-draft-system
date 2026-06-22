@extends('layouts.app')
@section('title', 'Leaderboard')

@section('content')
@include('partials.navbar')

<div class="container py-5">
    <h2 class="font-heading text-center mb-4">Tournament Leaderboard</h2>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card card-stadium p-3">
                <h5 class="font-heading mb-3"><i class="fa-solid fa-baseball-bat-ball me-2"></i>Top Run Scorers</h5>
                <table class="table table-stadium mb-0">
                    <thead><tr><th>#</th><th>Player</th><th>Team</th><th>Runs</th></tr></thead>
                    <tbody>
                        @forelse($topBatsmen as $i => $p)
                        <tr><td>{{ $i+1 }}</td><td>{{ $p->name }}</td><td>{{ $p->team->team_name ?? '-' }}</td><td class="fw-bold">{{ $p->stats->sum('runs_scored') }}</td></tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted">No data yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-stadium p-3">
                <h5 class="font-heading mb-3"><i class="fa-solid fa-bullseye me-2"></i>Top Wicket Takers</h5>
                <table class="table table-stadium mb-0">
                    <thead><tr><th>#</th><th>Player</th><th>Team</th><th>Wickets</th></tr></thead>
                    <tbody>
                        @forelse($topBowlers as $i => $p)
                        <tr><td>{{ $i+1 }}</td><td>{{ $p->name }}</td><td>{{ $p->team->team_name ?? '-' }}</td><td class="fw-bold">{{ $p->stats->sum('wickets_taken') }}</td></tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted">No data yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card card-stadium p-3">
        <h5 class="font-heading mb-3"><i class="fa-solid fa-shield-halved me-2"></i>Team Standings</h5>
        <table class="table table-stadium mb-0">
            <thead><tr><th>Team</th><th>Squad Size</th></tr></thead>
            <tbody>
                @foreach($teams as $t)
                <tr><td>{{ $t->team_name }}</td><td>{{ $t->players_count }}/17</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@include('partials.footer')
@endsection
