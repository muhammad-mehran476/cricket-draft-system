@extends('layouts.app')
@section('title', 'My Stats')

@section('content')
@include('partials.navbar')

<div class="container py-5">
    <h3 class="font-heading mb-4"><i class="fa-solid fa-chart-line me-2"></i>My Performance Stats</h3>

    <div class="card card-stadium p-3">
        <table class="table table-stadium table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Match</th><th>Runs</th><th>Balls</th><th>4s</th><th>6s</th>
                    <th>Wickets</th><th>Overs</th><th>Catches</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats as $stat)
                <tr>
                    <td>{{ $stat->match ? \Carbon\Carbon::parse($stat->match->match_date)->format('d M Y') . ' vs ' . ($stat->match->opponent_name ?? $stat->match->awayTeam->team_name ?? '-') : '-' }}</td>
                    <td>{{ $stat->runs_scored }}{{ $stat->is_not_out ? '*' : '' }}</td>
                    <td>{{ $stat->balls_faced }}</td>
                    <td>{{ $stat->fours }}</td>
                    <td>{{ $stat->sixes }}</td>
                    <td>{{ $stat->wickets_taken }}</td>
                    <td>{{ $stat->overs_bowled }}</td>
                    <td>{{ $stat->catches }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No stats recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $stats->links() }}</div>
</div>

@include('partials.footer')
@endsection
