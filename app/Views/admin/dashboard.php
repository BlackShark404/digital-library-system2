<?php

use Core\Session;

include $headerPath;

// In a real application, you would fetch this data from your database
// For demo purposes, I'm using dummy data
$monthly_sales = [3500, 4200, 3800, 5100, 4800, 6200, 7500, 7800, 6500, 8200, 9000, 9500];
$monthly_users = [120, 150, 140, 180, 210, 250, 280, 310, 290, 340, 380, 420];
$monthly_reading_sessions = [450, 520, 480, 550, 600, 680, 750, 820, 780, 850, 920, 980];
$monthly_new_books = [15, 18, 12, 24, 20, 28, 32, 30, 26, 35, 40, 38];

$top_books = [
    ['id' => 1, 'title' => 'The Great Novel', 'author' => 'John Author', 'sales' => 356, 'rating' => 4.8],
    ['id' => 2, 'title' => 'Mystery of the Century', 'author' => 'Jane Writer', 'sales' => 289, 'rating' => 4.7],
    ['id' => 3, 'title' => 'Future Technology', 'author' => 'Alan Scientist', 'sales' => 245, 'rating' => 4.6],
    ['id' => 4, 'title' => 'History Uncovered', 'author' => 'Helen Historian', 'sales' => 198, 'rating' => 4.5],
    ['id' => 5, 'title' => 'Poetry Collection', 'author' => 'Robert Poet', 'sales' => 176, 'rating' => 4.4]
];

$recent_activities = [
    ['user' => 'john_doe', 'action' => 'Purchased "The Great Novel"', 'time' => '2 hours ago'],
    ['user' => 'jane_smith', 'action' => 'Completed reading "Mystery of the Century"', 'time' => '3 hours ago'],
    ['user' => 'bob_jackson', 'action' => 'Added "Future Technology" to wishlist', 'time' => '5 hours ago'],
    ['user' => 'alice_walker', 'action' => 'Wrote review for "History Uncovered"', 'time' => '6 hours ago'],
    ['user' => 'samuel_green', 'action' => 'Started reading "Poetry Collection"', 'time' => '8 hours ago']
];

$user_demographics = [
    ['age' => '18-24', 'percentage' => 15],
    ['age' => '25-34', 'percentage' => 32],
    ['age' => '35-44', 'percentage' => 28],
    ['age' => '45-54', 'percentage' => 18],
    ['age' => '55+', 'percentage' => 7]
];

$genre_distribution = [
    ['genre' => 'Fiction', 'count' => 450],
    ['genre' => 'Non-Fiction', 'count' => 320],
    ['genre' => 'Mystery', 'count' => 280],
    ['genre' => 'Science', 'count' => 210],
    ['genre' => 'History', 'count' => 180],
    ['genre' => 'Biography', 'count' => 150],
    ['genre' => 'Poetry', 'count' => 90]
];



?>

