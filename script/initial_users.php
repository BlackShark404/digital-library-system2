<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\UserModel;
use Config\Database;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

Database::getInstance()->getConnection();

$userModel = new UserModel;

// Check if any admin user exists
$adminExists = $userModel->getByRole('admin');

// If no admin exists, create one from environment variables
if (empty($adminExists)) {
    // Get admin details from environment variables
    $adminProfileUrl = $_ENV['ADMIN_PROFILE_URL'];
    $adminFirstName = $_ENV['ADMIN_FIRST_NAME'];
    $adminLastName = $_ENV['ADMIN_LAST_NAME'];
    $adminEmail = $_ENV['ADMIN_EMAIL'];
    $adminPassword = $_ENV['ADMIN_PASSWORD'];
    $adminRoleId = (int) $_ENV['ADMIN_ROLE_ID'];
    
    // Create admin user
    $userModel->createUser([
        'profile_url' => $adminProfileUrl,
        'first_name' => $adminFirstName,
        'last_name' => $adminLastName,
        'email' => $adminEmail,
        'password' => $adminPassword,
        'role_id' => $adminRoleId,
        'is_active' => true
    ]);

    // Define user details
    $userProfileUrl = 'https://ui-avatars.com/api/?name=Khen+Sorela&background=1a2236&color=fff&size=128';
    $firstName = 'Khen';
    $lastName = 'Sorela';
    $email = 'khen@gmail.com';
    $password = 'ad';  // plaintext password
    $roleId = 1;  // User role

    // Create user
    $userModel->createUser([
        'profile_url' => $userProfileUrl,
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'password' => $password,
        'role_id' => $roleId,
        'is_active' => true
    ]);
    
    // Log the creation of admin account (optional)
    error_log('Admin and User account has been created: ' . $adminEmail . " and " . $email);
}

