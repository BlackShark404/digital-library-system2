<?php
include $headerPath;
?>

<!-- Purchase Management Content -->
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="bi bi-cart-check me-2"></i>Purchase Management</h2>
        </div>
        <div class="col-md-4 text-md-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="bi bi-download me-1"></i>Export
            </button>
            <button class="btn btn-success ms-2" data-bs-toggle="modal" data-bs-target="#addPurchaseModal">
                <i class="bi bi-plus-circle me-1"></i>Add Purchase
            </button>
        </div>
    </div>

    <!-- Filters Row -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="searchInput" placeholder="Search purchases...">
            </div>
        </div>
        <div class="col-md-2 mb-3 mb-md-0">
            <select class="form-select" id="statusFilter">
                <option value="">All Statuses</option>
                <option value="completed">Completed</option>
                <option value="pending">Pending</option>
                <option value="refunded">Refunded</option>
                <option value="failed">Failed</option>
            </select>
        </div>
        <div class="col-md-2 mb-3 mb-md-0">
            <select class="form-select" id="dateFilter">
                <option value="all">All Time</option>
                <option value="today">Today</option>
                <option value="week">This Week</option>
                <option value="month">This Month</option>
                <option value="custom">Custom Range</option>
            </select>
        </div>
        <div class="col-md-3 mb-3 mb-md-0" id="customDateContainer" style="display: none;">
            <div class="input-group">
                <input type="date" class="form-control" id="startDate">
                <span class="input-group-text">to</span>
                <input type="date" class="form-control" id="endDate">
            </div>
        </div>
        <div class="col-md-2">
            <button class="btn btn-outline-secondary w-100" id="resetFilters">
                <i class="bi bi-x-circle me-1"></i>Reset
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Revenue</h5>
                    <h3 class="mb-0">$24,856.00</h3>
                    <p class="small mb-0">+12% from last month</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Completed</h5>
                    <h3 class="mb-0">432</h3>
                    <p class="small mb-0">+8% from last month</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Pending</h5>
                    <h3 class="mb-0">28</h3>
                    <p class="small mb-0">-15% from last month</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Refunded</h5>
                    <h3 class="mb-0">15</h3>
                    <p class="small mb-0">+3% from last month</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">User</th>
                            <th scope="col">Book</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Date</th>
                            <th scope="col">Payment Method</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Hardcoded sample data -->
                        <tr>
                            <td>#PUR-1082</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar-sm bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">JD</span>
                                    <span>John Doe</span>
                                </div>
                            </td>
                            <td>The Great Adventure</td>
                            <td>$19.99</td>
                            <td>2025-03-28</td>
                            <td><i class="bi bi-credit-card me-1"></i> Credit Card</td>
                            <td><span class="badge bg-success">Completed</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2"></i>View Details</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-receipt me-2"></i>Generate Invoice</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-arrow-counterclockwise me-2"></i>Process Refund</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>#PUR-1081</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar-sm bg-info text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">MS</span>
                                    <span>Maria Smith</span>
                                </div>
                            </td>
                            <td>Business Strategy 101</td>
                            <td>$24.99</td>
                            <td>2025-03-27</td>
                            <td><i class="bi bi-paypal me-1"></i> PayPal</td>
                            <td><span class="badge bg-success">Completed</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2"></i>View Details</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-receipt me-2"></i>Generate Invoice</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-arrow-counterclockwise me-2"></i>Process Refund</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>#PUR-1080</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar-sm bg-warning text-dark rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">RJ</span>
                                    <span>Robert Johnson</span>
                                </div>
                            </td>
                            <td>Creative Writing Course</td>
                            <td>$49.99</td>
                            <td>2025-03-26</td>
                            <td><i class="bi bi-credit-card me-1"></i> Credit Card</td>
                            <td><span class="badge bg-warning text-dark">Pending</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2"></i>View Details</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-check-circle me-2"></i>Mark as Complete</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-x-circle me-2"></i>Cancel Purchase</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>#PUR-1079</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar-sm bg-danger text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">EW</span>
                                    <span>Emily Wilson</span>
                                </div>
                            </td>
                            <td>Psychology Basics</td>
                            <td>$29.99</td>
                            <td>2025-03-25</td>
                            <td><i class="bi bi-apple me-1"></i> Apple Pay</td>
                            <td><span class="badge bg-danger">Refunded</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2"></i>View Details</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-receipt me-2"></i>View Refund Details</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>#PUR-1078</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar-sm bg-success text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">DT</span>
                                    <span>David Thompson</span>
                                </div>
                            </td>
                            <td>Python Programming</td>
                            <td>$34.99</td>
                            <td>2025-03-25</td>
                            <td><i class="bi bi-google me-1"></i> Google Pay</td>
                            <td><span class="badge bg-secondary">Failed</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2"></i>View Details</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-arrow-repeat me-2"></i>Retry Payment</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <nav>
                <ul class="pagination justify-content-center justify-content-md-end mb-0">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1"><i class="bi bi-chevron-left"></i></a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#"><i class="bi bi-chevron-right"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Purchases</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Date Range</label>
                        <select class="form-select mb-2">
                            <option value="all">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="custom">Custom Range</option>
                        </select>
                        <div class="input-group" id="customExportDates" style="display: none;">
                            <input type="date" class="form-control">
                            <span class="input-group-text">to</span>
                            <input type="date" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Format</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="exportFormat" id="formatCSV" checked>
                            <label class="form-check-label" for="formatCSV">
                                CSV
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="exportFormat" id="formatExcel">
                            <label class="form-check-label" for="formatExcel">
                                Excel
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="exportFormat" id="formatPDF">
                            <label class="form-check-label" for="formatPDF">
                                PDF
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Include Fields</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="fieldID" checked>
                                    <label class="form-check-label" for="fieldID">ID</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="fieldUser" checked>
                                    <label class="form-check-label" for="fieldUser">User</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="fieldBook" checked>
                                    <label class="form-check-label" for="fieldBook">Book</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="fieldAmount" checked>
                                    <label class="form-check-label" for="fieldAmount">Amount</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="fieldDate" checked>
                                    <label class="form-check-label" for="fieldDate">Date</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="fieldMethod" checked>
                                    <label class="form-check-label" for="fieldMethod">Payment Method</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="fieldStatus" checked>
                                    <label class="form-check-label" for="fieldStatus">Status</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Export</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Purchase Modal -->
