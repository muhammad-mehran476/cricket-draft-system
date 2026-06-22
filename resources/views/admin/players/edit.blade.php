@extends('layouts.admin')
@section('title', 'Edit Player')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0">Edit Player: {{ $player->name }}</h3>
    <a href="{{ route('admin.players.show', $player) }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Back to Player
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

    <form action="{{ route('admin.players.update', $player) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $player->name) }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Role</label>
                <input type="text" name="role" class="form-control" value="{{ old('role', $player->role) }}" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">-- None --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $player->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Team</label>
                <select name="team_id" class="form-select">
                    <option value="">-- Unassigned --</option>
                    @foreach($teams as $team)
                        <option value="{{ $team->id }}" {{ old('team_id', $player->team_id) == $team->id ? 'selected' : '' }}>
                            {{ $team->team_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- <div class="col-md-4">
                <label class="form-label">Skill Level</label>
                <select name="skill_level" class="form-select">
                    @foreach(['beginner','intermediate','advanced','professional'] as $level)
                        <option value="{{ $level }}" {{ old('skill_level', $player->skill_level) === $level ? 'selected' : '' }}>
                            {{ ucfirst($level) }}
                        </option>
                    @endforeach
                </select>
            </div> -->

            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    @foreach(['pending','approved','rejected','drafted'] as $s)
                        <option value="{{ $s }}" {{ old('status', $player->status) === $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6" id="rejectionReasonField" style="{{ old('status', $player->status) === 'rejected' ? '' : 'display:none;' }}">
                <label class="form-label">Rejection Reason</label>
                <input type="text" name="rejection_reason" class="form-control" value="{{ old('rejection_reason', $player->rejection_reason) }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Profile Picture</label>
                <input type="file" name="profile_picture" class="form-control" accept="image/*">
                @if($player->profile_picture_url)
                    <img src="{{ $player->profile_picture_url }}" width="60" height="60" class="rounded-circle mt-2" style="object-fit:cover;">
                @endif
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-stadium">Save Changes</button>
            <a href="{{ route('admin.players.show', $player) }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
    document.getElementById('status').addEventListener('change', function () {
        document.getElementById('rejectionReasonField').style.display = this.value === 'rejected' ? '' : 'none';
    });
</script>
@endsection