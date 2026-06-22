@extends('layouts.app')
@section('title', 'Live Draft Room')

@push('styles')
<style>
    .turn-banner { border-radius: 14px; padding: 1.25rem; }
    .turn-banner.my-turn { background: linear-gradient(135deg,#27ae60,#1e8449); color:#fff; animation: glow 1.5s infinite; }
    .turn-banner.waiting { background: #eef2f0; color:#555; }
    @keyframes glow { 0%,100%{box-shadow:0 0 0 rgba(39,174,96,.4);} 50%{box-shadow:0 0 25px rgba(39,174,96,.6);} }
    #pickFeed { max-height: 360px; overflow-y: auto; }
    .feed-item { border-left: 4px solid var(--stadium-green); padding: .5rem .75rem; margin-bottom: .5rem; background:#f8f9fa; border-radius: 6px; }
</style>
@endpush

@section('content')
@include('partials.navbar')

<div class="container py-4" id="draftRoomApp"
     data-session-id="{{ $session->id }}"
     data-team-id="{{ $team->id }}">

    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div id="turnBanner" class="turn-banner {{ $isMyTurn ? 'my-turn' : 'waiting' }}">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1" id="turnText">
                            @if($isMyTurn) <i class="fa-solid fa-bell me-2"></i>It's YOUR turn to pick! @else Waiting for {{ $session->currentTeam->team_name ?? '...' }} @endif
                        </h5>
                        <small>Category: <strong id="currentCategoryName">{{ $session->currentCategory->name ?? '-' }}</strong></small>
                    </div>
                    <div class="timer-display" id="timerDisplay">--:--</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stadium p-3 text-center h-100">
                <h6 class="text-muted mb-1">Players Drafted</h6>
                <h2 class="fw-bold text-success mb-0">{{ $team->total_players_drafted }} / 17</h2>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card card-stadium p-3">
                <h5 class="font-heading mb-3">Available Players — <span id="categoryLabel">{{ $session->currentCategory->name ?? '' }}</span></h5>
                <div class="row g-3" id="playerGrid">
                    @forelse($availablePlayers as $player)
                    <div class="col-md-6 col-lg-4">
                        <div class="card player-card h-100 p-2 text-center {{ $isMyTurn ? '' : 'disabled' }}"
                             data-player-id="{{ $player->id }}" onclick="selectPlayer({{ $player->id }}, '{{ $player->name }}')">
                            <img src="{{ $player->profile_picture_url }}" class="rounded-circle mx-auto mt-2" width="64" height="64" style="object-fit:cover;">
                            <div class="card-body p-2">
                                <h6 class="mb-0 fw-bold">{{ $player->name }}</h6>
                                <small class="text-muted d-block">{{ $player->role_display }}</small>
                                <span class="badge {{ $player->skill_badge_class }} mt-1">{{ ucfirst($player->skill_level) }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center">No players available in this category right now.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-stadium p-3 mb-3">
                <h6 class="font-heading mb-2">My Picks This Draft</h6>
                <ul class="list-group list-group-flush" id="myPicksList">
                    @forelse($myPicks as $pick)
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span>{{ $pick->player->name }}</span>
                        <small class="text-muted">{{ $pick->player->category->name ?? '' }}</small>
                    </li>
                    @empty
                    <li class="list-group-item px-0 text-muted">No picks yet.</li>
                    @endforelse
                </ul>
            </div>

            <div class="card card-stadium p-3">
                <h6 class="font-heading mb-2">Live Pick Feed</h6>
                <div id="pickFeed">
                    <p class="text-muted small">Waiting for picks...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirm pick modal -->
<div class="modal fade" id="confirmPickModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Confirm Selection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">Are you sure you want to draft <strong id="confirmPlayerName"></strong>?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-stadium" id="confirmPickBtn">Confirm Pick</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const sessionId = {{ $session->id }};
const myTeamId  = {{ $team->id }};
let selectedPlayerId = null;
let isMyTurn = {{ $isMyTurn ? 'true' : 'false' }};
let pollInterval = null;

// Initialize Pusher if configured, else fall back to polling
function initRealtime() {
    if (window.CDCMS.pusherKey) {
        const pusher = new Pusher(window.CDCMS.pusherKey, { cluster: window.CDCMS.pusherCluster });
        const channel = pusher.subscribe('draft.' + sessionId);
        channel.bind('player.picked', onPlayerPicked);
        channel.bind('timer.expired', onTimerExpired);
        channel.bind('category.changed', onCategoryChanged);
        channel.bind('draft.completed', onDraftCompleted);
    } else {
        pollInterval = setInterval(fetchLiveState, 3000);
    }
}

function fetchLiveState() {
    fetch(`/api/draft/${sessionId}/poll`)
        .then(r => r.json())
        .then(updateUIFromState)
        .catch(console.error);
}

function updateUIFromState(state) {
    isMyTurn = state.current_team && state.current_team.id === myTeamId;
    document.getElementById('turnBanner').className = 'turn-banner ' + (isMyTurn ? 'my-turn' : 'waiting');
    document.getElementById('turnText').innerHTML = isMyTurn
        ? '<i class="fa-solid fa-bell me-2"></i>It\'s YOUR turn to pick!'
        : 'Waiting for ' + (state.current_team?.team_name ?? '...');
    updateTimerDisplay(state.timer_remaining);
    document.querySelectorAll('.player-card').forEach(card => {
        card.classList.toggle('disabled', !isMyTurn);
    });
}

let countdownSeconds = {{ $session->timer_remaining ?? $session->timer_seconds }};
function startCountdown() {
    setInterval(() => {
        if (countdownSeconds > 0) countdownSeconds--;
        updateTimerDisplay(countdownSeconds);
    }, 1000);
}
function updateTimerDisplay(seconds) {
    countdownSeconds = seconds;
    const m = Math.floor(seconds / 60).toString().padStart(2, '0');
    const s = (seconds % 60).toString().padStart(2, '0');
    const el = document.getElementById('timerDisplay');
    el.textContent = `${m}:${s}`;
    el.classList.toggle('warning', seconds <= 30);
}

function selectPlayer(playerId, playerName) {
    if (!isMyTurn) return;
    selectedPlayerId = playerId;
    document.getElementById('confirmPlayerName').textContent = playerName;
    new bootstrap.Modal(document.getElementById('confirmPickModal')).show();
}

document.getElementById('confirmPickBtn').addEventListener('click', () => {
    if (!selectedPlayerId) return;
    fetch("{{ route('team.draft.pick') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.CDCMS.csrfToken,
        },
        body: JSON.stringify({ player_id: selectedPlayerId, draft_session_id: sessionId }),
    })
    .then(r => r.json())
    .then(data => {
        bootstrap.Modal.getInstance(document.getElementById('confirmPickModal')).hide();
        if (data.success) {
            setTimeout(() => location.reload(), 800);
        } else {
            alert(data.message);
        }
    })
    .catch(() => alert('An error occurred. Please try again.'));
});

function onPlayerPicked(data) {
    const feed = document.getElementById('pickFeed');
    const item = document.createElement('div');
    item.className = 'feed-item';
    item.innerHTML = `<strong>${data.team.name}</strong> picked <strong>${data.player.name}</strong> <small class="text-muted">(${data.category})</small>`;
    feed.prepend(item);
    setTimeout(() => location.reload(), 1500);
}
function onTimerExpired(data) {
    alert(data.message);
    setTimeout(() => location.reload(), 1000);
}
function onCategoryChanged(data) {
    alert(data.message);
    setTimeout(() => location.reload(), 1000);
}
function onDraftCompleted(data) {
    alert('Draft completed! Redirecting to dashboard...');
    window.location.href = "{{ route('team.dashboard') }}";
}

startCountdown();
initRealtime();
</script>
@endpush
@endsection
