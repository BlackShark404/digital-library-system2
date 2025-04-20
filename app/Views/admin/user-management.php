<?php
use Core\Session;

include $headerPath;

// Access the provided data
$users = $viewData['users'] ?? [];
$pagination = $viewData['pagination'] ?? [];
$filters = $viewData['filters'] ?? [];
?>

<div class="container">
    <h1 class="mb-4"><i class="bi bi-people me-2"></i>User Management</h1>

    <!-- Search and Filter Options -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="userFiltersForm">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search users..." 
                                  id="userSearch" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="roleFilter" name="role">
                            <option value="">All Roles</option>
                            <option value="admin" <?= ($filters['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="user" <?= ($filters['role'] ?? '') === 'user' ? 'selected' : '' ?>>User</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter" name="status">
                            <option value="">All Status</option>
                            <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#addUserModal" type="button">
                            <i class="fas fa-plus"></i> Add User
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Export Buttons -->
    <div class="mb-3">
        <div class="btn-group" role="group" aria-label="Export options">
            <button type="button" class="btn btn-outline-secondary" id="exportCsv">
                <i class="fas fa-file-csv me-1"></i> Export CSV
            </button>
            <button type="button" class="btn btn-outline-secondary" id="exportExcel">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </button>
            <button type="button" class="btn btn-outline-secondary" id="exportPdf">
                <i class="fas fa-file-pdf me-1"></i> Export PDF
            </button>
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

<script>
    $(document).ready(function() {
        // Define the action buttons for the DataTable
        const actions = [
            {
                name: 'edit',
                icon: '<i class="fas fa-pen"></i>',
                class: 'btn-outline-primary',
                attributes: 'title="Edit User"'
            },
            {
                name: 'view',
                icon: '<i class="fas fa-eye"></i>',
                class: 'btn-outline-info',
                attributes: 'title="View Details"'
            },
            {
                name: 'delete',
                icon: '<i class="fas fa-trash"></i>',
                class: 'btn-outline-danger',
                attributes: 'title="Delete User"'
            }
        ];

        // Column definitions for DataTable
        const columns = [
            { data: 'id', name: 'id' },
            { 
                data: null,
                name: 'name',
                render: function(data, type, row) {
                    return row.first_name + ' ' + row.last_name;
                }
            },
            { data: 'email', name: 'email' },
            { 
                data: 'role_name',
                name: 'role_name',
                render: function(data, type, row) {
                    const badgeClass = data === 'admin' ? 'bg-danger' : 'bg-primary';
                    return `<span class="badge ${badgeClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                }
            },
            { 
                data: 'is_active',
                name: 'is_active',
                render: function(data, type, row) {
                    return data == 1 ? 
                        '<span class="badge bg-success">Active</span>' : 
                        '<span class="badge bg-warning text-dark">Inactive</span>';
                }
            },
            { 
                data: 'created_at',
                name: 'created_at',
                render: function(data, type, row) {
                    return new Date(data).toISOString().split('T')[0];
                }
            },
            { 
                data: 'last_login',
                name: 'last_login',
                render: function(data, type, row) {
                    return data ? new Date(data).toISOString().replace('T', ' ').substring(0, 16) : 'Never';
                }
            },
            { 
                data: null,
                orderable: false, 
                searchable: false,
                render: DataTablesHelper.createActionColumn(actions)
            }
        ];

        // Initialize DataTable with server-side processing
        const usersTable = DataTablesHelper.initServerSide(
            'usersTable',
            '/admin/users/data',
            columns,
            {
                order: [[0, 'desc']],
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                // Add any additional DataTables options here
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    emptyTable: 'No users found',
                    zeroRecords: 'No matching users found'
                }
            }
        );

        // Handle form submission for adding new users
        DataTablesHelper.handleFormSubmit(
            'addUserForm',
            'usersTable',
            '/admin/users/create',
            function(response) {
                showToast('Success', 'User added successfully!', 'success');
            }
        );
        
        // Handle form submission for editing users
        DataTablesHelper.handleFormSubmit(
            'editUserForm',
            'usersTable',
            '/admin/users/update',
            function(response) {
                showToast('Success', 'User updated successfully!', 'success');
            }
        );
        
        // Bind action buttons events
        DataTablesHelper.bindActionEvents('usersTable', {
            edit: function(id, rowData, row) {
                // Populate edit form with user data
                $('#editUserId').val(rowData.id);
                $('#editUserName').text(rowData.first_name + ' ' + rowData.last_name);
                $('#editFirstName').val(rowData.first_name);
                $('#editLastName').val(rowData.last_name);
                $('#editEmail').val(rowData.email);
                $('#editRole').val(rowData.role_id);
                $('#editStatus').val(rowData.is_active);
                $('#editPassword').val(''); // Clear password field
                
                // Show edit modal
                $('#editUserModal').modal('show');
            },
            view: function(id, rowData, row) {
                // Populate view modal with user data
                $('#viewUserId').text(rowData.id);
                $('#viewUserUsername').text(rowData.email.split('@')[0]);
                $('#viewUserName').text(rowData.first_name + ' ' + rowData.last_name);
                $('#viewUserEmail').text(rowData.email);
                
                // Set role badge with appropriate class
                const roleBadgeClass = rowData.role_name === 'admin' ? 'bg-danger' : 'bg-primary';
                $('#viewUserRole').html(`<span class="badge ${roleBadgeClass}">${rowData.role_name.charAt(0).toUpperCase() + rowData.role_name.slice(1)}</span>`);
                
                // Set status badge with appropriate class
                const statusBadgeClass = rowData.is_active == 1 ? 'bg-success' : 'bg-warning text-dark';
                const statusText = rowData.is_active == 1 ? 'Active' : 'Inactive';
                $('#viewUserStatus').html(`<span class="badge ${statusBadgeClass}">${statusText}</span>`);
                
                // Set dates
                $('#viewUserRegistered').text(new Date(rowData.created_at).toISOString().split('T')[0]);
                $('#viewUserLastLogin').text(rowData.last_login ? 
                    new Date(rowData.last_login).toISOString().replace('T', ' ').substring(0, 16) : 'Never');
                
                // Set sample statistics (in a real app, you would fetch these from the server)
                $('#viewUserLogins').text(Math.floor(Math.random() * 200));
                $('#viewUserPurchases').text(Math.floor(Math.random() * 20));
                $('#viewUserSessions').text(Math.floor(Math.random() * 100));
                $('#viewUserHours').text((Math.random() * 200).toFixed(1));
                $('#viewUserComments').text(Math.floor(Math.random() * 50));
                $('#viewUserRatings').text(Math.floor(Math.random() * 30));
                
                // Setup edit button in view modal
                $('#editUserBtn').off('click').on('click', function() {
                    $('#viewUserModal').modal('hide');
                    // Trigger the edit action with a slight delay to allow modal transition
                    setTimeout(function() {
                        $('#usersTable').find('.edit-btn[data-id="' + id + '"]').trigger('click');
                    }, 500);
                });
                
                // Show view modal
                $('#viewUserModal').modal('show');
            },
            delete: function(id, rowData, row) {
                // Populate delete modal
                $('#deleteUserId').val(id);
                $('#deleteUserName').text(rowData.first_name + ' ' + rowData.last_name);
                $('#confirmDelete').prop('checked', false);
                $('#deleteUserBtn').prop('disabled', true);
                
                // Show delete modal
                $('#deleteUserModal').modal('show');
            }
        });
        
        // Handle delete confirmation checkbox
        $('#confirmDelete').on('change', function() {
            $('#deleteUserBtn').prop('disabled', !$(this).is(':checked'));
        });
        
        // Handle delete user button click
        $('#deleteUserBtn').on('click', function() {
            const userId = $('#deleteUserId').val();
            
            $.ajax({
                url: '/admin/users/delete',
                type: 'POST',
                data: {
                    id: userId,
                    _token: typeof csrfToken !== 'undefined' ? csrfToken : ''
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Close delete modal
                        $('#deleteUserModal').modal('hide');
                        
                        // Refresh table
                        DataTablesHelper.refreshTable('usersTable');
                        
                        // Show success message
                        showToast('Success', 'User deleted successfully!', 'success');
                    } else {
                        showToast('Error', response.message || 'Failed to delete user.', 'error');
                    }
                },
                error: function() {
                    showToast('Error', 'Server error occurred', 'error');
                }
            });
        });

        // Handle filter form submission
        $('#userFiltersForm').on('submit', function(e) {
            e.preventDefault();
            
            // Apply filters to DataTable
            usersTable.search($('#userSearch').val()).draw();
            
            // Apply custom filters
            usersTable.column(3).search($('#roleFilter').val()).draw();
            usersTable.column(4).search($('#statusFilter').val()).draw();
        });
        
        // Reset filters button
        $('#resetFilters').on('click', function() {
            $('#userFiltersForm')[0].reset();
            usersTable.search('').columns().search('').draw();
        });
        
        // Export buttons
        $('#exportCsv').on('click', function() {
            DataTablesHelper.exportData('usersTable', 'csv', 'Users_Export');
        });
        
        $('#exportExcel').on('click', function() {
            DataTablesHelper.exportData('usersTable', 'excel', 'Users_Export');
        });
        
        $('#exportPdf').on('click', function() {
            DataTablesHelper.exportData('usersTable', 'pdf', 'Users_Export');
        });
    });
</script>

<?php
include $footerPath;
?>