<?php
session_start();
include 'includes/db_connect.php';
include 'includes/auth_functions.php';
include 'includes/helper_functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$uploadId = $_POST['upload_id'] ?? null;

if (!$uploadId || !is_numeric($uploadId)) {
    header('Location: profile.php?error=Invalid upload');
    exit;
}

$upload = getUploadById($uploadId);
if (!$upload || $upload['user_id'] != $userId) {
    header('Location: profile.php?error=Unauthorized');
    exit;
}

// Delete code file
$codeFile = __DIR__ . '/assets/uploads/code_files/' . basename($upload['file_path']);
if (file_exists($codeFile)) {
    unlink($codeFile);
}
// Delete poster if exists
if (!empty($upload['poster_path'])) {
    $posterFile = __DIR__ . '/' . $upload['poster_path'];
    if (file_exists($posterFile)) {
        unlink($posterFile);
    }
}
// Delete from DB
$pdo->prepare('DELETE FROM uploads WHERE id = ?')->execute([$uploadId]);
header('Location: profile.php?username=' . urlencode($_SESSION['username']) . '&msg=Upload deleted');
exit;
