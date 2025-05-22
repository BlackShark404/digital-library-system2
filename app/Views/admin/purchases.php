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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="purchaseDetailsModalLabel">
                    <i class="bi bi-receipt-cutoff me-2"></i>Purchase Receipt
                </h5>
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
                    <div class="text-center mb-4">
                        <i class="bi bi-bag-check-fill text-success" style="font-size: 3rem;"></i>
                        <h4 class="mt-2">Purchase Complete</h4>
                        <p class="text-muted">Purchase receipt details</p>
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
                                        <span class="fw-bold" id="purchase-id"></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Purchase Date</small>
                                        <span id="purchase-full-date"></span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Transaction ID</small>
                                        <span class="font-monospace" id="purchase-transaction-id"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <i class="bi bi-person me-2"></i>Customer Information
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div id="purchase-user-avatar" class="me-3"></div>
                                <div>
                                    <h6 class="mb-1" id="purchase-user-name"></h6>
                                    <p class="text-muted small mb-0" id="purchase-user-email"></p>
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
                                <div class="bg-light rounded me-3" style="width:60px; height:80px; overflow: hidden;">
                                    <img id="purchase-book-cover" src="" alt="Book Cover" class="img-fluid" style="width:100%; height:100%; object-fit:cover;">
                                </div>
                                <div>
                                    <h6 class="mb-1" id="purchase-book-title"></h6>
                                    <p class="text-muted small mb-0" id="purchase-book-author"></p>
                                    <p class="text-muted small mb-0" id="purchase-book-isbn"></p>
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
                                <span id="purchase-subtotal"></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax</span>
                                <span>$0.00</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">Total</span>
                                <span class="fw-bold" id="purchase-amount"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="purchase-error" class="alert alert-danger d-none">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Error loading purchase details.
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
        
        // Redirect to the page with query params
        if (queryParams.toString()) {
            window.location.href = '/admin/purchases?' + queryParams.toString();
        } else {
            window.location.href = '/admin/purchases';
        }
    });
    
    // Reset filter
    document.getElementById('reset-filter').addEventListener('click', function() {
        window.location.href = '/admin/purchases';
    });
    
    // View purchase details
    const viewButtons = document.querySelectorAll('.view-purchase-btn');
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            viewPurchaseDetails(id);
        });
    });
    
    function viewPurchaseDetails(id) {
        // Reset modal content
        document.getElementById('purchase-details').classList.add('d-none');
        document.getElementById('purchase-error').classList.add('d-none');
        document.getElementById('purchase-loading').classList.remove('d-none');
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('purchaseDetailsModal'));
        modal.show();
        
        // Fetch purchase details
        fetch(`/api/purchases/${id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to load purchase details');
                }
                return response.json();
            })
            .then(response => {
                console.log('Response received:', response);
                
                // The response has nested data objects
                const purchaseData = response.data;
                
                if (!purchaseData) {
                    throw new Error('Invalid data format received');
                }
                
                // Format purchase ID
                document.getElementById('purchase-id').textContent = `ORD-${String(purchaseData.up_id).padStart(5, '0')}`;
                
                // Format transaction ID
                const hashPart = purchaseData.up_purchased_at ? purchaseData.up_purchased_at.substring(0, 8) : '';
                document.getElementById('purchase-transaction-id').textContent = `TXN-${String(purchaseData.up_id).padStart(6, '0')}-${hashPart}`;
                
                // Format dates
                const purchaseDate = new Date(purchaseData.up_purchased_at);
                // Check if purchase-date element exists before trying to update it
                const purchaseDateElement = document.getElementById('purchase-date');
                if (purchaseDateElement) {
                    purchaseDateElement.textContent = purchaseDate.toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'});
                }
                
                document.getElementById('purchase-full-date').textContent = purchaseDate.toLocaleString('en-US', {
                    month: 'short', 
                    day: 'numeric', 
                    year: 'numeric', 
                    hour: 'numeric', 
                    minute: 'numeric', 
                    hour12: true
                });
                
                // Set book details
                document.getElementById('purchase-book-title').textContent = purchaseData.b_title;
                document.getElementById('purchase-book-author').textContent = `By ${purchaseData.b_author}`;
                document.getElementById('purchase-book-isbn').textContent = purchaseData.b_isbn ? `ISBN: ${purchaseData.b_isbn}` : '';
                
                // Set cover image
                const coverPath = purchaseData.b_cover_path 
                    ? `/assets/images/book-cover/${purchaseData.b_cover_path}` 
                    : '/assets/images/book-cover/default-cover.svg';
                document.getElementById('purchase-book-cover').src = coverPath;
                
                // Set amount
                const amount = `$${parseFloat(purchaseData.b_price).toFixed(2)}`;
                document.getElementById('purchase-amount').textContent = amount;
                document.getElementById('purchase-subtotal').textContent = amount;
                
                // Set user info
                document.getElementById('purchase-user-name').textContent = `${purchaseData.ua_first_name} ${purchaseData.ua_last_name}`;
                document.getElementById('purchase-user-email').textContent = purchaseData.ua_email;
                
                // Set user avatar - make sure all properties exist before accessing them
                if (purchaseData.ua_profile_url) {
                    document.getElementById('purchase-user-avatar').innerHTML = `
                        <img src="${purchaseData.ua_profile_url}" alt="User Avatar" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                    `;
                } else if (purchaseData.ua_first_name && purchaseData.ua_last_name) {
                    // Safe access for initials creation
                    const firstName = purchaseData.ua_first_name || '';
                    const lastName = purchaseData.ua_last_name || '';
                    const firstInitial = firstName.length > 0 ? firstName.charAt(0) : '';
                    const lastInitial = lastName.length > 0 ? lastName.charAt(0) : '';
                    const initials = (firstInitial + lastInitial).toUpperCase();
                    
                    const colors = ['primary', 'success', 'danger', 'warning', 'info'];
                    const colorIndex = purchaseData.ua_email ? 
                        purchaseData.ua_email.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0) % colors.length : 
                        0;
                    
                    document.getElementById('purchase-user-avatar').innerHTML = `
                        <div class="rounded-circle bg-${colors[colorIndex]} text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            ${initials}
                        </div>
                    `;
                } else {
                    // Fallback if no user data
                    document.getElementById('purchase-user-avatar').innerHTML = `
                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            ?
                        </div>
                    `;
                }
                
                // Show purchase details
                document.getElementById('purchase-loading').classList.add('d-none');
                document.getElementById('purchase-details').classList.remove('d-none');
            })
            .catch(error => {
                console.error(error);
                document.getElementById('purchase-loading').classList.add('d-none');
                document.getElementById('purchase-error').classList.remove('d-none');
            });
    }
});
</script>

<?php include $footerPath; ?>