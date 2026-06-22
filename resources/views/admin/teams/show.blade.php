@extends('layouts.admin')
@section('title', 'Team Details')

@section('admin-content')
<a href="{{ route('admin.teams.index') }}" class="btn btn-link mb-3">&larr; Back to Teams</a>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card card-stadium p-4 text-center">
            <img src="{{ $team->logo_url }}" class="rounded-circle mx-auto mb-3" width="120" height="120" style="object-fit:cover;">
            <h4 class="fw-bold">{{ $team->team_name }}</h4>
            <p class="text-muted">{{ $team->captain_name }}</p>

            @if($team->status === 'pending') <span class="badge bg-warning text-dark">Pending Approval</span>
            @elseif($team->status === 'approved') <span class="badge bg-success">Approved</span>
            @else <span class="badge bg-danger">Rejected</span> @endif

            @if($team->status === 'pending')
            <div class="d-flex gap-2 mt-3">
                <form action="{{ route('admin.teams.approve', $team) }}" method="POST" class="flex-fill">
                    @csrf<button class="btn btn-success w-100">Approve</button>
                </form>
                <button class="btn btn-danger flex-fill" data-bs-toggle="modal" data-bs-target="#rejectTeamModal">Reject</button>
            </div>
            @endif

            @if($team->payment_slip)
            <a href="{{ asset('storage/' . $team->payment_slip) }}" target="_blank" class="btn btn-outline-secondary btn-sm mt-3">
                <i class="fa-solid fa-file me-1"></i>View Payment Slip
            </a>
            @endif
        </div>
    </div>

    <div class="col-md-8">
        <div class="card card-stadium p-4 mb-4">
            <h5 class="font-heading mb-3">Team Information</h5>
            <table class="table table-borderless mb-0">
                <tr><th width="180">Email</th><td>{{ $team->email }}</td></tr>
                <tr><th>Phone</th><td>{{ $team->phone }}</td></tr>
                <tr><th>Address</th><td>{{ $team->address }}</td></tr>
                <tr><th>Players Drafted</th><td>{{ $team->total_players_drafted }} / 17</td></tr>
            </table>
        </div>

        <div class="card card-stadium p-4">
            <h5 class="font-heading mb-3">Squad ({{ $team->players->count() }})</h5>
            <table class="table table-stadium mb-0">
                <thead><tr><th>Name</th><th>Role</th><th>Category</th></tr></thead>
                <tbody>
                    @forelse($team->players as $player)
                    <tr><td>{{ $player->name }}</td><td>{{ $player->role_display }}</td><td>{{ $player->category->name ?? '-' }}</td></tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted">No players drafted yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectTeamModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.teams.reject', $team) }}" method="POST">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Reject Team</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <label class="form-label">Reason for rejection</label>
                    <textarea name="reason" class="form-control" rows="3" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger">Reject Team</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
