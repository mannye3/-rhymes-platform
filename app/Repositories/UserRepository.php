<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function updatePaymentDetails(User $user, array $paymentDetails): bool
    {
        return $user->update([
            'payment_details' => $paymentDetails
        ]);
    }

    public function getWalletBalance(User $user): float
    {
        return $user->getWalletBalance();
    }

    public function getUsersByRole(string $role)
    {
        return User::role($role)->get();
    }

    public function updateProfile(User $user, array $data): bool
    {
        return $user->update($data);
    }
}
