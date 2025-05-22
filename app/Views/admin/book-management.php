<?php
include $headerPath;
?>

<div class="container">
    <h1 class="mb-4"><i class="bi bi-book me-2"></i>Book Management</h1>

    <!-- Book Actions -->
    <div class="mb-4">
        <button id="addBookBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bookFormModal">
            <i class="bi bi-plus-circle me-2"></i>Add New Book
        </button>
        <button id="manageCategoriesBtn" class="btn btn-outline-primary ms-2" data-bs-toggle="modal" data-bs-target="#categoryManagementModal">
            <i class="bi bi-tags me-2"></i>Manage Categories
        </button>
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

<!-- Book Form Modal (Add/Edit) -->
<div class="modal fade" id="bookFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookFormTitle">Add New Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="bookForm">
                    <input type="hidden" id="bookId">
                    
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title *</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="author" class="form-label">Author *</label>
                                <input type="text" class="form-control" id="author" name="author" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="genre_id" class="form-label">Genre</label>
                                    <select class="form-select" id="genre_id" name="genre_id">
                                        <option value="">Select Genre</option>
                                        <?php foreach ($genres as $genre): ?>
                                            <option value="<?= $genre['g_id'] ?>"><?= $genre['g_name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="price" class="form-label">Price ($)</label>
                                    <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
                                    </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3 text-center">
                                <label for="coverUpload" class="form-label">Book Cover</label>
                                <div class="cover-preview-container border rounded p-2 mb-2" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                                    <img id="coverPreview" src="/assets/images/book-cover/default-cover.svg" alt="Book Cover" class="img-fluid" style="max-height: 180px;">
                                </div>
                                <input type="file" class="form-control" id="coverUpload" accept="image/*">
                                <input type="hidden" id="cover_image_data" name="cover_image_data">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="publisher" class="form-label">Publisher</label>
                            <input type="text" class="form-control" id="publisher" name="publisher">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="publication_date" class="form-label">Publication Date</label>
                            <input type="date" class="form-control" id="publication_date" name="publication_date">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="isbn" class="form-label">ISBN</label>
                            <input type="text" class="form-control" id="isbn" name="isbn">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="pages" class="form-label">Pages</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="pages" name="pages" min="1">
                                <span class="input-group-text" id="pageCountInfo" style="display: none;">
                                    <i class="bi bi-info-circle"></i>
                                </span>
                            </div>
                            <small id="pagesHelp" class="form-text text-muted">Page count will be automatically extracted from PDF if left empty.</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bookFileUpload" class="form-label">Book File (PDF)</label>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <input type="file" class="form-control" id="bookFileUpload" accept="application/pdf">
                            <span class="badge bg-light text-dark ms-2">Max: 100MB</span>
                        </div>
                        <input type="hidden" id="book_file_data" name="book_file_data">
                        <div id="currentFileInfo" class="small text-muted mt-1"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveBookBtn">Save Book</button>
            </div>
        </div>
    </div>
</div>

<!-- View Book Modal -->
<div class="modal fade" id="viewBookModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Book Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <img id="viewCoverImage" src="" alt="Book Cover" class="img-fluid border rounded" style="max-height: 280px;">
                    </div>
                    <div class="col-md-8">
                        <h3 id="viewTitle" class="fw-bold"></h3>
                        <h5 id="viewAuthor" class="text-secondary"></h5>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <p><strong>Genre:</strong> <span id="viewGenre"></span></p>
                                <p><strong>Publisher:</strong> <span id="viewPublisher"></span></p>
                                <p><strong>Publication Date:</strong> <span id="viewPublicationDate"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>ISBN:</strong> <span id="viewIsbn"></span></p>
                                <p><strong>Pages:</strong> <span id="viewPages"></span></p>
                                <p><strong>Price:</strong> <span id="viewPrice"></span></p>
                            </div>
                        </div>
                        
                        <div id="viewFileSection" class="mt-3">
                            <p><strong>Book File:</strong> <a id="viewFileLink" href="#" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-file-pdf"></i> View PDF</a></p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h5>Description</h5>
                    <div id="viewDescription" class="border rounded p-3 bg-light"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editBookFromViewBtn">Edit Book</button>
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

<!-- Category Management Modal -->
<div class="modal fade" id="categoryManagementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-tags me-2"></i>Category Management</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5">
                        <!-- Add New Category Form -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Add New Category</h6>
                            </div>
                            <div class="card-body">
                                <form id="addCategoryForm" class="mb-0">
                                    <input type="hidden" id="editCategoryId">
                                    <div class="mb-3">
                                        <label for="categoryName" class="form-label">Category Name</label>
                                        <input type="text" class="form-control" id="categoryName" placeholder="Enter category name" required>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <button type="button" id="cancelEditBtn" class="btn btn-outline-secondary" style="display: none;">
                                            Cancel Edit
                                        </button>
                                        <button type="submit" id="saveCategoryBtn" class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i> Add Category
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <!-- Categories List -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Categories</h6>
                                <span id="categoryCount" class="badge bg-primary rounded-pill">0</span>
                            </div>
                            <div class="card-body p-0">
                                <div id="categoriesLoading" class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading categories...</span>
                                    </div>
                                    <p class="mt-2">Loading categories...</p>
                                </div>
                                <div id="categoriesEmpty" class="text-center py-4 d-none">
                                    <i class="bi bi-tags" style="font-size: 2rem;"></i>
                                    <p class="mt-2">No categories found.</p>
                                </div>
                                <div id="categoriesTableContainer" class="d-none" style="max-height: 300px; overflow-y: auto;">
                                    <table class="table table-hover table-striped mb-0">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th>Name</th>
                                                <th>Book Count</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="categoriesList">
                                            <!-- Categories will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="categoryAlert" class="alert mt-3 d-none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Category Modal -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>Delete Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="deleteCategoryId">
                <p>Are you sure you want to delete the category <strong id="deleteCategoryName"></strong>?</p>
                <div id="categoryHasBooks" class="alert alert-warning d-none">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This category has associated books. Deleting it will remove the category assignment from these books.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmDeleteCategoryBtn" class="btn btn-danger">Delete Category</button>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery first -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">

<!-- Custom styles -->
<style>
    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 1;
        background-color: #f8f9fa;
    }
    
    #categoriesTableContainer {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }
    
    #categoriesTableContainer .table {
        margin-bottom: 0;
    }
    
    #categoriesTableContainer thead th {
        border-bottom: 2px solid #dee2e6;
        padding: 0.75rem;
    }
