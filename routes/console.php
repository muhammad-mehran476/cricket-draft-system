<?php

use App\Models\DraftQueue;
use App\Models\DraftSession;
use App\Services\DraftEngine;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Command: auto-skip draft turns whose timer has expired
|--------------------------------------------------------------------------
| Acts as a server-side safety net in case the browser-driven
| timer/skip call doesn't fire for any reason (closed tab, lost
| connection, etc). Scheduled to run every minute below.
*/
Artisan::command('draft:check-timers', function () {
    $expired = DraftQueue::where('status', 'active')
        ->where('timer_expires_at', '<', now())
        ->get();

    foreach ($expired as $entry) {
        $session = DraftSession::find($entry->draft_session_id);
        if ($session && $session->isActive()) {
            (new DraftEngine($session))->handleTimerExpiry();
            $this->info("Auto-skipped expired turn for session #{$session->id}");
        }
    }
})->purpose('Check and auto-skip any draft turns whose timer has expired');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks (Laravel 11 style)
|--------------------------------------------------------------------------
*/
Schedule::command('draft:check-timers')->everyMinute();
