<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        $query = Book::with(['user', 'walletTransactions'])->whereNull('deleted_at');
        
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
       

        // Log the incoming request data for debugging
        Log::info('Book review request data:', [
            'book_id' => $book->id,
            'request_data' => $request->all(),
            'user_id' => auth()->id(),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'all_headers' => $request->headers->all(),
        ]);
        
        // Log raw input
        $rawInput = file_get_contents('php://input');
        Log::info('Raw input data:', [
            'raw_input' => $rawInput,
        ]);
        
        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected,stocked',
            'admin_notes' => 'nullable|string',
            'rev_book_id' => 'nullable|string|unique:books,rev_book_id,' . $book->id . ',id',
        ]);
        
        // Log the validated data
        Log::info('Book review validated data:', $validated);
        
        try {
            $updated = $this->bookReviewService->reviewBook($book, $validated, auth()->user());
            
            Log::info('Book review service result:', [
                'book_id' => $book->id,
                'updated' => $updated,
                'new_status' => $validated['status'] ?? null,
            ]);
            
            if ($updated) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Book status updated successfully! Author has been notified.'
                    ]);
                }
                return back()->with('success', 'Book status updated successfully! Author has been notified.');
            } else {
                Log::warning('Book review failed - service returned false', [
                    'book_id' => $book->id,
                    'validated_data' => $validated,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Book review error: ' . $e->getMessage(), [
                'exception' => $e,
                'book_id' => $book->id,
                'request_data' => $request->all(),
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update book status: ' . $e->getMessage()
                ], 422);
            }
            return back()->with('error', 'Failed to update book status: ' . $e->getMessage());
        }
        
        Log::warning('Book review failed - unknown reason', [
            'book_id' => $book->id,
            'validated_data' => $validated,
        ]);
        
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
            'action' => 'required|in:accept,reject,delete,restore,forceDelete',
            'book_ids' => 'required|array',
            'book_ids.*' => 'exists:books,id',
            'admin_notes' => 'nullable|string',
        ]);

        $books = Book::withTrashed()->whereIn('id', $validated['book_ids'])->get();
        $successCount = 0;

        foreach ($books as $book) {
            $status = match($validated['action']) {
                'accept' => 'accepted',
                'reject' => 'rejected',
                'delete' => null,
                'restore' => null,
                'forceDelete' => null,
            };

            switch ($validated['action']) {
                case 'delete':
                    if ($book->walletTransactions()->count() === 0) {
                        $book->delete();
                        $successCount++;
                    }
                    break;
                case 'restore':
                    if ($book->trashed()) {
                        $book->restore();
                        $successCount++;
                    }
                    break;
                case 'forceDelete':
                    if ($book->trashed()) {
                        // Check if there are any transactions before force deleting
                        if ($book->walletTransactions()->count() === 0) {
                            $book->forceDelete();
                            $successCount++;
                        }
                    }
                    break;
                default:
                    try {
                        $updated = $this->bookReviewService->reviewBook($book, [
                            'status' => $status,
                            'admin_notes' => $validated['admin_notes'] ?? null,
                        ], auth()->user());
                        
                        if ($updated) {
                            $successCount++;
                        }
                    } catch (\Exception $e) {
                        Log::error('Bulk action book review error: ' . $e->getMessage());
                    }
                    break;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully processed {$successCount} books."
        ]);
    }
}