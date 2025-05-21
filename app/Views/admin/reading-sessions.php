<?php include $headerPath; ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-book me-2"></i>Reading Sessions Management</h1>
        <a href="/api/reading-sessions/export" class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-1"></i> Export CSV
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Filter Reading Sessions</h5>
            <form id="filter-form" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Book title, author, or user">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="expired">Expired</option>
                        <option value="purchased">Purchased</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" class="form-control" id="date_from" name="date_from">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" class="form-control" id="date_to" name="date_to">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <button type="button" id="reset-filter" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Reset
                    </button>
                </div>
            </form>
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
                                            <a href="/reading-session/read-book/<?= $session['rs_id'] ?>" class="btn btn-outline-primary" target="_blank">
                                                <i class="bi bi-book"></i> View
                                            </a>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    const table = new DataTable('#sessions-table', {
        responsive: true,
        pageLength: 15,
        language: {
            search: "",
            searchPlaceholder: "Search in table..."
        }
    });
    
    // Handle filter form submission
    document.getElementById('filter-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(this);
        const queryParams = new URLSearchParams();
        
        // Add form fields to query params
        for (const [key, value] of formData.entries()) {
            if (value.trim() !== '') {
                queryParams.append(key, value);
            }
        }
        
        // Make AJAX request to get filtered data
        fetch('/api/reading-sessions?' + queryParams.toString())
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    // Clear the table
                    table.clear();
                    
                    // Add new data
                    data.data.forEach(session => {
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
                        
                        // Add row to table
                        table.row.add([
                            session.rs_id,
                            `<div class="d-flex align-items-center">
                                <div>
                                    <div class="fw-bold">${session.ua_first_name} ${session.ua_last_name}</div>
                                    <div class="small text-muted">${session.ua_email}</div>
                                </div>
                            </div>`,
                            `<div class="d-flex align-items-center">
                                <img src="${coverPath}" alt="Book Cover" class="me-2" style="width: 40px; height: 60px; object-fit: cover; border-radius: 2px;">
                                <div>
                                    <div class="fw-bold">${session.b_title}</div>
                                    <div class="small text-muted">${session.b_author}</div>
                                </div>
                            </div>`,
                            formattedStartDate,
                            formattedExpireDate,
                            `<div class="d-flex align-items-center">
                                <div class="flex-grow-1 me-2" style="min-width: 100px;">
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-${statusClass}" role="progressbar" 
                                            style="width: ${progress}%;" 
                                            aria-valuenow="${progress}" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                                <span class="small">${progressText}</span>
                            </div>`,
                            `<span class="badge bg-${statusClass}">${statusText}</span>`,
                            `<div class="btn-group btn-group-sm">
                                <a href="/reading-session/read-book/${session.rs_id}" class="btn btn-outline-primary" target="_blank">
                                    <i class="bi bi-book"></i> View
                                </a>
                            </div>`
                        ]).draw();
                    });
                    
                    // If no data found
                    if (data.data.length === 0) {
                        const noDataRow = `
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <p class="text-muted mb-0"><i class="bi bi-info-circle me-1"></i> No reading sessions found with the selected filters</p>
                                </td>
                            </tr>
                        `;
                        document.querySelector('#sessions-table tbody').innerHTML = noDataRow;
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    });
    
    // Handle reset button
    document.getElementById('reset-filter').addEventListener('click', function() {
        // Reset form fields
        document.getElementById('filter-form').reset();
        
        // Trigger submit to reload all data
        document.getElementById('filter-form').dispatchEvent(new Event('submit'));
    });
});
</script>

<?php include $footerPath; ?>
