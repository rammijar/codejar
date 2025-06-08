<?php
require_once 'includes/header.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$errors = [];
$success = false;
$widgets = [];

// Get user's existing widgets
global $pdo;
$stmt = $pdo->prepare("SELECT * FROM widgets WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$widgets = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $amount = trim($_POST['amount']);
    $description = trim($_POST['description']);
    $payment_method = $_POST['payment_method'];
    
    // Validation
    if (empty($title)) $errors[] = 'Title is required';
    if (empty($amount) || !is_numeric($amount) || $amount <= 0) $errors[] = 'Valid amount is required';
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO widgets (user_id, title, amount, description, payment_method) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$_SESSION['user_id'], $title, $amount, $description, $payment_method])) {
            $success = true;
            // Refresh widgets list
            $stmt = $pdo->prepare("SELECT * FROM widgets WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$_SESSION['user_id']]);
            $widgets = $stmt->fetchAll();
        } else {
            $errors[] = 'Failed to create widget';
        }
    }
}

// Handle widget deletion
if (isset($_GET['delete'])) {
    $widgetId = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM widgets WHERE id = ? AND user_id = ?");
    if ($stmt->execute([$widgetId, $_SESSION['user_id']])) {
        $success = true;
        // Refresh widgets list
        $stmt = $pdo->prepare("SELECT * FROM widgets WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$_SESSION['user_id']]);
        $widgets = $stmt->fetchAll();
    } else {
        $errors[] = 'Failed to delete widget';
    }
}
?>

<div class="widgets-container">
    <div class="card" style="max-width:1100px;margin:36px auto 32px auto;box-shadow:0 2px 16px #ececec;border-radius:18px;">
        <div class="card-header" style="border-radius:18px 18px 0 0;background:linear-gradient(90deg,#6c5ce7 0%,#a29bfe 100%);color:#fff;padding:32px 32px 22px 32px;">
            <h2 style="font-size:2rem;font-weight:700;margin-bottom:0;">Your Donation Widgets</h2>
        </div>
        <div class="card-body" style="padding:32px;">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    Operation completed successfully!
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
            
            <div class="row" style="display:flex;gap:32px;flex-wrap:wrap;">
                <div class="col-md-5" style="flex:1 1 340px;min-width:320px;max-width:400px;">
                    <div class="card" style="box-shadow:0 1px 8px #ececec;border-radius:14px;">
                        <div class="card-header" style="background:transparent;padding:22px 18px 0 18px;">
                            <h3 style="font-size:1.3rem;color:var(--primary);font-weight:700;">Create New Widget</h3>
                        </div>
                        <div class="card-body" style="padding:18px;">
                            <form method="POST" action="widgets.php">
                                <div class="form-group" style="margin-bottom:16px;">
                                    <label for="title" style="font-weight:600;">Widget Title</label>
                                    <input type="text" id="title" name="title" class="form-control" required placeholder="e.g. Buy me a coffee">
                                </div>
                                <div class="form-group" style="margin-bottom:16px;">
                                    <label for="amount" style="font-weight:600;">Amount (Rs.)</label>
                                    <input type="number" id="amount" name="amount" class="form-control" min="1" step="1" required placeholder="Enter amount">
                                </div>
                                <div class="form-group" style="margin-bottom:16px;">
                                    <label for="description" style="font-weight:600;">Description <span style="color:#888;font-weight:400;">(Optional)</span></label>
                                    <textarea id="description" name="description" class="form-control" rows="3" placeholder="Write a short message..."></textarea>
                                </div>
                                <div class="form-group" style="margin-bottom:18px;">
                                    <label for="payment_method" style="font-weight:600;">Payment Method</label>
                                    <select id="payment_method" name="payment_method" class="form-control">
                                        <option value="both">Khalti or eSewa</option>
                                        <option value="khalti">Khalti Only</option>
                                        <option value="esewa">eSewa Only</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block" style="width:100%;font-size:1.1rem;padding:10px 0;border-radius:24px;">Create Widget</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-7" style="flex:2 1 480px;min-width:320px;">
                    <div class="card" style="box-shadow:0 1px 8px #ececec;border-radius:14px;">
                        <div class="card-header" style="background:transparent;padding:22px 18px 0 18px;">
                            <h3 style="font-size:1.3rem;color:var(--primary);font-weight:700;">Your Widgets</h3>
                        </div>
                        <div class="card-body" style="padding:18px;">
                            <?php if (empty($widgets)): ?>
                                <p style="color:#888;">You haven't created any widgets yet.</p>
                            <?php else: ?>
                                <div class="widgets-list" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:18px;">
                                    <?php foreach ($widgets as $widget): ?>
                                        <div class="widget-item card" style="box-shadow:0 1px 8px #ececec;border-radius:12px;padding:0;overflow:hidden;">
                                            <div class="card-body" style="padding:22px 16px 16px 16px;">
                                                <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                                                    <div style="font-size:1.7rem;font-weight:700;color:var(--primary);"><?= htmlspecialchars($widget['title']) ?></div>
                                                    <span style="background:var(--light);color:var(--primary);font-size:1rem;font-weight:600;padding:6px 15px;border-radius:18px;box-shadow:0 1px 4px #ececec;margin-left:auto;">
                                                        Rs. <?= number_format($widget['amount'], 2) ?>
                                                    </span>
                                                </div>
                                                <?php if ($widget['description']): ?>
                                                    <div class="widget-description" style="color:#555;font-size:1rem;margin-bottom:12px;">
                                                        <?= htmlspecialchars($widget['description']) ?>
                                                    </div>
                                                <?php endif; ?>
                                                <div style="font-size:13px;color:#888;margin-bottom:10px;">
                                                    Payment: <strong><?= ucfirst($widget['payment_method']) ?></strong>
                                                </div>
                                                <div style="margin-bottom:14px;background:#fafbff;border-radius:8px;padding:10px 12px;display:flex;align-items:center;gap:8px;box-shadow:0 1px 4px #f0f0f0;">
                                                    <?php
                                                        $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['PHP_SELF']) . "/donate.php?widget=" . $widget['id'];
                                                    ?>
                                                    <span style="color:#6c5ce7;">
                                                        <!-- Link SVG -->
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20"><path fill="currentColor" d="M7.5 13a1 1 0 1 1 0 2H6a5 5 0 0 1 0-10h1.5a1 1 0 1 1 0 2H6a3 3 0 1 0 0 6zm5-8a1 1 0 1 1 0-2H14a5 5 0 0 1 0 10h-1.5a1 1 0 1 1 0-2H14a3 3 0 1 0 0-6zm-7 5a1 1 0 0 1 1-1h7a1 1 0 1 1 0 2H6.5a1 1 0 0 1-1-1z"/></svg>
                                                    </span>
                                                    <input type="text" value="<?= htmlspecialchars($link) ?>" readonly style="flex:1;border:none;background:transparent;font-size:15px;color:#333;outline:none;" id="widget-link-<?= $widget['id'] ?>">
                                                    <button class="btn btn-sm btn-secondary copy-widget-link" data-widget-id="<?= $widget['id'] ?>" style="margin-left:4px;border-radius:8px;font-size:14px;padding:6px 10px;">Copy</button>
                                                </div>
                                                <div class="widget-actions" style="display:flex;gap:10px;">
                                                    <a href="widgets.php?delete=<?= $widget['id'] ?>" class="btn btn-sm btn-danger" 
                                                       style="border-radius:8px;padding:7px 16px;font-weight:600;"
                                                       onclick="return confirm('Are you sure you want to delete this widget?')">
                                                        <i class="fas fa-trash-alt"></i> Delete
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Copy widget link to clipboard
document.querySelectorAll('.copy-widget-link').forEach(button => {
    button.addEventListener('click', function() {
        const widgetId = this.getAttribute('data-widget-id');
        const input = document.getElementById('widget-link-' + widgetId);
        input.select();
        input.setSelectionRange(0, 99999);
        document.execCommand('copy');
        this.textContent = 'Copied!';
        this.classList.add('btn-success');
        this.classList.remove('btn-secondary');
        setTimeout(() => {
            this.textContent = 'Copy';
            this.classList.add('btn-secondary');
            this.classList.remove('btn-success');
        }, 1200);
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>