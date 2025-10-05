<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = User::with('roles');
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('email_verified_at');
            } elseif ($request->status === 'verified') {
                $query->whereNotNull('email_verified_at');
            }
        }

        $users = $query->latest()->paginate(15);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function authors(Request $request)
    {
        $query = User::role('author')->with(['roles', 'books']);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $authors = $query->latest()->paginate(15);

        return view('admin.users.authors', compact('authors'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'website' => $validated['website'] ?? null,
            'email_verified_at' => now(), // Admin created users are auto-verified
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully!');
    }

    public function show(User $user)
    {
        $user->load(['roles', 'books.walletTransactions', 'payouts']);
        
        // Calculate user statistics
        $stats = [
            'total_books' => $user->books->count(),
            'published_books' => $user->books->where('status', 'accepted')->count(),
            'pending_books' => $user->books->where('status', 'pending')->count(),
            'total_earnings' => $user->walletTransactions()->where('type', 'sale')->sum('amount'),
            'total_payouts' => $user->payouts->sum('amount_requested'),
            'pending_payouts' => $user->payouts->where('status', 'pending')->sum('amount_requested'),
        ];

        return view('admin.users.show', compact('user', 'stats'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|exists:roles,name',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
            'status' => 'required|in:active,suspended',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'bio' => $validated['bio'],
            'website' => $validated['website'],
        ]);

        // Update role if changed
        if (!$user->hasRole($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        // Handle suspension (you might want to add a suspended_at field to users table)
        if ($validated['status'] === 'suspended') {
            // Add suspension logic here
        }

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        // Prevent deleting the current admin
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        // Prevent deleting users with books or transactions
        if ($user->books()->count() > 0 || $user->walletTransactions()->count() > 0) {
            return back()->with('error', 'Cannot delete user with existing books or transactions!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }

    public function promoteToAuthor(User $user)
    {
        if ($user->hasRole('author')) {
            return back()->with('error', 'User is already an author!');
        }

        $user->assignRole('author');
        
        // Set author promotion date
        $user->update(['author_promoted_at' => now()]);

        return back()->with('success', 'User promoted to author successfully!');
    }

    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password reset successfully!');
    }
}
