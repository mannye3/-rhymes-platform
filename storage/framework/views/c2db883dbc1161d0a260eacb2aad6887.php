<?php $__env->startSection('title', 'Payout Management | Admin Panel'); ?>

<?php $__env->startSection('page-title', 'Payout Management'); ?>

<?php $__env->startSection('page-description', 'Review and manage author payout requests'); ?>

<?php $__env->startSection('content'); ?>
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
                                <span class="amount"><?php echo e(number_format($stats['total_payouts'])); ?></span>
                                <span class="sub-title">₦<?php echo e(number_format($stats['total_amount_requested'], 2)); ?> requested</span>
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
                                <span class="amount"><?php echo e(number_format($stats['pending_payouts'])); ?></span>
                                <span class="sub-title">₦<?php echo e(number_format($stats['pending_amount'], 2)); ?></span>
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
                                <span class="amount"><?php echo e(number_format($stats['approved_payouts'])); ?></span>
                                <span class="sub-title">₦<?php echo e(number_format($stats['approved_amount'], 2)); ?></span>
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
                                <span class="amount"><?php echo e(number_format($stats['denied_payouts'])); ?></span>
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
                                        <form method="GET" action="<?php echo e(route('admin.payouts.index')); ?>" class="d-flex gap-2">
                                            <div class="form-wrap w-150px">
                                                <select name="status" class="form-select form-select-sm">
                                                    <option value="">All Status</option>
                                                    <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>Pending</option>
                                                    <option value="approved" <?php echo e(request('status') === 'approved' ? 'selected' : ''); ?>>Approved</option>
                                                    <option value="denied" <?php echo e(request('status') === 'denied' ? 'selected' : ''); ?>>Denied</option>
                                                </select>
                                            </div>
                                            <div class="form-wrap w-150px">
                                                <input type="number" name="amount_min" class="form-control form-control-sm" placeholder="Min Amount" value="<?php echo e(request('amount_min')); ?>">
                                            </div>
                                            <div class="form-wrap w-150px">
                                                <input type="number" name="amount_max" class="form-control form-control-sm" placeholder="Max Amount" value="<?php echo e(request('amount_max')); ?>">
                                            </div>
                                            <div class="form-wrap flex-md-nowrap">
                                                <div class="form-icon form-icon-right">
                                                    <em class="icon ni ni-search"></em>
                                                </div>
                                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search authors..." value="<?php echo e(request('search')); ?>">
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

                                <?php $__empty_1 = true; $__currentLoopData = $payouts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payout): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <div class="nk-tb-item">
                                        
                                        <div class="nk-tb-col">
                                            <div class="user-card">
                                                <div class="user-avatar bg-primary-dim">
                                                    <span><?php echo e(strtoupper(substr($payout->user->name, 0, 2))); ?></span>
                                                </div>
                                                <div class="user-info">
                                                    <span class="tb-lead"><?php echo e($payout->user->name); ?></span>
                                                    <span><?php echo e($payout->user->email); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="nk-tb-col tb-col-mb">
                                            <span class="tb-lead">₦<?php echo e(number_format($payout->amount_requested, 2)); ?></span>
                                            <span class="tb-sub">Requested</span>
                                        </div>
                                        <div class="nk-tb-col tb-col-md">
                                            <?php if($payout->status === 'pending'): ?>
                                                <span class="badge badge-sm badge-dim bg-outline-warning">Pending</span>
                                            <?php elseif($payout->status === 'approved'): ?>
                                                <span class="badge badge-sm badge-dim bg-outline-success">Approved</span>
                                            <?php elseif($payout->status === 'denied'): ?>
                                                <span class="badge badge-sm badge-dim bg-outline-danger">Denied</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="nk-tb-col tb-col-lg">
                                            <span class="tb-lead"><?php echo e(ucfirst($payout->payment_method)); ?></span>
                                            <span class="tb-sub"><?php echo e($payout->payment_details); ?></span>
                                        </div>
                                        <div class="nk-tb-col tb-col-lg">
                                            <span><?php echo e($payout->created_at->format('M d, Y')); ?></span>
                                            <span class="tb-sub"><?php echo e($payout->created_at->diffForHumans()); ?></span>
                                        </div>
                                        <div class="nk-tb-col nk-tb-col-tools">
                                            <ul class="nk-tb-actions gx-1">
                                               
                                                <li>
                                                    <div class="drodown">
                                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <ul class="link-list-opt no-bdr">
                                                                <li><a href="#" onclick="viewPayout(<?php echo e($payout->id); ?>, event); return false;"><em class="icon ni ni-eye"></em><span>View Details</span></a></li>
                                                                <?php if($payout->status === 'pending'): ?>
                                                                    <li><a href="#" onclick="openReviewModal(<?php echo e($payout->id); ?>, 'approve', event); return false;"><em class="icon ni ni-check"></em><span>Approve</span></a></li>
                                                                    <li><a href="#" onclick="openReviewModal(<?php echo e($payout->id); ?>, 'deny', event); return false;"><em class="icon ni ni-cross"></em><span>Deny</span></a></li>
                                                                <?php endif; ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div class="nk-tb-item">
                                        <div class="nk-tb-col">
                                            <div class="text-center py-4">
                                                <em class="icon ni ni-tranx" style="font-size: 3rem; opacity: 0.3;"></em>
                                                <p class="text-soft mt-2">No payout requests found</p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-inner">
                            <?php echo e($payouts->appends(request()->query())->links()); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Payout Modal -->

