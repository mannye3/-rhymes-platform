<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * Find user by ID
     */
    public function findById(int $id): ?User;

    /**
     * Update user payment details
     */
    public function updatePaymentDetails(User $user, array $paymentDetails): bool;

    /**
     * Get user wallet balance
     */
    public function getWalletBalance(User $user): float;

    /**
     * Get users by role
     */
    public function getUsersByRole(string $role);

    /**
     * Update user profile
     */
    public function updateProfile(User $user, array $data): bool;
}
