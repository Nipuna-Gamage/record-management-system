<?php

namespace App\Http\Controllers;

use App\Support\ActivityLogger;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('categories.index', ['categories' => Category::withCount('records')->orderBy('name')->paginate(15)]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $category = Category::create($data);
        ActivityLogger::log('category.created', 'Created category '.$category->name, $category);
        return back()->with('success', 'Category created successfully.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $this->validated($request, $category);
        $data['slug'] = $this->uniqueSlug($data['name'], $category->id);
        $category->update($data);
        ActivityLogger::log('category.updated', 'Updated category '.$category->name, $category);
        return back()->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->records()->exists()) {
            return back()->withErrors(['category' => 'This category is used by records. Deactivate it instead.']);
        }
        ActivityLogger::log('category.deleted', 'Deleted category '.$category->name, $category);
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('categories', 'name')->ignore($category?->id)],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;
        while (Category::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base . '-' . ++$i;
        }
        return $slug;
    }
}
