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


    // Hash the password
    $hashedPassword = $userModel->hashPassword($adminPassword);
    
    // Create admin user
    $userModel->insert([
        'ua_profile_url' => $adminProfileUrl,
        'ua_first_name' => $adminFirstName,
        'ua_last_name' => $adminLastName,
        'ua_email' => $adminEmail,
        'ua_hashed_password' => $hashedPassword,
        'ua_role_id' => $adminRoleId
    ]);
    
    // Log the creation of admin account (optional)
    error_log('Admin account created: ' . $adminEmail);
}
