<?php

namespace App\Repositories\Contracts;

use App\Models\WalletTransaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface WalletTransactionRepositoryInterface
{
    /**
     * Get paginated transactions for a user with filters
     */
    public function getPaginatedByUser(int $userId, array $filters = [], int $perPage = 20): LengthAwarePaginator;

    /**
     * Get transactions for export
     */
    public function getForExport(int $userId, array $filters = []): Collection;

    /**
     * Get sales analytics by book for user
     */
    public function getSalesByBookForUser(int $userId): Collection;

    /**
     * Get total earnings for user
     */
    public function getTotalEarnings(int $userId): float;

    /**
     * Get earnings for specific period
     */
    public function getEarningsForPeriod(int $userId, string $period): float;

    /**
     * Get sales count for user
     */
    public function getSalesCount(int $userId): int;

    /**
     * Get sales count for specific period
     */
    public function getSalesCountForPeriod(int $userId, string $period): int;

    /**
     * Get average sale amount for user
     */
    public function getAverageSaleAmount(int $userId): float;

    /**
     * Get total payouts for user
     */
    public function getTotalPayouts(int $userId): float;

    /**
     * Create a new transaction
     */
    public function create(array $data): WalletTransaction;
}
