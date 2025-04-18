<?php
// Include header
include $headerPath;
?>

<div class="container py-4">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <img src="https://via.placeholder.com/150" class="rounded-circle img-fluid mx-auto d-block" alt="Profile Picture" style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    <h5 class="card-title">John Doe</h5>
                    <p class="text-muted">Member since: January 15, 2025</p>
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
                            <h6 class="mb-0">Full Name</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            John Doe
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Email</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            johndoe@example.com
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Phone</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            (123) 456-7890
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Reading Preferences</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            <span class="badge bg-secondary me-1">Fantasy</span>
                            <span class="badge bg-secondary me-1">Science Fiction</span>
                            <span class="badge bg-secondary me-1">Mystery</span>
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
                    <a href="#" class="btn btn-outline-primary mb-2 ms-2" data-bs-toggle="modal" data-bs-target="#notificationSettingsModal">
                        <i class="bi bi-bell"></i> Notification Settings
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
                <form>
                    <div class="mb-3">
                        <label for="fullName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullName" value="John Doe">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" value="johndoe@example.com">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="phone" value="(123) 456-7890">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reading Preferences</label>
                        <div class="d-flex flex-wrap gap-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="genre1" checked>
                                <label class="form-check-label" for="genre1">
                                    Fantasy
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="genre2" checked>
                                <label class="form-check-label" for="genre2">
                                    Science Fiction
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="genre3" checked>
                                <label class="form-check-label" for="genre3">
                                    Mystery
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="genre4">
                                <label class="form-check-label" for="genre4">
                                    Romance
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="genre5">
                                <label class="form-check-label" for="genre5">
                                    Biography
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Save Changes</button>
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
                <form>
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="currentPassword">
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="newPassword">
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirmPassword">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Change Password</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Picture Modal -->
<div class="modal fade" id="editProfilePictureModal" tabindex="-1" aria-labelledby="editProfilePictureModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfilePictureModalLabel">Change Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3 text-center">
                        <img src="https://via.placeholder.com/150" class="rounded-circle mb-3" alt="Profile Picture" style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    <div class="mb-3">
                        <label for="profilePicture" class="form-label">Upload New Picture</label>
                        <input class="form-control" type="file" id="profilePicture">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Upload</button>
            </div>
        </div>
    </div>
</div>

<!-- Notification Settings Modal -->
<div class="modal fade" id="notificationSettingsModal" tabindex="-1" aria-labelledby="notificationSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationSettingsModalLabel">Notification Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                            <label class="form-check-label" for="emailNotifications">Email Notifications</label>
                        </div>
                        <div class="form-text text-muted mb-3">Receive notifications about new books, promotions, and reading reminders via email.</div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="readingReminders" checked>
                            <label class="form-check-label" for="readingReminders">Reading Reminders</label>
                        </div>
                        <div class="form-text text-muted mb-3">Get reminders to continue your reading sessions.</div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="newBookNotifications" checked>
                            <label class="form-check-label" for="newBookNotifications">New Book Notifications</label>
                        </div>
                        <div class="form-text text-muted mb-3">Be notified when new books are added to our collection.</div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="promotionNotifications">
                            <label class="form-check-label" for="promotionNotifications">Promotional Notifications</label>
                        </div>
                        <div class="form-text text-muted">Receive updates about promotions, discounts, and special offers.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Save Settings</button>
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
                <form>
                    <div class="mb-3">
                        <label for="confirmDeletePassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="confirmDeletePassword">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger">Delete Account</button>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include $footerPath;
?>