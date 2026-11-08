@extends('layouts.app')
@section('title', 'Record details')
@section('content')
<div class="page-heading"><p class="eyebrow">DOCUMENT DETAILS</p><div class="heading-row"><div><h1>{{ $record->title }}</h1><p class="muted">{{ $record->reference_number }}</p></div><div class="action-row">@if($record->status==='active')<a class="btn light" href="{{ route('records.edit',$record) }}">Edit</a><form method="POST" action="{{ route('records.archive',$record) }}" onsubmit="return confirm('Archive this record?')">@csrf @method('PATCH')<button class="btn danger" type="submit">Archive</button></form>@else<form method="POST" action="{{ route('records.restore',$record) }}">@csrf @method('PATCH')<button class="btn primary" type="submit">Restore record</button></form>@endif</div></div></div>
<div class="content-panel detail-panel"><div class="detail-grid">
<div><small>DOCUMENT TYPE</small><strong>{{ ucfirst($record->document_type) }}</strong></div><div><small>CATEGORY</small><strong>{{ $record->category?->name ?? 'Uncategorized' }}</strong></div><div><small>DEPARTMENT</small><strong>{{ $record->department ?: '—' }}</strong></div><div><small>DOCUMENT DATE</small><strong>{{ $record->document_date?->format('d M Y') ?? '—' }}</strong></div><div><small>STATUS</small><strong><span class="status-pill {{ $record->status }}">{{ ucfirst($record->status) }}</span></strong></div><div><small>CREATED BY</small><strong>{{ $record->creator?->name ?? '—' }}</strong></div><div class="full-field"><small>DESCRIPTION / NOTES</small><p class="description-text">{{ $record->description ?: 'No description provided.' }}</p></div></div>
<div class="detail-footer"><span>Created {{ $record->created_at->format('d M Y, H:i') }}</span><span>Last updated {{ $record->updated_at->format('d M Y, H:i') }}</span></div></div>
<section class="content-panel" style="margin-top:20px">
    <div class="heading-row" style="margin-bottom:16px">
        <div><p class="eyebrow">ATTACHMENTS</p><h2>Files & documents</h2><p class="muted">Private files linked to this record.</p></div>
        <span class="status-pill">{{ $record->attachments->count() }} files</span>
    </div>
    @if($record->status === 'active' && in_array(auth()->user()->role, ['admin','manager','staff'], true))
        <form method="POST" action="{{ route('records.attachments.store', $record) }}" enctype="multipart/form-data" class="form-grid" style="margin-bottom:20px">
            @csrf
            <div class="full-field">
                <label for="files">Upload files</label>
                <input id="files" type="file" name="files[]" multiple required accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                <small class="muted">PDF, DOC, DOCX, XLS, XLSX, JPG or PNG. Maximum 10 MB per file; up to 10 files per upload.</small>
                @error('files')<p class="field-error">{{ $message }}</p>@enderror
                @error('files.*')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            <div class="full-field"><button class="btn primary" type="submit">Upload files</button></div>
        </form>
    @endif
    @if($record->attachments->isEmpty())
        <p class="muted">No files attached yet.</p>
    @else
        <div class="table-wrap"><table><thead><tr><th>File</th><th>Size</th><th>Uploaded by</th><th>Date</th><th>Actions</th></tr></thead><tbody>
        @foreach($record->attachments as $attachment)
            <tr>
                <td><strong>{{ $attachment->original_name }}</strong><br><small class="muted">{{ $attachment->mime_type ?: 'Unknown type' }}</small></td>
                <td>{{ number_format($attachment->size_bytes / 1024, 1) }} KB</td>
                <td>{{ $attachment->uploader?->name ?? '—' }}</td>
                <td>{{ $attachment->created_at->format('d M Y') }}</td>
                <td><div class="action-row">
                    <a class="btn light" href="{{ route('records.attachments.download', [$record, $attachment]) }}">Download</a>
                    @if($record->status === 'active' && in_array(auth()->user()->role, ['admin','manager','staff'], true))
                        <form method="POST" action="{{ route('records.attachments.destroy', [$record, $attachment]) }}" onsubmit="return confirm('Delete this attachment permanently?')">@csrf @method('DELETE')<button class="btn danger" type="submit">Delete</button></form>
                    @endif
                </div></td>
            </tr>
        @endforeach
        </tbody></table></div>
    @endif
</section>
@endsection
