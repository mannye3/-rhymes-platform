@extends('layouts.admin')

@section('title', $user->name . ' | User Details')

@section('page-title', 'User Details')

@section('page-description', 'View and manage user information')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">{{ $user->name }}</h3>
                        <div class="nk-block-des text-soft">
                            <p>User ID: #{{ $user->id }} • Joined {{ $user->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li><a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary"><em class="icon ni ni-edit"></em><span>Edit User</span></a></li>
                                    <li><a href="{{ route('admin.users.index') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-arrow-left"></em><span>Back to Users</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="row g-gs">
                    <!-- User Profile Card -->
                    <div class="col-xxl-4">
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">User Profile</h6>
                                    </div>
                                    <div class="card-tools">
                                        @if($user->email_verified_at)
                                            <span class="badge badge-success">Verified</span>
                                        @else
                                            <span class="badge badge-warning">Unverified</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="user-card">
                                    <div class="user-avatar lg bg-primary">
                                        <span>{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                    </div>
                                    <div class="user-info">
                                        <h5>{{ $user->name }}</h5>
                                        <span class="sub-text">{{ $user->email }}</span>
                                    </div>
                                </div>
                                
                                <div class="user-meta mt-4">
                                    <ul class="nk-list-meta">
                                        <li class="nk-list-meta-item">
                                            <span class="nk-list-meta-label">Role:</span>
                                            <span class="nk-list-meta-value">
                                                @foreach($user->roles as $role)
                                                    <span class="badge badge-dim bg-outline-primary">{{ ucfirst($role->name) }}</span>
                                                @endforeach
                                            </span>
                                        </li>
                                        @if($user->phone)
                                            <li class="nk-list-meta-item">
                                                <span class="nk-list-meta-label">Phone:</span>
                                                <span class="nk-list-meta-value">{{ $user->phone }}</span>
                                            </li>
                                        @endif
                                        @if($user->website)
                                            <li class="nk-list-meta-item">
                                                <span class="nk-list-meta-label">Website:</span>
                                                <span class="nk-list-meta-value">
                                                    <a href="{{ $user->website }}" target="_blank" class="link">{{ $user->website }}</a>
                                                </span>
                                            </li>
                                        @endif
                                        <li class="nk-list-meta-item">
                                            <span class="nk-list-meta-label">Member Since:</span>
                                            <span class="nk-list-meta-value">{{ $user->created_at->format('M d, Y') }}</span>
                                        </li>
                                        @if($user->email_verified_at)
                                            <li class="nk-list-meta-item">
                                                <span class="nk-list-meta-label">Verified:</span>
                                                <span class="nk-list-meta-value">{{ $user->email_verified_at->format('M d, Y') }}</span>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                                
                                @if($user->bio)
                                    <div class="user-bio mt-4">
                                        <h6 class="overline-title-alt">About</h6>
                                        <p>{{ $user->bio }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- User Statistics & Activity -->
                    <div class="col-xxl-8">
                        @if($user->hasRole('author'))
                            <!-- Author Statistics -->
                            <div class="card card-bordered card-full mb-4">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-3">
                                        <div class="card-title">
                                            <h6 class="title">Author Statistics</h6>
                                        </div>
                                    </div>
                                    
                                    <div class="row g-4">
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="statbox">
                                                <div class="inbox-item">
                                                    <div class="inbox-item-img">
                                                        <div class="inbox-item-img bg-primary-dim">
                                                            <em class="icon ni ni-book"></em>
                                                        </div>
                                                    </div>
                                                    <div class="inbox-item-body">
                                                        <p class="inbox-item-text">Total Books</p>
                                                        <h4 class="inbox-item-title">{{ number_format($stats['total_books']) }}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="statbox">
                                                <div class="inbox-item">
                                                    <div class="inbox-item-img">
                                                        <div class="inbox-item-img bg-success-dim">
                                                            <em class="icon ni ni-check-circle"></em>
                                                        </div>
                                                    </div>
                                                    <div class="inbox-item-body">
                                                        <p class="inbox-item-text">Published</p>
                                                        <h4 class="inbox-item-title">{{ number_format($stats['published_books']) }}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="statbox">
                                                <div class="inbox-item">
                                                    <div class="inbox-item-img">
                                                        <div class="inbox-item-img bg-warning-dim">
                                                            <em class="icon ni ni-clock"></em>
                                                        </div>
                                                    </div>
                                                    <div class="inbox-item-body">
                                                        <p class="inbox-item-text">Pending</p>
                                                        <h4 class="inbox-item-title">{{ number_format($stats['pending_books']) }}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="statbox">
                                                <div class="inbox-item">
                                                    <div class="inbox-item-img">
                                                        <div class="inbox-item-img bg-info-dim">
                                                            <em class="icon ni ni-coins"></em>
                                                        </div>
                                                    </div>
                                                    <div class="inbox-item-body">
                                                        <p class="inbox-item-text">Total Earnings</p>
                                                        <h4 class="inbox-item-title">${{ number_format($stats['total_earnings'], 2) }}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Books -->
                            @if($user->books->count() > 0)
                                <div class="card card-bordered card-full mb-4">
                                    <div class="card-inner">
                                        <div class="card-title-group align-start mb-3">
                                            <div class="card-title">
                                                <h6 class="title">Recent Books</h6>
                                            </div>
                                        </div>
                                        
                                        <div class="nk-tb-list nk-tb-orders">
                                            <div class="nk-tb-item nk-tb-head">
                                                <div class="nk-tb-col"><span>Title</span></div>
                                                <div class="nk-tb-col tb-col-md"><span>Status</span></div>
                                                <div class="nk-tb-col tb-col-lg"><span>Created</span></div>
                                                <div class="nk-tb-col"><span>Action</span></div>
                                            </div>
                                            @foreach($user->books->take(5) as $book)
                                                <div class="nk-tb-item">
                                                    <div class="nk-tb-col">
                                                        <span class="tb-lead">{{ $book->title }}</span>
                                                        <span class="tb-sub text-primary">{{ $book->genre }}</span>
                                                    </div>
                                                    <div class="nk-tb-col tb-col-md">
                                                        @if($book->status === 'pending')
                                                            <span class="badge badge-dot badge-dot-xs bg-warning">Pending</span>
                                                        @elseif($book->status === 'accepted')
                                                            <span class="badge badge-dot badge-dot-xs bg-success">Published</span>
                                                        @elseif($book->status === 'rejected')
                                                            <span class="badge badge-dot badge-dot-xs bg-danger">Rejected</span>
                                                        @endif
                                                    </div>
                                                    <div class="nk-tb-col tb-col-lg">
                                                        <span class="tb-sub">{{ $book->created_at->format('M d, Y') }}</span>
                                                    </div>
                                                    <div class="nk-tb-col">
                                                        <a href="{{ route('admin.books.show', $book) }}" class="btn btn-sm btn-outline-light">View</a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Payout History -->
                            @if($user->payouts->count() > 0)
                                <div class="card card-bordered card-full">
                                    <div class="card-inner">
                                        <div class="card-title-group align-start mb-3">
                                            <div class="card-title">
                                                <h6 class="title">Recent Payouts</h6>
                                            </div>
                                        </div>
                                        
                                        <div class="nk-tb-list nk-tb-orders">
                                            <div class="nk-tb-item nk-tb-head">
                                                <div class="nk-tb-col"><span>Amount</span></div>
                                                <div class="nk-tb-col tb-col-md"><span>Status</span></div>
                                                <div class="nk-tb-col tb-col-lg"><span>Requested</span></div>
                                            </div>
                                            @foreach($user->payouts->take(5) as $payout)
                                                <div class="nk-tb-item">
                                                    <div class="nk-tb-col">
                                                        <span class="tb-lead">${{ number_format($payout->amount_requested, 2) }}</span>
                                                        <span class="tb-sub">Fee: ${{ number_format($payout->processing_fee, 2) }}</span>
                                                    </div>
                                                    <div class="nk-tb-col tb-col-md">
                                                        @if($payout->status === 'pending')
                                                            <span class="badge badge-dot badge-dot-xs bg-warning">Pending</span>
                                                        @elseif($payout->status === 'approved')
                                                            <span class="badge badge-dot badge-dot-xs bg-success">Approved</span>
                                                        @elseif($payout->status === 'denied')
                                                            <span class="badge badge-dot badge-dot-xs bg-danger">Denied</span>
                                                        @endif
                                                    </div>
                                                    <div class="nk-tb-col tb-col-lg">
                                                        <span class="tb-sub">{{ $payout->created_at->format('M d, Y') }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @else
                            <!-- Non-Author User Actions -->
                            <div class="card card-bordered card-full">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-3">
                                        <div class="card-title">
                                            <h6 class="title">User Actions</h6>
                                        </div>
                                    </div>
                                    
                                    <div class="row g-4">
                                        <div class="col-12">
                                            <div class="alert alert-info">
                                                <div class="alert-cta">
                                                    <h6>Promote to Author</h6>
                                                    <p>This user is not currently an author. You can promote them to author status to allow them to publish books.</p>
                                                    <button class="btn btn-info" onclick="promoteToAuthor({{ $user->id }})">Promote to Author</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function promoteToAuthor(userId) {
    Swal.fire({
        title: 'Promote to Author?',
        text: 'This will give the user author privileges and allow them to publish books.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, promote!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/users/${userId}/promote-author`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Promoted!', 'User has been promoted to author.', 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Error!', data.message || 'Something went wrong.', 'error');
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
