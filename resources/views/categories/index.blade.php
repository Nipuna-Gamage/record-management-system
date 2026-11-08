@extends('layouts.app')
@section('title', 'Categories')
@section('content')
<div class="page-heading"><p class="eyebrow">ORGANIZE DOCUMENTS</p><div class="heading-row"><div><h1>Categories</h1><p class="muted">Create and maintain document categories.</p></div></div></div>
<div class="content-panel">
    <h2>Add category</h2>
    <form method="POST" action="{{ route('categories.store') }}" class="filter-bar">@csrf
        <input name="name" required maxlength="120" placeholder="Category name" value="{{ old('name') }}">
        <input name="description" placeholder="Description (optional)" value="{{ old('description') }}">
        <label class="check"><input type="checkbox" name="is_active" value="1" checked> Active</label>
        <button class="btn primary">Add category</button>
    </form>
</div>
<div class="content-panel" style="margin-top:20px"><div class="table-wrap"><table class="data-table"><thead><tr><th>Category</th><th>Description</th><th>Records</th><th>Status</th><th>Update</th><th></th></tr></thead><tbody>
@forelse($categories as $category)
<tr><td><strong>{{ $category->name }}</strong></td><td>{{ $category->description ?: '—' }}</td><td>{{ $category->records_count }}</td><td>{{ $category->is_active ? 'Active' : 'Inactive' }}</td><td>
<form method="POST" action="{{ route('categories.update',$category) }}" class="inline-form">@csrf @method('PUT')
<input name="name" value="{{ $category->name }}" required aria-label="Category name">
<input name="description" value="{{ $category->description }}" aria-label="Description">
<label><input type="checkbox" name="is_active" value="1" @checked($category->is_active)> Active</label>
<button class="btn light">Save</button></form></td><td>
@if($category->records_count === 0)<form method="POST" action="{{ route('categories.destroy',$category) }}" onsubmit="return confirm('Delete this category?')">@csrf @method('DELETE')<button class="table-action danger-text">Delete</button></form>@else<span class="muted small">In use</span>@endif
</td></tr>
@empty<tr><td colspan="6" class="empty-state">No categories found.</td></tr>@endforelse
</tbody></table></div><div class="pagination-wrap">{{ $categories->links() }}</div></div>
@endsection
