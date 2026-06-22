@extends('layouts.admin')
@section('title', 'Match Details & Stats')

@section('admin-content')
<a href="{{ route('admin.stats.matches') }}" class="btn btn-link mb-3">&larr; Back to Matches</a>

<div class="card card-stadium p-4 mb-4">
    <div class="row">
        <div class="col-md-8">
            <h4 class="font-heading">
                {{ $match->homeTeam->team_name }} vs
                {{ $match->awayTeam?->team_name ?? $match->opponent_name }}
            </h4>
            <p class="text-muted mb-1">
                {{ \Carbon\Carbon::parse($match->match_date)->format('d M Y') }}
                @if($match->venue) &nbsp;|&nbsp; {{ $match->venue }} @endif
                &nbsp;|&nbsp; <span class="badge bg-secondary">{{ ucfirst($match->match_type) }}</span>
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <h3 class="fw-bold mb-0">{{ $match->scoreline }}</h3>
            @php
                $colors = ['win'=>'success','loss'=>'danger','draw'=>'warning','no_result'=>'secondary'];
                $color  = $colors[$match->result] ?? 'secondary';
            @endphp
            <span class="badge bg-{{ $color }} {{ $match->result === 'draw' ? 'text-dark' : '' }} mt-1">
                {{ ucfirst(str_replace('_',' ', $match->result ?? '-')) }}
            </span>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-7">
        <div class="card card-stadium p-3">
            <h6 class="font-heading mb-3">Player Stats for this Match</h6>
            <table class="table table-stadium table-hover mb-0 small">
                <thead>
                    <tr>
                        <th>Player</th><th>R</th><th>B</th><th>4s</th><th>6s</th>
                        <th>W</th><th>Ov</th><th>Ct</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($match->playerStats as $stat)
                    <tr>
                        <td class="fw-semibold">{{ $stat->player->name }}</td>
                        <td>{{ $stat->runs_scored }}{{ $stat->is_not_out ? '*' : '' }}</td>
                        <td>{{ $stat->balls_faced }}</td>
                        <td>{{ $stat->fours }}</td>
                        <td>{{ $stat->sixes }}</td>
                        <td>{{ $stat->wickets_taken }}</td>
                        <td>{{ $stat->overs_bowled }}</td>
                        <td>{{ $stat->catches }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-3">
                            No player stats recorded yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card card-stadium p-3">
            <h6 class="font-heading mb-3">Add / Update Player Stat</h6>

            @if(session('success'))
                <div class="alert alert-success py-2">{{ session('success') }}</div>
            @endif

            <form action="{{ route('admin.stats.store-player-stat', $match) }}" method="POST">
                @csrf
                <div class="mb-2">
                    <label class="form-label small">Player</label>
                    <select name="player_id" class="form-select form-select-sm" required>
                        <option value="">Select player</option>
                        @foreach($availablePlayers as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label small">Runs</label>
                        <input type="number" name="runs_scored" class="form-control form-control-sm" value="0" min="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label small">Balls</label>
                        <input type="number" name="balls_faced" class="form-control form-control-sm" value="0" min="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label small">Fours</label>
                        <input type="number" name="fours" class="form-control form-control-sm" value="0" min="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label small">Sixes</label>
                        <input type="number" name="sixes" class="form-control form-control-sm" value="0" min="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label small">Wickets</label>
                        <input type="number" name="wickets_taken" class="form-control form-control-sm" value="0" min="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label small">Overs Bowled</label>
                        <input type="number" step="0.1" name="overs_bowled" class="form-control form-control-sm" value="0" min="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label small">Runs Conceded</label>
                        <input type="number" name="runs_conceded" class="form-control form-control-sm" value="0" min="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label small">Catches</label>
                        <input type="number" name="catches" class="form-control form-control-sm" value="0" min="0">
                    </div>
                </div>
                <div class="form-check mt-2">
                    <input type="checkbox" name="is_not_out" class="form-check-input" value="1" id="isNotOut">
                    <label class="form-check-label small" for="isNotOut">Not Out</label>
                </div>
                <button type="submit" class="btn btn-stadium w-100 mt-3 btn-sm">Save Stats</button>
            </form>
        </div>
    </div>
</div>
@endsection
