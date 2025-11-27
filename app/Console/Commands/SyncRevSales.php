<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RevService;
use App\Models\Book;
use App\Models\WalletTransaction;
use Carbon\Carbon;

class SyncRevSales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rev:sync-sales {--since=} {--book-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync sales data from ERPREV and update author wallets';

    /**
     * Execute the console command.
     */
    public function handle(RevService $revService)
    {
        $this->info('Starting ERPREV sales sync...');
        
        // Determine the date range for syncing
        $since = $this->option('since') ? Carbon::parse($this->option('since')) : Carbon::now()->subDay();
        $bookId = $this->option('book-id');
        
        // Prepare filters for the ERPREV API
        $filters = [
            'date_from' => $since->format('Y-m-d'),
            'date_to' => Carbon::now()->format('Y-m-d'),
        ];
        
        if ($bookId) {
            $book = Book::find($bookId);
            if (!$book || !$book->rev_book_id) {
                $this->error("Book not found or not registered in ERPREV");
                return 1;
            }
            $filters['product_id'] = $book->rev_book_id;
        }
        
        // Fetch sales data from ERPREV
        $this->info("Fetching sales data from ERPREV since {$filters['date_from']}...");
        $result = $revService->getSalesItems($filters);
        
        if (!$result['success']) {
            $this->error("Failed to fetch sales data: {$result['message']}");
            return 1;
        }
        
        $salesData = $result['data']['data'] ?? [];
        $this->info("Found " . count($salesData) . " sales records to process");
        
        $processedCount = 0;
        $errorCount = 0;
        
        // Process each sale record
        foreach ($salesData as $sale) {
            try {
                // Find the corresponding book in our system
                $book = Book::where('rev_book_id', $sale['product_id'])->first();
                
                if (!$book) {
                    $this->warn("Book with ERPREV product ID {$sale['product_id']} not found in system");
                    $errorCount++;
                    continue;
                }
                
                // Calculate author earnings (assuming 70% goes to author)
                $totalAmount = $sale['total_amount'];
                $authorEarnings = $totalAmount * 0.7; // 70% to author, 30% to platform
                
                // Check if this sale has already been processed
                $existingTransaction = WalletTransaction::where('meta->erprev_sale_id', $sale['sale_id'])->first();
                
                if ($existingTransaction) {
                    $this->line("Sale {$sale['sale_id']} already processed, skipping");
                    continue;
                }
                
                // Create wallet transaction for the author
                WalletTransaction::create([
                    'user_id' => $book->user_id,
                    'book_id' => $book->id,
                    'type' => 'sale',
                    'amount' => $authorEarnings,
                    'meta' => [
                        'erprev_sale_id' => $sale['sale_id'],
                        'invoice_id' => $sale['invoice_id'] ?? null,
                        'quantity_sold' => $sale['quantity_sold'],
                        'unit_price' => $sale['unit_price'],
                        'total_amount' => $totalAmount,
                        'sale_date' => $sale['sale_date'],
                        'location' => $sale['location'] ?? null,
                        'description' => "Sale of {$sale['quantity_sold']} copies of '{$book->title}'",
                    ],
                ]);
                
                $this->line("Processed sale {$sale['sale_id']} for book '{$book->title}' - Author earned ₦" . number_format($authorEarnings, 2));
                $processedCount++;
            } catch (\Exception $e) {
                $this->error("Error processing sale {$sale['sale_id']}: " . $e->getMessage());
                $errorCount++;
            }
        }
        
        $this->info("Sales sync completed. Processed: {$processedCount}, Errors: {$errorCount}");
        
        return 0;
    }
}