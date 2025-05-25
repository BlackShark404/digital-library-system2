<?php

include $headerPath;

?>

<div class="container">
    <h1 class="mb-4 text-primary"><i class="bi bi-people me-2"></i>User Management</h1>


    <div class="col-md-3 ms-auto">
        <button class="btn btn-primary w-100 d-flex align-items-center justify-content-center shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal" type="button">
            <i class="fas fa-plus-circle me-2"></i> Add User
        </button>
    </div>
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
                            <th scope="col">User</th>
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
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label for="addFname" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="addFname" name="first_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="addLname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="addLname" name="last_name" required>
                        </div>
                    </div>

                    <div class="mb-3 mt-2">
                        <label for="addEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="addEmail" name="email" required>
                    </div>

                    <!-- Password field (Full width) -->
                    <div class="mb-3">
                        <label for="addPassword" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="addPassword" name="password" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword" data-target="password">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password field (Full width) -->
                    <div class="mb-3">
                        <label for="addConfirmPassword" class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="addConfirmPassword" name="confirm_password" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword" data-target="confirmPassword">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row g-2 mt-2">
                        <div class="col-md-6">
                            <label for="addRole" class="form-label">Role</label>
                            <select class="form-select" id="addRole" name="role_id" required>
                                <option value="">Select Role</option>
                                <option value="1" selected>User</option>
                                <option value="2">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="addStatus" class="form-label">Status</label>
                            <select class="form-select" id="addStatus" name="is_active" required>
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
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
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle me-2"></i> For security reasons, administrators can only modify the user's status (Active/Inactive).
                </div>
                <form id="editUserForm">
                    <input type="hidden" id="editUserId" name="id">
                    <div class="row">
                        <div class="col-md-4 d-flex flex-column align-items-center">
                            <img id="editUserProfilePic" src="" alt="User Profile" class="rounded-circle img-thumbnail mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                            <div class="text-center">
                                <span id="editUserNameDisplay" class="fw-bold fs-5"></span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-user-edit me-2"></i>User Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="editFirstName" class="form-label text-muted"><i class="fas fa-user me-1"></i>First Name</label>
                                            <input type="text" class="form-control bg-light" id="editFirstName" name="first_name" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="editLastName" class="form-label text-muted"><i class="fas fa-user me-1"></i>Last Name</label>
                                            <input type="text" class="form-control bg-light" id="editLastName" name="last_name" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="editEmail" class="form-label text-muted"><i class="fas fa-envelope me-1"></i>Email</label>
                                            <input type="email" class="form-control bg-light" id="editEmail" name="email" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="editRole" class="form-label text-muted"><i class="fas fa-user-tag me-1"></i>Role</label>
                                            <select class="form-select bg-light" id="editRole" name="role_id" disabled>
                                                <option value="1">User</option>
                                                <option value="2">Admin</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label for="editStatus" class="form-label text-muted"><i class="fas fa-toggle-on me-1"></i>Status</label>
                                            <select class="form-select border-primary" id="editStatus" name="is_active" required>
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                <h5 class="modal-title">
                    <i class="fas fa-id-badge me-2"></i>User Details: 
                    <span id="viewUserUsername"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 d-flex flex-column align-items-center">
                        <img id="viewUserProfilePic" src="" alt="User Profile" class="rounded-circle img-thumbnail mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                        <div class="text-center">
                            <h5 id="viewUserName" class="fw-bold mb-1"></h5>
                            <span id="viewUserRole" class="badge bg-primary"></span>
                            <span id="viewUserStatus" class="badge ms-1"></span>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>User Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2 d-flex">
                                    <div class="text-muted me-2" style="width: 120px;"><i class="fas fa-hashtag me-1"></i>User ID:</div>
                                    <div id="viewUserId" class="fw-bold"></div>
                                </div>
                                <div class="mb-2 d-flex">
                                    <div class="text-muted me-2" style="width: 120px;"><i class="fas fa-envelope me-1"></i>Email:</div>
                                    <div id="viewUserEmail" class="fw-bold"></div>
                                </div>
                                <div class="mb-2 d-flex">
                                    <div class="text-muted me-2" style="width: 120px;"><i class="fas fa-calendar-alt me-1"></i>Registered:</div>
                                    <div id="viewUserRegistered" class="fw-bold"></div>
                                </div>
                                <div class="mb-2 d-flex">
                                    <div class="text-muted me-2" style="width: 120px;"><i class="fas fa-clock me-1"></i>Last Login:</div>
                                    <div id="viewUserLastLogin" class="fw-bold"></div>
                                </div>
                            </div>
                        </div>
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

