@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
<div class="page-heading">
    <div><p class="eyebrow">SECURITY & GOVERNANCE</p><h1>Activity Logs</h1><p class="muted">A chronological audit trail of important actions in RecordRoom.</p></div>
</div>

<div class="panel">
    <form method="GET" action="{{ route('logs.index') }}" class="filter-bar">
        <input type="search" name="q" value="{{ $search }}" placeholder="Search user, action, description or IP">
        <select name="action"><option value="">All actions</option>@foreach($actions as $action)<option value="{{ $action }}" @selected($selectedAction === $action)>{{ $action }}</option>@endforeach</select>
        <input type="date" name="from" value="{{ request('from') }}" aria-label="From date">
        <input type="date" name="to" value="{{ request('to') }}" aria-label="To date">
        <button class="btn btn-primary" type="submit">Filter</button>
        <a class="btn btn-light" href="{{ route('logs.index') }}">Reset</a>
    </form>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Activity</th><th>User</th><th>IP Address</th><th>Date & Time</th></tr></thead>
            <tbody>
            @forelse($logs as $log)
                <tr>
                    <td><strong>{{ $log->action }}</strong><div class="muted small">{{ $log->description }}</div></td>
                    <td>{{ $log->user?->name ?? 'System / Deleted user' }}<div class="muted small">{{ $log->user?->email }}</div></td>
                    <td class="mono">{{ $log->ip_address ?? '—' }}</td>
                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty-state">No activity logs found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrap">{{ $logs->links() }}</div>
</div>
@endsection
