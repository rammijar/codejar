<?php
if (!function_exists('formatDate')) {
    function formatDate($dateString) {
        $date = new DateTime($dateString);
        return $date->format('M d, Y');
    }
}

if (!function_exists('getRecentUploads')) {
    function getRecentUploads($limit = 5) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT u.*, us.username 
                              FROM uploads u 
                              JOIN users us ON u.user_id = us.id 
                              WHERE u.is_approved = 1 
                              ORDER BY u.created_at DESC 
                              LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}

if (!function_exists('countUserUploads')) {
    function countUserUploads($userId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM uploads WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch()['count'];
    }
}

if (!function_exists('countUserDonations')) {
    function countUserDonations($userId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM donations WHERE recipient_id = ? AND status = 'completed'");
        $stmt->execute([$userId]);
        return $stmt->fetch()['count'];
    }
}

if (!function_exists('calculateTotalDonations')) {
    function calculateTotalDonations($userId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT SUM(amount) as total FROM donations WHERE recipient_id = ? AND status = 'completed'");
        $stmt->execute([$userId]);
        return $stmt->fetch()['total'] ?? 0;
    }
}

if (!function_exists('getUserUploads')) {
    function getUserUploads($userId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM uploads WHERE user_id = ? AND is_approved = 1 ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}

if (!function_exists('sanitize')) {
    function sanitize($data) {
        return htmlspecialchars(trim($data));
    }
}

if (!function_exists('getUploadById')) {
    function getUploadById($id) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM uploads WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('getUploadFileContents')) {
    function getUploadFileContents($filePath) {
        $fullPath = __DIR__ . '/../assets/uploads/code_files/' . basename($filePath);
        if (file_exists($fullPath)) {
            return file_get_contents($fullPath);
        }
        return 'File not found.';
    }
}
?>