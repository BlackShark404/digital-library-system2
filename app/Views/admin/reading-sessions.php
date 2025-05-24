<?php include $headerPath; ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-book me-2"></i>Reading Sessions Management</h1>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Filter Reading Sessions</h5>
            <div id="filter-form" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control filter-input" id="search" name="search" placeholder="Book title, author, or user">
                    </div>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select filter-input" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="expired">Expired</option>
                        <option value="purchased">Purchased</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" class="form-control filter-input" id="date_from" name="date_from">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" class="form-control filter-input" id="date_to" name="date_to">
                </div>
                <div class="col-12">
                    <button type="button" id="reset-filter" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Reset Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="sessions-table" class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Book</th>
                            <th>Started At</th>
                            <th>Expires At</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($sessions)): ?>
                            <?php foreach ($sessions as $session): ?>
                                <?php 
                                    // Determine status
                                    if (isset($session['is_purchased']) && $session['is_purchased']) {
                                        $statusClass = 'primary';
                                        $statusText = 'Purchased';
                                    } else if (isset($session['is_expired']) && $session['is_expired']) {
                                        $statusClass = 'danger';
                                        $statusText = 'Expired';
                                    } else {
                                        $statusClass = 'success';
                                        $statusText = 'Active';
                                    }
                                    
                                    // Calculate progress
                                    $progress = 0;
                                    $progressText = 'Not started';
                                    if (isset($session['current_page']) && isset($session['b_pages']) && $session['b_pages'] > 0) {
                                        $progress = min(100, round(($session['current_page'] / $session['b_pages']) * 100));
                                        $progressText = $session['current_page'] . '/' . $session['b_pages'] . ' (' . $progress . '%)';
                                    }
                                ?>
                                <tr>
                                    <td><?= $session['rs_id'] ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <div class="fw-bold"><?= $session['ua_first_name'] . ' ' . $session['ua_last_name'] ?></div>
                                                <div class="small text-muted"><?= $session['ua_email'] ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php 
                                                $coverPath = $session['b_cover_path'] 
                                                    ? '/assets/images/book-cover/' . $session['b_cover_path'] 
                                                    : '/assets/images/book-cover/default-cover.svg';
                                            ?>
                                            <img src="<?= $coverPath ?>" alt="Book Cover" class="me-2" style="width: 40px; height: 60px; object-fit: cover; border-radius: 2px;">
                                            <div>
                                                <div class="fw-bold"><?= $session['b_title'] ?></div>
                                                <div class="small text-muted"><?= $session['b_author'] ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= date('M d, Y H:i', strtotime($session['rs_started_at'])) ?></td>
                                    <td>
                                        <?php if (isset($session['is_purchased']) && $session['is_purchased']): ?>
                                            <span class="badge bg-primary">Unlimited</span>
                                        <?php else: ?>
                                            <?= date('M d, Y H:i', strtotime($session['rs_expires_at'])) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1 me-2" style="min-width: 100px;">
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-<?= $statusClass ?>" role="progressbar" 
                                                        style="width: <?= $progress ?>%;" 
                                                        aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="small"><?= $progressText ?></span>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary view-session-btn" data-id="<?= $session['rs_id'] ?>">
                                                <i class="bi bi-info-circle"></i> View
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <p class="text-muted mb-0"><i class="bi bi-info-circle me-1"></i> No reading sessions found</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Session Details Modal -->
<div class="modal fade" id="sessionDetailsModal" tabindex="-1" aria-labelledby="sessionDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sessionDetailsModalLabel">Reading Session Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="session-loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading session details...</p>
                </div>
                <div id="session-details" class="d-none">
                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            <img id="session-book-cover" src="" alt="Book Cover" class="img-fluid rounded mb-2" style="max-height: 200px;">
                        </div>
                        <div class="col-md-8">
                            <h4 id="session-book-title" class="mb-1"></h4>
                            <p id="session-book-author" class="text-muted"></p>
                            
                            <div class="d-flex justify-content-between mb-3">
                                <span class="badge" id="session-status-badge"></span>
                                <span class="text-muted" id="session-expiry"></span>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Reading Progress:</span>
                                    <span id="session-progress-text"></span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div id="session-progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-0 bg-light mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">User Information</h6>
                                    <div class="mb-2">
                                        <strong>Name:</strong> <span id="session-user-name"></span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Email:</strong> <span id="session-user-email"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 bg-light mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">Session Information</h6>
                                    <div class="mb-2">
                                        <strong>Started:</strong> <span id="session-start-date"></span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Expires:</strong> <span id="session-expiry-date"></span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Session ID:</strong> <span id="session-id"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="session-error" class="alert alert-danger d-none">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Error loading session details.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if jQuery is defined
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded. Using vanilla JavaScript instead.');
        
        // Initialize DataTable using vanilla JS
        const table = document.getElementById('sessions-table');
        if (table && typeof DataTable !== 'undefined') {
            // Check if the table has any data rows before initializing DataTable
            const hasData = table.querySelector('tbody tr:not([data-empty-message])');
            if (hasData) {
                new DataTable(table, {
                    responsive: true,
                    pageLength: 15,
                    language: {
                        search: "",
                        searchPlaceholder: "Search in table..."
                    }
                });
            }
        } else {
            console.error('DataTable is not defined or table element not found');
        }
        
        // Add event listeners for view buttons
        document.querySelectorAll('.view-session-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const sessionId = this.getAttribute('data-id');
                showSessionDetails(sessionId);
            });
        });
    } else {
        // Initialize DataTable using jQuery
        const sessionsTable = jQuery('#sessions-table');
        
        // Check if the table has any data rows before initializing DataTable
        if (sessionsTable.find('tbody tr').length > 0 && !sessionsTable.find('tbody tr td[colspan]').length) {
            jQuery('#sessions-table').DataTable({
                responsive: true,
                pageLength: 15,
                language: {
                    search: "",
                    searchPlaceholder: "Search in table..."
                }
            });
            
            // Use jQuery event delegation for view buttons
            jQuery('#sessions-table tbody').on('click', '.view-session-btn', function() {
                const sessionId = jQuery(this).data('id');
                showSessionDetails(sessionId);
            });
        }
    }
    
    // Handle dynamic filtering when input values change
    const filterInputs = document.querySelectorAll('.filter-input');
    let filterDebounceTimer;
    
    filterInputs.forEach(input => {
        if (input.tagName === 'SELECT') {
            // For select elements, apply filter immediately on change
            input.addEventListener('change', function() {
                applyFilters();
            });
        } else {
            // For text and date inputs, use debounce
            input.addEventListener('input', function() {
                // Clear previous timer
                clearTimeout(filterDebounceTimer);
                
                // Set a debounce timeout to avoid too many requests
                filterDebounceTimer = setTimeout(function() {
                    applyFilters();
                }, 500); // Wait 500ms after user stops typing
            });
            
            // Also apply immediately for date inputs on change
            if (input.type === 'date') {
                input.addEventListener('change', function() {
                    applyFilters();
                });
            }
        }
    });
    
    function applyFilters() {
        // Get form data
        const queryParams = new URLSearchParams();
        
        // Add filter values to query params
        filterInputs.forEach(input => {
            if (input.value.trim() !== '') {
                queryParams.append(input.name, input.value.trim());
            }
        });
        
        console.log('Filtering with params:', queryParams.toString());
        
        // Make AJAX request to get filtered data
        fetch('/api/reading-sessions?' + queryParams.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Received filtered data:', data);
                if (data.success && data.data) {
                    // Check if we have an existing DataTable instance
                    let table;
                    let tableExists = false;
                    
                    try {
                        table = jQuery('#sessions-table').DataTable();
                        tableExists = true;
                    } catch (e) {
                        console.log('No existing DataTable instance found');
                    }
                    
                    // If table exists, destroy it first since we'll recreate it with new data
                    if (tableExists) {
                        table.destroy();
                    }
                    
                    // Clear the table HTML
                    const tableBody = jQuery('#sessions-table tbody');
                    tableBody.empty();
                    
                    // Add new data
                    if (data.data.length > 0) {
                        // Add rows to the table body
                        data.data.forEach(session => {
                            // Create HTML for this row
                            const rowHtml = createRowHtml(session);
                            tableBody.append(rowHtml);
                        });
                        
                        // Reinitialize the DataTable on the non-empty table
                        jQuery('#sessions-table').DataTable({
                            responsive: true,
                            pageLength: 15,
                            language: {
                                search: "",
                                searchPlaceholder: "Search in table..."
                            }
                        });
                        
                        // Reattach event listeners
                        jQuery('#sessions-table tbody').on('click', '.view-session-btn', function() {
                            const sessionId = jQuery(this).data('id');
                            showSessionDetails(sessionId);
                        });
                    } else {
                        // If no data found, show message
                        tableBody.html(`
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <p class="text-muted mb-0"><i class="bi bi-info-circle me-1"></i> No reading sessions found with the selected filters</p>
                                </td>
                            </tr>
                        `);
                        
                        // We don't initialize DataTable for empty results
                    }
                } else {
                    console.error('API returned error or no data');
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                alert('Error fetching data. Please try again.');
            });
    }
    
    // Function to create HTML for a table row
    function createRowHtml(session) {
        // Determine status
        let statusClass, statusText;
        if (session.is_purchased) {
            statusClass = 'primary';
            statusText = 'Purchased';
        } else if (session.is_expired) {
            statusClass = 'danger';
            statusText = 'Expired';
        } else {
            statusClass = 'success';
            statusText = 'Active';
        }
        
        // Calculate progress
        let progress = 0;
        let progressText = 'Not started';
        if (session.current_page && session.b_pages && session.b_pages > 0) {
            progress = Math.min(100, Math.round((session.current_page / session.b_pages) * 100));
            progressText = `${session.current_page}/${session.b_pages} (${progress}%)`;
        }
        
        // Format cover path
        const coverPath = session.b_cover_path 
            ? '/assets/images/book-cover/' + session.b_cover_path 
            : '/assets/images/book-cover/default-cover.svg';
        
        // Format dates
        const startedAt = new Date(session.rs_started_at);
        const expiresAt = new Date(session.rs_expires_at);
        const formattedStartDate = startedAt.toLocaleString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: 'numeric',
            minute: 'numeric'
        });
        
        const formattedExpireDate = session.is_purchased 
            ? `<span class="badge bg-primary">Unlimited</span>`
            : expiresAt.toLocaleString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                hour: 'numeric',
                minute: 'numeric'
            });
            
        // Create row HTML
        return `
            <tr>
                <td>${session.rs_id}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="fw-bold">${session.ua_first_name} ${session.ua_last_name}</div>
                            <div class="small text-muted">${session.ua_email}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <img src="${coverPath}" alt="Book Cover" class="me-2" style="width: 40px; height: 60px; object-fit: cover; border-radius: 2px;">
                        <div>
                            <div class="fw-bold">${session.b_title}</div>
                            <div class="small text-muted">${session.b_author}</div>
                        </div>
                    </div>
                </td>
                <td>${formattedStartDate}</td>
                <td>${formattedExpireDate}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 me-2" style="min-width: 100px;">
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-${statusClass}" role="progressbar" 
                                    style="width: ${progress}%;" 
                                    aria-valuenow="${progress}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                        <span class="small">${progressText}</span>
                    </div>
                </td>
                <td><span class="badge bg-${statusClass}">${statusText}</span></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary view-session-btn" data-id="${session.rs_id}">
                            <i class="bi bi-info-circle"></i> View
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }
    
    // Handle reset button
    document.getElementById('reset-filter').addEventListener('click', function() {
        // Reset form fields
        filterInputs.forEach(input => {
            input.value = '';
        });
        
        // Apply filters with cleared values
        applyFilters();
    });
    
    function showSessionDetails(sessionId) {
        console.log('Opening modal for session ID:', sessionId);
        
        // Show loading state
        document.getElementById('session-loading').classList.remove('d-none');
        document.getElementById('session-details').classList.add('d-none');
        document.getElementById('session-error').classList.add('d-none');
        
        // Show modal - try jQuery first, fallback to Bootstrap JS
        if (typeof jQuery !== 'undefined') {
            jQuery('#sessionDetailsModal').modal('show');
        } else if (typeof bootstrap !== 'undefined') {
            const modal = new bootstrap.Modal(document.getElementById('sessionDetailsModal'));
            modal.show();
        } else {
            document.getElementById('sessionDetailsModal').classList.add('show');
            document.getElementById('sessionDetailsModal').style.display = 'block';
        }
        
        // Fetch session details - Fix URL structure to match router expectations
        fetch(`/api/reading-sessions/${sessionId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Session data:', data);
                if (data.success && data.data) {
                    // Hide loading, show details
                    document.getElementById('session-loading').classList.add('d-none');
                    document.getElementById('session-details').classList.remove('d-none');
                    
                    const session = data.data;
                    updateModalContent(session);
                } else {
                    throw new Error('Failed to load session data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('session-loading').classList.add('d-none');
                document.getElementById('session-error').classList.remove('d-none');
            });
    }
    
    function updateModalContent(session) {
        // Fill in session details
        document.getElementById('session-id').textContent = session.rs_id;
        document.getElementById('session-book-title').textContent = session.b_title;
        document.getElementById('session-book-author').textContent = session.b_author;
        
        // Book cover
        const coverPath = session.b_cover_path 
            ? '/assets/images/book-cover/' + session.b_cover_path 
            : '/assets/images/book-cover/default-cover.svg';
        document.getElementById('session-book-cover').src = coverPath;
        
        // User details
        document.getElementById('session-user-name').textContent = `${session.ua_first_name} ${session.ua_last_name}`;
        document.getElementById('session-user-email').textContent = session.ua_email;
        
        // Dates
        const startDate = new Date(session.rs_started_at);
        const expiryDate = new Date(session.rs_expires_at);
        document.getElementById('session-start-date').textContent = startDate.toLocaleString();
        
        // Status and expiry
        let statusClass, statusText;
        if (session.is_purchased) {
            statusClass = 'bg-primary';
            statusText = 'Purchased';
            document.getElementById('session-expiry-date').textContent = 'Unlimited Access';
            document.getElementById('session-expiry').textContent = '';
        } else if (session.is_expired) {
            statusClass = 'bg-danger';
            statusText = 'Expired';
            document.getElementById('session-expiry-date').textContent = expiryDate.toLocaleString() + ' (Expired)';
            document.getElementById('session-expiry').textContent = 'Expired on ' + expiryDate.toLocaleDateString();
        } else {
            statusClass = 'bg-success';
            statusText = 'Active';
            document.getElementById('session-expiry-date').textContent = expiryDate.toLocaleString();
            
            // Calculate remaining time
            const now = new Date();
            const diffTime = Math.abs(expiryDate - now);
            const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
            const diffHours = Math.floor((diffTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            
            let remainingText = '';
            if (diffDays > 0) {
                remainingText = `${diffDays} days`;
                if (diffHours > 0) remainingText += ` and ${diffHours} hours`;
                remainingText += ' remaining';
            } else {
                remainingText = `${diffHours} hours remaining`;
            }
            
            document.getElementById('session-expiry').textContent = remainingText;
        }
        
        const statusBadge = document.getElementById('session-status-badge');
        statusBadge.className = `badge ${statusClass}`;
        statusBadge.textContent = statusText;
        
        // Progress
        let progress = 0;
        let progressText = 'Not started';
        if (session.current_page && session.b_pages && session.b_pages > 0) {
            progress = Math.min(100, Math.round((session.current_page / session.b_pages) * 100));
            progressText = `${session.current_page}/${session.b_pages} pages (${progress}%)`;
        }
        
        const progressBar = document.getElementById('session-progress-bar');
        progressBar.style.width = `${progress}%`;
        progressBar.setAttribute('aria-valuenow', progress);
        progressBar.className = `progress-bar bg-${statusClass.replace('bg-', '')}`;
        
        document.getElementById('session-progress-text').textContent = progressText;
    }
});
</script>

<?php include $footerPath; ?>
