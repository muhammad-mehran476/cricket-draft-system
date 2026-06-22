@extends('layouts.admin')
@section('title', 'Edit Team')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0">Edit Team: {{ $team->team_name }}</h3>
    <a href="{{ route('admin.teams.show', $team) }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Back to Team
    </a>
</div>

<div class="card card-stadium p-4">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.teams.update', $team) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Team Name</label>
                <input type="text" name="team_name" class="form-control" value="{{ old('team_name', $team->team_name) }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Captain Name</label>
                <input type="text" name="captain_name" class="form-control" value="{{ old('captain_name', $team->captain_name) }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    @foreach(['pending','approved','rejected'] as $s)
                        <option value="{{ $s }}" {{ old('status', $team->status) === $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6" id="rejectionReasonField" style="{{ old('status', $team->status) === 'rejected' ? '' : 'display:none;' }}">
                <label class="form-label">Rejection Reason</label>
                <input type="text" name="rejection_reason" class="form-control" value="{{ old('rejection_reason', $team->rejection_reason) }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Team Logo</label>
                <input type="file" name="logo" class="form-control" accept="image/*">
                @if($team->logo_url)
                    <img src="{{ $team->logo_url }}" width="60" height="60" class="rounded-circle mt-2" style="object-fit:cover;">
                @endif
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-stadium">Save Changes</button>
            <a href="{{ route('admin.teams.show', $team) }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
    document.getElementById('status').addEventListener('change', function () {
        document.getElementById('rejectionReasonField').style.display = this.value === 'rejected' ? '' : 'none';
    });
</script>
@endsection