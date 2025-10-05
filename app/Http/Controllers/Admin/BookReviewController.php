<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Services\Admin\BookReviewService;

class BookReviewController extends Controller
{
    public function __construct(
        private BookReviewService $bookReviewService
    ) {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = Book::with(['user', 'walletTransactions']);
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('genre', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('genre')) {
            $query->where('genre', $request->genre);
        }
        
        $books = $query->latest()->paginate(15);
        $genres = Book::distinct()->pluck('genre')->filter()->sort();
        
        return view('admin.books.index', compact('books', 'genres'));
    }

    public function show(Book $book)
    {
        $book->load(['user', 'walletTransactions']);
        
        // Calculate book statistics
        $stats = [
            'total_sales' => $book->walletTransactions()->where('type', 'sale')->count(),
            'total_revenue' => $book->walletTransactions()->where('type', 'sale')->sum('amount'),
            'average_sale_price' => $book->walletTransactions()->where('type', 'sale')->avg('amount') ?? 0,
        ];
        
        return view('admin.books.show', compact('book', 'stats'));
    }

    public function review(Request $request, Book $book)
    {
        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected,stocked',
            'admin_notes' => 'nullable|string',
            'rev_book_id' => 'nullable|string|unique:books,rev_book_id,' . $book->id,
        ]);
        
        $updated = $this->bookReviewService->reviewBook($book, $validated, auth()->user());
        
        if ($updated) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Book status updated successfully! Author has been notified.'
                ]);
            }
            return back()->with('success', 'Book status updated successfully! Author has been notified.');
        }
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update book status.'
            ], 422);
        }
        return back()->with('error', 'Failed to update book status.');
    }

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:accept,reject,delete',
            'book_ids' => 'required|array',
            'book_ids.*' => 'exists:books,id',
            'admin_notes' => 'nullable|string',
        ]);

        $books = Book::whereIn('id', $validated['book_ids'])->get();
        $successCount = 0;

        foreach ($books as $book) {
            $status = match($validated['action']) {
                'accept' => 'accepted',
                'reject' => 'rejected',
                'delete' => null,
            };

            if ($validated['action'] === 'delete') {
                if ($book->walletTransactions()->count() === 0) {
                    $book->delete();
                    $successCount++;
                }
            } else {
                $updated = $this->bookReviewService->reviewBook($book, [
                    'status' => $status,
                    'admin_notes' => $validated['admin_notes'] ?? null,
                ], auth()->user());
                
                if ($updated) {
                    $successCount++;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully processed {$successCount} books."
        ]);
    }
}