<div class="modal fade" id="addPurchaseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Purchase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">User</label>
                        <select class="form-select">
                            <option value="">Select User</option>
                            <option value="1">John Doe</option>
                            <option value="2">Maria Smith</option>
                            <option value="3">Robert Johnson</option>
                            <option value="4">Emily Wilson</option>
                            <option value="5">David Thompson</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Book</label>
                        <select class="form-select">
                            <option value="">Select Book</option>
                            <option value="1">The Great Adventure</option>
                            <option value="2">Business Strategy 101</option>
                            <option value="3">Creative Writing Course</option>
                            <option value="4">Psychology Basics</option>
                            <option value="5">Python Programming</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount ($)</label>
                        <input type="number" class="form-control" step="0.01" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select class="form-select">
                            <option value="">Select Payment Method</option>
                            <option value="credit">Credit Card</option>
                            <option value="paypal">PayPal</option>
                            <option value="applepay">Apple Pay</option>
                            <option value="googlepay">Google Pay</option>
                            <option value="banktransfer">Bank Transfer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select">
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                            <option value="refunded">Refunded</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success">Add Purchase</button>
            </div>
        </div>
    </div>
</div>

<!-- Custom JavaScript for the Purchase UI -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle date filter change
        const dateFilter = document.getElementById('dateFilter');
        const customDateContainer = document.getElementById('customDateContainer');

        dateFilter.addEventListener('change', function() {
            if (this.value === 'custom') {
                customDateContainer.style.display = 'block';
            } else {
                customDateContainer.style.display = 'none';
            }
        });

        // Reset filters button
        const resetFilters = document.getElementById('resetFilters');
        resetFilters.addEventListener('click', function() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').selectedIndex = 0;
            document.getElementById('dateFilter').selectedIndex = 0;
            customDateContainer.style.display = 'none';
        });

        // Handle export modal custom date range
        const exportSelect = document.querySelector('#exportModal select');
        const customExportDates = document.getElementById('customExportDates');

        exportSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                customExportDates.style.display = 'flex';
            } else {
                customExportDates.style.display = 'none';
            }
        });
    });
</script>

<?php
// Include footer
include $footerPath;
?>