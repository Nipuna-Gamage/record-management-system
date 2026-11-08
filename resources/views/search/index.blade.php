@extends('layouts.app')
@section('title', 'Search Results')
@section('content')
<div class="page-heading">
    <p class="eyebrow">GLOBAL SEARCH</p>
    <div class="heading-row"><div><h1>Search Results</h1><p>Showing matches for <strong>{{ $term }}</strong></p></div><a class="btn light" href="{{ route('records.index', ['q' => $term]) }}">Open advanced records search</a></div>
</div>
<div class="content-panel">
    <form class="filter-bar global-search-form" method="GET" action="{{ route('search') }}">
        <input type="search" name="q" value="{{ $term }}" minlength="2" maxlength="100" required aria-label="Search records" placeholder="Search reference, title, description, department or category…">
        <button class="btn" type="submit">Search</button>
    </form>
    <div class="table-wrap"><table class="data-table"><thead><tr><th>REFERENCE</th><th>DOCUMENT</th><th>CATEGORY</th><th>DEPARTMENT</th><th>DATE</th><th>STATUS</th><th></th></tr></thead><tbody>
    @forelse($records as $record)
        <tr><td><strong>{{ $record->reference_number }}</strong></td><td><a class="record-title" href="{{ route('records.show', $record) }}">{{ $record->title }}</a><small>{{ ucfirst($record->document_type) }}</small></td><td>{{ $record->category?->name ?? '—' }}</td><td>{{ $record->department ?: '—' }}</td><td>{{ $record->document_date?->format('d M Y') ?? '—' }}</td><td><span class="status-pill {{ $record->status }}">{{ ucfirst($record->status) }}</span></td><td><a class="table-action" href="{{ route('records.show', $record) }}">Open</a></td></tr>
    @empty
        <tr><td colspan="7" class="empty-state">No matching records found. Try another reference number or keyword.</td></tr>
    @endforelse
    </tbody></table></div>
    <div class="pagination-wrap">{{ $records->links() }}</div>
</div>
@endsection
