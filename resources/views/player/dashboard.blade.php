@extends('layouts.app')
@section('title', 'Player Dashboard')

@section('content')
@include('partials.navbar')

<div class="container py-5">
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card card-stadium text-center p-4">
                <img src="{{ $player->profile_picture_url }}" class="rounded-circle mx-auto mb-3" width="120" height="120" style="object-fit:cover;">
                <h4 class="fw-bold mb-0">{{ $player->name }}</h4>
                <p class="text-muted mb-2">{{ $player->role_display }}</p>
                <span class="badge {{ $player->skill_badge_class }} mb-2">{{ ucfirst($player->skill_level) }}</span>

                @if($player->status === 'pending')
                    <div class="alert alert-warning mt-2 mb-0"><i class="fa-solid fa-clock me-1"></i>Pending Admin Approval</div>
                @elseif($player->status === 'approved')
                    <div class="alert alert-success mt-2 mb-0"><i class="fa-solid fa-check-circle me-1"></i>Approved — Awaiting Draft</div>
                @elseif($player->status === 'drafted')
                    <div class="alert alert-info mt-2 mb-0"><i class="fa-solid fa-trophy me-1"></i>Drafted by {{ $player->team->team_name }}</div>
                @elseif($player->status === 'rejected')
                    <div class="alert alert-danger mt-2 mb-0">
                        <i class="fa-solid fa-circle-xmark me-1"></i>Rejected<br>
                        <small>{{ $player->rejection_reason }}</small>
                    </div>
                @endif

                <a href="{{ route('player.profile.edit') }}" class="btn btn-outline-secondary btn-sm mt-3">
                    <i class="fa-solid fa-pen me-1"></i>Edit Profile
                </a>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card card-stadium p-4 mb-4">
                <h5 class="font-heading mb-3">Player Information</h5>
                <table class="table table-borderless mb-0">
                    <tr><th width="180">Category</th><td>{{ $player->category->name ?? 'Not assigned yet' }}</td></tr>
                    <tr><th>City</th><td>{{ $player->city }}</td></tr>
                    <tr><th>Phone</th><td>{{ $player->phone }}</td></tr>
                    <tr><th>Bowling Type</th><td>{{ ucfirst($player->bowling_type) }}</td></tr>
                    <tr><th>Batting Style</th><td>{{ $player->batting_style === 'right_hand' ? 'Right Handed' : 'Left Handed' }}</td></tr>
                    @if($player->team)
                    <tr><th>Team</th><td><span class="badge bg-success">{{ $player->team->team_name }}</span></td></tr>
                    @endif
                </table>
            </div>

            @if($player->stats->count())
            <div class="card card-stadium p-4">
                <h5 class="font-heading mb-3">Performance Summary</h5>
                <div class="row text-center g-3">
                    <div class="col-3"><h4 class="fw-bold text-success">{{ $player->stats->sum('runs_scored') }}</h4><small>Total Runs</small></div>
                    <div class="col-3"><h4 class="fw-bold text-success">{{ $player->stats->sum('wickets_taken') }}</h4><small>Wickets</small></div>
                    <div class="col-3"><h4 class="fw-bold text-success">{{ $player->batting_average }}</h4><small>Batting Avg</small></div>
                    <div class="col-3"><h4 class="fw-bold text-success">{{ $player->bowling_average }}</h4><small>Bowling Avg</small></div>
                </div>
                <a href="{{ route('player.stats') }}" class="btn btn-link mt-2">View detailed stats →</a>
            </div>
            @endif
        </div>
    </div>
</div>

@include('partials.footer')
@endsection
