@extends('layouts.app')

@section('title', 'Categories')

@section('content')
    <h1>Categories</h1>
    
    @if (Auth::user()->role === 'admin')
        <a href="/categories/create" class="btn btn-primary">Add New Category</a>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td>
                        <a href="/categories/{{ $category->id }}/edit" class="btn btn-warning">Edit</a>
                        <form action="/categories/{{ $category->id }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
