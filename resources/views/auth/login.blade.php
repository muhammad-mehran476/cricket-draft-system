@extends('layouts.app')
@section('title', 'Login')

@section('content')
@include('partials.navbar')

<div class="container py-5" style="max-width:480px;">
    <div class="card card-stadium p-4">
        <h3 class="font-heading text-center mb-4"><i class="fa-solid fa-cricket-bat-ball me-2"></i>Login</h3>

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <button type="submit" class="btn btn-stadium w-100">Login</button>
        </form>
        <p class="text-center mt-3 small">Don't have an account? <a href="{{ route('register') }}">Register here</a></p>

        <div class="alert alert-light border mt-3 small">
            <strong>Admin demo login:</strong><br>
            Email: admin@cdcms.com<br>
            Password: Admin@123
        </div>
    </div>
</div>

@include('partials.footer')
@endsection
