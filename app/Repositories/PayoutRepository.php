<?php

namespace App\Repositories;

use App\Models\Payout;
use App\Repositories\Contracts\PayoutRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PayoutRepository implements PayoutRepositoryInterface
{
    public function getPaginatedByUser(int $userId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Payout::where('user_id', $userId);

        // Apply filters
        if (isset($filters['status']) && in_array($filters['status'], ['pending', 'approved', 'denied'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function create(array $data): Payout
    {
        return Payout::create($data);
    }

    public function findById(int $id): ?Payout
    {
        return Payout::find($id);
    }

    public function updateStatus(Payout $payout, string $status, ?string $adminNotes = null): bool
    {
        $data = ['status' => $status];
        
        if ($adminNotes) {
            $data['admin_notes'] = $adminNotes;
        }

        return $payout->update($data);
    }

    public function getPendingPayoutsSum(int $userId): float
    {
        return Payout::where('user_id', $userId)
            ->where('status', 'pending')
            ->sum('amount_requested') ?? 0;
    }

    public function getPayoutStats(int $userId): array
    {
        $payouts = Payout::where('user_id', $userId);

        return [
            'total_requested' => $payouts->sum('amount_requested') ?? 0,
            'total_approved' => $payouts->where('status', 'approved')->sum('amount_requested') ?? 0,
            'pending_count' => $payouts->where('status', 'pending')->count(),
            'approved_count' => $payouts->where('status', 'approved')->count(),
            'denied_count' => $payouts->where('status', 'denied')->count(),
            'pending_amount' => $this->getPendingPayoutsSum($userId),
        ];
    }

    public function getAllPending(): Collection
    {
        return Payout::where('status', 'pending')
            ->with('user')
            ->latest()
            ->get();
    }

    public function getByStatus(string $status): Collection
    {
        return Payout::where('status', $status)
            ->with('user')
            ->latest()
            ->get();
    }
}
