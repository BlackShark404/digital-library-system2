<?php
// Include header
include $headerPath;
?>

<div class="container my-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Reading Sessions Management</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-muted mb-1">Total Sessions</h6>
                                    <h2 class="mb-0 text-primary" id="total-sessions">0</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-muted mb-1">Unique Users</h6>
                                    <h2 class="mb-0 text-info" id="unique-users">0</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-muted mb-1">Unique Books</h6>
                                    <h2 class="mb-0 text-success" id="unique-books">0</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-muted mb-1">Avg. Duration</h6>
                                    <h2 class="mb-0 text-warning" id="avg-duration">0</h2>
                                    <small class="text-muted">minutes</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sessions Table Card -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Reading Session List</h5>
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="readingSessionsTable" class="table table-striped table-hover display">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Book</th>
                            <th>Start Time</th>
                            <th>Status</th>
                            <th>Duration</th>
                            <th>Pages</th>
                            <th>Completion</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTable will populate this -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Session Detail Modal -->
<div class="modal fade" id="sessionDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reading Session Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1 text-secondary">Session ID</p>
                        <p class="fw-bold mb-0" id="detail-session-id">#1</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-secondary">Status</p>
                        <span class="badge bg-success" id="detail-status">Active</span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1 text-secondary">User</p>
                        <p class="fw-bold mb-0" id="detail-user">John Doe</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-secondary">Purchased</p>
                        <p class="fw-bold mb-0" id="detail-purchased">No</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <p class="mb-1 text-secondary">Book</p>
                        <p class="fw-bold mb-0" id="detail-book">The Great Gatsby</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1 text-secondary">Start Time</p>
                        <p class="fw-bold mb-0" id="detail-start-time">Mar 28, 2025 09:15:22</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-secondary">Expiry Time</p>
                        <p class="fw-bold mb-0" id="detail-expiry-time">Mar 31, 2025 09:15:22</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <p class="mb-1 text-secondary">Duration</p>
                        <p class="fw-bold mb-0" id="detail-duration">90 minutes</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1 text-secondary">Pages Read</p>
                        <p class="fw-bold mb-0" id="detail-pages">42 / 180 pages</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1 text-secondary">Completion</p>
                        <p class="fw-bold mb-0" id="detail-completion">21%</p>
                    </div>
                </div>
                <div class="mb-3">
                    <p class="mb-1 text-secondary">Reading Progress</p>
                    <div class="progress mb-2" style="height: 10px;">
                        <div class="progress-bar bg-success" id="detail-progress-bar" style="width: 21%"></div>
                    </div>
                </div>
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title">Last Activity</h6>
                        <p class="mb-0" id="detail-last-activity">Mar 28, 2025 10:45:12</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="detail-edit-btn">Edit Session</button>
            </div>
        </div>
    </div>
                            </div>

<!-- Edit Session Modal -->
<div class="modal fade" id="editSessionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Reading Session</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
            <div class="modal-body">
                <form id="editSessionForm">
                    <input type="hidden" id="edit-session-id">
                    <div class="mb-3">
                        <label for="edit-pages-read" class="form-label">Pages Read</label>
                        <input type="number" class="form-control" id="edit-pages-read" min="1" required>
                            </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit-is-completed">
                            <label class="form-check-label" for="edit-is-completed">
                                Mark as Completed
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveSessionBtn">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteSessionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this reading session? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Deleting this session will remove all reading progress data for this session.
                </div>
                <input type="hidden" id="delete-session-id">
                <p class="mb-0">Session: <span id="delete-session-info" class="fw-bold"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete Session</button>
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
                <form id="filterForm">
                    <div class="mb-3">
                        <label for="filter-status" class="form-label">Status</label>
                        <select class="form-select" id="filter-status">
                            <option value="">All</option>
                            <option value="Active">Active</option>
                            <option value="Completed">Completed</option>
                            <option value="Expired">Expired</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="filter-purchased" class="form-label">Purchased</label>
                        <select class="form-select" id="filter-purchased">
                            <option value="">All</option>
                            <option value="true">Yes</option>
                            <option value="false">No</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="filter-completion" class="form-label">Completion</label>
                        <select class="form-select" id="filter-completion">
                            <option value="">All</option>
                            <option value="1">0% - 25%</option>
                            <option value="2">25% - 50%</option>
                            <option value="3">50% - 75%</option>
                            <option value="4">75% - 100%</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="clearFiltersBtn">Clear Filters</button>
                <button type="button" class="btn btn-primary" id="applyFiltersBtn">Apply Filters</button>
            </div>
        </div>
    </div>
</div>

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

