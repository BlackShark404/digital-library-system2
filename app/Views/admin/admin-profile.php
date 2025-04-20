<?php
// Include header

use Core\Session;

include $headerPath;
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <img src=<?= Session::get("profile_url") ?> class="rounded-circle img-fluid mx-auto d-block" alt="Profile Picture" style="width: 130px; height: 130px; object-fit: cover;">
                    </div>
                    <h5 class="card-title"><?= Session::get("full_name") ?></h5>
                    <p class="text-muted">Administrator</p>
                    <div class="badge bg-danger mb-2">Admin Access</div>
                    <div>
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProfilePictureModal">
                            <i class="bi bi-camera"></i> Change Picture
                        </button>
                    </div>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Total Users
                        <span class="badge bg-primary rounded-pill">1,245</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Total Books
                        <span class="badge bg-primary rounded-pill">3,782</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Active Sessions
                        <span class="badge bg-primary rounded-pill">87</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Today's Purchases
                        <span class="badge bg-primary rounded-pill">32</span>
                    </li>
                </ul>
            </div>

            <!-- Admin Navigation -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Admin Navigation</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php
                    $admin_links = [
                        ['title' => 'Dashboard', 'icon' => 'tachometer-alt', 'url' => '/admin/dashboard'],
                        ['title' => 'User Management', 'icon' => 'users', 'url' => '/admin/user-management'],
                        ['title' => 'Book Management', 'icon' => 'book', 'url' => '/admin/book-management'],
                        ['title' => 'Reading Sessions', 'icon' => 'history', 'url' => '/admin/reading-sessions'],
                        ['title' => 'Purchases', 'icon' => 'shopping-cart', 'url' => '/admin/purchases'],
                        ['title' => 'Activity Log', 'icon' => 'clipboard-list', 'url' => '/admin/activity-logs'],
                        ['title' => 'Logout', 'icon' => 'sign-out-alt', 'url' => '/logout']
                    ];
                    
                    foreach ($admin_links as $link): ?>
                        <a href="<?= $link['url'] ?>" class="list-group-item list-group-item-action">
                            <i class="fas fa-<?= $link['icon'] ?> me-2"></i> <?= $link['title'] ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <!-- Admin Overview -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Admin Dashboard Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-info text-white h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">New Users</h6>
                                            <h2 class="mb-0">24</h2>
                                        </div>
                                        <i class="fas fa-user-plus fa-2x"></i>
                                    </div>
                                    <p class="small mb-0">+12% from last week</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-success text-white h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">Revenue</h6>
                                            <h2 class="mb-0">$3.2k</h2>
                                        </div>
                                        <i class="fas fa-dollar-sign fa-2x"></i>
                                    </div>
                                    <p class="small mb-0">+8% from last week</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-warning text-dark h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">New Books</h6>
                                            <h2 class="mb-0">56</h2>
                                        </div>
                                        <i class="fas fa-book fa-2x"></i>
                                    </div>
                                    <p class="small mb-0">+15% from last week</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-danger text-white h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">Support</h6>
                                            <h2 class="mb-0">8</h2>
                                        </div>
                                        <i class="fas fa-ticket-alt fa-2x"></i>
                                    </div>
                                    <p class="small mb-0">Pending tickets</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
                            (123) 456-7890
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Role</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            <span class="badge bg-danger">Administrator</span>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Last Login</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            <?= date('F j, Y, g:i a') ?>
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
                    <a href="#" class="btn btn-outline-secondary mb-2" data-bs-toggle="modal" data-bs-target="#twoFactorModal">
                        <i class="bi bi-shield-lock"></i> Two-Factor Authentication
                    </a>
                    <a href="#" class="btn btn-outline-info mb-2">
                        <i class="bi bi-bell"></i> Notification Settings
                    </a>
                    
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">API Access</h6>
                            <p class="text-muted small">Manage your API keys and permissions</p>
                        </div>
                        <button class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#apiAccessModal">
                            <i class="bi bi-code-slash"></i> Manage API Keys
                        </button>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Recent System Activity</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">New User Registration</h6>
                                <small>10 minutes ago</small>
                            </div>
                            <p class="mb-1">User 'sarah.johnson@example.com' registered a new account</p>
                            <small class="text-muted">IP: 192.168.1.45</small>
                        </div>
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Book Inventory Update</h6>
                                <small>2 hours ago</small>
                            </div>
                            <p class="mb-1">15 new books added to the inventory</p>
                            <small class="text-muted">By admin: jessica.smith@example.com</small>
                        </div>
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Payment Processed</h6>
                                <small>4 hours ago</small>
                            </div>
                            <p class="mb-1">Payment of $129.99 processed for order #39284</p>
                            <small class="text-muted">Transaction ID: TXN-583921</small>
                        </div>
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">System Maintenance</h6>
                                <small>Yesterday</small>
                            </div>
                            <p class="mb-1">Database backup completed successfully</p>
                            <small class="text-muted">Size: 2.4 GB</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="/admin/activity-logs" class="btn btn-link">View All Activity</a>
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
                        <input type="text" class="form-control" id="fullName" value="<?= Session::get('full_name') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" value="<?= Session::get('user_email') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="phone" value="(123) 456-7890">
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
                        <img src=<?= Session::get("profile_url") ?> class="rounded-circle mb-3" alt="Profile Picture" style="width: 150px; height: 150px; object-fit: cover;">
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

