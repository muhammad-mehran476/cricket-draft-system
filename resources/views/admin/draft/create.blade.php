@extends('layouts.admin')
@section('title', 'Create Draft Session')

@section('admin-content')
<a href="{{ route('admin.draft.index') }}" class="btn btn-link mb-3">&larr; Back to Draft Engine</a>

<div class="card card-stadium p-4" style="max-width:650px;">
    <h4 class="font-heading mb-4">Create New Draft Session</h4>

    <div class="alert alert-info">
        <strong>{{ $approvedTeams }}</strong> approved teams and <strong>{{ $approvedPlayers }}</strong> approved players are ready.
        @if($approvedTeams < 2)
            <div class="text-danger mt-1">You need at least 2 approved teams to start a draft.</div>
        @endif
    </div>

    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form action="{{ route('admin.draft.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Draft Session Title</label>
            <input type="text" name="title" class="form-control" placeholder="e.g. CDCMS Season 1 Draft" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Timer per Pick (seconds)</label>
            <input type="number" name="timer_seconds" class="form-control" value="300" min="30" max="600" required>
            <small class="text-muted">Default: 300 seconds (5 minutes) per team per pick.</small>
        </div>

        <h6 class="mt-4">Draft Category Order</h6>
        <ol class="list-group list-group-numbered mb-3">
            @foreach($categories as $cat)
            <li class="list-group-item d-flex justify-content-between">{{ $cat->name }} <span class="text-muted">max {{ $cat->max_players }}</span></li>
            @endforeach
        </ol>

        <button type="submit" class="btn btn-stadium w-100">Create Draft Session</button>
    </form>
</div>
@endsection
