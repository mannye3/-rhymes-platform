<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Payout;
use App\Services\BookService;
use App\Services\WalletService;
use App\Services\PayoutService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $bookService;
    protected $walletService;
    protected $payoutService;

    public function __construct(
        BookService $bookService,
        WalletService $walletService,
        PayoutService $payoutService
    ) {
        $this->bookService = $bookService;
        $this->walletService = $walletService;
        $this->payoutService = $payoutService;
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return $this->adminDashboard();
        } elseif ($user->hasRole('author')) {
            return $this->authorDashboard();
        } else {
            return $this->userDashboard();
        }
    }

    private function adminDashboard()
    {
        // Redirect admins to the dedicated admin dashboard route which prepares
        // the full $analytics payload used by `resources/views/admin/dashboard.blade.php`.
        return redirect()->route('admin.dashboard');
    }

    private function authorDashboard()
    {
        $user = auth()->user();
        
        // Get comprehensive analytics data
        $analytics = $this->getAuthorAnalytics($user);
        
        return view('author.dashboard', compact('analytics'));
    }

    private function userDashboard()
    {
        return view('user.dashboard');
    }

    private function getAuthorAnalytics($user)
    {
        // Basic stats using existing methods
        $totalBooks = $user->books()->count();
        $pendingBooks = $user->books()->where('status', 'pending')->count();
        $publishedBooks = $user->books()->where('status', 'accepted')->count();
        $rejectedBooks = $user->books()->where('status', 'rejected')->count();

        // Wallet analytics using existing methods
        $walletBalance = $user->getWalletBalance();
        $walletOverview = $this->walletService->getWalletOverview($user);
        $availableBalance = $this->walletService->getAvailableBalance($user);

        // Payout analytics using existing methods
        $payoutOverview = $this->payoutService->getPayoutOverview($user);

        // Recent activity - get latest books and transactions
        $recentBooks = $user->books()->latest()->limit(5)->get();
        $recentTransactions = $user->walletTransactions()->latest()->limit(5)->get();
        $recentPayouts = $user->payouts()->latest()->limit(3)->get();

        // Performance metrics
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth();
        
        $thisMonthEarnings = $user->walletTransactions()
            ->where('type', 'sale')
            ->where('created_at', '>=', $thisMonth)
            ->sum('amount');
            
        $lastMonthEarnings = $user->walletTransactions()
            ->where('type', 'sale')
            ->whereBetween('created_at', [$lastMonth->startOfMonth(), $lastMonth->endOfMonth()])
            ->sum('amount');

        $monthlyGrowth = $lastMonthEarnings > 0 ? (($thisMonthEarnings - $lastMonthEarnings) / $lastMonthEarnings) * 100 : 0;

        return [
            'user' => $user,
            'stats' => [
                'total_books' => $totalBooks,
                'pending_books' => $pendingBooks,
                'published_books' => $publishedBooks,
                'rejected_books' => $rejectedBooks,
                'wallet_balance' => $walletBalance,
                'available_balance' => $availableBalance,
                'total_earnings' => $walletOverview['analytics']['total_earnings'] ?? 0,
                'monthly_earnings' => $thisMonthEarnings,
                'monthly_growth' => $monthlyGrowth,
                'pending_payouts' => $payoutOverview['payoutStats']['pending_amount'] ?? 0,
                'total_payouts' => $payoutOverview['payoutStats']['total_amount'] ?? 0,
            ],
            'recent' => [
                'books' => $recentBooks,
                'transactions' => $recentTransactions,
                'payouts' => $recentPayouts,
            ],
            'analytics' => [
                'book_sales' => $walletOverview['salesByBook'] ?? [],
                'wallet_analytics' => $walletOverview['analytics'] ?? [],
            ]
        ];
    }
}
