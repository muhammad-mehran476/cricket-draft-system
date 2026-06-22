@extends('layouts.admin')
@section('title', 'User Details')

@section('admin-content')
<a href="{{ route('admin.users.index') }}" class="btn btn-link mb-3">&larr; Back to Users</a>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card card-stadium p-4 text-center">
            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto mb-3"
                 style="width:80px;height:80px;font-size:2rem;color:#fff;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
            <p class="text-muted mb-1">{{ $user->email }}</p>

            <span class="badge
                {{ $user->role === 'admin' ? 'bg-danger' : ($user->role === 'team_captain' ? 'bg-primary' : 'bg-success') }}
                mb-2">
                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
            </span>
            <br>
            <span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-warning text-dark' }}">
                {{ ucfirst($user->status) }}
            </span>

            <p class="small text-muted mt-3 mb-2">Joined: {{ $user->created_at->format('d M Y') }}</p>

            @if(!$user->isAdmin())
                <div class="d-flex gap-2 mt-2">
                    @if($user->status === 'active')
                    <form action="{{ route('admin.users.suspend', $user) }}" method="POST" class="flex-fill">
                        @csrf
                        <button class="btn btn-warning w-100 btn-sm">Suspend</button>
                    </form>
                    @else
                    <form action="{{ route('admin.users.activate', $user) }}" method="POST" class="flex-fill">
                        @csrf
                        <button class="btn btn-success w-100 btn-sm">Activate</button>
                    </form>
                    @endif
                </div>
            @endif
        </div>

        <div class="card card-stadium p-4 mt-3">
            <h6 class="font-heading mb-3">Reset Password</h6>
            <form action="{{ route('admin.users.reset-password', $user) }}" method="POST">
                @csrf
                <div class="mb-2">
                    <input type="password" name="password" class="form-control"
                           placeholder="New password" required minlength="8">
                </div>
                <div class="mb-2">
                    <input type="password" name="password_confirmation" class="form-control"
                           placeholder="Confirm password" required>
                </div>
                <button class="btn btn-outline-danger w-100 btn-sm">Reset Password</button>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        @if($user->player)
        <div class="card card-stadium p-3 mb-3">
            <h6 class="font-heading mb-2">Player Profile</h6>
            <table class="table table-borderless mb-0 small">
                <tr><th width="160">Status</th>
                    <td><span class="badge bg-{{ $user->player->status === 'approved' ? 'success' : 'warning text-dark' }}">{{ ucfirst($user->player->status) }}</span></td></tr>
                <tr><th>Role</th><td>{{ $user->player->role_display }}</td></tr>
                <tr><th>Category</th><td>{{ $user->player->category?->name ?? 'Not assigned' }}</td></tr>
                <tr><th>Team</th><td>{{ $user->player->team?->team_name ?? 'Not drafted' }}</td></tr>
            </table>
            <a href="{{ route('admin.players.show', $user->player) }}" class="btn btn-link btn-sm mt-1">Manage Player →</a>
        </div>
        @endif

        @if($user->team)
        <div class="card card-stadium p-3 mb-3">
            <h6 class="font-heading mb-2">Team Profile</h6>
            <table class="table table-borderless mb-0 small">
                <tr><th width="160">Team Name</th><td>{{ $user->team->team_name }}</td></tr>
                <tr><th>Status</th>
                    <td><span class="badge bg-{{ $user->team->status === 'approved' ? 'success' : 'warning text-dark' }}">{{ ucfirst($user->team->status) }}</span></td></tr>
                <tr><th>Players Drafted</th><td>{{ $user->team->total_players_drafted }}/17</td></tr>
            </table>
            <a href="{{ route('admin.teams.show', $user->team) }}" class="btn btn-link btn-sm mt-1">Manage Team →</a>
        </div>
        @endif

        <div class="card card-stadium p-3">
            <h6 class="font-heading mb-2">Recent Activity</h6>
            <ul class="list-group list-group-flush" style="max-height:320px;overflow-y:auto;">
                @forelse($user->auditLogs as $log)
                <li class="list-group-item px-0 py-2">
                    <div class="d-flex justify-content-between">
                        <span>{{ $log->description }}</span>
                        <small class="text-muted ms-3 text-nowrap">{{ $log->created_at->diffForHumans() }}</small>
                    </div>
                </li>
                @empty
                <li class="list-group-item px-0 text-muted">No activity recorded.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
