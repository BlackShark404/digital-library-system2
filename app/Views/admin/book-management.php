<?php
include $headerPath;

// Hardcoded sample book data
$books = [
    [
        'id' => 1,
        'title' => 'The Great Gatsby',
        'author' => 'F. Scott Fitzgerald',
        'genre' => 'Classic Literature',
        'price' => 12.99,
        'status' => 'Published',
        'cover' => 'https://placekitten.com/200/300', // Placeholder image
        'description' => 'A story about wealth, love, and the American dream.',
        'pages' => 180,
        'published_date' => '2023-01-15'
    ],
    [
        'id' => 2,
        'title' => 'To Kill a Mockingbird',
        'author' => 'Harper Lee',
        'genre' => 'Fiction',
        'price' => 14.99,
        'status' => 'Published',
        'cover' => 'To Kill a Mockingbird Cover 1961.webp', // Placeholder image
        'description' => 'A classic of modern American literature about racial inequality.',
        'pages' => 281,
        'published_date' => '2023-02-20'
    ],
    [
        'id' => 3,
        'title' => 'The Hobbit',
        'author' => 'J.R.R. Tolkien',
        'genre' => 'Fantasy',
        'price' => 19.99,
        'status' => 'Published',
        'cover' => 'https://placekitten.com/202/300', // Placeholder image
        'description' => 'A fantasy novel about the adventures of Bilbo Baggins.',
        'pages' => 310,
        'published_date' => '2023-03-05'
    ],
    [
        'id' => 4,
        'title' => 'Crime and Punishment',
        'author' => 'Fyodor Dostoevsky',
        'genre' => 'Psychological Fiction',
        'price' => 16.99,
        'status' => 'Draft',
        'cover' => 'https://placekitten.com/203/300', // Placeholder image
        'description' => 'A novel that explores the moral dilemmas of a poor ex-student.',
        'pages' => 430,
        'published_date' => null
    ],
    [
        'id' => 5,
        'title' => 'Pride and Prejudice',
        'author' => 'Jane Austen',
        'genre' => 'Romance',
        'price' => 11.99,
        'status' => 'Published',
        'cover' => 'https://placekitten.com/204/300', // Placeholder image
        'description' => 'A romantic novel about the Bennet sisters finding love.',
        'pages' => 279,
        'published_date' => '2023-04-12'
    ]
];

// Filter books based on search term if provided
$search_term = isset($_GET['search']) ? $_GET['search'] : '';
$filtered_books = $books;
if (!empty($search_term)) {
    $filtered_books = array_filter($books, function ($book) use ($search_term) {
        return (
            stripos($book['title'], $search_term) !== false ||
            stripos($book['author'], $search_term) !== false ||
            stripos($book['genre'], $search_term) !== false
        );
    });
}

// Filter books based on status if provided
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
if (!empty($status_filter)) {
    $filtered_books = array_filter($filtered_books, function ($book) use ($status_filter) {
        return strtolower($book['status']) === strtolower($status_filter);
    });
}
?>

<div class="container">
    <h1 class="mb-4"><i class="bi bi-book me-2"></i>Book Management</h1>

    <!-- Search and Filter Options -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search books..." id="bookSearch" value="<?php echo htmlspecialchars($search_term); ?>">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="genreFilter">
                        <option value="">All Genres</option>
                        <option value="Fiction">Fiction</option>
                        <option value="Classic Literature">Classic Literature</option>
                        <option value="Fantasy">Fantasy</option>
                        <option value="Psychological Fiction">Psychological Fiction</option>
                        <option value="Romance">Romance</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="Published" <?php echo ($status_filter === 'published') ? 'selected' : ''; ?>>Published</option>
                        <option value="Draft" <?php echo ($status_filter === 'draft') ? 'selected' : ''; ?>>Draft</option>
                    </select>
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
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Cover</th>
                            <th scope="col">Title</th>
                            <th scope="col">Author</th>
                            <th scope="col">Genre</th>
                            <th scope="col">Price</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($filtered_books) > 0): ?>
                            <?php foreach ($filtered_books as $book): ?>
                                <tr>
                                    <td><?php echo $book['id']; ?></td>
                                    <td>
                                        <img src="<?php echo $book['cover']; ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" class="img-thumbnail" width="50">
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($book['title']); ?>
                                        <div class="small text-muted"><?php echo $book['pages']; ?> pages</div>
                                    </td>
                                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                                    <td><?php echo htmlspecialchars($book['genre']); ?></td>
                                    <td>$<?php echo number_format($book['price'], 2); ?></td>
                                    <td>
                                        <span class="badge <?php echo ($book['status'] === 'Published') ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                            <?php echo $book['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editBookModal<?php echo $book['id']; ?>" title="Edit Book">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewBookModal<?php echo $book['id']; ?>" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteBookModal<?php echo $book['id']; ?>" title="Delete Book">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-search display-6"></i>
                                        <p class="mt-2">No books found matching your criteria.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center justify-content-md-end mb-0">
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

