<?php
// Include header

use Core\Session;

include $headerPath;
?>

<div class="container py-5">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Admin Profile</h2>
    </div>

    <div class="row g-4">
        <!-- Left Column - Profile Info -->
        <div class="col-lg-4">
            <!-- Profile Card -->
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-body p-4">
                    <div class="text-center position-relative mb-4">
                        <div class="profile-picture-container mx-auto">
                            <img src="<?= Session::get("profile_url") ?>" class="rounded-circle img-fluid profile-image shadow" 
                                style="width: 150px; height: 150px; object-fit: cover;" alt="Admin Profile Picture">
                            <div class="profile-picture-overlay position-absolute bottom-0 end-0">
                                <button class="btn btn-sm btn-primary rounded-circle shadow-sm" 
                                    data-bs-toggle="modal" data-bs-target="#editProfilePictureModal"
                                    style="width: 36px; height: 36px;">
                                    <i class="bi bi-camera-fill"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <h4 class="mb-1"><?= Session::get('full_name') ?></h4>
                        <p class="text-muted small mb-2">
                            <span class="badge bg-primary">Administrator</span>
                        </p>
                        <p class="text-muted small mb-3">
                            <i class="bi bi-calendar3 me-1"></i>Admin since: <?= Session::get('member_since') ?>
                        </p>
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3" 
                            data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="bi bi-pencil me-1"></i>Edit Profile
                        </button>
                    </div>
                </div>
                
                <hr class="my-0">
            </div>
            
            <!-- Account Settings Card -->
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-light border-0">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear me-2 text-muted"></i>Account Settings
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            <i class="bi bi-key me-2"></i>Change Password
                        </button>
                        
                        <div class="dropdown-divider my-3"></div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Delete Account</h6>
                                <p class="text-muted small mb-0">This action cannot be undone</p>
                            </div>
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                <i class="bi bi-trash me-1"></i>Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column - Details -->
        <div class="col-lg-8">
            <!-- Personal Information Card -->
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center border-0">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-vcard me-2 text-primary"></i>Personal Information
                    </h5>
                    <button class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </button>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-group">
                                <label class="text-muted small mb-1">Full Name</label>
                                <h6 class="mb-0"><?= Session::get("full_name") ?></h6>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-group">
                                <label class="text-muted small mb-1">Email Address</label>
                                <h6 class="mb-0"><?= Session::get("user_email") ?></h6>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-group">
                                <label class="text-muted small mb-1">Phone Number</label>
                                <h6 class="mb-0">
                                    <?php if (Session::get('phone_number')): ?>
                                        <i class="bi bi-telephone me-1 text-success"></i><?= Session::get('phone_number') ?>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic">No phone number added</span>
                                    <?php endif; ?>
                                </h6>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-group">
                                <label class="text-muted small mb-1">Admin Role</label>
                                <h6 class="mb-0">System Administrator</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Admin Statistics Card -->
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center border-0">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bar-chart me-2 text-primary"></i>Admin Statistics
                    </h5>
                    <span class="text-muted small">System overview</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="stat-card p-3 border rounded h-100">
                                <h6 class="text-muted mb-3">System Overview</h6>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-box me-3 p-2 rounded-circle bg-primary bg-opacity-10">
                                        <i class="bi bi-people text-primary"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0"><?= number_format($stats['total_users']) ?></h4>
                                        <span class="small text-muted">Total Users</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-box me-3 p-2 rounded-circle bg-success bg-opacity-10">
                                        <i class="bi bi-book text-success"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0"><?= number_format($stats['total_books']) ?></h4>
                                        <span class="small text-muted">Total Books</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="icon-box me-3 p-2 rounded-circle bg-info bg-opacity-10">
                                        <i class="bi bi-bag text-info"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0"><?= number_format($stats['total_purchases']) ?></h4>
                                        <span class="small text-muted">Total Purchases</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="stat-card p-3 border rounded h-100">
                                <h6 class="text-muted mb-3">Admin Activity</h6>
                                <div class="completion-rate mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small text-muted">System Health</span>
                                        <span class="badge bg-success"><?= $stats['system_health'] ?>%</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $stats['system_health'] ?>%"
                                            aria-valuenow="<?= $stats['system_health'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <p class="text-muted small mt-2 mb-0">
                                        <i class="bi bi-info-circle me-1"></i>
                                        All systems operating normally
                                    </p>
                                </div>
                                
                                <div class="admin-actions">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small text-muted">Activity Log Entries</span>
                                        <span class="badge bg-primary"><?= number_format($stats['admin_actions']) ?></span>
                                    </div>
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-lightning me-1"></i>
                                        Total logged activities in the system
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions Card -->
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-light d-flex justify-content-between align-items-center border-0">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning me-2 text-warning"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-2">
                        <div class="col-md-3 col-6">
                            <a href="/admin/user-management" class="btn btn-outline-primary w-100 py-2">
                                <i class="bi bi-people me-2"></i>Users
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="/admin/book-management" class="btn btn-outline-success w-100 py-2">
                                <i class="bi bi-book me-2"></i>Books
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="/admin/reading-sessions" class="btn btn-outline-warning w-100 py-2">
                                <i class="bi bi-book-half me-2"></i>Reading
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="/admin/activity-logs" class="btn btn-outline-info w-100 py-2">
                                <i class="bi bi-activity me-2"></i>Logs
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="editProfileModalLabel">
                    <i class="bi bi-person-lines-fill me-2"></i>Edit Admin Profile
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateProfileForm" action="/admin/admin-profile/update-profile-info" method="POST">
                    <div class="mb-3">
                        <label for="firstName" class="form-label fw-bold">First Name</label>
                        <input type="text" class="form-control" id="firstName" name="firstName" value="<?= Session::get('first_name') ?>" placeholder="Enter your first name">
                    </div>
                    <div class="mb-3">
                        <label for="lastName" class="form-label fw-bold">Last Name</label>
                        <input type="text" class="form-control" id="lastName" name="lastName" value="<?= Session::get('last_name') ?>" placeholder="Enter your last name">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label fw-bold">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?= Session::get('phone_number') ?>" placeholder="Enter your phone number">
                        </div>
                    </div>
                    <div class="mt-4 d-flex justify-content-end">
                        <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Picture Modal -->
