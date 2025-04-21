<?php
// Include header

use Core\Session;

include $headerPath;
?>

<div class="container py-4">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <img src=<?= Session::get("profile_url") ?> class="rounded-circle img-fluid mx-auto d-block" alt="Profile Picture" style="width: 130px; height: 130px; object-fit: cover;">
                    </div>
                    <h5 class="card-title"><?= Session::get('full_name') ?></h5>
                    <p class="text-muted">Member since: <?= Session::get('member_since') ?></p>
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProfilePictureModal">
                        <i class="bi bi-camera"></i> Change Picture
                    </button>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Books Read
                        <span class="badge bg-primary rounded-pill">24</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Reading Hours
                        <span class="badge bg-primary rounded-pill">156</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Wishlist Items
                        <span class="badge bg-primary rounded-pill">12</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Purchases
                        <span class="badge bg-primary rounded-pill">17</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Personal Information</h5>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Name</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            <?= Session::get("full_name") ?>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Email</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            <?= Session::get("user_email") ?>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Phone</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            <?= Session::get('phone_number') ?? 'No phone number' ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Account Settings</h5>
                </div>
                <div class="card-body">
                    <a href="#" class="btn btn-outline-primary mb-2" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                        <i class="bi bi-key"></i> Change Password
                    </a>
                    
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Delete Account</h6>
                            <p class="text-muted small">Once you delete your account, there is no going back.</p>
                        </div>
                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                            <i class="bi bi-trash"></i> Delete Account
                        </button>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Recent Reading Activity</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <div class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">The Great Gatsby</h6>
                                <small>3 days ago</small>
                            </div>
                            <p class="mb-1">Read for 45 minutes · Page 78 of 180</p>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar" role="progressbar" style="width: 43%;" aria-valuenow="43" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">To Kill a Mockingbird</h6>
                                <small>1 week ago</small>
                            </div>
                            <p class="mb-1">Read for 30 minutes · Page 156 of 281</p>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar" role="progressbar" style="width: 55%;" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">1984</h6>
                                <small>2 weeks ago</small>
                            </div>
                            <p class="mb-1">Read for 60 minutes · Page 203 of 328</p>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar" role="progressbar" style="width: 62%;" aria-valuenow="62" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="reading_session.php" class="btn btn-link">View All Reading Sessions</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateProfileForm">
                    <div class="mb-3">
                        <label for="firstName" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstName" name="firstName" value="<?= Session::get('first_name') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="lastName" name="lastName" value="<?= Session::get('last_name') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="<?= Session::get('phone_number') ?? '' ?>">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="updateProfileForm" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Picture Modal -->
<div class="modal fade" id="editProfilePictureModal" tabindex="-1" aria-labelledby="editProfilePictureModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfilePictureModalLabel">Update Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateProfilePictureForm" enctype="multipart/form-data">
                    <div class="text-center mb-3">
                        <img id="profileImagePreview" src="<?= Session::get('profile_url') ?>" class="rounded-circle img-fluid mx-auto d-block" alt="Profile Picture Preview" style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    <div class="mb-3">
                        <label for="profileImage" class="form-label">Choose New Profile Picture</label>
                        <input type="file" class="form-control" id="profileImage" name="profile_image" accept="image/jpeg,image/png,image/gif,image/webp">
                        <div class="form-text">
                            Supported formats: JPEG, PNG, GIF, WebP. Maximum size: 5MB.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="updateProfilePictureForm" class="btn btn-primary">Upload Image</button>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm">
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="currentPassword" name="currentPassword">
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="newPassword" name="newPassword">
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="changePasswordForm" class="btn btn-primary">Change Password</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteAccountModalLabel">Delete Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    This action cannot be undone. All your data will be permanently deleted.
                </div>
                <p>Please enter your password to confirm account deletion:</p>
                <form id="deleteAccountForm">
                    <div class="mb-3">
                        <label for="confirmDeletePassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="confirmDeletePassword" name="password">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="deleteAccountForm" class="btn btn-danger">Delete Account</button>
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
            
            handleImageUpload(
                    'updateProfilePictureForm',                // Form ID
                    'profileImage',                            // File input ID
                    'profileImagePreview',                     // Image preview ID
                    '/user/user-profile/update-profile-pic',   // Endpoint
                    {
                        modalId: 'editProfilePictureModal',     // Modal ID to close after success
                        reloadPage: true,                       // Reload the page after successful upload
                        reloadDelay: 1500,                      // Delay before reloading (ms)
                        
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
    </script>

    

<?php
// Include footer
include $footerPath;
?>