<div class="modal fade" tabindex="-1" id="viewModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payout Details</h5>
                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body">
                <div class="row gy-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Author</label>
                            <div class="form-control-wrap">
                                <div class="user-card">
                                    <div class="user-avatar bg-primary-dim">
                                        <span id="viewAuthorInitials"></span>
                                    </div>
                                    <div class="user-info">
                                        <span class="tb-lead" id="viewAuthorName"></span>
                                        <span id="viewAuthorEmail"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Amount Requested</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control form-control-lg" id="viewAmount" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <div class="form-control-wrap">
                                <span class="badge badge-dot" id="viewStatusBadge">
                                    <span id="viewStatus"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Requested Date</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" id="viewRequestedDate" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Processed Date</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" id="viewProcessedDate" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Payment Method</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" id="viewPaymentMethod" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label">Admin Notes</label>
                            <div class="form-control-wrap">
                                <textarea class="form-control" id="viewAdminNotes" rows="3" readonly></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



<!-- Review Modal -->
<div class="modal fade" tabindex="-1" id="reviewModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalTitle">Review Payout Request</h5>
                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body">
                <form id="reviewForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="payoutId" name="payout_id">
                    <input type="hidden" id="reviewAction" name="action">
                    
                    <div class="form-group">
                        <label class="form-label">Amount Requested</label>
                        <div class="form-control-wrap">
                            <input type="text" class="form-control" id="reviewAmount" readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Author</label>
                        <div class="form-control-wrap">
                            <div class="user-card">
                                <div class="user-avatar bg-primary-dim">
                                    <span id="reviewAuthorInitials"></span>
                                </div>
                                <div class="user-info">
                                    <span class="tb-lead" id="reviewAuthorName"></span>
                                    <span id="reviewAuthorEmail"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group" id="adminNotesField">
                        <label class="form-label" id="adminNotesLabel">Admin Notes</label>
                        <div class="form-control-wrap">
                            <textarea class="form-control" name="admin_notes" id="adminNotes" rows="4" placeholder="Enter notes for the author..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitReviewBtn">Submit Review</button>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// View payout details
