@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<section class="page-heading">
    <div><p class="eyebrow">OVERVIEW</p><h1>Good day, {{ auth()->user()->name }}</h1><p class="muted">Welcome to your records workspace.</p></div>
</section>
<div class="notice-card"><div class="notice-icon">▤</div><div><strong>Your workspace foundation is ready</strong><p class="muted">Record registration, categories, document attachments and reporting will be added in the next development phases.</p></div></div>
<div class="section-heading"><h2>Getting started</h2><span class="muted small">Phase 1 · Foundation</span></div>
<div class="feature-grid">
    <article class="feature-card"><span class="feature-icon">⌂</span><h3>Secure access</h3><p class="muted">Authenticated access with role-aware accounts.</p><span class="tag">Implemented</span></article>
    <article class="feature-card"><span class="feature-icon">▤</span><h3>Record register</h3><p class="muted">Centralized document and record tracking.</p><span class="tag planned">Next phase</span></article>
    <article class="feature-card"><span class="feature-icon">⌕</span><h3>Search & reports</h3><p class="muted">Find records and generate operational reports.</p><span class="tag planned">Planned</span></article>
</div>
@endsection
