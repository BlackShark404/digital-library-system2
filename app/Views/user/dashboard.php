<?php
// Include header
use Core\Session;
include $headerPath;
?>

<div class="container py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h2>
        
    </div>

    <!-- Welcome Banner -->
    <div class="card bg-gradient-primary text-white mb-4 shadow border-0 rounded-3">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="mb-2 fw-bold">Welcome back, <?= Session::get('first_name') ?>!</h3>
                    <p class="mb-0 opacity-90">
                        Track your reading progress, discover new books, and manage your library all in one place.
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="/user/browse-books" class="btn btn-light btn-lg rounded-pill">
                        <i class="bi bi-book me-1"></i> Browse Books
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Reading Statistics -->
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-light py-3 border-0">
            <h5 class="card-title mb-0">
                <i class="bi bi-graph-up me-2 text-success"></i>Reading Statistics
            </h5>
        </div>
        <div class="card-body py-4">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="icon-box rounded-circle bg-primary bg-opacity-10 p-3 mx-auto mb-3" style="width: 70px; height: 70px;">
                            <i class="bi bi-book text-primary fs-3 d-flex justify-content-center align-items-center h-100"></i>
                        </div>
                        <h2 class="display-5 fw-bold mb-0"><?= $reading_stats['books_started'] ?></h2>
                        <p class="text-muted">Books Started</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="icon-box rounded-circle bg-success bg-opacity-10 p-3 mx-auto mb-3" style="width: 70px; height: 70px;">
                            <i class="bi bi-check-circle text-success fs-3 d-flex justify-content-center align-items-center h-100"></i>
                        </div>
                        <h2 class="display-5 fw-bold mb-0"><?= $reading_stats['books_completed'] ?></h2>
                        <p class="text-muted">Books Completed</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="icon-box rounded-circle bg-info bg-opacity-10 p-3 mx-auto mb-3" style="width: 70px; height: 70px;">
                            <i class="bi bi-cart-check text-info fs-3 d-flex justify-content-center align-items-center h-100"></i>
                        </div>
                        <h2 class="display-5 fw-bold mb-0"><?= $reading_stats['books_purchased'] ?></h2>
                        <p class="text-muted">Purchases</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="icon-box rounded-circle bg-warning bg-opacity-10 p-3 mx-auto mb-3" style="width: 70px; height: 70px;">
                            <i class="bi bi-star-half text-warning fs-3 d-flex justify-content-center align-items-center h-100"></i>
                        </div>
                        <h2 class="display-5 fw-bold mb-0"><?= $reading_stats['completion_rate'] ?>%</h2>
                        <p class="text-muted">Completion Rate</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <a href="/user/browse-books" class="card h-100 shadow-sm hover-lift text-decoration-none border-0 rounded-3 card-hover-primary">
                <div class="card-body p-4 text-center">
                    <div class="icon-box rounded-circle bg-primary bg-opacity-10 p-3 mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-book text-primary fs-3 d-flex justify-content-center align-items-center h-100"></i>
                    </div>
                    <h5 class="card-title mb-2">Browse Library</h5>
                    <p class="card-text text-muted small">Explore our collection of books</p>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="/user/reading-sessions" class="card h-100 shadow-sm hover-lift text-decoration-none border-0 rounded-3 card-hover-success">
                <div class="card-body p-4 text-center">
                    <div class="icon-box rounded-circle bg-success bg-opacity-10 p-3 mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-book-half text-success fs-3 d-flex justify-content-center align-items-center h-100"></i>
                    </div>
                    <h5 class="card-title mb-2">Reading Sessions</h5>
                    <p class="card-text text-muted small">Continue where you left off</p>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="/user/purchases" class="card h-100 shadow-sm hover-lift text-decoration-none border-0 rounded-3 card-hover-info">
                <div class="card-body p-4 text-center">
                    <div class="icon-box rounded-circle bg-info bg-opacity-10 p-3 mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-bag text-info fs-3 d-flex justify-content-center align-items-center h-100"></i>
                    </div>
                    <h5 class="card-title mb-2">My Purchases</h5>
                    <p class="card-text text-muted small">View your purchased books</p>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="/user/wishlist" class="card h-100 shadow-sm hover-lift text-decoration-none border-0 rounded-3 card-hover-danger">
                <div class="card-body p-4 text-center">
                    <div class="icon-box rounded-circle bg-danger bg-opacity-10 p-3 mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-heart text-danger fs-3 d-flex justify-content-center align-items-center h-100"></i>
                    </div>
                    <h5 class="card-title mb-2">Wishlist</h5>
                    <p class="card-text text-muted small">Books you want to read</p>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Activity -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center border-0">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-activity me-2 text-primary"></i>Recent Activity
                    </h5>
                    <a href="/user/reading-sessions" class="btn btn-sm btn-outline-primary rounded-pill">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php if (empty($recent_activity)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-journal-x text-muted" style="font-size: 3.5rem;"></i>
                                <h5 class="mt-3 mb-1">No reading activity yet</h5>
                                <p class="text-muted">Start reading to track your progress</p>
                                <a href="/user/browse-books" class="btn btn-primary mt-2">
                                    <i class="bi bi-book me-1"></i>Find Books to Read
                                </a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($recent_activity as $activity): ?>
                                <div class="list-group-item py-3 px-4">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3" style="width: 60px; height: 80px;">
                                            <?php if (!empty($activity['b_cover_path'])): ?>
                                                <img src="/assets/images/book-cover/<?= $activity['b_cover_path'] ?>" 
                                                    class="img-fluid rounded shadow-sm" alt="<?= htmlspecialchars($activity['b_title']) ?>"
                                                    style="width: 100%; height: 100%; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center h-100">
                                                    <i class="bi bi-book text-primary"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0 text-truncate" style="max-width: 250px;"><?= htmlspecialchars($activity['b_title']) ?></h6>
                                            </div>
                                            <p class="text-muted small mb-1"><?= $activity['activity_text'] ?></p>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-light text-dark">
                                                    <i class="bi bi-clock me-1"></i><?= $activity['time_ago'] ?>
                                                </span>
                                                <a href="/reading-session/read-book/<?= $activity['b_id'] ?>" class="btn btn-sm btn-outline-primary ms-2 rounded-pill">
                                                    Continue Reading
                                                </a>
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
                        <i class="bi bi-bag me-2 text-info"></i>Recent Purchases
                    </h5>
                    <a href="/user/purchases" class="btn btn-sm btn-outline-primary rounded-pill">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php if (empty($recent_purchases)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-bag-x text-muted" style="font-size: 3.5rem;"></i>
                                <h5 class="mt-3 mb-1">No purchases yet</h5>
                                <p class="text-muted">Your purchased books will appear here</p>
                                <a href="/user/browse-books" class="btn btn-primary mt-2">
                                    <i class="bi bi-book me-1"></i>Browse Books
                                </a>
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
                                        <div>
                                            <h6 class="mb-0 text-truncate" style="max-width: 250px;"><?= htmlspecialchars($purchase['b_title']) ?></h6>
                                            <p class="text-muted small mb-1">By <?= htmlspecialchars($purchase['b_author']) ?></p>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-success me-2">
                                                    <i class="bi bi-currency-dollar"></i>
                                                    <?= number_format((float)$purchase['b_price'], 2) ?>
                                                </span>
                                                <small class="text-muted me-2">
                                                    <i class="bi bi-calendar-date me-1"></i>
                                                    <?= date('M d, Y', strtotime($purchase['up_purchased_at'])) ?>
                                                </small>
                                                <div>
                                                    <a href="/user/download-book/<?= $purchase['b_id'] ?>" class="btn btn-sm btn-outline-secondary rounded-pill">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                    <a href="/reading-session/read-book/<?= $purchase['b_id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                                                        <i class="bi bi-book"></i>
                                                    </a>
                                                </div>
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
</div>

<style>
/* Custom styles for hover effects */
.hover-lift {
    transition: transform 0.2s, box-shadow 0.2s;
}
.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
.card-hover-primary:hover {
    border-bottom: 3px solid var(--bs-primary) !important;
}
.card-hover-success:hover {
    border-bottom: 3px solid var(--bs-success) !important;
}
.card-hover-info:hover {
    border-bottom: 3px solid var(--bs-info) !important;
}
.card-hover-danger:hover {
    border-bottom: 3px solid var(--bs-danger) !important;
}
.bg-gradient-primary {
    background: linear-gradient(45deg, var(--bs-primary), #5c7cfa);
}
/* Larger icon size in empty states */
.list-group-item .btn-outline-primary,
.list-group-item .btn-outline-secondary {
    margin: 0 2px;
}
</style>

<?php
// Include footer
include $footerPath;
?>