function viewPayout(payoutId, event) {
    // Prevent default anchor behavior
    if (event) {
        event.preventDefault();
    }
    
    // Show loading state
    Swal.fire({
        title: 'Loading...',
        text: 'Fetching payout details',
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Use the correct base URL from Laravel
    const baseUrl = "<?php echo e(url('/')); ?>";
    const url = `${baseUrl}/admin/payouts/${payoutId}`;
    
    console.log('Fetching payout details from:', url);
    
    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            if (response.status === 404) {
                throw new Error('Payout not found (404)');
            } else if (response.status === 403) {
                throw new Error('Access denied (403)');
            } else {
                throw new Error('Network response was not ok: ' + response.status);
            }
        }
        return response.json();
    })
    .then(data => {
        Swal.close();
        console.log('Response data:', data);
        if (data.success) {
            const payout = data.payout;
            
            // Populate view modal
            document.getElementById('viewAuthorInitials').textContent = payout.user.name.substring(0, 2).toUpperCase();
            document.getElementById('viewAuthorName').textContent = payout.user.name;
            document.getElementById('viewAuthorEmail').textContent = payout.user.email;
            document.getElementById('viewAmount').value = '₦' + parseFloat(payout.amount_requested).toFixed(2);
            
            // Set status badge
            const statusElement = document.getElementById('viewStatus');
            const statusBadge = document.getElementById('viewStatusBadge');
            statusElement.textContent = payout.status.charAt(0).toUpperCase() + payout.status.slice(1);
            
            // Remove existing classes
            statusBadge.className = 'badge badge-dot';
            
            // Add appropriate class based on status
            if (payout.status === 'pending') {
                statusBadge.classList.add('bg-warning');
            } else if (payout.status === 'approved') {
                statusBadge.classList.add('bg-success');
            } else if (payout.status === 'denied') {
                statusBadge.classList.add('bg-danger');
            }
            
            document.getElementById('viewRequestedDate').value = new Date(payout.created_at).toLocaleDateString();
            document.getElementById('viewProcessedDate').value = payout.processed_at ? new Date(payout.processed_at).toLocaleDateString() : 'Not processed yet';
            
            // Payment method from user payment details
            let paymentMethod = 'Bank Transfer';
            if (payout.user.payment_details) {
                try {
                    const paymentDetails = typeof payout.user.payment_details === 'string' 
                        ? JSON.parse(payout.user.payment_details) 
                        : payout.user.payment_details;
                    paymentMethod = paymentDetails.bank_name || 'Bank Transfer';
                } catch (e) {
                    paymentMethod = 'Bank Transfer';
                }
            }
            document.getElementById('viewPaymentMethod').value = paymentMethod;
            
            document.getElementById('viewAdminNotes').value = payout.admin_notes || 'No notes provided';
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('viewModal'));
            modal.show();
        } else {
            Swal.fire('Error!', data.message || 'Failed to load payout details.', 'error');
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Error fetching payout details:', error);
        Swal.fire('Error!', 'Something went wrong: ' + error.message, 'error');
    });
    
    return false;
}

// Open review modal for approve/deny
function openReviewModal(payoutId, action, event) {
    // Prevent default anchor behavior
    if (event) {
        event.preventDefault();
    }
    
    // Show loading state
    Swal.fire({
        title: 'Loading...',
        text: 'Fetching payout details',
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Use the correct base URL from Laravel
    const baseUrl = "<?php echo e(url('/')); ?>";
    const url = `${baseUrl}/admin/payouts/${payoutId}`;
    
    console.log('Fetching payout details from:', url);
    
    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            if (response.status === 404) {
                throw new Error('Payout not found (404)');
            } else if (response.status === 403) {
                throw new Error('Access denied (403)');
            } else {
                throw new Error('Network response was not ok: ' + response.status);
            }
        }
        return response.json();
    })
    .then(data => {
        Swal.close();
        console.log('Response data:', data);
        if (data.success) {
            const payout = data.payout;
            
            // Set payout ID and action
            document.getElementById('payoutId').value = payoutId;
            document.getElementById('reviewAction').value = action;
            
            // Populate review modal
            document.getElementById('reviewAmount').value = '₦' + parseFloat(payout.amount_requested).toFixed(2);
            document.getElementById('reviewAuthorInitials').textContent = payout.user.name.substring(0, 2).toUpperCase();
            document.getElementById('reviewAuthorName').textContent = payout.user.name;
            document.getElementById('reviewAuthorEmail').textContent = payout.user.email;
            
            // Set modal title and button text based on action
            const modalTitle = document.getElementById('reviewModalTitle');
            const submitBtn = document.getElementById('submitReviewBtn');
            
            if (action === 'approve') {
                modalTitle.textContent = 'Approve Payout Request';
                submitBtn.textContent = 'Approve Payout';
                submitBtn.className = 'btn btn-success';
                document.getElementById('adminNotesLabel').textContent = 'Approval Notes (Optional)';
                document.getElementById('adminNotes').placeholder = 'Optional notes for the author...';
                document.getElementById('adminNotes').required = false;
            } else {
                modalTitle.textContent = 'Deny Payout Request';
                submitBtn.textContent = 'Deny Payout';
                submitBtn.className = 'btn btn-danger';
                document.getElementById('adminNotesLabel').textContent = 'Denial Reason (Required)';
                document.getElementById('adminNotes').placeholder = 'Reason for denying this payout...';
                document.getElementById('adminNotes').required = true;
            }
            
            // Clear previous notes
            document.getElementById('adminNotes').value = '';
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
            modal.show();
        } else {
            Swal.fire('Error!', data.message || 'Failed to load payout details.', 'error');
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Error fetching payout details:', error);
        Swal.fire('Error!', 'Something went wrong: ' + error.message, 'error');
    });
    
    return false;
}

