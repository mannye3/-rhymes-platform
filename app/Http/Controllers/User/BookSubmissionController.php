<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BookService;
use App\Models\Book;
use App\Models\User;
use App\Notifications\BookSubmitted;

class BookSubmissionController extends Controller
{
    public function __construct(
        private BookService $bookService
    ) {
        $this->middleware(['auth']);
    }

    /**
     * Show the form for creating a new book submission.
     */
    public function create()
    {
        return view('user.books.create');
    }

    /**
     * Store a newly created book submission in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate(
            $this->bookService->validateBookData($request->all())
        );

        $book = $this->bookService->createBook($user, $validated);
        
        // Eager load the user relationship for the notification
        $book->load('user');

        // Notify the user who submitted the book
        $user->notify(new BookSubmitted($book));

        // Notify all admins about the new book submission
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();

        foreach ($admins as $admin) {
            $admin->notify(new BookSubmitted($book));
        }

        return redirect()->route('dashboard')
            ->with('success', 'Book submitted successfully for review! You will be notified once your book is approved and you become an author.');
    }
}