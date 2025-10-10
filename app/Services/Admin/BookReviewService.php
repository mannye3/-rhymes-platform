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
        \Log::info('BookReviewService: reviewBook called', [
            'book_id' => $book->id,
            'data' => $data,
            'admin_id' => $admin->id,
        ]);
        
        $this->validateReviewData($data);
        
        $oldStatus = $book->status;
        
        // Update book status
        $updated = $book->update($data);
        
        \Log::info('BookReviewService: book update result', [
            'book_id' => $book->id,
            'updated' => $updated,
            'old_status' => $oldStatus,
            'new_status' => $data['status'] ?? null,
        ]);
        
        if ($updated) {
            // Handle author promotion if first book is accepted and user is not already an author
            if ($data['status'] === 'accepted' && !$book->user->hasRole('author') && !$book->user->hasRole('admin')) {
                \Log::info('BookReviewService: Promoting user to author', [
                    'user_id' => $book->user->id,
                    'book_id' => $book->id, 
                ]);
                $this->promoteUserToAuthor($book->user);
            }
            
            // Send notification to the author
            \Log::info('BookReviewService: Sending notification to author', [
                'user_id' => $book->user->id,
                'book_id' => $book->id,
                'old_status' => $oldStatus,
                'new_status' => $data['status'],
            ]);
            $book->user->notify(new BookStatusChanged($book, $oldStatus, $data['status']));
        }
        
        return $updated;
    }

    /**
     * Promote user to author role
     */
    private function promoteUserToAuthor(User $user): void
    {
        \Log::info('BookReviewService: Assigning author role to user', [
            'user_id' => $user->id,
        ]);
        
        $user->assignRole('author');
        $user->update([
            'promoted_to_author_at' => now()
        ]);
        
        \Log::info('BookReviewService: User promoted to author', [
            'user_id' => $user->id,
            'promoted_at' => $user->promoted_to_author_at,
        ]);
    }

    /**
     * Validate review data
     */
    private function validateReviewData(array $data): void
    {
        \Log::info('BookReviewService: Validating review data', $data);
        
        $validStatuses = ['accepted', 'rejected', 'stocked'];
        
        if (!isset($data['status']) || !in_array($data['status'], $validStatuses)) {
            \Log::error('BookReviewService: Invalid book status', $data);
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