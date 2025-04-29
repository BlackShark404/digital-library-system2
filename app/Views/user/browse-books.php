<?php
// Include header
include $headerPath;

// Hardcoded book data
$books = [
    [
        'id' => 1,
        'title' => 'The Great Gatsby',
        'author' => 'F. Scott Fitzgerald',
        'genre' => 'Classic',
        'description' => 'A story of wealth, love, and the American Dream in the 1920s.',
        'cover_image' => '../assets/images/gatsby.jpg',
        'price' => 9.99,
        'published_date' => '1925-04-10'
    ],
    [
        'id' => 2,
        'title' => 'To Kill a Mockingbird',
        'author' => 'Harper Lee',
        'genre' => 'Classic',
        'description' => 'A powerful story of growing up amid racial injustice in the American South.',
        'cover_image' => 'To Kill a Mockingbird Cover 1961.webpg',
        'price' => 8.99,
        'published_date' => '1960-07-11'
    ],
    [
        'id' => 3,
        'title' => 'Harry Potter and the Philosopher\'s Stone',
        'author' => 'J.K. Rowling',
        'genre' => 'Fantasy',
        'description' => 'The first adventure of a young wizard at Hogwarts School of Witchcraft and Wizardry.',
        'cover_image' => '../assets/images/harry.jpg',
        'price' => 12.99,
        'published_date' => '1997-06-26'
    ],
    [
        'id' => 4,
        'title' => 'Pride and Prejudice',
        'author' => 'Jane Austen',
        'genre' => 'Romance',
        'description' => 'A classic tale of love and misunderstanding among British landed gentry.',
        'cover_image' => '../assets/images/pride.jpg',
        'price' => 7.99,
        'published_date' => '1813-01-28'
    ],
    [
        'id' => 5,
        'title' => 'The Hunger Games',
        'author' => 'Suzanne Collins',
        'genre' => 'Science Fiction',
        'description' => 'In a dystopian future, teenagers fight to the death in a televised spectacle.',
        'cover_image' => '../assets/images/hunger.jpg',
        'price' => 10.99,
        'published_date' => '2008-09-14'
    ],
    [
        'id' => 6,
        'title' => 'The Hobbit',
        'author' => 'J.R.R. Tolkien',
        'genre' => 'Fantasy',
        'description' => 'The precursor to The Lord of the Rings, following Bilbo Baggins on an unexpected journey.',
        'cover_image' => '../assets/images/hobbit.jpg',
        'price' => 11.99,
        'published_date' => '1937-09-21'
    ],
    [
        'id' => 7,
        'title' => '1984',
        'author' => 'George Orwell',
        'genre' => 'Dystopian',
        'description' => 'A chilling portrayal of a totalitarian future society with constant surveillance.',
        'cover_image' => '../assets/images/1984.jpg',
        'price' => 9.99,
        'published_date' => '1949-06-08'
    ],
    [
        'id' => 8,
        'title' => 'The Catcher in the Rye',
        'author' => 'J.D. Salinger',
        'genre' => 'Coming-of-age',
        'description' => 'A teenager\'s alienation from the adult world as he navigates New York City.',
        'cover_image' => '../assets/images/catcher.jpg',
        'price' => 8.50,
        'published_date' => '1951-07-16'
    ],
    [
        'id' => 9,
        'title' => 'The Alchemist',
        'author' => 'Paulo Coelho',
        'genre' => 'Adventure',
        'description' => 'A shepherd boy\'s journey to find a hidden treasure near the Egyptian pyramids.',
        'cover_image' => '../assets/images/alchemist.jpg',
        'price' => 0.00,
        'published_date' => '1988-01-01'
    ],
    [
        'id' => 10,
        'title' => 'Brave New World',
        'author' => 'Aldous Huxley',
        'genre' => 'Dystopian',
        'description' => 'A futuristic society where humans are genetically bred and pharmaceutically controlled.',
        'cover_image' => '../assets/images/brave.jpg',
        'price' => 9.99,
        'published_date' => '1932-01-01'
    ],
    [
        'id' => 11,
        'title' => 'The Lord of the Rings',
        'author' => 'J.R.R. Tolkien',
        'genre' => 'Fantasy',
        'description' => 'Epic fantasy trilogy about the quest to destroy a powerful ring.',
        'cover_image' => '../assets/images/lotr.jpg',
        'price' => 19.99,
        'published_date' => '1954-07-29'
    ],
    [
        'id' => 12,
        'title' => 'Crime and Punishment',
        'author' => 'Fyodor Dostoevsky',
        'genre' => 'Psychological Fiction',
        'description' => 'A psychological account of a poor student\'s murder and the moral dilemmas that follow.',
        'cover_image' => '../assets/images/crime.jpg',
        'price' => 8.99,
        'published_date' => '1866-01-01'
    ]
];

// Hardcoded genres
$genres = ['Classic', 'Fantasy', 'Romance', 'Science Fiction', 'Dystopian', 'Coming-of-age', 'Adventure', 'Psychological Fiction'];

// Get filter parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$genre = isset($_GET['genre']) ? $_GET['genre'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'title_asc';

// Filter books based on search term
if (!empty($search)) {
    $filtered_books = [];
    foreach ($books as $book) {
        if (stripos($book['title'], $search) !== false || stripos($book['author'], $search) !== false) {
            $filtered_books[] = $book;
        }
    }
    $books = $filtered_books;
}

// Filter books based on genre
if (!empty($genre)) {
    $filtered_books = [];
    foreach ($books as $book) {
        if ($book['genre'] === $genre) {
            $filtered_books[] = $book;
        }
    }
    $books = $filtered_books;
}

