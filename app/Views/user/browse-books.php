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
                        <div class="col-md-5">
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
                                        style="height: 250px; object-fit: cover;">

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
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($book['genre']); ?></span>
                                    </p>
                                    <p class="card-text small text-truncate" title="<?php echo htmlspecialchars($book['description']); ?>">
                                        <?php echo htmlspecialchars($book['description']); ?>
                                    </p>
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
                                        <a href="/user/book-details?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline-primary">Details</a>
                                        <a href="/user/read?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-primary">Read</a>
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

            <!-- Pagination -->
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

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
                            
                            // Show toast notification
                            showToast('Added to wishlist', 'success');
                        } else {
                            icon.classList.remove('bi-heart-fill');
                            icon.classList.add('bi-heart');
                            this.setAttribute('data-action', 'add');
                            this.setAttribute('data-bs-title', 'Add to wishlist');
                            
                            // Show toast notification
                            showToast('Removed from wishlist', 'info');
                        }
                    } else {
                        // Show error notification
                        showToast(data.message || 'Failed to update wishlist', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred', 'danger');
                });
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

<?php
// Include footer
include $footerPath;
?>