<script src="/assets/js/utility/DataTablesManager.js"></script>

<script>
    /**
 * User Management JavaScript
 * Initializes DataTable with DataTablesManager and handles user actions
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable with DataTablesManager
    const userTableManager = new DataTablesManager('usersTable', {
        ajaxUrl: '/api/users',
        columns: [
            { data: 'id', title: 'ID' },
            { 
                data: null, 
                title: 'User',
                render: function(data, type, row) {
                    const profileUrl = row.profile_url || '/assets/images/default-avatar.png';
                    return `<div class="d-flex align-items-center">
                                <img src="${profileUrl}" alt="Profile" class="rounded-circle me-2" width="40" height="40">
                                <div>${row.first_name} ${row.last_name}</div>
                            </div>`;
                }
            },
            { data: 'email', title: 'Email' },
            { 
                data: 'role', 
                title: 'Role',
                badge: {
                    type: 'primary',
                    pill: true,
                    valueMap: {
                        'admin': {
                            type: 'danger',
                            display: 'Admin'
                        },
                        'user': {
                            type: 'info',
                            display: 'User'
                        }
                    }
                }
            },
            { 
                data: 'status', 
                title: 'Status',
                badge: {
                    type: 'secondary',
                    pill: true,
                    valueMap: {
                        'active': {
                            type: 'success',
                            display: 'Active'
                        },
                        'inactive': {
                            type: 'danger',
                            display: 'Inactive'
                        }
                    }
                }
            },
            { data: 'registered', title: 'Registered' },
            { data: 'last_login', title: 'Last Login' }
        ],
        // View user callback
        viewRowCallback: function(rowData, tableManager) {
            // Populate the view modal with user data
            $('#viewUserId').text(rowData.id);
            $('#viewUserName').text(rowData.first_name + ' ' + rowData.last_name);
            $('#viewUserUsername').text(rowData.first_name + ' ' + rowData.last_name);
            $('#viewUserEmail').text(rowData.email);
            
            // Set role badge
            const roleBadgeClass = rowData.role === 'admin' ? 'bg-danger' : 'bg-info';
            $('#viewUserRole').attr('class', `badge ${roleBadgeClass}`).text(rowData.role);
            
            // Set status badge
            const statusBadgeClass = rowData.status === 'active' ? 'bg-success' : 'bg-danger';
            $('#viewUserStatus').attr('class', `badge ${statusBadgeClass} ms-1`).text(rowData.status);
            
            $('#viewUserRegistered').text(rowData.registered);
            $('#viewUserLastLogin').text(rowData.last_login || 'Never');
            
            // Set profile picture
            const profileUrl = rowData.profile_url || '/assets/images/default-avatar.png';
            $('#viewUserProfilePic').attr('src', profileUrl);
            
            // Set sample activity statistics
            $('#viewUserLogins').text(rowData.logins || '0');
            $('#viewUserPurchases').text(rowData.purchases || '0');
            $('#viewUserSessions').text(rowData.sessions || '0');
            $('#viewUserHours').text(rowData.hours || '0');
            $('#viewUserComments').text(rowData.comments || '0');
            $('#viewUserRatings').text(rowData.ratings || '0');
            
            // Show the modal
            const viewModal = new bootstrap.Modal(document.getElementById('viewUserModal'));
            viewModal.show();
            
            // Setup edit button in view modal
            $('#editUserBtn').off('click').on('click', function() {
                // Hide view modal
                viewModal.hide();
                
                // Setup and show edit modal
                setupEditUserModal(rowData);
            });
        },
        
        // Edit user callback
        editRowCallback: function(rowData, tableManager) {
            setupEditUserModal(rowData);
        },
        
        
    });
    
    // Setup filter change listeners
    $('#roleFilter, #statusFilter').on('change', function() {
        applyFilters();
    });
    
    // Function to apply table filters
    function applyFilters() {
        const roleFilter = $('#roleFilter').val();
        const statusFilter = $('#statusFilter').val();
        
        const filters = {};
        if (roleFilter) filters.role = roleFilter;
        if (statusFilter) filters.status = statusFilter;
        
        userTableManager.applyFilters(filters);
    }
    
    // Handle form submission for adding users
    $('#addUserForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate passwords match
        const password = $('#addPassword').val();
        const confirmPassword = $('#addConfirmPassword').val();
        
        if (password !== confirmPassword) {
            userTableManager.showErrorToast('Validation Error', 'Passwords do not match');
            return false;
        }
        
        // Get form data
        const formData = {
            first_name: $('#addFname').val(),
            last_name: $('#addLname').val(),
            email: $('#addEmail').val(),
            password: password,
            role_id: $('#addRole').val(),
            is_active: $('#addStatus').val()
        };
        
        // Submit form via AJAX
        $.ajax({
            url: '/api/users',
            method: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            success: function(response) {
                if (response.success) {
                    // Close modal
                    const addModal = bootstrap.Modal.getInstance(document.getElementById('addUserModal'));
                    addModal.hide();
                    
                    // Reset form
                    $('#addUserForm')[0].reset();
                    
                    // Refresh table
                    userTableManager.refresh();
                    
                    // Show success message
                    userTableManager.showSuccessToast('User Added', response.message);
                } else {
                    userTableManager.showErrorToast('Error', response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON || { message: 'Server error' };
                userTableManager.showErrorToast('Error', response.message);
            }
        });
    });
    
    // Handle form submission for editing users
    $('#editUserForm').on('submit', function(e) {
        e.preventDefault();
        
        // Get form data - Only include status field
        const userId = $('#editUserId').val();
        const formData = {
            is_active: $('#editStatus').val()
        };
        
        // Submit form via AJAX
        $.ajax({
            url: `/api/users/${userId}`,
            method: 'PUT',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            success: function(response) {
                if (response.success) {
                    // Close modal
                    const editModal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
                    editModal.hide();
                    
                    // Refresh table
                    userTableManager.refresh();
                    
                    // Show success message
                    userTableManager.showSuccessToast('User Status Updated', response.message);
                } else {
                    userTableManager.showErrorToast('Error', response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON || { message: 'Server error' };
                userTableManager.showErrorToast('Error', response.message);
            }
        });
    });
    
    // Function to set up the edit user modal
    function setupEditUserModal(rowData) {
        // Populate edit form
        $('#editUserId').val(rowData.id);
        $('#editFirstName').val(rowData.first_name);
        $('#editLastName').val(rowData.last_name);
        $('#editEmail').val(rowData.email);
        $('#editUserNameDisplay').text(rowData.first_name + ' ' + rowData.last_name);
        
        // Set dropdown values
        const roleId = rowData.role === 'admin' ? 2 : 1;
        const status = rowData.status === 'active' ? 1 : 0;
        
        $('#editRole').val(roleId);
        $('#editStatus').val(status);
        
        // Set profile picture
        const profileUrl = rowData.profile_url || '/assets/images/default-avatar.png';
        $('#editUserProfilePic').attr('src', profileUrl);
        
        // Show the modal
        const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
        editModal.show();
    }
    
    // Toggle password visibility for add form
    $('#togglePassword, #toggleConfirmPassword').on('click', function() {
        const targetId = $(this).data('target') === 'password' ? 'addPassword' : 'addConfirmPassword';
        const passwordField = $('#' + targetId);
        const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
        
        // Toggle input type
        passwordField.attr('type', type);
        
        // Toggle icon
        const icon = $(this).find('i');
        icon.toggleClass('fa-eye-slash fa-eye');
    });
    
    // Handle confirm delete checkbox
    $('#confirmDelete').on('change', function() {
        $('#deleteUserBtn').prop('disabled', !$(this).is(':checked'));
    });
    
});
</script>


<?php
include $footerPath;
?>