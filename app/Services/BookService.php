<?php

namespace App\Services;

use App\Models\Book;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BookService
{
    /**
     * Get paginated books for user (excluding soft deleted)
     */
    public function getUserBooks(User $user, int $perPage = 10): LengthAwarePaginator
    {
        return Book::where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new book
     */
    public function createBook(User $user, array $data): Book
    {
        $data['user_id'] = $user->id;
        $data['status'] = 'pending'; // Default status for new books

        return Book::create($data);
    }

    /**
     * Update a book
     */
    public function updateBook(Book $book, array $data): bool
    {
        return $book->update($data);
    }

    /**
     * Delete a book (soft delete)
     */
    public function deleteBook(Book $book): bool
    {
        return $book->delete();
    }

    /**
     * Restore a soft deleted book
     */
    public function restoreBook(Book $book): bool
    {
        return $book->restore();
    }

    /**
     * Permanently delete a book
     */
    public function forceDeleteBook(Book $book): bool
    {
        return $book->forceDelete();
    }

    /**
     * Get book by ID (excluding soft deleted)
     */
    public function getBookById(int $id): ?Book
    {
        return Book::find($id);
    }

    /**
     * Get book by ID including soft deleted
     */
    public function getBookByIdWithTrashed(int $id): ?Book
    {
        return Book::withTrashed()->find($id);
    }

    /**
     * Get all books including soft deleted
     */
    public function getAllBooksWithTrashed(): Collection
    {
        return Book::withTrashed()->get();
    }

    /**
     * Get only soft deleted books
     */
    public function getOnlyTrashedBooks(): Collection
    {
        return Book::onlyTrashed()->get();
    }

    /**
     * Get books by status
     */
    public function getBooksByStatus(string $status): Collection
    {
        return Book::where('status', $status)->get();
    }

    /**
     * Get user books by status
     */
    public function getUserBooksByStatus(User $user, string $status): Collection
    {
        return Book::where('user_id', $user->id)
            ->where('status', $status)
            ->get();
    }

    /**
     * Get book sales analytics
     */
    public function getBookSalesAnalytics(int $bookId): array
    {
        $book = $this->getBookById($bookId);
        
        if (!$book) {
            return [
                'total_sales' => 0,
                'sales_count' => 0,
            ];
        }

        return [
            'total_sales' => $book->getTotalSales(),
            'sales_count' => $book->getSalesCount(),
        ];
    }

    /**
     * Get books with sales data for user
     */
    public function getBooksWithSalesForUser(User $user): Collection
    {
        return Book::where('user_id', $user->id)
            ->with(['walletTransactions' => function ($query) {
                $query->where('type', 'sale');
            }])
            ->get();
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

        return $this->updateBook($book, $data);
    }

    /**
     * Get author books count
     */
    public function getAuthorBooksCount(int $userId): int
    {
        return Book::where('user_id', $userId)->count();
    }

    /**
     * Get author books by status
     */
    public function getAuthorBooksByStatus(int $userId, string $status): Collection
    {
        return Book::where('user_id', $userId)
            ->where('status', $status)
            ->get();
    }

    /**
     * Get recent books for author
     */
    public function getAuthorRecentBooks(int $userId, int $limit = 5): Collection
    {
        return Book::where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get();
    }
}