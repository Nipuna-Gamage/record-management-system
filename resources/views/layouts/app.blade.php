<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') · RecordRoom</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <a class="brand" href="{{ route('dashboard') }}"><span class="brand-mark small-mark">RR</span><span><strong>RecordRoom</strong><small>DOCUMENT MANAGEMENT</small></span></a>
        <p class="nav-label">WORKSPACE</p>
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">▦ <span>Dashboard</span></a>
        <a class="nav-link {{ request()->routeIs('records.*') ? 'active' : '' }}" href="{{ route('records.index') }}">▤ <span>Records</span></a>
        @if(in_array(auth()->user()->role, ['admin', 'manager']))
            <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">▦ <span>Categories</span></a>
        @endif
        @if(auth()->user()->role === 'admin')
            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">♙ <span>Users</span></a>
        @endif
        <div class="sidebar-bottom">
            <div class="user-chip"><span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><span><strong>{{ auth()->user()->name }}</strong><small>{{ str_replace('_', ' ', ucfirst(auth()->user()->role)) }}</small></span></div>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">↪ Sign out</button></form>
        </div>
    </aside>
    <main class="main-content">
        <header class="topbar"><span>Workspace / @yield('title', 'Dashboard')</span><span class="status-dot">● System online</span></header>
        @if (session('success'))<div class="success">{{ session('success') }}</div>@endif
        @if ($errors->any())<div class="alert validation-alert">Please check the form: {{ $errors->first() }}</div>@endif
        @yield('content')
    </main>
</div>
</body></html>
