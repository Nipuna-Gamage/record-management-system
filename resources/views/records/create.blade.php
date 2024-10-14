@extends('layouts.app')

@section('title', 'Add New Record')

@section('content')
    <h1>Add New Record</h1>

    <form action="/records" method="POST">
        @csrf
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>

        <div class="form-group">
            <label for="category">Category</label>
            <select name="category_id" class="form-control" required>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" class="form-control">
                <option value="available">Available</option>
                <option value="checked_out">Checked Out</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Add Record</button>
    </form>
@endsection
