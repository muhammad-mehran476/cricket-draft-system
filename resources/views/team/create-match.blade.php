@extends('layouts.app')
@section('title', 'Add Match Result')

@section('content')
@include('partials.navbar')

<div class="container py-5" style="max-width:700px;">
    <div class="card card-stadium p-4">
        <h3 class="font-heading mb-4"><i class="fa-solid fa-plus me-2"></i>Add Match Result</h3>

        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        <form method="POST" action="{{ route('team.matches.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Opponent Name</label>
                    <input type="text" name="opponent_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Match Date</label>
                    <input type="date" name="match_date" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Venue</label>
                    <input type="text" name="venue" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Match Type</label>
                    <select name="match_type" class="form-select" required>
                        <option value="league">League</option>
                        <option value="knockout">Knockout</option>
                        <option value="friendly">Friendly</option>
                        <option value="final">Final</option>
                    </select>
                </div>

                <div class="col-12"><hr><h6>My Team Score</h6></div>
                <div class="col-md-4"><label class="form-label">Runs</label><input type="number" name="home_runs" class="form-control" min="0" required></div>
                <div class="col-md-4"><label class="form-label">Wickets</label><input type="number" name="home_wickets" class="form-control" min="0" max="10" required></div>
                <div class="col-md-4"><label class="form-label">Overs</label><input type="number" step="0.1" name="home_overs" class="form-control" min="0" required></div>

                <div class="col-12"><hr><h6>Opponent Score</h6></div>
                <div class="col-md-4"><label class="form-label">Runs</label><input type="number" name="away_runs" class="form-control" min="0" required></div>
                <div class="col-md-4"><label class="form-label">Wickets</label><input type="number" name="away_wickets" class="form-control" min="0" max="10" required></div>
                <div class="col-md-4"><label class="form-label">Overs</label><input type="number" step="0.1" name="away_overs" class="form-control" min="0" required></div>

                <div class="col-md-6">
                    <label class="form-label">Result</label>
                    <select name="result" class="form-select" required>
                        <option value="win">Win</option>
                        <option value="loss">Loss</option>
                        <option value="draw">Draw</option>
                        <option value="no_result">No Result</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-stadium w-100 mt-4">Save Match Result</button>
        </form>
    </div>
</div>

@include('partials.footer')
@endsection