<!-- Two Factor Authentication Modal -->
<div class="modal fade" id="twoFactorModal" tabindex="-1" aria-labelledby="twoFactorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="twoFactorModalLabel">Two-Factor Authentication</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Two-factor authentication adds an extra layer of security to your account
                    </div>
                    <div class="mb-4">
                        <img src="/api/placeholder/200/200" alt="QR Code" class="img-fluid border p-2">
                    </div>
                    <p>Scan this QR code with your authenticator app</p>
                    <p class="text-muted">Or enter this code manually: <strong>ABCD EFGH IJKL MNOP</strong></p>
                </div>
                <form>
                    <div class="mb-3">
                        <label for="verificationCode" class="form-label">Verification Code</label>
                        <input type="text" class="form-control" id="verificationCode" placeholder="Enter 6-digit code">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success">Enable 2FA</button>
            </div>
        </div>
    </div>
</div>

<!-- API Access Modal -->
<div class="modal fade" id="apiAccessModal" tabindex="-1" aria-labelledby="apiAccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="apiAccessModalLabel">API Access Management</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    API keys grant access to your account. Never share your keys with others.
                </div>
                
                <h6>Active API Keys</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Key</th>
                                <th>Created</th>
                                <th>Last Used</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Development Key</td>
                                <td>
                                    <div class="input-group">
                                        <input type="password" class="form-control" value="api_key_378462184628364" readonly>
                                        <button class="btn btn-outline-secondary" type="button">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>Apr 10, 2025</td>
                                <td>Today</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger">Revoke</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Production Key</td>
                                <td>
                                    <div class="input-group">
                                        <input type="password" class="form-control" value="api_key_123456789012345" readonly>
                                        <button class="btn btn-outline-secondary" type="button">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>Mar 15, 2025</td>
                                <td>Yesterday</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger">Revoke</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    <h6>Generate New API Key</h6>
                    <form>
                        <div class="mb-3">
                            <label for="keyName" class="form-label">Key Name</label>
                            <input type="text" class="form-control" id="keyName" placeholder="e.g., Development, Test, Production">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Permissions</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="permRead" value="read" checked>
                                    <label class="form-check-label" for="permRead">Read</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="permWrite" value="write">
                                    <label class="form-check-label" for="permWrite">Write</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="permDelete" value="delete">
                                    <label class="form-check-label" for="permDelete">Delete</label>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary">Generate Key</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include $footerPath;
?>