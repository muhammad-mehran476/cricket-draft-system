@extends('layouts.app')
@section('title', 'Edit Profile')

@section('content')
@include('partials.navbar')

<div class="container py-5" style="max-width:650px;">
    <div class="card card-stadium p-4">
        <h3 class="font-heading mb-4"><i class="fa-solid fa-pen me-2"></i>Edit Profile</h3>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('player.profile.update') }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $player->phone) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">City</label>
                <input type="text" name="city" class="form-control" value="{{ old('city', $player->city) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2" required>{{ old('address', $player->address) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Bowling Type</label>
                <select name="bowling_type" class="form-select" required>
                    @foreach(['none'=>'None','fast'=>'Fast','medium'=>'Medium','spin'=>'Spin'] as $val => $label)
                        <option value="{{ $val }}" {{ $player->bowling_type === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Batting Style</label>
                <select name="batting_style" class="form-select" required>
                    <option value="right_hand" {{ $player->batting_style === 'right_hand' ? 'selected' : '' }}>Right Handed</option>
                    <option value="left_hand" {{ $player->batting_style === 'left_hand' ? 'selected' : '' }}>Left Handed</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Profile Picture</label>
                <input type="file" name="profile_picture" class="form-control" accept="image/*">
                <small class="text-muted">Leave blank to keep current picture.</small>
            </div>

            <p class="small text-muted">Note: Name, role, skill level, and category are locked once submitted and can only be changed by an admin.</p>

            <button type="submit" class="btn btn-stadium w-100">Save Changes</button>
        </form>
    </div>
</div>

@include('partials.footer')
@endsection
