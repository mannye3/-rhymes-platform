<?php

namespace App\Services;

use App\Repositories\Contracts\BookRepositoryInterface;
use App\Models\Book;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BookService
{
    public function __construct(
        private BookRepositoryInterface $bookRepository
    ) {}

    /**
     * Get paginated books for user
     */
    public function getUserBooks(User $user, int $perPage = 10): LengthAwarePaginator
    {
        return $this->bookRepository->getPaginatedByUser($user->id, $perPage);
    }

    /**
     * Create a new book
     */
    public function createBook(User $user, array $data): Book
    {
        $data['user_id'] = $user->id;
        $data['status'] = 'pending'; // Default status for new books

        return $this->bookRepository->create($data);
    }

    /**
     * Update a book
     */
    public function updateBook(Book $book, array $data): bool
    {
        return $this->bookRepository->update($book, $data);
    }

    /**
     * Delete a book
     */
    public function deleteBook(Book $book): bool
    {
        return $this->bookRepository->delete($book);
    }

    /**
     * Get book by ID
     */
    public function getBookById(int $id): ?Book
    {
        return $this->bookRepository->findById($id);
    }

    /**
     * Get books by status
     */
    public function getBooksByStatus(string $status): Collection
    {
        return $this->bookRepository->getByStatus($status);
    }

    /**
     * Get user books by status
     */
    public function getUserBooksByStatus(User $user, string $status): Collection
    {
        return $this->bookRepository->getByUserAndStatus($user->id, $status);
    }

    /**
     * Get book sales analytics
     */
    public function getBookSalesAnalytics(int $bookId): array
    {
        return $this->bookRepository->getSalesAnalytics($bookId);
    }

    /**
     * Get books with sales data for user
     */
    public function getBooksWithSalesForUser(User $user): Collection
    {
        return $this->bookRepository->getBooksWithSalesForUser($user->id);
    }

    /**
     * Validate book data
     */
    public function validateBookData(array $data, ?Book $book = null): array
    {
        $rules = [
            'isbn' => 'required|string|unique:books',
            'title' => 'required|string|max:255',
            'genre' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'book_type' => 'required|in:physical,digital,both',
            'description' => 'required|string',
        ];

        // If updating, exclude current book from ISBN uniqueness check
        if ($book) {
            $rules['isbn'] = 'required|string|unique:books,isbn,' . $book->id;
        }

        return $rules;
    }

    /**
     * Check if user can perform action on book
     */
    public function canUserAccessBook(User $user, Book $book): bool
    {
        return $book->user_id === $user->id || $user->hasRole('admin');
    }

    /**
     * Update book status (admin function)
     */
    public function updateBookStatus(Book $book, string $status, ?string $adminNotes = null): bool
    {
        $validStatuses = ['pending', 'accepted', 'rejected', 'stocked'];
        
        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException('Invalid book status');
        }

        $data = ['status' => $status];
        
        if ($adminNotes) {
            $data['admin_notes'] = $adminNotes;
        }

        return $this->bookRepository->update($book, $data);
    }

    /**
     * Get author books count
     */
    public function getAuthorBooksCount(int $userId): int
    {
        return $this->bookRepository->getCountByUser($userId);
    }

    /**
     * Get author books by status
     */
    public function getAuthorBooksByStatus(int $userId, string $status): Collection
    {
        return $this->bookRepository->getByUserAndStatus($userId, $status);
    }

    /**
     * Get recent books for author
     */
    public function getAuthorRecentBooks(int $userId, int $limit = 5): Collection
    {
        return $this->bookRepository->getRecentByUser($userId, $limit);
    }
}
