<?php
session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'codejar');

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Define base URL
define('BASE_URL', 'http://localhost/codejar');

// File upload paths
define('UPLOAD_PATH', __DIR__ . '/../assets/uploads/');
define('PROFILE_IMAGE_PATH', UPLOAD_PATH . 'profile_images/');
define('CODE_UPLOAD_PATH', UPLOAD_PATH . 'code_files/');

// Create directories if they don't exist
if (!file_exists(UPLOAD_PATH)) mkdir(UPLOAD_PATH, 0777, true);
if (!file_exists(PROFILE_IMAGE_PATH)) mkdir(PROFILE_IMAGE_PATH, 0777, true);
if (!file_exists(CODE_UPLOAD_PATH)) mkdir(CODE_UPLOAD_PATH, 0777, true);
?>