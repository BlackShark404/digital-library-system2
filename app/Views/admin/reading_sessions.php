<?php
// Set page title
$page_title = 'Reading Sessions';

// Include header
include 'includes/header.php';

// Check if user is admin, if not redirect
if (!is_admin()) {
    header('Location: ../login.php');
    exit();
}

// Hardcoded reading session data
$reading_sessions = [
    [
        'id' => 1,
        'user_id' => 15,
        'username' => 'janesmith',
        'book_id' => 103,
        'book_title' => 'The Great Gatsby',
        'start_time' => '2025-03-28 09:15:22',
        'end_time' => '2025-03-28 10:45:12',
        'duration_minutes' => 90,
        'pages_read' => 42,
        'completion_percentage' => 21
    ],
    [
        'id' => 2,
        'user_id' => 8,
        'username' => 'robertjohnson',
        'book_id' => 155,
        'book_title' => 'To Kill a Mockingbird',
        'start_time' => '2025-03-29 14:30:05',
        'end_time' => '2025-03-29 15:10:45',
        'duration_minutes' => 40,
        'pages_read' => 18,
        'completion_percentage' => 6
    ],
    [
        'id' => 3,
        'user_id' => 22,
        'username' => 'mikebrown',
        'book_id' => 87,
        'book_title' => '1984',
        'start_time' => '2025-03-30 20:05:33',
        'end_time' => '2025-03-30 22:15:21',
        'duration_minutes' => 130,
        'pages_read' => 75,
        'completion_percentage' => 25
    ],
    [
        'id' => 4,
        'user_id' => 15,
        'username' => 'janesmith',
        'book_id' => 103,
        'book_title' => 'The Great Gatsby',
        'start_time' => '2025-03-31 12:10:44',
        'end_time' => '2025-03-31 13:25:18',
        'duration_minutes' => 75,
        'pages_read' => 35,
        'completion_percentage' => 38
    ],
    [
        'id' => 5,
        'user_id' => 31,
        'username' => 'sarahwilliams',
        'book_id' => 210,
        'book_title' => 'Pride and Prejudice',
        'start_time' => '2025-04-01 08:30:10',
        'end_time' => '2025-04-01 09:45:30',
        'duration_minutes' => 75,
        'pages_read' => 40,
        'completion_percentage' => 12
    ]
];

// Calculate statistics
$total_sessions = count($reading_sessions);
$total_duration = array_sum(array_column($reading_sessions, 'duration_minutes'));
$total_pages = array_sum(array_column($reading_sessions, 'pages_read'));
$avg_duration = round($total_duration / $total_sessions, 1);
$unique_users = count(array_unique(array_column($reading_sessions, 'user_id')));
$unique_books = count(array_unique(array_column($reading_sessions, 'book_id')));
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-clock-history me-2"></i>Reading Sessions</h2>
        <div>
            <button class="btn btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="bi bi-funnel me-1"></i>Filter
            </button>
            <button class="btn btn-outline-primary" id="exportBtn">
                <i class="bi bi-download me-1"></i>Export Data
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">Session Summary</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Sessions:</span>
                        <strong><?php echo $total_sessions; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Unique Users:</span>
                        <strong><?php echo $unique_users; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Unique Books:</span>
                        <strong><?php echo $unique_books; ?></strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">Reading Metrics</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Time:</span>
                        <strong><?php echo $total_duration; ?> minutes</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Avg. Session Length:</span>
                        <strong><?php echo $avg_duration; ?> minutes</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Total Pages Read:</span>
                        <strong><?php echo $total_pages; ?> pages</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">Date Range</h5>
                    <div class="input-group mb-2">
                        <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                        <input type="date" class="form-control" id="startDate" value="2025-03-28">
                    </div>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                        <input type="date" class="form-control" id="endDate" value="2025-04-01">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sessions Table -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Reading Session List</h5>
                <div class="input-group" style="width: 300px;">
                    <input type="text" class="form-control" placeholder="Search sessions..." id="searchInput">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">User</th>
                            <th scope="col">Book</th>
                            <th scope="col">Date</th>
                            <th scope="col">Duration</th>
                            <th scope="col">Pages</th>
                            <th scope="col">Completion</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reading_sessions as $session): ?>
                            <tr>
                                <td><?php echo $session['id']; ?></td>
                                <td>
                                    <a href="../user_management.php?id=<?php echo $session['user_id']; ?>" class="fw-semibold text-decoration-none">
                                        <?php echo htmlspecialchars($session['username']); ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="../book_management.php?id=<?php echo $session['book_id']; ?>" class="text-decoration-none">
                                        <?php echo htmlspecialchars($session['book_title']); ?>
                                    </a>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($session['start_time'])); ?></td>
                                <td><?php echo $session['duration_minutes']; ?> min</td>
                                <td><?php echo $session['pages_read']; ?></td>
                                <td>
                                    <div class="progress" style="height: 8px;" data-bs-toggle="tooltip" data-bs-title="<?php echo $session['completion_percentage']; ?>% completed">
                                        <div class="progress-bar bg-success" style="width: <?php echo $session['completion_percentage']; ?>%"></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#sessionDetailModal" data-session-id="<?php echo $session['id']; ?>">
                                                    <i class="bi bi-eye me-2"></i>View Details
                                                </a></li>
                                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editSessionModal" data-session-id="<?php echo $session['id']; ?>">
                                                    <i class="bi bi-pencil me-2"></i>Edit
                                                </a></li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteSessionModal" data-session-id="<?php echo $session['id']; ?>">
                                                    <i class="bi bi-trash me-2"></i>Delete
                                                </a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <nav>
                <ul class="pagination justify-content-center mb-0">
                    <li class="page-item disabled">
                        <a class="page-link" href="#"><i class="bi bi-chevron-left"></i></a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#"><i class="bi bi-chevron-right"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Session Detail Modal -->
