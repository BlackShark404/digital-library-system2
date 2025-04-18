<?php
// Include header
include_once $headerPath;

// Check if user is logged in but is not an admin
// if (!is_logged_in() || is_admin()) {
//     // Redirect to login page
//     header('Location: login.php');
//     exit;
// }

// In a real application, you would fetch this data from your database
// For demo purposes, I'm using dummy data
$reading_history = [350, 420, 380, 510, 480, 620, 750, 780, 650, 820, 900, 950];
$books_read = [4, 5, 3, 6, 4, 7, 8, 6, 5, 7, 9, 8];
$hours_spent = [15, 18, 12, 24, 20, 28, 32, 30, 26, 35, 40, 38];
$completion_rate = [75, 80, 65, 85, 75, 90, 95, 85, 80, 88, 92, 90];

$current_books = [
    ['id' => 1, 'title' => 'The Great Novel', 'author' => 'John Author', 'progress' => 65, 'last_read' => '2 hours ago'],
    ['id' => 2, 'title' => 'Mystery of the Century', 'author' => 'Jane Writer', 'progress' => 32, 'last_read' => '1 day ago'],
    ['id' => 3, 'title' => 'Future Technology', 'author' => 'Alan Scientist', 'progress' => 48, 'last_read' => '3 days ago']
];

$recommended_books = [
    ['id' => 4, 'title' => 'History Uncovered', 'author' => 'Helen Historian', 'rating' => 4.5, 'genre' => 'History'],
    ['id' => 5, 'title' => 'Poetry Collection', 'author' => 'Robert Poet', 'rating' => 4.4, 'genre' => 'Poetry'],
    ['id' => 6, 'title' => 'Modern Architecture', 'author' => 'David Designer', 'rating' => 4.7, 'genre' => 'Art'],
    ['id' => 7, 'title' => 'Cooking Masters', 'author' => 'Gordon Chef', 'rating' => 4.6, 'genre' => 'Cooking']
];

$reading_achievements = [
    ['title' => 'Bookworm', 'description' => 'Read 10 books', 'progress' => 70, 'icon' => 'book'],
    ['title' => 'Speed Reader', 'description' => 'Finish a book in under 3 days', 'progress' => 100, 'icon' => 'lightning'],
    ['title' => 'Genre Explorer', 'description' => 'Read books from 5 different genres', 'progress' => 60, 'icon' => 'globe'],
    ['title' => 'Night Owl', 'description' => 'Read for 10 hours during night time', 'progress' => 90, 'icon' => 'moon']
];

$reading_stats = [
    'total_books' => 62,
    'total_pages' => 18560,
    'total_hours' => 312,
    'favorite_genre' => 'Mystery',
    'fastest_book' => 'Short Stories Collection',
    'longest_book' => 'Epic Fantasy Saga',
    'avg_rating' => 4.2
];
?>

