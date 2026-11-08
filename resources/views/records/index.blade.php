@extends('layouts.app')
@section('title', 'Records')
@section('content')
<div class="page-heading">
    <p class="eyebrow">DOCUMENT REGISTER</p>
    <div class="heading-row"><div><h1>Records</h1><p class="muted">Register, find and manage organizational documents.</p></div><a class="btn primary" href="{{ route('records.create') }}">＋ New record</a></div>
</div>
<div class="content-panel">
    <form class="filter-bar" method="GET" action="{{ route('records.index') }}">
        <input name="q" value="{{ $search }}" placeholder="Search reference, title or department…">
        <select name="type"><option value="">All types</option>@foreach(['incoming'=>'Incoming','outgoing'=>'Outgoing','internal'=>'Internal','other'=>'Other'] as $key=>$label)<option value="{{ $key }}" @selected($type===$key)>{{ $label }}</option>@endforeach</select>
        <select name="status"><option value="active" @selected($status==='active')>Active</option><option value="archived" @selected($status==='archived')>Archived</option><option value="" @selected($status==='')>All</option></select>
        <button class="btn primary" type="submit">Filter</button>
        <a class="btn light" href="{{ route('records.index') }}">Reset</a>
    </form>
    <div class="table-wrap"><table class="data-table"><thead><tr><th>Reference</th><th>Title</th><th>Type</th><th>Category</th><th>Date</th><th>Status</th><th></th></tr></thead><tbody>
    @forelse($records as $record)
        <tr><td><strong>{{ $record->reference_number }}</strong></td><td><a class="record-title" href="{{ route('records.show',$record) }}">{{ $record->title }}</a><small>{{ $record->department ?: 'No department' }}</small></td><td><span class="type-pill">{{ ucfirst($record->document_type) }}</span></td><td>{{ $record->category?->name ?? '—' }}</td><td>{{ $record->document_date?->format('d M Y') ?? '—' }}</td><td><span class="status-pill {{ $record->status }}">{{ ucfirst($record->status) }}</span></td><td><a class="table-action" href="{{ route('records.show',$record) }}">View →</a></td></tr>
    @empty<tr><td colspan="7" class="empty-state">No records found. Create your first record to get started.</td></tr>@endforelse
    </tbody></table></div>
    <div class="pagination-wrap">{{ $records->links() }}</div>
</div>
@endsection