<div class="modal fade" id="editProfilePictureModal" tabindex="-1" aria-labelledby="editProfilePictureModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="editProfilePictureModalLabel">
                    <i class="bi bi-camera me-2"></i>Change Profile Picture
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateProfilePictureForm" action="/admin/admin-profile/update-profile-pic" method="POST" enctype="multipart/form-data">
                    <div class="text-center mb-4">
                        <img id="profileImagePreview" src="<?= Session::get("profile_url") ?>" class="rounded-circle img-fluid profile-image shadow" 
                            style="width: 150px; height: 150px; object-fit: cover;" alt="Profile Picture Preview">
                    </div>
                    
                    <div class="mb-3">
                        <label for="profileImage" class="form-label fw-bold">Upload New Picture</label>
                        <input class="form-control" type="file" id="profileImage" name="profileImage" accept="image/*">
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <small>Recommended image size: 500x500 pixels. Maximum file size: 2MB. Supported formats: JPG, PNG, GIF</small>
                    </div>
                    
                    <div class="mt-4 d-flex justify-content-end">
                        <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload Photo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="changePasswordModalLabel">
                    <i class="bi bi-key me-2"></i>Change Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm" action="/admin/admin-profile/change-password" method="POST">
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label fw-bold">Current Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="currentPassword" name="currentPassword" placeholder="Enter your current password">
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="mb-3">
                        <label for="newPassword" class="form-label fw-bold">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" class="form-control" id="newPassword" name="newPassword" placeholder="Enter new password">
                        </div>
                        <div class="form-text text-muted">
                            <small>Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label fw-bold">Confirm New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm new password">
                        </div>
                    </div>
                    
                    <div class="mt-4 d-flex justify-content-end">
                        <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteAccountModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Delete Account
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="display-1 text-danger">
                        <i class="bi bi-trash"></i>
                    </div>
                    <h4 class="mt-3">Are you absolutely sure?</h4>
                    <p class="text-muted">This action <strong>cannot</strong> be undone. This will permanently delete your account and remove your data from our servers.</p>
                </div>
                
                <form id="deleteAccountForm" action="/admin/admin-profile/delete-account" method="POST">
                    <div class="mb-3">
                        <label for="deleteConfirmPassword" class="form-label fw-bold">Enter Your Password to Confirm</label>
                        <div class="input-group">
                            <span class="input-group-text bg-danger text-white"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="deleteConfirmPassword" name="password" placeholder="Enter your password">
                        </div>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="confirmDeleteCheckbox" required>
                        <label class="form-check-label" for="confirmDeleteCheckbox">
                            I understand that this action cannot be reversed
                        </label>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                            <i class="bi bi-trash me-2"></i>Delete My Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview profile image when file is selected
    const profileImage = document.getElementById('profileImage');
    const profileImagePreview = document.getElementById('profileImagePreview');
    
    if (profileImage && profileImagePreview) {
        profileImage.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profileImagePreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Handle delete account checkbox
    const confirmDeleteCheckbox = document.getElementById('confirmDeleteCheckbox');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    
    if (confirmDeleteCheckbox && confirmDeleteBtn) {
        confirmDeleteCheckbox.addEventListener('change', function() {
            confirmDeleteBtn.disabled = !this.checked;
        });
    }
});
</script>

<?php include $footerPath; ?>