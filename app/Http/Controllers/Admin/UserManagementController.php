<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\UserService;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->middleware(['auth', 'role:admin']);
        $this->userService = $userService;
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

        // Exclude soft deleted users
        $query->whereNull('deleted_at');

        $users = $query->latest()->paginate(15);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function trashed(Request $request)
    {
        $query = User::onlyTrashed()->with('roles');
        
        // Search functionality for trashed users
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter for trashed users
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        $users = $query->latest('deleted_at')->paginate(15);
        $roles = Role::all();

        return view('admin.users.trashed', compact('users', 'roles'));
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

        // Exclude soft deleted users
        $query->whereNull('deleted_at');

        $authors = $query->latest()->paginate(15);

        return view('admin.users.authors', compact('authors'));
    }

    public function create()
    {
        $roles = $this->userService->getAllRoles();
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

        $this->userService->createUser($validated);

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
        $roles = $this->userService->getAllRoles();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'bio' => 'nullable|string|max:1000',
            'roles' => 'sometimes|array',
            'email_verified' => 'boolean'
        ]);

        $this->userService->updateUser($user, $validated);

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting the current admin
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        // Prevent deleting users with books or transactions
        if ($user->books()->count() > 0 || $user->walletTransactions()->count() > 0) {
            return back()->with('error', 'Cannot delete user with existing books or transactions!');
        }

        // Soft delete the user
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }

    public function restore($id)
    {
        // Find the user including soft deleted ones
        $user = User::withTrashed()->findOrFail($id);
        
        // Restore the user
        $user->restore();
        
        return back()->with('success', 'User restored successfully!');
    }

    public function promoteToAuthor(User $user)
    {
        if ($user->hasRole('author')) {
            return back()->with('error', 'User is already an author!');
        }

        $this->userService->promoteToAuthor($user);

        return back()->with('success', 'User promoted to author successfully!');
    }

    public function resetPassword(Request $request, User $user)
    {
        // Debug: Log that the method is being called
        \Illuminate\Support\Facades\Log::info('Reset password method called', [
            'user_id' => $user->id,
            'request_data' => $request->all(),
            'has_password' => $request->has('password'),
            'has_password_confirmation' => $request->has('password_confirmation')
        ]);
        
        try {
            $validated = $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            
            \Illuminate\Support\Facades\Log::info('Validation passed', [
                'validated_data' => $validated
            ]);

            $this->userService->resetPassword($user, $validated['password']);

            return back()->with('success', 'Password reset successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::error('Validation failed', [
                'errors' => $e->errors()
            ]);
            
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Password reset failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Failed to reset password: ' . $e->getMessage());
        }
    }

    public function sendVerificationEmail(User $user)
    {
        try {
            if ($user->email_verified_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already verified!'
                ]);
            }

            $user->sendEmailVerificationNotification();

            return response()->json([
                'success' => true,
                'message' => 'Verification email sent successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification email: ' . $e->getMessage()
            ], 500);
        }
    }

    public function loginAsUser(User $user)
    {
        try {
            // Store the original admin user ID in session
            session(['original_admin_id' => Auth::id()]);
            
            // Login as the target user
            Auth::login($user);

            return redirect()->route('dashboard')
                ->with('success', "You are now logged in as {$user->name}. You can switch back from the user menu.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to login as user: ' . $e->getMessage());
        }
    }
}