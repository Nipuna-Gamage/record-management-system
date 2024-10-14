@extends('layouts.app')

@section('title', 'Records')

@section('content')
    <h1>All Records</h1>
    
    @if (Auth::user()->role === 'admin' || Auth::user()->role === 'record_keeper')
        <a href="/records/create" class="btn btn-primary">Add New Record</a>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Category</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $record)
                <tr>
                    <td>{{ $record->title }}</td>
                    <td>{{ $record->description }}</td>
                    <td>{{ $record->category->name }}</td>
                    <td>{{ $record->status }}</td>
                    <td>
                        <a href="/records/{{ $record->id }}" class="btn btn-info">View</a>
                        @if (Auth::user()->role === 'admin' || Auth::user()->role === 'record_keeper')
                            <a href="/records/{{ $record->id }}/edit" class="btn btn-warning">Edit</a>
                            <form action="/records/{{ $record->id }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
