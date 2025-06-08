<?php
include 'includes/header.php';

$widgetId = isset($_GET['widget']) ? (int)$_GET['widget'] : 0;
$widget = null;
$user = null;

if ($widgetId > 0) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT w.*, u.username, u.name, u.profile_image FROM widgets w JOIN users u ON w.user_id = u.id WHERE w.id = ? AND w.is_active = 1");
    $stmt->execute([$widgetId]);
    $widget = $stmt->fetch();
    if ($widget) {
        $user = $widget;
    }
}

// Helper for profile image
function getProfileImgUrl($profileImg) {
    if (!$profileImg || $profileImg === 'default-avatar.jpg') {
        return 'assets/images/default-avatar.jpg';
    }
    if (strpos($profileImg, 'profile_images/') === false) {
        return 'assets/uploads/profile_images/' . htmlspecialchars($profileImg);
    }
    return 'assets/uploads/' . htmlspecialchars($profileImg);
}
?>

<div class="donate-container" style="padding-top:48px;padding-bottom:48px;">
    <?php if ($widget): ?>
        <div class="card donate-card animated-gradient" style="max-width: 440px; margin: 0 auto; border-radius:18px; box-shadow:0 4px 32px #d5dfff; overflow:hidden;">
            <div class="card-header text-center" style="background:linear-gradient(90deg,#6c5ce7 0%,#a29bfe 100%);border-radius:18px 18px 0 0;padding:38px 24px 22px 24px;color:#fff;">
                <img src="<?= getProfileImgUrl($user['profile_image']) ?>" alt="<?= htmlspecialchars($user['username']) ?>" class="profile-avatar" style="width:92px;height:92px;object-fit:cover;border-radius:50%;border:4px solid #fff;box-shadow:0 2px 16px #bfbfff;margin-bottom:14px;">
                <h2 style="font-size:1.5rem;font-weight:700;margin-bottom:0;">Support <?= htmlspecialchars($user['name'] ?: $user['username']) ?></h2>
                <div style="font-size:1.04rem;color:#e0e0e0;margin-top:7px;">
                    <?= '@' . htmlspecialchars($user['username']) ?>
                </div>
            </div>
            <div class="card-body text-center" style="padding:28px 26px 22px 26px;background:#fff;">
                <h3 class="widget-title" style="font-size:1.2rem;font-weight:700;color:var(--primary);margin-bottom:8px;"><?= htmlspecialchars($widget['title']) ?></h3>
                <?php if ($widget['description']): ?>
                    <p style="color:#555;margin-bottom:16px;"><?= htmlspecialchars($widget['description']) ?></p>
                <?php endif; ?>
                <div class="widget-amount" style="font-size:2.1em;color:var(--primary);margin:18px 0 18px 0;letter-spacing:1px;font-weight:700;">
                    Rs. <?= htmlspecialchars($widget['amount']) ?>
                </div>
                <div style="display:flex;gap:16px;justify-content:center;margin-bottom:14px;">
                    <?php if ($widget['payment_method'] === 'khalti' || $widget['payment_method'] === 'both'): ?>
                        <a href="#" class="btn btn-accent btn-pulse" style="min-width:120px;" onclick="alert('Integrate Khalti payment here!');return false;">
                            <!-- Khalti SVG Icon -->
                            <span style="display:inline-flex;align-items:center;gap:7px;">
                              <svg width="22" height="22" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg" style="vertical-align:middle;">
                                <rect width="60" height="60" rx="12" fill="#5A1988"/>
                                <path d="M16 15.5C16 14.12 17.12 13 18.5 13H41.5C42.88 13 44 14.12 44 15.5V44.5C44 45.88 42.88 47 41.5 47H18.5C17.12 47 16 45.88 16 44.5V15.5Z" fill="white"/>
                                <path d="M30 42C30 42 23.5 32.5 23.5 26.5C23.5 22.91 26.41 20 30 20C33.59 20 36.5 22.91 36.5 26.5C36.5 32.5 30 42 30 42ZM30 29.25C31.24 29.25 32.25 28.24 32.25 27C32.25 25.76 31.24 24.75 30 24.75C28.76 24.75 27.75 25.76 27.75 27C27.75 28.24 28.76 29.25 30 29.25Z" fill="#5A1988"/>
                              </svg>
                              Khalti
                            </span>
                        </a>
                    <?php endif; ?>
                    <?php if ($widget['payment_method'] === 'esewa' || $widget['payment_method'] === 'both'): ?>
                        <a href="#" class="btn btn-success btn-pulse" style="min-width:120px;background:#5cb85c;" onclick="alert('Integrate eSewa payment here!');return false;">
                            <!-- eSewa SVG Icon -->
                            <span style="display:inline-flex;align-items:center;gap:7px;">
                              <svg width="22" height="22" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg" style="vertical-align:middle;">
                                <rect width="60" height="60" rx="12" fill="#5CB85C"/>
                                <g>
                                  <circle cx="30" cy="30" r="16" fill="white"/>
                                  <text x="30" y="38" text-anchor="middle" font-size="22" font-family="Arial, Helvetica, sans-serif" fill="#5CB85C" font-weight="bold">e</text>
                                </g>
                              </svg>
                              eSewa
                            </span>
                        </a>
                    <?php endif; ?>
                </div>
                <hr style="margin:24px 0 16px 0;border-color:#f1f1fa;">
                <div style="font-size:1rem;font-weight:500;margin-bottom:8px;color:#888;">Share this donation link:</div>
                <div style="display:flex;align-items:center;gap:8px;justify-content:center;background:#fafbff;padding:10px 12px;border-radius:10px;box-shadow:0 1px 4px #eeeeff;">
                    <input type="text" id="donation-link" value="<?= htmlspecialchars((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" readonly style="flex:1;padding:7px 10px;border-radius:7px;border:none;background:transparent;font-size:15px;color:#333;outline:none;">
                    <button class="btn btn-sm btn-secondary" id="copy-donation-link" style="border-radius:7px;padding:7px 13px;font-size:14px;">Copy</button>
                </div>
                <div id="copy-success" style="display:none;color:var(--success);margin-top:9px;font-size:15px;">Link copied!</div>
            </div>
        </div>
        <style>
        /* Responsive and modern donate card styles */
        @media (max-width:600px) {
            .donate-card { max-width:98vw; }
            .donate-card .card-header { padding:28px 12px 14px 12px; }
            .donate-card .card-body { padding:14px 8px 14px 8px; }
        }
        .donate-card .btn-accent:hover,
        .donate-card .btn-success:hover {
            filter: brightness(1.08);
            box-shadow: 0 2px 12px #d8d8fa;
        }
        </style>
    <?php else: ?>
        <div class="alert alert-danger" style="max-width:500px;margin:48px auto;">Donation widget not found or inactive.</div>
    <?php endif; ?>
</div>

<script>
document.getElementById('copy-donation-link')?.addEventListener('click', function() {
    var input = document.getElementById('donation-link');
    input.select();
    input.setSelectionRange(0, 99999);
    document.execCommand('copy');
    document.getElementById('copy-success').style.display = 'block';
    setTimeout(function() {
        document.getElementById('copy-success').style.display = 'none';
    }, 1500);
});
</script>

<?php include 'includes/footer.php'; ?>
