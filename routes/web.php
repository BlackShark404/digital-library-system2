<?php

// Define public routes
$publicRoutes = [
    '/',
    '/login',
    '/register',
    '/contact-us',
    'user-data'
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

    // Logout route (accessible by any logged-in user)
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
$router->map('GET', '/logout', 'App\Controllers\AuthController#logout', 'logout');

// Admin routes
$router->map('GET', '/admin/dashboard', 'App\Controllers\AdminController#renderAdminDashboard', 'admin_dashboard');
$router->map('GET', '/admin/user-management', 'App\Controllers\AdminController#renderUserManagement', 'user-management');
$router->map('GET', '/admin/book-management', 'App\Controllers\AdminController#renderBookManagement', 'book-management');
$router->map('GET', '/admin/reading-sessions', 'App\Controllers\AdminController#renderReadingSessions', 'reading-session');
$router->map('GET', '/admin/purchases', 'App\Controllers\AdminController#renderPurchases', 'purchases');
$router->map('GET', '/admin/activity-logs', 'App\Controllers\AdminController#renderActivityLogs', 'activity-log');

$router->map('POST', '/admin/user-management', 'App\Controllers\UserManagementController#registerUsers', 'register-users');


// User routes
$router->map('GET', '/user/dashboard', 'App\Controllers\UserController#renderUserDashboard', 'user_dashboard');



// Error routes
$router->map('GET', '/error/404', 'App\Controllers\ErrorController#error404', 'error_404');
$router->map('GET', '/error/403', 'App\Controllers\ErrorController#error403', 'error_403');
$router->map('GET', '/error/500', 'App\Controllers\ErrorController#error500', 'error_500');

$router->map('GET', '/test', 'App\Controllers\TestController#showTestView', 'test');
$router->map('POST', '/test', 'App\Controllers\TestController#getData', 'form_submission');
$router->map('GET', '/view', 'App\Controllers\TestController#viewData', 'view-data');


// Testing routes
$router->map('GET', '/user-data', 'App\Controllers\TestController#showUser', 'test-modal');
