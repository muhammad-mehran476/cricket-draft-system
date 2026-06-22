@extends('layouts.admin')
@section('title', 'Manage Users')

@section('admin-content')
<h3 class="font-heading mb-4">All Users</h3>

<div class="card card-stadium p-3 mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control"
                   placeholder="Name or email..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="role" class="form-select">
                <option value="">All Roles</option>
                <option value="admin"        {{ request('role') === 'admin'        ? 'selected' : '' }}>Admin</option>
                <option value="player"       {{ request('role') === 'player'       ? 'selected' : '' }}>Player</option>
                <option value="team_captain" {{ request('role') === 'team_captain' ? 'selected' : '' }}>Team Captain</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Active</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-stadium w-100">Filter</button>
        </div>
    </form>
</div>

<div class="card card-stadium p-3">
    <table class="table table-stadium table-hover align-middle mb-0">
        <thead>
            <tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Joined</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td class="fw-semibold">
                    <a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a>
                </td>
                <td>{{ $user->email }}</td>
                <td>
                    <span class="badge
                        {{ $user->role === 'admin' ? 'bg-danger' :
                           ($user->role === 'team_captain' ? 'bg-primary' : 'bg-success') }}">
                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                    </span>
                </td>
                <td>
                    <span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-warning text-dark' }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </td>
                <td>{{ $user->created_at->format('d M Y') }}</td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.users.show', $user) }}"
                           class="btn btn-outline-secondary btn-sm">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        @if(!$user->isAdmin())
                            @if($user->status === 'active')
                            <form action="{{ route('admin.users.suspend', $user) }}" method="POST">
                                @csrf
                                <button class="btn btn-outline-warning btn-sm" title="Suspend">
                                    <i class="fa-solid fa-ban"></i>
                                </button>
                            </form>
                            @else
                            <form action="{{ route('admin.users.activate', $user) }}" method="POST">
                                @csrf
                                <button class="btn btn-outline-success btn-sm" title="Activate">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                            </form>
                            @endif
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-muted py-4">No users found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $users->links() }}</div>
@endsection
