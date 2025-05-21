<?php

use Core\Session;

include $headerPath;
?>

<!-- Purchase Management Content -->
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-cart-check me-2"></i>Purchase Management</h1>
        <a href="/api/purchases/export" class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-1"></i> Export CSV
        </a>
    </div>
    
    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Filter Purchases</h5>
            <form id="filter-form" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Book title, author, or user" value="<?= $filters['search'] ?? '' ?>">
                </div>
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="<?= $filters['date_from'] ?? '' ?>">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="<?= $filters['date_to'] ?? '' ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-grid gap-2 w-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i> Filter
                        </button>
                    </div>
                </div>
                <div class="col-12">
                    <button type="button" id="reset-filter" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Purchase Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="purchases-table" class="table table-hover w-100">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Book</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($purchases)): ?>
                            <?php foreach ($purchases as $purchase): ?>
                                <tr>
                                    <td data-order="<?= $purchase['up_id'] ?>">#PUR-<?= $purchase['up_id'] ?></td>
                                    <td data-search="<?= htmlspecialchars($purchase['ua_first_name'] . ' ' . $purchase['ua_last_name'] . ' ' . $purchase['ua_email']) ?>">
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($purchase['ua_profile_url'])): ?>
                                                <img src="<?= $purchase['ua_profile_url'] ?>" alt="profile" class="avatar-wrapper-compact avatar-compact me-2 rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                            <?php else: ?>
                                                <?php 
                                                    $initials = strtoupper(substr($purchase['ua_first_name'], 0, 1) . substr($purchase['ua_last_name'], 0, 1));
                                                    $colors = ['primary', 'success', 'danger', 'warning', 'info'];
                                                    $colorIndex = crc32($purchase['ua_email']) % count($colors);
                                                    $color = $colors[$colorIndex];
                                                ?>
                                                <span class="avatar-sm bg-<?= $color ?> text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;"><?= $initials ?></span>
                                            <?php endif; ?>
                                            <div>
                                                <div class="fw-bold"><?= $purchase['ua_first_name'] . ' ' . $purchase['ua_last_name'] ?></div>
                                                <div class="small text-muted"><?= $purchase['ua_email'] ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-search="<?= htmlspecialchars($purchase['b_title'] . ' ' . $purchase['b_author']) ?>">
                                        <div class="d-flex align-items-center">
                                            <?php 
                                                $coverPath = $purchase['b_cover_path'] 
                                                    ? '/assets/images/book-cover/' . $purchase['b_cover_path'] 
                                                    : '/assets/images/book-cover/default-cover.svg';
                                            ?>
                                            <img src="<?= $coverPath ?>" alt="Book Cover" class="me-2" style="width: 40px; height: 60px; object-fit: cover; border-radius: 2px;">
                                            <div>
                                                <div class="fw-bold"><?= $purchase['b_title'] ?></div>
                                                <div class="small text-muted"><?= $purchase['b_author'] ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-order="<?= $purchase['b_price'] ?>">$<?= number_format($purchase['b_price'], 2) ?></td>
                                    <td data-order="<?= strtotime($purchase['up_purchased_at']) ?>"><?= date('M d, Y H:i', strtotime($purchase['up_purchased_at'])) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary view-purchase-btn" data-id="<?= $purchase['up_id'] ?>">
                                            <i class="bi bi-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <p class="text-muted mb-0"><i class="bi bi-info-circle me-1"></i> No purchases found</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Purchase Details Modal -->
