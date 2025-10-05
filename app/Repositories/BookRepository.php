<?php

namespace App\Repositories;

use App\Models\Book;
use App\Repositories\Contracts\BookRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BookRepository implements BookRepositoryInterface
{
    public function getPaginatedByUser(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return Book::where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): ?Book
    {
        return Book::find($id);
    }

    public function create(array $data): Book
    {
        return Book::create($data);
    }

    public function update(Book $book, array $data): bool
    {
        return $book->update($data);
    }

    public function delete(Book $book): bool
    {
        return $book->delete();
    }

    public function getByStatus(string $status): Collection
    {
        return Book::where('status', $status)->get();
    }

    public function getByUserAndStatus(int $userId, string $status): Collection
    {
        return Book::where('user_id', $userId)
            ->where('status', $status)
            ->get();
    }

    public function getSalesAnalytics(int $bookId): array
    {
        $book = $this->findById($bookId);
        
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

    public function getBooksWithSalesForUser(int $userId): Collection
    {
        return Book::where('user_id', $userId)
            ->with(['walletTransactions' => function ($query) {
                $query->where('type', 'sale');
            }])
            ->get();
    }
}
