<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Repository Contracts
use App\Repositories\Contracts\BookRepositoryInterface;
use App\Repositories\Contracts\WalletTransactionRepositoryInterface;
use App\Repositories\Contracts\PayoutRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\RevSyncLogRepositoryInterface;

// Repository Implementations
use App\Repositories\BookRepository;
use App\Repositories\WalletTransactionRepository;
use App\Repositories\PayoutRepository;
use App\Repositories\UserRepository;
use App\Repositories\RevSyncLogRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind repository interfaces to their implementations
        $this->app->bind(BookRepositoryInterface::class, BookRepository::class);
        $this->app->bind(WalletTransactionRepositoryInterface::class, WalletTransactionRepository::class);
        $this->app->bind(PayoutRepositoryInterface::class, PayoutRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RevSyncLogRepositoryInterface::class, RevSyncLogRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
