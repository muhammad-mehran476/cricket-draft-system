@extends('layouts.app')
@section('title', 'Team Registration')

@section('content')
@include('partials.navbar')

<div class="container py-5" style="max-width:700px;">
    <div class="card card-stadium p-4">
        <h3 class="font-heading mb-4"><i class="fa-solid fa-people-group me-2"></i>Team Registration</h3>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('team.register.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Team Name</label>
                    <input type="text" name="team_name" class="form-control" value="{{ old('team_name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Captain Name</label>
                    <input type="text" name="captain_name" class="form-control" value="{{ old('captain_name') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Team Logo</label>
                    <input type="file" name="team_logo" class="form-control" accept="image/*" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="2" required>{{ old('address') }}</textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Captain Photo</label>
                    <input type="file" name="captain_image" class="form-control" accept="image/*" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Payment Slip (PDF/Image)</label>
                    <input type="file" name="payment_slip" class="form-control" accept=".pdf,image/*" required>
                </div>
            </div>

            <button type="submit" class="btn btn-stadium w-100 mt-4">Submit Team Registration</button>
        </form>
    </div>
</div>

@include('partials.footer')
@endsection
