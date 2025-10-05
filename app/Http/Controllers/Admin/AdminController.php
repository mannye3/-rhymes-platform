<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Book;
use App\Models\Payout;
use App\Models\WalletTransaction;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function dashboard()
    {
        $analytics = $this->getDashboardAnalytics();
        return view('admin.dashboard', compact('analytics'));
    }

    private function getDashboardAnalytics()
    {
        // Basic counts
        $totalUsers = User::count();
        $totalAuthors = User::role('author')->count();
        $totalBooks = Book::count();
        $pendingBooks = Book::where('status', 'pending')->count();
        $publishedBooks = Book::where('status', 'accepted')->count();
        $rejectedBooks = Book::where('status', 'rejected')->count();

        // Payout statistics
        $totalPayouts = Payout::count();
        $pendingPayouts = Payout::where('status', 'pending')->count();
        $approvedPayouts = Payout::where('status', 'approved')->count();
        $totalPayoutAmount = Payout::where('status', 'approved')->sum('amount_requested');
        $pendingPayoutAmount = Payout::where('status', 'pending')->sum('amount_requested');

        // Revenue statistics
        $totalRevenue = WalletTransaction::where('type', 'sale')->sum('amount');
        $thisMonthRevenue = WalletTransaction::where('type', 'sale')
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->sum('amount');
        $lastMonthRevenue = WalletTransaction::where('type', 'sale')
            ->whereBetween('created_at', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth()
            ])
            ->sum('amount');

        $revenueGrowth = $lastMonthRevenue > 0 
            ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 
            : 0;

        // Recent activity
        $recentBooks = Book::with('user')->latest()->limit(5)->get();
        $recentPayouts = Payout::with('user')->latest()->limit(5)->get();
        $recentUsers = User::latest()->limit(5)->get();

        // Monthly data for charts (last 6 months)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            
            $monthlyRevenue = WalletTransaction::where('type', 'sale')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('amount');
            
            $monthlyBooks = Book::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();
            
            $monthlyUsers = User::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();

            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'revenue' => $monthlyRevenue,
                'books' => $monthlyBooks,
                'users' => $monthlyUsers,
            ];
        }

        // Top performing books
$topBooks = Book::select([
        'books.id',
        'books.title',
        'books.description',
        'books.price',
        'books.status',
        'books.user_id',
        'books.created_at',
        'books.updated_at'
    ])
    ->selectRaw('COUNT(wallet_transactions.id) as sales_count')
    ->selectRaw('SUM(wallet_transactions.amount) as total_revenue')
    ->leftJoin('wallet_transactions', function($join) {
        $join->on('books.id', '=', 'wallet_transactions.book_id')
             ->where('wallet_transactions.type', '=', 'sale');
    })
    ->with('user')
    ->groupBy([
        'books.id',
        'books.title',
        'books.description',
        'books.price',
        'books.status',
        'books.user_id',
        'books.created_at',
        'books.updated_at'
    ])
    ->orderByDesc('total_revenue')
    ->limit(10)
    ->get();


        // Top authors by revenue
        $topAuthors = User::select([
                'users.id',
                'users.name',
                'users.email',
                'users.avatar',
                'users.created_at',
                'users.updated_at'
            ])
            ->selectRaw('SUM(wallet_transactions.amount) as total_earnings')
            ->selectRaw('COUNT(DISTINCT books.id) as books_count')
            ->join('books', 'users.id', '=', 'books.user_id')
            ->leftJoin('wallet_transactions', function($join) {
                $join->on('books.id', '=', 'wallet_transactions.book_id')
                     ->where('wallet_transactions.type', '=', 'sale');
            })
            ->role('author')
            ->groupBy([
                'users.id',
                'users.name',
                'users.email',
                'users.avatar',
                'users.created_at',
                'users.updated_at'
            ])
            ->orderByDesc('total_earnings')
            ->limit(10)
            ->get();

        return [
            'stats' => [
                'total_users' => $totalUsers,
                'total_authors' => $totalAuthors,
                'total_books' => $totalBooks,
                'pending_books' => $pendingBooks,
                'published_books' => $publishedBooks,
                'rejected_books' => $rejectedBooks,
                'total_payouts' => $totalPayouts,
                'pending_payouts' => $pendingPayouts,
                'approved_payouts' => $approvedPayouts,
                'total_payout_amount' => $totalPayoutAmount,
                'pending_payout_amount' => $pendingPayoutAmount,
                'total_revenue' => $totalRevenue,
                'this_month_revenue' => $thisMonthRevenue,
                'last_month_revenue' => $lastMonthRevenue,
                'revenue_growth' => $revenueGrowth,
            ],
            'recent' => [
                'books' => $recentBooks,
                'payouts' => $recentPayouts,
                'users' => $recentUsers,
            ],
            'charts' => [
                'monthly_data' => $monthlyData,
            ],
            'top_performers' => [
                'books' => $topBooks,
                'authors' => $topAuthors,
            ]
        ];
    }
}
