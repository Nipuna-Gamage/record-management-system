<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Record Room Management')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <nav>
        <ul>
            <li><a href="/">Dashboard</a></li>
            <li><a href="/records">Records</a></li>
            <li><a href="/categories">Categories</a></li>
            @if (Auth::user() && Auth::user()->role === 'super_admin')
                <li><a href="/users">Manage Users</a></li>
            @endif
            <li><a href="/logout">Logout</a></li>
        </ul>
    </nav>
    <div class="container">
        @yield('content')
    </div>
</body>
</html>
