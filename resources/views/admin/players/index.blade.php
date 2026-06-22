@extends('layouts.admin')
@section('title', 'Manage Players')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0">Manage Players</h3>
</div>

<div class="card card-stadium p-3 mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                @foreach(['pending','approved','rejected','drafted'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-stadium w-100">Filter</button>
        </div>
    </form>
</div>

<div class="card card-stadium p-3">
    <table class="table table-stadium table-hover align-middle mb-0">
        <thead>
            <tr><th>Photo</th><th>Name</th><th>Role</th><th>Category</th><th>Skill</th><th>Status</th><th>Team</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @forelse($players as $player)
            <tr>
                <td><img src="{{ $player->profile_picture_url }}" width="40" height="40" class="rounded-circle" style="object-fit:cover;"></td>
                <td class="fw-semibold"><a href="{{ route('admin.players.show', $player) }}">{{ $player->name }}</a></td>
                <td>{{ $player->role_display }}</td>
                <td>{{ $player->category->name ?? '-' }}</td>
                <!-- inline bootstrap is used for chang of text color to black  -->
                <td>
                    <span class="badge {{ $player->skill_badge_class }} text-dark" style="color:black !important;">
                        {{ ucfirst($player->skill_level) }}
                    </span>
                </td>                <td>
                    @if($player->status === 'pending') <span class="badge bg-warning text-dark">Pending</span>
                    @elseif($player->status === 'approved') <span class="badge bg-success">Approved</span>
                    @elseif($player->status === 'drafted') <span class="badge bg-info">Drafted</span>
                    @else <span class="badge bg-danger">Rejected</span> @endif
                </td>
                <td>{{ $player->team->team_name ?? '-' }}</td>
                <td>
                    <div class="d-flex gap-1">
                        @if($player->status === 'pending')
                        <form action="{{ route('admin.players.approve', $player) }}" method="POST">
                            @csrf
                            <button class="btn btn-success btn-sm" title="Approve"><i class="fa-solid fa-check"></i></button>
                        </form>
                        @endif

                        <a href="{{ route('admin.players.show', $player) }}" class="btn btn-outline-secondary btn-sm" title="View"><i class="fa-solid fa-eye"></i></a>

                        <a href="{{ route('admin.players.edit', $player) }}" class="btn btn-outline-primary btn-sm" title="Edit"><i class="fa-solid fa-pen"></i></a>

                        <form action="{{ route('admin.players.destroy', $player) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete {{ $player->name }}? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm" title="Delete"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted py-4">No players found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $players->links() }}</div>
@endsection