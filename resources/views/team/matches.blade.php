@extends('layouts.app')
@section('title', 'Match History')

@section('content')
@include('partials.navbar')

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="font-heading mb-0"><i class="fa-solid fa-calendar me-2"></i>Match History</h3>
        <a href="{{ route('team.matches.create') }}" class="btn btn-stadium"><i class="fa-solid fa-plus me-2"></i>Add Match Result</a>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="card card-stadium p-3">
        <table class="table table-stadium table-hover align-middle mb-0">
            <thead>
                <tr><th>Date</th><th>Opponent</th><th>Venue</th><th>Type</th><th>Score</th><th>Result</th></tr>
            </thead>
            <tbody>
                @forelse($matches as $match)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($match->match_date)->format('d M Y') }}</td>
                    <td>{{ $match->awayTeam->team_name ?? $match->opponent_name }}</td>
                    <td>{{ $match->venue ?? '-' }}</td>
                    <td><span class="badge bg-secondary">{{ ucfirst($match->match_type) }}</span></td>
                    <td>{{ $match->scoreline }}</td>
                    <td>
                        @if($match->result === 'win') <span class="badge bg-success">Win</span>
                        @elseif($match->result === 'loss') <span class="badge bg-danger">Loss</span>
                        @elseif($match->result === 'draw') <span class="badge bg-warning text-dark">Draw</span>
                        @else <span class="badge bg-secondary">No Result</span> @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No matches recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $matches->links() }}</div>
</div>

@include('partials.footer')
@endsection
