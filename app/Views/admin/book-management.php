<?php
include $headerPath;
?>

<div class="container">
    <h1 class="mb-4"><i class="bi bi-book me-2"></i>Book Management</h1>

    <!-- Search and Filter Options -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="bookSearchForm">
                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" id="searchQuery" placeholder="Search by title, author, or ISBN...">
                            <button type="submit" class="btn btn-outline-primary">Search</button>
                            <button type="button" class="btn btn-outline-secondary" id="clearSearch">Clear</button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="resultsPerPage">
                            <option value="10">10 per page</option>
                            <option value="25">25 per page</option>
                            <option value="50">50 per page</option>
                            <option value="100">100 per page</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#addBookModal">
                            <i class="bi bi-plus-lg"></i> Add Book
                        </button>
                    </div>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="genreFilter" class="form-label">Genre</label>
                        <select class="form-select" id="genreFilter">
                            <option value="">All Genres</option>
                            <option value="fiction">Fiction</option>
                            <option value="non-fiction">Non-Fiction</option>
                            <option value="mystery">Mystery</option>
                            <option value="sci-fi">Science Fiction</option>
                            <option value="fantasy">Fantasy</option>
                            <option value="biography">Biography</option>
                            <option value="history">History</option>
                            <option value="self-help">Self-Help</option>
                            <option value="business">Business</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="priceRangeFilter" class="form-label">Price Range</label>
                        <select class="form-select" id="priceRangeFilter">
                            <option value="">Any Price</option>
                            <option value="0-10">$0 - $10</option>
                            <option value="10-20">$10 - $20</option>
                            <option value="20-30">$20 - $30</option>
                            <option value="30+">$30+</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="statusFilter" class="form-label">Status</label>
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="in-stock">In Stock</option>
                            <option value="low-stock">Low Stock</option>
                            <option value="out-of-stock">Out of Stock</option>
                            <option value="discontinued">Discontinued</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="sortBy" class="form-label">Sort By</label>
                        <select class="form-select" id="sortBy">
                            <option value="title-asc">Title (A-Z)</option>
                            <option value="title-desc">Title (Z-A)</option>
                            <option value="price-asc">Price (Low to High)</option>
                            <option value="price-desc">Price (High to Low)</option>
                            <option value="date-added-desc">Newest First</option>
                            <option value="date-added-asc">Oldest First</option>
                        </select>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-12 text-end">
                        <button type="button" class="btn btn-outline-secondary" id="resetFilters">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Filters
                        </button>
                        <button type="submit" class="btn btn-success ms-2">
                            <i class="bi bi-funnel me-1"></i>Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Book List Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="booksTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cover</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Genre</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Book Modal -->
<div class="modal fade" id="deleteBookModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-triangle-exclamation me-2 text-danger"></i>Delete Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="deleteBookForm">
                    <input type="hidden" id="deleteBookId">
                    <p>Are you sure you want to delete the book <strong id="deleteBookTitle"></strong>?</p>
                    <p class="text-danger"><strong>Warning:</strong> This action cannot be undone. All book data will be permanently removed.</p>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="confirmDelete" required>
                        <label class="form-check-label" for="confirmDelete">
                            I understand the consequences of this action
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="deleteBookForm" class="btn btn-danger">Delete Book</button>
            </div>
        </div>
    </div>
</div>





<?php
include $footerPath;
?>