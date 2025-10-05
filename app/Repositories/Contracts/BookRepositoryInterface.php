<?php

namespace App\Repositories\Contracts;

use App\Models\Book;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BookRepositoryInterface
{
    /**
     * Get paginated books for a user
     */
    public function getPaginatedByUser(int $userId, int $perPage = 10): LengthAwarePaginator;

    /**
     * Find book by ID
     */
    public function findById(int $id): ?Book;

    /**
     * Create a new book
     */
    public function create(array $data): Book;

    /**
     * Update a book
     */
    public function update(Book $book, array $data): bool;

    /**
     * Delete a book
     */
    public function delete(Book $book): bool;

    /**
     * Get books by status
     */
    public function getByStatus(string $status): Collection;

    /**
     * Get books by user and status
     */
    public function getByUserAndStatus(int $userId, string $status): Collection;

    /**
     * Get book sales analytics
     */
    public function getSalesAnalytics(int $bookId): array;

    /**
     * Get books with sales data for user
     */
    public function getBooksWithSalesForUser(int $userId): Collection;
}
