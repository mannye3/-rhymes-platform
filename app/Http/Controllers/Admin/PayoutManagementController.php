<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payout;
use App\Services\Admin\PayoutManagementService;

class PayoutManagementController extends Controller
{
    public function __construct(
        private PayoutManagementService $payoutManagementService
    ) {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = Payout::with(['user']);
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($userQuery) use ($search) {
                $userQuery->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('amount_min')) {
            $query->where('amount_requested', '>=', $request->amount_min);
        }
        
        if ($request->filled('amount_max')) {
            $query->where('amount_requested', '<=', $request->amount_max);
        }
        
        $payouts = $query->latest()->paginate(15);
        
        // Calculate statistics
        $stats = [
            'total_payouts' => Payout::count(),
            'pending_payouts' => Payout::where('status', 'pending')->count(),
            'approved_payouts' => Payout::where('status', 'approved')->count(),
            'denied_payouts' => Payout::where('status', 'denied')->count(),
            'total_amount_requested' => Payout::sum('amount_requested'),
            'pending_amount' => Payout::where('status', 'pending')->sum('amount_requested'),
            'approved_amount' => Payout::where('status', 'approved')->sum('amount_requested'),
        ];
        
        return view('admin.payouts.index', compact('payouts', 'stats'));
    }

    public function show(Payout $payout)
    {
        $payout->load(['user', 'user.books', 'user.walletTransactions']);
        
        // Calculate user statistics
        $userStats = [
            'total_earnings' => $payout->user->walletTransactions()->where('type', 'sale')->sum('amount'),
            'total_payouts' => $payout->user->payouts()->where('status', 'approved')->sum('amount_requested'),
            'pending_payouts' => $payout->user->payouts()->where('status', 'pending')->sum('amount_requested'),
            'available_balance' => $payout->user->walletTransactions()->sum('amount') - $payout->user->payouts()->where('status', 'pending')->sum('amount_requested'),
        ];
        
        return view('admin.payouts.show', compact('payout', 'userStats'));
    }

    public function approve(Request $request, Payout $payout)
    {
        $request->validate([
            'admin_notes' => 'nullable|string',
        ]);

        try {
            $admin = auth()->user();
            $approved = $this->payoutManagementService->approvePayout(
                $payout, 
                $request->admin_notes, 
                $admin
            );

            if ($approved) {
                return back()->with('success', 'Payout approved successfully! Author has been notified.');
            }
            
            return back()->with('error', 'Failed to approve payout.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function deny(Request $request, Payout $payout)
    {
        $request->validate([
            'admin_notes' => 'required|string',
        ]);

        try {
            $admin = auth()->user();
            $denied = $this->payoutManagementService->denyPayout(
                $payout, 
                $request->admin_notes, 
                $admin
            );

            if ($denied) {
                return back()->with('success', 'Payout denied. Author has been notified with the reason.');
            }
            
            return back()->with('error', 'Failed to deny payout.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,deny',
            'payout_ids' => 'required|array',
            'payout_ids.*' => 'exists:payouts,id',
        ]);

        try {
            $payouts = Payout::whereIn('id', $request->payout_ids)->get();
            $admin = auth()->user();
            $successCount = 0;

            foreach ($payouts as $payout) {
                if ($payout->status !== 'pending') {
                    continue;
                }

                if ($request->action === 'approve') {
                    $result = $this->payoutManagementService->approvePayout($payout, 'Bulk approval', $admin);
                } else {
                    $result = $this->payoutManagementService->denyPayout($payout, 'Bulk denial', $admin);
                }

                if ($result) {
                    $successCount++;
                }
            }

            $actionText = $request->action === 'approve' ? 'approved' : 'denied';
            return response()->json([
                'success' => true,
                'message' => "{$successCount} payout(s) {$actionText} successfully!"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk action failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
