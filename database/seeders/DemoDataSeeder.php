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
        $admin = User::firstOrCreate([
            'email' => 'admin@rhymes.com',
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        
        // Ensure admin has the admin role
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Create author user
        $author = User::firstOrCreate([
            'email' => 'author@rhymes.com',
        ], [
            'name' => 'John Author',
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
        
        // Ensure author has the author role
        if (!$author->hasRole('author')) {
            $author->assignRole('author');
        }

        // Create additional authors
        $author2 = User::firstOrCreate([
            'email' => 'sarah@rhymes.com',
        ], [
            'name' => 'Sarah Writer',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'phone' => '+1987654321',
            'payment_details' => [
                'bank_name' => 'Demo Bank',
                'account_number' => '9876543210',
                'routing_number' => '123456789'
            ],
            'promoted_to_author_at' => now(),
        ]);
        $author2->assignRole('author');

        $author3 = User::firstOrCreate([
            'email' => 'michael@rhymes.com',
        ], [
            'name' => 'Michael Pen',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'phone' => '+1555666777',
            'payment_details' => [
                'bank_name' => 'Demo Bank',
                'account_number' => '5556667770',
                'routing_number' => '777666555'
            ],
            'promoted_to_author_at' => now(),
        ]);
        $author3->assignRole('author');

        // Create regular user
        $user = User::firstOrCreate([
            'email' => 'user@rhymes.com',
        ], [
            'name' => 'Regular User',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $user->assignRole('user');

        // Create additional regular users
        $user2 = User::firstOrCreate([
            'email' => 'jane@rhymes.com',
        ], [
            'name' => 'Jane Reader',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $user2->assignRole('user');

        $user3 = User::firstOrCreate([
            'email' => 'bob@rhymes.com',
        ], [
            'name' => 'Bob Customer',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $user3->assignRole('user');

        // Create sample books for first author (avoid duplicates)
        $book1 = Book::firstOrCreate([
            'isbn' => '9781234567890',
        ], [
            'user_id' => $author->id,
            'title' => 'The Great Adventure',
            'genre' => 'Fiction',
            'price' => 19.99,
            'book_type' => 'both',
            'description' => 'An epic tale of adventure and discovery in a magical world.',
            'status' => 'accepted',
            'admin_notes' => 'Great book! Approved for stocking.',
            'rev_book_id' => 'REV-001',
        ]);

        $book2 = Book::firstOrCreate([
            'isbn' => '9781234567891',
        ], [
            'user_id' => $author->id,
            'title' => 'Business Success Guide',
            'genre' => 'Business',
            'price' => 24.99,
            'book_type' => 'digital',
            'description' => 'A comprehensive guide to building a successful business.',
            'status' => 'stocked',
            'admin_notes' => 'Excellent content. Now available in our inventory.',
            'rev_book_id' => 'REV-002',
        ]);

        // Create books for second author
        $book3 = Book::firstOrCreate([
            'isbn' => '9781234567892',
        ], [
            'user_id' => $author2->id,
            'title' => 'Mystery of the Lost City',
            'genre' => 'Mystery',
            'price' => 16.99,
            'book_type' => 'physical',
            'description' => 'A thrilling mystery set in an ancient lost city.',
            'status' => 'accepted',
            'admin_notes' => 'Intriguing plot. Approved.',
            'rev_book_id' => 'REV-003',
        ]);

        $book4 = Book::firstOrCreate([
            'isbn' => '9781234567893',
        ], [
            'user_id' => $author2->id,
            'title' => 'Science Fiction Odyssey',
            'genre' => 'Science Fiction',
            'price' => 21.99,
            'book_type' => 'digital',
            'description' => 'Journey through space and time in this epic sci-fi adventure.',
            'status' => 'stocked',
            'admin_notes' => 'Great sci-fi elements. Available for sale.',
            'rev_book_id' => 'REV-004',
        ]);

        // Create books for third author
        $book5 = Book::firstOrCreate([
            'isbn' => '9781234567894',
        ], [
            'user_id' => $author3->id,
            'title' => 'Romance in Paris',
            'genre' => 'Romance',
            'price' => 14.99,
            'book_type' => 'both',
            'description' => 'A heartwarming love story set in the city of lights.',
            'status' => 'accepted',
            'admin_notes' => 'Beautifully written romance.',
            'rev_book_id' => 'REV-005',
        ]);

        $book6 = Book::firstOrCreate([
            'isbn' => '9781234567895',
        ], [
            'user_id' => $author3->id,
            'title' => 'Cooking for Beginners',
            'genre' => 'Cooking',
            'price' => 18.99,
            'book_type' => 'physical',
            'description' => 'Easy recipes for those just starting their culinary journey.',
            'status' => 'pending',
            'admin_notes' => 'Under review.',
        ]);

        // Create sample wallet transactions (sales) for first author
        // We'll create new transactions each time to avoid duplicates
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

        // Create sample wallet transactions (sales) for second author
        WalletTransaction::create([
            'user_id' => $author2->id,
            'book_id' => $book3->id,
            'type' => 'sale',
            'amount' => 13.59,
            'meta' => [
                'sale_date' => now()->subDays(7),
                'customer_location' => 'Miami, FL',
                'sale_type' => 'physical'
            ],
        ]);

        WalletTransaction::create([
            'user_id' => $author2->id,
            'book_id' => $book3->id,
            'type' => 'sale',
            'amount' => 13.59,
            'meta' => [
                'sale_date' => now()->subDays(4),
                'customer_location' => 'Seattle, WA',
                'sale_type' => 'digital'
            ],
        ]);

        WalletTransaction::create([
            'user_id' => $author2->id,
            'book_id' => $book4->id,
            'type' => 'sale',
            'amount' => 17.59,
            'meta' => [
                'sale_date' => now()->subDays(2),
                'customer_location' => 'Boston, MA',
                'sale_type' => 'digital'
            ],
        ]);

        // Create sample wallet transactions (sales) for third author
        WalletTransaction::create([
            'user_id' => $author3->id,
            'book_id' => $book5->id,
            'type' => 'sale',
            'amount' => 11.99,
            'meta' => [
                'sale_date' => now()->subDays(6),
                'customer_location' => 'Denver, CO',
                'sale_type' => 'physical'
            ],
        ]);

        WalletTransaction::create([
            'user_id' => $author3->id,
            'book_id' => $book5->id,
            'type' => 'sale',
            'amount' => 11.99,
            'meta' => [
                'sale_date' => now()->subDays(3),
                'customer_location' => 'Austin, TX',
                'sale_type' => 'digital'
            ],
        ]);

        $this->command->info('Demo data created successfully!');
        $this->command->info('Admin: admin@rhymes.com / password');
        $this->command->info('Authors:');
        $this->command->info('  - author@rhymes.com / password');
        $this->command->info('  - sarah@rhymes.com / password');
        $this->command->info('  - michael@rhymes.com / password');
        $this->command->info('Users:');
        $this->command->info('  - user@rhymes.com / password');
        $this->command->info('  - jane@rhymes.com / password');
        $this->command->info('  - bob@rhymes.com / password');
    }
}