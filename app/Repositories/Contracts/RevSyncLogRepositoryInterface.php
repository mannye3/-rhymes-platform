<?php

namespace App\Repositories\Contracts;

use App\Models\RevSyncLog;
use Illuminate\Database\Eloquent\Collection;

interface RevSyncLogRepositoryInterface
{
    /**
     * Create a new sync log entry
     */
    public function create(array $data): RevSyncLog;

    /**
     * Get sync logs with filters
     */
    public function getWithFilters(array $filters = []): Collection;

    /**
     * Get recent sync logs
     */
    public function getRecent(int $limit = 50): Collection;

    /**
     * Get sync logs by type
     */
    public function getByType(string $type): Collection;

    /**
     * Get sync logs by status
     */
    public function getByStatus(string $status): Collection;

    /**
     * Get failed sync logs
     */
    public function getFailedLogs(): Collection;

    /**
     * Delete old sync logs
     */
    public function deleteOldLogs(int $daysOld = 30): int;
}
