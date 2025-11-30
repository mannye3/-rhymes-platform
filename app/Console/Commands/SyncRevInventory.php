<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RevService;
use App\Models\Book;
use App\Models\WalletTransaction;

class SyncRevInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rev:sync-inventory {--book-id=} {--process-sales-value} {--debug}';

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
            $this->info("Filtering for product_id: {$book->rev_book_id}");
        }
        
        // Fetch inventory data from ERPREV
        $this->info("Fetching inventory data from ERPREV...");
        if (!empty($filters)) {
            $this->info("Using filters: " . json_encode($filters));
        }
        
        $result = $revService->getStockList($filters);
        
        if (!$result['success']) {
            $this->error("Failed to fetch inventory data: {$result['message']}");
            return 1;
        }
        
        $inventoryData = isset($result['data']['records']) ? $result['data']['records'] : [];
        $this->info("Found " . count($inventoryData) . " inventory records from API");
        
        // If we're looking for a specific book, filter the results
        if ($bookId) {
            $targetProductId = $book->rev_book_id;
            $filteredData = array_filter($inventoryData, function($item) use ($targetProductId) {
                $productId = isset($item['ProductID']) ? $item['ProductID'] : (isset($item['product_id']) ? $item['product_id'] : null);
                return $productId == $targetProductId;
            });
            
            $inventoryData = array_values($filteredData);
            $this->info("Filtered to " . count($inventoryData) . " records for product ID {$targetProductId}");
        }
        
        // If we're filtering for a specific book, check if we got data for it
        if ($bookId && count($inventoryData) == 0) {
            $this->warn("No inventory data found for the specified book");
        }
        
        $updatedCount = 0;
        $errorCount = 0;
        $salesValueProcessed = 0;
        
        // Process each inventory record
        foreach ($inventoryData as $item) {
            try {
                // Find the corresponding book in our system
                $productId = isset($item['ProductID']) ? $item['ProductID'] : (isset($item['product_id']) ? $item['product_id'] : null);
                $book = Book::where('rev_book_id', $productId)->first();
                
                if (!$book) {
                    $displayProductId = $productId ?: 'N/A';
                    $this->warn("Book with ERPREV product ID {$displayProductId} not found in system");
                    $errorCount++;
                    continue;
                }
                
                // Debug: Show available fields
                if ($this->option('debug')) {
                    $this->info("Available fields for product {$productId}: " . json_encode(array_keys($item)));
                }
                
                // Update book status based on inventory levels
                // If book is currently 'accepted' and now has stock, update to 'stocked'
                $quantityOnHand = isset($item['UnitsInStock']) ? $item['UnitsInStock'] : (isset($item['quantity_on_hand']) ? $item['quantity_on_hand'] : 0);
                if ($book->status === 'accepted' && $quantityOnHand > 0) {
                    $book->update(['status' => 'stocked']);
                    $this->line("Updated book '{$book->title}' status to 'stocked' ({$quantityOnHand} units in stock)");
                    $updatedCount++;
                } elseif ($book->status === 'stocked' && $quantityOnHand <= 0) {
                    // If book was stocked but now out of stock, we might want to notify
                    $this->line("Book '{$book->title}' is out of stock ({$quantityOnHand} units)");
                }
                
                // Process sales value if option is enabled
                if ($this->option('process-sales-value')) {
                    // Try different possible field names for sales value
                    $salesValue = 0;
                    $possibleSalesFields = ['SalesValue', 'sales_value', 'TotalSales', 'total_sales', 'SalesAmount', 'sales_amount', 'Value', 'value'];
                    foreach ($possibleSalesFields as $field) {
                        if (isset($item[$field])) {
                            // Handle currency values that might have symbols
                            $value = $item[$field];
                            if (is_string($value)) {
                                // Remove currency symbols and commas
                                $value = preg_replace('/[^\d.-]/', '', $value);
                            }
                            if (is_numeric($value)) {
                                $salesValue = (float)$value;
                                if ($this->option('debug')) {
                                    $this->info("Found sales value in field '{$field}': {$salesValue}");
                                }
                                break;
                            }
                        }
                    }
                    
                    // If still no sales value found, try to calculate from available fields
                    if ($salesValue == 0) {
                        $unitPrice = isset($item['SellingPrice']) ? $item['SellingPrice'] : (isset($item['selling_price']) ? $item['selling_price'] : 0);
                        // Handle currency values for unit price
                        if (is_string($unitPrice)) {
                            $unitPrice = preg_replace('/[^\d.-]/', '', $unitPrice);
                        }
                        if (is_numeric($unitPrice)) {
                            $unitPrice = (float)$unitPrice;
                            if ($unitPrice > 0 && $quantityOnHand > 0) {
                                $salesValue = $unitPrice * $quantityOnHand;
                                if ($this->option('debug')) {
                                    $this->info("Calculated sales value from unit price ({$unitPrice}) * quantity ({$quantityOnHand}): {$salesValue}");
                                }
                            }
                        }
                    }
                    
                    if ($salesValue > 0) {
                        // Check if this sales value has already been processed
                        $existingTransaction = WalletTransaction::where('meta->erprev_product_id', $productId)
                            ->where('meta->erprev_inventory_sync_date', now()->toDateString())
                            ->first();
                        
                        if (!$existingTransaction) {
                            // Calculate author earnings (assuming 70% goes to author)
                            $authorEarnings = $salesValue * 0.7; // 70% to author, 30% to platform
                            
                            // Create wallet transaction for the author
                            WalletTransaction::create([
                                'user_id' => $book->user_id,
                                'book_id' => $book->id,
                                'type' => 'sale',
                                'amount' => $authorEarnings,
                                'meta' => [
                                    'erprev_product_id' => $productId,
                                    'erprev_inventory_sync_date' => now()->toDateString(),
                                    'quantity_on_hand' => $quantityOnHand,
                                    'sales_value' => $salesValue,
                                    'description' => "Sales value from inventory sync for '{$book->title}'",
                                ],
                            ]);
                            
                            $this->line("Processed sales value for book '{$book->title}' - Author earned $" . number_format($authorEarnings, 2));
                            $salesValueProcessed++;
                        } else {
                            $this->line("Sales value for book '{$book->title}' already processed today, skipping");
                        }
                    } else {
                        $this->line("No sales value to process for book '{$book->title}' (checked fields: " . implode(', ', $possibleSalesFields) . ")");
                        if ($this->option('debug')) {
                            $unitPrice = isset($item['SellingPrice']) ? $item['SellingPrice'] : (isset($item['selling_price']) ? $item['selling_price'] : 'N/A');
                            $this->info("Unit price: {$unitPrice}, Quantity on hand: {$quantityOnHand}");
                        }
                    }
                }
                
            } catch (\Exception $e) {
                $displayProductId = isset($item['ProductID']) ? $item['ProductID'] : (isset($item['product_id']) ? $item['product_id'] : 'N/A');
                $this->error("Error processing inventory item {$displayProductId}: " . $e->getMessage());
                $errorCount++;
            }
        }
        
        $this->info("Inventory sync completed. Updated: {$updatedCount}, Sales Value Processed: {$salesValueProcessed}, Errors: {$errorCount}");
        
        return 0;
    }
}