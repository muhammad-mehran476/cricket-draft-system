@extends('layouts.admin')
@section('title', 'Create Category')

@section('admin-content')
<a href="{{ route('admin.categories.index') }}" class="btn btn-link mb-3">&larr; Back to Categories</a>

<div class="card card-stadium p-4" style="max-width:600px;">
    <h4 class="font-heading mb-4">Create Draft Category</h4>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        @include('admin.categories._form', ['category' => null])
        <button type="submit" class="btn btn-stadium w-100 mt-3">Create Category</button>
    </form>
</div>
@endsection
