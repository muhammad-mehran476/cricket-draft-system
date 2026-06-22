<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
| The draft channel is public (not private) so all logged-in users
| (admin, teams, players) can watch the live draft ceremony.
| Picking actions themselves are still validated server-side in
| DraftEngine::pickPlayer(), so broadcasting publicly is safe.
*/

Broadcast::channel('draft.{sessionId}', function ($user, $sessionId) {
    // Any authenticated, active user may listen to the live draft feed.
    return $user->status === 'active';
});
