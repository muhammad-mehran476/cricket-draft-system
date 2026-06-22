@extends('layouts.app')
@section('title', 'Register')

@section('content')
@include('partials.navbar')

<div class="container py-5" style="max-width:520px;">
    <div class="card card-stadium p-4">
        <h3 class="font-heading text-center mb-4"><i class="fa-solid fa-user-plus me-2"></i>Create Account</h3>

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required minlength="8">
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <div class="mb-4">
                <label class="form-label d-block">I am registering as a:</label>
                <div class="d-flex gap-3">
                    <div class="form-check flex-fill border rounded p-3">
                        <input class="form-check-input" type="radio" name="role" id="rolePlayer" value="player"
                               {{ request('role') !== 'team_captain' ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="rolePlayer">
                            <i class="fa-solid fa-person-running me-1"></i> Player
                        </label>
                    </div>
                    <div class="form-check flex-fill border rounded p-3">
                        <input class="form-check-input" type="radio" name="role" id="roleTeam" value="team_captain"
                               {{ request('role') === 'team_captain' ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="roleTeam">
                            <i class="fa-solid fa-people-group me-1"></i> Team Captain
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-stadium w-100">Create Account</button>
        </form>
        <p class="text-center mt-3 small">Already have an account? <a href="{{ route('login') }}">Login here</a></p>
    </div>
</div>

@include('partials.footer')
@endsection