<!-- Add Book Modal -->
<div class="modal fade" id="addBookModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg"> <!-- Scrollable and wider -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-book-medical me-2"></i>Add New Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <form id="addBookForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="addTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="addTitle" name="title" required>
                        </div>
                        <div class="col-md-6">
                            <label for="addAuthor" class="form-label">Author</label>
                            <input type="text" class="form-control" id="addAuthor" name="author" required>
                        </div>
                        <div class="col-md-6">
                            <label for="addGenre" class="form-label">Genre</label>
                            <select class="form-select" id="addGenre" name="genre" required>
                                <option value="">Select Genre</option>
                                <option value="Fiction">Fiction</option>
                                <option value="Non-Fiction">Non-Fiction</option>
                                <option value="Fantasy">Fantasy</option>
                                <option value="Science Fiction">Science Fiction</option>
                                <option value="Mystery">Mystery</option>
                                <option value="Romance">Romance</option>
                                <option value="Classic Literature">Classic Literature</option>
                                <option value="Biography">Biography</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="addPublicationDate" class="form-label">Publication Date</label>
                            <input type="date" class="form-control" id="addPublicationDate" name="publication_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="addPrice" class="form-label">Price ($)</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="addPrice" name="price" required>
                        </div>
                        <div class="col-md-6">
                            <label for="addPages" class="form-label">Pages</label>
                            <input type="number" min="1" class="form-control" id="addPages" name="pages" required>
                        </div>
                        <div class="col-md-6">
                            <label for="addStatus" class="form-label">Status</label>
                            <select class="form-select" id="addStatus" name="status" required>
                                <option value="Published" selected>Published</option>
                                <option value="Draft">Draft</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="addCover" class="form-label">Cover Image</label>
                            <input type="file" class="form-control" id="addCover" name="cover">
                        </div>
                        <div class="col-12">
                            <label for="addDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="addDescription" name="description" rows="2" required></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addBookForm" class="btn btn-primary">Add Book</button>
            </div>
        </div>
    </div>
</div>


