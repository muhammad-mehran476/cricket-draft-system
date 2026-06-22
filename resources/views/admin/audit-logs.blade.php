@extends('layouts.admin')
@section('title', 'Audit Logs')

@section('admin-content')
<h3 class="font-heading mb-4">Audit Logs</h3>

<div class="card card-stadium p-3 mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-6">
            <select name="action" class="form-select">
                <option value="">All Actions</option>
                @foreach($actions as $a)
                    <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ str_replace('_', ' ', ucfirst($a)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3"><button class="btn btn-stadium w-100">Filter</button></div>
    </form>
</div>

<div class="card card-stadium p-3">
    <table class="table table-stadium table-hover mb-0">
        <thead><tr><th>Date/Time</th><th>User</th><th>Action</th><th>Description</th><th>IP</th></tr></thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td>{{ $log->created_at->format('d M Y, H:i:s') }}</td>
                <td>{{ $log->user->name ?? 'System' }}</td>
                <td><span class="badge bg-secondary">{{ str_replace('_',' ', $log->action) }}</span></td>
                <td>{{ $log->description }}</td>
                <td><small class="text-muted">{{ $log->ip_address }}</small></td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No audit logs found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $logs->links() }}</div>
@endsection
