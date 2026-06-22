@extends('layouts.app')
@section('title', 'Tournament Rules')

@section('content')
@include('partials.navbar')

<div class="container py-5" style="max-width:800px;">
    <h2 class="font-heading mb-4 text-center">Tournament & Draft Rules</h2>

    <div class="card card-stadium p-4 mb-3">
        <h5 class="font-heading">1. Registration</h5>
        <p>All players and teams must register through the official portal and submit valid documents (photo and payment slip). Registrations are reviewed and approved or rejected by the tournament admin.</p>
    </div>

    <div class="card card-stadium p-4 mb-3">
        <h5 class="font-heading">2. Player Categories</h5>
        <p>Approved players are placed into one of six draft categories by the admin: Iconic Players, Platinum Players, Gold Batsmen, Gold Bowlers, All Rounders, and Emerging Players. Categories are drafted in this order.</p>
    </div>

    <div class="card card-stadium p-4 mb-3">
        <h5 class="font-heading">3. Draft Order</h5>
        <p>At the start of each category, the system randomly generates a pick order for all approved teams. This order is regenerated for every new category.</p>
    </div>

    <div class="card card-stadium p-4 mb-3">
        <h5 class="font-heading">4. Pick Timer</h5>
        <p>Each team is given a fixed time window (default 5 minutes) to select a player on their turn. If the timer expires without a selection, that team's turn is skipped for the round.</p>
    </div>

    <div class="card card-stadium p-4 mb-3">
        <h5 class="font-heading">5. Player Locking</h5>
        <p>Once a player is drafted by a team, they are permanently locked to that team for the tournament and cannot be selected again or moved.</p>
    </div>

    <div class="card card-stadium p-4">
        <h5 class="font-heading">6. Squad Size</h5>
        <p>Each team must draft a minimum of 16 and a maximum of 17 players across all categories to complete their squad.</p>
    </div>
</div>

@include('partials.footer')
@endsection
