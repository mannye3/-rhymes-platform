<?php

namespace App\Repositories;

use App\Models\WalletTransaction;
use App\Repositories\Contracts\WalletTransactionRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class WalletTransactionRepository implements WalletTransactionRepositoryInterface
{
    public function getPaginatedByUser(int $userId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = WalletTransaction::where('user_id', $userId)->with('book');

        // Apply filters
        if (isset($filters['type']) && in_array($filters['type'], ['sale', 'payout', 'commission', 'refund'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['date_from']) && $filters['date_from']) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && $filters['date_to']) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function getForExport(int $userId, array $filters = []): Collection
    {
        $query = WalletTransaction::where('user_id', $userId)->with('book');

        // Apply filters
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getSalesByBookForUser(int $userId): Collection
    {
        return WalletTransaction::where('user_id', $userId)
            ->where('type', 'sale')
            ->with('book')
            ->selectRaw('book_id, SUM(amount) as total_sales, COUNT(*) as sales_count')
            ->groupBy('book_id')
            ->orderByDesc('total_sales')
            ->get();
    }

    public function getTotalEarnings(int $userId): float
    {
        return WalletTransaction::where('user_id', $userId)
            ->where('type', 'sale')
            ->sum('amount') ?? 0;
    }

    public function getEarningsForPeriod(int $userId, string $period): float
    {
        $now = Carbon::now();
        $query = WalletTransaction::where('user_id', $userId)->where('type', 'sale');

        switch ($period) {
            case 'this_month':
                $query->whereMonth('created_at', $now->month)
                      ->whereYear('created_at', $now->year);
                break;
            case 'last_month':
                $query->whereMonth('created_at', $now->subMonth()->month)
                      ->whereYear('created_at', $now->year);
                break;
            case 'this_year':
                $query->whereYear('created_at', $now->year);
                break;
        }

        return $query->sum('amount') ?? 0;
    }

    public function getSalesCount(int $userId): int
    {
        return WalletTransaction::where('user_id', $userId)
            ->where('type', 'sale')
            ->count();
    }

    public function getSalesCountForPeriod(int $userId, string $period): int
    {
        $now = Carbon::now();
        $query = WalletTransaction::where('user_id', $userId)->where('type', 'sale');

        switch ($period) {
            case 'this_month':
                $query->whereMonth('created_at', $now->month)
                      ->whereYear('created_at', $now->year);
                break;
            case 'last_month':
                $query->whereMonth('created_at', $now->subMonth()->month)
                      ->whereYear('created_at', $now->year);
                break;
            case 'this_year':
                $query->whereYear('created_at', $now->year);
                break;
        }

        return $query->count();
    }

    public function getAverageSaleAmount(int $userId): float
    {
        return WalletTransaction::where('user_id', $userId)
            ->where('type', 'sale')
            ->avg('amount') ?? 0;
    }

    public function getTotalPayouts(int $userId): float
    {
        return WalletTransaction::where('user_id', $userId)
            ->where('type', 'payout')
            ->sum('amount') ?? 0;
    }

    public function create(array $data): WalletTransaction
    {
        return WalletTransaction::create($data);
    }
}
