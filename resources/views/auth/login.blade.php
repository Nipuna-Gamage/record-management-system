<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sign in · RecordRoom</title><link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="auth-shell">
<main class="auth-card">
    <div class="brand-mark">RR</div>
    <p class="eyebrow">RECORD MANAGEMENT</p>
    <h1>Welcome back</h1><p class="muted">Sign in to access your document workspace.</p>
    @if ($errors->any()) <div class="alert">{{ $errors->first() }}</div> @endif
    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <label for="email">Email address</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
        <label class="check"><input type="checkbox" name="remember" value="1"> Remember me</label>
        <button class="btn primary full" type="submit">Sign in</button>
    </form>
    <p class="muted small">Contact your system administrator if you need an account.</p>
</main>
</body></html>