// Sort books
switch ($sort) {
    case 'title_desc':
        usort($books, function ($a, $b) {
            return strcmp($b['title'], $a['title']);
        });
        break;
    case 'author_asc':
        usort($books, function ($a, $b) {
            return strcmp($a['author'], $b['author']);
        });
        break;
    case 'author_desc':
        usort($books, function ($a, $b) {
            return strcmp($b['author'], $a['author']);
        });
        break;
    case 'published_asc':
        usort($books, function ($a, $b) {
            return strcmp($a['published_date'], $b['published_date']);
        });
        break;
    case 'published_desc':
        usort($books, function ($a, $b) {
            return strcmp($b['published_date'], $a['published_date']);
        });
        break;
    case 'title_asc':
    default:
        usort($books, function ($a, $b) {
            return strcmp($a['title'], $b['title']);
        });
        break;
}
?>

<div class="container my-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="mb-3">Browse Books</h1>

            <!-- Search and Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="" class="row g-3">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" placeholder="Search titles or authors" name="search" value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="genre">
                                <option value="">All Genres</option>
                                <?php foreach ($genres as $g): ?>
                                    <option value="<?php echo htmlspecialchars($g); ?>" <?php echo ($g === $genre) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($g); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="sort">
                                <option value="title_asc" <?php echo ($sort === 'title_asc') ? 'selected' : ''; ?>>Title (A-Z)</option>
                                <option value="title_desc" <?php echo ($sort === 'title_desc') ? 'selected' : ''; ?>>Title (Z-A)</option>
                                <option value="author_asc" <?php echo ($sort === 'author_asc') ? 'selected' : ''; ?>>Author (A-Z)</option>
                                <option value="author_desc" <?php echo ($sort === 'author_desc') ? 'selected' : ''; ?>>Author (Z-A)</option>
                                <option value="published_asc" <?php echo ($sort === 'published_asc') ? 'selected' : ''; ?>>Published (Oldest)</option>
                                <option value="published_desc" <?php echo ($sort === 'published_desc') ? 'selected' : ''; ?>>Published (Newest)</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                        </div>
                    </form>
                </div>
            </div>

            <!-- Book Grid -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
                <?php
                if (count($books) > 0) {
                    foreach ($books as $book) {
                ?>
                        <div class="col">
                            <div class="card h-100 shadow-sm">
                                <div class="position-relative">
                                    <img src="<?php echo !empty($book['cover_image']) ? htmlspecialchars($book['cover_image']) : '../assets/images/book-placeholder.jpg'; ?>"
                                        class="card-img-top"
                                        alt="<?php echo htmlspecialchars($book['title']); ?>"
                                        style="height: 250px; object-fit: cover;">

                                    <!-- Wishlist button -->
                                    <button type="button"
                                        class="btn btn-sm position-absolute top-0 end-0 m-2 text-danger bg-light rounded-circle p-2 wishlist-toggle"
                                        data-book-id="<?php echo $book['id']; ?>"
                                        data-bs-toggle="tooltip"
                                        data-bs-title="Add to wishlist">
                                        <i class="bi bi-heart"></i>
                                    </button>
                                </div>

                                <div class="card-body">
                                    <h5 class="card-title text-truncate" title="<?php echo htmlspecialchars($book['title']); ?>">
                                        <?php echo htmlspecialchars($book['title']); ?>
                                    </h5>
                                    <p class="card-text text-muted mb-1">
                                        by <?php echo htmlspecialchars($book['author']); ?>
                                    </p>
                                    <p class="card-text mb-2">
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($book['genre']); ?></span>
                                    </p>
                                    <p class="card-text small text-truncate" title="<?php echo htmlspecialchars($book['description']); ?>">
                                        <?php echo htmlspecialchars($book['description']); ?>
                                    </p>
                                </div>
                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">
                                        <?php
                                        if ($book['price'] > 0) {
                                            echo '$' . number_format($book['price'], 2);
                                        } else {
                                            echo '<span class="text-success">Free</span>';
                                        }
                                        ?>
                                    </span>
                                    <div>
                                        <a href="book_details.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline-primary">Details</a>
                                        <a href="read.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-primary">Read</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                } else {
                    ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No books found matching your criteria. Try adjusting your filters.
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>

            <!-- Pagination -->
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle wishlist toggles
        const wishlistButtons = document.querySelectorAll('.wishlist-toggle');
        wishlistButtons.forEach(button => {
            button.addEventListener('click', function() {
                const bookId = this.getAttribute('data-book-id');
                const icon = this.querySelector('i');

                // Toggle icon
                if (icon.classList.contains('bi-heart')) {
                    icon.classList.remove('bi-heart');
                    icon.classList.add('bi-heart-fill');

                    // Show toast notification
                    showToast('Added to wishlist', 'success');
                } else {
                    icon.classList.remove('bi-heart-fill');
                    icon.classList.add('bi-heart');

                    // Show toast notification
                    showToast('Removed from wishlist', 'info');
                }
            });
        });

        // Toast notification function
        function showToast(message, type = 'info') {
            // Create toast container if it doesn't exist
            let toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
                document.body.appendChild(toastContainer);
            }

            // Create toast element
            const toastEl = document.createElement('div');
            toastEl.className = `toast align-items-center text-white bg-${type} border-0`;
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            toastEl.setAttribute('aria-atomic', 'true');

            // Toast content
            toastEl.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;

            // Add to container
            toastContainer.appendChild(toastEl);

            // Initialize and show toast
            const toast = new bootstrap.Toast(toastEl, {
                delay: 3000
            });
            toast.show();

            // Remove from DOM after hiding
            toastEl.addEventListener('hidden.bs.toast', function() {
                toastEl.remove();
            });
        }
    });
</script>

<?php
// Include footer
include $footerPath;
?>