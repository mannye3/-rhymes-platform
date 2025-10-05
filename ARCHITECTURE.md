# Service Layer & Repository Pattern Architecture

This document outlines the Service Layer and Repository pattern implementation for the Rhymes Platform.

## Architecture Overview

The application now follows a clean architecture pattern with clear separation of concerns:

```
Controllers → Services → Repositories → Models
```

### Key Benefits

1. **Separation of Concerns**: Controllers handle HTTP requests, Services contain business logic, Repositories handle data access
2. **Testability**: Each layer can be unit tested independently
3. **Maintainability**: Business logic is centralized in services
4. **Flexibility**: Easy to swap data sources or add new business rules
5. **Code Reusability**: Services can be used across multiple controllers

## Repository Pattern

### Interfaces

All repositories implement contracts (interfaces) for dependency injection:

- `BookRepositoryInterface`
- `WalletTransactionRepositoryInterface` 
- `PayoutRepositoryInterface`
- `UserRepositoryInterface`
- `RevSyncLogRepositoryInterface`

### Implementations

Concrete repository classes handle all database operations:

- `BookRepository`
- `WalletTransactionRepository`
- `PayoutRepository` 
- `UserRepository`
- `RevSyncLogRepository`

### Example Usage

```php
// In a service
public function __construct(
    private BookRepositoryInterface $bookRepository
) {}

public function getUserBooks(User $user): LengthAwarePaginator
{
    return $this->bookRepository->getPaginatedByUser($user->id);
}
```

## Service Layer

### Core Services

#### WalletService
- Handles wallet analytics and transaction management
- Calculates available balance considering pending payouts
- Manages transaction exports
- Provides wallet overview data

#### PayoutService  
- Manages payout requests and validation
- Calculates payout fees (2.5%)
- Handles payment method updates
- Validates available balance for payouts

#### BookService
- Manages book CRUD operations
- Handles book validation
- Provides sales analytics
- Manages book status updates

### Admin Services

#### BookReviewService
- Handles book review workflow
- Manages author promotions
- Sends status change notifications
- Provides book statistics

#### PayoutManagementService
- Processes payout approvals/denials
- Creates wallet transactions for approved payouts
- Sends payout status notifications
- Provides payout statistics

### Integration Services

#### RevService
- Handles external REV system integration
- Uses RevSyncLogRepository for logging
- Manages API communication
- Provides connection testing

## Controller Refactoring

### Before (Fat Controllers)
```php
public function index(Request $request)
{
    $user = auth()->user();
    $balance = $user->getWalletBalance();
    
    // Complex query building...
    $transactionsQuery = $user->walletTransactions()->with('book');
    // ... filtering logic
    // ... analytics calculations
    
    return view('author.wallet.index', compact('balance', 'transactions', 'analytics'));
}
```

### After (Thin Controllers)
```php
public function index(Request $request)
{
    $user = auth()->user();
    $filters = $request->only(['type', 'date_from', 'date_to']);
    $walletData = $this->walletService->getWalletOverview($user, $filters);

    return view('author.wallet.index', $walletData);
}
```

## Dependency Injection

### Service Provider Registration

All repository interfaces are bound to their implementations in `RepositoryServiceProvider`:

```php
$this->app->bind(BookRepositoryInterface::class, BookRepository::class);
$this->app->bind(WalletTransactionRepositoryInterface::class, WalletTransactionRepository::class);
// ... other bindings
```

### Constructor Injection

Controllers and services use constructor injection:

```php
public function __construct(
    private WalletService $walletService,
    private PayoutService $payoutService
) {
    $this->middleware(['auth', 'role:author|admin']);
}
```

## Error Handling

Services throw appropriate exceptions that controllers catch:

```php
try {
    $this->payoutService->createPayoutRequest($user, $validated);
    return back()->with('success', 'Payout request submitted successfully!');
} catch (\Illuminate\Validation\ValidationException $e) {
    return back()->withErrors($e->errors());
}
```

## Testing Strategy

### Unit Testing

Each layer can be tested independently:

- **Repository Tests**: Mock database, test data access
- **Service Tests**: Mock repositories, test business logic  
- **Controller Tests**: Mock services, test HTTP handling

### Example Service Test

```php
public function test_can_create_payout_request()
{
    $user = User::factory()->create();
    $payoutRepo = Mockery::mock(PayoutRepositoryInterface::class);
    $walletService = Mockery::mock(WalletService::class);
    
    $walletService->shouldReceive('getAvailableBalance')->andReturn(100.00);
    $payoutRepo->shouldReceive('create')->once()->andReturn(new Payout());
    
    $service = new PayoutService($payoutRepo, $userRepo, $walletService);
    $result = $service->createPayoutRequest($user, ['amount_requested' => 50.00]);
    
    $this->assertInstanceOf(Payout::class, $result);
}
```

## Migration Guide

### For New Features

1. Create repository interface and implementation
2. Add to `RepositoryServiceProvider`
3. Create service class with business logic
4. Inject service into controller
5. Keep controller thin - delegate to service

### For Existing Code

1. Identify business logic in controllers
2. Extract to appropriate service method
3. Replace direct model calls with repository calls
4. Update controller to use service
5. Add proper error handling

## Best Practices

1. **Single Responsibility**: Each service should have one clear purpose
2. **Interface Segregation**: Keep repository interfaces focused
3. **Dependency Inversion**: Depend on abstractions, not concretions
4. **Error Handling**: Use exceptions for business rule violations
5. **Validation**: Keep validation in services, not repositories
6. **Transactions**: Handle database transactions in services
7. **Caching**: Implement caching at the service layer

## File Structure

```
app/
├── Http/Controllers/
│   ├── Author/
│   │   ├── BookController.php (thin)
│   │   ├── WalletController.php (thin)
│   │   └── PayoutController.php (thin)
│   └── Admin/
│       ├── BookReviewController.php (thin)
│       └── PayoutManagementController.php (thin)
├── Services/
│   ├── WalletService.php
│   ├── PayoutService.php
│   ├── BookService.php
│   ├── RevService.php
│   └── Admin/
│       ├── BookReviewService.php
│       └── PayoutManagementService.php
├── Repositories/
│   ├── Contracts/
│   │   ├── BookRepositoryInterface.php
│   │   ├── WalletTransactionRepositoryInterface.php
│   │   ├── PayoutRepositoryInterface.php
│   │   ├── UserRepositoryInterface.php
│   │   └── RevSyncLogRepositoryInterface.php
│   ├── BookRepository.php
│   ├── WalletTransactionRepository.php
│   ├── PayoutRepository.php
│   ├── UserRepository.php
│   └── RevSyncLogRepository.php
└── Providers/
    └── RepositoryServiceProvider.php
```

This architecture provides a solid foundation for scalable, maintainable, and testable code.
