@extends('layouts.admin')

@section('title', 'Edit User | Admin Panel')

@section('page-title', 'Edit User')

@section('page-description', 'Update user information and permissions')

@section('content')
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-xl">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Edit User: {{ $user->name }}</h3>
                        <div class="nk-block-des text-soft">
                            <p>Update user information, roles, and account settings.</p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-light">
                            <em class="icon ni ni-arrow-left"></em><span>Back to User</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <div class="row g-gs">
                    <!-- User Information Form -->
                    <div class="col-xxl-8">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">User Information</h6>
                                    </div>
                                </div>
                                
                                <form action="{{ route('admin.users.update', $user) }}" method="POST" class="form-validate">
                                    @csrf
                                    @method('PUT')
                                    <div class="row g-4">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="name">Full Name <span class="text-danger">*</span></label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control @error('name') error @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                                    @error('name')
                                                        <span class="form-note-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="email">Email Address <span class="text-danger">*</span></label>
                                                <div class="form-control-wrap">
                                                    <input type="email" class="form-control @error('email') error @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                                    @error('email')
                                                        <span class="form-note-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="phone">Phone Number</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control @error('phone') error @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                                    @error('phone')
                                                        <span class="form-note-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="website">Website URL</label>
                                                <div class="form-control-wrap">
                                                    <input type="url" class="form-control @error('website') error @enderror" id="website" name="website" value="{{ old('website', $user->website) }}" placeholder="https://example.com">
                                                    @error('website')
                                                        <span class="form-note-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label" for="bio">Bio/Description</label>
                                                <div class="form-control-wrap">
                                                    <textarea class="form-control @error('bio') error @enderror" id="bio" name="bio" rows="4" placeholder="Brief description about the user">{{ old('bio', $user->bio) }}</textarea>
                                                    @error('bio')
                                                        <span class="form-note-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Role Management -->
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">User Roles</label>
                                                <div class="form-control-wrap">
                                                    @foreach($roles as $role)
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="role_{{ $role->id }}" name="roles[]" value="{{ $role->name }}" 
                                                                {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                                                            <label class="custom-control-label" for="role_{{ $role->id }}">
                                                                {{ ucfirst($role->name) }}
                                                                <span class="form-note">{{ $role->description ?? 'No description available' }}</span>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                    @error('roles')
                                                        <span class="form-note-error">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Account Status -->
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Email Verification</label>
                                                <div class="form-control-wrap">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id="email_verified" name="email_verified" 
                                                            {{ $user->email_verified_at ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="email_verified">Email Verified</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-lg btn-primary">Update User</button>
                                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-lg btn-outline-light">Cancel</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- User Actions & Security -->
                    <div class="col-xxl-4">
                        <!-- Password Reset -->
                        <div class="card card-bordered card-full mb-4">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Password Management</h6>
                                    </div>
                                </div>
                                
                                <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" id="passwordResetForm">
                                    @csrf
                                    <div class="form-group">
                                        <label class="form-label" for="new_password">New Password</label>
                                        <div class="form-control-wrap">
                                            <a href="#" class="form-icon form-icon-right passcode-switch lg" data-target="new_password">
                                                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                            </a>
                                            <input type="password" class="form-control" id="new_password" name="new_password">
                                        </div>
                                        <div class="form-note">Leave blank to keep current password.</div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="new_password_confirmation">Confirm Password</label>
                                        <div class="form-control-wrap">
                                            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-warning btn-block">Reset Password</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Account Actions -->
                        <div class="card card-bordered card-full mb-4">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title">Account Actions</h6>
                                    </div>
                                </div>
                                
                                <div class="row g-2">
                                    @if(!$user->email_verified_at)
                                        <div class="col-12">
                                            <button class="btn btn-info btn-block" onclick="sendVerificationEmail({{ $user->id }})">
                                                <em class="icon ni ni-mail"></em><span>Send Verification Email</span>
                                            </button>
                                        </div>
                                    @endif
                                    
                                    @if(!$user->hasRole('author'))
                                        <div class="col-12">
                                            <button class="btn btn-success btn-block" onclick="promoteToAuthor({{ $user->id }})">
                                                <em class="icon ni ni-user-add"></em><span>Promote to Author</span>
                                            </button>
                                        </div>
                                    @endif
                                    
                                    <div class="col-12">
                                        <button class="btn btn-outline-primary btn-block" onclick="loginAsUser({{ $user->id }})">
                                            <em class="icon ni ni-signin"></em><span>Login as User</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Danger Zone -->
                        <div class="card card-bordered card-full">
                            <div class="card-inner">
                                <div class="card-title-group align-start mb-3">
                                    <div class="card-title">
                                        <h6 class="title text-danger">Danger Zone</h6>
                                    </div>
                                </div>
                                
                                <div class="alert alert-danger">
                                    <div class="alert-cta">
                                        <h6>Delete User Account</h6>
                                        <p>This action cannot be undone. All user data, books, and transactions will be permanently deleted.</p>
                                        <button class="btn btn-danger" onclick="deleteUser({{ $user->id }})">Delete Account</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function sendVerificationEmail(userId) {
    Swal.fire({
        title: 'Send Verification Email?',
        text: 'This will send a verification email to the user.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, send!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/users/${userId}/send-verification`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Sent!', 'Verification email has been sent.', 'success');
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

function loginAsUser(userId) {
    Swal.fire({
        title: 'Login as User?',
        text: 'You will be logged in as this user. You can switch back from the user dashboard.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, login!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `/admin/users/${userId}/login-as`;
        }
    });
}

function deleteUser(userId) {
    Swal.fire({
        title: 'Delete User?',
        text: 'This action cannot be undone! All user data will be permanently deleted.',
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
                        .then(() => window.location.href = '/admin/users');
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

// Password reset form validation
document.getElementById('passwordResetForm').addEventListener('submit', function(e) {
    const password = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('new_password_confirmation').value;
    
    if (password && password !== confirmPassword) {
        e.preventDefault();
        Swal.fire('Error!', 'Passwords do not match.', 'error');
        return false;
    }
    
    if (password && password.length < 8) {
        e.preventDefault();
        Swal.fire('Error!', 'Password must be at least 8 characters long.', 'error');
        return false;
    }
});
</script>
@endpush
@endsection
