<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Book;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@rhymes.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Create author user
        $author = User::create([
            'name' => 'John Author',
            'email' => 'author@rhymes.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'phone' => '+1234567890',
            'payment_details' => [
                'bank_name' => 'Demo Bank',
                'account_number' => '1234567890',
                'routing_number' => '987654321'
            ],
            'promoted_to_author_at' => now(),
        ]);
        $author->assignRole('author');

        // Create regular user
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@rhymes.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $user->assignRole('user');

        // Create sample books
        $book1 = Book::create([
            'user_id' => $author->id,
            'isbn' => '9781234567890',
            'title' => 'The Great Adventure',
            'genre' => 'Fiction',
            'price' => 19.99,
            'book_type' => 'both',
            'description' => 'An epic tale of adventure and discovery in a magical world.',
            'status' => 'accepted',
            'admin_notes' => 'Great book! Approved for stocking.',
            'rev_book_id' => 'REV-001',
        ]);

        $book2 = Book::create([
            'user_id' => $author->id,
            'isbn' => '9781234567891',
            'title' => 'Business Success Guide',
            'genre' => 'Business',
            'price' => 24.99,
            'book_type' => 'digital',
            'description' => 'A comprehensive guide to building a successful business.',
            'status' => 'stocked',
            'admin_notes' => 'Excellent content. Now available in our inventory.',
            'rev_book_id' => 'REV-002',
        ]);

        $book3 = Book::create([
            'user_id' => $user->id,
            'isbn' => '9781234567892',
            'title' => 'Mystery of the Lost City',
            'genre' => 'Mystery',
            'price' => 16.99,
            'book_type' => 'physical',
            'description' => 'A thrilling mystery set in an ancient lost city.',
            'status' => 'pending',
        ]);

        // Create sample wallet transactions (sales)
        WalletTransaction::create([
            'user_id' => $author->id,
            'book_id' => $book1->id,
            'type' => 'sale',
            'amount' => 15.99,
            'meta' => [
                'sale_date' => now()->subDays(5),
                'customer_location' => 'New York, NY',
                'sale_type' => 'physical'
            ],
        ]);

        WalletTransaction::create([
            'user_id' => $author->id,
            'book_id' => $book1->id,
            'type' => 'sale',
            'amount' => 15.99,
            'meta' => [
                'sale_date' => now()->subDays(3),
                'customer_location' => 'Los Angeles, CA',
                'sale_type' => 'digital'
            ],
        ]);

        WalletTransaction::create([
            'user_id' => $author->id,
            'book_id' => $book2->id,
            'type' => 'sale',
            'amount' => 19.99,
            'meta' => [
                'sale_date' => now()->subDays(1),
                'customer_location' => 'Chicago, IL',
                'sale_type' => 'digital'
            ],
        ]);

        $this->command->info('Demo data created successfully!');
        $this->command->info('Admin: admin@rhymes.com / password');
        $this->command->info('Author: author@rhymes.com / password');
        $this->command->info('User: user@rhymes.com / password');
    }
}
