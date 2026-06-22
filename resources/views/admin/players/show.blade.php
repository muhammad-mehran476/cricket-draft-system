@extends('layouts.admin')
@section('title', 'Player Details')

@section('admin-content')
<a href="{{ route('admin.players.index') }}" class="btn btn-link mb-3">&larr; Back to Players</a>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card card-stadium p-4 text-center">
            <img src="{{ $player->profile_picture_url }}" class="rounded-circle mx-auto mb-3" width="120" height="120" style="object-fit:cover;">
            <h4 class="fw-bold">{{ $player->name }}</h4>
            <p class="text-muted">{{ $player->role_display }}</p>
            <span class="badge {{ $player->skill_badge_class }} mb-2">{{ ucfirst($player->skill_level) }}</span><br>

            @if($player->status === 'pending')
                <span class="badge bg-warning text-dark">Pending Approval</span>
            @elseif($player->status === 'approved')
                <span class="badge bg-success">Approved</span>
            @elseif($player->status === 'drafted')
                <span class="badge bg-info">Drafted by {{ $player->team->team_name }}</span>
            @else
                <span class="badge bg-danger">Rejected</span>
            @endif

            @if($player->status === 'pending')
            <div class="d-flex gap-2 mt-3">
                <form action="{{ route('admin.players.approve', $player) }}" method="POST" class="flex-fill">
                    @csrf<button class="btn btn-success w-100">Approve</button>
                </form>
                <button class="btn btn-danger flex-fill" data-bs-toggle="modal" data-bs-target="#rejectModal">Reject</button>
            </div>
            @endif

            @if($player->payment_slip)
            <a href="{{ asset('storage/' . $player->payment_slip) }}" target="_blank" class="btn btn-outline-secondary btn-sm mt-2">
                <i class="fa-solid fa-file me-1"></i>View Payment Slip
            </a>
            @endif
        </div>
    </div>

    <div class="col-md-8">
        <div class="card card-stadium p-4 mb-4">
            <h5 class="font-heading mb-3">Player Details</h5>
            <table class="table table-borderless mb-0">
                <tr><th width="180">Email</th><td>{{ $player->email }}</td></tr>
                <tr><th>Phone</th><td>{{ $player->phone }}</td></tr>
                <tr><th>City</th><td>{{ $player->city }}</td></tr>
                <tr><th>Address</th><td>{{ $player->address }}</td></tr>
                <tr><th>Bowling Type</th><td>{{ ucfirst($player->bowling_type) }}</td></tr>
                <tr><th>Batting Style</th><td>{{ $player->batting_style === 'right_hand' ? 'Right Handed' : 'Left Handed' }}</td></tr>
            </table>
        </div>

        <div class="card card-stadium p-4">
            <h5 class="font-heading mb-3">Assign Draft Category</h5>
            <form action="{{ route('admin.players.category', $player) }}" method="POST" class="d-flex gap-2">
                @csrf
                <select name="category_id" class="form-select">
                    <option value="">Select category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $player->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <button class="btn btn-stadium">Assign</button>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.players.reject', $player) }}" method="POST">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Reject Player</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <label class="form-label">Reason for rejection</label>
                    <textarea name="reason" class="form-control" rows="3" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger">Reject Player</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
