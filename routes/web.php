<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\StatsController as AdminStatsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\DraftController as AdminDraftController;
use App\Http\Controllers\Admin\PlayerController as AdminPlayerController;
use App\Http\Controllers\Admin\TeamController as AdminTeamController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Player\RegistrationController as PlayerController;
use App\Http\Controllers\Team\TeamController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/rules', [HomeController::class, 'rules'])->name('rules');
Route::get('/leaderboard', [HomeController::class, 'leaderboard'])->name('leaderboard');

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| PLAYER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:player'])->prefix('player')->name('player.')->group(function () {
    Route::get('/register', [PlayerController::class, 'showForm'])->name('register');
    Route::post('/register', [PlayerController::class, 'store'])->name('register.store');
    Route::get('/dashboard', [PlayerController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile/edit', [PlayerController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [PlayerController::class, 'updateProfile'])->name('profile.update');
    Route::get('/stats', [PlayerController::class, 'stats'])->name('stats');
});

/*
|--------------------------------------------------------------------------
| TEAM CAPTAIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:team_captain'])->prefix('team')->name('team.')->group(function () {
    Route::get('/register', [TeamController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [TeamController::class, 'store'])->name('register.store');
    Route::get('/dashboard', [TeamController::class, 'dashboard'])->name('dashboard');
    Route::put('/profile', [TeamController::class, 'updateProfile'])->name('profile.update');
    Route::get('/players', [TeamController::class, 'players'])->name('players');

    // Draft room
    Route::get('/draft-room', [TeamController::class, 'draftRoom'])->name('draft-room');
    Route::post('/draft/pick', [TeamController::class, 'pickPlayer'])->name('draft.pick');

    // Matches
    Route::get('/matches', [TeamController::class, 'matches'])->name('matches');
    Route::get('/matches/create', [TeamController::class, 'createMatch'])->name('matches.create');
    Route::post('/matches', [TeamController::class, 'storeMatch'])->name('matches.store');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/audit-logs', [AdminDashboard::class, 'auditLogs'])->name('audit-logs');

    // Players
    Route::get('/players', [AdminPlayerController::class, 'index'])->name('players.index');
    Route::get('/players/{player}', [AdminPlayerController::class, 'show'])->name('players.show');
    Route::get('/players/{player}/edit', [AdminPlayerController::class, 'edit'])->name('players.edit');
    Route::put('/players/{player}', [AdminPlayerController::class, 'update'])->name('players.update');
    Route::post('/players/{player}/approve', [AdminPlayerController::class, 'approve'])->name('players.approve');
    Route::post('/players/{player}/reject', [AdminPlayerController::class, 'reject'])->name('players.reject');
    Route::post('/players/{player}/category', [AdminPlayerController::class, 'assignCategory'])->name('players.category');
    Route::post('/players/bulk-approve', [AdminPlayerController::class, 'bulkApprove'])->name('players.bulk-approve');
    Route::delete('/players/{player}', [AdminPlayerController::class, 'destroy'])->name('players.destroy');

    // Teams
    Route::get('/teams', [AdminTeamController::class, 'index'])->name('teams.index');
    Route::get('/teams/{team}', [AdminTeamController::class, 'show'])->name('teams.show');
    Route::get('/teams/{team}/edit', [AdminTeamController::class, 'edit'])->name('teams.edit');
    Route::put('/teams/{team}', [AdminTeamController::class, 'update'])->name('teams.update');
    Route::post('/teams/{team}/approve', [AdminTeamController::class, 'approve'])->name('teams.approve');
    Route::post('/teams/{team}/reject', [AdminTeamController::class, 'reject'])->name('teams.reject');
    Route::delete('/teams/{team}', [AdminTeamController::class, 'destroy'])->name('teams.destroy');

    // Draft Engine
    Route::get('/draft', [AdminDraftController::class, 'index'])->name('draft.index');
    Route::get('/draft/create', [AdminDraftController::class, 'create'])->name('draft.create');
    Route::post('/draft', [AdminDraftController::class, 'store'])->name('draft.store');
    Route::get('/draft/{draft}', [AdminDraftController::class, 'show'])->name('draft.show');
    Route::post('/draft/{draft}/start', [AdminDraftController::class, 'start'])->name('draft.start');
    Route::post('/draft/{draft}/pause', [AdminDraftController::class, 'pause'])->name('draft.pause');
    Route::post('/draft/{draft}/resume', [AdminDraftController::class, 'resume'])->name('draft.resume');
    Route::get('/draft/{draft}/live-state', [AdminDraftController::class, 'liveState'])->name('draft.live-state');
    Route::post('/draft/{draft}/force-pick', [AdminDraftController::class, 'forcePick'])->name('draft.force-pick');
    Route::post('/draft/{draft}/skip-turn', [AdminDraftController::class, 'skipTurn'])->name('draft.skip-turn');
    Route::get('/draft/{draft}/analytics', [AdminDraftController::class, 'analytics'])->name('draft.analytics');

    // Categories
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('/categories/{category}/toggle', [AdminCategoryController::class, 'toggleActive'])->name('categories.toggle');

    // Users
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/suspend', [AdminUserController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{user}/activate', [AdminUserController::class, 'activate'])->name('users.activate');
    Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Stats / Matches
    Route::get('/stats/matches', [AdminStatsController::class, 'matchIndex'])->name('stats.matches');
    Route::get('/stats/matches/{match}', [AdminStatsController::class, 'matchShow'])->name('stats.match-show');
    Route::post('/stats/matches/{match}/player-stat', [AdminStatsController::class, 'storePlayerStat'])->name('stats.store-player-stat');
    Route::get('/stats/leaderboard', [AdminStatsController::class, 'leaderboard'])->name('stats.leaderboard');
});