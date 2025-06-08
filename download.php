<?php
require_once 'includes/db_connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(404);
    echo 'Invalid download request.';
    exit;
}

// Fetch upload info
$stmt = $pdo->prepare("SELECT * FROM uploads WHERE id = ? AND is_approved = 1");
$stmt->execute([$id]);
$upload = $stmt->fetch();

if (!$upload) {
    http_response_code(404);
    echo 'File not found or not approved.';
    exit;
}

$filePath = CODE_UPLOAD_PATH . $upload['file_path'];
if (!file_exists($filePath)) {
    http_response_code(404);
    echo 'File does not exist on server.';
    exit;
}

// Increment download count
$pdo->prepare("UPDATE uploads SET download_count = download_count + 1 WHERE id = ?")->execute([$id]);

// Send file for download
header('Content-Description: File Transfer');
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . basename($upload['file_path']) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;
