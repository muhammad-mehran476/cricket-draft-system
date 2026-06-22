@extends('layouts.app')
@section('title', 'Player Registration')

@section('content')
@include('partials.navbar')

<div class="container py-5" style="max-width:700px;">
    <div class="card card-stadium p-4">
        <h3 class="font-heading mb-4"><i class="fa-solid fa-person-running me-2"></i>Player Registration</h3>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('player.register.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Profile Picture</label>
                    <input type="file" name="profile_picture" class="form-control" accept="image/*" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="2" required>{{ old('address') }}</textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Playing Role</label>
                    <select name="role" class="form-select" required>
                        <option value="">Select role</option>
                        <option value="batsman">Batsman</option>
                        <option value="bowler">Bowler</option>
                        <option value="all_rounder">All Rounder</option>
                        <option value="wicket_keeper">Wicket Keeper</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Skill Level</label>
                    <select name="skill_level" class="form-select" required>
                        <option value="good">Good</option>
                        <option value="better">Better</option>
                        <option value="best">Best</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Batting Style</label>
                    <select name="batting_style" class="form-select" required>
                        <option value="right_hand">Right Handed</option>
                        <option value="left_hand">Left Handed</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Bowling Type</label>
                    <select name="bowling_type" class="form-select" required>
                        <option value="none">None</option>
                        <option value="fast">Fast</option>
                        <option value="medium">Medium</option>
                        <option value="spin">Spin</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Payment Slip (PDF/Image)</label>
                    <input type="file" name="payment_slip" class="form-control" accept=".pdf,image/*" required>
                </div>

                <div class="col-12 form-check mt-2">
                    <input type="checkbox" name="rules_accepted" class="form-check-input" id="rulesCheck" required>
                    <label class="form-check-label" for="rulesCheck">
                        I have read and accept the <a href="{{ route('rules') }}" target="_blank">tournament rules</a>.
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-stadium w-100 mt-4">Submit Registration</button>
        </form>
    </div>
</div>

@include('partials.footer')
@endsection
