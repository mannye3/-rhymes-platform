<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Book;
use App\Models\User;

echo "=== ERPREV Auto-Registration Test ===\n\n";

// Find a pending book or show status
$pendingBooks = Book::where('status', 'pending')->with('user')->get();
$acceptedBooks = Book::where('status', 'accepted')->with('user')->get();

echo "📊 Current Book Status:\n";
echo "   Pending Books: " . $pendingBooks->count() . "\n";
echo "   Accepted Books: " . $acceptedBooks->count() . "\n\n";

if ($pendingBooks->count() > 0) {
    echo "📚 Pending Books (Ready to Test):\n\n";
    foreach ($pendingBooks as $book) {
        echo "   - ID: {$book->id}\n";
        echo "     Title: {$book->title}\n";
        echo "     ISBN: {$book->isbn}\n";
        echo "     Author: {$book->user->name}\n";
        echo "     Status: {$book->status}\n";
        echo "     REV Book ID: " . ($book->rev_book_id ?? 'Not set (will be set on approval)') . "\n\n";
    }
    
    echo "💡 To test auto-registration:\n";
    echo "   1. Login as admin\n";
    echo "   2. Go to Admin → Books → Pending\n";
    echo "   3. Click 'Accept' on any book\n";
    echo "   4. Run: php verify_erprev.php\n";
    echo "   5. The book will have a REV Product ID!\n\n";
} else {
    echo "ℹ️  No pending books to test with.\n\n";
    echo "💡 To create a test book:\n";
    echo "   1. Login as a user (not admin)\n";
    echo "   2. Go to 'Submit Book'\n";
    echo "   3. Fill in the form and submit\n";
    echo "   4. Then approve it as admin\n\n";
}

echo "✅ Accepted Books (Already Registered):\n\n";
if ($acceptedBooks->count() > 0) {
    foreach ($acceptedBooks as $book) {
        $hasRevId = $book->rev_book_id ? '✅' : '❌';
        echo "   {$hasRevId} ID: {$book->id} | {$book->title}\n";
        echo "      REV Product ID: " . ($book->rev_book_id ?? 'NOT SET') . "\n\n";
    }
} else {
    echo "   No accepted books yet.\n\n";
}

echo "🔍 How to Verify Auto-Registration:\n\n";
echo "   Before Approval:\n";
echo "   - Book status: 'pending'\n";
echo "   - rev_book_id: NULL\n\n";
echo "   After Admin Approves:\n";
echo "   - Book status: 'accepted'\n";
echo "   - rev_book_id: [ERPREV Product ID] ← Automatically set!\n\n";

echo "📝 Check Logs:\n";
echo "   tail -f storage/logs/laravel.log\n\n";
echo "   Look for:\n";
echo "   - 'BookReviewService: Registering book in ERPREV'\n";
echo "   - 'BookReviewService: Book registered in ERPREV successfully'\n\n";

echo "=== Test Guide Complete ===\n";