</style>

<!-- Include DataTables JS and Book Management JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="/assets/js/utility/DataTablesManager.js"></script>

<script>
    $(document).ready(function() {
        let dataTable;
        let currentBookId = null;
        let isEditMode = false;
        
        // Initialize DataTables
        initBookDataTable();
        
        // Initialize file upload handlers
        initFileUploadHandlers();
        
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Initialize tooltip for page count info
        $('#pageCountInfo').attr('data-bs-toggle', 'tooltip')
                           .attr('data-bs-placement', 'top');
        
        // Handle modal events
        $('#bookFormModal').on('hidden.bs.modal', function() {
            resetBookForm();
        });
        
        // Add/edit book button click
        $('#saveBookBtn').on('click', function() {
            saveBook();
        });
        
        // Edit from view modal
        $('#editBookFromViewBtn').on('click', function() {
            // Get the current book ID from the view modal
            const bookId = currentBookId;
            
            // Close view modal and open edit modal
            $('#viewBookModal').modal('hide');
            
            // Open the form in edit mode
            openBookFormForEdit(bookId);
        });
        
        // Delete book submit
        $('#deleteBookForm').on('submit', function(e) {
            e.preventDefault();
            if ($('#confirmDelete').is(':checked')) {
                deleteBook($('#deleteBookId').val());
            }
        });
        
        // Initialize DataTable
        function initBookDataTable() {
            dataTable = new DataTablesManager('booksTable', {
                columns: [
                    { data: 'id' },
                    { 
                        data: 'cover',
                        render: function(data) {
                            return `<img src="${data}" alt="Book Cover" class="img-thumbnail" style="height: 60px;">`;
                        }
                    },
                    { data: 'title' },
                    { data: 'author' },
                    { data: 'genre' },
                    { data: 'price' },
                    {
                        data: null,
                        render: function(data) {
                            return `
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-info view-book" data-id="${data.id}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary edit-book" data-id="${data.id}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-book" data-id="${data.id}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                            `;
                        }
                    }
                ],
                ajaxUrl: '/api/books',
                toastOptions: {
                    position: 'bottom-right',
                    autoClose: 4000,
                    hideProgressBar: false,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: true,
                    enableIcons: true
                }
            });
            
            // Add event listeners for action buttons
            $('#booksTable').on('click', '.view-book', function() {
                const bookId = $(this).data('id');
                viewBook(bookId);
            });
            
            $('#booksTable').on('click', '.edit-book', function() {
                const bookId = $(this).data('id');
                openBookFormForEdit(bookId);
            });
            
            $('#booksTable').on('click', '.delete-book', function() {
                const bookId = $(this).data('id');
                showDeleteConfirmation(bookId);
            });
        }
        
        // Initialize file upload handlers
        function initFileUploadHandlers() {
            // Cover image upload and preview
            $('#coverUpload').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            $('#coverPreview').attr('src', e.target.result);
                            $('#cover_image_data').val(e.target.result);
                        };
                        reader.readAsDataURL(file);
                    } else {
                        alert('Please select an image file for the cover.');
                        $(this).val('');
                    }
                }
            });
            
            // Handle manual changes to page count
            $('#pages').on('input', function() {
                // If user manually enters a page count, update the help text
                if ($(this).val() && $('#book_file_data').val()) {
                    $('#pagesHelp').text('Manual page count will override extracted count.');
                    
                    // Show info icon if not visible
                    if (!$('#pageCountInfo').is(':visible')) {
                        $('#pageCountInfo').show()
                                          .attr('data-bs-original-title', 'Manual override of extracted page count')
                                          .tooltip('dispose')
                                          .tooltip();
                    }
                } else if (!$(this).val()) {
                    // If field is emptied and there's a PDF uploaded
                    if ($('#book_file_data').val()) {
                        $('#pagesHelp').text('Page count will be extracted from PDF when saved.');
                        $('#pageCountInfo').hide();
                    } else {
                        $('#pagesHelp').text('Page count will be automatically extracted from PDF if left empty.');
                        $('#pageCountInfo').hide();
                    }
                }
            });
            
            // Book file upload
            $('#bookFileUpload').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    if (file.type === 'application/pdf') {
                        // Check file size (max 100MB)
                        if (file.size > 104857600) { // 100MB in bytes
                            alert('File size exceeds the maximum limit of 100MB.');
                            $(this).val('');
                            $('#currentFileInfo').text('');
                            return;
                        }
                        
                        // Show loading indicator
                        $('#currentFileInfo').html('<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Processing PDF...');
                        
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            $('#book_file_data').val(e.target.result);
                            $('#currentFileInfo').text(`Selected file: ${file.name} (${formatFileSize(file.size)})`);
                            
                            // Clear manual page count if it's empty or zero
                            const currentPages = $('#pages').val();
                            if (!currentPages || parseInt(currentPages) === 0) {
                                // Clear the tooltip/info if it exists
                                $('#pageCountInfo').hide().attr('data-bs-original-title', '');
                                
                                // Add processing indicator to pages field
                                $('#pagesHelp').html('<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Page count will be extracted when saved');
                            }
                        };
                        reader.readAsDataURL(file);
                    } else {
                        alert('Please select a PDF file for the book.');
                        $(this).val('');
                        $('#currentFileInfo').text('');
                    }
                }
            });
        }
        
        // Open Add Book Modal
        $('#addBookBtn').on('click', function() {
            resetBookForm();
            isEditMode = false;
            $('#bookFormTitle').text('Add New Book');
            $('#bookFormModal').modal('show');
        });
        
        // Open Edit Book Modal
        function openBookFormForEdit(bookId) {
            isEditMode = true;
            currentBookId = bookId;
            $('#bookFormTitle').text('Edit Book');
            $('#bookId').val(bookId);
            
            // Fetch book data and populate form
            $.ajax({
                url: `/api/books/${bookId}`,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const book = response.data;
                        
                        // Populate form fields
                        $('#title').val(book.b_title);
                        $('#author').val(book.b_author);
                        $('#publisher').val(book.b_publisher);
                        $('#publication_date').val(book.b_publication_date ? book.b_publication_date.split(' ')[0] : '');
                        $('#isbn').val(book.b_isbn);
                        $('#genre_id').val(book.b_genre_id);
                        $('#pages').val(book.b_pages);
                        $('#price').val(book.b_price);
                        $('#description').val(book.b_description);
                        
                        // If the book has a page count and it was automatically extracted from PDF
                        if (book.b_pages && book.b_file_path && book.b_file_path.endsWith('.pdf')) {
                            // Show the info icon with tooltip
                            $('#pageCountInfo').show()
                                               .attr('data-bs-original-title', 'Page count extracted from PDF')
                                               .tooltip('dispose')
                                               .tooltip();
                        } else {
                            $('#pageCountInfo').hide();
                        }
                        
                        // Show existing cover image
                        $('#coverPreview').attr('src', book.cover_url);
                        
                        // Show existing file info
                        if (book.b_file_path) {
                            $('#currentFileInfo').html(`Current file: <a href="${book.file_url}" target="_blank">${book.b_file_path}</a>`);
                        } else {
                            $('#currentFileInfo').text('No file attached');
                        }
                        
                        // Show the modal
                        $('#bookFormModal').modal('show');
                    } else {
                        showErrorAlert('Failed to load book data');
                    }
                },
                error: function() {
                    showErrorAlert('Error loading book data');
                }
            });
        }
        
        // View Book Details
        function viewBook(bookId) {
            currentBookId = bookId;
            
            $.ajax({
                url: `/api/books/${bookId}`,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const book = response.data;
                        
                        // Populate view modal
                        $('#viewTitle').text(book.b_title);
                        $('#viewAuthor').text(book.b_author);
                        $('#viewGenre').text(book.genre_name || book.genre || 'Uncategorized');
                        $('#viewPublisher').text(book.b_publisher || 'N/A');
                        $('#viewPublicationDate').text(book.b_publication_date ? formatDate(book.b_publication_date) : 'N/A');
                        $('#viewIsbn').text(book.b_isbn || 'N/A');
                        $('#viewPages').text(book.b_pages || 'N/A');
                        $('#viewPrice').text(book.b_price ? `$${parseFloat(book.b_price).toFixed(2)}` : 'N/A');
                        $('#viewDescription').text(book.b_description || 'No description available.');
                        $('#viewCoverImage').attr('src', book.cover_url);
                        
                        // Handle file link
                        if (book.file_url) {
                            $('#viewFileLink').attr('href', book.file_url);
                            $('#viewFileLink').html('<i class="bi bi-file-pdf"></i> View PDF');
                            $('#viewFileSection').show();
                        } else {
                            $('#viewFileSection').hide();
                        }
                        
                        // Show the modal
                        $('#viewBookModal').modal('show');
                    } else {
                        showErrorAlert('Failed to load book data');
                    }
                },
                error: function() {
                    showErrorAlert('Error loading book data');
                }
            });
        }
        
        // Show Delete Confirmation
        function showDeleteConfirmation(bookId) {
            $.ajax({
                url: `/api/books/${bookId}`,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#deleteBookId').val(bookId);
                        $('#deleteBookTitle').text(response.data.b_title);
                        $('#confirmDelete').prop('checked', false);
                        $('#deleteBookModal').modal('show');
                    } else {
                        showErrorAlert('Failed to load book data');
                    }
                },
                error: function() {
                    showErrorAlert('Error loading book data');
                }
            });
        }
        
        // Save Book (Create or Update)
        function saveBook() {
            // Validate form
            if (!$('#bookForm')[0].checkValidity()) {
                $('#bookForm')[0].reportValidity();
                return;
            }
            
            // Prepare data
            const bookId = $('#bookId').val();
            const formData = {
                title: $('#title').val(),
                author: $('#author').val(),
                publisher: $('#publisher').val(),
                publication_date: $('#publication_date').val(),
                isbn: $('#isbn').val(),
                genre_id: $('#genre_id').val(),
                pages: $('#pages').val() ? parseInt($('#pages').val()) : null,
                price: $('#price').val(),
                description: $('#description').val()
            };
            
            // Include image data if available
            if ($('#cover_image_data').val()) {
                formData.cover_image_data = $('#cover_image_data').val();
            }
            
            // Add book file data if available
            if ($('#book_file_data').val()) {
                formData.book_file_data = $('#book_file_data').val();
            }
            
            const endpoint = bookId ? `/api/books/${bookId}` : '/api/books';
            const method = bookId ? 'PUT' : 'POST';
            
            // Show loading state
            $('#saveBookBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
            
            // Send request
            $.ajax({
                url: endpoint,
                method: method,
                contentType: 'application/json',
                data: JSON.stringify(formData),
                success: function(response) {
                    if (response.success) {
                        // Check if page count was extracted
                        if (response.data.pageCount) {
                            // If we're still in the modal (user can see the response), show the extracted page count
                            const pageCount = response.data.pageCount;
                            // Use toast notification instead of alert
                            dataTable.showSuccessToast('Book Saved', `Book ${bookId ? 'updated' : 'added'} successfully. Extracted page count: ${pageCount}`);
                        } else {
                            // Regular success message
                            dataTable.showSuccessToast('Book Saved', `Book ${bookId ? 'updated' : 'added'} successfully`);
                        }
                        
                        // Close modal
                        $('#bookFormModal').modal('hide');
                        
                        // Refresh table using DataTablesManager
                        dataTable.refresh();
                    } else {
                        showErrorAlert(response.message || 'Error saving book');
                    }
                },
                error: function() {
                    showErrorAlert('Error processing request');
                },
                complete: function() {
                    // Reset button state
                    $('#saveBookBtn').prop('disabled', false).text('Save Book');
                    
                    // Reset the page help text
                    $('#pagesHelp').text('Page count will be automatically extracted from PDF if left empty.');
                }
            });
        }
        
        // Delete Book
        function deleteBook(bookId) {
            $.ajax({
                url: `/api/books/${bookId}`,
                method: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        $('#deleteBookModal').modal('hide');
                        
                        // Show success message with toast
                        dataTable.showSuccessToast('Book Deleted', response.message);
                        
                        // Refresh the DataTable using DataTablesManager
                        dataTable.refresh();
                    } else {
                        showErrorAlert(response.message);
                    }
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'An error occurred';
                    showErrorAlert(errorMessage);
                }
            });
        }
        
        // Reset Book Form
        function resetBookForm() {
            $('#bookForm')[0].reset();
            $('#bookId').val('');
            $('#coverPreview').attr('src', '/assets/images/book-cover/default-cover.svg');
            $('#cover_image_data').val('');
            $('#book_file_data').val('');
            $('#currentFileInfo').text('');
            $('#pages').attr('placeholder', 'Enter number of pages');
            $('#pageCountInfo').hide().attr('data-bs-original-title', '');
            $('#pagesHelp').text('Page count will be automatically extracted from PDF if left empty.');
        }
        
        // Helper for showing error alerts
        function showErrorAlert(message) {
            // Display toast notification instead of alert
            dataTable.showErrorToast('Error', message);
        }
        
        // Helper for showing success alerts
        function showSuccessAlert(message) {
            // Display toast notification instead of alert
            dataTable.showSuccessToast('Success', message);
        }
        
        // Helper for formatting file size
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        
        // Helper for formatting dates
        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString();
        }

        // ========== CATEGORY MANAGEMENT ==========
        
        // Load categories when the modal is opened
        $('#categoryManagementModal').on('show.bs.modal', function (e) {
            loadCategories();
        });
        
        // Handle category form submission
        $('#addCategoryForm').on('submit', function(e) {
            e.preventDefault();
            
            const categoryId = $('#editCategoryId').val();
            const categoryName = $('#categoryName').val().trim();
            
            if (!categoryName) {
                showCategoryAlert('Please enter a category name.', 'danger');
                return;
            }
            
            if (categoryId) {
                // Update existing category
                updateCategory(categoryId, categoryName);
            } else {
                // Add new category
                addCategory(categoryName);
            }
        });
        
        // Cancel editing category
        $('#cancelEditBtn').on('click', function() {
            resetCategoryForm();
        });
        
        // Open delete confirmation modal
        $(document).on('click', '.delete-category-btn', function() {
            const categoryId = $(this).data('id');
            const categoryName = $(this).data('name');
            const hasBooks = $(this).data('has-books');
            
            $('#deleteCategoryId').val(categoryId);
            $('#deleteCategoryName').text(categoryName);
            
            if (hasBooks) {
                $('#categoryHasBooks').removeClass('d-none');
            } else {
                $('#categoryHasBooks').addClass('d-none');
            }
            
            const deleteCategoryModal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));
            deleteCategoryModal.show();
        });
        
        // Handle confirm delete button
        $('#confirmDeleteCategoryBtn').on('click', function() {
            const categoryId = $('#deleteCategoryId').val();
            deleteCategory(categoryId);
        });
        
        // Handle edit category button
        $(document).on('click', '.edit-category-btn', function() {
            const categoryId = $(this).data('id');
            const categoryName = $(this).data('name');
            
            $('#editCategoryId').val(categoryId);
            $('#categoryName').val(categoryName);
            $('#saveCategoryBtn').html('<i class="bi bi-check-circle me-1"></i> Update Category');
            $('#cancelEditBtn').show();
        });
        
        // Function to load all categories
        function loadCategories() {
            $('#categoriesLoading').removeClass('d-none');
            $('#categoriesTableContainer').addClass('d-none');
            $('#categoriesEmpty').addClass('d-none');
            
            $.ajax({
                url: '/api/categories',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#categoriesLoading').addClass('d-none');
                    
                    if (response.success && response.data && response.data.length > 0) {
                        displayCategories(response.data);
                    } else {
                        $('#categoriesEmpty').removeClass('d-none');
                    }
                },
                error: function(xhr, status, error) {
                    $('#categoriesLoading').addClass('d-none');
                    $('#categoriesEmpty').removeClass('d-none');
                    showCategoryAlert('Failed to load categories: ' + error, 'danger');
                }
            });
        }
        
        // Function to display categories
        function displayCategories(categories) {
            const categoriesList = $('#categoriesList');
            categoriesList.empty();
            
            categories.forEach(function(category) {
                const hasBooks = category.book_count > 0;
                const item = `
                    <tr>
                        <td>${escapeHtml(category.g_name)}</td>
                        <td><span class="badge bg-primary rounded-pill">${category.book_count}</span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary edit-category-btn" 
                                    data-id="${category.g_id}" 
                                    data-name="${escapeHtml(category.g_name)}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger delete-category-btn" 
                                    data-id="${category.g_id}" 
                                    data-name="${escapeHtml(category.g_name)}"
                                    data-has-books="${hasBooks ? 'true' : 'false'}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                categoriesList.append(item);
            });
            
            $('#categoryCount').text(categories.length);
            $('#categoriesTableContainer').removeClass('d-none');
            
            // Also update the genre dropdown in the book form
            updateGenreDropdown(categories);
        }
        
        // Function to add a new category
        function addCategory(name) {
            $.ajax({
                url: '/api/categories',
                method: 'POST',
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify({ name: name }),
                success: function(response) {
                    if (response.success) {
                        showCategoryAlert('Category added successfully.', 'success');
                        resetCategoryForm();
                        loadCategories();
                    } else {
                        showCategoryAlert(response.message || 'Failed to add category.', 'danger');
                    }
                },
                error: function(xhr, status, error) {
                    let errorMessage = 'Failed to add category.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showCategoryAlert(errorMessage, 'danger');
                }
            });
        }
        
        // Function to update a category
        function updateCategory(id, name) {
            $.ajax({
                url: `/api/categories/${id}`,
                method: 'PUT',
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify({ name: name }),
                success: function(response) {
                    if (response.success) {
                        showCategoryAlert('Category updated successfully.', 'success');
                        resetCategoryForm();
                        loadCategories();
                    } else {
                        showCategoryAlert(response.message || 'Failed to update category.', 'danger');
                    }
                },
                error: function(xhr, status, error) {
                    let errorMessage = 'Failed to update category.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showCategoryAlert(errorMessage, 'danger');
                }
            });
        }
        
        // Function to delete a category
        function deleteCategory(id) {
            $.ajax({
                url: `/api/categories/${id}`,
                method: 'DELETE',
                dataType: 'json',
                success: function(response) {
                    // Close the delete modal
                    const deleteCategoryModal = bootstrap.Modal.getInstance(document.getElementById('deleteCategoryModal'));
                    deleteCategoryModal.hide();
                    
                    if (response.success) {
                        showCategoryAlert('Category deleted successfully.', 'success');
                        loadCategories();
                    } else {
                        showCategoryAlert(response.message || 'Failed to delete category.', 'danger');
                    }
                },
                error: function(xhr, status, error) {
                    // Close the delete modal
                    const deleteCategoryModal = bootstrap.Modal.getInstance(document.getElementById('deleteCategoryModal'));
                    deleteCategoryModal.hide();
                    
                    let errorMessage = 'Failed to delete category.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showCategoryAlert(errorMessage, 'danger');
                }
            });
        }
        
        // Function to reset the category form
        function resetCategoryForm() {
            $('#editCategoryId').val('');
            $('#categoryName').val('');
            $('#saveCategoryBtn').html('<i class="bi bi-plus-circle me-1"></i> Add Category');
            $('#cancelEditBtn').hide();
        }
        
        // Function to show category alerts
        function showCategoryAlert(message, type) {
            const alertElement = $('#categoryAlert');
            alertElement.text(message);
            alertElement.removeClass('d-none alert-success alert-danger alert-warning')
                .addClass(`alert-${type}`);
            
            // Auto-hide after 5 seconds
            setTimeout(function() {
                alertElement.addClass('d-none');
            }, 5000);
        }
        
        // Function to update the genre dropdown in the book form
        function updateGenreDropdown(categories) {
            const dropdown = $('#genre_id');
            const currentValue = dropdown.val();
            
            // Clear existing options except the placeholder
            dropdown.find('option:not(:first)').remove();
            
            // Add new options
            categories.forEach(function(category) {
                dropdown.append(`<option value="${category.g_id}">${escapeHtml(category.g_name)}</option>`);
            });
            
            // Restore previously selected value if it still exists
            if (currentValue) {
                dropdown.val(currentValue);
            }
        }
        
        // Helper function to escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    });
</script>

<?php
include $footerPath;
?>