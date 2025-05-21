<?php
include $headerPath;
?>

<div class="container">
    <h1 class="mb-4"><i class="bi bi-book me-2"></i>Book Management</h1>

    <!-- Alert Container -->
    <div id="alertContainer"></div>

    <!-- Book Actions -->
    <div class="mb-4">
        <button id="addBookBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bookFormModal">
            <i class="bi bi-plus-circle me-2"></i>Add New Book
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
                                    <input type="number" class="form-control" id="price" name="price" step="0.01" min="0">
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
                            <input type="number" class="form-control" id="pages" name="pages" min="1">
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

<!-- Include jQuery first -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">

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
        
        // Initialize file upload previews
        initFileUploadHandlers();
        
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
                                    <button type="button" class="btn btn-sm btn-info view-book" data-id="${data.id}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary edit-book" data-id="${data.id}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-book" data-id="${data.id}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                ajaxUrl: '/api/books'
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
                        
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            $('#book_file_data').val(e.target.result);
                            $('#currentFileInfo').text(`Selected file: ${file.name} (${formatFileSize(file.size)})`);
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
                        $('#viewGenre').text(book.genre || 'Uncategorized');
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
                        // Close modal
                        $('#bookFormModal').modal('hide');
                        
                        // Show success message
                        showSuccessAlert(`Book ${bookId ? 'updated' : 'added'} successfully`);
                        
                        // Refresh table
                        if (dataTable.ajax && typeof dataTable.ajax.reload === 'function') {
                            dataTable.ajax.reload();
                        } else {
                            location.reload(); // Fallback
                        }
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
                        
                        // Show success message
                        dataTable.showSuccessToast('Book Deleted', response.message);
                        
                        // Refresh the DataTable
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
        }
        
        // Helper for showing error alerts
        function showErrorAlert(message) {
            $('#alertContainer').html(`
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `);
        }
        
        // Helper for showing success alerts
        function showSuccessAlert(message) {
            $('#alertContainer').html(`
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `);
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
    });
</script>

<?php
include $footerPath;
?>