<!-- Include DataTablesManager -->
<script src="/assets/js/utility/DataTablesManager.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable with DataTablesManager
        const sessionTableManager = new DataTablesManager('readingSessionsTable', {
            ajaxUrl: '/api/reading-sessions',
            columns: [
                { data: 'id', title: 'ID' },
                { data: 'user_name', title: 'User' },
                { data: 'book_title', title: 'Book' },
                { data: 'start_time', title: 'Start Time', 
                  render: function(data) {
                      return new Date(data).toLocaleString();
                  }
                },
                { 
                    data: 'status', 
                    title: 'Status',
                    badge: {
                        type: 'secondary',
                        pill: true,
                        valueMap: {
                            'Active': {
                                type: 'success',
                                display: 'Active'
                            },
                            'Completed': {
                                type: 'primary',
                                display: 'Completed'
                            },
                            'Expired': {
                                type: 'danger',
                                display: 'Expired'
                            }
                        }
                    }
                },
                { 
                    data: 'duration_minutes', 
                    title: 'Duration',
                    render: function(data) {
                        return Math.round(data) + ' min';
                    }
                },
                { 
                    data: 'pages_read', 
                    title: 'Pages',
                    render: function(data, type, row) {
                        return data + ' / ' + row.total_pages;
                    }
                },
                { 
                    data: 'completion_percentage', 
                    title: 'Completion',
                    render: function(data) {
                        return '<div class="progress" style="height: 8px;" data-bs-toggle="tooltip" data-bs-title="' + data + '% completed">' +
                            '<div class="progress-bar bg-success" style="width: ' + data + '%"></div>' +
                            '</div>';
                    }
                }
            ],
            viewRowCallback: function(row) {
                showSessionDetails(row);
            },
            editRowCallback: function(row) {
                showEditSession(row);
            },
            deleteRowCallback: function(row) {
                deleteSession(row.id);
            }
        });
        
        // Update statistics from the loaded data
        function updateStatistics(data) {
            // Calculate statistics
            const totalSessions = data.length;
            const totalDuration = data.reduce((sum, session) => sum + parseFloat(session.duration_minutes || 0), 0);
            const totalPages = data.reduce((sum, session) => sum + parseInt(session.pages_read || 0), 0);
            const avgDuration = totalSessions > 0 ? Math.round(totalDuration / totalSessions) : 0;
            
            // Get unique users and books
            const uniqueUsers = new Set(data.map(session => session.user_id)).size;
            const uniqueBooks = new Set(data.map(session => session.book_id)).size;
            
            // Update DOM
            document.getElementById('total-sessions').textContent = totalSessions;
            document.getElementById('unique-users').textContent = uniqueUsers;
            document.getElementById('unique-books').textContent = uniqueBooks;
            document.getElementById('avg-duration').textContent = avgDuration;
        }
        
        // Custom event for data loaded
        document.addEventListener('datatableDataLoaded', function(e) {
            updateStatistics(e.detail.data);
        });
        
        // Function to display session details in modal
        function showSessionDetails(session) {
            // Populate the modal with session details
            document.getElementById('detail-session-id').textContent = '#' + session.id;
            document.getElementById('detail-user').textContent = session.user_name;
            document.getElementById('detail-book').textContent = session.book_title;
            document.getElementById('detail-start-time').textContent = new Date(session.start_time).toLocaleString();
            document.getElementById('detail-expiry-time').textContent = new Date(session.expiry_time).toLocaleString();
            document.getElementById('detail-duration').textContent = Math.round(session.duration_minutes) + ' minutes';
            document.getElementById('detail-pages').textContent = session.pages_read + ' / ' + session.total_pages + ' pages';
            document.getElementById('detail-completion').textContent = session.completion_percentage + '%';
            document.getElementById('detail-last-activity').textContent = new Date(session.last_activity).toLocaleString();
            document.getElementById('detail-purchased').textContent = session.is_purchased ? 'Yes' : 'No';
            
            // Set progress bar
            document.getElementById('detail-progress-bar').style.width = session.completion_percentage + '%';
            
            // Set status badge
            const statusElement = document.getElementById('detail-status');
            statusElement.textContent = session.status;
            statusElement.className = 'badge'; // Reset class
            
            // Add appropriate color based on status
            if (session.status === 'Active') {
                statusElement.classList.add('bg-success');
            } else if (session.status === 'Completed') {
                statusElement.classList.add('bg-primary');
            } else if (session.status === 'Expired') {
                statusElement.classList.add('bg-danger');
            } else {
                statusElement.classList.add('bg-secondary');
            }
            
            // Set up the edit button to open the edit modal
            document.getElementById('detail-edit-btn').onclick = function() {
                $('#sessionDetailModal').modal('hide');
                showEditSession(session);
            };
            
            // Show the modal
            $('#sessionDetailModal').modal('show');
        }
        
        // Function to show edit session modal
        function showEditSession(session) {
            document.getElementById('edit-session-id').value = session.id;
            document.getElementById('edit-pages-read').value = session.pages_read;
            document.getElementById('edit-is-completed').checked = session.is_completed;
            
            // Show the modal
            $('#editSessionModal').modal('show');
        }
        
        // Handle saving session edits
        document.getElementById('saveSessionBtn').addEventListener('click', function() {
            const sessionId = document.getElementById('edit-session-id').value;
            const pagesRead = document.getElementById('edit-pages-read').value;
            const isCompleted = document.getElementById('edit-is-completed').checked;
            
            // Validate input
            if (!pagesRead || pagesRead < 1) {
                alert('Please enter a valid number of pages read.');
                return;
            }
            
            // Make API call to update
            fetch(`/api/reading-sessions/${sessionId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    current_page: pagesRead,
                    is_completed: isCompleted
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal and refresh data table
                    $('#editSessionModal').modal('hide');
                    sessionTableManager.refresh();
                    
                    // Show success message
                    alert('Session updated successfully');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the session');
            });
        });
        
        // Delete session confirmation
        function deleteSession(id) {
            // Get session details for confirmation
            fetch(`/api/reading-sessions/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const session = data.data;
                        document.getElementById('delete-session-id').value = id;
                        document.getElementById('delete-session-info').textContent = 
                            `#${id} - ${session.user_name} reading "${session.book_title}"`;
                        
                        // Show delete confirmation modal
                        $('#deleteSessionModal').modal('show');
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while fetching session details');
                });
        }
        
        // Confirm delete button handler
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            const sessionId = document.getElementById('delete-session-id').value;
            
            // Make API call to delete
            fetch(`/api/reading-sessions/${sessionId}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal and refresh data table
                    $('#deleteSessionModal').modal('hide');
                    sessionTableManager.refresh();
                    
                    // Show success message
                    alert('Session deleted successfully');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the session');
            });
        });
        
        // Handle filter application
        document.getElementById('applyFiltersBtn').addEventListener('click', function() {
            const statusFilter = document.getElementById('filter-status').value;
            const purchasedFilter = document.getElementById('filter-purchased').value;
            const completionFilter = document.getElementById('filter-completion').value;
            
            // Create filter object
            const filters = {};
            if (statusFilter) filters.status = statusFilter;
            if (purchasedFilter) filters.is_purchased = purchasedFilter === 'true';
            
            // Apply filters to datatable
            sessionTableManager.applyFilters(filters);
            
            // Handle completion filter separately
            if (completionFilter) {
                $.fn.dataTable.ext.search.push(
                    function(settings, data, dataIndex, rowData) {
                        const completion = parseInt(rowData.completion_percentage || 0);
                        
                        // Check ranges based on filter value
                        if (completionFilter === '1' && completion >= 0 && completion < 25) return true;
                        if (completionFilter === '2' && completion >= 25 && completion < 50) return true;
                        if (completionFilter === '3' && completion >= 50 && completion < 75) return true;
                        if (completionFilter === '4' && completion >= 75 && completion <= 100) return true;
                        
                        return false;
                    }
                );
                
                // Redraw the table with filter applied
                sessionTableManager.dataTable.draw();
            }
            
            // Close the modal
            $('#filterModal').modal('hide');
        });
        
        // Clear filters button handler
        document.getElementById('clearFiltersBtn').addEventListener('click', function() {
            // Reset filter form
            document.getElementById('filterForm').reset();
            
            // Clear datatable filters
            sessionTableManager.applyFilters({});
            
            // Close the modal
            $('#filterModal').modal('hide');
        });
        
        // Safely modify the dataSrc function to dispatch custom events
        if (sessionTableManager && sessionTableManager.options && sessionTableManager.options.ajax) {
            const originalDataSrc = typeof sessionTableManager.options.ajax.dataSrc === 'function' 
                ? sessionTableManager.options.ajax.dataSrc 
                : json => json.data || json;
                
            sessionTableManager.options.ajax.dataSrc = function(json) {
                // Get data using original function or fallback
                const data = originalDataSrc(json);
                
                // Dispatch event with the data
                document.dispatchEvent(new CustomEvent('datatableDataLoaded', {
                    detail: { data: data }
                }));
                
                return data;
            };
        }
        
        // Update statistics directly on initial load
        fetch('/api/reading-sessions')
            .then(response => response.json())
            .then(json => {
                if (json.success && json.data) {
                    updateStatistics(json.data);
                }
            })
            .catch(error => console.error('Error loading statistics:', error));
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<?php include $footerPath; ?>