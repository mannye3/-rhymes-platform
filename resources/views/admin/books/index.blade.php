@extends('layouts.admin')

@section('title', 'Book Management | Admin Panel')

@section('page-title', 'Book Management')

@section('page-description', 'Review and manage all books on the platform')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Books Management</h3>
                        <div class="nk-block-des text-soft">
                            <p>Review, approve, and manage all books submitted by authors.</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li><a href="javascript:void(0)" class="btn btn-primary" onclick="bulkAction('accept'); return false;"><em class="icon ni ni-check"></em><span>Bulk Approve</span></a></li>
                                    <li><a href="javascript:void(0)" class="btn btn-white btn-dim btn-outline-light" onclick="bulkAction('reject'); return false;"><em class="icon ni ni-cross"></em><span>Bulk Reject</span></a></li>
                                </ul>
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
                                        <form method="GET" action="{{ route('admin.books.index') }}" class="d-flex gap-2">
                                            <div class="form-wrap w-150px">
                                                <select name="status" class="form-select form-select-sm">
                                                    <option value="">All Status</option>
                                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Published</option>
                                                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                    <option value="stocked" {{ request('status') === 'stocked' ? 'selected' : '' }}>Stocked</option>
                                                </select>
                                            </div>
                                            <div class="form-wrap w-150px">
                                                <select name="genre" class="form-select form-select-sm">
                                                    <option value="">All Genres</option>
                                                    @foreach($genres as $genre)
                                                        <option value="{{ $genre }}" {{ request('genre') === $genre ? 'selected' : '' }}>
                                                            {{ $genre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-wrap flex-md-nowrap">
                                                <div class="form-icon form-icon-right">
                                                    <em class="icon ni ni-search"></em>
                                                </div>
                                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search books..." value="{{ request('search') }}">
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
                                    <div class="nk-tb-col"><span class="sub-text">Book</span></div>
                                    <div class="nk-tb-col tb-col-mb"><span class="sub-text">Author</span></div>
                                    <div class="nk-tb-col tb-col-md"><span class="sub-text">Status</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span class="sub-text">Sales</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span class="sub-text">Submitted</span></div>
                                    <div class="nk-tb-col nk-tb-col-tools text-end">
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-xs btn-outline-light btn-icon dropdown-toggle" data-bs-toggle="dropdown"><em class="icon ni ni-plus"></em></a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <ul class="link-list-opt no-bdr">
                                                    <li><a href="javascript:void(0)" onclick="bulkAction('accept'); return false;"><span>Bulk Approve</span></a></li>
                                                    <li><a href="javascript:void(0)" onclick="bulkAction('reject'); return false;"><span>Bulk Reject</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @forelse($books as $book)
                                    <div class="nk-tb-item">
                                        <div class="nk-tb-col nk-tb-col-check">
                                            <div class="custom-control custom-control-sm custom-checkbox notext">
                                                <input type="checkbox" class="custom-control-input book-checkbox" id="uid{{ $book->id }}" value="{{ $book->id }}">
                                                <label class="custom-control-label" for="uid{{ $book->id }}"></label>
                                            </div>
                                        </div>
                                        <div class="nk-tb-col">
                                            <div class="user-card">
                                                <div class="user-avatar bg-primary-dim">
                                                    <em class="icon ni ni-book"></em>
                                                </div>
                                                <div class="user-info">
                                                    <span class="tb-lead">{{ $book->title }}</span>
                                                    <span>{{ $book->genre }} • ${{ number_format($book->price, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="nk-tb-col tb-col-mb">
                                            <span class="tb-lead-sub">{{ $book->user->name }}</span>
                                            <span class="tb-sub">{{ $book->user->email }}</span>
                                        </div>
                                        <div class="nk-tb-col tb-col-md">
                                            @if($book->status === 'pending')
                                                <span class="badge badge-sm badge-dim bg-outline-warning">Pending Review</span>
                                            @elseif($book->status === 'accepted')
                                                <span class="badge badge-sm badge-dim bg-outline-success">Published</span>
                                            @elseif($book->status === 'rejected')
                                                <span class="badge badge-sm badge-dim bg-outline-danger">Rejected</span>
                                            @elseif($book->status === 'stocked')
                                                <span class="badge badge-sm badge-dim bg-outline-info">Stocked</span>
                                            @endif
                                            @if($book->trashed())
                                                <span class="badge badge-sm badge-dim bg-outline-secondary">Deleted</span>
                                            @endif
                                        </div>
                                        <div class="nk-tb-col tb-col-lg">
                                            @php
                                                $salesCount = $book->walletTransactions->where('type', 'sale')->count();
                                                $revenue = $book->walletTransactions->where('type', 'sale')->sum('amount');
                                            @endphp
                                            <span class="tb-lead">{{ $salesCount }}</span>
                                            <span class="tb-sub">${{ number_format($revenue, 2) }}</span>
                                        </div>
                                        <div class="nk-tb-col tb-col-lg">
                                            <span>{{ $book->created_at->format('M d, Y') }}</span>
                                            <span class="tb-sub">{{ $book->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="nk-tb-col nk-tb-col-tools">
                                            <ul class="nk-tb-actions gx-1">
                                                
                                              
                                                <li>
                                                    <div class="drodown">
                                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <ul class="link-list-opt no-bdr">
                                                                <li><a href="#" data-bs-toggle="modal" data-bs-target="#viewDetailsModal-{{$book->id}}"><em class="icon ni ni-eye"></em><span>View Details</span></a></li>
                                                                @if($book->trashed())
                                                                    <li><a href="#" onclick="restoreBook({{ $book->id }}); return false;"><em class="icon ni ni-reload"></em><span>Restore</span></a></li>
                                                                    <li><a href="#" onclick="forceDeleteBook({{ $book->id }}); return false;"><em class="icon ni ni-trash-fill"></em><span>Permanently Delete</span></a></li>
                                                                @else
                                                                    @if($book->status === 'pending')
                                                                        <li><a href="#" onclick="reviewBook({{ $book->id }}, 'accepted'); return false;"><em class="icon ni ni-check"></em><span>Approve</span></a></li>
                                                                        <li><a href="#" onclick="reviewBook({{ $book->id }}, 'rejected'); return false;"><em class="icon ni ni-cross"></em><span>Reject</span></a></li>
                                                                        <li><a href="#" onclick="reviewBook({{ $book->id }}, 'stocked'); return false;"><em class="icon ni ni-package"></em><span>Stock</span></a></li>
                                                                    @else
                                                                        <li><a href="#" data-bs-toggle="modal" data-bs-target="#reviewModal-{{$book->id}}"><em class="icon ni ni-edit"></em><span>Edit Status</span></a></li>
                                                                    @endif
                                                                    <li class="divider"></li>
                                                                    <li><a href="#" class="text-danger" onclick="deleteBook({{ $book->id }})"><em class="icon ni ni-trash"></em><span>Delete</span></a></li>
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
                                                <em class="icon ni ni-book" style="font-size: 3rem; opacity: 0.3;"></em>
                                                <p class="text-soft mt-2">No books found</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="card-inner">
                            {{ $books->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($books as $book)
<!-- Review Modal -->
<div class="modal fade" tabindex="-1" id="reviewModal-{{$book->id}}" aria-labelledby="reviewModalLabel-{{$book->id}}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel-{{$book->id}}">Review Book: {{ $book->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Author:</strong> {{ $book->user->name }}</p>
                        <p><strong>Email:</strong> {{ $book->user->email }}</p>
                        <p><strong>Genre:</strong> {{ $book->genre }}</p>
                        <p><strong>Price:</strong> ${{ number_format($book->price, 2) }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong> 
                            @if($book->status === 'pending')
                                <span class="badge badge-sm badge-dim bg-outline-warning">Pending Review</span>
                            @elseif($book->status === 'accepted')
                                <span class="badge badge-sm badge-dim bg-outline-success">Published</span>
                            @elseif($book->status === 'rejected')
                                <span class="badge badge-sm badge-dim bg-outline-danger">Rejected</span>
                            @elseif($book->status === 'stocked')
                                <span class="badge badge-sm badge-dim bg-outline-info">Stocked</span>
                            @endif
                        </p>
                        <p><strong>Sales:</strong> {{ $book->getSalesCount() }}</p>
                        <p><strong>Revenue:</strong> ${{ number_format($book->getTotalSales(), 2) }}</p>
                        <p><strong>Submitted:</strong> {{ $book->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
                
                @if($book->description)
                <div class="form-group mb-3">
                    <label class="form-label"><strong>Description:</strong></label>
                    <div class="form-control-wrap">
                        <p>{{ $book->description }}</p>
                    </div>
                </div>
                @endif
                
                <form id="reviewForm-{{$book->id}}" class="review-form">
                    @csrf
                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Admin Decision</label>
                        <div class="form-control-wrap">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="accept-{{$book->id}}" value="accepted" 
                                       {{ $book->status === 'accepted' ? 'checked' : '' }}>
                                <label class="form-check-label" for="accept-{{$book->id}}">Accept</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="reject-{{$book->id}}" value="rejected" 
                                       {{ $book->status === 'rejected' ? 'checked' : '' }}>
                                <label class="form-check-label" for="reject-{{$book->id}}">Reject</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="stock-{{$book->id}}" value="stocked" 
                                       {{ $book->status === 'stocked' ? 'checked' : '' }}>
                                <label class="form-check-label" for="stock-{{$book->id}}">Stock</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Admin Notes</label>
                        <textarea class="form-control" name="admin_notes" rows="4" placeholder="Optional notes for the author...">{{ $book->admin_notes }}</textarea>
                    </div>
                    
                    <div class="form-group mb-3" id="revBookIdGroup-{{$book->id}}" style="{{ $book->status !== 'accepted' ? 'display: none;' : '' }}">
                        <label class="form-label">REV Book ID</label>
                        <input type="text" class="form-control" name="rev_book_id" placeholder="Enter REV system book ID" value="{{ $book->rev_book_id }}">
                        <div class="form-note">Required when approving books for REV system integration.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitReview({{ $book->id }})">Submit Review</button>
            </div>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" tabindex="-1" id="viewDetailsModal-{{$book->id}}" aria-labelledby="viewDetailsModalLabel-{{$book->id}}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailsModalLabel-{{$book->id}}">Book Details: {{ $book->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="small text-muted">Book Information</h6>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="text-muted">Title:</td>
                                <td><strong>{{ $book->title }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Author:</td>
                                <td>{{ $book->user->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Email:</td>
                                <td>{{ $book->user->email }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Genre:</td>
                                <td>{{ $book->genre }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Price:</td>
                                <td>${{ number_format($book->price, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">ISBN:</td>
                                <td>{{ $book->isbn ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Type:</td>
                                <td>{{ ucfirst($book->book_type) }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="small text-muted">Status & Performance</h6>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="text-muted">Status:</td>
                                <td>
                                    @if($book->status === 'pending')
                                        <span class="badge badge-sm bg-warning">Pending Review</span>
                                    @elseif($book->status === 'accepted')
                                        <span class="badge badge-sm bg-success">Published</span>
                                    @elseif($book->status === 'rejected')
                                        <span class="badge badge-sm bg-danger">Rejected</span>
                                    @elseif($book->status === 'stocked')
                                        <span class="badge badge-sm bg-info">Stocked</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Sales:</td>
                                <td>{{ $book->getSalesCount() }} copies</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Revenue:</td>
                                <td>${{ number_format($book->getTotalSales(), 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Submitted:</td>
                                <td>{{ $book->created_at->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Last Updated:</td>
                                <td>{{ $book->updated_at->format('M d, Y H:i') }}</td>
                            </tr>
                            @if($book->rev_book_id)
                            <tr>
                                <td class="text-muted">REV Book ID:</td>
                                <td>{{ $book->rev_book_id }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
                
                @if($book->description)
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="small text-muted">Description</h6>
                        <div class="border p-3 rounded">
                            <p class="mb-0">{{ $book->description }}</p>
                        </div>
                    </div>
                </div>
                @endif
                
                @if($book->admin_notes)
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="small text-muted">Admin Notes</h6>
                        <div class="border p-3 rounded bg-light">
                            <p class="mb-0">{{ $book->admin_notes }}</p>
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="row">
                    <div class="col-12">
                        <h6 class="small text-muted">Recent Sales</h6>
                        @php
                            $recentSales = $book->walletTransactions()->where('type', 'sale')->latest()->limit(5)->get();
                        @endphp
                        @if($recentSales->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Transaction ID</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentSales as $sale)
                                    <tr>
                                        <td>{{ $sale->created_at->format('M d, Y') }}</td>
                                        <td>${{ number_format($sale->amount, 2) }}</td>
                                        <td>{{ $sale->transaction_id ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-muted">No sales recorded yet.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Close</button>
                @if($book->status === 'pending')
                <button type="button" class="btn btn-primary" onclick="reviewBook({{ $book->id }}, 'accepted')">Approve</button>
                <button type="button" class="btn btn-danger" onclick="reviewBook({{ $book->id }}, 'rejected')">Reject</button>
                @else
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reviewModal-{{$book->id}}" data-bs-dismiss="modal">Edit Status</button>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

@push('scripts')
<script>
// Select all functionality
document.getElementById('uid-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.book-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Show/hide REV Book ID field based on status selection
function toggleRevBookIdField(bookId) {
    const form = document.getElementById(`reviewForm-${bookId}`);
    if (!form) return;
    
    const statusInputs = form.querySelectorAll('input[name="status"]');
    const revBookIdGroup = document.getElementById(`revBookIdGroup-${bookId}`);
    
    statusInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.value === 'accepted') {
                if (revBookIdGroup) revBookIdGroup.style.display = 'block';
            } else {
                if (revBookIdGroup) revBookIdGroup.style.display = 'none';
            }
        });
    });
}

// Initialize the toggle for each book modal
document.addEventListener('DOMContentLoaded', function() {
    @foreach($books as $book)
        toggleRevBookIdField({{ $book->id }});
    @endforeach
});

// Also initialize when a modal is shown
document.addEventListener('shown.bs.modal', function (event) {
    const modal = event.target;
    if (modal.id.startsWith('reviewModal-')) {
        const bookId = modal.id.replace('reviewModal-', '');
        if (bookId) {
            toggleRevBookIdField(bookId);
        }
    }
});

function submitReview(bookId) {
    const form = document.getElementById(`reviewForm-${bookId}`);
    const formData = new FormData(form);
    
    // Get the selected status
    const selectedStatus = form.querySelector('input[name="status"]:checked');
    if (!selectedStatus) {
        Swal.fire('Error!', 'Please select a status for the book.', 'error');
        return;
    }
    
    fetch(`/admin/books/${bookId}/review`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire('Success!', data.message, 'success').then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Error!', data.message || 'Failed to update book status.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
    });
    
    // Hide modal
    const modal = bootstrap.Modal.getInstance(document.getElementById(`reviewModal-${bookId}`));
    modal.hide();
}

function bulkAction(action) {
    // Prevent default action
    event.preventDefault();
    
    const selectedBooks = Array.from(document.querySelectorAll('.book-checkbox:checked')).map(cb => cb.value);
    
    if (selectedBooks.length === 0) {
        Swal.fire('Warning!', 'Please select at least one book.', 'warning');
        return;
    }
    
    const actionText = action === 'accept' ? 'approve' : action === 'reject' ? 'reject' : 'delete';
    
    Swal.fire({
        title: `${actionText.charAt(0).toUpperCase() + actionText.slice(1)} Books?`,
        text: `This will ${actionText} ${selectedBooks.length} selected book(s).`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: action === 'accept' ? '#28a745' : action === 'reject' ? '#dc3545' : '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Yes, ${actionText}!`
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/admin/books/bulk-action', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: action,
                    book_ids: selectedBooks
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire('Success!', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error!', data.message || 'Failed to process books.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
            });
        }
    });
}

function deleteBook(bookId) {
    // Prevent default action
    event.preventDefault();
    
    Swal.fire({
        title: 'Delete Book?',
        text: 'This action will soft delete the book. You can restore it later.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/admin/books/bulk-action', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'delete',
                    book_ids: [bookId]
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire('Deleted!', 'Book has been deleted.', 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error!', data.message || 'Failed to delete book.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
            });
        }
    });
}

function restoreBook(bookId) {
    // Prevent default action
    event.preventDefault();
    
    Swal.fire({
        title: 'Restore Book?',
        text: 'This action will restore the deleted book.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, restore!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/admin/books/bulk-action', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'restore',
                    book_ids: [bookId]
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire('Restored!', 'Book has been restored.', 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error!', data.message || 'Failed to restore book.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
            });
        }
    });
}

function forceDeleteBook(bookId) {
    // Prevent default action
    event.preventDefault();
    
    Swal.fire({
        title: 'Permanently Delete Book?',
        text: 'This action cannot be undone! The book will be permanently removed from the system.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, permanently delete!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/admin/books/bulk-action', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'forceDelete',
                    book_ids: [bookId]
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire('Deleted!', 'Book has been permanently deleted.', 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error!', data.message || 'Failed to permanently delete book.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
            });
        }
    });
}

// Function to open the review modal with book details
function reviewBook(bookId, status) {
    // Close any open view details modal first
    document.querySelectorAll('.modal.show').forEach(modal => {
        const modalInstance = bootstrap.Modal.getInstance(modal);
        if (modalInstance) {
            modalInstance.hide();
        }
    });
    
    // Show the review modal
    const modalElement = document.getElementById(`reviewModal-${bookId}`);
    if (!modalElement) {
        Swal.fire('Error!', 'Could not find the review modal for this book.', 'error');
        return;
    }
    
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
    
    // Set the status radio button after a short delay to ensure the modal is fully loaded
    setTimeout(function() {
        const form = document.getElementById(`reviewForm-${bookId}`);
        if (!form) return;
        
        const statusInput = form.querySelector(`input[value="${status}"]`);
        if (statusInput) {
            statusInput.checked = true;
            // Trigger change event to show/hide REV Book ID field
            statusInput.dispatchEvent(new Event('change'));
        }
    }, 100);
}
</script>
@endpush
@endsection
