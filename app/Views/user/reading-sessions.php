<?php
include $headerPath;
?>

<div class="container my-4">
    <div class="row">
        <div class="col">
            <h1 class="mb-4"><i class="bi bi-book-half me-2"></i>My Reading Sessions</h1>
            
            <!-- Alert for messages -->
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?= $_SESSION['error_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?= $_SESSION['success_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            
            <!-- Reading Sessions -->
            <div class="card">
                <div class="card-body">
                    <?php if (empty($sessions)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-journal-text display-1 text-muted"></i>
                            <h3 class="mt-3">No Reading Sessions Found</h3>
                            <p class="text-muted">You don't have any active reading sessions.</p>
                            <a href="/user/browse-books" class="btn btn-primary mt-3">Browse Books</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Book</th>
                                        <th>Started</th>
                                        <th>Expires</th>
                                        <th>Progress</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($sessions as $session): ?>
                                        <tr class="<?= $session['status'] === 'expired' ? 'table-secondary' : '' ?>">
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center">
                                                    <img src="<?= $session['b_cover_path'] ? '/assets/images/book-cover/' . $session['b_cover_path'] : '/assets/images/book-cover/default-cover.svg' ?>" 
                                                         alt="<?= htmlspecialchars($session['b_title']) ?>" 
                                                         style="width: 40px; height: 60px; object-fit: cover;" 
                                                         class="me-2">
                                                    <div>
                                                        <h6 class="mb-0"><?= htmlspecialchars($session['b_title']) ?></h6>
                                                        <small class="text-muted">by <?= htmlspecialchars($session['b_author']) ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <?= date('M d, Y', strtotime($session['rs_started_at'])) ?>
                                            </td>
                                            <td class="align-middle">
                                                <?= date('M d, Y', strtotime($session['rs_expires_at'])) ?>
                                            </td>
                                            <td class="align-middle">
                                                <?php if (isset($session['current_page'])): ?>
                                                    <div class="progress" style="height: 10px;">
                                                        <div class="progress-bar bg-success" 
                                                             role="progressbar" 
                                                             style="width: <?= min(100, ($session['current_page'] / max(1, $session['b_pages'])) * 100) ?>%"
                                                             aria-valuenow="<?= $session['current_page'] ?>" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="<?= $session['b_pages'] ?>">
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">Page <?= $session['current_page'] ?> of <?= $session['b_pages'] ?: 'Unknown' ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">Not started</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle">
                                                <?php if ($session['is_purchased']): ?>
                                                    <span class="badge bg-success">Purchased</span>
                                                <?php elseif ($session['status'] === 'active'): ?>
                                                    <span class="badge bg-primary">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Expired</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle">
                                                <?php if ($session['status'] === 'active' || $session['is_purchased']): ?>
                                                    <a href="/user/reading-sessions/view?id=<?= $session['rs_id'] ?>" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-book me-1"></i> Read
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-secondary" disabled>
                                                        <i class="bi bi-lock me-1"></i> Expired
                                                    </button>
                                                <?php endif; ?>
                                            </td>
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
</div>

<?php
include $footerPath;
?> 