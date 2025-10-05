<?php

namespace App\Repositories\Contracts;

use App\Models\Payout;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PayoutRepositoryInterface
{
    /**
     * Get paginated payouts for a user with filters
     */
    public function getPaginatedByUser(int $userId, array $filters = [], int $perPage = 10): LengthAwarePaginator;

    /**
     * Create a new payout request
     */
    public function create(array $data): Payout;

    /**
     * Find payout by ID
     */
    public function findById(int $id): ?Payout;

    /**
     * Update payout status
     */
    public function updateStatus(Payout $payout, string $status, ?string $adminNotes = null): bool;

    /**
     * Get pending payouts sum for user
     */
    public function getPendingPayoutsSum(int $userId): float;

    /**
     * Get payout statistics for user
     */
    public function getPayoutStats(int $userId): array;

    /**
     * Get all pending payouts
     */
    public function getAllPending(): Collection;

    /**
     * Get payouts by status
     */
    public function getByStatus(string $status): Collection;
}
