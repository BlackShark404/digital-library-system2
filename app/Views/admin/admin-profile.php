<?php
// Include header

use Core\Session;

include $headerPath;
?>

<div class="container py-5">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Admin Profile</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Admin Profile</li>
            </ol>
        </nav>
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
                
                <!-- Admin Stats -->
                <div class="card-body p-4">
                    <h5 class="card-title d-flex align-items-center mb-3">
                        <i class="bi bi-bar-chart-line me-2 text-primary"></i>
                        Admin Dashboard
                    </h5>
                    
                    <div class="row row-cols-2 g-3">
                        <div class="col">
                            <div class="bg-light rounded-3 p-3 h-100">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box me-3 bg-primary bg-opacity-10 text-primary rounded-circle p-2">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div>
                                        <h2 class="mb-0 fs-4 fw-bold">324</h2>
                                        <span class="small text-muted">Total Users</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col">
                            <div class="bg-light rounded-3 p-3 h-100">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box me-3 bg-success bg-opacity-10 text-success rounded-circle p-2">
                                        <i class="bi bi-book"></i>
                                    </div>
                                    <div>
                                        <h2 class="mb-0 fs-4 fw-bold">1,250</h2>
                                        <span class="small text-muted">Total Books</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col">
                            <div class="bg-light rounded-3 p-3 h-100">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box me-3 bg-warning bg-opacity-10 text-warning rounded-circle p-2">
                                        <i class="bi bi-ticket-perforated"></i>
                                    </div>
                                    <div>
                                        <h2 class="mb-0 fs-4 fw-bold">18</h2>
                                        <span class="small text-muted">Open Tickets</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col">
                            <div class="bg-light rounded-3 p-3 h-100">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box me-3 bg-info bg-opacity-10 text-info rounded-circle p-2">
                                        <i class="bi bi-bag"></i>
                                    </div>
                                    <div>
                                        <h2 class="mb-0 fs-4 fw-bold">82</h2>
                                        <span class="small text-muted">Total Orders</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                        
                        <button class="btn btn-outline-secondary">
                            <i class="bi bi-bell me-2"></i>Notification Settings
                        </button>
                        
                        <button class="btn btn-outline-info">
                            <i class="bi bi-shield-lock me-2"></i>Security Settings
                        </button>
                        
                        <div class="dropdown-divider my-3"></div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Admin Privileges</h6>
                                <p class="text-muted small mb-0">Request role change</p>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-person-gear me-1"></i>Request
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
            
            <!-- Recent Admin Activity Card -->
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center border-0">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-activity me-2 text-success"></i>Recent Admin Activity
                    </h5>
                    <a href="#" class="btn btn-sm btn-outline-success rounded-pill">
                        <i class="bi bi-clock-history me-1"></i>View All
                    </a>
                </div>
                <div class="card-body p-4">
                    <!-- Activity Item 1 -->
                    <div class="activity-item mb-3 pb-3 border-bottom">
                        <div class="d-flex">
                            <div class="activity-icon me-3 bg-primary bg-opacity-10 text-primary rounded-circle p-2">
                                <i class="bi bi-person-plus"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0">Added new user</h6>
                                    <span class="badge bg-light text-dark">2 hours ago</span>
                                </div>
                                <p class="text-muted small mb-0">Added new user "John Smith" to the system with "Reader" role.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Activity Item 2 -->
                    <div class="activity-item mb-3 pb-3 border-bottom">
                        <div class="d-flex">
                            <div class="activity-icon me-3 bg-success bg-opacity-10 text-success rounded-circle p-2">
                                <i class="bi bi-book"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0">Updated book catalog</h6>
                                    <span class="badge bg-light text-dark">Yesterday</span>
                                </div>
                                <p class="text-muted small mb-0">Added 15 new titles to the "Science Fiction" category.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Activity Item 3 -->
                    <div class="activity-item">
                        <div class="d-flex">
                            <div class="activity-icon me-3 bg-warning bg-opacity-10 text-warning rounded-circle p-2">
                                <i class="bi bi-gear"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0">System settings updated</h6>
                                    <span class="badge bg-light text-dark">3 days ago</span>
                                </div>
                                <p class="text-muted small mb-0">Updated email notification settings for user registration process.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 text-center">
                    <a href="#" class="btn btn-link text-success">View complete activity log <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
            
            <!-- System Overview Card -->
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-light d-flex justify-content-between align-items-center border-0">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up me-2 text-warning"></i>System Overview
                    </h5>
                    <button class="btn btn-sm btn-outline-warning rounded-pill">
                        <i class="bi bi-download me-1"></i>Export Report
                    </button>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- System Health -->
                        <div class="col-md-6">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title d-flex align-items-center mb-3">
                                        <i class="bi bi-heart-pulse me-2 text-primary"></i>System Health
                                    </h6>
                                    <div class="text-center mb-3">
                                        <div class="d-inline-block position-relative">
                                            <svg width="120" height="120" viewBox="0 0 120 120">
                                                <circle cx="60" cy="60" r="54" fill="none" stroke="#e9ecef" stroke-width="12"/>
                                                <circle cx="60" cy="60" r="54" fill="none" stroke="#0d6efd" stroke-width="12"
                                                    stroke-dasharray="339.3" stroke-dashoffset="33.9" transform="rotate(-90 60 60)"/>
                                            </svg>
                                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                                <h3 class="mb-0 fw-bold">90%</h3>
                                                <small class="text-muted">Optimal</small>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-center mb-0">All systems operating normally</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User Activity -->
                        <div class="col-md-6">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title d-flex align-items-center mb-3">
                                        <i class="bi bi-people me-2 text-success"></i>User Activity
                                    </h6>
                                    <div class="text-center mb-3">
                                        <div class="d-inline-block position-relative">
                                            <svg width="120" height="120" viewBox="0 0 120 120">
                                                <circle cx="60" cy="60" r="54" fill="none" stroke="#e9ecef" stroke-width="12"/>
                                                <circle cx="60" cy="60" r="54" fill="none" stroke="#198754" stroke-width="12"
                                                    stroke-dasharray="339.3" stroke-dashoffset="203.6" transform="rotate(-90 60 60)"/>
                                            </svg>
                                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                                <h3 class="mb-0 fw-bold">40%</h3>
                                                <small class="text-muted">Increase</small>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-center mb-0">User activity up 40% from last month</p>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="col-12">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title mb-3">Quick Admin Actions</h6>
                                    <div class="row g-2">
                                        <div class="col-md-3 col-6">
                                            <a href="#" class="btn btn-outline-primary w-100 py-2">
                                                <i class="bi bi-people me-2"></i>Users
                                            </a>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <a href="#" class="btn btn-outline-success w-100 py-2">
                                                <i class="bi bi-book me-2"></i>Books
                                            </a>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <a href="#" class="btn btn-outline-warning w-100 py-2">
                                                <i class="bi bi-ticket-perforated me-2"></i>Tickets
                                            </a>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <a href="#" class="btn btn-outline-info w-100 py-2">
                                                <i class="bi bi-gear me-2"></i>Settings
                                            </a>
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
                <form id="updateProfileForm">
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
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Used for account recovery and important system notifications.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x me-1"></i>Cancel
                </button>
                <button type="submit" form="updateProfileForm" class="btn btn-primary">
                    <i class="bi bi-check2 me-1"></i>Save Changes
                </button>
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
                    <i class="bi bi-person-badge me-2"></i>Update Admin Profile Picture
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateProfilePictureForm" enctype="multipart/form-data">
                    <div class="text-center mb-4">
                        <div class="position-relative mx-auto" style="width: 180px; height: 180px;">
                            <img id="profileImagePreview" src="<?= Session::get('profile_url') ?>" 
                                class="rounded-circle img-fluid border shadow" 
                                style="width: 180px; height: 180px; object-fit: cover;"
                                alt="Profile Picture Preview">
                            <div class="position-absolute bottom-0 end-0 bg-white rounded-circle p-1 shadow-sm">
                                <label for="profileImage" class="btn btn-sm btn-primary rounded-circle mb-0" style="width: 36px; height: 36px;">
                                    <i class="bi bi-camera-fill"></i>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label for="profileImage" class="form-label fw-bold mb-0">Select New Image</label>
                            <span class="badge bg-light text-dark">Max: 5MB</span>
                        </div>
                        
                        <div class="input-group">
                            <input type="file" class="form-control" id="profileImage" name="profile_image" 
                                accept="image/jpeg,image/png,image/gif,image/webp">
                            <button class="btn btn-outline-secondary" type="button" id="clearFileBtn">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        
                        <div class="form-text mt-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Supported formats: JPEG, PNG, GIF, WebP
                        </div>
                    </div>
                    
                    <div class="alert alert-light border rounded p-3 mb-0">
                        <div class="d-flex">
                            <i class="bi bi-lightbulb text-primary me-2 fs-5"></i>
                            <div>
                                <small class="text-muted">
                                    For best results, upload a square image. Your profile picture will be displayed as a circle throughout the site.
                                </small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x me-1"></i>Cancel
                </button>
                <button type="submit" form="updateProfilePictureForm" class="btn btn-primary">
                    <i class="bi bi-cloud-upload me-1"></i>Upload Image
                </button>
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
                    <i class="bi bi-shield-lock me-2"></i>Change Admin Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info d-flex align-items-center mb-4">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <div>
                        Maintain strong security by using a complex password that you don't use elsewhere.
                    </div>
                </div>
                
                <form id="changePasswordForm">
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label fw-bold">Current Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                            <input type="password" class="form-control" id="currentPassword" name="currentPassword" placeholder="Enter your current password">
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="currentPassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="mb-3">
                        <label for="newPassword" class="form-label fw-bold">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="newPassword" name="newPassword" placeholder="Enter your new password">
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="newPassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="form-text">
                            Password must be at least 8 characters with uppercase, lowercase, number and special character.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label fw-bold">Confirm New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm your new password">
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirmPassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x me-1"></i>Cancel
                </button>
                <button type="submit" form="changePasswordForm" class="btn btn-primary">
                    <i class="bi bi-shield-check me-1"></i>Change Password
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="/assets/js/utility/toast-notifications.js"></script>
<script src="/assets/js/utility/form-handler.js"></script>
<script src="/assets/js/utility/ImageFormHandler.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Form submission handlers
        handleFormSubmission('updateProfileForm', '/admin/admin-profile/update-profile-info', true);
        handleFormSubmission('changePasswordForm', '/admin/admin-profile/change-password');
        
        // Image upload handler
        handleImageUpload(
            'updateProfilePictureForm',                // Form ID
            'profileImage',                            // File input ID
            'profileImagePreview',                     // Image preview ID
            '/admin/admin-profile/update-profile-pic',   // Endpoint
            {
                modalId: 'editProfilePictureModal',     // Modal ID to close after success
                reloadPage: true,                       // Reload the page after successful upload
                reloadDelay: 1500,                      // Delay before reloading (ms)
                loadingText: 'Updating profile...',     // Custom loading text
                
                // Optional custom success handler
                onSuccess: function(data) {
                    // You can add additional behavior here if needed
                    console.log('Profile picture updated successfully!');
                    
                    // Example: Update session data
                    if (data.data && data.data.profile_url) {
                        sessionStorage.setItem('profile_url', data.data.profile_url);
                    }
                }
            }
        );

        // Handle clearing the file input
    const clearFileBtn = document.getElementById('clearFileBtn');
    if (clearFileBtn) {
        clearFileBtn.addEventListener('click', function() {
            const fileInput = document.getElementById('profileImage');
            fileInput.value = '';
            
            // Reset the image preview back to the current profile picture
            const imagePreview = document.getElementById('profileImagePreview');
            imagePreview.src = '<?= Session::get('profile_url') ?>';
        });
    }
});

const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.innerHTML = '<i class="bi bi-eye-slash"></i>';
            } else {
                passwordInput.type = 'password';
                this.innerHTML = '<i class="bi bi-eye"></i>';
            }
        });
    });
    
    // Handle delete account confirmation checkbox
    const confirmDeleteCheck = document.getElementById('confirmDeleteCheck');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    
    if (confirmDeleteCheck && confirmDeleteBtn) {
        confirmDeleteCheck.addEventListener('change', function() {
            confirmDeleteBtn.disabled = !this.checked;
        });
    }
    </script>

    <?php
// Include footer
include $footerPath;
?>