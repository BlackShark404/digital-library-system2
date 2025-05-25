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

// If no admin exists, create admin and default users
if (empty($adminExists)) {
    // Create Admin
    $userModel->createUser([
        'profile_url' => $_ENV['ADMIN_PROFILE_URL'],
        'first_name' => $_ENV['ADMIN_FIRST_NAME'],
        'last_name' => $_ENV['ADMIN_LAST_NAME'],
        'email' => $_ENV['ADMIN_EMAIL'],
        'password' => $_ENV['ADMIN_PASSWORD'],
        'role_id' => (int) $_ENV['ADMIN_ROLE_ID'],
        'is_active' => true
    ]);

    // Loop through 10 default users
    for ($i = 1; $i <= 10; $i++) {
        $userModel->createUser([
            'profile_url' => $_ENV["USER{$i}_PROFILE_URL"],
            'first_name' => $_ENV["USER{$i}_FIRST_NAME"],
            'last_name' => $_ENV["USER{$i}_LAST_NAME"],
            'email' => $_ENV["USER{$i}_EMAIL"],
            'password' => $_ENV["USER{$i}_PASSWORD"],
            'role_id' => (int) $_ENV["USER{$i}_ROLE_ID"],
            'is_active' => true
        ]);
    }

    error_log('Admin and 10 user accounts have been created.');
}
