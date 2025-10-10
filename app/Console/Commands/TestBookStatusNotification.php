<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Book;
use App\Notifications\BookStatusChanged;

class TestBookStatusNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:book-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the book status changed notification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get a test user
        $user = User::first();
        if (!$user) {
            $this->error('No user found in the database.');
            return 1;
        }

        // Get a test book
        $book = Book::first();
        if (!$book) {
            $this->error('No book found in the database.');
            return 1;
        }

        // Send the notification
        $user->notify(new BookStatusChanged($book, 'pending', 'accepted'));
        
        $this->info('Book status changed notification sent successfully!');
        return 0;
    }
}