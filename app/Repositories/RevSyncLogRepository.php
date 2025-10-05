<?php

namespace App\Repositories;

use App\Models\RevSyncLog;
use App\Repositories\Contracts\RevSyncLogRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class RevSyncLogRepository implements RevSyncLogRepositoryInterface
{
    public function create(array $data): RevSyncLog
    {
        return RevSyncLog::create($data);
    }

    public function getWithFilters(array $filters = []): Collection
    {
        $query = RevSyncLog::query();

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->latest()->get();
    }

    public function getRecent(int $limit = 50): Collection
    {
        return RevSyncLog::latest()->limit($limit)->get();
    }

    public function getByType(string $type): Collection
    {
        return RevSyncLog::where('type', $type)->latest()->get();
    }

    public function getByStatus(string $status): Collection
    {
        return RevSyncLog::where('status', $status)->latest()->get();
    }

    public function getFailedLogs(): Collection
    {
        return RevSyncLog::where('status', 'error')->latest()->get();
    }

    public function deleteOldLogs(int $daysOld = 30): int
    {
        $cutoffDate = Carbon::now()->subDays($daysOld);
        return RevSyncLog::where('created_at', '<', $cutoffDate)->delete();
    }
}
