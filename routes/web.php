<?php

// Define public routes
$publicRoutes = [
    '/',
    '/login',
    '/register',
    '/contact-us',
    '/user-data',
    '/paginate-test',
    '/datable-test'
];

// Define the access control map for routes
$accessMap = [
    // Admin-only routes
    '/admin/dashboard' => ['admin'],
    '/admin/users' => ['admin'],
    '/admin/books' => ['admin'],
    '/admin/reading' => ['admin'],
    '/admin/purchases' => ['admin'],
    '/admin/logs' => ['admin'],
    '/admin/user-management' => ['admin'],
    '/admin/book-management' => ['admin'],
    '/admin/reading-sessions' => ['admin'],
    '/admin/activity-logs' => ['admin'],
    '/admin/admin-profile' => ['admin'],
    '/admin/users/create' => ['admin'],
    '/admin/users/data' => ['admin'],
    '/admin/users/update' => ['admin'],
    '/admin/users/delete' => ['admin'],
    
    // Activity Log API routes - Admin only
    '/api/activity-logs' => ['admin'],
    '/api/activity-logs/stats' => ['admin'],
    '/api/activity-logs/view' => ['admin'],
    
    // Reading Session API routes - Admin only
    '/api/reading-sessions' => ['admin'],
    '/api/reading-sessions/stats' => ['admin'],
    '/api/reading-sessions/view' => ['admin'],
    
    // User Management API routes - Admin only
    '/api/users' => ['admin'],
    '/api/users/export' => ['admin'],
    
    // Book Management API routes - Admin only
    '/api/books' => ['admin'],
    
    // User-only routes
    '/user/dashboard' => ['user'],
    '/user/browse-books' => ['user'],
    '/user/reading-sessions' => ['user'],
    '/user/wishlist' => ['user'],
    '/user/purchases' => ['user'],
    '/user/user-profile' => ['user'],
    '/user/user-profile/delete-account' => ['user'],
    '/user/user-profile/change-password' => ['user'],
    '/user/user-profile/update-profile-info' => ['user'],
    '/user/wishlist/add' => ['user'],
    '/user/wishlist/remove' => ['user'],
    '/user/wishlist/toggle' => ['user'],
    '/user/read' => ['user'],
    '/user/download-book' => ['user'],
    '/reading-session/read-book' => ['user'],
    '/reading-session/start-session' => ['user'],
    '/reading-session/update-progress' => ['user'],
    '/reading-session/check-availability' => ['user'],
    '/api/books/purchase' => ['user'],

    // Shared (admin and user)
    '/logout' => ['admin', 'user'],

];

$router->setBasePath(''); // Set this if your app is in a subdirectory

// Define routes
// Home routes
$router->map('GET', '/', 'App\Controllers\HomeController#index', 'home');
$router->map('GET', '/about', 'App\Controllers\HomeController#about', 'about');
$router->map('GET', '/contact-us', 'App\Controllers\HomeController#contactUs', 'contact');
$router->map('POST', '/contact-us', 'App\Controllers\HomeController#contactUs', 'contact_post');
$router->map('GET', '/privacy-policy', 'App\Controllers\HomeController#privacy', 'privacy-policy');
$router->map('GET', '/terms-of-service', 'App\Controllers\HomeController#terms', 'terms-of-service');


// Auth routes
$router->map('GET', '/login', 'App\Controllers\AuthController#loginForm', 'login');
$router->map('POST', '/login', 'App\Controllers\AuthController#login', 'login_post');
$router->map('GET', '/register', 'App\Controllers\AuthController#registerForm', 'register');
$router->map('POST', '/register', 'App\Controllers\AuthController#register', 'register_post');
    

// Admin routes
$router->map('GET', '/admin/dashboard', 'App\Controllers\AdminController#renderAdminDashboard', 'admin_dashboard');
$router->map('GET', '/admin/book-management', 'App\Controllers\BookController#index', 'book-management');
$router->map('GET', '/admin/reading-sessions', 'App\Controllers\AdminController#renderReadingSessions', 'reading-session');
$router->map('GET', '/admin/purchases', 'App\Controllers\AdminController#renderPurchases', 'purchases');
$router->map('GET', '/admin/activity-logs', 'App\Controllers\AdminController#renderActivityLogs', 'activity-log');
$router->map('GET', '/admin/admin-profile', 'App\Controllers\AdminController#renderAdminProfile', 'admin-profile');

// Activity Log Routes
$router->map('GET', '/api/activity-logs', 'App\Controllers\ActivityLogController#getActivityLogs', 'api_get_activity_logs');
$router->map('GET', '/api/activity-logs/[i:id]', 'App\Controllers\ActivityLogController#viewLog', 'api_view_activity_log');
$router->map('GET', '/api/activity-logs/stats', 'App\Controllers\ActivityLogController#getActivityStats', 'api_get_activity_stats');

// Reading Session API Routes
$router->map('GET', '/api/reading-sessions', 'App\Controllers\ReadingSessionController#getReadingSessions', 'api_get_reading_sessions');
$router->map('GET', '/api/reading-sessions/[i:id]', 'App\Controllers\ReadingSessionController#getReadingSession', 'api_get_reading_session');
$router->map('PUT', '/api/reading-sessions/[i:id]', 'App\Controllers\ReadingSessionController#updateReadingSession', 'api_update_reading_session');
$router->map('DELETE', '/api/reading-sessions/[i:id]', 'App\Controllers\ReadingSessionController#deleteReadingSession', 'api_delete_reading_session');

// User Management Routes
$router->map('GET', '/admin/user-management', 'App\Controllers\UserManagementController#index', 'user-management');

