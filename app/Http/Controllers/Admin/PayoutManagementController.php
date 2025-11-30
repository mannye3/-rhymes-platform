<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payout;
use App\Services\Admin\PayoutManagementService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
        // Add debugging
        Log::info('Payout show method called', [
            'payout_id' => $payout->id ?? 'null',
            'request_wants_json' => request()->wantsJson(),
            'user_authenticated' => Auth::check()
        ]);
        
        // Check if payout exists
        if (!$payout->exists) {
            Log::warning('Payout not found', ['payout_id' => request()->route('payout')]);
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payout not found'
                ], 404);
            }
            abort(404);
        }
        
        $payout->load(['user']);
        
        // Return JSON response for AJAX requests
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'payout' => $payout
            ]);
        }
        
        return view('admin.payouts.show', compact('payout'));
    }

    public function approve(Request $request, Payout $payout)
    {
        $request->validate([
            'admin_notes' => 'nullable|string',
        ]);

        try {
            $admin = Auth::user();
            $approved = $this->payoutManagementService->approvePayout(
                $payout, 
                $request->admin_notes, 
                $admin
            );

            if ($approved) {
                // Return JSON response for AJAX requests
                if (request()->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Payout approved successfully! Author has been notified.'
                    ]);
                }
                
                return back()->with('success', 'Payout approved successfully! Author has been notified.');
            }
            
            // Return JSON response for AJAX requests
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to approve payout.'
                ]);
            }
            
            return back()->with('error', 'Failed to approve payout.');
        } catch (\InvalidArgumentException $e) {
            // Return JSON response for AJAX requests
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            
            return back()->with('error', $e->getMessage());
        }
    }

    public function deny(Request $request, Payout $payout)
    {
        $request->validate([
            'admin_notes' => 'required|string',
        ]);

        try {
            $admin = Auth::user();
            $denied = $this->payoutManagementService->denyPayout(
                $payout, 
                $request->admin_notes, 
                $admin
            );

            if ($denied) {
                // Return JSON response for AJAX requests
                if (request()->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Payout denied. Author has been notified with the reason.'
                    ]);
                }
                
                return back()->with('success', 'Payout denied. Author has been notified with the reason.');
            }
            
            // Return JSON response for AJAX requests
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to deny payout.'
                ]);
            }
            
            return back()->with('error', 'Failed to deny payout.');
        } catch (\InvalidArgumentException $e) {
            // Return JSON response for AJAX requests
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            
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
            $admin = Auth::user();
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