<?php
require_once 'includes/header.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user = getUserById($_SESSION['user_id']);
$errors = [];
$success = false;

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $bio = trim($_POST['bio']);
    $website = trim($_POST['website']);
    $github = trim($_POST['github']);
    $twitter = trim($_POST['twitter']);
    
    if (updateProfile($_SESSION['user_id'], $name, $bio, $website, $github, $twitter)) {
        $success = true;
        $user = getUserById($_SESSION['user_id']); // Refresh user data
    } else {
        $errors[] = 'Failed to update profile';
    }
}

// Handle profile image update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_image']) && isset($_FILES['profile_image'])) {
    $file = $_FILES['profile_image'];
    
    // Check if file is uploaded
    if ($file['error'] === UPLOAD_ERR_OK) {
        // Check file type and size
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            $errors[] = 'Only JPG, PNG, and GIF images are allowed';
        } elseif ($file['size'] > $maxSize) {
            $errors[] = 'Image size must be less than 2MB';
        } else {
            // Generate unique filename
            $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = 'profile_' . $_SESSION['user_id'] . '_' . time() . '.' . $fileExt;
            $filePath = PROFILE_IMAGE_PATH . $fileName;
            
            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                // Delete old profile image if it's not the default
                if ($user['profile_image'] !== 'default-avatar.jpg') {
                    @unlink(PROFILE_IMAGE_PATH . $user['profile_image']);
                }
                
                if (updateProfileImage($_SESSION['user_id'], $fileName)) {
                    $success = true;
                    $user = getUserById($_SESSION['user_id']); // Refresh user data
                } else {
                    $errors[] = 'Failed to update profile image in database';
                    // Delete the uploaded file if DB update failed
                    @unlink($filePath);
                }
            } else {
                $errors[] = 'Failed to upload image';
            }
        }
    } else {
        $errors[] = 'Please select a valid image file';
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    if (!password_verify($current_password, $user['password'])) {
        $errors[] = 'Current password is incorrect';
    } elseif (empty($new_password)) {
        $errors[] = 'New password is required';
    } elseif ($new_password !== $confirm_password) {
        $errors[] = 'New passwords do not match';
    } else {
        if (changePassword($_SESSION['user_id'], $new_password)) {
            $success = true;
        } else {
            $errors[] = 'Failed to change password';
        }
    }
}

// Handle export data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['export_data'])) {
    $userId = $_SESSION['user_id'];
    $userData = getUserById($userId);
    $userUploads = getUserUploads($userId);
    $export = [
        'user' => $userData,
        'uploads' => $userUploads
    ];
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="codejar_export_' . $userData['username'] . '.json"');
    echo json_encode($export, JSON_PRETTY_PRINT);
    exit;
}

// Handle delete account
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    $userId = $_SESSION['user_id'];
    // Delete user uploads
    $uploads = getUserUploads($userId);
    foreach ($uploads as $upload) {
        $codeFile = __DIR__ . '/assets/uploads/code_files/' . basename($upload['file_path']);
        if (file_exists($codeFile)) unlink($codeFile);
        if (!empty($upload['poster_path'])) {
            $posterFile = __DIR__ . '/' . $upload['poster_path'];
            if (file_exists($posterFile)) unlink($posterFile);
        }
    }
    // Delete user from DB
    global $pdo;
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$userId]);
    // Log out and redirect
    session_destroy();
    header('Location: index.php?msg=Account deleted');
    exit;
}

// Download all uploads as ZIP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download_uploads'])) {
    $userId = $_SESSION['user_id'];
    $uploads = getUserUploads($userId);
    $zip = new ZipArchive();
    $zipFile = sys_get_temp_dir() . '/codejar_uploads_' . $userId . '.zip';
    if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
        foreach ($uploads as $upload) {
            $filePath = __DIR__ . '/assets/uploads/code_files/' . basename($upload['file_path']);
            if (file_exists($filePath)) {
                $zip->addFile($filePath, basename($upload['file_path']));
            }
        }
        $zip->close();
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="my_codejar_uploads.zip"');
        readfile($zipFile);
        unlink($zipFile);
        exit;
    } else {
        echo "<script>alert('Failed to create ZIP file.');</script>";
    }
}
?>

