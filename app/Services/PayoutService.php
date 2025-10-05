<?php

namespace App\Services;

use App\Repositories\Contracts\PayoutRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Models\User;
use App\Models\Payout;
use Illuminate\Validation\ValidationException;

class PayoutService
{
    public function __construct(
        private PayoutRepositoryInterface $payoutRepository,
        private UserRepositoryInterface $userRepository,
        private WalletService $walletService
    ) {}

    /**
     * Get payout overview for user
     */
    public function getPayoutOverview(User $user, array $filters = []): array
    {
        $payouts = $this->payoutRepository->getPaginatedByUser($user->id, $filters);
        $walletBalance = $this->userRepository->getWalletBalance($user);
        $availableBalance = $this->walletService->getAvailableBalance($user);
        $payoutStats = $this->payoutRepository->getPayoutStats($user->id);

        return [
            'payouts' => $payouts,
            'walletBalance' => $walletBalance,
            'availableBalance' => $availableBalance,
            'payoutStats' => $payoutStats,
        ];
    }

    /**
     * Create a new payout request
     */
    public function createPayoutRequest(User $user, array $data): Payout
    {
        // Validate available balance
        $availableBalance = $this->walletService->getAvailableBalance($user);
        
        if ($data['amount_requested'] > $availableBalance) {
            $pendingPayouts = $this->payoutRepository->getPendingPayoutsSum($user->id);
            throw ValidationException::withMessages([
                'amount_requested' => 'Insufficient balance. You have $' . number_format($pendingPayouts, 2) . ' in pending payouts.'
            ]);
        }

        // Validate minimum amount
        if ($data['amount_requested'] < 10) {
            throw ValidationException::withMessages([
                'amount_requested' => 'Minimum payout amount is $10.00'
            ]);
        }

        $data['user_id'] = $user->id;
        $data['status'] = 'pending';

        return $this->payoutRepository->create($data);
    }

    /**
     * Calculate payout fee
     */
    public function calculatePayoutFee(float $amount): array
    {
        $feePercentage = 2.5; // 2.5%
        $fee = ($amount * $feePercentage) / 100;
        $netAmount = $amount - $fee;

        return [
            'gross_amount' => $amount,
            'fee_percentage' => $feePercentage,
            'fee_amount' => $fee,
            'net_amount' => $netAmount,
        ];
    }

    /**
     * Update payout status (admin function)
     */
    public function updatePayoutStatus(Payout $payout, string $status, ?string $adminNotes = null): bool
    {
        if (!in_array($status, ['pending', 'approved', 'denied'])) {
            throw new \InvalidArgumentException('Invalid payout status');
        }

        return $this->payoutRepository->updateStatus($payout, $status, $adminNotes);
    }

    /**
     * Get all pending payouts (admin function)
     */
    public function getAllPendingPayouts()
    {
        return $this->payoutRepository->getAllPending();
    }

    /**
     * Update user payment details
     */
    public function updatePaymentDetails(User $user, array $paymentDetails): bool
    {
        // Validate payment method specific fields
        $this->validatePaymentDetails($paymentDetails);

        return $this->userRepository->updatePaymentDetails($user, $paymentDetails);
    }

    /**
     * Validate payment details based on payment method
     */
    private function validatePaymentDetails(array $paymentDetails): void
    {
        $method = $paymentDetails['payment_method'];

        switch ($method) {
            case 'bank_transfer':
                if (empty($paymentDetails['account_number']) || empty($paymentDetails['routing_number'])) {
                    throw ValidationException::withMessages([
                        'payment_details' => 'Bank transfer requires account number and routing number'
                    ]);
                }
                break;
            case 'paypal':
                if (empty($paymentDetails['paypal_email'])) {
                    throw ValidationException::withMessages([
                        'payment_details' => 'PayPal requires email address'
                    ]);
                }
                break;
            case 'stripe':
                if (empty($paymentDetails['stripe_account_id'])) {
                    throw ValidationException::withMessages([
                        'payment_details' => 'Stripe requires account ID'
                    ]);
                }
                break;
        }
    }

    /**
     * Get total payouts for user
     */
    public function getTotalPayouts(int $userId): float
    {
        return $this->payoutRepository->getTotalPayoutsForUser($userId);
    }

    /**
     * Get pending payouts for user
     */
    public function getPendingPayouts(int $userId): float
    {
        return $this->payoutRepository->getPendingPayoutsSum($userId);
    }

    /**
     * Get completed payouts for user
     */
    public function getCompletedPayouts(int $userId): float
    {
        return $this->payoutRepository->getCompletedPayoutsForUser($userId);
    }

    /**
     * Get recent payouts for user
     */
    public function getRecentPayouts(int $userId, int $limit = 3): \Illuminate\Database\Eloquent\Collection
    {
        return $this->payoutRepository->getRecentByUser($userId, $limit);
    }
}
