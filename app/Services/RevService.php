<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Repositories\Contracts\RevSyncLogRepositoryInterface;

class RevService
{
    private $baseUrl;
    private $apiKey;
    private $enabled;

    public function __construct(
        private RevSyncLogRepositoryInterface $revSyncLogRepository
    ) {
        $this->baseUrl = config('services.rev.base_url');
        $this->apiKey = config('services.rev.api_key');
        $this->enabled = config('services.rev.enabled', false);
    }

    /**
     * Create or update a book in REV system
     */
    public function syncBook($book)
    {
        if (!$this->enabled) {
            return ['success' => false, 'message' => 'REV sync is disabled'];
        }

        try {
            $payload = [
                'isbn' => $book->isbn,
                'title' => $book->title,
                'author' => $book->user->name,
                'genre' => $book->genre,
                'price' => $book->price,
                'book_type' => $book->book_type,
                'description' => $book->description,
                'status' => $book->status,
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/api/books', $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                $this->logSync('books', 'success', 'Book synced successfully', [
                    'book_id' => $book->id,
                    'rev_book_id' => $data['book_id'] ?? null,
                    'payload' => $payload,
                    'response' => $data,
                ]);

                return [
                    'success' => true,
                    'rev_book_id' => $data['book_id'] ?? null,
                    'message' => 'Book synced successfully'
                ];
            } else {
                throw new \Exception('REV API returned error: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->logSync('books', 'error', $e->getMessage(), [
                'book_id' => $book->id,
                'payload' => $payload ?? null,
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Fetch sales data from REV
     */
    public function fetchSales($bookId = null, $dateFrom = null, $dateTo = null)
    {
        if (!$this->enabled) {
            return ['success' => false, 'message' => 'REV sync is disabled'];
        }

        try {
            $params = [];
            if ($bookId) $params['book_id'] = $bookId;
            if ($dateFrom) $params['date_from'] = $dateFrom;
            if ($dateTo) $params['date_to'] = $dateTo;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/api/sales', $params);

            if ($response->successful()) {
                $data = $response->json();
                
                $this->logSync('sales', 'success', 'Sales data fetched successfully', [
                    'params' => $params,
                    'count' => count($data['sales'] ?? []),
                ]);

                return ['success' => true, 'data' => $data];
            } else {
                throw new \Exception('REV API returned error: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->logSync('sales', 'error', $e->getMessage(), [
                'params' => $params ?? null,
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Fetch inventory data from REV
     */
    public function fetchInventory($bookId = null)
    {
        if (!$this->enabled) {
            return ['success' => false, 'message' => 'REV sync is disabled'];
        }

        try {
            $params = $bookId ? ['book_id' => $bookId] : [];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/api/inventory', $params);

            if ($response->successful()) {
                $data = $response->json();
                
                $this->logSync('inventory', 'success', 'Inventory data fetched successfully', [
                    'params' => $params,
                    'count' => count($data['inventory'] ?? []),
                ]);

                return ['success' => true, 'data' => $data];
            } else {
                throw new \Exception('REV API returned error: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->logSync('inventory', 'error', $e->getMessage(), [
                'params' => $params ?? null,
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Log sync operations
     */
    private function logSync($area, $status, $message, $payload = null)
    {
        $this->revSyncLogRepository->create([
            'area' => $area,
            'status' => $status,
            'message' => $message,
            'payload' => $payload,
        ]);

        Log::info("REV Sync [{$area}] {$status}: {$message}", $payload ?? []);
    }

    /**
     * Test REV connection
     */
    public function testConnection()
    {
        if (!$this->enabled) {
            return ['success' => false, 'message' => 'REV sync is disabled'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/api/health');

            return [
                'success' => $response->successful(),
                'message' => $response->successful() ? 'Connection successful' : 'Connection failed',
                'status_code' => $response->status(),
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
