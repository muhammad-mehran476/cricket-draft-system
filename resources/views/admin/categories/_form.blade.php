<div class="mb-3">
    <label class="form-label">Category Name</label>
    <input type="text" name="name" class="form-control"
           value="{{ old('name', $category?->name) }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Description <small class="text-muted">(optional)</small></label>
    <textarea name="description" class="form-control" rows="2">{{ old('description', $category?->description) }}</textarea>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Max Players in Category</label>
        <input type="number" name="max_players" class="form-control"
               value="{{ old('max_players', $category?->max_players ?? 10) }}" min="1" max="100" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Draft Order <small class="text-muted">(1 = first)</small></label>
        <input type="number" name="draft_order" class="form-control"
               value="{{ old('draft_order', $category?->draft_order ?? 0) }}" min="0" required>
    </div>
</div>

<div class="mt-3 form-check">
    <input type="checkbox" name="is_active" class="form-check-input" id="isActive" value="1"
           {{ old('is_active', $category?->is_active ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="isActive">Active (included in draft)</label>
</div>
