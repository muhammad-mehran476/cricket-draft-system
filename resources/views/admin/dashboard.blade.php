@extends('layouts.admin')
@section('title', 'Admin Dashboard')

@section('admin-content')
<h3 class="font-heading mb-4">Dashboard Overview</h3>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="stat-card green"><h2 class="fw-bold">{{ $stats['total_players'] }}</h2><p class="mb-0">Total Players</p></div></div>
    <div class="col-md-3"><div class="stat-card gold"><h2 class="fw-bold">{{ $stats['pending_players'] }}</h2><p class="mb-0">Pending Players</p></div></div>
    <div class="col-md-3"><div class="stat-card navy"><h2 class="fw-bold">{{ $stats['total_teams'] }}</h2><p class="mb-0">Total Teams</p></div></div>
    <div class="col-md-3"><div class="stat-card crimson"><h2 class="fw-bold">{{ $stats['pending_teams'] }}</h2><p class="mb-0">Pending Teams</p></div></div>
</div>

@if($draft)
<div class="card card-stadium p-3 mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h6 class="mb-1">Active Draft: <strong>{{ $draft->title }}</strong></h6>
            <span class="badge bg-{{ $draft->status === 'active' ? 'success' : ($draft->status === 'completed' ? 'secondary' : 'warning') }}">{{ ucfirst($draft->status) }}</span>
        </div>
        <a href="{{ route('admin.draft.show', $draft) }}" class="btn btn-stadium">Manage Draft</a>
    </div>
</div>
@else
<div class="alert alert-info d-flex justify-content-between align-items-center">
    <span>No draft session created yet.</span>
    <a href="{{ route('admin.draft.create') }}" class="btn btn-stadium btn-sm">Create Draft Session</a>
</div>
@endif

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card card-stadium p-4">
            <h6 class="font-heading mb-3">Players by Role</h6>
            <canvas id="roleChart" height="220"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-stadium p-4">
            <h6 class="font-heading mb-3">Players by Skill Level</h6>
            <canvas id="skillChart" height="220"></canvas>
        </div>
    </div>
</div>

<div class="card card-stadium p-4 mb-4">
    <h6 class="font-heading mb-3">Category Draft Progress</h6>
    <table class="table table-stadium">
        <thead><tr><th>Category</th><th>Available</th><th>Drafted</th><th>Total</th><th>Progress</th></tr></thead>
        <tbody>
            @foreach($categoryStats as $cat)
            @php $pct = $cat->total > 0 ? round(($cat->drafted / $cat->total) * 100) : 0; @endphp
            <tr>
                <td>{{ $cat->name }}</td>
                <td>{{ $cat->available }}</td>
                <td>{{ $cat->drafted }}</td>
                <td>{{ $cat->total }}</td>
                <td style="width:200px;">
                    <div class="progress" style="height:18px;">
                        <div class="progress-bar bg-success" style="width:{{ $pct }}%">{{ $pct }}%</div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card card-stadium p-4">
            <h6 class="font-heading mb-3">Team Draft Progress (Target: 17 each)</h6>
            @foreach($teamDraftProgress as $t)
            <div class="mb-2">
                <div class="d-flex justify-content-between"><span>{{ $t['name'] }}</span><span>{{ $t['count'] }}/17</span></div>
                <div class="progress" style="height:10px;"><div class="progress-bar bg-success" style="width:{{ $t['percent'] }}%"></div></div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-stadium p-4">
            <h6 class="font-heading mb-3">Recent Activity</h6>
            <ul class="list-group list-group-flush" style="max-height:300px; overflow-y:auto;">
                @foreach($recentActivity as $log)
                <li class="list-group-item px-0">
                    <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small><br>
                    {{ $log->description }}
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('roleChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(array_keys($playerRoleChart->toArray())) !!},
        datasets: [{ data: {!! json_encode(array_values($playerRoleChart->toArray())) !!}, backgroundColor: ['#0d5c2f','#d4a017','#0b1f3a','#c0392b'] }]
    }
});
new Chart(document.getElementById('skillChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_keys($playerSkillChart->toArray())) !!},
        datasets: [{ label: 'Players', data: {!! json_encode(array_values($playerSkillChart->toArray())) !!}, backgroundColor: '#0d5c2f' }]
    },
    options: { plugins: { legend: { display: false } } }
});
</script>
@endpush
@endsection
