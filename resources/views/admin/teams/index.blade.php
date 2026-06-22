@extends('layouts.admin')
@section('title', 'Manage Teams')

@section('admin-content')
<h3 class="font-heading mb-4">Manage Teams</h3>

<div class="card card-stadium p-3 mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-4"><input type="text" name="search" class="form-control" placeholder="Search team name..." value="{{ request('search') }}"></div>
        <div class="col-md-4">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                @foreach(['pending','approved','rejected'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4"><button class="btn btn-stadium w-100">Filter</button></div>
    </form>
</div>

<div class="card card-stadium p-3">
    <table class="table table-stadium table-hover align-middle mb-0">
        <thead><tr><th>Logo</th><th>Team</th><th>Captain</th><th>Players</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse($teams as $team)
            <tr>
                <td><img src="{{ $team->logo_url }}" width="40" height="40" class="rounded-circle" style="object-fit:cover;"></td>
                <td class="fw-semibold"><a href="{{ route('admin.teams.show', $team) }}">{{ $team->team_name }}</a></td>
                <td>{{ $team->captain_name }}</td>
                <td>{{ $team->players_count }}/17</td>
                <td>
                    @if($team->status === 'pending') <span class="badge bg-warning text-dark">Pending</span>
                    @elseif($team->status === 'approved') <span class="badge bg-success">Approved</span>
                    @else <span class="badge bg-danger">Rejected</span> @endif
                </td>
                <td>
                    <div class="d-flex gap-1">
                        @if($team->status === 'pending')
                        <form action="{{ route('admin.teams.approve', $team) }}" method="POST">
    @csrf
    <button class="btn btn-success btn-sm" title="Approve"><i class="fa-solid fa-check"></i></button>
</form>
                        @endif

                        <a href="{{ route('admin.teams.show', $team) }}" class="btn btn-outline-secondary btn-sm" title="View"><i class="fa-solid fa-eye"></i></a>

                        <a href="{{ route('admin.teams.edit', $team) }}" class="btn btn-outline-primary btn-sm" title="Edit"><i class="fa-solid fa-pen"></i></a>

                        <form action="{{ route('admin.teams.destroy', $team) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete {{ $team->team_name }}? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm" title="Delete"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-muted py-4">No teams found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $teams->links() }}</div>
@endsection