<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BookService;
use App\Models\Book;

class BookController extends Controller
{
    public function __construct(
        private BookService $bookService
    ) {
        $this->middleware(['auth', 'role:author|admin']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $books = $this->bookService->getUserBooks($user);
        return view('author.books.index', compact('books'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('author.books.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate(
            $this->bookService->validateBookData($request->all())
        );

        $this->bookService->createBook($user, $validated);

        return redirect()->route('author.books.index')
            ->with('success', 'Book submitted successfully for review!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $this->authorize('view', $book);
        return view('author.books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        $this->authorize('update', $book);
        return view('author.books.edit', compact('book'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        $this->authorize('update', $book);

        $validated = $request->validate(
            $this->bookService->validateBookData($request->all(), $book)
        );

        $this->bookService->updateBook($book, $validated);

        return redirect()->route('author.books.index')
            ->with('success', 'Book updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);
        
        // Check if book has any transactions before deleting
        if ($book->walletTransactions()->count() > 0) {
            return redirect()->route('author.books.index')
                ->with('error', 'Cannot delete book with existing transactions.');
        }
        
        $this->bookService->deleteBook($book);

        return redirect()->route('author.books.index')
            ->with('success', 'Book deleted successfully!');
    }

    /**
     * Restore a soft deleted book
     */
    public function restore($id)
    {
        $book = $this->bookService->getBookByIdWithTrashed($id);
        
        if (!$book) {
            return redirect()->route('author.books.index')
                ->with('error', 'Book not found.');
        }
        
        $this->authorize('delete', $book);
        
        if ($book->trashed()) {
            $this->bookService->restoreBook($book);
            return redirect()->route('author.books.index')
                ->with('success', 'Book restored successfully!');
        }
        
        return redirect()->route('author.books.index')
            ->with('error', 'Book is not deleted.');
    }
}
