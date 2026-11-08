<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('users.index', ['users' => User::orderBy('name')->paginate(15)]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:10', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'manager', 'staff', 'viewer'])],
        ]);
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = true;
        User::create($data);
        return back()->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'manager', 'staff', 'viewer'])],
            'password' => ['nullable', 'string', 'min:10', 'confirmed'],
        ]);
        if ($user->is($request->user()) && $data['role'] !== 'admin') {
            return back()->withErrors(['role' => 'You cannot remove your own administrator role.']);
        }
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        return back()->with('success', 'User updated successfully.');
    }

    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        abort_if($user->is($request->user()), 422, 'You cannot deactivate your own account.');
        if ($user->role === 'admin' && $user->is_active && User::where('role', 'admin')->where('is_active', true)->count() <= 1) {
            return back()->withErrors(['user' => 'At least one active administrator must remain.']);
        }
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'User status updated.');
    }
}
