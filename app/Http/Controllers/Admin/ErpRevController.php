<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RevService;
use App\Models\RevSyncLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ErpRevController extends Controller
{
    private $revService;

    public function __construct(RevService $revService)
    {
        $this->middleware(['auth', 'role:admin']);
        $this->revService = $revService;
        Log::info('ErpRevController constructed', [
            'service_instance' => get_class($revService),
        ]);
    }

    /**
     * Display sync operations monitoring dashboard
     */
    public function syncMonitoring(Request $request)
    {
        Log::info('ERPREV Controller - syncMonitoring called');
        
        // Get filter parameters
        $area = $request->get('area');
        $status = $request->get('status');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        // Build query for sync logs
        $query = RevSyncLog::orderBy('created_at', 'desc');
        
        // Apply filters
        if ($area) {
            $query->where('area', $area);
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo . ' 23:59:59');
        }
        
        // Get paginated logs
        $logs = $query->paginate(20);
        
        // Get summary statistics
        $summary = $this->getSyncSummary();
        
        // Get recent error logs
        $recentErrors = RevSyncLog::where('status', 'error')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('admin.erprev.monitoring', compact('logs', 'summary', 'recentErrors'));
    }
    
    /**
     * Get sync operation summary statistics
     */
    private function getSyncSummary()
    {
        // Total sync operations
        $totalSyncs = RevSyncLog::count();
        
        // Successful sync operations
        $successfulSyncs = RevSyncLog::where('status', 'success')->count();
        
        // Failed sync operations
        $failedSyncs = RevSyncLog::where('status', 'error')->count();
        
        // Success rate
        $successRate = $totalSyncs > 0 ? ($successfulSyncs / $totalSyncs) * 100 : 0;
        
        // Sync operations by area
        $syncsByArea = RevSyncLog::select('area')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('area')
            ->get()
            ->keyBy('area');
        
        // Recent sync operations (last 24 hours)
        $recentSyncs = RevSyncLog::where('created_at', '>=', now()->subDay())
            ->select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->keyBy('status');
        
        return [
            'total' => $totalSyncs,
            'successful' => $successfulSyncs,
            'failed' => $failedSyncs,
            'success_rate' => round($successRate, 2),
            'by_area' => $syncsByArea,
            'recent' => $recentSyncs
        ];
    }

    /**
     * Display sales data from ERPREV
     */
    public function salesData(Request $request)
    {
        Log::info('ERPREV Controller - salesData called', [
            'filters' => $request->all(),
            'request_method' => $request->method(),
            'request_url' => $request->fullUrl(),
        ]);
        
        $filters = [];
        
        // Apply date filters if provided
        if ($request->filled('date_from')) {
            $filters['date_from'] = $request->date_from;
        }
        
        if ($request->filled('date_to')) {
            $filters['date_to'] = $request->date_to;
        }
        
        // Apply product filter if provided
        if ($request->filled('product_id')) {
            $filters['product_id'] = $request->product_id;
        }
        
        Log::info('ERPREV Controller - Calling getSalesItems with filters', [
            'filters' => $filters,
        ]);
        
          $result = $this->revService->getSalesItems($filters);
        
        Log::info('ERPREV Controller - salesData result received', [
            'success' => $result['success'] ?? false,
            'has_data' => isset($result['data']),
            'data_keys' => isset($result['data']) ? array_keys($result['data']) : [],
            'message' => $result['message'] ?? 'N/A',
        ]);
        
        if (!$result['success']) {
            Log::error('ERPREV Controller - salesData failed', [
                'message' => $result['message'] ?? 'Unknown error',
                'full_result' => $result,
            ]);
            
            return back()->with('error', 'Failed to fetch sales data: ' . $result['message']);
        }
        
        // Extract records from the response
        $salesData = $result['data']['records'] ?? [];
        
        Log::info('ERPREV Controller - salesData processed', [
            'record_count' => count($salesData),
            'sample_record' => count($salesData) > 0 ? $salesData[0] : null,
        ]);
        
        return view('admin.erprev.sales', compact('salesData', 'filters'));
    }

    /**
     * Display inventory data from ERPREV
     */
    public function inventoryData(Request $request)
    {
        Log::info('ERPREV Controller - inventoryData called', [
            'filters' => $request->all(),
        ]);
        
        $filters = [];
        
        // Apply filters if provided
        if ($request->filled('product_id')) {
            $filters['product_id'] = $request->product_id;
        }
        
        if ($request->filled('warehouse_id')) {
            $filters['warehouse_id'] = $request->warehouse_id;
        }
        
         $result = $this->revService->getStockList($filters);
        
        Log::info('ERPREV Controller - inventoryData result', [
            'success' => $result['success'] ?? false,
            'has_data' => isset($result['data']),
            'data_keys' => isset($result['data']) ? array_keys($result['data']) : [],
        ]);
        
        if (!$result['success']) {
            Log::error('ERPREV Controller - inventoryData failed', [
                'message' => $result['message'] ?? 'Unknown error',
            ]);
            
            return back()->with('error', 'Failed to fetch inventory data: ' . $result['message']);
        }
        
        // Extract records from the response
        $inventoryData = $result['data']['records'] ?? [];
        
        Log::info('ERPREV Controller - inventoryData processed', [
            'record_count' => count($inventoryData),
            'sample_record' => count($inventoryData) > 0 ? $inventoryData[0] : null,
        ]);
        
        return view('admin.erprev.inventory', compact('inventoryData', 'filters'));
    }

    /**
     * Display product listings from ERPREV
     */
    public function productListings(Request $request)
    {
        Log::info('ERPREV Controller - productListings called', [
            'filters' => $request->all(),
        ]);
        
        $filters = [];
        
        // Apply filters if provided
        if ($request->filled('product_code')) {
            $filters['product_code'] = $request->product_code;
        }
        
        if ($request->filled('category')) {
            $filters['category'] = $request->category;
        }
        
         $result = $this->revService->getProductsList($filters);
        
        Log::info('ERPREV Controller - productListings result', [
            'success' => $result['success'] ?? false,
            'has_data' => isset($result['data']),
            'data_keys' => isset($result['data']) ? array_keys($result['data']) : [],
        ]);
        
        if (!$result['success']) {
            Log::error('ERPREV Controller - productListings failed', [
                'message' => $result['message'] ?? 'Unknown error',
            ]);
            
            return back()->with('error', 'Failed to fetch product listings: ' . $result['message']);
        }
        
        // Extract records from the response
        $products = $result['data']['records'] ?? [];
        
        Log::info('ERPREV Controller - productListings processed', [
            'record_count' => count($products),
            'sample_record' => count($products) > 0 ? $products[0] : null,
        ]);
        
        return view('admin.erprev.products', compact('products', 'filters'));
    }

    /**
     * Display sales summary from ERPREV
     */
    public function salesSummary(Request $request)
    {
        Log::info('ERPREV Controller - salesSummary called', [
            'filters' => $request->all(),
        ]);
        
        $filters = [];
        
        // Apply date filters if provided
        if ($request->filled('date_from')) {
            $filters['date_from'] = $request->date_from;
        }
        
        if ($request->filled('date_to')) {
            $filters['date_to'] = $request->date_to;
        }
        
        // Apply product filter if provided
        if ($request->filled('product_id')) {
            $filters['product_id'] = $request->product_id;
        }
        
        $result = $this->revService->getSoldProductsSummary($filters);
        
        Log::info('ERPREV Controller - salesSummary result', [
            'success' => $result['success'] ?? false,
            'has_data' => isset($result['data']),
            'data_keys' => isset($result['data']) ? array_keys($result['data']) : [],
        ]);
        
        if (!$result['success']) {
            Log::error('ERPREV Controller - salesSummary failed', [
                'message' => $result['message'] ?? 'Unknown error',
            ]);
            
            return back()->with('error', 'Failed to fetch sales summary: ' . $result['message']);
        }
        
        // Extract records from the response
        $summaryData = $result['data']['records'] ?? [];
        
        Log::info('ERPREV Controller - salesSummary processed', [
            'record_count' => count($summaryData),
            'sample_record' => count($summaryData) > 0 ? $summaryData[0] : null,
        ]);
        
        return view('admin.erprev.summary', compact('summaryData', 'filters'));
    }
}