// User Management API Routes
$router->map('GET', '/api/users', 'App\Controllers\UserManagementController#getUsers', 'api_get_users');
$router->map('GET', '/api/users/[i:id]', 'App\Controllers\UserManagementController#getUser', 'api_get_user');
$router->map('POST', '/api/users', 'App\Controllers\UserManagementController#createUser', 'api_create_user');
$router->map('PUT', '/api/users/[i:id]', 'App\Controllers\UserManagementController#updateUser', 'api_update_user');
$router->map('DELETE', '/api/users/[i:id]', 'App\Controllers\UserManagementController#deleteUser', 'api_delete_user');

// Book Management API Routes
$router->map('GET', '/api/books', 'App\Controllers\BookController#getBooks', 'api_get_books');
$router->map('GET', '/api/books/[i:id]', 'App\Controllers\BookController#getBook', 'api_get_book');
$router->map('POST', '/api/books', 'App\Controllers\BookController#createBook', 'api_create_book');
$router->map('PUT', '/api/books/[i:id]', 'App\Controllers\BookController#updateBook', 'api_update_book');
$router->map('DELETE', '/api/books/[i:id]', 'App\Controllers\BookController#deleteBook', 'api_delete_book');

// User routes
$router->map('GET', '/user/dashboard', 'App\Controllers\UserController#renderUserDashboard', 'user_dashboard');
$router->map('GET', '/user/browse-books', 'App\Controllers\UserController#renderBrowseBooks', 'user_browse_books');
$router->map('GET', '/user/reading-sessions', 'App\Controllers\ReadingSessionController#index', 'user_reading_sessions');
$router->map('GET', '/user/wishlist', 'App\Controllers\UserController#renderWishlist', 'user_wishlist');
$router->map('GET', '/user/purchases', 'App\Controllers\UserController#renderPurchases', 'user_purchases');
$router->map('GET', '/user/user-profile', 'App\Controllers\UserController#renderUserProfile', 'user_profile');
$router->map('GET', '/user/download-book/[i:bookId]', 'App\Controllers\UserController#downloadBook', 'download_book');

// Reading Session Routes
$router->map('GET', '/reading-session/read-book/[i:sessionId]', 'App\Controllers\ReadingSessionController#readBook', 'read_book');
$router->map('POST', '/reading-session/start-session/[i:bookId]', 'App\Controllers\ReadingSessionController#startSession', 'start_reading_session');
$router->map('POST', '/reading-session/update-progress', 'App\Controllers\ReadingSessionController#updateProgress', 'update_reading_progress');
$router->map('GET', '/user/read', 'App\Controllers\ReadingSessionController#startNewSession', 'start_new_session_from_browser');
$router->map('GET', '/reading-session/check-availability/[i:bookId]', 'App\Controllers\ReadingSessionController#checkAvailability', 'check_book_availability');

// Wishlist routes
$router->map('POST', '/user/wishlist/add', 'App\Controllers\UserController#addToWishlist', 'add_to_wishlist');
$router->map('POST', '/user/wishlist/remove', 'App\Controllers\UserController#removeFromWishlist', 'remove_from_wishlist');
$router->map('POST', '/user/wishlist/toggle', 'App\Controllers\UserController#toggleWishlist', 'toggle_wishlist');

// Purchase route
$router->map('POST', '/api/books/purchase/[i:id]', 'App\Controllers\BookController#purchaseBook', 'purchase_book');

// Profile routes 
$router->map('POST', '/user/user-profile/update-profile-pic', 'App\Controllers\ProfileController#updateProfilePicture', 'update-user-profile-pic');
$router->map('POST', '/user/user-profile/update-profile-info', 'App\Controllers\ProfileController#updateProfileInfo', 'update-user-profile-info');
$router->map('POST', '/user/user-profile/delete-account', 'App\Controllers\ProfileController#deleteAccount', 'delete_user-account');
$router->map('POST', '/user/user-profile/change-password', 'App\Controllers\ProfileController#changePassword', 'change-user-password');

$router->map('POST', '/admin/admin-profile/update-profile-pic', 'App\Controllers\ProfileController#updateProfilePicture', 'update-admin-profile-pic');
$router->map('POST', '/admin/admin-profile/update-profile-info', 'App\Controllers\ProfileController#updateProfileInfo', 'update-admin-profile-info');
$router->map('POST', '/admin/admin-profile/delete-account', 'App\Controllers\ProfileController#deleteAccount', 'delete_admin-account');
$router->map('POST', '/admin/admin-profile/change-password', 'App\Controllers\ProfileController#changePassword', 'change-admin-password');



// Logout routes
$router->map('GET', '/logout', 'App\Controllers\AuthController#logout', 'logout');

// Error routes
$router->map('GET', '/error/404', 'App\Controllers\ErrorController#error404', 'error_404');
$router->map('GET', '/error/403', 'App\Controllers\ErrorController#error403', 'error_403');
$router->map('GET', '/error/500', 'App\Controllers\ErrorController#error500', 'error_500');

$router->map('GET', '/test', 'App\Controllers\TestController#showTestView', 'test');
$router->map('POST', '/test', 'App\Controllers\TestController#getData', 'form_submission');
$router->map('GET', '/view', 'App\Controllers\TestController#viewData', 'view-data');

// Testing routes
$router->map('GET', '/user-data', 'App\Controllers\TestController#showUser', 'test-modal');
$router->map('GET', '/paginate', 'App\Controllers\TestController#paginateUsers', 'paginate-test');
$router->map('GET', '/datable', 'App\Controllers\TestController#renderDatableTest', 'datable-test');