<div class="container-fluid px-4">
    <h1 class="mt-4 mb-4">My Reading Dashboard</h1>

    <!-- Quick Stats -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Books Read</h5>
                            <h2 class="mt-2 mb-0"><?php echo $reading_stats['total_books']; ?></h2>
                        </div>
                        <i class="bi bi-book fs-1"></i>
                    </div>
                    <p class="mt-2 mb-0">
                        <i class="bi bi-arrow-up"></i> <?php echo end($books_read); ?> this month
                    </p>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="reading.php">View History</a>
                    <div class="small text-white"><i class="bi bi-chevron-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Reading Time</h5>
                            <h2 class="mt-2 mb-0"><?php echo $reading_stats['total_hours']; ?> hrs</h2>
                        </div>
                        <i class="bi bi-hourglass-split fs-1"></i>
                    </div>
                    <p class="mt-2 mb-0">
                        <i class="bi bi-arrow-up"></i> <?php echo end($hours_spent); ?> hrs this month
                    </p>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="reading.php">View Details</a>
                    <div class="small text-white"><i class="bi bi-chevron-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Pages Read</h5>
                            <h2 class="mt-2 mb-0"><?php echo number_format($reading_stats['total_pages']); ?></h2>
                        </div>
                        <i class="bi bi-file-text fs-1"></i>
                    </div>
                    <p class="mt-2 mb-0">
                        <i class="bi bi-arrow-up"></i> <?php echo end($reading_history); ?> this month
                    </p>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="reading.php">View Statistics</a>
                    <div class="small text-white"><i class="bi bi-chevron-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Completion Rate</h5>
                            <h2 class="mt-2 mb-0"><?php echo end($completion_rate); ?>%</h2>
                        </div>
                        <i class="bi bi-check-circle fs-1"></i>
                    </div>
                    <p class="mt-2 mb-0">
                        <i class="bi bi-arrow-up"></i> 5% from last month
                    </p>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="reading.php">View Trends</a>
                    <div class="small text-white"><i class="bi bi-chevron-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Reading Activity Chart -->
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-graph-up me-1"></i>
                    Reading Activity
                </div>
                <div class="card-body">
                    <canvas id="readingActivityChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>

        <!-- Favorite Genres Chart -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-pie-chart me-1"></i>
                    My Reading Preferences
                </div>
                <div class="card-body">
                    <canvas id="genreDistributionChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Reading Section -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-bookmark me-1"></i>
                    Currently Reading
                </div>
                <div class="card-body">
                    <?php foreach ($current_books as $book): ?>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="mb-0"><a href="book.php?id=<?php echo $book['id']; ?>"><?php echo htmlspecialchars($book['title']); ?></a></h6>
                                <span class="badge bg-primary"><?php echo $book['progress']; ?>%</span>
                            </div>
                            <p class="text-muted small mb-2">by <?php echo htmlspecialchars($book['author']); ?> · Last read <?php echo $book['last_read']; ?></p>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: <?php echo $book['progress']; ?>%"
                                    aria-valuenow="<?php echo $book['progress']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="text-center mt-3">
                        <a href="reading.php" class="btn btn-primary btn-sm">Continue Reading</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-award me-1"></i>
                    My Reading Achievements
                </div>
                <div class="card-body">
                    <?php foreach ($reading_achievements as $achievement): ?>
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-1">
                                <div class="achievement-icon me-2">
                                    <i class="bi bi-<?php echo $achievement['icon']; ?> fs-4 <?php echo $achievement['progress'] == 100 ? 'text-success' : 'text-secondary'; ?>"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0"><?php echo htmlspecialchars($achievement['title']); ?></h6>
                                    <p class="text-muted small mb-1"><?php echo htmlspecialchars($achievement['description']); ?></p>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar <?php echo $achievement['progress'] == 100 ? 'bg-success' : ''; ?>" role="progressbar"
                                            style="width: <?php echo $achievement['progress']; ?>%"
                                            aria-valuenow="<?php echo $achievement['progress']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <?php if ($achievement['progress'] == 100): ?>
                                    <div class="ms-2">
                                        <span class="badge bg-success rounded-pill" data-bs-toggle="tooltip" data-bs-title="Achievement Unlocked">
                                            <i class="bi bi-check-lg"></i>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="text-center mt-3">
                        <a href="achievements.php" class="btn btn-outline-primary btn-sm">View All Achievements</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Book Recommendations -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-stars me-1"></i>
                    Recommended For You
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($recommended_books as $book): ?>
                            <div class="col-md-6 col-lg-3 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title"><a href="book.php?id=<?php echo $book['id']; ?>"><?php echo htmlspecialchars($book['title']); ?></a></h5>
                                        <p class="card-text text-muted mb-2">by <?php echo htmlspecialchars($book['author']); ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-light text-dark"><?php echo htmlspecialchars($book['genre']); ?></span>
                                            <div class="d-flex align-items-center">
                                                <?php echo $book['rating']; ?>
                                                <div class="ms-1">
                                                    <?php
                                                    $full_stars = floor($book['rating']);
                                                    $half_star = $book['rating'] - $full_stars >= 0.5;

                                                    for ($i = 0; $i < $full_stars; $i++) {
                                                        echo '<i class="bi bi-star-fill text-warning"></i>';
                                                    }

                                                    if ($half_star) {
                                                        echo '<i class="bi bi-star-half text-warning"></i>';
                                                    }

                                                    $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);
                                                    for ($i = 0; $i < $empty_stars; $i++) {
                                                        echo '<i class="bi bi-star text-warning"></i>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex justify-content-between bg-transparent">
                                        <a href="book.php?id=<?php echo $book['id']; ?>" class="btn btn-outline-primary btn-sm">Details</a>
                                        <button class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-heart"></i> Add to Wishlist
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-center mt-3">
                        <a href="browse.php" class="btn btn-primary">Browse More Books</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reading Statistics -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-bar-chart me-1"></i>
                    My Reading Statistics
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="stat-card p-3 border rounded">
                                <h6 class="text-muted">Favorite Genre</h6>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-collection me-2 text-primary fs-3"></i>
                                    <h4 class="mb-0"><?php echo htmlspecialchars($reading_stats['favorite_genre']); ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="stat-card p-3 border rounded">
                                <h6 class="text-muted">Average Rating</h6>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-star me-2 text-warning fs-3"></i>
                                    <h4 class="mb-0"><?php echo $reading_stats['avg_rating']; ?>/5.0</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="stat-card p-3 border rounded">
                                <h6 class="text-muted">Longest Book Read</h6>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-book me-2 text-success fs-3"></i>
                                    <h4 class="mb-0"><?php echo htmlspecialchars($reading_stats['longest_book']); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>

<script>
    // Chart Data
    const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const readingData = <?php echo json_encode($reading_history); ?>;
    const booksReadData = <?php echo json_encode($books_read); ?>;
    const hoursSpentData = <?php echo json_encode($hours_spent); ?>;

    // Genre data (dummy data for pie chart)
    const genreLabels = ['Mystery', 'Science Fiction', 'Fantasy', 'Biography', 'History'];
    const genreData = [35, 20, 25, 10, 10];

    // Chart configurations and rendering
    document.addEventListener('DOMContentLoaded', function() {
        // Reading Activity Chart
        const readingCtx = document.getElementById('readingActivityChart').getContext('2d');
        const readingGradient = createGradient(readingCtx, ['rgba(54, 162, 235, 0.2)', 'rgba(54, 162, 235, 0.0)']);

        new Chart(readingCtx, {
            type: 'line',
            data: {
                labels: monthLabels,
                datasets: [{
                        label: 'Pages Read',
                        data: readingData,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: readingGradient,
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Books Completed',
                        data: booksReadData,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderWidth: 2,
                        tension: 0.4,
                        yAxisID: 'y1'
                    },
                    {
                        label: 'Hours Spent',
                        data: hoursSpentData,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        borderWidth: 2,
                        tension: 0.4,
                        yAxisID: 'y2'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Pages Read'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                        title: {
                            display: true,
                            text: 'Books Completed'
                        }
                    },
                    y2: {
                        type: 'linear',
                        display: false,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        }
                    }
                }
            }
        });

        // Genre Distribution Doughnut Chart
        const genreCtx = document.getElementById('genreDistributionChart').getContext('2d');
        new Chart(genreCtx, {
            type: 'doughnut',
            data: {
                labels: genreLabels,
                datasets: [{
                    data: genreData,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'Favorite Genres (%)'
                    }
                }
            }
        });
    });

    // Function to create smooth gradient
    function createGradient(ctx, colors) {
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        colors.forEach((color, index) => {
            gradient.addColorStop(index / (colors.length - 1), color);
        });
        return gradient;
    }
</script>

<?php
// Include footer
include_once $footerPath;
?>