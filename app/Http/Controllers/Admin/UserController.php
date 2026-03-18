<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Only administrators can manage users.');
        }

        $query = User::query();

        // Filter by trashed status
        if ($request->get('trashed') === 'true') {
            $query->onlyTrashed();
        }

        // Search by name or email
        if ($request->has('search') && $request->get('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        $totalUsers = User::count();
        $trashedUsers = User::onlyTrashed()->count();

        return view('admin.users.index', compact('users', 'totalUsers', 'trashedUsers'));
    }

    public function create(): View
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Only administrators can manage users.');
        }

        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Only administrators can manage users.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:user,admin,moderator',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully!');
    }

    public function edit(User $user): View
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Only administrators can manage users.');
        }

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Only administrators can manage users.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'role' => 'required|in:user,admin,moderator',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Only administrators can manage users.');
        }

        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User moved to trash!');
    }

    public function restore($id): RedirectResponse
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Only administrators can manage users.');
        }

        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('admin.users.index')
            ->with('success', 'User restored successfully!');
    }

    public function forceDelete($id): RedirectResponse
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Only administrators can manage users.');
        }

        $user = User::onlyTrashed()->findOrFail($id);

        // Prevent admin from permanently deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index', ['trashed' => true])
                ->with('error', 'You cannot permanently delete your own account!');
        }

        $user->forceDelete();

        return redirect()->route('admin.users.index', ['trashed' => true])
            ->with('success', 'User permanently deleted!');
    }

    public function changePassword(Request $request, User $user): JsonResponse
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully for '.$user->name.'!'
        ]);
    }
}
