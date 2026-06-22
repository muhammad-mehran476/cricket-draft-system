<nav class="navbar navbar-expand-lg navbar-cdcms sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="fa-solid fa-cricket-bat-ball me-2"></i>CDCMS
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" style="filter: invert(1);">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto ms-4">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('rules') }}">Rules</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('leaderboard') }}">Leaderboard</a></li>
            </ul>
            <ul class="navbar-nav">
                @auth
                    @php $u = auth()->user(); @endphp
                    <li class="nav-item">
                        <a class="nav-link" href="{{ $u->role === 'admin' ? route('admin.dashboard') : ($u->role === 'team_captain' ? route('team.dashboard') : route('player.dashboard')) }}">
                            <i class="fa-solid fa-gauge me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button class="nav-link border-0 bg-transparent" type="submit">
                                <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
                            </button>
                        </form>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    <li class="nav-item">
                        <a class="btn btn-gold ms-2" href="{{ route('register') }}">Get Started</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
<div class="pitch-stripe"></div>
