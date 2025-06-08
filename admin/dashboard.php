<?php
require_once '../includes/header.php';
if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}
global $pdo;

// Handle user update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $id = (int)$_POST['user_id'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $name = trim($_POST['name']);
    $role = $_POST['role'];
    $bio = trim($_POST['bio']);
    $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, name=?, role=?, bio=? WHERE id=?");
    $stmt->execute([$username, $email, $name, $role, $bio, $id]);
    $msg = "User updated.";
}

// Handle user delete
if (isset($_GET['delete_user'])) {
    $id = (int)$_GET['delete_user'];
    $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
    $msg = "User deleted.";
}

// Handle upload delete
if (isset($_GET['delete_upload'])) {
    $id = (int)$_GET['delete_upload'];
    $pdo->prepare("DELETE FROM uploads WHERE id=?")->execute([$id]);
    $msg = "Upload deleted.";
}

// Handle widget delete
if (isset($_GET['delete_widget'])) {
    $id = (int)$_GET['delete_widget'];
    $pdo->prepare("DELETE FROM widgets WHERE id=?")->execute([$id]);
    $msg = "Widget deleted.";
}

// Fetch all data for admin
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
$uploads = $pdo->query("SELECT * FROM uploads ORDER BY created_at DESC LIMIT 100")->fetchAll();
$donations = $pdo->query("SELECT * FROM donations ORDER BY created_at DESC LIMIT 100")->fetchAll();
$widgets = $pdo->query("SELECT * FROM widgets ORDER BY created_at DESC LIMIT 100")->fetchAll();

