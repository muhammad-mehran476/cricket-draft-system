@extends('layouts.app')

@section('content')
<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar-admin" style="width:260px; position:sticky; top:0; height:100vh;">
        <div class="sidebar-brand"><i class="fa-solid fa-cricket-bat-ball me-2"></i>CDCMS Admin</div>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge me-2"></i>Dashboard
        </a>
        <a href="{{ route('admin.players.index') }}" class="{{ request()->routeIs('admin.players.*') ? 'active' : '' }}">
            <i class="fa-solid fa-person-running me-2"></i>Players
        </a>
        <a href="{{ route('admin.teams.index') }}" class="{{ request()->routeIs('admin.teams.*') ? 'active' : '' }}">
            <i class="fa-solid fa-shield-halved me-2"></i>Teams
        </a>
        <a href="{{ route('admin.draft.index') }}" class="{{ request()->routeIs('admin.draft.*') ? 'active' : '' }}">
            <i class="fa-solid fa-gavel me-2"></i>Draft Engine
        </a>
        <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <i class="fa-solid fa-tags me-2"></i>Categories
        </a>
        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users me-2"></i>Users
        </a>
        <a href="{{ route('admin.stats.leaderboard') }}" class="{{ request()->routeIs('admin.stats.*') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line me-2"></i>Stats
        </a>
        <a href="{{ route('admin.audit-logs') }}" class="{{ request()->routeIs('admin.audit-logs') ? 'active' : '' }}">
            <i class="fa-solid fa-clipboard-list me-2"></i>Audit Logs
        </a>
        <a href="{{ route('home') }}"><i class="fa-solid fa-arrow-up-right-from-square me-2"></i>View Site</a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="border-0 bg-transparent w-100 text-start" style="color:#cfd9ea; padding:.75rem 1.25rem;">
                <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
            </button>
        </form>
    </div>

    <!-- Main content -->
    <div class="flex-grow-1 p-4" style="background:#f4f6f5; min-height:100vh;">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

        @yield('admin-content')
    </div>
</div>
@endsection
