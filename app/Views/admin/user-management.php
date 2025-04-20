<?php

include $headerPath;

?>

<div class="container">
    <h1 class="mb-4 text-primary"><i class="bi bi-people me-2"></i>User Management</h1>

    <!-- Search and Filter Options with Export Dropdown -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-light py-3">
            <h5 class="card-title mb-0 text-secondary">
                <i class="fas fa-filter me-2"></i>Filters and Actions
            </h5>
        </div>
        <div class="card-body">
            <form id="userFiltersForm">
                <div class="row g-3 align-items-center">
                    
                    <div class="col-md-3">
                        <label for="roleFilter" class="form-label small text-muted">Role</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-user-tag text-secondary"></i></span>
                            <select class="form-select" id="roleFilter" name="role">
                                <option value="">All Roles</option>
                                <option value="admin" <?= ($filters['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="user" <?= ($filters['role'] ?? '') === 'user' ? 'selected' : '' ?>>User</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="statusFilter" class="form-label small text-muted">Status</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-toggle-on text-secondary"></i></span>
                            <select class="form-select" id="statusFilter" name="status">
                                <option value="">All Status</option>
                                <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    
                    
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Export Data</label>
                        <div class="dropdown">
                            <button class="btn btn-success w-100 d-flex align-items-center justify-content-center shadow-sm dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-file-export me-2"></i> Export Data
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end w-100 shadow-sm border-0" aria-labelledby="exportDropdown">
                                <li>
                                    <a class="dropdown-item py-2 d-flex align-items-center" href="#" id="exportCsv">
                                        <i class="fas fa-file-csv me-2 text-success"></i> Export CSV
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item py-2 d-flex align-items-center" href="#" id="exportExcel">
                                        <i class="fas fa-file-excel me-2 text-success"></i> Export Excel
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item py-2 d-flex align-items-center" href="#" id="exportPdf">
                                        <i class="fas fa-file-pdf me-2 text-danger"></i> Export PDF
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small text-muted">Add New</label>
                        <button class="btn btn-primary w-100 d-flex align-items-center justify-content-center shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal" type="button">
                            <i class="fas fa-plus-circle me-2"></i> Add User
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    

    <!-- User List Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="usersTable" class="table table-hover display nowrap w-100">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Role</th>
                            <th scope="col">Status</th>
                            <th scope="col">Registered</th>
                            <th scope="col">Last Login</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables will populate this -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2 text-muted"></i>Add New User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label for="addFname" class="form-label">First Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-user text-muted"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" id="addFname" name="first_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="addLname" class="form-label">Last Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-user text-muted"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" id="addLname" name="last_name" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 mt-2">
                        <label for="addEmail" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-envelope text-muted"></i>
                            </span>
                            <input type="email" class="form-control border-start-0" id="addEmail" name="email" required>
                        </div>
                    </div>

                    <!-- Password field (Full width) -->
                    <div class="mb-3">
                        <label for="addPassword" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-lock text-muted"></i>
                            </span>
                            <input type="password" class="form-control border-start-0" id="addPassword" name="password" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword" data-target="password">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password field (Full width) -->
                    <div class="mb-3">
                        <label for="addConfirmPassword" class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-lock text-muted"></i>
                            </span>
                            <input type="password" class="form-control border-start-0" id="addConfirmPassword" name="confirm_password" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword" data-target="confirmPassword">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row g-2 mt-2">
                        <div class="col-md-6">
                            <label for="addRole" class="form-label">Role</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-briefcase text-muted"></i>
                                </span>
                                <select class="form-select border-start-0" id="addRole" name="role_id" required>
                                    <option value="">Select Role</option>
                                    <option value="1" selected>User</option>
                                    <option value="2">Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="addStatus" class="form-label">Status</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-toggle-on text-muted"></i>
                                </span>
                                <select class="form-select border-start-0" id="addStatus" name="is_active" required>
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addUserForm" class="btn btn-primary">Add User</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal Template -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-pen me-2"></i>Edit User: <span id="editUserName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="editUserId" name="id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editFirstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="editFirstName" name="first_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editLastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="editLastName" name="last_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editPassword" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="editPassword" name="password" placeholder="Leave blank to keep current">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editRole" class="form-label">Role</label>
                            <select class="form-select" id="editRole" name="role_id" required>
                                <option value="1">User</option>
                                <option value="2">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-select" id="editStatus" name="is_active" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editUserForm" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- View User Details Modal Template -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-id-badge me-2"></i>User Details: <span id="viewUserUsername"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Basic Information</h6>
                        <p><strong>User ID:</strong> <span id="viewUserId"></span></p>
                        <p><strong>Name:</strong> <span id="viewUserName"></span></p>
                        <p><strong>Email:</strong> <span id="viewUserEmail"></span></p>
                        <p><strong>Role:</strong> <span id="viewUserRole"></span></p>
                        <p><strong>Status:</strong> <span id="viewUserStatus"></span></p>
                        <p><strong>Registered:</strong> <span id="viewUserRegistered"></span></p>
                        <p><strong>Last Login:</strong> <span id="viewUserLastLogin"></span></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Activity Statistics</h6>
                        <p><strong>Total Logins:</strong> <span id="viewUserLogins"></span></p>
                        <p><strong>Books Purchased:</strong> <span id="viewUserPurchases"></span></p>
                        <p><strong>Reading Sessions:</strong> <span id="viewUserSessions"></span></p>
                        <p><strong>Hours Read:</strong> <span id="viewUserHours"></span></p>
                        <p><strong>Comments Made:</strong> <span id="viewUserComments"></span></p>
                        <p><strong>Ratings Given:</strong> <span id="viewUserRatings"></span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editUserBtn">Edit User</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete User Confirmation Modal Template -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-triangle-exclamation me-2 text-danger"></i>Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the user <strong id="deleteUserName"></strong>?</p>
                <p class="text-danger"><strong>Warning:</strong> This action cannot be undone. All user data will be permanently removed.</p>
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" id="confirmDelete" required>
                    <label class="form-check-label" for="confirmDelete">
                        I understand the consequences of this action
                    </label>
                </div>
                <input type="hidden" id="deleteUserId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="deleteUserBtn" disabled>Delete User</button>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery first -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">

<!-- Include DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>

<!-- Include DataTablesHelper -->
<script src="/assets/js/utility/DataTablesHelper.js"></script>

<script src="/assets/js/utility/toggle-password.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="/assets/js/utility/toast-notifications.js"></script>
<script src="/assets/js/utility/form-handler.js"></script>

<?php
include $footerPath;
?>