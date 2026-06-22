@extends('layouts.admin')
@section('title', 'Draft Engine')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0">Draft Engine</h3>
    @if(!$session)
    <a href="{{ route('admin.draft.create') }}" class="btn btn-stadium"><i class="fa-solid fa-plus me-2"></i>Create Draft Session</a>
    @endif
</div>

@if($session)
<div class="card card-stadium p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5>{{ $session->title }}</h5>
            <span class="badge bg-{{ $session->status === 'active' ? 'success' : ($session->status === 'completed' ? 'secondary' : 'warning') }}">{{ ucfirst($session->status) }}</span>
            <span class="ms-2 text-muted">Current Category: {{ $session->currentCategory->name ?? '-' }}</span>
        </div>
        <a href="{{ route('admin.draft.show', $session) }}" class="btn btn-stadium">Open Draft Control Room</a>
    </div>
</div>
@endif

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card card-stadium p-4">
            <h6 class="font-heading mb-3">Category Readiness</h6>
            <table class="table table-stadium mb-0">
                <thead><tr><th>Category</th><th>Available Players</th></tr></thead>
                <tbody>
                    @foreach($categories as $cat)
                    <tr><td>{{ $cat->name }}</td><td>{{ $cat->available }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-stadium p-4">
            <h6 class="font-heading mb-3">Approved Teams ({{ $teams->count() }})</h6>
            <table class="table table-stadium mb-0">
                <thead><tr><th>Team</th><th>Players Drafted</th></tr></thead>
                <tbody>
                    @foreach($teams as $t)
                    <tr><td>{{ $t->team_name }}</td><td>{{ $t->players_count }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