<div class="modal fade" id="purchaseDetailsModal" tabindex="-1" aria-labelledby="purchaseDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="purchaseDetailsModalLabel">Purchase Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="purchase-loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading purchase details...</p>
                </div>
                <div id="purchase-details" class="d-none">
                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            <img id="purchase-book-cover" src="" alt="Book Cover" class="img-fluid rounded mb-2" style="max-height: 200px;">
                        </div>
                        <div class="col-md-8">
                            <h4 id="purchase-book-title" class="mb-1"></h4>
                            <p id="purchase-book-author" class="text-muted"></p>
                            
                            <div class="d-flex justify-content-between mb-3">
                                <span class="badge bg-success">Purchased</span>
                                <span class="text-muted" id="purchase-date"></span>
                            </div>
                            
                            <div class="mb-3">
                                <h5 class="text-primary" id="purchase-amount"></h5>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-0 bg-light mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">User Information</h6>
                                    <div class="mb-2">
                                        <strong>Name:</strong> <span id="purchase-user-name"></span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Email:</strong> <span id="purchase-user-email"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 bg-light mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">Transaction Information</h6>
                                    <div class="mb-2">
                                        <strong>Transaction ID:</strong> <span id="purchase-id"></span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Date:</strong> <span id="purchase-full-date"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="purchase-error" class="alert alert-danger d-none">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Error loading purchase details.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable with a delay to ensure DOM is fully processed
    setTimeout(function() {
        if (typeof jQuery !== 'undefined' && typeof $.fn.DataTable !== 'undefined') {
            try {
                // Destroy existing DataTable instance if any
                if ($.fn.DataTable.isDataTable('#purchases-table')) {
                    $('#purchases-table').DataTable().destroy();
                }
                
                // Initialize with simpler configuration
                $('#purchases-table').DataTable({
                    responsive: true,
                    pageLength: 15,
                    searching: false, // Disable DataTables built-in search since we have our own filter form
                    language: {
                        emptyTable: "No purchases found",
                        info: "Showing _START_ to _END_ of _TOTAL_ purchases",
                        infoEmpty: "Showing 0 to 0 of 0 purchases",
                        paginate: {
                            first: '<i class="bi bi-chevron-double-left"></i>',
                            last: '<i class="bi bi-chevron-double-right"></i>',
                            next: '<i class="bi bi-chevron-right"></i>',
                            previous: '<i class="bi bi-chevron-left"></i>'
                        }
                    },
                    dom: '<"row"<"col-sm-12"tr>><"row"<"col-sm-5"i><"col-sm-7"p>>',
                    columnDefs: [
                        { orderable: false, targets: 5 } // Disable sorting on the actions column
                    ]
                });
            } catch (e) {
                console.error("DataTable initialization error:", e);
            }
        }
    }, 100); // Short delay helps ensure the DOM is ready
    
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
        
        // Navigate to the filtered URL
        window.location.href = '/admin/purchases?' + queryParams.toString();
    });
    
    // Handle reset button
    document.getElementById('reset-filter').addEventListener('click', function() {
        // Reset form fields
        document.getElementById('filter-form').reset();
        
        // Redirect to the base URL
        window.location.href = '/admin/purchases';
    });
    
    // Handle view purchase buttons
    document.querySelectorAll('.view-purchase-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const purchaseId = this.getAttribute('data-id');
            showPurchaseDetails(purchaseId);
        });
    });
    
    function showPurchaseDetails(purchaseId) {
        console.log('Opening modal for purchase ID:', purchaseId);
        
        // Show loading state
        document.getElementById('purchase-loading').classList.remove('d-none');
        document.getElementById('purchase-details').classList.add('d-none');
        document.getElementById('purchase-error').classList.add('d-none');
        
        // Show modal - try jQuery first, fallback to Bootstrap JS
        if (typeof jQuery !== 'undefined') {
            jQuery('#purchaseDetailsModal').modal('show');
        } else if (typeof bootstrap !== 'undefined') {
            const modal = new bootstrap.Modal(document.getElementById('purchaseDetailsModal'));
            modal.show();
        } else {
            document.getElementById('purchaseDetailsModal').classList.add('show');
            document.getElementById('purchaseDetailsModal').style.display = 'block';
        }
        
        // Fetch purchase details
        fetch(`/api/purchases/${purchaseId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Purchase data:', data);
                if (data.success && data.data) {
                    // Hide loading, show details
                    document.getElementById('purchase-loading').classList.add('d-none');
                    document.getElementById('purchase-details').classList.remove('d-none');
                    
                    const purchase = data.data;
                    updateModalContent(purchase);
                } else {
                    throw new Error('Failed to load purchase data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('purchase-loading').classList.add('d-none');
                document.getElementById('purchase-error').classList.remove('d-none');
            });
    }
    
    function updateModalContent(purchase) {
        // Fill in purchase details
        document.getElementById('purchase-id').textContent = purchase.up_id;
        document.getElementById('purchase-book-title').textContent = purchase.b_title;
        document.getElementById('purchase-book-author').textContent = purchase.b_author;
        document.getElementById('purchase-amount').textContent = '$' + parseFloat(purchase.b_price).toFixed(2);
        
        // Book cover
        const coverPath = purchase.b_cover_path 
            ? '/assets/images/book-cover/' + purchase.b_cover_path 
            : '/assets/images/book-cover/default-cover.svg';
        document.getElementById('purchase-book-cover').src = coverPath;
        
        // User details
        document.getElementById('purchase-user-name').textContent = `${purchase.ua_first_name} ${purchase.ua_last_name}`;
        document.getElementById('purchase-user-email').textContent = purchase.ua_email;
        
        // Date
        const purchaseDate = new Date(purchase.up_purchased_at);
        document.getElementById('purchase-full-date').textContent = purchaseDate.toLocaleString();
        document.getElementById('purchase-date').textContent = purchaseDate.toLocaleDateString();
    }
});
</script>

<?php include $footerPath; ?>