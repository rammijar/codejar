<?php
require_once '../includes/header.php';
if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}
global $pdo;

// Simple section routing
$section = $_GET['section'] ?? 'dashboard';
$msg = "";

// --- Handle actions as in previous dashboards (edit, delete, etc.) ---

// Fetch data for tabs
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
$uploads = $pdo->query("SELECT * FROM uploads ORDER BY created_at DESC LIMIT 100")->fetchAll();
$donations = $pdo->query("SELECT * FROM donations ORDER BY created_at DESC LIMIT 100")->fetchAll();
$widgets = $pdo->query("SELECT * FROM widgets ORDER BY created_at DESC LIMIT 100")->fetchAll();

?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
<style>
/* Sidebar admin panel layout */
.admin-panel-wrap {
    display: flex;
    min-height: 100vh;
    background: #f6f7ff;
}
.admin-sidebar {
    background: linear-gradient(160deg,#4e54c8 0%,#8f94fb 100%);
    color: #fff;
    min-width: 230px;
    max-width: 240px;
    padding: 0;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    box-shadow: 2px 0 12px #e6eaff;
}
.admin-sidebar .sidebar-logo {
    font-size: 2rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-align: center;
    padding: 30px 0;
    color: #fff;
    border-bottom: 1px solid rgba(255,255,255,0.15);
}
.admin-sidebar .sidebar-menu {
    flex:1;
    list-style: none;
    padding: 0;
    margin: 0;
    margin-top: 26px;
}
.admin-sidebar .sidebar-menu li {
    margin-bottom: 8px;
}
.admin-sidebar .sidebar-menu a, .admin-sidebar .sidebar-menu .active {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 35px;
    color: #fff;
    text-decoration: none;
    font-size: 1.09rem;
    font-weight: 500;
    border-left: 3px solid transparent;
    transition: background 0.17s, border-color 0.17s;
}
.admin-sidebar .sidebar-menu a:hover,
.admin-sidebar .sidebar-menu .active {
    background:rgba(255,255,255,0.11);
    border-left: 3px solid #ffeaa7;
    color: #ffeaa7;
}

/* Main admin content */
.admin-panel-main {
    flex: 1;
    padding: 42px 6vw 42px 6vw;
    min-height: 100vh;
    background: #f6f7ff;
    overflow-x: auto;
}

/* Responsive sidebar */
@media (max-width: 1100px) {
    .admin-panel-main { padding: 30px 2vw 30px 2vw;}
}
@media (max-width: 800px) {
    .admin-panel-wrap { flex-direction: column;}
    .admin-sidebar { flex-direction: row; min-width: 0; max-width: 99vw; width: 100vw;}
    .admin-sidebar .sidebar-logo { font-size: 1.3rem; padding: 14px; }
    .admin-sidebar .sidebar-menu { flex: 1; display: flex; margin-top:0;}
    .admin-sidebar .sidebar-menu li { margin-bottom:0; flex:1;}
    .admin-sidebar .sidebar-menu a { padding:12px 10px; justify-content:center; font-size:1rem; }
}
</style>

<div class="admin-panel-wrap">
    <nav class="admin-sidebar">
        <div class="sidebar-logo">
            <i class="fas fa-shield-alt"></i> Admin Panel
        </div>
        <ul class="sidebar-menu">
            <li><a href="?section=dashboard" class="<?= $section==='dashboard'?'active':'' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="?section=users" class="<?= $section==='users'?'active':'' ?>"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="?section=uploads" class="<?= $section==='uploads'?'active':'' ?>"><i class="fas fa-file-archive"></i> Uploads</a></li>
            <li><a href="?section=donations" class="<?= $section==='donations'?'active':'' ?>"><i class="fas fa-donate"></i> Donations</a></li>
            <li><a href="?section=widgets" class="<?= $section==='widgets'?'active':'' ?>"><i class="fas fa-cubes"></i> Widgets</a></li>
            <li><a href="?section=settings" class="<?= $section==='settings'?'active':'' ?>"><i class="fas fa-cogs"></i> Settings</a></li>
            <li><a href="<?= BASE_URL ?>/logout.php" class=""><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
    <div class="admin-panel-main">
        <?php if($section==='dashboard'): ?>
            <div class="admin-header" style="border-radius:12px;margin-bottom:32px;">
                <div class="admin-title">Dashboard Overview</div>
                <div style="margin-top:12px;color:#fff;">Quick summary of site stats and health</div>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:36px;">
                <div class="admin-card" style="background:#fff8e1;">
                    <div style="font-size:1.5rem;font-weight:700;color:#6c5ce7;">Users</div>
                    <div style="font-size:2.3rem;font-weight:bold;"><?= count($users) ?></div>
                </div>
                <div class="admin-card" style="background:#e0f7fa;">
                    <div style="font-size:1.5rem;font-weight:700;color:#00b894;">Uploads</div>
                    <div style="font-size:2.3rem;font-weight:bold;"><?= count($uploads) ?></div>
                </div>
                <div class="admin-card" style="background:#ffeaa7;">
                    <div style="font-size:1.5rem;font-weight:700;color:#fdcb6e;">Donations</div>
                    <div style="font-size:2.3rem;font-weight:bold;"><?= count($donations) ?></div>
                </div>
                <div class="admin-card" style="background:#e8eaf6;">
                    <div style="font-size:1.5rem;font-weight:700;color:#4e54c8;">Widgets</div>
                    <div style="font-size:2.3rem;font-weight:bold;"><?= count($widgets) ?></div>
                </div>
            </div>
        <?php elseif($section==='users'): ?>
            <div class="admin-card">
                <div class="admin-card-header">Users</div>
                <div class="admin-card-body" style="overflow-x:auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th><th>Username</th><th>Email</th><th>Name</th><th>Role</th><th>Bio</th><th>Joined</th><th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= $user['role'] ?></td>
                                <td><?= htmlspecialchars(mb_substr($user['bio'],0,32)) ?></td>
                                <td><?= date("Y-m-d", strtotime($user['created_at'])) ?></td>
                                <td>
                                    <a href="edit_user.php?id=<?= $user['id'] ?>" class="admin-btn admin-btn-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="delete_user.php?id=<?= $user['id'] ?>" class="admin-btn admin-btn-danger" onclick="return confirm('Delete this user?')" title="Delete"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php elseif($section==='uploads'): ?>
            <div class="admin-card">
                <div class="admin-card-header">Uploads</div>
                <div class="admin-card-body" style="overflow-x:auto;">
                    <table class="admin-table">
                        <thead>
                            <tr><th>ID</th><th>User</th><th>Title</th><th>File</th><th>Approved</th><th>Date</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($uploads as $upload): ?>
                            <tr>
                                <td><?= $upload['id'] ?></td>
                                <td><?= $upload['user_id'] ?></td>
                                <td><?= htmlspecialchars($upload['title']) ?></td>
                                <td><?= htmlspecialchars(basename($upload['file_path'])) ?></td>
                                <td><?= $upload['is_approved'] ? 'Yes' : 'No' ?></td>
                                <td><?= htmlspecialchars($upload['created_at']) ?></td>
                                <td>
                                    <a href="delete_upload.php?id=<?= $upload['id'] ?>" class="admin-btn admin-btn-danger" onclick="return confirm('Delete this upload?')" title="Delete"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php elseif($section==='donations'): ?>
            <div class="admin-card">
                <div class="admin-card-header">Donations</div>
                <div class="admin-card-body" style="overflow-x:auto;">
                    <table class="admin-table">
                        <thead>
                            <tr><th>ID</th><th>Donor</th><th>Recipient</th><th>Amount</th><th>Payment</th><th>Status</th><th>Date</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($donations as $donation): ?>
                            <tr>
                                <td><?= $donation['id'] ?></td>
                                <td><?= $donation['donor_id'] ?></td>
                                <td><?= $donation['recipient_id'] ?></td>
                                <td>Rs. <?= htmlspecialchars($donation['amount']) ?></td>
                                <td><?= htmlspecialchars($donation['payment_method']) ?></td>
                                <td><?= htmlspecialchars($donation['status']) ?></td>
                                <td><?= htmlspecialchars($donation['created_at']) ?></td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php elseif($section==='widgets'): ?>
            <div class="admin-card">
                <div class="admin-card-header">Widgets</div>
                <div class="admin-card-body" style="overflow-x:auto;">
                    <table class="admin-table">
                        <thead>
                            <tr><th>ID</th><th>User</th><th>Title</th><th>Amount</th><th>Payment</th><th>Active</th><th>Created</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($widgets as $widget): ?>
                            <tr>
                                <td><?= $widget['id'] ?></td>
                                <td><?= $widget['user_id'] ?></td>
                                <td><?= htmlspecialchars($widget['title']) ?></td>
                                <td><?= htmlspecialchars($widget['amount']) ?></td>
                                <td><?= htmlspecialchars($widget['payment_method']) ?></td>
                                <td><?= $widget['is_active'] ? 'Yes' : 'No' ?></td>
                                <td><?= htmlspecialchars($widget['created_at']) ?></td>
                                <td>
                                    <a href="delete_widget.php?id=<?= $widget['id'] ?>" class="admin-btn admin-btn-danger" onclick="return confirm('Delete this widget?')" title="Delete"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php elseif($section==='settings'): ?>
            <div class="admin-card">
                <div class="admin-card-header">Admin Settings</div>
                <div class="admin-card-body">
                    <div>
                        <!-- Add any special admin settings forms or export data here -->
                        <strong>Admin Email:</strong> <?= htmlspecialchars($_SESSION['username'] ?? '') ?><br><br>
                        <a href="<?= BASE_URL ?>/settings.php" class="admin-btn admin-btn-primary">Edit Profile &amp; Profile Settings</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
