@extends('layouts.admin')
@section('title', 'All Matches')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0">All Match Records</h3>
    <a href="{{ route('admin.stats.leaderboard') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-chart-line me-2"></i>Leaderboard
    </a>
</div>

<div class="card card-stadium p-3">
    <table class="table table-stadium table-hover align-middle mb-0">
        <thead>
            <tr>
                <th>Date</th><th>Home Team</th><th>Opponent</th>
                <th>Type</th><th>Score</th><th>Result</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($matches as $match)
            <tr>
                <td>{{ \Carbon\Carbon::parse($match->match_date)->format('d M Y') }}</td>
                <td class="fw-semibold">{{ $match->homeTeam->team_name }}</td>
                <td>{{ $match->awayTeam?->team_name ?? $match->opponent_name }}</td>
                <td><span class="badge bg-secondary">{{ ucfirst($match->match_type) }}</span></td>
                <td>{{ $match->scoreline }}</td>
                <td>
                    @php
                        $colors = ['win'=>'success','loss'=>'danger','draw'=>'warning','no_result'=>'secondary'];
                        $color  = $colors[$match->result] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $color }} {{ $match->result === 'draw' ? 'text-dark' : '' }}">
                        {{ ucfirst(str_replace('_',' ',$match->result ?? '-')) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.stats.match-show', $match) }}"
                       class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-eye"></i> Stats
                    </a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted py-4">No matches recorded yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $matches->links() }}</div>
@endsection
