<?php
// config/cloudinary.php

use Cloudinary\Cloudinary;

// Check required environment variables
$requiredEnvVars = ['CLOUDINARY_CLOUD_NAME', 'CLOUDINARY_API_KEY', 'CLOUDINARY_API_SECRET'];
foreach ($requiredEnvVars as $var) {
    if (!isset($_ENV[$var]) || empty($_ENV[$var])) {
        throw new Exception("Environment variable $var is not set or empty");
    }
}

// Configure cloudinary with environment variables
$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
        'api_key' => $_ENV['CLOUDINARY_API_KEY'],
        'api_secret' => $_ENV['CLOUDINARY_API_SECRET'],
    ],
    'url' => [
        'secure' => true // Use HTTPS by default
    ]
]);

return $cloudinary;