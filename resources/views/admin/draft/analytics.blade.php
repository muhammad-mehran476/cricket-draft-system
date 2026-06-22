@extends('layouts.admin')
@section('title', 'Draft Analytics')

@section('admin-content')
<a href="{{ route('admin.draft.show', $draft) }}" class="btn btn-link mb-3">&larr; Back to Draft Room</a>

<h3 class="font-heading mb-4">Draft Analytics — {{ $draft->title }}</h3>

<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="stat-card green"><h2 class="fw-bold">{{ $picks->count() }}</h2><p class="mb-0">Total Picks</p></div></div>
    <div class="col-md-4"><div class="stat-card gold"><h2 class="fw-bold">{{ $avgPickTime ? round($avgPickTime) : 0 }}s</h2><p class="mb-0">Avg. Pick Time</p></div></div>
    <div class="col-md-4"><div class="stat-card navy"><h2 class="fw-bold">{{ $picksPerTeam->count() }}</h2><p class="mb-0">Teams Participating</p></div></div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card card-stadium p-4">
            <h6 class="font-heading mb-3">Picks per Team</h6>
            <canvas id="teamChart" height="240"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-stadium p-4">
            <h6 class="font-heading mb-3">Picks per Category</h6>
            <canvas id="categoryChart" height="240"></canvas>
        </div>
    </div>
</div>

<div class="card card-stadium p-3">
    <h6 class="font-heading mb-3">Full Pick History</h6>
    <table class="table table-stadium table-hover mb-0">
        <thead><tr><th>#</th><th>Player</th><th>Team</th><th>Category</th><th>Time Taken</th><th>Picked At</th></tr></thead>
        <tbody>
            @foreach($picks->sortBy('pick_number') as $pick)
            <tr>
                <td>{{ $pick->pick_number }}</td>
                <td>{{ $pick->player->name }}</td>
                <td>{{ $pick->team->team_name }}</td>
                <td>{{ $pick->category->name }}</td>
                <td>{{ $pick->time_taken_seconds }}s</td>
                <td>{{ $pick->picked_at->format('d M, H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('teamChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($picksPerTeam->keys()) !!},
        datasets: [{ label: 'Picks', data: {!! json_encode($picksPerTeam->values()) !!}, backgroundColor: '#0d5c2f' }]
    },
    options: { plugins: { legend: { display: false } } }
});
new Chart(document.getElementById('categoryChart'), {
    type: 'pie',
    data: {
        labels: {!! json_encode($picksPerCategory->keys()) !!},
        datasets: [{ data: {!! json_encode($picksPerCategory->values()) !!}, backgroundColor: ['#0d5c2f','#d4a017','#0b1f3a','#c0392b','#16315c','#f39c12'] }]
    }
});
</script>
@endpush
@endsection
