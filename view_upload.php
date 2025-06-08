<?php
include 'includes/header.php';
include 'includes/auth_functions.php';
include 'includes/helper_functions.php';

$uploadId = $_GET['id'] ?? null;
if (!$uploadId || !is_numeric($uploadId)) {
    echo '<div class="container"><div class="alert alert-danger">Invalid upload ID.</div></div>';
    include 'includes/footer.php';
    exit;
}

$upload = getUploadById($uploadId); // You should have this helper function
if (!$upload) {
    echo '<div class="container"><div class="alert alert-danger">Upload not found.</div></div>';
    include 'includes/footer.php';
    exit;
}

$uploader = getUserById($upload['user_id']);
?>
<div class="container" style="max-width: 800px; margin-top: 40px;">
  <div class="card" style="padding: 32px;">
    <h1 style="margin-bottom: 10px;"><?= htmlspecialchars($upload['title']) ?></h1>
    <div style="color: #888; margin-bottom: 16px;">
      By <a href="profile.php?username=<?= urlencode($uploader['username']) ?>" class="profile-link">
        <?= htmlspecialchars($uploader['name'] ?: $uploader['username']) ?>
      </a>
      (<?= htmlspecialchars($uploader['email']) ?>)
      <span style="float:right;"><?= formatDate($upload['created_at']) ?></span>
    </div>
    <!-- Mini uploader profile card -->
    <div class="card" style="display: flex; align-items: center; margin-bottom: 24px; background: #f8f9fa; padding: 18px 24px;">
      <img src="<?php
        $profileImg = $uploader['profile_image'] ?? '';
        if ($profileImg && $profileImg !== 'default-avatar.jpg' && strpos($profileImg, 'profile_images/') === false) {
          echo 'assets/uploads/profile_images/' . htmlspecialchars($profileImg);
        } else if ($profileImg && strpos($profileImg, 'profile_images/') !== false) {
          echo 'assets/uploads/' . htmlspecialchars($profileImg);
        } else {
          echo 'assets/images/default-avatar.jpg';
        }
      ?>" alt="<?= htmlspecialchars($uploader['username']) ?>" class="profile-avatar" style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover; margin-right: 18px;">
      <div>
        <div style="font-weight: 600; font-size: 18px; color: var(--primary);">
          <?= htmlspecialchars($uploader['name'] ?: $uploader['username']) ?>
        </div>
        <div style="font-size: 14px; color: #666; margin-bottom: 4px;">
          <?= htmlspecialchars($uploader['bio']) ?>
        </div>
        <a href="profile.php?username=<?= urlencode($uploader['username']) ?>" class="btn btn-sm btn-secondary">View Full Profile</a>
      </div>
    </div>
    <!-- Donation widget for uploader -->
    <?php if (!isset($_SESSION['user']) || $_SESSION['user']['id'] != $uploader['id']): ?>
    <div class="widget-grid" style="margin-bottom: 24px;">
      <div class="card widget-card" data-amount="50" data-user="<?= $uploader['id'] ?>">
        <div class="widget-amount">Rs. 50</div>
        <div class="widget-title">Small Tip</div>
        <div class="widget-icon"><i class="fas fa-coffee"></i></div>
      </div>
      <div class="card widget-card" data-amount="100" data-user="<?= $uploader['id'] ?>">
        <div class="widget-amount">Rs. 100</div>
        <div class="widget-title">Nice Work</div>
        <div class="widget-icon"><i class="fas fa-heart"></i></div>
      </div>
      <div class="card widget-card" data-amount="500" data-user="<?= $uploader['id'] ?>">
        <div class="widget-amount">Rs. 500</div>
        <div class="widget-title">Awesome Job!</div>
        <div class="widget-icon"><i class="fas fa-star"></i></div>
      </div>
      <div class="card widget-card" data-amount="custom" data-user="<?= $uploader['id'] ?>">
        <div class="widget-amount">Custom</div>
        <div class="widget-title">Any Amount</div>
        <div class="widget-icon"><i class="fas fa-edit"></i></div>
      </div>
    </div>
    <?php else: ?>
      <div class="alert alert-info" style="margin-bottom: 24px;">You cannot donate to yourself.</div>
    <?php endif; ?>
    <div style="margin-bottom: 24px;">
      <strong>Description:</strong><br>
      <?= nl2br(htmlspecialchars($upload['description'])) ?>
    </div>
    <div style="background: #f5f6fa; border-radius: 8px; padding: 20px; margin-bottom: 24px; overflow-x: auto;">
      <strong>Code Preview:</strong><br>
      <pre style="white-space: pre-wrap; word-break: break-all; font-size: 15px; margin: 0;">
<?= htmlspecialchars(getUploadFileContents($upload['file_path'])) ?>
      </pre>
    </div>
    <a href="download.php?id=<?= $upload['id'] ?>" class="btn btn-primary">Download</a>
    <a href="profile.php?username=<?= urlencode($uploader['username']) ?>" class="btn btn-secondary" style="margin-left: 10px;">View Uploader Profile</a>
  </div>
</div>
<script>
// Donation widget functionality (same as profile page)
try {
  document.querySelectorAll('.widget-card').forEach(widget => {
    widget.addEventListener('click', function() {
      const amount = this.getAttribute('data-amount');
      const userId = this.getAttribute('data-user');
      if (amount === 'custom') {
        let customAmount = prompt('Enter donation amount (Rs):');
        if (customAmount === null) return; // Cancelled
        customAmount = customAmount.trim();
        if (!customAmount || isNaN(customAmount) || Number(customAmount) <= 0) {
          Swal.fire({
            icon: 'error',
            title: 'Invalid Amount',
            text: 'Please enter a valid positive number for the donation amount.'
          });
          return;
        }
        initiateDonation(userId, customAmount);
      } else {
        initiateDonation(userId, amount);
      }
    });
  });
} catch (e) { console.error('Donation widget error:', e); }
function initiateDonation(userId, amount) {
  const user = <?= json_encode($uploader) ?>;
  Swal.fire({
    title: `Donate Rs. ${amount} to ${user.name || user.username}?`,
    text: 'You will be redirected to the payment gateway',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Proceed to Pay',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = `payment.php?user=${userId}&amount=${amount}`;
    }
  });
}
</script>
<?php include 'includes/footer.php'; ?>
