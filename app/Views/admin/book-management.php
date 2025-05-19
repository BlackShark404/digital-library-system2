<?php
include $headerPath;
?>

<div class="container">
    <h1 class="mb-4"><i class="bi bi-book me-2"></i>Book Management</h1>

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