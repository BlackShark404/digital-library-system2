<?php
// Include header
use Core\Session;
include $headerPath;
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</h2>
        <a href="/admin/activity-log" class="btn btn-outline-primary">
            <i class="bi bi-activity me-1"></i> View Activity Logs
        </a>
    </div>

    <!-- Welcome Banner -->
    <div class="card bg-gradient-primary mb-4 shadow border-0 rounded-3">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="mb-2 fw-bold text-dark">Welcome, <?= Session::get('first_name') ?>!</h3>
                    <p class="mb-0 text-dark">
                        Manage your digital library system, track user activities, and monitor system performance.
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="/admin/book-management" class="btn btn-light btn-lg rounded-pill">
                        <i class="bi bi-book me-1"></i> Manage Books
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- System Statistics -->
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-light py-3 border-0">
            <h5 class="card-title mb-0">
                <i class="bi bi-graph-up me-2 text-success"></i>System Statistics
            </h5>
        </div>
        <div class="card-body py-4">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="icon-box rounded-circle bg-primary bg-opacity-10 p-3 mx-auto mb-3" style="width: 70px; height: 70px;">
                            <i class="bi bi-people text-primary fs-3 d-flex justify-content-center align-items-center h-100"></i>
                        </div>
                        <h2 class="display-5 fw-bold mb-0"><?= $stats['total_users'] ?? 0 ?></h2>
                        <p class="text-muted">Users</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="icon-box rounded-circle bg-success bg-opacity-10 p-3 mx-auto mb-3" style="width: 70px; height: 70px;">
                            <i class="bi bi-book text-success fs-3 d-flex justify-content-center align-items-center h-100"></i>
                        </div>
                        <h2 class="display-5 fw-bold mb-0"><?= $stats['total_books'] ?? 0 ?></h2>
                        <p class="text-muted">Books</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="icon-box rounded-circle bg-info bg-opacity-10 p-3 mx-auto mb-3" style="width: 70px; height: 70px;">
                            <i class="bi bi-bookmark-check text-info fs-3 d-flex justify-content-center align-items-center h-100"></i>
                        </div>
                        <h2 class="display-5 fw-bold mb-0"><?= $stats['total_sessions'] ?? 0 ?></h2>
                        <p class="text-muted">Reading Sessions</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="icon-box rounded-circle bg-warning bg-opacity-10 p-3 mx-auto mb-3" style="width: 70px; height: 70px;">
                            <i class="bi bi-cart-check text-warning fs-3 d-flex justify-content-center align-items-center h-100"></i>
                        </div>
                        <h2 class="display-5 fw-bold mb-0"><?= $stats['total_purchases'] ?? 0 ?></h2>
                        <p class="text-muted">Purchases</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <a href="/admin/user-management" class="card h-100 shadow-sm hover-lift text-decoration-none border-0 rounded-3 card-hover-primary">
                <div class="card-body p-4 text-center">
                    <div class="icon-box rounded-circle bg-primary bg-opacity-10 p-3 mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-people text-primary fs-3 d-flex justify-content-center align-items-center h-100"></i>
                    </div>
                    <h5 class="card-title mb-2">User Management</h5>
                    <p class="card-text text-muted small">Manage system users</p>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="/admin/book-management" class="card h-100 shadow-sm hover-lift text-decoration-none border-0 rounded-3 card-hover-success">
                <div class="card-body p-4 text-center">
                    <div class="icon-box rounded-circle bg-success bg-opacity-10 p-3 mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-book text-success fs-3 d-flex justify-content-center align-items-center h-100"></i>
                    </div>
                    <h5 class="card-title mb-2">Book Management</h5>
                    <p class="card-text text-muted small">Add and update books</p>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="/admin/reading-sessions" class="card h-100 shadow-sm hover-lift text-decoration-none border-0 rounded-3 card-hover-info">
                <div class="card-body p-4 text-center">
                    <div class="icon-box rounded-circle bg-info bg-opacity-10 p-3 mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-book-half text-info fs-3 d-flex justify-content-center align-items-center h-100"></i>
                    </div>
                    <h5 class="card-title mb-2">Reading Sessions</h5>
                    <p class="card-text text-muted small">Monitor reading activity</p>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="/admin/purchases" class="card h-100 shadow-sm hover-lift text-decoration-none border-0 rounded-3 card-hover-danger">
                <div class="card-body p-4 text-center">
                    <div class="icon-box rounded-circle bg-danger bg-opacity-10 p-3 mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-cart-check text-danger fs-3 d-flex justify-content-center align-items-center h-100"></i>
                    </div>
                    <h5 class="card-title mb-2">Purchases</h5>
                    <p class="card-text text-muted small">Review purchase history</p>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Reading Sessions -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center border-0">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-book-half me-2 text-primary"></i>Recent Reading Sessions
                    </h5>
                    <a href="/admin/reading-sessions" class="btn btn-sm btn-outline-primary rounded-pill">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php if (empty($recent_sessions)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-journal-x text-muted" style="font-size: 3.5rem;"></i>
                                <h5 class="mt-3 mb-1">No reading sessions yet</h5>
                                <p class="text-muted">Reading sessions will appear here as users read books</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($recent_sessions as $session): ?>
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
                                <div class="list-group-item py-3 px-4">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3" style="width: 60px; height: 80px;">
                                            <?php 
                                                $coverPath = !empty($session['b_cover_path']) 
                                                    ? '/assets/images/book-cover/' . $session['b_cover_path'] 
                                                    : '/assets/images/book-cover/default-cover.svg';
                                            ?>
                                            <img src="<?= $coverPath ?>" 
                                                class="img-fluid rounded shadow-sm" alt="<?= htmlspecialchars($session['b_title']) ?>"
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0 text-truncate" style="max-width: 250px;"><?= htmlspecialchars($session['b_title']) ?></h6>
                                                <span class="badge bg-<?= $statusClass ?> ms-2"><?= $statusText ?></span>
                                            </div>
                                            <p class="text-muted small mb-1">
                                                <?= htmlspecialchars($session['ua_first_name'] . ' ' . $session['ua_last_name']) ?> • 
                                                <?= date('M d, Y H:i', strtotime($session['rs_started_at'])) ?>
                                            </p>
                                            <div class="progress mt-1" style="height: 5px;">
                                                <div class="progress-bar bg-<?= $statusClass ?>" role="progressbar" 
                                                    style="width: <?= $progress ?>%;" 
                                                    aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                            <div class="d-flex mt-2">
                                                <small class="text-muted"><?= $progressText ?></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Purchases -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center border-0">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-cart-check me-2 text-info"></i>Recent Purchases
                    </h5>
                    <a href="/admin/purchases" class="btn btn-sm btn-outline-primary rounded-pill">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php if (empty($recent_purchases)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-bag-x text-muted" style="font-size: 3.5rem;"></i>
                                <h5 class="mt-3 mb-1">No purchases yet</h5>
                                <p class="text-muted">Purchases will appear here as users buy books</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($recent_purchases as $purchase): ?>
                                <div class="list-group-item py-3 px-4">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3" style="width: 60px; height: 80px;">
                                            <?php 
                                                $coverPath = !empty($purchase['b_cover_path']) 
                                                    ? '/assets/images/book-cover/' . $purchase['b_cover_path'] 
                                                    : '/assets/images/book-cover/default-cover.svg';
                                            ?>
                                            <img src="<?= $coverPath ?>" class="img-fluid rounded shadow-sm" 
                                                alt="<?= htmlspecialchars($purchase['b_title']) ?>"
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 text-truncate" style="max-width: 250px;"><?= htmlspecialchars($purchase['b_title']) ?></h6>
                                            <p class="text-muted small mb-1">
                                                <?= htmlspecialchars($purchase['ua_first_name'] . ' ' . $purchase['ua_last_name']) ?> • 
                                                <?= date('M d, Y H:i', strtotime($purchase['up_purchased_at'])) ?>
                                            </p>
                                            <div class="d-flex align-items-center mt-2">
                                                <span class="badge bg-success">
                                                    <i class="bi bi-currency-dollar"></i>
                                                    <?= number_format((float)$purchase['b_price'], 2) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="card shadow-sm border-0 rounded-3 mt-4">
        <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center border-0">
            <h5 class="card-title mb-0">
                <i class="bi bi-activity me-2 text-danger"></i>Recent Activity
            </h5>
            <a href="/admin/activity-log" class="btn btn-sm btn-outline-primary rounded-pill">
                View All
            </a>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                <?php if (empty($recent_activity)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-exclamation-circle text-muted" style="font-size: 3.5rem;"></i>
                        <h5 class="mt-3 mb-1">No activity logs yet</h5>
                        <p class="text-muted">System activity will appear here</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_activity as $activity): ?>
                                    <tr>
                                        <td>
                                            <?php if (isset($activity['user_id']) && $activity['user_id']): ?>
                                                <?= htmlspecialchars($activity['first_name'] . ' ' . $activity['last_name']) ?>
                                            <?php else: ?>
                                                <span class="text-muted">System</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><span class="badge bg-light text-dark"><?= $activity['action'] ?></span></td>
                                        <td class="text-truncate" style="max-width: 300px;"><?= htmlspecialchars($activity['description']) ?></td>
                                        <td><small class="text-muted"><?= $activity['time_ago'] ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include $footerPath; ?>
