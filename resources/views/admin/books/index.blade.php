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
                                    <li><a href="#" class="btn btn-primary" onclick="bulkAction('accept')"><em class="icon ni ni-check"></em><span>Bulk Approve</span></a></li>
                                    <li><a href="#" class="btn btn-white btn-dim btn-outline-light" onclick="bulkAction('reject')"><em class="icon ni ni-cross"></em><span>Bulk Reject</span></a></li>
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
                                                    <li><a href="#" onclick="bulkAction('accept')"><span>Bulk Approve</span></a></li>
                                                    <li><a href="#" onclick="bulkAction('reject')"><span>Bulk Reject</span></a></li>
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
                                                <li class="nk-tb-action-hidden">
                                                    <a href="{{ route('admin.books.show', $book) }}" class="btn btn-trigger btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="View Details">
                                                        <em class="icon ni ni-eye-fill"></em>
                                                    </a>
                                                </li>
                                                @if($book->status === 'pending')
                                                    <li class="nk-tb-action-hidden">
                                                        <a href="#" class="btn btn-trigger btn-icon text-success" onclick="reviewBook({{ $book->id }}, 'accepted')" data-bs-toggle="tooltip" data-bs-placement="top" title="Approve">
                                                            <em class="icon ni ni-check-fill"></em>
                                                        </a>
                                                    </li>
                                                    <li class="nk-tb-action-hidden">
                                                        <a href="#" class="btn btn-trigger btn-icon text-danger" onclick="reviewBook({{ $book->id }}, 'rejected')" data-bs-toggle="tooltip" data-bs-placement="top" title="Reject">
                                                            <em class="icon ni ni-cross-fill"></em>
                                                        </a>
                                                    </li>
                                                @endif
                                                <li>
                                                    <div class="drodown">
                                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <ul class="link-list-opt no-bdr">
                                                                <li><a href="{{ route('admin.books.show', $book) }}"><em class="icon ni ni-eye"></em><span>View Details</span></a></li>
                                                                @if($book->status === 'pending')
                                                                    <li><a href="#" onclick="reviewBook({{ $book->id }}, 'accepted')"><em class="icon ni ni-check"></em><span>Approve</span></a></li>
                                                                    <li><a href="#" onclick="reviewBook({{ $book->id }}, 'rejected')"><em class="icon ni ni-cross"></em><span>Reject</span></a></li>
                                                                @endif
                                                                <li class="divider"></li>
                                                                <li><a href="#" class="text-danger" onclick="deleteBook({{ $book->id }})"><em class="icon ni ni-trash"></em><span>Delete</span></a></li>
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

<!-- Review Modal -->
<div class="modal fade" tabindex="-1" id="reviewModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review Book</h5>
                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body">
                <form id="reviewForm">
                    @csrf
                    <input type="hidden" id="bookId" name="book_id">
                    <input type="hidden" id="reviewStatus" name="status">
                    
                    <div class="form-group">
                        <label class="form-label">Admin Notes</label>
                        <textarea class="form-control" name="admin_notes" rows="4" placeholder="Optional notes for the author..."></textarea>
                    </div>
                    
                    <div class="form-group" id="revBookIdGroup" style="display: none;">
                        <label class="form-label">REV Book ID</label>
                        <input type="text" class="form-control" name="rev_book_id" placeholder="Enter REV system book ID">
                        <div class="form-note">Required when approving books for REV system integration.</div>
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
    const checkboxes = document.querySelectorAll('.book-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

function reviewBook(bookId, status) {
    document.getElementById('bookId').value = bookId;
    document.getElementById('reviewStatus').value = status;
    
    // Show REV Book ID field only for accepted status
    const revBookIdGroup = document.getElementById('revBookIdGroup');
    if (status === 'accepted') {
        revBookIdGroup.style.display = 'block';
    } else {
        revBookIdGroup.style.display = 'none';
    }
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
    modal.show();
}

function submitReview() {
    const form = document.getElementById('reviewForm');
    const formData = new FormData(form);
    const bookId = formData.get('book_id');
    
    fetch(`/admin/books/${bookId}/review`, {
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
    const selectedBooks = Array.from(document.querySelectorAll('.book-checkbox:checked')).map(cb => cb.value);
    
    if (selectedBooks.length === 0) {
        Swal.fire('Warning!', 'Please select at least one book.', 'warning');
        return;
    }
    
    const actionText = action === 'accept' ? 'approve' : 'reject';
    
    Swal.fire({
        title: `${actionText.charAt(0).toUpperCase() + actionText.slice(1)} Books?`,
        text: `This will ${actionText} ${selectedBooks.length} selected book(s).`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: action === 'accept' ? '#28a745' : '#dc3545',
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

function deleteBook(bookId) {
    Swal.fire({
        title: 'Delete Book?',
        text: 'This action cannot be undone!',
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
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Deleted!', 'Book has been deleted.', 'success').then(() => {
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
