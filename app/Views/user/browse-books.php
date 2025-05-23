<?php
// Include header
include $headerPath;

// Books data is now passed from the controller
// Filter parameters are now passed from the controller
?>

<div class="container my-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="mb-3">Browse Books</h1>

            <!-- Search and Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="/user/browse-books" class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" placeholder="Search titles or authors" name="search" value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="genre">
                                <option value="">All Genres</option>
                                <?php foreach ($genres as $g): ?>
                                    <option value="<?php echo htmlspecialchars($g); ?>" <?php echo ($g === $genre) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($g); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="sort">
                                <option value="title_asc" <?php echo ($sort === 'title_asc') ? 'selected' : ''; ?>>Title (A-Z)</option>
                                <option value="title_desc" <?php echo ($sort === 'title_desc') ? 'selected' : ''; ?>>Title (Z-A)</option>
                                <option value="author_asc" <?php echo ($sort === 'author_asc') ? 'selected' : ''; ?>>Author (A-Z)</option>
                                <option value="author_desc" <?php echo ($sort === 'author_desc') ? 'selected' : ''; ?>>Author (Z-A)</option>
                                <option value="published_asc" <?php echo ($sort === 'published_asc') ? 'selected' : ''; ?>>Published (Oldest)</option>
                                <option value="published_desc" <?php echo ($sort === 'published_desc') ? 'selected' : ''; ?>>Published (Newest)</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                        <div class="col-md-1">
                            <button type="button" id="resetFiltersBtnUser" class="btn btn-secondary w-100">Reset</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Book Grid -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
                <?php
                if (count($books) > 0) {
                    foreach ($books as $book) {
                ?>
                        <div class="col">
                            <div class="card h-100 shadow-sm">
                                <div class="position-relative">
                                    <img src="<?php echo !empty($book['cover_image']) ? htmlspecialchars($book['cover_image']) : '../assets/images/book-cover/default-cover.svg'; ?>"
                                        class="card-img-top"
                                        alt="<?php echo htmlspecialchars($book['title']); ?>"
                                        style="height: 270px; object-fit: cover;">

                                    <!-- Wishlist button -->
                                    <button type="button"
                                        class="btn btn-sm position-absolute top-0 end-0 m-2 text-danger bg-light rounded-circle p-2 wishlist-toggle"
                                        data-book-id="<?php echo $book['id']; ?>"
                                        data-action="<?php echo $book['in_wishlist'] ? 'remove' : 'add'; ?>"
                                        data-bs-toggle="tooltip"
                                        data-bs-title="<?php echo $book['in_wishlist'] ? 'Remove from wishlist' : 'Add to wishlist'; ?>">
                                        <i class="bi bi-<?php echo $book['in_wishlist'] ? 'heart-fill' : 'heart'; ?>"></i>
                                    </button>
                                </div>

                                <div class="card-body">
                                    <h5 class="card-title text-truncate" title="<?php echo htmlspecialchars($book['title']); ?>">
                                        <?php echo htmlspecialchars($book['title']); ?>
                                    </h5>
                                    <p class="card-text text-muted mb-1">
                                        by <?php echo htmlspecialchars($book['author']); ?>
                                    </p>
                                    <p class="card-text mb-2">
                                        <?php if (!empty($book['genres'])): ?>
                                            <?php foreach ($book['genres'] as $genre): ?>
                                                <span class="badge bg-secondary me-1"><?php echo htmlspecialchars($genre['name']); ?></span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Uncategorized</span>
                                        <?php endif; ?>
                                    </p>
                                    <p class="card-text small text-truncate" title="<?php echo htmlspecialchars($book['description']); ?>"><?php echo htmlspecialchars($book['description']); ?></p>
                                </div>
                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">
                                        <?php
                                        if ($book['price'] > 0) {
                                            echo '$' . number_format($book['price'], 2);
                                        } else {
                                            echo '<span class="text-success">Free</span>';
                                        }
                                        ?>
                                    </span>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-primary view-book-details" data-book-id="<?php echo $book['id']; ?>">Details</button>
                                        <a href="/user/read?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-primary read-book-btn" data-book-id="<?php echo $book['id']; ?>">Read</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                } else {
                    ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No books found matching your criteria. Try adjusting your filters.
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>

<!-- Book Details Modal -->
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
                <span id="purchaseStatus" class="badge bg-success me-2 d-none">Purchased</span>
                <button type="button" class="btn btn-success" id="buyBookBtn" data-book-id=""><i class="bi bi-cart-plus me-2"></i>Buy Now</button>
            </div>
        </div>
    </div>
</div>

<!-- Reading Session Loading Modal -->
<div class="modal fade" id="readingSessionLoadingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5 class="mb-3">Checking availability...</h5>
                <p class="text-muted">We're checking if this book is available for you to read.</p>
                <div id="readSessionMessage" class="alert alert-info mt-3 d-none">
                    <!-- Dynamic message will be inserted here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/utility/toast-notifications.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle wishlist toggles
        const wishlistButtons = document.querySelectorAll('.wishlist-toggle');
        wishlistButtons.forEach(button => {
            button.addEventListener('click', function() {
                const bookId = this.getAttribute('data-book-id');
                const action = this.getAttribute('data-action');
                const icon = this.querySelector('i');

                // Send AJAX request
                fetch('/user/wishlist/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        book_id: bookId,
                        action: action
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Toggle icon and action
                        if (action === 'add') {
                            icon.classList.remove('bi-heart');
                            icon.classList.add('bi-heart-fill');
                            this.setAttribute('data-action', 'remove');
                            this.setAttribute('data-bs-title', 'Remove from wishlist');
                            showToast('success', 'Success', 'Book added to wishlist');
                        } else {
                            icon.classList.remove('bi-heart-fill');
                            icon.classList.add('bi-heart');
                            this.setAttribute('data-action', 'add');
                            this.setAttribute('data-bs-title', 'Add to wishlist');
                            showToast('info', 'Success', 'Book removed from wishlist');
                        }
                        
                        // Update tooltip
                        let tooltip = bootstrap.Tooltip.getInstance(this);
                        if (tooltip) {
                            tooltip.dispose();
                        }
                        new bootstrap.Tooltip(this);
                    } else {
                        // Show error toast
                        showToast('error', 'Error', data.message || 'Failed to update wishlist');
                    }
                })
                .catch(error => {
                    console.error('Error toggling wishlist:', error);
                    showToast('error', 'Error', 'Failed to update wishlist. Please try again.');
                });
            });
        });

        // View book details modal
        const viewButtons = document.querySelectorAll('.view-book-details');
        const buyBookBtn = document.getElementById('buyBookBtn');
        const purchaseStatus = document.getElementById('purchaseStatus');
        
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const bookId = this.getAttribute('data-book-id');
                
                // Fetch book details
                fetch(`/api/books/${bookId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const book = data.data;
                        
                        // Populate modal
                        document.getElementById('modalCoverImage').src = book.cover_url;
                        document.getElementById('modalTitle').textContent = book.b_title;
                        document.getElementById('modalAuthor').textContent = `by ${book.b_author}`;
                        
                        // Handle genres - display multiple badges
                        const genreContainer = document.getElementById('modalGenre');
                        genreContainer.innerHTML = '';
                        
                        if (book.genres && book.genres.length > 0) {
                            book.genres.forEach(genre => {
                                const badge = document.createElement('span');
                                badge.className = 'badge bg-secondary me-1';
                                badge.textContent = genre.g_name;
                                genreContainer.appendChild(badge);
                            });
                        } else {
                            genreContainer.textContent = book.genre_name || 'Uncategorized';
                        }
                        
                        document.getElementById('modalPublisher').textContent = book.b_publisher || 'N/A';
                        document.getElementById('modalPublicationDate').textContent = book.b_publication_date || 'N/A';
                        document.getElementById('modalIsbn').textContent = book.b_isbn || 'N/A';
                        document.getElementById('modalPages').textContent = book.b_pages || 'N/A';
                        
                        // Format price
                        const price = parseFloat(book.b_price);
                        document.getElementById('modalPrice').textContent = price > 0 
                            ? `$${price.toFixed(2)}` 
                            : 'Free';
                            
                        document.getElementById('modalDescription').textContent = book.b_description || 'No description available.';
                        
                        // Set book ID for buy button
                        buyBookBtn.setAttribute('data-book-id', book.b_id);
                        
                        // Check if book is already purchased
                        fetch(`/reading-session/check-availability/${book.b_id}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(availData => {
                            if (availData.success) {
                                if (availData.data.is_purchased) {
                                    // Book already purchased
                                    buyBookBtn.classList.add('d-none');
                                    purchaseStatus.classList.remove('d-none');
                                } else {
                                    // Book not purchased
                                    buyBookBtn.classList.remove('d-none');
                                    purchaseStatus.classList.add('d-none');
                                    
                                    // Disable buy button for free books
                                    if (price <= 0) {
                                        buyBookBtn.disabled = true;
                                        buyBookBtn.textContent = 'Free Book';
                                    } else {
                                        buyBookBtn.disabled = false;
                                        buyBookBtn.innerHTML = '<i class="bi bi-cart-plus me-2"></i>Buy Now';
                                    }
                                }
                            }
                        });
                        
                        // Show modal
                        new bootstrap.Modal(document.getElementById('bookDetailModal')).show();
                    }
                })
                .catch(error => {
                    console.error('Error fetching book details:', error);
                });
            });
        });
        
        // Handle book purchase
        buyBookBtn.addEventListener('click', function() {
            const bookId = this.getAttribute('data-book-id');
            
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
                    // Show success message
                    showToast('success', 'Success', 'Book purchased successfully! You can now read it anytime.');
                    
                    // Update UI to show purchased state
                    buyBookBtn.classList.add('d-none');
                    purchaseStatus.classList.remove('d-none');
                    
                    // Update the read button for this book to show it's purchased
                    const readButton = document.querySelector(`.read-book-btn[data-book-id="${bookId}"]`);
                    if (readButton) {
                        readButton.classList.add('purchased');
                    }
                } else {
                    // Show error message
                    showToast('error', 'Error', data.message || 'Failed to purchase the book. Please try again.');
                    
                    // Reset button state
                    buyBookBtn.disabled = false;
                    buyBookBtn.innerHTML = '<i class="bi bi-cart-plus me-2"></i>Buy Now';
                }
            })
            .catch(error => {
                console.error('Error purchasing book:', error);
                showToast('error', 'Error', 'An unexpected error occurred. Please try again.');
                
                // Reset button state
                buyBookBtn.disabled = false;
                buyBookBtn.innerHTML = '<i class="bi bi-cart-plus me-2"></i>Buy Now';
            });
        });

        // Reading session loading modal
        const loadingModal = new bootstrap.Modal(document.getElementById('readingSessionLoadingModal'));
        const messageContainer = document.getElementById('readSessionMessage');

        // Check book availability before accessing
        const readButtons = document.querySelectorAll('.read-book-btn');
        readButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const bookId = this.getAttribute('data-book-id');
                const href = this.getAttribute('href');
                
                // Show loading modal
                messageContainer.classList.add('d-none');
                loadingModal.show();
                
                // Check availability
                fetch(`/reading-session/check-availability/${bookId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (data.data.is_available) {
                                // Available to read, redirect after a short delay
                                messageContainer.textContent = "Book is available. Taking you to the reader...";
                                messageContainer.className = "alert alert-success mt-3";
                                messageContainer.classList.remove('d-none');
                                
                                setTimeout(() => {
                                    loadingModal.hide();
                                    window.location.href = href;
                                }, 1000);
                            } else if (data.data.is_previous_session_expired) {
                                // Previously read but expired
                                messageContainer.textContent = "Your 3-day reading period for this book has expired. Please purchase to continue reading.";
                                messageContainer.className = "alert alert-warning mt-3";
                                messageContainer.classList.remove('d-none');
                                
                                setTimeout(() => loadingModal.hide(), 3000);
                            } else {
                                // Not available due to concurrent readers
                                messageContainer.textContent = `This book has reached the maximum number of concurrent readers (${data.data.active_sessions_count}/${data.data.max_sessions}). Please try again later or purchase the book.`;
                                messageContainer.className = "alert alert-warning mt-3";
                                messageContainer.classList.remove('d-none');
                                
                                setTimeout(() => loadingModal.hide(), 3000);
                            }
                        } else {
                            // Error checking availability
                            messageContainer.textContent = "Error checking book availability. Please try again.";
                            messageContainer.className = "alert alert-danger mt-3";
                            messageContainer.classList.remove('d-none');
                            
                            setTimeout(() => loadingModal.hide(), 3000);
                        }
                    })
                    .catch(error => {
                        console.error('Error checking availability:', error);
                        
                        messageContainer.textContent = "Error checking book availability. Please try again.";
                        messageContainer.className = "alert alert-danger mt-3";
                        messageContainer.classList.remove('d-none');
                        
                        setTimeout(() => loadingModal.hide(), 3000);
                    });
            });
        });
        
        // Initialize tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        
        // Reset filters button
        document.getElementById('resetFiltersBtnUser').addEventListener('click', function() {
            window.location.href = '/user/browse-books';
        });

        // Format Date (if not already present)
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return date.toLocaleDateString(undefined, options);
        }

        // Toast notification function
        function showToast(type = 'info', title = 'Info', message = '') {
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
                        <strong>${title}</strong>
                        <p>${message}</p>
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

<?php
// Include footer
include $footerPath;
?>