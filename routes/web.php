<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Author\BookController;
use App\Http\Controllers\Author\WalletController;
use App\Http\Controllers\Author\PayoutController;
use App\Http\Controllers\Admin\BookReviewController;
use App\Http\Controllers\Admin\PayoutManagementController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Author\AuthorProfileController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Author routes
    Route::middleware('role:author')->group(function () {
        Route::prefix('author')->name('author.')->group(function () {
            Route::resource('books', BookController::class);
            
            Route::get('wallet', [WalletController::class, 'index'])->name('wallet.index');
            Route::get('wallet/export', [WalletController::class, 'export'])->name('wallet.export');
            Route::get('payouts', [PayoutController::class, 'index'])->name('payouts.index');
            Route::post('payouts', [PayoutController::class, 'store'])->name('payouts.store');
            Route::get('payouts/payment-details', [PayoutController::class, 'paymentDetails'])->name('payouts.payment-details');
            Route::put('payouts/payment-details', [PayoutController::class, 'updatePaymentDetails'])->name('payouts.payment-details.update');

            Route::get('profile', [AuthorProfileController::class, 'showProfile'])->name('profile.edit');
            Route::patch('profile', [AuthorProfileController::class, 'updateProfile'])->name('profile.update');
            Route::put('profile/password', [AuthorProfileController::class, 'updatePassword'])->name('profile.password.update');
            Route::post('profile/avatar', [AuthorProfileController::class, 'uploadAvatar'])->name('profile.avatar.update');
            Route::put('profile/payment-details', [AuthorProfileController::class, 'updatePaymentDetails'])->name('profile.payment-details.update');
            Route::delete('profile', [AuthorProfileController::class, 'deleteAccount'])->name('profile.destroy');
        });
    });
    
    // Admin routes
    Route::middleware('role:admin')->group(function () {
        Route::prefix('admin')->name('admin.')->group(function () {
            // Dashboard
            Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
            
            // User Management
            Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
            Route::get('users/authors', [UserManagementController::class, 'authors'])->name('users.authors');
            Route::get('users/create', [UserManagementController::class, 'create'])->name('users.create');
            Route::post('users', [UserManagementController::class, 'store'])->name('users.store');
            Route::get('users/{user}', [UserManagementController::class, 'show'])->name('users.show');
            Route::get('users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
            Route::put('users/{user}', [UserManagementController::class, 'update'])->name('users.update');
            Route::delete('users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
            Route::post('users/{user}/promote-author', [UserManagementController::class, 'promoteToAuthor'])->name('users.promote-author');
            Route::post('users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
            Route::post('users/{user}/send-verification', [UserManagementController::class, 'sendVerificationEmail'])->name('users.send-verification');
            Route::get('users/{user}/login-as', [UserManagementController::class, 'loginAsUser'])->name('users.login-as');
            
            // Book Management
            Route::get('books', [BookReviewController::class, 'index'])->name('books.index');
            Route::get('books/pending', function() { return app(BookReviewController::class)->index(request()->merge(['status' => 'pending'])); })->name('books.pending');
            Route::get('books/published', function() { return app(BookReviewController::class)->index(request()->merge(['status' => 'accepted'])); })->name('books.published');
            Route::get('books/{book}', [BookReviewController::class, 'show'])->name('books.show');
            Route::patch('books/{book}/review', [BookReviewController::class, 'review'])->name('books.review');
            Route::post('books/bulk-action', [BookReviewController::class, 'bulkAction'])->name('books.bulk-action');
            
            // Payout Management
            Route::get('payouts', [PayoutManagementController::class, 'index'])->name('payouts.index');
            Route::get('payouts/pending', function() { return app(PayoutManagementController::class)->index(request()->merge(['status' => 'pending'])); })->name('payouts.pending');
            Route::get('payouts/completed', function() { return app(PayoutManagementController::class)->index(request()->merge(['status' => 'approved'])); })->name('payouts.completed');
            Route::get('payouts/{payout}', [PayoutManagementController::class, 'show'])->name('payouts.show');
            Route::patch('payouts/{payout}/approve', [PayoutManagementController::class, 'approve'])->name('payouts.approve');
            Route::patch('payouts/{payout}/deny', [PayoutManagementController::class, 'deny'])->name('payouts.deny');
            Route::post('payouts/bulk-action', [PayoutManagementController::class, 'bulkAction'])->name('payouts.bulk-action');
            
            // Reports & Analytics
            Route::get('reports/sales', [ReportsController::class, 'sales'])->name('reports.sales');
            Route::get('reports/analytics', [ReportsController::class, 'analytics'])->name('reports.analytics');
            
            // Settings
            Route::get('settings', [SettingsController::class, 'index'])->name('settings');
            Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
            Route::post('settings/clear-cache', [SettingsController::class, 'clearCache'])->name('settings.clear-cache');
            Route::post('settings/test-email', [SettingsController::class, 'testEmail'])->name('settings.test-email');
            
            // Notifications
            Route::get('notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
            Route::post('notifications', [AdminNotificationController::class, 'store'])->name('notifications.store');
            Route::post('notifications/mark-all-read', [AdminNotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
            Route::post('authors/send-message', [AdminNotificationController::class, 'sendMessage'])->name('authors.send-message');
            
            // Admin Profile
            Route::get('profile', [AdminProfileController::class, 'index'])->name('profile.index');
            Route::put('profile', [AdminProfileController::class, 'update'])->name('profile.update');
            Route::put('profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');
            Route::put('profile/notifications', [AdminProfileController::class, 'updateNotifications'])->name('profile.notifications');
            Route::post('profile/export-data', [AdminProfileController::class, 'exportData'])->name('profile.export-data');
        });
    });
    // Notification routes
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
});

require __DIR__.'/auth.php';
