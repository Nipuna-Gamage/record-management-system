@extends('layouts.app')

@section('title', 'Edit Record')

@section('content')
    <h1>Edit Record</h1>

    <form action="/records/{{ $record->id }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" class="form-control" value="{{ $record->title }}" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" class="form-control" required>{{ $record->description }}</textarea>
        </div>

        <div class="form-group">
            <label for="category">Category</label>
            <select name="category_id" class="form-control" required>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ $category->id == $record->category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" class="form-control">
                <option value="available" {{ $record->status == 'available' ? 'selected' : '' }}>Available</option>
                <option value="checked_out" {{ $record->status == 'checked_out' ? 'selected' : '' }}>Checked Out</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Record</button>
    </form>
@endsection