<div class="modal fade" id="sessionDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reading Session Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1 text-secondary">Session ID</p>
                        <p class="fw-bold mb-0">#1</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-secondary">User</p>
                        <p class="fw-bold mb-0">janesmith</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <p class="mb-1 text-secondary">Book</p>
                        <p class="fw-bold mb-0">The Great Gatsby</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1 text-secondary">Start Time</p>
                        <p class="fw-bold mb-0">Mar 28, 2025 09:15:22</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-secondary">End Time</p>
                        <p class="fw-bold mb-0">Mar 28, 2025 10:45:12</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <p class="mb-1 text-secondary">Duration</p>
                        <p class="fw-bold mb-0">90 minutes</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1 text-secondary">Pages Read</p>
                        <p class="fw-bold mb-0">42 pages</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1 text-secondary">Completion</p>
                        <p class="fw-bold mb-0">21%</p>
                    </div>
                </div>
                <div class="mb-3">
                    <p class="mb-1 text-secondary">Reading Progress</p>
                    <div class="progress mb-2" style="height: 10px;">
                        <div class="progress-bar bg-success" style="width: 21%"></div>
                    </div>
                </div>
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title">Device Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1 text-secondary">Device</p>
                                <p class="mb-0">iPad (8th Gen)</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-secondary">IP Address</p>
                                <p class="mb-0">192.168.1.45</p>
                            </div>
                            <div class="col-12">
                                <p class="mb-1 text-secondary">Browser</p>
                                <p class="mb-0">Safari 16.0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm btn-primary">View Full Report</button>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Reading Sessions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">User</label>
                        <select class="form-select">
                            <option value="" selected>All Users</option>
                            <option value="15">janesmith</option>
                            <option value="8">robertjohnson</option>
                            <option value="22">mikebrown</option>
                            <option value="31">sarahwilliams</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Book</label>
                        <select class="form-select">
                            <option value="" selected>All Books</option>
                            <option value="103">The Great Gatsby</option>
                            <option value="155">To Kill a Mockingbird</option>
                            <option value="87">1984</option>
                            <option value="210">Pride and Prejudice</option>
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Date Range (Start)</label>
                            <input type="date" class="form-control" value="2025-03-28">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date Range (End)</label>
                            <input type="date" class="form-control" value="2025-04-01">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration (Minutes)</label>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="number" class="form-control" placeholder="Min">
                            </div>
                            <div class="col-md-6">
                                <input type="number" class="form-control" placeholder="Max">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Completion Percentage</label>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="number" class="form-control" placeholder="Min" min="0" max="100">
                            </div>
                            <div class="col-md-6">
                                <input type="number" class="form-control" placeholder="Max" min="0" max="100">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-outline-secondary">Reset</button>
                <button type="button" class="btn btn-primary">Apply Filters</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Session Modal -->
<div class="modal fade" id="deleteSessionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Reading Session</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this reading session record? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    This will permanently remove the reading session from the system.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger">Delete Session</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Reading Sessions -->
<script>
    // Initialize the session detail modal with dynamic data
    const sessionDetailModal = document.getElementById('sessionDetailModal');
    if (sessionDetailModal) {
        sessionDetailModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const sessionId = button.getAttribute('data-session-id');

            // In a real application, you would fetch the session details from the server here
            // For now, we'll just update the modal title with the session ID
            const modalTitle = sessionDetailModal.querySelector('.modal-title');
            modalTitle.textContent = `Reading Session Details #${sessionId}`;
        });
    }

    // Export button functionality
    document.getElementById('exportBtn').addEventListener('click', function() {
        alert('Export functionality would download session data as CSV or Excel file');
    });

    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchText = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('tbody tr');

        tableRows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            row.style.display = rowText.includes(searchText) ? '' : 'none';
        });
    });

    // Date range filtering
    document.getElementById('startDate').addEventListener('change', function() {
        // In a real app, this would trigger a filter of the data
        console.log('Date range changed');
    });

    document.getElementById('endDate').addEventListener('change', function() {
        // In a real app, this would trigger a filter of the data
        console.log('Date range changed');
    });
</script>

<?php
// Include footer
include 'includes/footer.php';
?>