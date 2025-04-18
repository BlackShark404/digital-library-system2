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
                        <!-- Sample purchase data - This would come from database in reality -->
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded me-3" style="width:60px; height:80px; display:flex; align-items:center; justify-content:center;">
                                        <i class="bi bi-book" style="font-size:1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">The Great Gatsby</h6>
                                        <small class="text-muted">F. Scott Fitzgerald</small>
                                    </div>
                                </div>
                            </td>
                            <td>Mar 28, 2025</td>
                            <td>$12.99</td>
                            <td><span class="badge bg-secondary">ORD-23981</span></td>
                            <td><span class="badge bg-success">Completed</span></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="reading_session.php?book_id=1" class="btn btn-sm btn-primary">
                                        <i class="bi bi-book"></i> Read
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Download">
                                        <i class="bi bi-download"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Receipt">
                                        <i class="bi bi-receipt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded me-3" style="width:60px; height:80px; display:flex; align-items:center; justify-content:center;">
                                        <i class="bi bi-book" style="font-size:1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">To Kill a Mockingbird</h6>
                                        <small class="text-muted">Harper Lee</small>
                                    </div>
                                </div>
                            </td>
                            <td>Mar 15, 2025</td>
                            <td>$9.99</td>
                            <td><span class="badge bg-secondary">ORD-23754</span></td>
                            <td><span class="badge bg-success">Completed</span></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="reading_session.php?book_id=2" class="btn btn-sm btn-primary">
                                        <i class="bi bi-book"></i> Read
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Download">
                                        <i class="bi bi-download"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Receipt">
                                        <i class="bi bi-receipt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded me-3" style="width:60px; height:80px; display:flex; align-items:center; justify-content:center;">
                                        <i class="bi bi-book" style="font-size:1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">1984</h6>
                                        <small class="text-muted">George Orwell</small>
                                    </div>
                                </div>
                            </td>
                            <td>Feb 23, 2025</td>
                            <td>$11.49</td>
                            <td><span class="badge bg-secondary">ORD-23112</span></td>
                            <td><span class="badge bg-success">Completed</span></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="reading_session.php?book_id=3" class="btn btn-sm btn-primary">
                                        <i class="bi bi-book"></i> Read
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Download">
                                        <i class="bi bi-download"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Receipt">
                                        <i class="bi bi-receipt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded me-3" style="width:60px; height:80px; display:flex; align-items:center; justify-content:center;">
                                        <i class="bi bi-book" style="font-size:1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">The Hobbit</h6>
                                        <small class="text-muted">J.R.R. Tolkien</small>
                                    </div>
                                </div>
                            </td>
                            <td>Feb 12, 2025</td>
                            <td>$14.99</td>
                            <td><span class="badge bg-secondary">ORD-22987</span></td>
                            <td><span class="badge bg-success">Completed</span></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="reading_session.php?book_id=4" class="btn btn-sm btn-primary">
                                        <i class="bi bi-book"></i> Read
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Download">
                                        <i class="bi bi-download"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Receipt">
                                        <i class="bi bi-receipt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded me-3" style="width:60px; height:80px; display:flex; align-items:center; justify-content:center;">
                                        <i class="bi bi-book" style="font-size:1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Pride and Prejudice</h6>
                                        <small class="text-muted">Jane Austen</small>
                                    </div>
                                </div>
                            </td>
                            <td>Jan 17, 2025</td>
                            <td>$7.99</td>
                            <td><span class="badge bg-secondary">ORD-22456</span></td>
                            <td><span class="badge bg-success">Completed</span></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="reading_session.php?book_id=5" class="btn btn-sm btn-primary">
                                        <i class="bi bi-book"></i> Read
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Download">
                                        <i class="bi bi-download"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Receipt">
                                        <i class="bi bi-receipt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
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
    </div>
</div>

<?php include $footerPath; ?>