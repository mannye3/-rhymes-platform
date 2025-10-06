@extends('layouts.admin')

@section('title', 'Payout Management | Admin Panel')

@section('page-title', 'Payout Management')

@section('page-description', 'Review and manage author payout requests')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Payout Management</h3>
                        <div class="nk-block-des text-soft">
                            <p>Review, approve, and manage author payout requests.</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li><a href="#" class="btn btn-success" onclick="bulkAction('approve')"><em class="icon ni ni-check"></em><span>Bulk Approve</span></a></li>
                                    <li><a href="#" class="btn btn-danger" onclick="bulkAction('deny')"><em class="icon ni ni-cross"></em><span>Bulk Deny</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-gs mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <div class="card-title-group align-start mb-2">
                                <div class="card-title">
                                    <h6 class="title">Total Payouts</h6>
                                </div>
                                <div class="card-tools">
                                    <em class="card-hint icon ni ni-tranx text-primary"></em>
                                </div>
                            </div>
                            <div class="card-amount">
                                <span class="amount">{{ number_format($stats['total_payouts']) }}</span>
                                <span class="sub-title">${{ number_format($stats['total_amount_requested'], 2) }} requested</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <div class="card-title-group align-start mb-2">
                                <div class="card-title">
                                    <h6 class="title">Pending</h6>
                                </div>
                                <div class="card-tools">
                                    <em class="card-hint icon ni ni-clock text-warning"></em>
                                </div>
                            </div>
                            <div class="card-amount">
                                <span class="amount">{{ number_format($stats['pending_payouts']) }}</span>
                                <span class="sub-title">${{ number_format($stats['pending_amount'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <div class="card-title-group align-start mb-2">
                                <div class="card-title">
                                    <h6 class="title">Approved</h6>
                                </div>
                                <div class="card-tools">
                                    <em class="card-hint icon ni ni-check-circle text-success"></em>
                                </div>
                            </div>
                            <div class="card-amount">
                                <span class="amount">{{ number_format($stats['approved_payouts']) }}</span>
                                <span class="sub-title">${{ number_format($stats['approved_amount'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <div class="card-title-group align-start mb-2">
                                <div class="card-title">
                                    <h6 class="title">Denied</h6>
                                </div>
                                <div class="card-tools">
                                    <em class="card-hint icon ni ni-cross-circle text-danger"></em>
                                </div>
                            </div>
                            <div class="card-amount">
                                <span class="amount">{{ number_format($stats['denied_payouts']) }}</span>
                                <span class="sub-title">Rejected requests</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="card card-bordered card-stretch">
                    <div class="card-inner-group">
                        <div class="card-inner position-relative card-tools-toggle">
                            <div class="card-title-group">
                                <div class="card-tools">
                                    <div class="form-inline flex-nowrap gx-3">
                                        <form method="GET" action="{{ route('admin.payouts.index') }}" class="d-flex gap-2">
                                            <div class="form-wrap w-150px">
                                                <select name="status" class="form-select form-select-sm">
                                                    <option value="">All Status</option>
                                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                                    <option value="denied" {{ request('status') === 'denied' ? 'selected' : '' }}>Denied</option>
                                                </select>
                                            </div>
                                            <div class="form-wrap w-150px">
                                                <input type="number" name="amount_min" class="form-control form-control-sm" placeholder="Min Amount" value="{{ request('amount_min') }}">
                                            </div>
                                            <div class="form-wrap w-150px">
                                                <input type="number" name="amount_max" class="form-control form-control-sm" placeholder="Max Amount" value="{{ request('amount_max') }}">
                                            </div>
                                            <div class="form-wrap flex-md-nowrap">
                                                <div class="form-icon form-icon-right">
                                                    <em class="icon ni ni-search"></em>
                                                </div>
                                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search authors..." value="{{ request('search') }}">
                                            </div>
                                            <div class="btn-wrap">
                                                <button type="submit" class="btn btn-sm btn-icon btn-primary"><em class="icon ni ni-search"></em></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-inner p-0">
                            <div class="nk-tb-list nk-tb-ulist">
                                <div class="nk-tb-item nk-tb-head">
                                    <div class="nk-tb-col nk-tb-col-check">
                                        <div class="custom-control custom-control-sm custom-checkbox notext">
                                            <input type="checkbox" class="custom-control-input" id="uid-all">
                                            <label class="custom-control-label" for="uid-all"></label>
                                        </div>
                                    </div>
                                    <div class="nk-tb-col"><span class="sub-text">Author</span></div>
                                    <div class="nk-tb-col tb-col-mb"><span class="sub-text">Amount</span></div>
                                    <div class="nk-tb-col tb-col-md"><span class="sub-text">Status</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span class="sub-text">Payment Method</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span class="sub-text">Requested</span></div>
                                    <div class="nk-tb-col nk-tb-col-tools text-end">
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-xs btn-outline-light btn-icon dropdown-toggle" data-bs-toggle="dropdown"><em class="icon ni ni-plus"></em></a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <ul class="link-list-opt no-bdr">
                                                    <li><a href="#" onclick="bulkAction('approve')"><span>Bulk Approve</span></a></li>
                                                    <li><a href="#" onclick="bulkAction('deny')"><span>Bulk Deny</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @forelse($payouts as $payout)
                                    <div class="nk-tb-item">
                                        <div class="nk-tb-col nk-tb-col-check">
                                            <div class="custom-control custom-control-sm custom-checkbox notext">
                                                <input type="checkbox" class="custom-control-input payout-checkbox" id="uid{{ $payout->id }}" value="{{ $payout->id }}">
                                                <label class="custom-control-label" for="uid{{ $payout->id }}"></label>
                                            </div>
                                        </div>
                                        <div class="nk-tb-col">
                                            <div class="user-card">
                                                <div class="user-avatar bg-primary-dim">
                                                    <span>{{ strtoupper(substr($payout->user->name, 0, 2)) }}</span>
                                                </div>
                                                <div class="user-info">
                                                    <span class="tb-lead">{{ $payout->user->name }}</span>
                                                    <span>{{ $payout->user->email }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="nk-tb-col tb-col-mb">
                                            <span class="tb-lead">${{ number_format($payout->amount_requested, 2) }}</span>
                                            <span class="tb-sub">Requested</span>
                                        </div>
                                        <div class="nk-tb-col tb-col-md">
                                            @if($payout->status === 'pending')
                                                <span class="badge badge-sm badge-dim bg-outline-warning">Pending</span>
                                            @elseif($payout->status === 'approved')
                                                <span class="badge badge-sm badge-dim bg-outline-success">Approved</span>
                                            @elseif($payout->status === 'denied')
                                                <span class="badge badge-sm badge-dim bg-outline-danger">Denied</span>
                                            @endif
                                        </div>
                                        <div class="nk-tb-col tb-col-lg">
                                            <span class="tb-lead">{{ ucfirst($payout->payment_method) }}</span>
                                            <span class="tb-sub">{{ $payout->payment_details }}</span>
                                        </div>
                                        <div class="nk-tb-col tb-col-lg">
                                            <span>{{ $payout->created_at->format('M d, Y') }}</span>
                                            <span class="tb-sub">{{ $payout->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="nk-tb-col nk-tb-col-tools">
                                            <ul class="nk-tb-actions gx-1">
                                                <li class="nk-tb-action-hidden">
                                                    <a href="{{ route('admin.payouts.show', $payout) }}" class="btn btn-trigger btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="View Details">
                                                        <em class="icon ni ni-eye-fill"></em>
                                                    </a>
                                                </li>
                                                @if($payout->status === 'pending')
                                                    <li class="nk-tb-action-hidden">
                                                        <a href="#" class="btn btn-trigger btn-icon text-success" onclick="reviewPayout({{ $payout->id }}, 'approve')" data-bs-toggle="tooltip" data-bs-placement="top" title="Approve">
                                                            <em class="icon ni ni-check-fill"></em>
                                                        </a>
                                                    </li>
                                                    <li class="nk-tb-action-hidden">
                                                        <a href="#" class="btn btn-trigger btn-icon text-danger" onclick="reviewPayout({{ $payout->id }}, 'deny')" data-bs-toggle="tooltip" data-bs-placement="top" title="Deny">
                                                            <em class="icon ni ni-cross-fill"></em>
                                                        </a>
                                                    </li>
                                                @endif
                                                <li>
                                                    <div class="drodown">
                                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <ul class="link-list-opt no-bdr">
                                                                <li><a href="{{ route('admin.payouts.show', $payout) }}"><em class="icon ni ni-eye"></em><span>View Details</span></a></li>
                                                                @if($payout->status === 'pending')
                                                                    <li><a href="#" onclick="reviewPayout({{ $payout->id }}, 'approve')"><em class="icon ni ni-check"></em><span>Approve</span></a></li>
                                                                    <li><a href="#" onclick="reviewPayout({{ $payout->id }}, 'deny')"><em class="icon ni ni-cross"></em><span>Deny</span></a></li>
                                                                @endif
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                @empty
                                    <div class="nk-tb-item">
                                        <div class="nk-tb-col">
                                            <div class="text-center py-4">
                                                <em class="icon ni ni-tranx" style="font-size: 3rem; opacity: 0.3;"></em>
                                                <p class="text-soft mt-2">No payout requests found</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="card-inner">
                            {{ $payouts->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" tabindex="-1" id="reviewModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review Payout Request</h5>
                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body">
                <form id="reviewForm">
                    @csrf
                    <input type="hidden" id="payoutId" name="payout_id">
                    <input type="hidden" id="reviewAction" name="action">
                    
                    <div class="form-group">
                        <label class="form-label">Admin Notes</label>
                        <textarea class="form-control" name="admin_notes" rows="4" placeholder="Optional notes for the author..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitReview()">Submit Review</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Select all functionality
document.getElementById('uid-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.payout-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

function reviewPayout(payoutId, action) {
    document.getElementById('payoutId').value = payoutId;
    document.getElementById('reviewAction').value = action;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
    modal.show();
}

function submitReview() {
    const form = document.getElementById('reviewForm');
    const formData = new FormData(form);
    const payoutId = formData.get('payout_id');
    const action = formData.get('action');
    
    fetch(`/admin/payouts/${payoutId}/${action}`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Success!', data.message, 'success').then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Error!', data.message, 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error!', 'Something went wrong.', 'error');
    });
    
    // Hide modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('reviewModal'));
    modal.hide();
}

function bulkAction(action) {
    const selectedPayouts = Array.from(document.querySelectorAll('.payout-checkbox:checked')).map(cb => cb.value);
    
    if (selectedPayouts.length === 0) {
        Swal.fire('Warning!', 'Please select at least one payout request.', 'warning');
        return;
    }
    
    const actionText = action === 'approve' ? 'approve' : 'deny';
    
    Swal.fire({
        title: `${actionText.charAt(0).toUpperCase() + actionText.slice(1)} Payouts?`,
        text: `This will ${actionText} ${selectedPayouts.length} selected payout request(s).`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: action === 'approve' ? '#28a745' : '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Yes, ${actionText}!`
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/admin/payouts/bulk-action', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: action,
                    payout_ids: selectedPayouts
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Success!', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Something went wrong.', 'error');
            });
        }
    });
}
</script>
@endpush
@endsection
