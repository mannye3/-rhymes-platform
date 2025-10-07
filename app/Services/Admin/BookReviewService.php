<?php

namespace App\Services\Admin;

use App\Models\Book;
use App\Models\User;
use App\Notifications\BookStatusChanged;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BookReviewService
{
    /**
     * Get paginated books for review with filters
     */
    public function getBooksForReview(array $filters = []): LengthAwarePaginator
    {
        $query = Book::with('user');
        
        if (isset($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }
        
        return $query->latest()->paginate(15);
    }

    /**
     * Review a book and update its status
     */
    public function reviewBook(Book $book, array $data, User $admin): bool
    {
        $this->validateReviewData($data);
        
        $oldStatus = $book->status;
        
        // Update book status
        $updated = $book->update($data);
        
        if ($updated) {
            // Handle author promotion if first book is accepted
            if ($data['status'] === 'accepted' && !$book->user->hasRole('author')) {
                $this->promoteUserToAuthor($book->user);
            }
            
            // Send notification to the author
            $book->user->notify(new BookStatusChanged($book, $oldStatus, $data['status']));
        }
        
        return $updated;
    }

    /**
     * Promote user to author role
     */
    private function promoteUserToAuthor(User $user): void
    {
        $user->assignRole('author');
        $user->update([
            'promoted_to_author_at' => now()
        ]);
    }

    /**
     * Validate review data
     */
    private function validateReviewData(array $data): void
    {
        $validStatuses = ['accepted', 'rejected', 'stocked'];
        
        if (!isset($data['status']) || !in_array($data['status'], $validStatuses)) {
            throw new \InvalidArgumentException('Invalid book status');
        }
    }

    /**
     * Get book statistics for admin dashboard
     */
    public function getBookStatistics(): array
    {
        return [
            'total_books' => Book::count(),
            'pending_books' => Book::where('status', 'pending')->count(),
            'accepted_books' => Book::where('status', 'accepted')->count(),
            'rejected_books' => Book::where('status', 'rejected')->count(),
            'stocked_books' => Book::where('status', 'stocked')->count(),
        ];
    }
}