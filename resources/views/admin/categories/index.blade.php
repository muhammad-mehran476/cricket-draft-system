@extends('layouts.admin')
@section('title', 'Draft Categories')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="font-heading mb-0">Draft Categories</h3>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-stadium">
        <i class="fa-solid fa-plus me-2"></i>Add Category
    </a>
</div>

<div class="card card-stadium p-3">
    <table class="table table-stadium table-hover align-middle mb-0">
        <thead>
            <tr>
                <th>Order</th><th>Category</th><th>Max Players</th>
                <th>Available</th><th>Drafted</th><th>Status</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $cat)
            <tr>
                <td><span class="badge bg-secondary">{{ $cat->draft_order }}</span></td>
                <td>
                    <strong>{{ $cat->name }}</strong>
                    @if($cat->description)
                        <br><small class="text-muted">{{ $cat->description }}</small>
                    @endif
                </td>
                <td>{{ $cat->max_players }}</td>
                <td>
                    <span class="badge bg-success">{{ $cat->approved_players ?? 0 }}</span>
                </td>
                <td>
                    <span class="badge bg-info">{{ $cat->drafted_players ?? 0 }}</span>
                </td>
                <td>
                    @if($cat->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.categories.edit', $cat) }}"
                           class="btn btn-outline-secondary btn-sm" title="Edit">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <form action="{{ route('admin.categories.toggle', $cat) }}" method="POST">
                            @csrf
                            <button class="btn btn-outline-warning btn-sm"
                                    title="{{ $cat->is_active ? 'Deactivate' : 'Activate' }}">
                                <i class="fa-solid fa-{{ $cat->is_active ? 'eye-slash' : 'eye' }}"></i>
                            </button>
                        </form>
                        <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST"
                              onsubmit="return confirm('Delete this category? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm" title="Delete">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted py-4">No categories found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
