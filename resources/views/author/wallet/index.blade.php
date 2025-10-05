@extends('layouts.author')

@section('title', 'Wallet | Rhymes Author Platform')

@section('page-title', 'Wallet')

@section('page-description', 'Manage your earnings and view transaction history')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Wallet Overview</h3>
                        <div class="nk-block-des text-soft">
                            <p>Track your earnings, view transaction history, and manage payouts.</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="nk-block-tools g-3">
                            <div class="nk-block-tools-opt">
                                <a href="{{ route('author.payouts.index') }}" class="btn btn-primary">
                                    <em class="icon ni ni-wallet-out"></em><span>Request Payout</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wallet Balance Cards -->
            <div class="nk-block">
                <div class="row g-gs">
                    <div class="col-md-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Available Balance</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint-icon icon ni ni-wallet text-primary"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">${{ number_format($balance, 2) }}</span>
                                </div>
                                <div class="card-note">
                                    <span class="text-soft">Ready for withdrawal</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Total Earnings</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint-icon icon ni ni-coins text-success"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">${{ number_format($transactions->where('type', 'sale')->sum('amount'), 2) }}</span>
                                </div>
                                <div class="card-note">
                                    <span class="text-soft">Lifetime earnings</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-2">
                                    <div class="card-title">
                                        <h6 class="title">Total Sales</h6>
                                    </div>
                                    <div class="card-tools">
                                        <em class="card-hint-icon icon ni ni-bar-chart text-info"></em>
                                    </div>
                                </div>
                                <div class="card-amount">
                                    <span class="amount">{{ $transactions->where('type', 'sale')->count() }}</span>
                                </div>
                                <div class="card-note">
                                    <span class="text-soft">Books sold</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales by Book -->
            @if($salesByBook->count() > 0)
            <div class="nk-block">
                <div class="card card-bordered">
                    <div class="card-inner-group">
                        <div class="card-inner p-0">
                            <div class="nk-tb-list">
                                <div class="nk-tb-item nk-tb-head">
                                    <div class="nk-tb-col"><span>Book</span></div>
                                    <div class="nk-tb-col tb-col-md"><span>Sales Count</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span>Total Earnings</span></div>
                                    <div class="nk-tb-col tb-col-md"><span>Avg. per Sale</span></div>
                                </div>
                                @foreach($salesByBook as $sale)
                                <div class="nk-tb-item">
                                    <div class="nk-tb-col">
                                        <div class="user-card">
                                            <div class="user-info">
                                                <span class="tb-lead">{{ $sale->book->title ?? 'Unknown Book' }}</span>
                                                <span class="tb-sub">{{ $sale->book->genre ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="nk-tb-col tb-col-md">
                                        <span class="tb-amount">{{ $sale->sales_count }}</span>
                                    </div>
                                    <div class="nk-tb-col tb-col-lg">
                                        <span class="tb-amount">${{ number_format($sale->total_sales, 2) }}</span>
                                    </div>
                                    <div class="nk-tb-col tb-col-md">
                                        <span class="tb-amount">${{ number_format($sale->total_sales / $sale->sales_count, 2) }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Transaction History -->
            <div class="nk-block">
                <div class="card card-bordered">
                    <div class="card-inner-group">
                        <div class="card-inner">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">Transaction History</h6>
                                </div>
                                <div class="card-tools">
                                    <ul class="card-tools-nav">
                                        <li><a href="#" class="active">All</a></li>
                                        <li><a href="?type=sale">Sales</a></li>
                                        <li><a href="?type=payout">Payouts</a></li>
                                        <li><a href="?type=commission">Commission</a></li>
                                    </ul>
                                    <div class="card-tools-more">
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-icon btn-trigger dropdown-toggle" data-bs-toggle="dropdown">
                                                <em class="icon ni ni-more-h"></em>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <ul class="link-list-opt no-bdr">
                                                    <li><a href="#" onclick="showExportModal()"><em class="icon ni ni-download"></em><span>Export Transactions</span></a></li>
                                                    <li><a href="#" onclick="showFilterModal()"><em class="icon ni ni-filter"></em><span>Advanced Filter</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-inner p-0">
                            <div class="nk-tb-list">
                                <div class="nk-tb-item nk-tb-head">
                                    <div class="nk-tb-col"><span>Type</span></div>
                                    <div class="nk-tb-col tb-col-md"><span>Book</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span>Amount</span></div>
                                    <div class="nk-tb-col tb-col-md"><span>Date</span></div>
                                    <div class="nk-tb-col tb-col-sm"><span>Status</span></div>
                                </div>
                                @forelse($transactions as $transaction)
                                <div class="nk-tb-item">
                                    <div class="nk-tb-col">
                                        <div class="user-card">
                                            <div class="user-avatar sq bg-{{ $transaction->type === 'sale' ? 'success' : ($transaction->type === 'payout' ? 'warning' : 'info') }}">
                                                <em class="icon ni ni-{{ $transaction->type === 'sale' ? 'coins' : ($transaction->type === 'payout' ? 'wallet-out' : 'percent') }}"></em>
                                            </div>
                                            <div class="user-info">
                                                <span class="tb-lead">{{ ucfirst($transaction->type) }}</span>
                                                <span class="tb-sub">{{ $transaction->created_at->format('M d, Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="nk-tb-col tb-col-md">
                                        <span class="tb-lead">{{ $transaction->book->title ?? 'N/A' }}</span>
                                    </div>
                                    <div class="nk-tb-col tb-col-lg">
                                        <span class="tb-amount {{ $transaction->amount > 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $transaction->amount > 0 ? '+' : '' }}${{ number_format($transaction->amount, 2) }}
                                        </span>
                                    </div>
                                    <div class="nk-tb-col tb-col-md">
                                        <span class="tb-date">{{ $transaction->created_at->format('M d, Y H:i') }}</span>
                                    </div>
                                    <div class="nk-tb-col tb-col-sm">
                                        <span class="tb-status text-success">Completed</span>
                                    </div>
                                </div>
                                @empty
                                <div class="nk-tb-item">
                                    <div class="nk-tb-col text-center" colspan="5">
                                        <div class="py-4">
                                            <em class="icon ni ni-wallet text-soft" style="font-size: 3rem;"></em>
                                            <h6 class="mt-2">No transactions yet</h6>
                                            <p class="text-soft">Your transaction history will appear here once you start making sales.</p>
                                        </div>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                        </div>
                        @if($transactions->hasPages())
                        <div class="card-inner">
                            {{ $transactions->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" tabindex="-1" id="exportModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Transactions</h5>
                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body">
                <form id="exportForm" action="{{ route('author.wallet.export') }}" method="GET">
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">Transaction Type</label>
                                <select class="form-select" name="type">
                                    <option value="">All Types</option>
                                    <option value="sale">Sales</option>
                                    <option value="payout">Payouts</option>
                                    <option value="commission">Commission</option>
                                    <option value="refund">Refunds</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">From Date</label>
                                <input type="date" class="form-control" name="date_from">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">To Date</label>
                                <input type="date" class="form-control" name="date_to">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="exportForm" class="btn btn-primary">
                    <em class="icon ni ni-download"></em> Export CSV
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" tabindex="-1" id="filterModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Advanced Filter</h5>
                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body">
                <form id="filterForm" method="GET">
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">Transaction Type</label>
                                <select class="form-select" name="type">
                                    <option value="">All Types</option>
                                    <option value="sale" {{ request('type') == 'sale' ? 'selected' : '' }}>Sales</option>
                                    <option value="payout" {{ request('type') == 'payout' ? 'selected' : '' }}>Payouts</option>
                                    <option value="commission" {{ request('type') == 'commission' ? 'selected' : '' }}>Commission</option>
                                    <option value="refund" {{ request('type') == 'refund' ? 'selected' : '' }}>Refunds</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">From Date</label>
                                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">To Date</label>
                                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" onclick="clearFilters()" class="btn btn-outline-secondary">Clear Filters</button>
                <button type="submit" form="filterForm" class="btn btn-primary">Apply Filter</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Wallet-specific JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        // Filter transactions by type
        const filterLinks = document.querySelectorAll('.card-tools-nav a');
        filterLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = new URL(window.location);
                const href = this.getAttribute('href');
                
                if (href.includes('?type=')) {
                    const type = href.split('type=')[1];
                    url.searchParams.set('type', type);
                } else {
                    url.searchParams.delete('type');
                }
                
                window.location.href = url.toString();
            });
        });
    });

    // Show export modal
    function showExportModal() {
        const modal = new bootstrap.Modal(document.getElementById('exportModal'));
        modal.show();
    }

    // Show filter modal
    function showFilterModal() {
        const modal = new bootstrap.Modal(document.getElementById('filterModal'));
        modal.show();
    }

    // Clear filters
    function clearFilters() {
        window.location.href = window.location.pathname;
    }

    // Handle filter form submission
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const url = new URL(window.location);
        
        // Clear existing parameters
        url.search = '';
        
        // Add form parameters
        for (let [key, value] of formData.entries()) {
            if (value) {
                url.searchParams.set(key, value);
            }
        }
        
        window.location.href = url.toString();
    });

    // Handle export form submission
    document.getElementById('exportForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const url = new URL(this.action);
        
        // Add form parameters to export URL
        for (let [key, value] of formData.entries()) {
            if (value) {
                url.searchParams.set(key, value);
            }
        }
        
        // Open export URL in new tab
        window.open(url.toString(), '_blank');
        
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('exportModal'));
        modal.hide();
    });
</script>
@endpush
