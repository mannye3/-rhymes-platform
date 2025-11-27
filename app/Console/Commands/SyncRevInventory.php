<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RevService;
use App\Models\Book;

class SyncRevInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rev:sync-inventory {--book-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync inventory data from ERPREV and update book statuses';

    /**
     * Execute the console command.
     */
    public function handle(RevService $revService)
    {
        $this->info('Starting ERPREV inventory sync...');
        
        // Prepare filters for the ERPREV API
        $filters = [];
        
        $bookId = $this->option('book-id');
        if ($bookId) {
            $book = Book::find($bookId);
            if (!$book || !$book->rev_book_id) {
                $this->error("Book not found or not registered in ERPREV");
                return 1;
            }
            $filters['product_id'] = $book->rev_book_id;
        }
        
        // Fetch inventory data from ERPREV
        $this->info("Fetching inventory data from ERPREV...");
        $result = $revService->getStockList($filters);
        
        if (!$result['success']) {
            $this->error("Failed to fetch inventory data: {$result['message']}");
            return 1;
        }
        
        $inventoryData = $result['data']['data'] ?? [];
        $this->info("Found " . count($inventoryData) . " inventory records to process");
        
        $updatedCount = 0;
        $errorCount = 0;
        
        // Process each inventory record
        foreach ($inventoryData as $item) {
            try {
                // Find the corresponding book in our system
                $book = Book::where('rev_book_id', $item['product_id'])->first();
                
                if (!$book) {
                    $this->warn("Book with ERPREV product ID {$item['product_id']} not found in system");
                    $errorCount++;
                    continue;
                }
                
                // Update book status based on inventory levels
                // If book is currently 'accepted' and now has stock, update to 'stocked'
                if ($book->status === 'accepted' && ($item['quantity_on_hand'] ?? 0) > 0) {
                    $book->update(['status' => 'stocked']);
                    $this->line("Updated book '{$book->title}' status to 'stocked' ({$item['quantity_on_hand']} units in stock)");
                    $updatedCount++;
                } elseif ($book->status === 'stocked' && ($item['quantity_on_hand'] ?? 0) <= 0) {
                    // If book was stocked but now out of stock, we might want to notify
                    $this->line("Book '{$book->title}' is out of stock ({$item['quantity_on_hand']} units)");
                }
                
            } catch (\Exception $e) {
                $this->error("Error processing inventory item {$item['product_id']}: " . $e->getMessage());
                $errorCount++;
            }
        }
        
        $this->info("Inventory sync completed. Updated: {$updatedCount}, Errors: {$errorCount}");
        
        return 0;
    }
}