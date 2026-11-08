@extends('layouts.app')
@section('title', 'Users')
@section('content')
<div class="page-heading"><p class="eyebrow">ACCESS CONTROL</p><div class="heading-row"><div><h1>Users</h1><p class="muted">Create accounts and manage system permissions.</p></div></div></div>
<div class="content-panel"><h2>Create user</h2>
<form method="POST" action="{{ route('users.store') }}" class="user-create-grid">@csrf
<label>Full name<input name="name" required maxlength="120" value="{{ old('name') }}"></label>
<label>Email address<input type="email" name="email" required value="{{ old('email') }}"></label>
<label>Role<select name="role" required><option value="viewer">Viewer</option><option value="staff">Staff</option><option value="manager">Manager</option><option value="admin">Admin</option></select></label>
<label>Password (min. 10 characters)<input type="password" name="password" required minlength="10"></label>
<label>Confirm password<input type="password" name="password_confirmation" required minlength="10"></label>
<div><button class="btn primary">Create user</button></div>
</form></div>
<div class="content-panel" style="margin-top:20px"><div class="table-wrap"><table class="data-table"><thead><tr><th>User</th><th>Role</th><th>Status</th><th>Update account</th><th>Access</th></tr></thead><tbody>
@foreach($users as $user)
<tr><td><strong>{{ $user->name }}</strong><small>{{ $user->email }}</small></td><td>{{ ucfirst($user->role) }}</td><td>{{ $user->is_active ? 'Active' : 'Inactive' }}</td><td>
<form method="POST" action="{{ route('users.update',$user) }}" class="inline-form">@csrf @method('PUT')
<input name="name" value="{{ $user->name }}" required aria-label="Name">
<input type="email" name="email" value="{{ $user->email }}" required aria-label="Email">
<select name="role" aria-label="Role"><option value="viewer" @selected($user->role==='viewer')>Viewer</option><option value="staff" @selected($user->role==='staff')>Staff</option><option value="manager" @selected($user->role==='manager')>Manager</option><option value="admin" @selected($user->role==='admin')>Admin</option></select>
<input type="password" name="password" placeholder="New password (optional)" minlength="10">
<input type="password" name="password_confirmation" placeholder="Confirm new password">
<button class="btn light">Save</button></form></td><td>
@if(!$user->is(auth()->user()))
<form method="POST" action="{{ route('users.toggle-active',$user) }}">@csrf @method('PATCH')<button class="btn {{ $user->is_active ? 'light' : 'primary' }}">{{ $user->is_active ? 'Deactivate' : 'Activate' }}</button></form>
@else<span class="muted small">Current account</span>@endif
</td></tr>
@endforeach
</tbody></table></div><div class="pagination-wrap">{{ $users->links() }}</div></div>
@endsection
