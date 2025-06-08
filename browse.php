<?php
include 'includes/header.php';
include 'includes/auth_functions.php';
include 'includes/helper_functions.php';

$search = trim($_GET['search'] ?? '');
$users = [];
$uploads = [];
$showUploads = false;
if ($search !== '') {
    global $pdo;
    // Search users
    $stmt = $pdo->prepare("SELECT * FROM users WHERE name LIKE ? OR username LIKE ? LIMIT 20");
    $stmt->execute(['%' . $search . '%', '%' . $search . '%']);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Search uploads by title or file name
    $stmt2 = $pdo->prepare("SELECT uploads.*, users.username, users.name, users.profile_image, users.id as user_id FROM uploads JOIN users ON uploads.user_id = users.id WHERE uploads.title LIKE ? OR uploads.file_path LIKE ? ORDER BY uploads.created_at DESC LIMIT 50");
    $stmt2->execute(['%' . $search . '%', '%' . $search . '%']);
    $uploads = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} else {
    $showUploads = true;
    global $pdo;
    // For all uploads (no search)
    $stmt = $pdo->query("SELECT uploads.*, users.username, users.name, users.profile_image, users.id as user_id FROM uploads JOIN users ON uploads.user_id = users.id ORDER BY uploads.created_at DESC LIMIT 50");
    $all_uploads = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<div class="container" style="max-width: 900px; margin-top: 40px;">
  <h1>Browse Code Uploads & Find Developers</h1>
  <form method="get" action="browse.php" style="margin-bottom: 32px; display: flex; gap: 12px;">
    <input type="text" name="search" class="form-control" placeholder="Search by name, username, code title, or file name..." value="<?= htmlspecialchars($search) ?>" style="max-width: 350px;">
    <button type="submit" class="btn btn-primary">Search</button>
  </form>
  <?php if ($showUploads): ?>
    <h2>All Recent Code Uploads</h2>
    <div class="upload-grid" style="gap: 28px;">
      <?php if (isset($all_uploads) && count($all_uploads) > 0): ?>
        <?php foreach ($all_uploads as $upload): ?>
          <div class="card upload-card" style="margin-bottom: 18px; padding:0; overflow:hidden;">
            <a href="view_upload.php?id=<?= $upload['id'] ?>" style="display:block;text-decoration:none;color:inherit;">
              <div style="width:100%;height:170px;overflow:hidden;background:#f5f6fa;display:flex;align-items:center;justify-content:center;">
                <?php if (!empty($upload['poster_path'])): ?>
                  <img src="<?= htmlspecialchars($upload['poster_path']) ?>" alt="Poster" style="width:100%;height:100%;object-fit:cover;">
                <?php else: ?>
                  <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#bbb;font-size:2.5rem;">
                    <i class="fas fa-file-archive"></i>
                  </div>
                <?php endif; ?>
              </div>
              <div class="card-body" style="padding: 18px 18px 12px 18px;">
                <h3 class="upload-title" style="font-size: 1.1rem; margin-bottom: 7px; font-weight: 600; white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                  <?= htmlspecialchars($upload['title']) ?>
                </h3>
                <p class="upload-description" style="color: #555; font-size: 15px; margin-bottom: 8px; height:38px;overflow:hidden;text-overflow:ellipsis;">
                  <?= htmlspecialchars(substr($upload['description'], 0, 100)) ?>...
                </p>
              </div>
            </a>
            <div class="upload-meta" style="background: #f5f6fa; border-top: 1px solid #ececec; padding: 10px 16px; display: flex; align-items: center; gap: 12px;">
              <a href="download.php?id=<?= $upload['id'] ?>" class="btn btn-sm btn-primary" style="background:transparent;border:none;">
                <!-- Download SVG Icon Only -->
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="26" height="26" x="0" y="0" viewBox="0 0 48 48" style="vertical-align:middle;"><g><path d="M24 0a24 24 0 1 0 24 24A24 24 0 0 0 24 0zm7.873 39.46H16.127a2 2 0 1 1 0-4h15.746a2 2 0 0 1 0 4zm1.414-15.044L25.408 32.3a2 2 0 0 1-2.828 0l-7.867-7.866a2 2 0 0 1 2.828-2.834L22 26.059V8.365a2 2 0 0 1 4 0v17.682l4.459-4.459a2 2 0 0 1 2.828 2.828z" fill="#000000" opacity="1"></path></g></svg>
              </a>
              <?php if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $upload['user_id']): ?>
                <form method="post" action="delete_upload.php" style="display:inline; margin-left: 6px;">
                  <input type="hidden" name="upload_id" value="<?= $upload['id'] ?>">
                  <button type="submit" class="btn btn-sm btn-danger" style="background:transparent;border:none;" onclick="return confirm('Are you sure you want to delete this upload?');">
                    <!-- Trash SVG Icon Only -->
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="26" height="26" x="0" y="0" viewBox="0 0 512 512" style="vertical-align:middle;"><g><path fill="#0c0c0c" fill-rule="evenodd" d="M256 0C114.842 0 0 114.842 0 256s114.839 256 256 256 256-114.841 256-256S397.16 0 256 0zm-49.921 114.329a8.325 8.325 0 0 1 8.32-8.324h83.2a8.341 8.341 0 0 1 8.322 8.334v20.577h-99.842zm136.8 279.759A12.745 12.745 0 0 1 330.067 406h-149.1a12.873 12.873 0 0 1-12.807-11.963L155.407 207.4h201.081l-13.614 186.688zM376.02 190.5H135.98v-19.339a19.357 19.357 0 0 1 19.339-19.341l201.359-.006a19.365 19.365 0 0 1 19.338 19.348v19.336zM217.35 361.508V243a8.449 8.449 0 0 1 16.9.006v118.502a8.451 8.451 0 1 1-16.9 0zm60.292 0V243a8.451 8.451 0 0 1 16.9.006v118.507a8.452 8.452 0 1 1-16.9 0z" opacity="1"></path></g></svg>
                  </button>
                </form>
              <?php endif; ?>
              <span class="upload-date" style="font-size: 13px; color: #888; margin-left:auto;">
                By <a href="profile.php?username=<?= urlencode($upload['username']) ?>"><?= htmlspecialchars($upload['name'] ?: $upload['username']) ?></a> &middot; <?= formatDate($upload['created_at']) ?>
              </span>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="alert alert-info">No uploads found.</div>
      <?php endif; ?>
    </div>
  <?php elseif ($search !== ''): ?>
    <h2>Results for "<?= htmlspecialchars($search) ?>"</h2>
    <?php if (count($users) === 0 && count($uploads) === 0): ?>
      <div class="alert alert-info">No users or uploads found.</div>
    <?php else: ?>
      <?php if (count($users) > 0): ?>
        <h3>Matching Users</h3>
        <?php foreach ($users as $user): ?>
          <div class="card" style="margin-bottom: 32px; padding: 24px;">
            <div style="display: flex; align-items: center;">
              <img src="<?php
                $profileImg = $user['profile_image'] ?? '';
                if ($profileImg && $profileImg !== 'default-avatar.jpg' && strpos($profileImg, 'profile_images/') === false) {
                  echo 'assets/uploads/profile_images/' . htmlspecialchars($profileImg);
                } else if ($profileImg && strpos($profileImg, 'profile_images/') !== false) {
                  echo 'assets/uploads/' . htmlspecialchars($profileImg);
                } else {
                  echo 'assets/images/default-avatar.jpg';
                }
              ?>" alt="<?= htmlspecialchars($user['username']) ?>" class="profile-avatar" style="width: 56px; height: 56px; border-radius: 50%; object-fit: cover; margin-right: 18px;">
              <div>
                <div style="font-weight: 600; font-size: 18px; color: var(--primary);">
                  <a href="profile.php?username=<?= urlencode($user['username']) ?>"><?= htmlspecialchars($user['name'] ?: $user['username']) ?></a>
                </div>
                <div style="font-size: 14px; color: #666; margin-bottom: 4px;">
                  <?= htmlspecialchars($user['bio']) ?>
                </div>
              </div>
            </div>
            <div style="margin-top: 18px;">
              <strong>Uploads:</strong>
              <div class="upload-grid">
                <?php $user_uploads = getUserUploads($user['id']); ?>
                <?php if ($user_uploads && count($user_uploads) > 0): ?>
                  <?php foreach ($user_uploads as $upload): ?>
                    <?php $upload['user_id'] = $user['id']; ?>
                    <div class="card upload-card" style="margin-bottom: 10px; display: flex; align-items: flex-start;">
                      <?php if (!empty($upload['poster_path'])): ?>
                        <img src="<?= htmlspecialchars($upload['poster_path']) ?>" alt="Poster" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; margin: 12px;">
                      <?php endif; ?>
                      <div style="flex:1;">
                        <div class="card-body">
                          <h3 class="upload-title">
                            <a href="view_upload.php?id=<?= $upload['id'] ?>" class="upload-link">
                              <?= htmlspecialchars($upload['title']) ?>
                            </a>
                          </h3>
                          <p class="upload-description"><?= htmlspecialchars(substr($upload['description'], 0, 100)) ?>...</p>
                          <div style="font-size: 13px; color: #888; margin-top: 6px;">
                            <strong>File:</strong> <?= htmlspecialchars(basename($upload['file_path'])) ?>
                          </div>
                        </div>
                        <div class="upload-meta">
                          <a href="download.php?id=<?= $upload['id'] ?>" class="btn btn-sm btn-primary">Download</a>
                          <?php if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $upload['user_id']): ?>
                            <form method="post" action="delete_upload.php" style="display:inline; margin-left: 6px;">
                              <input type="hidden" name="upload_id" value="<?= $upload['id'] ?>">
                              <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this upload?');">Delete</button>
                            </form>
                          <?php endif; ?>
                          <span class="upload-date">By <a href="profile.php?username=<?= urlencode($user['username']) ?>"><?= htmlspecialchars($user['name'] ?: $user['username']) ?></a> (<?= htmlspecialchars($user['email']) ?>) &middot; <?= formatDate($upload['created_at']) ?></span>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div class="alert alert-info">No uploads yet.</div>
                <?php endif; ?>
              </div>
            </div>
            <!-- Donation widget for this user -->
            <?php if (!isset($_SESSION['user']) || $_SESSION['user']['id'] != $user['id']): ?>
            <div class="widget-grid" style="margin-top: 18px;">
              <div class="card widget-card" data-amount="50" data-user="<?= $user['id'] ?>">
                <div class="widget-amount">Rs. 50</div>
                <div class="widget-title">Small Tip</div>
                <div class="widget-icon"><i class="fas fa-coffee"></i></div>
              </div>
              <div class="card widget-card" data-amount="100" data-user="<?= $user['id'] ?>">
                <div class="widget-amount">Rs. 100</div>
                <div class="widget-title">Nice Work</div>
                <div class="widget-icon"><i class="fas fa-heart"></i></div>
              </div>
              <div class="card widget-card" data-amount="500" data-user="<?= $user['id'] ?>">
                <div class="widget-amount">Rs. 500</div>
                <div class="widget-title">Awesome Job!</div>
                <div class="widget-icon"><i class="fas fa-star"></i></div>
              </div>
              <div class="card widget-card" data-amount="custom" data-user="<?= $user['id'] ?>">
                <div class="widget-amount">Custom</div>
                <div class="widget-title">Any Amount</div>
                <div class="widget-icon"><i class="fas fa-edit"></i></div>
              </div>
            </div>
            <?php else: ?>
              <div class="alert alert-info" style="margin-top: 18px;">You cannot donate to yourself.</div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
      <?php if (count($uploads) > 0): ?>
        <h3>Matching Code Uploads</h3>
        <div class="upload-grid">
          <?php foreach ($uploads as $upload): ?>
            <div class="card upload-card" style="margin-bottom: 18px; display: flex; align-items: flex-start;">
              <?php if (!empty($upload['poster_path'])): ?>
                <img src="<?= htmlspecialchars($upload['poster_path']) ?>" alt="Poster" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin: 16px;">
              <?php endif; ?>
              <div style="flex:1;">
                <div class="card-body">
                  <h3 class="upload-title">
                    <a href="view_upload.php?id=<?= $upload['id'] ?>" class="upload-link">
                      <?= htmlspecialchars($upload['title']) ?>
                    </a>
                  </h3>
                  <p class="upload-description"><?= htmlspecialchars(substr($upload['description'], 0, 100)) ?>...</p>
                  <div style="font-size: 13px; color: #888; margin-top: 6px;">
                    <strong>File:</strong> <?= htmlspecialchars(basename($upload['file_path'])) ?>
                  </div>
                </div>
                <div class="upload-meta">
                  <a href="download.php?id=<?= $upload['id'] ?>" class="btn btn-sm btn-primary" style="background:transparent;border:none;">
                    <!-- Download SVG Icon Only -->
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="26" height="26" x="0" y="0" viewBox="0 0 48 48" style="vertical-align:middle;"><g><path d="M24 0a24 24 0 1 0 24 24A24 24 0 0 0 24 0zm7.873 39.46H16.127a2 2 0 1 1 0-4h15.746a2 2 0 0 1 0 4zm1.414-15.044L25.408 32.3a2 2 0 0 1-2.828 0l-7.867-7.866a2 2 0 0 1 2.828-2.834L22 26.059V8.365a2 2 0 0 1 4 0v17.682l4.459-4.459a2 2 0 0 1 2.828 2.828z" fill="#000000" opacity="1"></path></g></svg>
                  </a>
                  <?php if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $upload['user_id']): ?>
                    <form method="post" action="delete_upload.php" style="display:inline; margin-left: 6px;">
                      <input type="hidden" name="upload_id" value="<?= $upload['id'] ?>">
                      <button type="submit" class="btn btn-sm btn-danger" style="background:transparent;border:none;" onclick="return confirm('Are you sure you want to delete this upload?');">
                        <!-- Trash SVG Icon Only -->
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="26" height="26" x="0" y="0" viewBox="0 0 512 512" style="vertical-align:middle;"><g><path fill="#0c0c0c" fill-rule="evenodd" d="M256 0C114.842 0 0 114.842 0 256s114.839 256 256 256 256-114.841 256-256S397.16 0 256 0zm-49.921 114.329a8.325 8.325 0 0 1 8.32-8.324h83.2a8.341 8.341 0 0 1 8.322 8.334v20.577h-99.842zm136.8 279.759A12.745 12.745 0 0 1 330.067 406h-149.1a12.873 12.873 0 0 1-12.807-11.963L155.407 207.4h201.081l-13.614 186.688zM376.02 190.5H135.98v-19.339a19.357 19.357 0 0 1 19.339-19.341l201.359-.006a19.365 19.365 0 0 1 19.338 19.348v19.336zM217.35 361.508V243a8.449 8.449 0 0 1 16.9.006v118.502a8.451 8.451 0 1 1-16.9 0zm60.292 0V243a8.451 8.451 0 0 1 16.9.006v118.507a8.452 8.452 0 1 1-16.9 0z" opacity="1"></path></g></svg>
                      </button>
                    </form>
                  <?php endif; ?>
                  <span class="upload-date">By <a href="profile.php?username=<?= urlencode($upload['username']) ?>"><?= htmlspecialchars($upload['name'] ?: $upload['username']) ?></a> (<?= htmlspecialchars(getUserById($upload['user_id'])['email']) ?>) &middot; <?= formatDate($upload['created_at']) ?></span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  <?php endif; ?>
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
  Swal.fire({
    title: `Donate Rs. ${amount} to this developer?`,
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
