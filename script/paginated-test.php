<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\BaseModel;
use App\Models\UserModel;
use Config\Database;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

Database::getInstance()->getConnection();

$userModel = new UserModel;

$test = $this->select('users.*, roles.name AS role_name')
            ->join('roles', 'users.role_id', 'roles.id');

$name = $test['first_name'];

if(empty($name)) {
    error_log("Users is empty");
}
