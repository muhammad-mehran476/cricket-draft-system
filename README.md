# CDCMS — Cricket Drafting Ceremony Management System

A production-ready Laravel application for running a live, multi-team cricket player draft: registration → admin approval → categorization → real-time drafting ceremony → match/stat tracking.

## Tech Stack
- **Backend:** Laravel 11 (PHP 8.2+)
- **Database:** MySQL 8 (fully normalized, see `database/cricket_draft_system.sql`)
- **Frontend:** Blade + Bootstrap 5 + vanilla JS
- **Charts:** Chart.js
- **Real-time:** Laravel Broadcasting via Pusher (with a polling fallback if Pusher isn't configured)

## 1. Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set your database credentials (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) and, optionally, your Pusher credentials for real-time draft updates.

## 2. Database Setup

**Option A — Laravel migrations (recommended):**
```bash
php artisan migrate
php artisan db:seed
```

**Option B — Direct SQL import (phpMyAdmin):**
Import `database/cricket_draft_system.sql` directly into phpMyAdmin or via:
```bash
mysql -u root -p < database/cricket_draft_system.sql
```
This file creates the database, all tables with foreign keys/indexes, and seeds the admin user + 6 draft categories.

## 3. Storage Link (for uploaded photos/slips)

```bash
php artisan storage:link
```

## 4. Run the App

```bash
php artisan serve
```
Visit `http://localhost:8000`.

## 5. Default Admin Login

```
Email:    admin@cdcms.com
Password: Admin@123
```

## 6. Real-Time Draft (Pusher)

Set these in `.env` to enable live WebSocket updates during the draft ceremony:
```
PUSHER_APP_ID=...
PUSHER_APP_KEY=...
PUSHER_APP_SECRET=...
PUSHER_APP_CLUSTER=...
```
If left blank, the draft room and admin control room automatically fall back to polling every few seconds via `/api/draft/{id}/poll`, so the system still works without Pusher configured.

## 7. Scheduler (auto-skip expired turns)

For the server-side safety net that auto-skips a team's turn if their pick timer expires (in case the browser tab is closed), add this cron entry:
```
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

## How the Draft Engine Works

1. Admin creates a Draft Session (sets per-pick timer, e.g. 300s).
2. Admin clicks **Start Draft** → the system picks the first active category (by `draft_order`) and randomly shuffles the approved teams into a pick queue.
3. Each team gets a turn, in order, to pick one available player from the current category. Picking is enforced server-side in `App\Services\DraftEngine::pickPlayer()` — a team cannot pick out of turn, pick an already-drafted player, or pick from the wrong category.
4. If a team's timer expires, their turn is automatically skipped (`handleTimerExpiry()`), either by the browser-side JS calling the skip endpoint, by the admin clicking "Skip Turn", or by the `draft:check-timers` scheduled command as a safety net.
5. Once every team has picked or had a turn in the round, the round is marked complete and the engine automatically transitions to the next category, generating a brand-new random pick order.
6. When the last category's round completes, the draft session is marked `completed` and a `DraftCompleted` event is broadcast.
7. All actions (picks, approvals, rejections, category changes) are written to `audit_logs` via `AuditLog::record()`.

## Folder Structure Highlights

```
app/
  Events/            → Broadcasting events (DraftStarted, PlayerPicked, etc.)
  Http/Controllers/
    Admin/           → Player/Team approval, Draft control room, Dashboard
    Player/          → Player registration + dashboard
    Team/             → Team registration, dashboard, draft room, matches
    Auth/            → Login/Register
  Http/Middleware/   → RoleMiddleware (role:admin|player|team_captain)
  Models/            → One Eloquent model per file (PSR-4 compliant)
  Services/
    DraftEngine.php  → Core draft logic (turn order, picking, timers, category transitions)
database/
  migrations/        → All schema migrations
  seeders/           → Admin user + categories seed
  cricket_draft_system.sql → Full SQL export for phpMyAdmin
resources/views/
  admin/             → Admin dashboard, player/team management, draft control room
  player/, team/      → Role dashboards, registration forms, live draft room
  public/            → Landing page, rules, leaderboard
routes/
  web.php            → All web routes, grouped by role
  channels.php       → Broadcast channel authorization
  console.php        → Scheduled commands
```

## Notes
- Default placeholder images (`images/default-player.png`, `images/default-team.png`) should be added to `public/images/` before going live, or swap the fallback URLs in `Player.php` / `Team.php`.
- File uploads (photos, payment slips, logos) are stored on the `public` disk under `storage/app/public/...` and served via the `storage:link` symlink.
