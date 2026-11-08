@extends('layouts.app')
@section('title', 'Reports & Analytics')
@section('content')
<div class="page-heading">
    <div class="eyebrow">INSIGHTS & EXPORTS</div>
    <div class="heading-row"><div><h1>Reports & Analytics</h1><p class="muted">Explore document activity and export filtered records.</p></div>
        <div class="report-actions"><a class="btn primary" href="{{ route('reports.export', request()->query()) }}">↓ Export Excel (CSV)</a><button class="btn light" type="button" onclick="window.print()">Print / Save PDF</button></div>
    </div>
</div>
<div class="content-panel report-filter-panel">
    <form method="GET" action="{{ route('reports.index') }}" class="report-filters">
        <div class="field"><label for="from">From date</label><input id="from" type="date" name="from" value="{{ $filters['from'] ?? '' }}"></div>
        <div class="field"><label for="to">To date</label><input id="to" type="date" name="to" value="{{ $filters['to'] ?? '' }}"></div>
        <div class="field"><label for="type">Document type</label><select id="type" name="type"><option value="">All types</option>@foreach(['incoming'=>'Incoming','outgoing'=>'Outgoing','internal'=>'Internal','other'=>'Other'] as $value=>$label)<option value="{{ $value }}" @selected(($filters['type'] ?? '') === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="field"><label for="status">Status</label><select id="status" name="status"><option value="">All statuses</option><option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option><option value="archived" @selected(($filters['status'] ?? '') === 'archived')>Archived</option></select></div>
        <div class="field"><label for="category_id">Category</label><select id="category_id" name="category_id"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string)($filters['category_id'] ?? '') === (string)$category->id)>{{ $category->name }}</option>@endforeach</select></div>
        <div class="field"><label for="department">Department</label><input id="department" name="department" value="{{ $filters['department'] ?? '' }}" placeholder="Any department"></div>
        <div class="report-filter-buttons"><button class="btn primary" type="submit">Apply filters</button><a class="btn light" href="{{ route('reports.index') }}">Reset</a></div>
    </form>
</div>
<div class="stats-grid report-stats">
    <div class="stat-card"><span>Matching records</span><strong>{{ number_format($total) }}</strong><small>Based on selected filters</small></div>
    <div class="stat-card"><span>Active records</span><strong>{{ number_format($active) }}</strong><small>Currently active</small></div>
    <div class="stat-card"><span>Archived records</span><strong>{{ number_format($archived) }}</strong><small>Archived documents</small></div>
</div>
<div class="report-grid">
    <section class="content-panel report-card"><h2>Document types</h2>
        @forelse($byType as $item)<div class="bar-row"><div class="bar-label"><span>{{ ucfirst($item->document_type) }}</span><strong>{{ $item->total }}</strong></div><div class="bar-track"><div class="bar-fill" style="width: {{ $total ? ($item->total / $total * 100) : 0 }}%"></div></div></div>@empty<p class="muted">No records match these filters.</p>@endforelse
    </section>
    <section class="content-panel report-card"><h2>Records by category</h2>
        @forelse($byCategory as $item)<div class="bar-row"><div class="bar-label"><span>{{ $item->category_name }}</span><strong>{{ $item->total }}</strong></div><div class="bar-track"><div class="bar-fill secondary-fill" style="width: {{ $item->total / $maxCategory * 100 }}%"></div></div></div>@empty<p class="muted">No category data available.</p>@endforelse
    </section>
</div>
<section class="content-panel monthly-panel"><h2>Monthly document activity</h2><div class="monthly-chart">
    @foreach($monthly as $month)<div class="month-column"><div class="month-value">{{ $month->total }}</div><div class="month-track"><div class="month-bar" style="height: {{ max(3, $month->total / $maxMonthly * 100) }}%"></div></div><small>{{ $month->label }}</small></div>@endforeach
</div></section>
<section class="content-panel report-records"><div class="report-table-heading"><h2>Matching records</h2><span class="muted small">{{ $records->total() }} results</span></div><div class="table-wrap"><table class="data-table"><thead><tr><th>REFERENCE</th><th>TITLE</th><th>TYPE</th><th>CATEGORY</th><th>DEPARTMENT</th><th>DATE</th><th>STATUS</th></tr></thead><tbody>
@forelse($records as $record)<tr><td><strong>{{ $record->reference_number }}</strong></td><td><a class="record-title" href="{{ route('records.show', $record) }}">{{ $record->title }}</a></td><td>{{ ucfirst($record->document_type) }}</td><td>{{ $record->category?->name ?? '—' }}</td><td>{{ $record->department ?: '—' }}</td><td>{{ $record->document_date?->format('d M Y') ?? '—' }}</td><td><span class="status-pill {{ $record->status }}">{{ ucfirst($record->status) }}</span></td></tr>@empty<tr><td colspan="7" class="empty-state">No matching records found.</td></tr>@endforelse
</tbody></table></div><div class="pagination-wrap">{{ $records->links() }}</div></section>
@endsection
