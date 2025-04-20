<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - DataTables</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-4">
        <h1>User Management</h1>
        
        <div class="card">
            <div class="card-header bg-light">
                <div class="row">
                    <div class="col">
                        <h5 class="mb-0">Users List</h5>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-secondary">
                            <?= $pagination['total'] ?? 0 ?> Total Users
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Role and Status Filters -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select id="roleFilter" class="form-select">
                            <option value="">All Roles</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="statusFilter" class="form-select">
                            <option value="">All Status</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <!-- Users Table -->
                <div class="table-responsive">
                    <table id="usersTable" class="table table-hover table-striped display responsive nowrap" style="width:100%">
                        <thead class="bg-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($users) && !empty($users)): ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= $user['id'] ?></td>
                                        <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td>
                                            <span class="badge <?= ($user['role_name'] === 'admin') ? 'bg-danger' : 'bg-primary' ?>">
                                                <?= htmlspecialchars($user['role_name']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?= ($user['is_active']) ? 'bg-success' : 'bg-secondary' ?>">
                                                <?= ($user['is_active']) ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                        <td><?= $user['last_login'] ? date('M d, Y H:i', strtotime($user['last_login'])) : 'Never' ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="#" class="btn btn-outline-secondary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="#" class="btn btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="#" class="btn btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    
    <script>
    $(document).ready(function() {
        // Initialize DataTables
        var table = $('#usersTable').DataTable({
            responsive: true,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            columnDefs: [
                { responsivePriority: 1, targets: [0, 1, 6] }, // ID, Name and Actions are high priority
                { responsivePriority: 2, targets: [2, 3] },    // Email and Role are medium priority
                { responsivePriority: 3, targets: [4, 5] }     // Status and Last Login are lowest priority
            ]
        });
        
        // Apply custom filters for Role and Status
        $('#roleFilter').on('change', function() {
            table.column(3).search($(this).val()).draw();
        });
        
        $('#statusFilter').on('change', function() {
            table.column(4).search($(this).val()).draw();
        });
    });
    </script>
</body>
</html>