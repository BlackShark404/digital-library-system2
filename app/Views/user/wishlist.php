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
                                <button type="button" class="btn btn-outline-secondary view-book-details" data-book-id="<?php echo $book['b_id']; ?>">
                                    <i class="bi bi-info-circle"></i> Details
                                </button>
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

<!-- Book Details Modal (Copied from browse-books.php) -->
<div class="modal fade" id="bookDetailModal" tabindex="-1" aria-labelledby="bookDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookDetailModalLabel">Book Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <img id="modalCoverImage" src="" alt="Book Cover" class="img-fluid border rounded" style="max-height: 280px;">
                    </div>
                    <div class="col-md-8">
                        <h3 id="modalTitle" class="fw-bold"></h3>
                        <h5 id="modalAuthor" class="text-secondary mb-3"></h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Genre:</strong> <span id="modalGenre"></span></p>
                                <p><strong>Publisher:</strong> <span id="modalPublisher"></span></p>
                                <p><strong>Publication Date:</strong> <span id="modalPublicationDate"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>ISBN:</strong> <span id="modalIsbn"></span></p>
                                <p><strong>Pages:</strong> <span id="modalPages"></span></p>
                                <p><strong>Price:</strong> <span id="modalPrice" class="fw-bold"></span></p>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="mt-3">
                    <h5>Description</h5>
                    <div id="modalDescription" class="border rounded p-3 bg-light" style="min-height: 100px;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="buyBookBtnModal"><i class="bi bi-cart-plus me-2"></i>Buy Now</button>
            </div>
        </div>
    </div>
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
        
        // --- Additions for Book Detail Modal ---
        const bookDetailModal = new bootstrap.Modal(document.getElementById('bookDetailModal'));
        
        document.querySelectorAll('.view-book-details').forEach(button => {
            button.addEventListener('click', function() {
                const bookId = this.getAttribute('data-book-id');
                fetchBookDetailsAndShowModal(bookId);
            });
        });

        function fetchBookDetailsAndShowModal(bookId) {
            document.getElementById('modalTitle').textContent = 'Loading...';
            document.getElementById('modalCoverImage').src = ''; // Reset image
            document.getElementById('modalAuthor').textContent = '';
            document.getElementById('modalGenre').textContent = '';
            document.getElementById('modalPublisher').textContent = '';
            document.getElementById('modalPublicationDate').textContent = '';
            document.getElementById('modalIsbn').textContent = '';
            document.getElementById('modalPages').textContent = '';
            document.getElementById('modalPrice').textContent = '';
            document.getElementById('modalDescription').innerHTML = '';


            fetch(`/api/books/${bookId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.data) {
                    const book = data.data;
                    document.getElementById('modalCoverImage').src = book.cover_url || '/assets/images/book-cover/default-cover.svg';
                    document.getElementById('modalTitle').textContent = book.b_title || 'N/A';
                    document.getElementById('modalAuthor').textContent = book.b_author ? `by ${book.b_author}` : 'N/A';
                    document.getElementById('modalGenre').textContent = book.genre || 'N/A';
                    document.getElementById('modalPublisher').textContent = book.b_publisher || 'N/A';
                    document.getElementById('modalPublicationDate').textContent = book.b_publication_date ? formatDate(book.b_publication_date) : 'N/A';
                    document.getElementById('modalIsbn').textContent = book.b_isbn || 'N/A';
                    document.getElementById('modalPages').textContent = book.b_pages || 'N/A';
                    document.getElementById('modalPrice').textContent = book.b_price ? `$${parseFloat(book.b_price).toFixed(2)}` : 'Free';
                    document.getElementById('modalDescription').innerHTML = book.b_description ? book.b_description.replace(/\\n/g, '<br>') : 'No description available.';
                    
                    document.getElementById('buyBookBtnModal').setAttribute('data-book-id', bookId); // Changed ID to buyBookBtnModal
                    bookDetailModal.show();
                } else {
                    showToast(data.message || 'Failed to load book details.', 'danger');
                }
            })
            .catch(error => {
                console.error('Error fetching book details:', error);
                showToast('Error fetching book details. Please try again.', 'danger');
                document.getElementById('modalTitle').textContent = 'Error';
            });
        }

        // Event listener for the modal's Buy button
        const buyBookBtnModal = document.getElementById('buyBookBtnModal');
        if (buyBookBtnModal) {
            buyBookBtnModal.addEventListener('click', function() {
                const bookId = this.getAttribute('data-book-id');
                console.log('Modal Buy button clicked for book ID:', bookId);
                // This can be a direct purchase or redirect to a purchase page/cart
                // For now, we'll try to submit the existing purchase form if available for that book
                // Or, more simply, redirect to a purchase initiation URL or trigger another AJAX
                
                // Attempt to find the purchase form for this book on the page and submit it
                // This is a bit of a workaround as the buy button is now in a generic modal
                // A more robust solution would be an AJAX purchase or a dedicated purchase page
                let purchaseFormFound = false;
                document.querySelectorAll('form[action="/user/purchase/add"]').forEach(form => {
                    const formBookIdInput = form.querySelector('input[name="book_id"]');
                    if (formBookIdInput && formBookIdInput.value == bookId) {
                        form.submit();
                        purchaseFormFound = true;
                    }
                });

                if (!purchaseFormFound) {
                     // Fallback if direct form submission isn't feasible/found
                    showToast(`"Buy" clicked for book ID: ${bookId}. Purchase form not found on page. Implement direct purchase.`, 'info');
                }
                bookDetailModal.hide();
            });
        }


        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return date.toLocaleDateString(undefined, options);
        }
        // --- End of Additions ---

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