$section = $_GET['section'] ?? 'dashboard';
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
    position: sticky;
    top: 0;
    height: 100vh;
    z-index: 10;
}
.admin-sidebar .sidebar-logo {
    font-size: 2rem;
    font-weight: 700;
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
    border-radius: 0 24px 24px 0;
}
.admin-sidebar .sidebar-menu a .fa {
    min-width: 22px;
    text-align: center;
}
.admin-sidebar .sidebar-menu a:hover,
.admin-sidebar .sidebar-menu .active {
    background:rgba(255,255,255,0.11);
    border-left: 3px solid #ffeaa7;
    color: #ffeaa7;
}
.admin-panel-main {
    flex: 1;
    padding: 42px 6vw 42px 6vw;
    min-height: 100vh;
    background: #f6f7ff;
    overflow-x: auto;
    position: relative;
}
.admin-table tr.editing {
    background: #e8eaf6 !important;
}
.admin-table tr:hover {
    background: #f0f4ff;
}
.admin-table .admin-btn[title] {
    position: relative;
}
.admin-table .admin-btn[title]:hover::after {
    content: attr(title);
    position: absolute;
    top: -30px;
    left: 50%;
    transform: translateX(-50%);
    background: #222;
    color: #fff;
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 13px;
    white-space: nowrap;
    z-index: 99;
}
.fab-add-user {
    position: fixed;
    bottom: 36px;
    right: 36px;
    background: var(--admin-primary);
    color: #fff;
    border-radius: 50%;
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    box-shadow: 0 4px 16px #bfcaff;
    cursor: pointer;
    z-index: 100;
    transition: background 0.2s;
}
.fab-add-user:hover {
    background: var(--admin-secondary);
}
.admin-search-bar {
    margin-bottom: 18px;
    display: flex;
    gap: 12px;
    align-items: center;
}
.admin-search-bar input {
    padding: 8px 14px;
    border-radius: 8px;
    border: 1px solid #ddd;
    font-size: 15px;
    width: 220px;
}
.admin-pagination {
    margin-top: 12px;
    display: flex;
    gap: 8px;
    justify-content: flex-end;
}
.admin-pagination button {
    background: #f5f6fa;
    border: none;
    border-radius: 6px;
    padding: 6px 14px;
    font-size: 15px;
    cursor: pointer;
    color: var(--admin-primary);
    transition: background 0.18s;
}
.admin-pagination button.active,
.admin-pagination button:hover {
    background: var(--admin-primary);
    color: #fff;
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
<script>
function confirmDelete(url, msg) {
    if (confirm(msg)) window.location.href = url;
}
function filterTable(inputId, tableId) {
    const filter = document.getElementById(inputId).value.toLowerCase();
    const rows = document.querySelectorAll(`#${tableId} tbody tr`);
    rows.forEach(row => {
        let show = false;
        row.querySelectorAll('td').forEach(td => {
            if (td.innerText.toLowerCase().includes(filter)) show = true;
        });
        row.style.display = show ? '' : 'none';
    });
}
</script>

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
        <?php if (!empty($msg)): ?>
            <div class="alert alert-success" style="margin:18px 0;"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

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
                <div class="admin-card-header" style="display:flex;align-items:center;justify-content:space-between;">
                    <span>Users</span>
                    <div class="admin-search-bar">
                        <input type="text" id="userSearch" placeholder="Search users..." onkeyup="filterTable('userSearch','usersTable')">
                    </div>
                </div>
                <div class="admin-card-body" style="overflow-x:auto;">
                    <table class="admin-table" id="usersTable">
                        <thead>
                            <tr>
                                <th>ID</th><th>Username</th><th>Email</th><th>Name</th><th>Role</th><th>Bio</th><th>Joined</th><th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <form method="post" action="?section=users">
                                    <td><?= $user['id'] ?><input type="hidden" name="user_id" value="<?= $user['id'] ?>"></td>
                                    <td><input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="admin-form-control" required></td>
                                    <td><input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="admin-form-control" required></td>
                                    <td><input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="admin-form-control"></td>
                                    <td>
                                        <select name="role" class="admin-form-control">
                                            <option value="user" <?= $user['role']=='user'?'selected':'' ?>>User</option>
                                            <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="bio" value="<?= htmlspecialchars($user['bio']) ?>" class="admin-form-control"></td>
                                    <td><?= date("Y-m-d", strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <button type="submit" name="edit_user" class="admin-btn admin-btn-success" title="Save"><i class="fas fa-save"></i></button>
                                        <button type="button" class="admin-btn admin-btn-danger" title="Delete" onclick="confirmDelete('?section=users&delete_user=<?= $user['id'] ?>','Delete this user?')"><i class="fas fa-trash"></i></button>
                                    </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="fab-add-user" title="Add User (demo)">
                <i class="fas fa-user-plus"></i>
            </div>
        <?php elseif($section==='uploads'): ?>
            <div class="admin-card">
                <div class="admin-card-header" style="display:flex;align-items:center;justify-content:space-between;">
                    <span>Uploads</span>
                    <div class="admin-search-bar">
                        <input type="text" id="uploadSearch" placeholder="Search uploads..." onkeyup="filterTable('uploadSearch','uploadsTable')">
                    </div>
                </div>
                <div class="admin-card-body" style="overflow-x:auto;">
                    <table class="admin-table" id="uploadsTable">
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
                                    <button type="button" class="admin-btn admin-btn-danger" title="Delete" onclick="confirmDelete('?section=uploads&delete_upload=<?= $upload['id'] ?>','Delete this upload?')"><i class="fas fa-trash"></i></button>
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
                                    <button type="button" class="admin-btn admin-btn-danger" title="Delete" onclick="confirmDelete('?section=widgets&delete_widget=<?= $widget['id'] ?>','Delete this widget?')"><i class="fas fa-trash"></i></button>
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
                        <strong>Admin Username:</strong> <?= htmlspecialchars($_SESSION['username'] ?? '') ?><br>
                        <strong>Admin Role:</strong> <?= htmlspecialchars($_SESSION['role'] ?? '') ?><br><br>
                        <a href="<?= BASE_URL ?>/settings.php" class="admin-btn admin-btn-primary">Edit My Profile</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>