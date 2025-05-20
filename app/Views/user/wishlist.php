<?php
include $headerPath;

// Wishlist data is now passed from the controller
?>

<!-- Main content area -->
<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">My Wishlist</h1>
            <p class="text-muted">Books you've saved for later</p>
        </div>
        
    </div>

    <?php if (empty($wishlist_books)): ?>
        <!-- Empty state -->
        <div class="card shadow-sm border-0 p-5 text-center">
            <div class="py-5">
                <i class="bi bi-heart text-muted" style="font-size: 3rem;"></i>
                <h3 class="mt-4">Your wishlist is empty</h3>
                <p class="text-muted mb-4">Save books you're interested in to your wishlist for easy access later.</p>
                <a href="/user/browse-books" class="btn btn-primary">
                    <i class="bi bi-search"></i> Discover Books
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Wishlist items -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($wishlist_books as $book): ?>
                <div class="col wishlist-item" data-wishlist-id="<?php echo $book['wl_id']; ?>">
                    <div class="card h-100 shadow-sm">
                        <div class="position-relative">
                            <img src="<?php echo !empty($book['cover_image']) ? htmlspecialchars($book['cover_image']) : '/assets/images/book-cover/default-cover.svg'; ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($book['title']); ?>" 
                                 style="height: 250px; object-fit: cover;">
                            <button type="button" 
                                   class="btn btn-sm btn-light rounded-circle position-absolute top-0 end-0 m-2 remove-wishlist-btn" 
                                   data-wishlist-id="<?php echo $book['wl_id']; ?>"
                                   data-bs-toggle="tooltip" 
                                   title="Remove from wishlist">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <span class="badge bg-secondary mb-2"><?php echo htmlspecialchars($book['genre']); ?></span>
                            <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                            <p class="card-text text-muted">by <?php echo htmlspecialchars($book['author']); ?></p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <span class="fw-bold">$<?php echo number_format($book['price'], 2); ?></span>
                                </div>
                                <small class="text-muted">Published: <?php echo date('M Y', strtotime($book['publication_date'])); ?></small>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <div class="d-grid gap-2 d-md-flex justify-content-between">
                                <a href="/user/book-details?id=<?php echo $book['b_id']; ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-info-circle"></i> Details
                                </a>
                                <form method="post" action="/user/purchase/add" class="d-inline">
                                    <input type="hidden" name="book_id" value="<?php echo $book['b_id']; ?>">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-cart-plus"></i> Purchase
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle remove wishlist buttons
        const removeButtons = document.querySelectorAll('.remove-wishlist-btn');
        removeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const wishlistId = this.getAttribute('data-wishlist-id');
                
                if (confirm('Are you sure you want to remove this book from your wishlist?')) {
                    // Send AJAX request
                    fetch('/user/wishlist/remove', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            wishlist_id: parseInt(wishlistId)
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the item from the DOM
                            const wishlistItem = document.querySelector(`.wishlist-item[data-wishlist-id="${wishlistId}"]`);
                            if (wishlistItem) {
                                wishlistItem.remove();
                                
                                // Show toast notification
                                showToast('Book removed from wishlist', 'info');
                                
                                // If no items left, refresh page to show empty state
                                const remainingItems = document.querySelectorAll('.wishlist-item');
                                if (remainingItems.length === 0) {
                                    window.location.reload();
                                }
                            }
                        } else {
                            // Show error notification
                            showToast(data.message || 'Failed to remove from wishlist', 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('An error occurred', 'danger');
                    });
                }
            });
        });
        
        // Toast notification function
        function showToast(message, type = 'info') {
            // Create toast container if it doesn't exist
            let toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
                document.body.appendChild(toastContainer);
            }

            // Create toast element
            const toastEl = document.createElement('div');
            toastEl.className = `toast align-items-center text-white bg-${type} border-0`;
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            toastEl.setAttribute('aria-atomic', 'true');

            // Toast content
            toastEl.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;

            // Add to container
            toastContainer.appendChild(toastEl);

            // Initialize and show toast
            const toast = new bootstrap.Toast(toastEl, {
                delay: 3000
            });
            toast.show();

            // Remove from DOM after hiding
            toastEl.addEventListener('hidden.bs.toast', function() {
                toastEl.remove();
            });
        }
    });
</script>

<?php include $footerPath; ?>