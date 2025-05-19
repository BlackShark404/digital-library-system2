<?php
include $headerPath;

// Get action types for filtering from the controller data
$unique_actions = $unique_actions ?? [];
$action_filter = $action_filter ?? '';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Activity Log</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Activity Log</li>
    </ol>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-funnel me-1"></i>
            Filter Activity
        </div>
        <div class="card-body">
            <form id="filterForm" class="row g-3">
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
                <div class="col-md-12 mt-3">
                    <button type="button" id="resetFilters" class="btn btn-secondary">Reset</button>
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
                            <th>View</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- The table data will be loaded by DataTables -->
                    </tbody>
                </table>
            </div>
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
                        <table class="table table-sm" id="activitySummaryTable">
                            <thead>
                                <tr>
                                    <th>Action Type</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Activity summary will be loaded dynamically -->
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
                        <li>Click the eye icon to view detailed information about an activity</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Log Detail Modal -->
<div class="modal fade" id="logModal" tabindex="-1" aria-labelledby="logModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logModalLabel">Activity Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>User:</strong> <span id="modalUsername"></span>
                </div>
                <div class="mb-3">
                    <strong>Action:</strong> <span id="modalAction"></span>
                </div>
                <div class="mb-3">
                    <strong>Details:</strong> <span id="modalDetails"></span>
                </div>
                <div class="mb-3">
                    <strong>Date/Time:</strong> <span id="modalTimestamp"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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

<script>
// Helper function to get badge class based on action type
function getBadgeClass(action) {
    switch (action) {
        case 'Login':
            return 'bg-success';
        case 'Registration':
            return 'bg-success';
        case 'Logout':
            return 'bg-secondary';
        case 'Book Purchase':
            return 'bg-primary';
        case 'Book Added':
            return 'bg-primary';
        case 'Reading Session':
            return 'bg-warning';
        case 'Profile Update':
            return 'bg-warning';
        default:
            return 'bg-secondary';
    }
}

// Initialize DataTable when the document is ready
$(document).ready(function() {
    // Initialize the activity log DataTable
    var table = $('#activityTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/api/activity-logs',
            type: 'GET',
            data: function(d) {
                // Add filter parameters from the form
                d.action = $('#action').val();
            }
        },
        columns: [
            { data: 'id' },
            { 
                data: 'username',
                render: function(data, type, row) {
                    if (!row.user_id) return 'System';
                    return '<a href="/admin/user-management?id=' + row.user_id + '" data-bs-toggle="tooltip" data-bs-title="View User Profile">' 
                         + data + '</a>';
                }
            },
            { 
                data: 'action',
                render: function(data, type, row) {
                    return '<span class="badge ' + getBadgeClass(data) + '">' + data + '</span>';
                }
            },
            { data: 'details' },
            { data: 'timestamp' },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return '<button class="btn btn-sm btn-info view-log" data-id="' + row.id + '">'
                         + '<i class="bi bi-eye"></i></button>';
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 10,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    
    // Load activity statistics
    loadActivityStats();
    
    // Apply filters when the filter form is submitted
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        table.ajax.reload();
    });
    
    // Reset filters
    $('#resetFilters').on('click', function() {
        $('#filterForm')[0].reset();
        table.ajax.reload();
    });
    
    // Also reload the table when the action dropdown changes
    $('#action').on('change', function() {
        table.ajax.reload();
    });
    
    // Handle view log button click
    $('#activityTable').on('click', '.view-log', function() {
        var id = $(this).data('id');
        
        // Fetch log details
        $.ajax({
            url: '/api/activity-logs/' + id,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var log = response.data;
                    
                    // Update modal content
                    $('#logModalLabel').text('Activity Details #' + log.id);
                    $('#modalUsername').text(log.user_id ? log.username + ' (ID: ' + log.user_id + ')' : 'System');
                    $('#modalAction').html('<span class="badge ' + getBadgeClass(log.action) + '">' + log.action + '</span>');
                    $('#modalDetails').text(log.details);
                    $('#modalTimestamp').text(log.timestamp);
                    
                    // Show the modal
                    $('#logModal').modal('show');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while fetching log details.');
            }
        });
    });
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});

// Function to load activity statistics
function loadActivityStats() {
    $.ajax({
        url: '/api/activity-logs/stats',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var stats = response.data;
                var tbody = $('#activitySummaryTable tbody');
                tbody.empty();
                
                // Add rows for each action type
                $.each(stats, function(i, stat) {
                    tbody.append(
                        '<tr>' +
                        '<td><span class="badge ' + getBadgeClass(stat.action) + '">' + stat.action + '</span></td>' +
                        '<td>' + stat.count + '</td>' +
                        '</tr>'
                    );
                });
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('An error occurred while fetching activity statistics.');
        }
    });
}
</script>

<?php
// Include footer
include $footerPath;
?>