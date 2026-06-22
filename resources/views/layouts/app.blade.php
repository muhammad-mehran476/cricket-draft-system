<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CDCMS') | Cricket Draft Ceremony System</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/cdcms.css') }}">

    <style>
        :root {
            --stadium-green: #0d5c2f;
            --stadium-green-dark: #073e1f;
            --pitch-gold: #d4a017;
            --pitch-gold-light: #f0c843;
            --night-navy: #0b1f3a;
            --crimson: #c0392b;
        }
        body { font-family: 'Poppins', sans-serif; background: #f4f6f5; color: #1c2b25; }
        h1,h2,h3,h4,h5,.font-heading { font-family: 'Oswald', sans-serif; }
        .navbar-cdcms { background: linear-gradient(120deg,var(--stadium-green-dark),var(--stadium-green)); box-shadow: 0 2px 10px rgba(0,0,0,.15); }
        .navbar-cdcms .navbar-brand { color: var(--pitch-gold-light); font-family: 'Oswald', sans-serif; font-weight: 700; letter-spacing: .5px; }
        .navbar-cdcms .nav-link { color: #e8f0ea !important; font-weight: 500; }
        .navbar-cdcms .nav-link:hover { color: var(--pitch-gold-light) !important; }
        .btn-gold { background: var(--pitch-gold); border: none; color: #1c2b25; font-weight: 600; }
        .btn-gold:hover { background: var(--pitch-gold-light); color: #1c2b25; }
        .btn-stadium { background: var(--stadium-green); border: none; color: #fff; font-weight: 600; }
        .btn-stadium:hover { background: var(--stadium-green-dark); color: #fff; }
        .card-stadium { border: none; border-radius: 14px; box-shadow: 0 4px 16px rgba(13,92,47,.08); }
        .badge-good { background: #2ecc71; }
        .badge-better { background: #f39c12; }
        .badge-best { background: var(--crimson); }
        .stat-card { border-radius: 14px; padding: 1.5rem; color: #fff; }
        .stat-card.green   { background: linear-gradient(135deg,var(--stadium-green),var(--stadium-green-dark)); }
        .stat-card.gold    { background: linear-gradient(135deg,var(--pitch-gold-light),var(--pitch-gold)); color:#1c2b25; }
        .stat-card.navy    { background: linear-gradient(135deg,#16315c,var(--night-navy)); }
        .stat-card.crimson { background: linear-gradient(135deg,#e74c3c,var(--crimson)); }
        .sidebar-admin { background: var(--night-navy); min-height: 100vh; color: #cfd9ea; }
        .sidebar-admin a { color: #cfd9ea; text-decoration: none; display: block; padding: .75rem 1.25rem; border-radius: 8px; margin: 2px 8px; }
        .sidebar-admin a:hover,.sidebar-admin a.active { background: rgba(255,255,255,.08); color: var(--pitch-gold-light); }
        .sidebar-admin .sidebar-brand { color: var(--pitch-gold-light); font-family: 'Oswald'; padding: 1.25rem; font-weight: 700; font-size: 1.2rem; }
        .timer-display { font-family: 'Oswald', sans-serif; font-size: 3rem; font-weight: 700; color: var(--crimson); }
        .timer-display.warning { color: #f39c12; animation: pulse 1s infinite; }
        @keyframes pulse { 0%,100%{opacity:1;} 50%{opacity:.5;} }
        .player-card { transition: transform .15s ease, box-shadow .15s ease; cursor: pointer; }
        .player-card:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,.12); }
        .player-card.disabled { opacity: .4; pointer-events: none; }
        .hero-stadium { background: linear-gradient(135deg,rgba(7,62,31,.92),rgba(11,31,58,.92)), url('https://images.unsplash.com/photo-1531415074968-036ba1b575da?auto=format&fit=crop&w=1600&q=60') center/cover; color:#fff; padding: 6rem 0 5rem; }
        .pitch-stripe { height: 6px; background: repeating-linear-gradient(90deg,var(--pitch-gold) 0 40px,var(--stadium-green) 40px 80px); }
        footer { background: var(--night-navy); color: #b8c5d6; padding: 2.5rem 0; }
        .table-stadium thead { background: var(--stadium-green); color: #fff; }
    </style>

    @stack('styles')
</head>
<body data-my-team-id="{{ auth()->user()?->team?->id }}">

@if(session('success'))
    <div class="alert alert-success alert-dismissible flash-bar" data-auto-dismiss="4000">
        <i class="fa-solid fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible flash-bar" data-auto-dismiss="5000">
        <i class="fa-solid fa-circle-xmark me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@yield('content')

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
<script src="{{ asset('js/cdcms.js') }}"></script>
<script>
    window.CDCMS = {
        csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        pusherKey: "{{ config('broadcasting.connections.pusher.key') }}",
        pusherCluster: "{{ config('broadcasting.connections.pusher.options.cluster') }}",
    };
</script>
@stack('scripts')
</body>
</html>
