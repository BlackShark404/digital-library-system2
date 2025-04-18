<?php
include $headerPath;

// Hardcoded wishlist data
$wishlist_books = [
    [
        'wishlist_id' => 1,
        'book_id' => 101,
        'title' => 'The Silent Patient',
        'author' => 'Alex Michaelides',
        'cover_image' => 'https://placekitten.com/200/300', // Placeholder image
        'price' => 24.99,
        'discount_price' => 19.99,
        'publication_date' => '2019-02-05',
        'genre' => 'Psychological Thriller'
    ],
    [
        'wishlist_id' => 2,
        'book_id' => 102,
        'title' => 'Atomic Habits',
        'author' => 'James Clear',
        'cover_image' => 'https://placekitten.com/201/300', // Placeholder image
        'price' => 27.99,
        'discount_price' => null,
        'publication_date' => '2018-10-16',
        'genre' => 'Self-Help'
    ],
    [
        'wishlist_id' => 3,
        'book_id' => 103,
        'title' => 'Project Hail Mary',
        'author' => 'Andy Weir',
        'cover_image' => 'https://placekitten.com/202/300', // Placeholder image
        'price' => 28.99,
        'discount_price' => 22.50,
        'publication_date' => '2021-05-04',
        'genre' => 'Science Fiction'
    ],
    [
        'wishlist_id' => 4,
        'book_id' => 104,
        'title' => 'The Midnight Library',
        'author' => 'Matt Haig',
        'cover_image' => 'https://placekitten.com/203/300', // Placeholder image
        'price' => 26.99,
        'discount_price' => 21.99,
        'publication_date' => '2020-09-29',
        'genre' => 'Fantasy Fiction'
    ],
    [
        'wishlist_id' => 5,
        'book_id' => 105,
        'title' => 'The Psychology of Money',
        'author' => 'Morgan Housel',
        'cover_image' => 'https://placekitten.com/204/300', // Placeholder image
        'price' => 19.99,
        'discount_price' => 16.99,
        'publication_date' => '2020-09-08',
        'genre' => 'Finance'
    ]
];

// Check if we're simulating an empty wishlist
$show_empty = isset($_GET['empty']) && $_GET['empty'] == 'true';
if ($show_empty) {
    $wishlist_books = [];
}
?>

<!-- Main content area -->
<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">My Wishlist</h1>
            <p class="text-muted">Books you've saved for later</p>
        </div>
        <div class="col-auto">
            <a href="../browse_books.php" class="btn btn-outline-primary">
                <i class="bi bi-search"></i> Browse More Books
            </a>
        </div>
    </div>

    <?php if (empty($wishlist_books)): ?>
        <!-- Empty state -->
        <div class="card shadow-sm border-0 p-5 text-center">
            <div class="py-5">
                <i class="bi bi-heart text-muted" style="font-size: 3rem;"></i>
                <h3 class="mt-4">Your wishlist is empty</h3>
                <p class="text-muted mb-4">Save books you're interested in to your wishlist for easy access later.</p>
                <a href="../browse_books.php" class="btn btn-primary">
                    <i class="bi bi-search"></i> Discover Books
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Wishlist items -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($wishlist_books as $book): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="position-relative">
                            <img src="<?php echo $book['cover_image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($book['title']); ?>" style="height: 250px; object-fit: cover;">
                            <form method="post" action="remove_wishlist.php" class="position-absolute" style="top: 10px; right: 10px;">
                                <input type="hidden" name="wishlist_id" value="<?php echo $book['wishlist_id']; ?>">
                                <button type="submit" class="btn btn-sm btn-light rounded-circle" data-bs-toggle="tooltip" title="Remove from wishlist">
                                    <i class="bi bi-x"></i>
                                </button>
                            </form>
                        </div>
                        <div class="card-body">
                            <span class="badge bg-secondary mb-2"><?php echo htmlspecialchars($book['genre']); ?></span>
                            <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                            <p class="card-text text-muted">by <?php echo htmlspecialchars($book['author']); ?></p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <?php if ($book['discount_price']): ?>
                                    <div>
                                        <span class="text-decoration-line-through text-muted">$<?php echo number_format($book['price'], 2); ?></span>
                                        <span class="ms-2 fw-bold text-danger">$<?php echo number_format($book['discount_price'], 2); ?></span>
                                    </div>
                                <?php else: ?>
                                    <div>
                                        <span class="fw-bold">$<?php echo number_format($book['price'], 2); ?></span>
                                    </div>
                                <?php endif; ?>
                                <small class="text-muted">Published: <?php echo date('M Y', strtotime($book['publication_date'])); ?></small>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <div class="d-grid gap-2 d-md-flex justify-content-between">
                                <a href="../book_detail.php?id=<?php echo $book['book_id']; ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-info-circle"></i> Details
                                </a>
                                <form method="post" action="add_to_cart.php" class="d-inline">
                                    <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-cart-plus"></i> Add to Cart
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include $footerPath; ?>