@extends('layouts.admin')

@section('title', 'User Management | Admin Panel')

@section('page-title', 'User Management')

@section('page-description', 'Manage platform users and permissions')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Users</h3>
                        <div class="nk-block-des text-soft">
                            <p>Manage all platform users, roles, and permissions.</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li><a href="{{ route('admin.users.create') }}" class="btn btn-primary"><em class="icon ni ni-plus"></em><span>Add User</span></a></li>
                                    <li><a href="{{ route('admin.users.authors') }}" class="btn btn-white btn-dim btn-outline-light"><em class="icon ni ni-users"></em><span>View Authors</span></a></li>
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
                                        <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex gap-2">
                                            <div class="form-wrap w-150px">
                                                <select name="role" class="form-select form-select-sm">
                                                    <option value="">All Roles</option>
                                                    @foreach($roles as $role)
                                                        <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                                                            {{ ucfirst($role->name) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-wrap w-150px">
                                                <select name="status" class="form-select form-select-sm">
                                                    <option value="">All Status</option>
                                                    <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Unverified</option>
                                                </select>
                                            </div>
                                            <div class="form-wrap flex-md-nowrap">
                                                <div class="form-icon form-icon-right">
                                                    <em class="icon ni ni-search"></em>
                                                </div>
                                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search users..." value="{{ request('search') }}">
                                            </div>
                                            <div class="btn-wrap">
                                                <button type="submit" class="btn btn-sm btn-icon btn-primary"><em class="icon ni ni-search"></em></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="card-tools me-n1">
                                    <ul class="btn-toolbar gx-1">
                                        <li>
                                            <a href="#" class="btn btn-icon search-toggle toggle-search" data-target="search"><em class="icon ni ni-search"></em></a>
                                        </li>
                                        <li class="btn-toolbar-sep"></li>
                                        <li>
                                            <div class="toggle-wrap">
                                                <a href="#" class="btn btn-icon btn-trigger toggle" data-target="cardTools"><em class="icon ni ni-menu-right"></em></a>
                                                <div class="toggle-content" data-content="cardTools">
                                                    <ul class="btn-toolbar gx-1">
                                                        <li class="toggle-close">
                                                            <a href="#" class="btn btn-icon btn-trigger toggle" data-target="cardTools"><em class="icon ni ni-arrow-left"></em></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card-inner p-0">
                            <div class="nk-tb-list nk-tb-ulist">
                                <div class="nk-tb-item nk-tb-head">
                                    <div class="nk-tb-col nk-tb-col-check">
                                        <div class="custom-control custom-control-sm custom-checkbox notext">
                                            <input type="checkbox" class="custom-control-input" id="uid">
                                            <label class="custom-control-label" for="uid"></label>
                                        </div>
                                    </div>
                                    <div class="nk-tb-col"><span class="sub-text">User</span></div>
                                    <div class="nk-tb-col tb-col-mb"><span class="sub-text">Role</span></div>
                                    <div class="nk-tb-col tb-col-md"><span class="sub-text">Status</span></div>
                                    <div class="nk-tb-col tb-col-lg"><span class="sub-text">Joined</span></div>
                                    <div class="nk-tb-col nk-tb-col-tools text-end">
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-xs btn-outline-light btn-icon dropdown-toggle" data-bs-toggle="dropdown"><em class="icon ni ni-plus"></em></a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <ul class="link-list-opt no-bdr">
                                                    <li><a href="{{ route('admin.users.create') }}"><span>Add User</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @forelse($users as $user)
                                    <div class="nk-tb-item">
                                        <div class="nk-tb-col nk-tb-col-check">
                                            <div class="custom-control custom-control-sm custom-checkbox notext">
                                                <input type="checkbox" class="custom-control-input" id="uid{{ $user->id }}">
                                                <label class="custom-control-label" for="uid{{ $user->id }}"></label>
                                            </div>
                                        </div>
                                        <div class="nk-tb-col">
                                            <div class="user-card">
                                                <div class="user-avatar bg-primary">
                                                    <span>{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                                </div>
                                                <div class="user-info">
                                                    <span class="tb-lead">{{ $user->name }} 
                                                        @if($user->email_verified_at)
                                                            <span class="dot dot-success d-md-none ms-1"></span>
                                                        @endif
                                                    </span>
                                                    <span>{{ $user->email }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="nk-tb-col tb-col-mb">
                                            @foreach($user->roles as $role)
                                                <span class="badge badge-sm badge-dim bg-outline-primary">{{ ucfirst($role->name) }}</span>
                                            @endforeach
                                        </div>
                                        <div class="nk-tb-col tb-col-md">
                                            @if($user->email_verified_at)
                                                <span class="tb-status text-success">Verified</span>
                                            @else
                                                <span class="tb-status text-warning">Unverified</span>
                                            @endif
                                        </div>
                                        <div class="nk-tb-col tb-col-lg">
                                            <span>{{ $user->created_at->format('M d, Y') }}</span>
                                        </div>
                                        <div class="nk-tb-col nk-tb-col-tools">
                                            <ul class="nk-tb-actions gx-1">
                                                <li class="nk-tb-action-hidden">
                                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-trigger btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="View">
                                                        <em class="icon ni ni-eye-fill"></em>
                                                    </a>
                                                </li>
                                                <li class="nk-tb-action-hidden">
                                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-trigger btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                        <em class="icon ni ni-edit-fill"></em>
                                                    </a>
                                                </li>
                                                <li>
                                                    <div class="drodown">
                                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <ul class="link-list-opt no-bdr">
                                                                <li><a href="{{ route('admin.users.show', $user) }}"><em class="icon ni ni-eye"></em><span>View Details</span></a></li>
                                                                <li><a href="{{ route('admin.users.edit', $user) }}"><em class="icon ni ni-edit"></em><span>Edit User</span></a></li>
                                                                @if(!$user->hasRole('author'))
                                                                    <li><a href="#" onclick="promoteToAuthor({{ $user->id }})"><em class="icon ni ni-user-add"></em><span>Promote to Author</span></a></li>
                                                                @endif
                                                                <li class="divider"></li>
                                                                <li><a href="#" class="text-danger" onclick="deleteUser({{ $user->id }})"><em class="icon ni ni-trash"></em><span>Delete User</span></a></li>
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
                                                <em class="icon ni ni-users" style="font-size: 3rem; opacity: 0.3;"></em>
                                                <p class="text-soft mt-2">No users found</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="card-inner">
                            {{ $users->appends(request()->query())->links() }}
                        </div>
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
        text: 'This will give the user author privileges.',
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

function deleteUser(userId) {
    Swal.fire({
        title: 'Delete User?',
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Deleted!', 'User has been deleted.', 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Error!', data.message || 'Cannot delete user.', 'error');
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