<div class="settings-container">
    <div class="card">
        <div class="card-header">
            <h2>Account Settings</h2>
        </div>
        <div class="card-body">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    Your changes have been saved successfully!
                </div>
            <?php elseif (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3>Profile Image</h3>
                        </div>
                        <div class="card-body text-center">
                            <img src="<?= BASE_URL ?>/assets/uploads/profile_images/<?= $user['profile_image'] ?>" 
                                 alt="Profile Image" class="profile-avatar" style="width:90px;height:90px;object-fit:cover;border-radius:50%;margin-bottom:12px;">
                            <form method="POST" action="settings.php" enctype="multipart/form-data" class="mt-3">
                                <div class="form-group">
                                    <input type="file" id="profile_image" name="profile_image" class="d-none" accept="image/*">
                                    <label for="profile_image" class="btn btn-secondary btn-sm">Choose Image</label>
                                    <small class="d-block text-muted">Max 2MB (JPG, PNG, GIF)</small>
                                </div>
                                <button type="submit" name="update_image" class="btn btn-primary btn-sm">Update Image</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3>Profile Information</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="settings.php">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" id="username" class="form-control" 
                                           value="<?= htmlspecialchars($user['username']) ?>" disabled>
                                    <small class="text-muted">Username cannot be changed</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" class="form-control" 
                                           value="<?= htmlspecialchars($user['email']) ?>" disabled>
                                    <small class="text-muted">Email cannot be changed</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" id="name" name="name" class="form-control" 
                                           value="<?= htmlspecialchars($user['name'] ?? '') ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="bio">Bio</label>
                                    <textarea id="bio" name="bio" class="form-control" rows="3"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="website">Website</label>
                                    <input type="url" id="website" name="website" class="form-control" 
                                           value="<?= htmlspecialchars($user['website'] ?? '') ?>" placeholder="https://">
                                </div>
                                
                                <div class="form-group">
                                    <label for="github">GitHub Username</label>
                                    <input type="text" id="github" name="github" class="form-control" 
                                           value="<?= htmlspecialchars($user['github'] ?? '') ?>" placeholder="username">
                                </div>
                                
                                <div class="form-group">
                                    <label for="twitter">Twitter Username</label>
                                    <input type="text" id="twitter" name="twitter" class="form-control" 
                                           value="<?= htmlspecialchars($user['twitter'] ?? '') ?>" placeholder="username">
                                </div>
                                
                                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-header">
                            <h3>Change Password</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="settings.php">
                                <div class="form-group">
                                    <label for="current_password">Current Password</label>
                                    <input type="password" id="current_password" name="current_password" class="form-control" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="new_password">New Password</label>
                                    <input type="password" id="new_password" name="new_password" class="form-control" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="confirm_password">Confirm New Password</label>
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                                </div>
                                
                                <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                            </form>
                        </div>
                    </div>
                    <!-- New Features -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h3>Account Actions</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="settings.php" style="margin-bottom:18px;">
                                <button type="submit" name="export_data" class="btn btn-secondary">Export My Data</button>
                            </form>
                            <form method="POST" action="settings.php" onsubmit="return confirm('Are you sure you want to delete your account? This cannot be undone.');">
                                <button type="submit" name="delete_account" class="btn btn-danger">Delete My Account</button>
                            </form>
                        </div>
                    </div>
                    <!-- End New Features -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add more features to the settings page -->
<div class="card mt-4">
    <div class="card-header">
        <h3>More Features</h3>
    </div>
    <div class="card-body">
        <div style="margin-bottom:18px;">
            <strong>Login Activity:</strong>
            <ul style="font-size:15px;color:#555;list-style:disc inside;">
                <?php
                // Only show login activity if table exists
                try {
                    global $pdo;
                    $stmt = $pdo->prepare("SHOW TABLES LIKE 'login_log'");
                    $stmt->execute();
                    if ($stmt->fetch()) {
                        $stmt = $pdo->prepare("SELECT * FROM login_log WHERE user_id = ? ORDER BY login_time DESC LIMIT 5");
                        $stmt->execute([$_SESSION['user_id']]);
                        $logins = $stmt->fetchAll();
                        if ($logins) {
                            foreach ($logins as $log) {
                                echo '<li>' . htmlspecialchars($log['login_time']) . ' - ' . htmlspecialchars($log['ip_address']) . '</li>';
                            }
                        } else {
                            echo '<li>No login activity found.</li>';
                        }
                    } else {
                        echo '<li>Login activity not available.</li>';
                    }
                } catch (Exception $e) {
                    echo '<li>Login activity not available.</li>';
                }
                ?>
            </ul>
        </div>
        <div style="margin-bottom:18px;">
            <strong>Download My Uploads:</strong>
            <form method="POST" action="settings.php">
                <button type="submit" name="download_uploads" class="btn btn-secondary btn-sm">Download All My Uploads (ZIP)</button>
            </form>
        </div>
        <div>
            <strong>Session Info:</strong>
            <div style="font-size:15px;color:#555;">
                Session ID: <?= session_id() ?><br>
                IP: <?= $_SERVER['REMOTE_ADDR'] ?>
            </div>
        </div>
    </div>
</div>

<!-- QR Code for your profile (always show for logged in user) -->
<?php
$profileUrl = BASE_URL . '/profile.php?username=' . urlencode($user['username']);
?>
<div class="card mt-4" style="max-width:320px;">
    <div class="card-header">
        <h3>My Profile QR Code</h3>
    </div>
    <div class="card-body" style="text-align:center;">
        <img src="https://chart.googleapis.com/chart?chs=140x140&cht=qr&chl=<?= urlencode($profileUrl) ?>" alt="QR Code" style="border-radius:8px;">
        <div style="margin-top:10px;">
            <a href="https://chart.googleapis.com/chart?chs=400x400&cht=qr&chl=<?= urlencode($profileUrl) ?>" download="profile-qr-<?= htmlspecialchars($user['username']) ?>.png" class="btn btn-sm btn-accent">Download QR</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>