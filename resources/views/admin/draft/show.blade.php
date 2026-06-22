@extends('layouts.admin')
@section('title', 'Draft Control Room')

@push('styles')
<style>
    #pickFeedAdmin { max-height: 420px; overflow-y: auto; }
    .feed-item { border-left: 4px solid var(--stadium-green); padding: .5rem .75rem; margin-bottom: .5rem; background:#f8f9fa; border-radius: 6px; }
    .control-bar { background: var(--night-navy); border-radius: 14px; padding: 1.5rem; color: #fff; }
</style>
@endpush

@section('admin-content')
<a href="{{ route('admin.draft.index') }}" class="btn btn-link mb-3">&larr; Back to Draft Engine</a>

<div id="adminDraftApp" data-session-id="{{ $draft->id }}">

    <div class="control-bar mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h4 class="mb-1">{{ $draft->title }}</h4>
            <span class="badge bg-{{ $draft->status === 'active' ? 'success' : ($draft->status === 'completed' ? 'secondary' : 'warning') }}" id="sessionStatusBadge">{{ ucfirst($draft->status) }}</span>
            <span class="ms-2">Category: <strong id="currentCategoryName">{{ $draft->currentCategory->name ?? '-' }}</strong></span>
            <span class="ms-2">Round: <strong>{{ $draft->current_round }}</strong></span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="timer-display text-white" id="adminTimerDisplay">--:--</div>
            <div class="d-flex gap-2">
                @if($draft->status === 'pending')
                <form action="{{ route('admin.draft.start', $draft) }}" method="POST">
                    @csrf<button class="btn btn-success">Start Draft</button>
                </form>
                @elseif($draft->status === 'active')
                <form action="{{ route('admin.draft.pause', $draft) }}" method="POST">
                    @csrf<button class="btn btn-warning">Pause</button>
                </form>
                <button class="btn btn-outline-light" id="skipTurnBtn">Skip Turn</button>
                @elseif($draft->status === 'paused')
                <form action="{{ route('admin.draft.resume', $draft) }}" method="POST">
                    @csrf<button class="btn btn-success">Resume</button>
                </form>
                @endif
                <a href="{{ route('admin.draft.analytics', $draft) }}" class="btn btn-gold">Analytics</a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card card-stadium p-3">
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="font-heading mb-0">Current Turn: <span id="currentTeamName">{{ $liveState['current_team']->team_name ?? '-' }}</span></h5>
                </div>

                <h6 class="text-muted mb-2">Available Players in Current Category</h6>
                <div class="row g-3" id="adminPlayerGrid">
                    @forelse($liveState['available_players'] as $player)
                    <div class="col-md-6 col-lg-4">
                        <div class="card player-card h-100 p-2 text-center" data-player-id="{{ $player->id }}">
                            <img src="{{ $player->profile_picture_url }}" class="rounded-circle mx-auto mt-2" width="56" height="56" style="object-fit:cover;">
                            <div class="card-body p-2">
                                <h6 class="mb-0 fw-bold">{{ $player->name }}</h6>
                                <small class="text-muted d-block">{{ $player->role_display }}</small>
                                <span class="badge {{ $player->skill_badge_class }} mt-1 mb-2">{{ ucfirst($player->skill_level) }}</span><br>
                                <button class="btn btn-stadium btn-sm mt-1 force-pick-btn" data-player-id="{{ $player->id }}" data-player-name="{{ $player->name }}">
                                    Assign to Current Team
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted">No players available in this category.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-stadium p-3 mb-3">
                <h6 class="font-heading mb-2">Team Draft Counts</h6>
                @foreach($teams as $t)
                <div class="d-flex justify-content-between mb-1">
                    <span>{{ $t->team_name }}</span>
                    <span class="badge bg-secondary">{{ $t->players_count }}/17</span>
                </div>
                @endforeach
            </div>

            <div class="card card-stadium p-3">
                <h6 class="font-heading mb-2">Live Pick Feed</h6>
                <div id="pickFeedAdmin">
                    @forelse($draft->picks as $pick)
                    <div class="feed-item">
                        <strong>{{ $pick->team->team_name }}</strong> picked <strong>{{ $pick->player->name }}</strong>
                        <small class="text-muted d-block">{{ $pick->category->name }} — Pick #{{ $pick->pick_number }}</small>
                    </div>
                    @empty
                    <p class="text-muted small">No picks yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Force-pick confirm modal -->
<div class="modal fade" id="forcePickModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Confirm Assignment</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                Assign <strong id="fpPlayerName"></strong> to <strong id="fpTeamName">{{ $liveState['current_team']->team_name ?? '-' }}</strong>?
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-stadium" id="confirmForcePickBtn">Confirm</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const draftId = {{ $draft->id }};
let selectedPlayerId = null;
let countdownSeconds = {{ $liveState['timer_remaining'] ?? 0 }};

function updateTimerDisplay(seconds) {
    countdownSeconds = seconds;
    const m = Math.floor(seconds / 60).toString().padStart(2, '0');
    const s = (seconds % 60).toString().padStart(2, '0');
    const el = document.getElementById('adminTimerDisplay');
    el.textContent = `${m}:${s}`;
    el.classList.toggle('warning', seconds <= 30);
}
setInterval(() => {
    if (countdownSeconds > 0) { countdownSeconds--; updateTimerDisplay(countdownSeconds); }
}, 1000);
updateTimerDisplay(countdownSeconds);

document.querySelectorAll('.force-pick-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        selectedPlayerId = btn.dataset.playerId;
        document.getElementById('fpPlayerName').textContent = btn.dataset.playerName;
        new bootstrap.Modal(document.getElementById('forcePickModal')).show();
    });
});

document.getElementById('confirmForcePickBtn').addEventListener('click', () => {
    const currentTeamId = {{ $draft->current_team_turn_id ?? 'null' }};
    if (!currentTeamId) { alert('No active team turn.'); return; }

    fetch(`/admin/draft/${draftId}/force-pick`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.CDCMS.csrfToken },
        body: JSON.stringify({ team_id: currentTeamId, player_id: selectedPlayerId }),
    })
    .then(r => r.json())
    .then(data => {
        bootstrap.Modal.getInstance(document.getElementById('forcePickModal')).hide();
        if (data.success) { setTimeout(() => location.reload(), 600); }
        else { alert(data.message); }
    });
});

const skipBtn = document.getElementById('skipTurnBtn');
if (skipBtn) {
    skipBtn.addEventListener('click', () => {
        if (!confirm('Skip the current team\'s turn?')) return;
        fetch(`/admin/draft/${draftId}/skip-turn`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.CDCMS.csrfToken },
        }).then(() => location.reload());
    });
}

// Real-time updates via Pusher (falls back to polling if not configured)
if (window.CDCMS.pusherKey) {
    const pusher = new Pusher(window.CDCMS.pusherKey, { cluster: window.CDCMS.pusherCluster });
    const channel = pusher.subscribe('draft.' + draftId);
    channel.bind('player.picked', () => setTimeout(() => location.reload(), 1200));
    channel.bind('timer.expired', () => setTimeout(() => location.reload(), 1000));
    channel.bind('category.changed', () => setTimeout(() => location.reload(), 1000));
    channel.bind('draft.completed', () => setTimeout(() => location.reload(), 1000));
} else {
    setInterval(() => {
        fetch(`{{ route('admin.draft.live-state', $draft) }}`)
            .then(r => r.json())
            .then(state => updateTimerDisplay(state.timer_remaining));
    }, 5000);
}
</script>
@endpush
@endsection
