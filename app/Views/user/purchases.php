<?php
include $headerPath;
?>

<!-- Main Content -->
<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h2><i class="bi bi-bag-check me-2"></i>My Purchases</h2>
            <p class="text-muted">View and manage your book purchases</p>
        </div>
    </div>

    <!-- Purchase Filters -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="searchPurchases" class="form-label">Search Purchases</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" id="searchPurchases" placeholder="Search by book title">
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="filterDate" class="form-label">Filter by Date</label>
                    <select class="form-select" id="filterDate">
                        <option value="all">All Time</option>
                        <option value="last30">Last 30 Days</option>
                        <option value="last90">Last 90 Days</option>
                        <option value="lastYear">Last Year</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sortBy" class="form-label">Sort By</label>
                    <select class="form-select" id="sortBy">
                        <option value="date_desc">Date (Newest First)</option>
                        <option value="date_asc">Date (Oldest First)</option>
                        <option value="title">Title (A-Z)</option>
                        <option value="price_desc">Price (High to Low)</option>
                        <option value="price_asc">Price (Low to High)</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i> Apply
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchases List -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Book</th>
                            <th scope="col">Date</th>
                            <th scope="col">Price</th>
                            <th scope="col">Order ID</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($purchased_books)) {
                            foreach ($purchased_books as $index => $book) {
                                // Generate a dummy order ID using the purchase ID and date
                                $orderId = 'ORD-' . str_pad($book['up_id'], 5, '0', STR_PAD_LEFT);
                                
                                // Format the cover path
                                $coverPath = !empty($book['b_cover_path']) 
                                    ? '/assets/images/book-cover/' . $book['b_cover_path'] 
                                    : '/assets/images/book-cover/default-cover.svg';
                                
                                // Format the date
                                $purchaseDate = date('M d, Y', strtotime($book['up_purchased_at']));
                        ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded me-3" style="width:60px; height:80px; overflow: hidden;">
                                            <img src="<?php echo $coverPath; ?>" alt="<?php echo htmlspecialchars($book['b_title']); ?>" class="img-fluid" style="width:100%; height:100%; object-fit:cover;">
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?php echo htmlspecialchars($book['b_title']); ?></h6>
                                            <small class="text-muted"><?php echo htmlspecialchars($book['b_author']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo $purchaseDate; ?></td>
                                <td>$<?php echo number_format((float)$book['b_price'], 2); ?></td>
                                <td><span class="badge bg-secondary"><?php echo $orderId; ?></span></td>
                                <td><span class="badge bg-success">Completed</span></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="/reading-session/read-book/<?php echo $book['b_id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="bi bi-book"></i> Read
                                        </a>
                                        <a href="/user/download-book/<?php echo $book['b_id']; ?>" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Download PDF">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#receiptModal<?php echo $book['up_id']; ?>" title="View Receipt">
                                            <i class="bi bi-receipt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="py-5">
                                        <i class="bi bi-bag-x" style="font-size: 3rem;"></i>
                                        <h5 class="mt-3">No Purchases Yet</h5>
                                        <p class="text-muted">You haven't purchased any books yet.</p>
                                        <a href="/user/browse-books" class="btn btn-primary mt-2">
                                            <i class="bi bi-book me-2"></i>Browse Books
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if (!empty($purchased_books) && count($purchased_books) > 10): ?>
        <div class="card-footer bg-white">
            <nav>
                <ul class="pagination justify-content-center mb-0">
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
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($purchased_books)): ?>
    <!-- Receipt Modals -->
    <?php foreach ($purchased_books as $book): 
        // Generate a dummy order ID using the purchase ID and date
        $orderId = 'ORD-' . str_pad($book['up_id'], 5, '0', STR_PAD_LEFT);
        
        // Format the date
        $purchaseDate = date('M d, Y', strtotime($book['up_purchased_at']));
        $purchaseDateTime = date('M d, Y h:i A', strtotime($book['up_purchased_at']));
        
        // Generate a transaction ID based on purchase ID and timestamp
        $transactionId = 'TXN-' . str_pad($book['up_id'], 6, '0', STR_PAD_LEFT) . '-' . substr(hash('sha256', $book['up_purchased_at']), 0, 6);
    ?>
    <div class="modal fade" id="receiptModal<?php echo $book['up_id']; ?>" tabindex="-1" aria-labelledby="receiptModalLabel<?php echo $book['up_id']; ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="receiptModalLabel<?php echo $book['up_id']; ?>">
                        <i class="bi bi-receipt-cutoff me-2"></i>Purchase Receipt
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                        <h4 class="mt-2">Thank you for your purchase!</h4>
                        <p class="text-muted">This is your official receipt</p>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-info-circle me-2"></i>Order Information</span>
                                <span class="badge bg-success">Completed</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Order ID</small>
                                        <span class="fw-bold"><?php echo $orderId; ?></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Purchase Date</small>
                                        <span><?php echo $purchaseDateTime; ?></span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Transaction ID</small>
                                        <span class="font-monospace"><?php echo $transactionId; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <i class="bi bi-book me-2"></i>Product Details
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <?php
                                // Format the cover path
                                $coverPath = !empty($book['b_cover_path']) 
                                    ? '/assets/images/book-cover/' . $book['b_cover_path'] 
                                    : '/assets/images/book-cover/default-cover.svg';
                                ?>
                                <div class="bg-light rounded me-3" style="width:60px; height:80px; overflow: hidden;">
                                    <img src="<?php echo $coverPath; ?>" alt="<?php echo htmlspecialchars($book['b_title']); ?>" class="img-fluid" style="width:100%; height:100%; object-fit:cover;">
                                </div>
                                <div>
                                    <h6 class="mb-1"><?php echo htmlspecialchars($book['b_title']); ?></h6>
                                    <p class="text-muted small mb-0">By <?php echo htmlspecialchars($book['b_author']); ?></p>
                                    <?php if (!empty($book['b_isbn'])): ?>
                                    <p class="text-muted small mb-0">ISBN: <?php echo htmlspecialchars($book['b_isbn']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-light">
                            <i class="bi bi-currency-dollar me-2"></i>Payment Details
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span>$<?php echo number_format((float)$book['b_price'], 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax</span>
                                <span>$0.00</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">Total</span>
                                <span class="fw-bold">$<?php echo number_format((float)$book['b_price'], 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i>Print Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Add click event to download buttons
        const downloadLinks = document.querySelectorAll('a[href^="/user/download-book/"]');
        downloadLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Show a toast notification 
                if (typeof showToast === 'function') {
                    showToast('Downloading your PDF...', 'success');
                }
            });
        });

        // Add event listeners for filter controls
        document.getElementById('searchPurchases').addEventListener('input', filterPurchases);
        document.getElementById('filterDate').addEventListener('change', filterPurchases);
        document.getElementById('sortBy').addEventListener('change', sortPurchases);
    });

    // Simple client-side filtering function
    function filterPurchases() {
        const searchTerm = document.getElementById('searchPurchases').value.toLowerCase();
        const dateFilter = document.getElementById('filterDate').value;
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const titleElement = row.querySelector('h6');
            if (!titleElement) return; // Skip empty rows
            
            const title = titleElement.textContent.toLowerCase();
            let showRow = title.includes(searchTerm);
            
            // Additional date filtering could be implemented here
            
            row.style.display = showRow ? '' : 'none';
        });
    }

    // Sort function placeholder
    function sortPurchases() {
        // This would typically reload the page with a sort parameter
        // or sort the data client-side
        console.log('Sorting by: ' + document.getElementById('sortBy').value);
        // For now, we'll just reload the page when sorting changes
        // location.href = '/user/purchases?sort=' + document.getElementById('sortBy').value;
    }
</script>

<?php include $footerPath; ?>