<div class="container-fluid px-4">
    <h1 class="mt-4 mb-4">Welcome <?= Session::get("first_name") . " " . Session::get("last_name") ?> </h1>
    <!-- Quick Stats -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Total Sales</h5>
                            <h2 class="mt-2 mb-0">$<?php echo number_format(array_sum($monthly_sales)); ?></h2>
                        </div>
                        <i class="bi bi-cart-check fs-1"></i>
                    </div>
                    <p class="mt-2 mb-0">
                        <i class="bi bi-arrow-up"></i> 12.5% from last month
                    </p>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="purchases.php">View Details</a>
                    <div class="small text-white"><i class="bi bi-chevron-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Active Users</h5>
                            <h2 class="mt-2 mb-0"><?php echo number_format(end($monthly_users)); ?></h2>
                        </div>
                        <i class="bi bi-people fs-1"></i>
                    </div>
                    <p class="mt-2 mb-0">
                        <i class="bi bi-arrow-up"></i> 8.3% from last month
                    </p>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="user_management.php">View Details</a>
                    <div class="small text-white"><i class="bi bi-chevron-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Reading Sessions</h5>
                            <h2 class="mt-2 mb-0"><?php echo number_format(end($monthly_reading_sessions)); ?></h2>
                        </div>
                        <i class="bi bi-book-half fs-1"></i>
                    </div>
                    <p class="mt-2 mb-0">
                        <i class="bi bi-arrow-up"></i> 6.5% from last month
                    </p>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="reading_sessions.php">View Details</a>
                    <div class="small text-white"><i class="bi bi-chevron-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Total Books</h5>
                            <h2 class="mt-2 mb-0"><?php echo number_format(array_sum($monthly_new_books)); ?></h2>
                        </div>
                        <i class="bi bi-journals fs-1"></i>
                    </div>
                    <p class="mt-2 mb-0">
                        <i class="bi bi-arrow-up"></i> 10.2% from last month
                    </p>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="book_management.php">View Details</a>
                    <div class="small text-white"><i class="bi bi-chevron-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Sales & Users Chart -->
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-graph-up me-1"></i>
                    Monthly Performance
                </div>
                <div class="card-body">
                    <canvas id="salesUsersChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>

        <!-- User Demographics Chart -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-pie-chart me-1"></i>
                    User Demographics
                </div>
                <div class="card-body">
                    <canvas id="userDemographicsChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row of Charts -->
    <div class="row">
        <!-- Book Genre Distribution -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-bar-chart me-1"></i>
                    Book Genre Distribution
                </div>
                <div class="card-body">
                    <canvas id="genreDistributionChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>

        <!-- Reading Sessions & New Books -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-graph-up me-1"></i>
                    Reading Activity & New Content
                </div>
                <div class="card-body">
                    <canvas id="readingActivityChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row">
        <!-- Top Books Table -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-table me-1"></i>
                    Top Performing Books
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Sales</th>
                                    <th>Rating</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_books as $book): ?>
                                    <tr>
                                        <td><a href="books.php?id=<?php echo $book['id']; ?>"><?php echo htmlspecialchars($book['title']); ?></a></td>
                                        <td><?php echo htmlspecialchars($book['author']); ?></td>
                                        <td><?php echo $book['sales']; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php echo $book['rating']; ?>
                                                <div class="ms-2">
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
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="book_management.php" class="btn btn-primary btn-sm">View All Books</a>
                </div>
            </div>
        </div>

        <!-- Recent Activities Table -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-activity me-1"></i>
                    Recent User Activities
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Activity</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_activities as $activity): ?>
                                    <tr>
                                        <td><a href="users.php?username=<?php echo urlencode($activity['user']); ?>"><?php echo htmlspecialchars($activity['user']); ?></a></td>
                                        <td><?php echo htmlspecialchars($activity['action']); ?></td>
                                        <td><?php echo htmlspecialchars($activity['time']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="activity_log.php" class="btn btn-primary btn-sm">View All Activities</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>

<script>
    // Function to create smooth gradient
    function createGradient(ctx, colors) {
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        colors.forEach((color, index) => {
            gradient.addColorStop(index / (colors.length - 1), color);
        });
        return gradient;
    }

    // Chart Data
    const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const salesData = <?php echo json_encode($monthly_sales); ?>;
    const usersData = <?php echo json_encode($monthly_users); ?>;
    const readingSessionsData = <?php echo json_encode($monthly_reading_sessions); ?>;
    const newBooksData = <?php echo json_encode($monthly_new_books); ?>;

    // Demographics data
    const demographicsLabels = <?php echo json_encode(array_column($user_demographics, 'age')); ?>;
    const demographicsData = <?php echo json_encode(array_column($user_demographics, 'percentage')); ?>;

    // Genre Distribution data
    const genreLabels = <?php echo json_encode(array_column($genre_distribution, 'genre')); ?>;
    const genreData = <?php echo json_encode(array_column($genre_distribution, 'count')); ?>;

    // Chart configurations and rendering
    document.addEventListener('DOMContentLoaded', function() {
        // Sales & Users Chart
        const salesUsersCtx = document.getElementById('salesUsersChart').getContext('2d');
        const salesUsersGradient = createGradient(salesUsersCtx, ['rgba(54, 162, 235, 0.2)', 'rgba(54, 162, 235, 0.0)']);

        new Chart(salesUsersCtx, {
            type: 'line',
            data: {
                labels: monthLabels,
                datasets: [{
                        label: 'Sales ($)',
                        data: salesData,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: salesUsersGradient,
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Active Users',
                        data: usersData,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderWidth: 2,
                        tension: 0.4,
                        yAxisID: 'y1'
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
                            text: 'Sales ($)'
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
                            text: 'Users'
                        }
                    }
                }
            }
        });

        // User Demographics Doughnut Chart
        const demographicsCtx = document.getElementById('userDemographicsChart').getContext('2d');
        new Chart(demographicsCtx, {
            type: 'doughnut',
            data: {
                labels: demographicsLabels,
                datasets: [{
                    data: demographicsData,
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
                        text: 'User Age Distribution (%)'
                    }
                }
            }
        });

        // Genre Distribution Chart
        const genreCtx = document.getElementById('genreDistributionChart').getContext('2d');
        new Chart(genreCtx, {
            type: 'bar',
            data: {
                labels: genreLabels,
                datasets: [{
                    label: 'Number of Books',
                    data: genreData,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Books by Genre'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Books'
                        }
                    }
                }
            }
        });

        // Reading Activity Chart
        const readingCtx = document.getElementById('readingActivityChart').getContext('2d');
        new Chart(readingCtx, {
            type: 'line',
            data: {
                labels: monthLabels,
                datasets: [{
                        label: 'Reading Sessions',
                        data: readingSessionsData,
                        borderColor: 'rgba(255, 159, 64, 1)',
                        backgroundColor: 'rgba(255, 159, 64, 0.2)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y'
                    },
                    {
                        label: 'New Books Added',
                        data: newBooksData,
                        borderColor: 'rgba(153, 102, 255, 1)',
                        backgroundColor: 'rgba(153, 102, 255, 0.5)',
                        borderWidth: 2,
                        tension: 0.4,
                        yAxisID: 'y1'
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
                            text: 'Reading Sessions'
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
                            text: 'New Books'
                        }
                    }
                }
            }
        });
    });
</script>

<?php
// Include footer
include_once $footerPath;
?>