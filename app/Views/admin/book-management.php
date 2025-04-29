<?php
include $headerPath;
?>

<div class="container">
    <h1 class="mb-4"><i class="bi bi-book me-2"></i>Book Management</h1>

    <!-- Search and Filter Options -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                   <!-- Additional filters can go here -->
                </div>
                <div class="col-md-6">
                  <!-- Additional filters can go here -->
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#addBookModal">
                        <i class="fas fa-plus"></i> Add Book
                    </button>
                </div>
            </div>
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

<!-- Include your modals here (addBookModal, editBookModal, etc.) -->

<!-- Include jQuery first -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">

<!-- Include DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="/assets/js/utility/DataTablesHelper.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Define columns for the DataTable
    const columns = [
        { data: 'id' },
        { data: 'cover', orderable: false },
        { data: 'title' },
        { data: 'author' },
        { data: 'genre' },
        { data: 'price' },
        { data: 'status' },
        { 
            data: null,
            orderable: false,
            render: DataTablesHelper.createActionColumn([
                {
                    name: 'edit',
                    icon: '<i class="fas fa-pen"></i>',
                    class: 'btn-outline-primary',
                    attributes: 'data-bs-toggle="modal" data-bs-target="#editBookModal"'
                },
                {
                    name: 'view',
                    icon: '<i class="fas fa-eye"></i>',
                    class: 'btn-outline-info',
                    attributes: 'data-bs-toggle="modal" data-bs-target="#viewBookModal"'
                },
                {
                    name: 'delete',
                    icon: '<i class="fas fa-trash"></i>',
                    class: 'btn-outline-danger',
                    attributes: 'data-bs-toggle="modal" data-bs-target="#deleteBookModal"'
                }
            ])
        }
    ];

    // Initialize DataTable with server-side processing
    const table = DataTablesHelper.initServerSide('booksTable', '/admin/book-management/get-books', columns, {
        pageLength: 10,
        language: {
            emptyTable: 'No books found',
            zeroRecords: 'No books match your search criteria'
        },
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f>>rtip',
        initComplete: function() {
            // Add custom filters or buttons if needed
        }
    });

    // Setup action button event handlers
    DataTablesHelper.bindActionEvents('booksTable', {
        'edit': function(id, rowData) {
            // Populate edit modal with data
            document.getElementById('editBookId').value = id;
            document.getElementById('editTitle').value = rowData.title.split('<div')[0].trim();
            document.getElementById('editAuthor').value = rowData.author;
            // ... Set other fields
            
            // Show the modal
            const editModal = new bootstrap.Modal(document.getElementById('editBookModal'));
            editModal.show();
        },
        'view': function(id, rowData) {
            // Fetch full book details with AJAX if needed, or use rowData
            fetch(`/book/details/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const book = data.data;
                        // Populate view modal
                        document.getElementById('viewBookTitle').textContent = book.title;
                        // ... Set other fields
                        
                        // Show the modal
                        const viewModal = new bootstrap.Modal(document.getElementById('viewBookModal'));
                        viewModal.show();
                    } else {
                        DataTablesHelper.showToast(data.message, 'error');
                    }
                });
        },
        'delete': function(id, rowData) {
            // Setup delete confirmation
            document.getElementById('deleteBookId').value = id;
            document.getElementById('deleteBookTitle').textContent = rowData.title.split('<div')[0].trim();
            
            // Show the modal
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteBookModal'));
            deleteModal.show();
        }
    });

    // Handle form submissions
    DataTablesHelper.handleFormSubmit('addBookForm', 'booksTable', '/book/add', function(response) {
        // Additional callback actions after successful add
    });
    
    DataTablesHelper.handleFormSubmit('editBookForm', 'booksTable', '/book/update');
    
    // Handle delete form submission
    document.getElementById('deleteBookForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const bookId = document.getElementById('deleteBookId').value;
        
        fetch(`/book/delete/${bookId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Close the modal
            const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteBookModal'));
            deleteModal.hide();
            
            if (data.success) {
                // Refresh table and show success message
                DataTablesHelper.refreshTable('booksTable');
                DataTablesHelper.showToast(data.message, 'success');
            } else {
                DataTablesHelper.showToast(data.message, 'error');
            }
        });
    });
});
</script>

<?php
include $footerPath;
?>