<?php foreach ($books as $book): ?>
    <!-- Edit Book Modal -->
    <div class="modal fade" id="editBookModal<?php echo $book['id']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-pen me-2"></i>Edit Book: <?php echo htmlspecialchars($book['title']); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editBookForm<?php echo $book['id']; ?>">
                        <div class="mb-3">
                            <label for="editTitle<?php echo $book['id']; ?>" class="form-label">Title</label>
                            <input type="text" class="form-control" id="editTitle<?php echo $book['id']; ?>" value="<?php echo htmlspecialchars($book['title']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="editAuthor<?php echo $book['id']; ?>" class="form-label">Author</label>
                            <input type="text" class="form-control" id="editAuthor<?php echo $book['id']; ?>" value="<?php echo htmlspecialchars($book['author']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="editGenre<?php echo $book['id']; ?>" class="form-label">Genre</label>
                            <select class="form-select" id="editGenre<?php echo $book['id']; ?>" required>
                                <option value="Fiction" <?php echo ($book['genre'] === 'Fiction') ? 'selected' : ''; ?>>Fiction</option>
                                <option value="Non-Fiction" <?php echo ($book['genre'] === 'Non-Fiction') ? 'selected' : ''; ?>>Non-Fiction</option>
                                <option value="Fantasy" <?php echo ($book['genre'] === 'Fantasy') ? 'selected' : ''; ?>>Fantasy</option>
                                <option value="Science Fiction" <?php echo ($book['genre'] === 'Science Fiction') ? 'selected' : ''; ?>>Science Fiction</option>
                                <option value="Mystery" <?php echo ($book['genre'] === 'Mystery') ? 'selected' : ''; ?>>Mystery</option>
                                <option value="Romance" <?php echo ($book['genre'] === 'Romance') ? 'selected' : ''; ?>>Romance</option>
                                <option value="Classic Literature" <?php echo ($book['genre'] === 'Classic Literature') ? 'selected' : ''; ?>>Classic Literature</option>
                                <option value="Psychological Fiction" <?php echo ($book['genre'] === 'Psychological Fiction') ? 'selected' : ''; ?>>Psychological Fiction</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editPrice<?php echo $book['id']; ?>" class="form-label">Price ($)</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="editPrice<?php echo $book['id']; ?>" value="<?php echo $book['price']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPages<?php echo $book['id']; ?>" class="form-label">Pages</label>
                            <input type="number" min="1" class="form-control" id="editPages<?php echo $book['id']; ?>" value="<?php echo $book['pages']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="editStatus<?php echo $book['id']; ?>" class="form-label">Status</label>
                            <select class="form-select" id="editStatus<?php echo $book['id']; ?>" required>
                                <option value="Published" <?php echo ($book['status'] === 'Published') ? 'selected' : ''; ?>>Published</option>
                                <option value="Draft" <?php echo ($book['status'] === 'Draft') ? 'selected' : ''; ?>>Draft</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editCover<?php echo $book['id']; ?>" class="form-label">Cover Image (leave blank to keep current)</label>
                            <input type="file" class="form-control" id="editCover<?php echo $book['id']; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="editDescription<?php echo $book['id']; ?>" class="form-label">Description</label>
                            <textarea class="form-control" id="editDescription<?php echo $book['id']; ?>" rows="3" required><?php echo htmlspecialchars($book['description']); ?></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="editBookForm<?php echo $book['id']; ?>" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Book Details Modal -->
    <div class="modal fade" id="viewBookModal<?php echo $book['id']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-book me-2"></i>Book Details: <?php echo htmlspecialchars($book['title']); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3">Basic Information</h6>
                            <p><strong>Book ID:</strong> <?php echo $book['id']; ?></p>
                            <p><strong>Title:</strong> <?php echo htmlspecialchars($book['title']); ?></p>
                            <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
                            <p><strong>Genre:</strong> <span class="badge bg-info"><?php echo htmlspecialchars($book['genre']); ?></span></p>
                            <p><strong>Status:</strong> <span class="badge <?php echo ($book['status'] === 'Published') ? 'bg-success' : 'bg-warning text-dark'; ?>"><?php echo $book['status']; ?></span></p>
                            <p><strong>Published Date:</strong> <?php echo $book['published_date'] ?? 'Not published'; ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3">Book Details</h6>
                            <p><strong>Price:</strong> $<?php echo number_format($book['price'], 2); ?></p>
                            <p><strong>Pages:</strong> <?php echo $book['pages']; ?></p>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($book['description']); ?></p>
                            <div class="text-center mt-3">
                                <img src="<?php echo $book['cover']; ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" class="img-thumbnail" style="max-height: 150px">
                            </div>
                        </div>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3 mt-4">Book Statistics</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Total Sales:</strong> 142</p>
                            <p><strong>Rating:</strong> 4.5/5</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Total Reviews:</strong> 27</p>
                            <p><strong>Time to Read:</strong> ~<?php echo round($book['pages'] / 30); ?> hours</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Downloads:</strong> 86</p>
                            <p><strong>Added:</strong> <?php echo date('Y-m-d', strtotime('-'.rand(30, 180).' days')); ?></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editBookModal<?php echo $book['id']; ?>">Edit Book</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Book Confirmation Modal -->
    <div class="modal fade" id="deleteBookModal<?php echo $book['id']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-triangle-exclamation me-2 text-danger"></i>Delete Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the book <strong><?php echo htmlspecialchars($book['title']); ?></strong>?</p>
                    <p class="text-danger"><strong>Warning:</strong> This action cannot be undone. All book data will be permanently removed.</p>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="confirmDelete<?php echo $book['id']; ?>" required>
                        <label class="form-check-label" for="confirmDelete<?php echo $book['id']; ?>">
                            I understand the consequences of this action
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="deleteBookBtn<?php echo $book['id']; ?>" disabled>Delete Book</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>


<script src="/assets/js/utility/toast-notifications.js"></script>
<script src="/assets/js/utility/form-handler.js"></script>
<script>
    handleFormSubmission("addBookForm", "/admin/book-management");
    
    // Enable delete buttons when confirmation checkbox is checked
    document.addEventListener('DOMContentLoaded', function() {
        <?php foreach ($books as $book): ?>
        const confirmDelete<?php echo $book['id']; ?> = document.getElementById('confirmDelete<?php echo $book['id']; ?>');
        const deleteBookBtn<?php echo $book['id']; ?> = document.getElementById('deleteBookBtn<?php echo $book['id']; ?>');
        
        if (confirmDelete<?php echo $book['id']; ?> && deleteBookBtn<?php echo $book['id']; ?>) {
            confirmDelete<?php echo $book['id']; ?>.addEventListener('change', function() {
                deleteBookBtn<?php echo $book['id']; ?>.disabled = !this.checked;
            });
        }
        <?php endforeach; ?>
    });
</script>
<?php
include $footerPath;
?>