<?php
include $headerPath;

// Hardcoded activity log data
$activity_logs = [
    [
        'id' => 1,
        'user_id' => 5,
        'username' => 'johndoe',
        'action' => 'Login',
        'details' => 'User logged in successfully',
        'timestamp' => '2025-04-01 09:15:22'
    ],
    [
        'id' => 2,
        'user_id' => 12,
        'username' => 'emilysmith',
        'action' => 'Book Purchase',
        'details' => 'Purchased "The Great Gatsby" (ID: 23)',
        'timestamp' => '2025-04-01 08:42:15'
    ],
    [
        'id' => 3,
        'user_id' => 8,
        'username' => 'michaelwilson',
        'action' => 'Reading Session',
        'details' => 'Started reading "1984" (ID: 14)',
        'timestamp' => '2025-04-01 07:30:41'
    ],
    [
        'id' => 4,
        'user_id' => 3,
        'username' => 'sarahparker',
        'action' => 'Profile Update',
        'details' => 'User updated profile information',
        'timestamp' => '2025-03-31 23:18:05'
    ],
    [
        'id' => 5,
        'user_id' => 19,
        'username' => 'robertjohnson',
        'action' => 'Registration',
        'details' => 'New user registered',
        'timestamp' => '2025-03-31 20:45:19'
    ],
    [
        'id' => 6,
        'user_id' => 7,
        'username' => 'davidbrown',
        'action' => 'Logout',
        'details' => 'User logged out',
        'timestamp' => '2025-03-31 18:22:37'
    ],
    [
        'id' => 7,
        'user_id' => 5,
        'username' => 'johndoe',
        'action' => 'Book Rating',
        'details' => 'Rated "To Kill a Mockingbird" (ID: 8) with 5 stars',
        'timestamp' => '2025-03-31 16:08:52'
    ],
    [
        'id' => 9,
        'user_id' => 1,
        'username' => 'admin',
        'action' => 'Book Added',
        'details' => 'Added new book "Dune" (ID: 42)',
        'timestamp' => '2025-03-31 11:20:45'
    ],
];


?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Activity Log</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Activity Log</li>
    </ol>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-funnel me-1"></i>
            Filter Activity
        </div>
        <div class="card-body">
            <form action="" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="action" class="form-label">Action Type</label>
                    <select class="form-select" id="action" name="action">
                        <option value="">All Actions</option>
                        <?php foreach ($unique_actions as $action): ?>
                            <option value="<?php echo htmlspecialchars($action); ?>" <?php echo ($action_filter === $action) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($action); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Log Table -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-list-check me-1"></i>
            Activity Records
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="activityTable">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Action</th>
                            <th>Details</th>
                            <th>Timestamp</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activity_logs as $log): ?>
                            <tr>
                                <td><?php echo $log['id']; ?></td>
                                <td>
                                    <a href="user_management.php?id=<?php echo $log['user_id']; ?>" data-bs-toggle="tooltip" data-bs-title="View User Profile">
                                        <?php echo htmlspecialchars($log['username']); ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="badge <?php echo getBadgeClass($log['action']); ?>">
                                        <?php echo htmlspecialchars($log['action']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($log['details']); ?></td>
                                <td><?php echo htmlspecialchars($log['timestamp']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#logModal<?php echo $log['id']; ?>">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Detail Modal for each log entry -->
                            <div class="modal fade" id="logModal<?php echo $log['id']; ?>" tabindex="-1" aria-labelledby="logModalLabel<?php echo $log['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="logModalLabel<?php echo $log['id']; ?>">Activity Details #<?php echo $log['id']; ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <strong>User:</strong> <?php echo htmlspecialchars($log['username']); ?> (ID: <?php echo $log['user_id']; ?>)
                                            </div>
                                            <div class="mb-3">
                                                <strong>Action:</strong> <?php echo htmlspecialchars($log['action']); ?>
                                            </div>
                                            <div class="mb-3">
                                                <strong>Details:</strong> <?php echo htmlspecialchars($log['details']); ?>
                                            </div>
                                            <div class="mb-3">
                                                <strong>Date/Time:</strong> <?php echo htmlspecialchars($log['timestamp']); ?>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination - would be dynamic in a real implementation -->
            <nav aria-label="Activity log pagination">
                <ul class="pagination justify-content-center mt-4">
                    <li class="page-item disabled">
                        <span class="page-link">Previous</span>
                    </li>
                    <li class="page-item active" aria-current="page">
                        <span class="page-link">1</span>
                    </li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Activity Summary Card -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-bar-chart me-1"></i>
                    Recent Activity Summary
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Action Type</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Count occurrences of each action
                                $action_counts = array_count_values(array_column($activity_logs, 'action'));
                                arsort($action_counts); // Sort by count in descending order

                                foreach ($action_counts as $action => $count) {
                                    echo '<tr>';
                                    echo '<td><span class="badge ' . getBadgeClass($action) . '">' . htmlspecialchars($action) . '</span></td>';
                                    echo '<td>' . $count . '</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-info-circle me-1"></i>
                    Activity Log Information
                </div>
                <div class="card-body">
                    <p>The activity log tracks all significant actions performed by users and administrators in the system.</p>
                    <p>Use the filters above to narrow down the activity records by action type</p>
                    <p><strong>Tip:</strong></p>
                    <ul>
                        <li>Use the eye icon to view detailed information about an activity</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Helper function to get appropriate badge class based on action
function getBadgeClass($action)
{
    switch ($action) {
        case 'Login':
        case 'Registration':
            return 'bg-success';
        case 'Logout':
            return 'bg-secondary';
        case 'Book Purchase':
        case 'Book Added':
            return 'bg-primary';
        case 'Reading Session':
        case 'Book Rating':
        case 'Profile Update':
            return 'bg-warning';
        default:
            return 'bg-secondary';
    }
}

// Include footer
include $footerPath;
?>