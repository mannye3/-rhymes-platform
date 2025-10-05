<?php

namespace App\Services;

use App\Repositories\Contracts\WalletTransactionRepositoryInterface;
use App\Repositories\Contracts\PayoutRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Models\User;
use Carbon\Carbon;

class WalletService
{
    public function __construct(
        private WalletTransactionRepositoryInterface $walletTransactionRepository,
        private PayoutRepositoryInterface $payoutRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * Get wallet overview data for user
     */
    public function getWalletOverview(User $user, array $filters = []): array
    {
        $balance = $this->userRepository->getWalletBalance($user);
        $transactions = $this->walletTransactionRepository->getPaginatedByUser($user->id, $filters);
        $salesByBook = $this->walletTransactionRepository->getSalesByBookForUser($user->id);
        $analytics = $this->getWalletAnalytics($user);

        return [
            'balance' => $balance,
            'transactions' => $transactions,
            'salesByBook' => $salesByBook,
            'analytics' => $analytics,
        ];
    }

    /**
     * Get wallet analytics for user
     */
    public function getWalletAnalytics(User $user): array
    {
        return [
            'total_earnings' => $this->walletTransactionRepository->getTotalEarnings($user->id),
            'this_month_earnings' => $this->walletTransactionRepository->getEarningsForPeriod($user->id, 'this_month'),
            'last_month_earnings' => $this->walletTransactionRepository->getEarningsForPeriod($user->id, 'last_month'),
            'total_sales_count' => $this->walletTransactionRepository->getSalesCount($user->id),
            'this_month_sales_count' => $this->walletTransactionRepository->getSalesCountForPeriod($user->id, 'this_month'),
            'average_sale_amount' => $this->walletTransactionRepository->getAverageSaleAmount($user->id),
            'total_payouts' => $this->walletTransactionRepository->getTotalPayouts($user->id),
        ];
    }

    /**
     * Get available balance for user (considering pending payouts)
     */
    public function getAvailableBalance(User $user): float
    {
        $walletBalance = $this->userRepository->getWalletBalance($user);
        $pendingPayouts = $this->payoutRepository->getPendingPayoutsSum($user->id);
        
        return $walletBalance - $pendingPayouts;
    }

    /**
     * Export wallet transactions
     */
    public function exportTransactions(User $user, array $filters = []): array
    {
        $transactions = $this->walletTransactionRepository->getForExport($user->id, $filters);
        
        $csvData = [];
        foreach ($transactions as $transaction) {
            $csvData[] = [
                'date' => $transaction->created_at->format('Y-m-d H:i:s'),
                'type' => ucfirst($transaction->type),
                'book' => $transaction->book ? $transaction->book->title : 'N/A',
                'amount' => '$' . number_format($transaction->amount, 2),
                'description' => $transaction->meta['description'] ?? ''
            ];
        }

        return [
            'filename' => 'wallet_transactions_' . now()->format('Y-m-d') . '.csv',
            'headers' => ['Date', 'Type', 'Book', 'Amount', 'Description'],
            'data' => $csvData
        ];
    }

    /**
     * Create a wallet transaction
     */
    public function createTransaction(array $data): void
    {
        $this->walletTransactionRepository->create($data);
    }

    /**
     * Get wallet balance for user ID
     */
    public function getWalletBalance(int $userId): float
    {
        $user = $this->userRepository->findById($userId);
        return $user ? $this->userRepository->getWalletBalance($user) : 0.0;
    }

    /**
     * Get available balance for user ID
     */
    public function getAvailableBalanceById(int $userId): float
    {
        $user = $this->userRepository->findById($userId);
        return $user ? $this->getAvailableBalance($user) : 0.0;
    }

    /**
     * Get total earnings for user
     */
    public function getTotalEarnings(int $userId): float
    {
        return $this->walletTransactionRepository->getTotalEarnings($userId);
    }

    /**
     * Get monthly earnings for user
     */
    public function getMonthlyEarnings(int $userId): float
    {
        return $this->walletTransactionRepository->getEarningsForPeriod($userId, 'this_month');
    }

    /**
     * Get weekly earnings for user
     */
    public function getWeeklyEarnings(int $userId): float
    {
        return $this->walletTransactionRepository->getEarningsForPeriod($userId, 'this_week');
    }

    /**
     * Get recent transactions for user
     */
    public function getRecentTransactions(int $userId, int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return $this->walletTransactionRepository->getRecentByUser($userId, $limit);
    }

    /**
     * Get book sales analytics for user
     */
    public function getBookSalesAnalytics(int $userId): array
    {
        return $this->walletTransactionRepository->getSalesByBookForUser($userId);
    }

    /**
     * Get monthly earnings chart data
     */
    public function getMonthlyEarningsChart(int $userId, int $months = 6): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            
            $earnings = $this->getEarningsForPeriod($userId, $startOfMonth, $endOfMonth);
            
            $data[] = [
                'month' => $date->format('M Y'),
                'earnings' => $earnings
            ];
        }
        
        return $data;
    }

    /**
     * Get earnings for specific period
     */
    public function getEarningsForPeriod(int $userId, Carbon $startDate, Carbon $endDate): float
    {
        return $this->walletTransactionRepository->getEarningsForDateRange($userId, $startDate, $endDate);
    }
}
