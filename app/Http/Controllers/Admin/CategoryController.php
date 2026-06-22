<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount([
            'players as total_players',
            'players as approved_players' => fn($q) => $q->where('status', 'approved'),
            'players as drafted_players'  => fn($q) => $q->where('status', 'drafted'),
        ])->orderBy('draft_order')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:191|unique:categories,name',
            'description' => 'nullable|string',
            'max_players' => 'required|integer|min:1|max:100',
            'draft_order' => 'required|integer|min:0',
        ]);

        $category = Category::create([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
            'max_players' => $request->max_players,
            'draft_order' => $request->draft_order,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        AuditLog::record('category_created', "Category created: {$category->name}", $category);
        return redirect()->route('admin.categories.index')
            ->with('success', "Category '{$category->name}' created.");
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'        => 'required|string|max:191|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'max_players' => 'required|integer|min:1|max:100',
            'draft_order' => 'required|integer|min:0',
        ]);

        $old = $category->toArray();
        $category->update([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
            'max_players' => $request->max_players,
            'draft_order' => $request->draft_order,
            'is_active'   => $request->boolean('is_active'),
        ]);

        AuditLog::record('category_updated', "Category updated: {$category->name}", $category, $old, $category->fresh()->toArray());
        return redirect()->route('admin.categories.index')
            ->with('success', "Category '{$category->name}' updated.");
    }

    public function destroy(Category $category)
    {
        if ($category->players()->count() > 0) {
            return back()->with('error', 'Cannot delete a category that has assigned players.');
        }
        $name = $category->name;
        $category->delete();
        AuditLog::record('category_deleted', "Category deleted: {$name}");
        return redirect()->route('admin.categories.index')->with('success', "Category '{$name}' deleted.");
    }

    public function toggleActive(Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);
        $state = $category->is_active ? 'activated' : 'deactivated';
        AuditLog::record('category_toggled', "Category {$state}: {$category->name}", $category);
        return back()->with('success', "Category '{$category->name}' {$state}.");
    }
}