// Submit review (approve/deny)
document.getElementById('submitReviewBtn').addEventListener('click', function() {
    const form = document.getElementById('reviewForm');
    const formData = new FormData(form);
    const payoutId = formData.get('payout_id');
    const action = formData.get('action');
    
    // Validate form for deny action
    if (action === 'deny' && !formData.get('admin_notes')) {
        Swal.fire('Error!', 'Please provide a reason for denying this payout.', 'error');
        return;
    }
    
    // Show loading state
    Swal.fire({
        title: 'Processing...',
        text: 'Please wait while we process your request',
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Determine the correct URL based on action
    const baseUrl = "<?php echo e(url('/')); ?>";
    let url = '';
    if (action === 'approve') {
        url = `${baseUrl}/admin/payouts/${payoutId}/approve`;
    } else if (action === 'deny') {
        url = `${baseUrl}/admin/payouts/${payoutId}/deny`;
    }
    
    console.log('Submitting payout action to:', url);
    
    fetch(url, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            if (response.status === 404) {
                throw new Error('Payout not found (404)');
            } else if (response.status === 403) {
                throw new Error('Access denied (403)');
            } else {
                throw new Error('Network response was not ok: ' + response.status);
            }
        }
        return response.json();
    })
    .then(data => {
        Swal.close();
        console.log('Response data:', data);
        if (data.success) {
            Swal.fire('Success!', data.message, 'success').then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Error!', data.message || 'Failed to process request', 'error');
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Error processing payout:', error);
        Swal.fire('Error!', 'Something went wrong: ' + error.message, 'error');
    });
});

function bulkAction(action) {
    // Prevent default anchor behavior
    event.preventDefault();
    
    const selectedPayouts = Array.from(document.querySelectorAll('.payout-checkbox:checked')).map(cb => cb.value);
    
    if (selectedPayouts.length === 0) {
        Swal.fire('Warning!', 'Please select at least one payout request.', 'warning');
        return false;
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
            // Show loading state
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we process your request',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            const baseUrl = "<?php echo e(url('/')); ?>";
            const url = `${baseUrl}/admin/payouts/bulk-action`;
            
            console.log('Submitting bulk action to:', url);
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    action: action,
                    payout_ids: selectedPayouts
                })
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    if (response.status === 403) {
                        throw new Error('Access denied (403)');
                    } else {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                }
                return response.json();
            })
            .then(data => {
                Swal.close();
                console.log('Response data:', data);
                if (data.success) {
                    Swal.fire('Success!', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error!', data.message || 'Failed to process request', 'error');
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error processing bulk action:', error);
                Swal.fire('Error!', 'Something went wrong: ' + error.message, 'error');
            });
        }
    });
    
    return false;
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Make sure Bootstrap modals are properly initialized
    var viewModalEl = document.getElementById('viewModal');
    var reviewModalEl = document.getElementById('reviewModal');
    
    if (viewModalEl) {
        var viewModal = new bootstrap.Modal(viewModalEl);
    }
    
    if (reviewModalEl) {
        var reviewModal = new bootstrap.Modal(reviewModalEl);
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\rhyme_app\resources\views/admin/payouts/index.blade.php ENDPATH**/ ?>