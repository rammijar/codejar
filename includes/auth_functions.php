<?php
require_once __DIR__ . '/db_connect.php';

if (!function_exists('getUserByUsername')) {
function getUserByUsername($username) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
}

if (!function_exists('getUserByEmail')) {
function getUserByEmail($email) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}
}

if (!function_exists('countUserUploads')) {
function countUserUploads($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM uploads WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchColumn();
}
}

if (!function_exists('countUserDonations')) {
function countUserDonations($userId) {
    global $pdo;
    // Fix: Use recipient_id instead of user_id
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM donations WHERE recipient_id = ? AND status = 'completed'");
    $stmt->execute([$userId]);
    return $stmt->fetchColumn();
}
}

if (!function_exists('calculateTotalDonations')) {
function calculateTotalDonations($userId) {
    global $pdo;
    // Fix: Use recipient_id instead of user_id
    $stmt = $pdo->prepare("SELECT SUM(amount) FROM donations WHERE recipient_id = ? AND status = 'completed'");
    $stmt->execute([$userId]);
    return $stmt->fetchColumn() ?: 0;
}
}

if (!function_exists('getUserUploads')) {
function getUserUploads($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM uploads WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}
}

if (!function_exists('getRecentUploads')) {
function getRecentUploads($limit = 6) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT u.*, us.username FROM uploads u JOIN users us ON u.user_id = us.id WHERE u.is_approved = 1 ORDER BY u.created_at DESC LIMIT ?");
    $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}
}

if (!function_exists('formatDate')) {
function formatDate($dateStr) {
    return date('M d, Y', strtotime($dateStr));
}
}

if (!function_exists('isLoggedIn')) {
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}
}

if (!function_exists('isAdmin')) {
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
}

if (!function_exists('getUserById')) {
function getUserById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}
}

if (!function_exists('updateProfileImage')) {
function updateProfileImage($userId, $fileName) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
    return $stmt->execute([$fileName, $userId]);
}
}

if (!function_exists('updateProfile')) {
function updateProfile($userId, $name, $bio, $website, $github, $twitter) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE users SET name = ?, bio = ?, website = ?, github = ?, twitter = ? WHERE id = ?");
    return $stmt->execute([$name, $bio, $website, $github, $twitter, $userId]);
}
}

if (!function_exists('loginUser')) {
function loginUser($usernameOrEmail, $password) {
    global $pdo;
    // Try username first
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
    $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        return true;
    }
    return false;
}
}

if (!function_exists('registerUser')) {
function registerUser($username, $email, $password) {
    global $pdo;
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
    return $stmt->execute([$username, $email, $hashedPassword]);
}
}

