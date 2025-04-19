<?php
$page_title = "User Management";

include $headerPath;
?>

<div class="container">
    <h1 class="mb-4"><i class="bi bi-people me-2"></i>User Management</h1>

    <!-- Search and Filter Options -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search users..." id="userSearch">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="roleFilter">
                        <option value="">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="fas fa-plus"></i> Add User
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- User List Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
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
                        <!-- Hardcoded user data -->
                        <tr>
                            <td>1</td>
                            <td>Admin User</td>
                            <td>admin@example.com</td>
                            <td><span class="badge bg-danger">Admin</span></td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>2023-01-10</td>
                            <td>2025-03-30 14:25</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUserModal1" title="Edit User">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewUserModal1" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal1" title="Delete User">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>John Doe</td>
                            <td>john@example.com</td>
                            <td><span class="badge bg-primary">User</span></td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>2023-02-15</td>
                            <td>2025-03-31 09:42</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUserModal2" title="Edit User">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewUserModal2" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal2" title="Delete User">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Jane Smith</td>
                            <td>jane@example.com</td>
                            <td><span class="badge bg-primary">User</span></td>
                            <td><span class="badge bg-warning text-dark">Inactive</span></td>
                            <td>2023-03-05</td>
                            <td>2025-03-15 17:13</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUserModal3" title="Edit User">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewUserModal3" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal3" title="Delete User">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Robert Johnson</td>
                            <td>robert@example.com</td>
                            <td><span class="badge bg-primary">User</span></td>
                            <td><span class="badge bg-danger">Suspended</span></td>
                            <td>2023-04-18</td>
                            <td>2025-02-28 11:05</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUserModal4" title="Edit User">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewUserModal4" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal4" title="Delete User">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Sarah Williams</td>
                            <td>sarah@example.com</td>
                            <td><span class="badge bg-primary">User</span></td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>2023-05-22</td>
                            <td>2025-03-30 21:47</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUserModal5" title="Edit User">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewUserModal5" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal5" title="Delete User">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center justify-content-md-end mb-0">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
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



<!-- Edit User Modal (for user ID 1) -->
<div class="modal fade" id="editUserModal1" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- Added centered & wider modal -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-pen me-2"></i>Edit User: Admin User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm1">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editFirstName1" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="editFirstName1" value="Admin" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editLastName1" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="editLastName1" value="User" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editEmail1" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail1" value="admin@example.com" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editPassword1" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="editPassword1" placeholder="Leave blank to keep current">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editRole1" class="form-label">Role</label>
                            <select class="form-select" id="editRole1" required>
                                <option value="admin" selected>Admin</option>
                                <option value="user">User</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editStatus1" class="form-label">Status</label>
                            <select class="form-select" id="editStatus1" required>
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editUserForm1" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>



<!-- View User Details Modal (for user ID 1) -->
<div class="modal fade" id="viewUserModal1" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-id-badge me-2"></i>User Details: admin_user</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Basic Information</h6>
                        <p><strong>User ID:</strong> 1</p>
                        <p><strong>Name:</strong> John Doe</p>
                        <p><strong>Email:</strong> admin@example.com</p>
                        <p><strong>Role:</strong> <span class="badge bg-danger">Admin</span></p>
                        <p><strong>Status:</strong> <span class="badge bg-success">Active</span></p>
                        <p><strong>Registered:</strong> 2023-01-10</p>
                        <p><strong>Last Login:</strong> 2025-03-30 14:25</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Activity Statistics</h6>
                        <p><strong>Total Logins:</strong> 142</p>
                        <p><strong>Books Purchased:</strong> 12</p>
                        <p><strong>Reading Sessions:</strong> 86</p>
                        <p><strong>Hours Read:</strong> 134.5</p>
                        <p><strong>Comments Made:</strong> 27</p>
                        <p><strong>Ratings Given:</strong> 15</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editUserModal1">Edit User</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete User Confirmation Modal (for user ID 1) -->
<div class="modal fade" id="deleteUserModal1" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-triangle-exclamation me-2 text-danger"></i>Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the user <strong>admin_user</strong>?</p>
                <p class="text-danger"><strong>Warning:</strong> This action cannot be undone. All user data will be permanently removed.</p>
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" id="confirmDelete1" required>
                    <label class="form-check-label" for="confirmDelete1">
                        I understand the consequences of this action
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="deleteUserBtn1" disabled>Delete User</button>
            </div>
        </div>
    </div>
</div>


<script src="/assets/js/utility/toggle-password.js"></script>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script src="/assets/js/utility/toast-notifications.js"></script>
<script src="/assets/js/utility/form-handler.js"></script>
<script>
    handleFormSubmission("addUserForm", "/admin/user-management");
</script>
<?php
include $footerPath;
?>