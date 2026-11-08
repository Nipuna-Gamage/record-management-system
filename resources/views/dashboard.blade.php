@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<section class="page-heading"><p class="eyebrow">OVERVIEW</p><h1>Good day, {{ auth()->user()->name }}</h1><p class="muted">Your document workspace at a glance.</p></section>
<div class="stats-grid"><article class="stat-card"><span>Active records</span><strong>{{ $totalRecords ?? 0 }}</strong><small>Currently in the register</small></article><article class="stat-card"><span>Archived records</span><strong>{{ $archivedRecords ?? 0 }}</strong><small>Stored for reference</small></article><article class="stat-card"><span>Quick action</span><strong class="stat-action"><a href="{{ route('records.create') }}">＋ Add record</a></strong><small>Register a new document</small></article></div>
<div class="section-heading"><h2>Recently added</h2><a class="table-action" href="{{ route('records.index') }}">View all →</a></div>
<div class="content-panel table-wrap"><table class="data-table"><thead><tr><th>Reference</th><th>Title</th><th>Category</th><th>Added</th></tr></thead><tbody>
@forelse($recentRecords ?? [] as $record)<tr><td><strong>{{ $record->reference_number }}</strong></td><td><a class="record-title" href="{{ route('records.show',$record) }}">{{ $record->title }}</a></td><td>{{ $record->category?->name ?? '—' }}</td><td>{{ $record->created_at->format('d M Y') }}</td></tr>@empty<tr><td colspan="4" class="empty-state">No records yet. <a href="{{ route('records.create') }}">Create the first record.</a></td></tr>@endforelse
</tbody></table></div>
@endsection
