@extends('layouts.admin')
@section('title', 'Stats Leaderboard')

@push('styles')
<style>
    .rank-gold   { color:#d4a017; font-weight:800; }
    .rank-silver { color:#adb5bd; font-weight:700; }
    .rank-bronze { color:#cd7f32; font-weight:700; }
</style>
@endpush

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0">Player Stats Leaderboard</h3>
    <a href="{{ route('admin.stats.matches') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-calendar me-2"></i>All Matches
    </a>
</div>

<div class="row g-4">
    {{-- Top Batsmen --}}
    <div class="col-md-6">
        <div class="card card-stadium p-3">
            <h5 class="font-heading mb-3">
                <i class="fa-solid fa-baseball-bat-ball me-2 text-success"></i>Top Run Scorers
            </h5>
            <table class="table table-stadium table-hover mb-0">
                <thead>
                    <tr><th>#</th><th>Player</th><th>Team</th><th>Runs</th><th>Avg</th><th>6s</th></tr>
                </thead>
                <tbody>
                    @forelse($topBatsmen as $i => $b)
                    <tr>
                        <td class="{{ $i === 0 ? 'rank-gold' : ($i === 1 ? 'rank-silver' : ($i === 2 ? 'rank-bronze' : '')) }}">
                            {{ $i + 1 }}
                        </td>
                        <td class="fw-semibold">{{ $b['name'] }}</td>
                        <td><small class="text-muted">{{ $b['team'] ?? '-' }}</small></td>
                        <td class="fw-bold text-success">{{ $b['runs'] }}</td>
                        <td>{{ $b['avg'] }}</td>
                        <td>{{ $b['sixes'] }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-3">No batting data yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top Bowlers --}}
    <div class="col-md-6">
        <div class="card card-stadium p-3">
            <h5 class="font-heading mb-3">
                <i class="fa-solid fa-bullseye me-2 text-danger"></i>Top Wicket Takers
            </h5>
            <table class="table table-stadium table-hover mb-0">
                <thead>
                    <tr><th>#</th><th>Player</th><th>Team</th><th>Wkts</th><th>Avg</th><th>Econ</th></tr>
                </thead>
                <tbody>
                    @forelse($topBowlers as $i => $b)
                    <tr>
                        <td class="{{ $i === 0 ? 'rank-gold' : ($i === 1 ? 'rank-silver' : ($i === 2 ? 'rank-bronze' : '')) }}">
                            {{ $i + 1 }}
                        </td>
                        <td class="fw-semibold">{{ $b['name'] }}</td>
                        <td><small class="text-muted">{{ $b['team'] ?? '-' }}</small></td>
                        <td class="fw-bold text-danger">{{ $b['wickets'] }}</td>
                        <td>{{ $b['avg'] }}</td>
                        <td>{{ $b['econ'] }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-3">No bowling data yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
