<?php
// Include header

use Core\Session;

include $headerPath;
?>

<div class="container py-5">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-person-circle me-2"></i>My Profile</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Profile</li>
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
                                style="width: 150px; height: 150px; object-fit: cover;" alt="Profile Picture">
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
                        <p class="text-muted small mb-3">
                            <i class="bi bi-calendar3 me-1"></i>Member since: <?= Session::get('member_since') ?>
                        </p>
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3" 
                            data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="bi bi-pencil me-1"></i>Edit Profile
                        </button>
                    </div>
                </div>
                
                <hr class="my-0">
                
                <!-- Reading Stats -->
                <div class="card-body p-4">
                    <h5 class="card-title d-flex align-items-center mb-3">
                        <i class="bi bi-bar-chart-line me-2 text-primary"></i>
                        Reading Statistics
                    </h5>
                    
                    <div class="row row-cols-2 g-3">
                        <div class="col">
                            <div class="bg-light rounded-3 p-3 h-100">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box me-3 bg-primary bg-opacity-10 text-primary rounded-circle p-2">
                                        <i class="bi bi-book"></i>
                                    </div>
                                    <div>
                                        <h2 class="mb-0 fs-4 fw-bold">24</h2>
                                        <span class="small text-muted">Books Read</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col">
                            <div class="bg-light rounded-3 p-3 h-100">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box me-3 bg-success bg-opacity-10 text-success rounded-circle p-2">
                                        <i class="bi bi-clock"></i>
                                    </div>
                                    <div>
                                        <h2 class="mb-0 fs-4 fw-bold">156</h2>
                                        <span class="small text-muted">Reading Hours</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col">
                            <div class="bg-light rounded-3 p-3 h-100">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box me-3 bg-warning bg-opacity-10 text-warning rounded-circle p-2">
                                        <i class="bi bi-bookmark-heart"></i>
                                    </div>
                                    <div>
                                        <h2 class="mb-0 fs-4 fw-bold">12</h2>
                                        <span class="small text-muted">Wishlist</span>
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
                                        <h2 class="mb-0 fs-4 fw-bold">17</h2>
                                        <span class="small text-muted">Purchases</span>
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
                                <label class="text-muted small mb-1">Member Since</label>
                                <h6 class="mb-0"><?= Session::get('member_since') ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Reading Activity Card -->
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center border-0">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-book-half me-2 text-success"></i>Recent Reading Activity
                    </h5>
                    <a href="reading_session.php" class="btn btn-sm btn-outline-success rounded-pill">
                        <i class="bi bi-clock-history me-1"></i>View All
                    </a>
                </div>
                <div class="card-body p-4">
                    <!-- Reading Item 1 -->
                    <div class="reading-activity-item mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <div class="book-cover me-3 rounded shadow-sm" style="width: 60px; height: 90px; background-color: #f8f9fa; display: flex; justify-content: center; align-items: center;">
                                <i class="bi bi-book text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 fw-bold">The Great Gatsby</h6>
                                    <span class="badge bg-light text-dark">3 days ago</span>
                                </div>
                                <p class="text-muted small mb-2">Read for 45 minutes · Page 78 of 180</p>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 43%;" 
                                        aria-valuenow="43" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Reading Item 2 -->
                    <div class="reading-activity-item mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <div class="book-cover me-3 rounded shadow-sm" style="width: 60px; height: 90px; background-color: #f8f9fa; display: flex; justify-content: center; align-items: center;">
                                <i class="bi bi-book text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 fw-bold">To Kill a Mockingbird</h6>
                                    <span class="badge bg-light text-dark">1 week ago</span>
                                </div>
                                <p class="text-muted small mb-2">Read for 30 minutes · Page 156 of 281</p>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 55%;" 
                                        aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Reading Item 3 -->
                    <div class="reading-activity-item">
                        <div class="d-flex align-items-center mb-2">
                            <div class="book-cover me-3 rounded shadow-sm" style="width: 60px; height: 90px; background-color: #f8f9fa; display: flex; justify-content: center; align-items: center;">
                                <i class="bi bi-book text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 fw-bold">1984</h6>
                                    <span class="badge bg-light text-dark">2 weeks ago</span>
                                </div>
                                <p class="text-muted small mb-2">Read for 60 minutes · Page 203 of 328</p>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 62%;" 
                                        aria-valuenow="62" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 text-center">
                    <a href="reading_session.php" class="btn btn-link text-success">View all reading sessions <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
            
            <!-- Reading Goals Card -->
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-light d-flex justify-content-between align-items-center border-0">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-trophy me-2 text-warning"></i>Reading Goals
                    </h5>
                    <button class="btn btn-sm btn-outline-warning rounded-pill">
                        <i class="bi bi-plus me-1"></i>Add Goal
                    </button>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Monthly Goal -->
                        <div class="col-md-6">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title d-flex align-items-center mb-3">
                                        <i class="bi bi-calendar-month me-2 text-primary"></i>Monthly Goal
                                    </h6>
                                    <div class="text-center mb-3">
                                        <div class="d-inline-block position-relative">
                                            <svg width="120" height="120" viewBox="0 0 120 120">
                                                <circle cx="60" cy="60" r="54" fill="none" stroke="#e9ecef" stroke-width="12"/>
                                                <circle cx="60" cy="60" r="54" fill="none" stroke="#0d6efd" stroke-width="12"
                                                    stroke-dasharray="339.3" stroke-dashoffset="152.7" transform="rotate(-90 60 60)"/>
                                            </svg>
                                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                                <h3 class="mb-0 fw-bold">4/6</h3>
                                                <small class="text-muted">Books</small>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-center mb-0">2 more books to reach your monthly goal!</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Yearly Goal -->
                        <div class="col-md-6">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title d-flex align-items-center mb-3">
                                        <i class="bi bi-calendar-check me-2 text-success"></i>Yearly Goal
                                    </h6>
                                    <div class="text-center mb-3">
                                        <div class="d-inline-block position-relative">
                                            <svg width="120" height="120" viewBox="0 0 120 120">
                                                <circle cx="60" cy="60" r="54" fill="none" stroke="#e9ecef" stroke-width="12"/>
                                                <circle cx="60" cy="60" r="54" fill="none" stroke="#198754" stroke-width="12"
                                                    stroke-dasharray="339.3" stroke-dashoffset="237.5" transform="rotate(-90 60 60)"/>
                                            </svg>
                                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                                <h3 class="mb-0 fw-bold">17/40</h3>
                                                <small class="text-muted">Books</small>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-center mb-0">You're 43% of the way to your yearly goal!</p>
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
                    <i class="bi bi-person-lines-fill me-2"></i>Edit Profile
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
                            We'll use this number only for important account notifications.
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
                    <i class="bi bi-person-badge me-2"></i>Update Profile Picture
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
                    <i class="bi bi-shield-lock me-2"></i>Change Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info d-flex align-items-center mb-4">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <div>
                        For your security, please enter your current password before setting a new one.
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

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteAccountModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Delete Account
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger border-danger mb-4">
                    <div class="d-flex">
                        <div class="me-3 fs-2 text-danger">
                            <i class="bi bi-exclamation-diamond-fill"></i>
                        </div>
                        <div>
                            <h5 class="alert-heading">Warning: Permanent Action</h5>
                            <p class="mb-0">This action cannot be undone. All your data will be permanently deleted, including:</p>
                            <ul class="mb-0 mt-2">
                                <li>Reading history and progress</li>
                                <li>Personal information and settings</li>
                                <li>Wishlists and purchases</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <p class="fw-bold">Are you absolutely sure you want to delete your account?</p>
                    <p>Please enter your password to confirm account deletion:</p>
                </div>
                
                <form id="deleteAccountForm">
                    <div class="mb-3">
                        <label for="confirmDeletePassword" class="form-label fw-bold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                            <input type="password" class="form-control" id="confirmDeletePassword" name="password" placeholder="Enter your password">
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirmDeletePassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" id="confirmDeleteCheck" required>
                        <label class="form-check-label" for="confirmDeleteCheck">
                            I understand that this action is permanent and cannot be reversed.
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-arrow-left me-1"></i>Cancel
                </button>
                <button type="submit" form="deleteAccountForm" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                    <i class="bi bi-trash me-1"></i>Delete Account
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
        // Add to the existing script at the bottom of user-profile.php
        document.addEventListener("DOMContentLoaded", function () {
            // Remove the generic form submission handler for profile picture
            // Comment out or remove this line:
            // handleFormSubmission('updateProfilePictureForm', '/user/user-profile/update-profile-pic', false);

            // Keep the other form handlers
            handleFormSubmission('updateProfileForm', '/user/user-profile/update-profile-info', true);
            handleFormSubmission('deleteAccountForm', '/user/user-profile/delete-account');
            handleFormSubmission('changePasswordForm', '/user/user-profile/change-password');
            
            // Update the handleImageUpload call in user-profile.php
            handleImageUpload(
                'updateProfilePictureForm',                // Form ID
                'profileImage',                            // File input ID
                'profileImagePreview',                     // Image preview ID
                '/user/user-profile/update-profile-pic',   // Endpoint
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
            });

            // Add this to your existing script section
document.addEventListener("DOMContentLoaded", function () {
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