<?php
include $headerPath;
?>

<div class="container py-4">
    <h1 class="mb-4"><i class="bi bi-book me-2"></i>My Reading Sessions</h1>
    
    <div class="row">
        <!-- Reading Stats -->
        <div class="col-md-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Your Reading Stats</h5>
                    <div class="row g-4">
                        <div class="col-md-3 col-6">
                            <div class="text-center">
                                <div class="display-4 fw-bold text-primary"><?= $stats['books_started'] ?></div>
                                <div class="text-muted">Books Started</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="text-center">
                                <div class="display-4 fw-bold text-success"><?= $stats['books_completed'] ?></div>
                                <div class="text-muted">Books Finished</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="text-center">
                                <div class="display-4 fw-bold text-info"><?= $stats['completion_rate'] ?>%</div>
                                <div class="text-muted">Completion Rate</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="text-center">
                                <div class="display-4 fw-bold text-warning"><?= $stats['books_purchased'] ?></div>
                                <div class="text-muted">Books Purchased</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <div class="col-md-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-0">
                    <div class="alert alert-info border-0 rounded-0 m-0">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="bi bi-info-circle-fill fs-3"></i>
                            </div>
                            <div>
                                <h5>Reading Session Information</h5>
                                <p class="mb-0">You can have up to one active 3-day reading session per book. After expiration, you'll need to purchase the book to continue reading.</p>
                                <p class="mb-0 text-muted mt-1"><small>Note: Only 3 users can read a book simultaneously.</small></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if (empty($sessions)): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <img src="/assets/images/illustrations/no-books.svg" alt="No Reading Sessions" class="img-fluid mb-3" style="max-height: 150px;">
                        <h3>No Reading Sessions Found</h3>
                        <p class="text-muted">You haven't started reading any books yet.</p>
                        <a href="/user/browse-books" class="btn btn-primary">Browse Books</a>
                    </div>
                </div>
            <?php else: ?>
                <h4 class="mb-3">Current & Past Reading Sessions</h4>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
                    <?php foreach ($sessions as $session): ?>
                        <?php 
                            $isExpired = $session['is_expired'];
                            $isPurchased = isset($session['is_purchased']) && $session['is_purchased'];
                            
                            // Set status based on purchase and expiration
                            if ($isPurchased) {
                                $statusClass = 'primary';
                                $statusText = 'Owned';
                            } else {
                                $statusClass = $isExpired ? 'danger' : 'success';
                                $statusText = $isExpired ? 'Expired' : 'Active';
                            }
                            
                            $coverPath = $session['b_cover_path'] 
                                ? '/assets/images/book-cover/' . $session['b_cover_path'] 
                                : '/assets/images/book-cover/default-cover.svg';
                            
                            // Calculate time remaining for non-purchased, active sessions
                            $timeRemaining = '';
                            if (!$isExpired && !$isPurchased) {
                                $now = new DateTime();
                                $expiry = new DateTime($session['rs_expires_at']);
                                $interval = $now->diff($expiry);
                                $days = $interval->d;
                                $hours = $interval->h;
                                $timeRemaining = $days > 0 ? "$days days" : "$hours hrs";
                            }
                        ?>
                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="position-relative">
                                    <img src="<?= $coverPath ?>" alt="<?= $session['b_title'] ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-<?= $statusClass ?> rounded-pill">
                                            <?= $statusText ?>
                                            <?= (!$isExpired && !$isPurchased) ? " ($timeRemaining left)" : "" ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?= $session['b_title'] ?></h5>
                                    <p class="card-text text-muted mb-1"><?= $session['b_author'] ?></p>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-3 mb-2">
                                        <small class="text-muted">
                                            <?php if ($isPurchased): ?>
                                                <i class="bi bi-infinity me-1"></i> Unlimited access
                                            <?php elseif ($isExpired): ?>
                                                Expired: <?= date('M d, Y', strtotime($session['rs_expires_at'])) ?>
                                            <?php else: ?>
                                                Expires: <?= date('M d, Y H:i', strtotime($session['rs_expires_at'])) ?>
                                            <?php endif; ?>
                                        </small>
                                        <small class="text-muted">
                                            <?php if (isset($session['current_page'])): ?>
                                                Page: <?= $session['current_page'] ?> / <?= $session['b_pages'] ?>
                                            <?php else: ?>
                                                Not started
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                    
                                    <div class="progress mb-3" style="height: 8px;">
                                        <?php 
                                            $progress = 0;
                                            if (isset($session['current_page']) && isset($session['b_pages']) && $session['b_pages'] > 0) {
                                                $progress = min(100, round(($session['current_page'] / $session['b_pages']) * 100));
                                            }
                                        ?>
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $progress ?>%;" 
                                            aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <?php if ($isPurchased): ?>
                                            <a href="/reading-session/read-book/<?= $session['rs_id'] ?>" class="btn btn-primary">
                                                <i class="bi bi-book me-1"></i> Continue Reading
                                            </a>
                                        <?php elseif ($isExpired): ?>
                                            <div class="btn-group">
                                                <a href="/api/books/purchase/<?= $session['b_id'] ?>" class="btn btn-outline-primary purchase-book-btn" data-book-id="<?= $session['b_id'] ?>">
                                                    <i class="bi bi-cart-plus me-1"></i> Purchase
                                                </a>
                                                <button class="btn btn-outline-secondary" disabled>
                                                    <i class="bi bi-book me-1"></i> Expired
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <a href="/reading-session/read-book/<?= $session['rs_id'] ?>" class="btn btn-primary">
                                                <i class="bi bi-book me-1"></i> Continue Reading
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <!-- Recommended Books Based on Reading History -->
            <?php if (!empty($suggestions)): ?>
                <h4 class="mb-3 mt-4">Recommended for You</h4>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                    <?php foreach ($suggestions as $book): ?>
                        <?php
                            $coverPath = $book['b_cover_path'] 
                                ? '/assets/images/book-cover/' . $book['b_cover_path'] 
                                : '/assets/images/book-cover/default-cover.svg';
                        ?>
                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="position-relative">
                                    <img src="<?= $coverPath ?>" alt="<?= $book['b_title'] ?>" class="card-img-top" style="height: 180px; object-fit: cover;">
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-info rounded-pill">
                                            Recommended
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title"><?= $book['b_title'] ?></h6>
                                    <p class="card-text small text-muted mb-1"><?= $book['b_author'] ?></p>
                                    <p class="card-text mb-2">
                                        <span class="badge bg-secondary"><?= $book['genre'] ?></span>
                                    </p>
                                    
                                    <div class="d-grid mt-3">
                                        <a href="/user/read?id=<?= $book['b_id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-book me-1"></i> Start Reading
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
include $footerPath;
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle purchase button clicks
    const purchaseButtons = document.querySelectorAll('.purchase-book-btn');
    
    purchaseButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const bookId = this.getAttribute('data-book-id');
            const originalText = this.innerHTML;
            
            // Show loading state
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
            
            // Make purchase request
            fetch(`/api/books/purchase/${bookId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', 'Book purchased successfully! You now have unlimited access to this book.');
                    
                    // Reload the page after a short delay to show updated status
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    // Show error message
                    showToast('error', data.message || 'Failed to purchase the book. Please try again.');
                    
                    // Reset button state
                    this.disabled = false;
                    this.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error purchasing book:', error);
                showToast('error', 'An unexpected error occurred. Please try again.');
                
                // Reset button state
                this.disabled = false;
                this.innerHTML = originalText;
            });
        });
    });
    
    // Toast notification function
    function showToast(type, message) {
        // Create toast container if it doesn't exist
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }
        
        // Create a unique ID for the toast
        const toastId = 'toast-' + Date.now();
        
        // Create the toast element
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
        toast.id = toastId;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        // Set the inner HTML of the toast
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        
        // Add the toast to the container
        toastContainer.appendChild(toast);
        
        // Initialize the Bootstrap toast
        const bsToast = new bootstrap.Toast(toast, {
            autohide: true,
            delay: 3000
        });
        
        // Show the toast
        bsToast.show();
        
        // Remove the toast from the DOM when hidden
        toast.addEventListener('hidden.bs.toast', function() {
            toast.remove();
        });
    }
});
</script>
