@extends('layouts.app')
@section('title', 'My Squad')

@section('content')
@include('partials.navbar')

<div class="container py-5">
    <h3 class="font-heading mb-4"><i class="fa-solid fa-people-group me-2"></i>My Squad ({{ $team->players->count() }})</h3>

    <div class="card card-stadium p-3">
        <table class="table table-stadium table-hover align-middle mb-0">
            <thead>
                <tr><th>Photo</th><th>Name</th><th>Role</th><th>Category</th><th>Skill</th><th>Runs</th><th>Wickets</th></tr>
            </thead>
            <tbody>
                @forelse($team->players as $player)
                <tr>
                    <td><img src="{{ $player->profile_picture_url }}" width="40" height="40" class="rounded-circle" style="object-fit:cover;"></td>
                    <td class="fw-semibold">{{ $player->name }}</td>
                    <td>{{ $player->role_display }}</td>
                    <td>{{ $player->category->name ?? '-' }}</td>
                    <td><span class="badge {{ $player->skill_badge_class }}">{{ ucfirst($player->skill_level) }}</span></td>
                    <td>{{ $player->stats->sum('runs_scored') }}</td>
                    <td>{{ $player->stats->sum('wickets_taken') }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No players drafted yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@include('partials.footer')
@endsection
