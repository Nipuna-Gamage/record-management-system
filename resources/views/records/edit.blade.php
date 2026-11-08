@extends('layouts.app')
@section('title','Edit record')
@section('content')
<div class="page-heading"><p class="eyebrow">DOCUMENT REGISTER</p><h1>Edit record</h1><p class="muted">{{ $record->reference_number }}</p></div>
<div class="content-panel form-panel"><form method="POST" action="{{ route('records.update',$record) }}">@csrf @method('PUT')
<div class="form-grid">
    <div class="field full-field"><label for="title">Document title <span>*</span></label><input id="title" name="title" value="{{ old('title', $record?->title) }}" required maxlength="200" placeholder="Enter a clear document title">@error('title')<small class="field-error">{{ $message }}</small>@enderror</div>
    <div class="field"><label for="document_type">Document type <span>*</span></label><select id="document_type" name="document_type" required>@foreach(['incoming'=>'Incoming','outgoing'=>'Outgoing','internal'=>'Internal','other'=>'Other'] as $key=>$label)<option value="{{ $key }}" @selected(old('document_type',$record?->document_type ?? 'internal')===$key)>{{ $label }}</option>@endforeach</select></div>
    <div class="field"><label for="category_id">Category</label><select id="category_id" name="category_id"><option value="">Uncategorized</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string)old('category_id',$record?->category_id)===(string)$category->id)>{{ $category->name }}</option>@endforeach</select></div>
    <div class="field"><label for="department">Department</label><input id="department" name="department" value="{{ old('department',$record?->department) }}" maxlength="120" placeholder="e.g. Administration"></div>
    <div class="field"><label for="document_date">Document date</label><input id="document_date" type="date" name="document_date" value="{{ old('document_date',$record?->document_date?->format('Y-m-d')) }}"></div>
    <div class="field full-field"><label for="description">Description / Notes</label><textarea id="description" name="description" rows="5" maxlength="10000" placeholder="Add relevant details…">{{ old('description',$record?->description) }}</textarea></div>
</div>

<div class="form-actions"><a class="btn light" href="{{ route('records.show',$record) }}">Cancel</a><button class="btn primary" type="submit">Update record</button></div></form></div>
@endsection
