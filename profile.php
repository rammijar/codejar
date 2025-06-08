<?php 
include 'includes/header.php';

// Get user profile data
$username = $_GET['username'] ?? '';
$user = getUserByUsername($username); // Assume this function exists

if (!$user) {
  header('Location: 404.php');
  exit;
}

// Add stats for uploads/donations
$totalUploads = countUserUploads($user['id']);
$totalDonations = countUserDonations($user['id']);
$totalDonationAmount = calculateTotalDonations($user['id']);
$profileUrl = BASE_URL . '/profile.php?username=' . urlencode($user['username']);
?>

<div class="profile-container" style="margin-top: 90px;">
  <div class="profile-header animate-on-scroll">
    <img src="<?php
      $profileImg = $user['profile_image'] ?? '';
      if ($profileImg && $profileImg !== 'default-avatar.jpg' && strpos($profileImg, 'profile_images/') === false) {
        echo 'assets/uploads/profile_images/' . htmlspecialchars($profileImg);
      } else if ($profileImg && strpos($profileImg, 'profile_images/') !== false) {
        echo 'assets/uploads/' . htmlspecialchars($profileImg);
      } else {
        echo 'assets/images/default-avatar.jpg';
      }
    ?>"
         alt="<?= htmlspecialchars($user['username']) ?>" 
         class="profile-avatar">
    <div class="profile-info">
      <h1 class="profile-name"><?= htmlspecialchars($user['name'] ?: $user['username']) ?></h1>
      <p class="profile-bio"><?= htmlspecialchars($user['bio']) ?></p>
      <div class="profile-stats" style="margin-top:12px;display:flex;gap:24px;">
        <div>
          <span class="stat-number"><?= $totalUploads ?></span>
          <span class="stat-label">Uploads</span>
        </div>
        <div>
          <span class="stat-number"><?= $totalDonations ?></span>
          <span class="stat-label">Donations</span>
        </div>
        <div>
          <span class="stat-number">Rs. <?= number_format($totalDonationAmount,2) ?></span>
          <span class="stat-label">Total Donated</span>
        </div>
      </div>
      <div style="margin-top:14px;">
        <button class="btn btn-secondary btn-sm" id="copy-profile-link" style="margin-right:10px;">Copy Profile Link</button>
        <!-- Contact Me button removed -->
      </div>
    </div>
  </div>
  <div class="profile-tabs">
    <ul class="tab-nav">
      <li class="active"><a href="#uploads">Code Uploads</a></li>
      <?php if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $user['id']): ?>
        <li><a href="#donate">Support Me</a></li>
      <?php endif; ?>
      <li><a href="#about">About</a></li>
    </ul>
    <div class="tab-content active" id="uploads" style="background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(108,92,231,0.06); padding: 32px 24px 24px 24px; margin-bottom: 24px;">
      <h2 class="section-title" style="font-size: 2rem; color: var(--primary); margin-bottom: 18px; letter-spacing: 0.5px; font-weight: 700;">
        <i class="fas fa-folder-open" style="margin-right: 10px; color: var(--secondary);"></i>
        My Code Uploads
      </h2>
      <div class="upload-grid" style="gap: 24px;">
        <?php 
        $uploads = getUserUploads($user['id']);
        if ($uploads && count($uploads) > 0):
          foreach ($uploads as $upload): ?>
            <div class="card upload-card" style="border-radius: 14px; box-shadow: 0 2px 12px rgba(108,92,231,0.07); overflow: hidden; padding:0;">
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
                <div class="card-body" style="padding: 18px 18px 10px 18px;">
                  <h3 class="upload-title" style="font-size: 1.1rem; margin-bottom: 7px; font-weight: 600; white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    <?= htmlspecialchars($upload['title']) ?>
                  </h3>
                  <div class="upload-description" style="color: #555; font-size: 15px; margin-bottom: 8px; min-height:38px;max-height:48px;overflow:hidden;text-overflow:ellipsis;white-space:pre-line;">
                    <?= nl2br(htmlspecialchars($upload['description'])) ?>
                  </div>
                </div>
              </a>
              <div class="upload-meta" style="background: #f5f6fa; border-top: 1px solid #ececec; padding: 10px 16px; display: flex; align-items: center; gap: 12px;">
                <a href="download.php?id=<?= $upload['id'] ?>" 
                   class="btn btn-sm download-btn"
                   style="display:inline-flex;align-items:center;gap:7px;padding:0;background:transparent;box-shadow:none;border:none;"
                   title="Download <?= htmlspecialchars($upload['title']) ?>">
                  <!-- Download SVG Icon Only -->
                  <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 48 48" style="vertical-align:middle;">
                    <g>
                      <path d="M24 0a24 24 0 1 0 24 24A24 24 0 0 0 24 0zm7.873 39.46H16.127a2 2 0 1 1 0-4h15.746a2 2 0 0 1 0 4zm1.414-15.044L25.408 32.3a2 2 0 0 1-2.828 0l-7.867-7.866a2 2 0 0 1 2.828-2.834L22 26.059V8.365a2 2 0 0 1 4 0v17.682l4.459-4.459a2 2 0 0 1 2.828 2.828z" fill="#000" opacity="1"/>
                    </g>
                  </svg>
                </a>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $upload['user_id']): ?>
                  <form method="post" action="delete_upload.php" style="display:inline; margin-left:7px;">
                    <input type="hidden" name="upload_id" value="<?= $upload['id'] ?>">
                    <button type="submit" 
                            class="btn btn-sm delete-btn"
                            style="display:inline-flex;align-items:center;gap:7px;background:transparent;padding:0;border:none;box-shadow:none;"
                            title="Delete <?= htmlspecialchars($upload['title']) ?>"
                            onclick="return confirm('Are you sure you want to delete this upload?');">
                      <!-- Trash SVG Icon Only -->
                      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 512 512" style="vertical-align:middle;">
                        <g>
                          <path fill="#0c0c0c" fill-rule="evenodd" d="M256 0C114.842 0 0 114.842 0 256s114.839 256 256 256 256-114.841 256-256S397.16 0 256 0zm-49.921 114.329a8.325 8.325 0 0 1 8.32-8.324h83.2a8.341 8.341 0 0 1 8.322 8.334v20.577h-99.842zm136.8 279.759A12.745 12.745 0 0 1 330.067 406h-149.1a12.873 12.873 0 0 1-12.807-11.963L155.407 207.4h201.081l-13.614 186.688zM376.02 190.5H135.98v-19.339a19.357 19.357 0 0 1 19.339-19.341l201.359-.006a19.365 19.365 0 0 1 19.338 19.348v19.336zM217.35 361.508V243a8.449 8.449 0 0 1 16.9.006v118.502a8.451 8.451 0 1 1-16.9 0zm60.292 0V243a8.451 8.451 0 0 1 16.9.006v118.507a8.452 8.452 0 1 1-16.9 0z" opacity="1"/>
                        </g>
                      </svg>
                    </button>
                  </form>
                <?php endif; ?>
                <span class="upload-date" style="font-size: 13px; color: #888; margin-left:auto;">
                  <?= formatDate($upload['created_at']) ?>
                </span>
              </div>
            </div>
          <?php endforeach;
        else: ?>
          <div class="alert alert-info" style="margin-top: 24px; font-size: 1.1rem;">No uploads yet.</div>
        <?php endif; ?>
      </div>
    </div>
    <?php if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $user['id']): ?>
    <div class="tab-content" id="donate">
      <h2 class="section-title">Support My Work</h2>
      <p>If you find my code helpful, consider supporting me with a donation!</p>
      <div class="widget-grid">
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
    </div>
    <?php endif; ?>
    <div class="tab-content" id="about">
      <h2 class="section-title">About Me</h2>
      <div class="about-content">
        <?php if ($user['bio']): ?>
          <p><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
        <?php else: ?>
          <p>This user hasn't written a bio yet.</p>
        <?php endif; ?>

        <!-- Show README file if exists -->
        <?php
        $readmePath = __DIR__ . '/assets/uploads/profile_readme/' . $user['id'] . '_README.md';
        if (file_exists($readmePath)) {
            $readmeContent = file_get_contents($readmePath);
            $readmeHtml = nl2br(htmlspecialchars($readmeContent));
            echo '<div class="card" style="margin:18px 0 28px 0;padding:18px 22px;background:#fafbff;border-radius:14px;box-shadow:0 2px 8px #ececec;"><h4 style="color:var(--primary);margin-bottom:10px;">README</h4><div style="font-size:15px;color:#444;">' . $readmeHtml . '</div></div>';
        }
        ?>

        <!-- Social links card below About Me -->
        <div class="card" style="margin:24px auto 0 auto;max-width:420px;padding:24px 18px 18px 18px;border-radius:14px;box-shadow:0 2px 12px #ececec;">
          <h3 style="margin-bottom:16px;font-size:1.1rem;color:var(--primary);font-weight:700;text-align:center;">Contact & Social Links</h3>
          <div class="social-links" style="display:flex;gap:28px;align-items:center;justify-content:center;">
            <?php if (!empty($user['github'])): ?>
              <a href="https://github.com/<?= htmlspecialchars($user['github']) ?>" target="_blank" class="social-link" data-tooltip="GitHub" style="background:#f5f6fa;border-radius:12px;padding:10px 14px;display:flex;align-items:center;gap:8px;text-decoration:none;box-shadow:0 1px 4px #ececec;">
                <!-- Modern GitHub SVG -->
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" x="0" y="0" viewBox="0 0 438.549 438.549" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M409.132 114.573c-19.608-33.596-46.205-60.194-79.798-79.8-33.598-19.607-70.277-29.408-110.063-29.408-39.781 0-76.472 9.804-110.063 29.408-33.596 19.605-60.192 46.204-79.8 79.8C9.803 148.168 0 184.854 0 224.63c0 47.78 13.94 90.745 41.827 128.906 27.884 38.164 63.906 64.572 108.063 79.227 5.14.954 8.945.283 11.419-1.996 2.475-2.282 3.711-5.14 3.711-8.562 0-.571-.049-5.708-.144-15.417a2549.81 2549.81 0 0 1-.144-25.406l-6.567 1.136c-4.187.767-9.469 1.092-15.846 1-6.374-.089-12.991-.757-19.842-1.999-6.854-1.231-13.229-4.086-19.13-8.559-5.898-4.473-10.085-10.328-12.56-17.556l-2.855-6.57c-1.903-4.374-4.899-9.233-8.992-14.559-4.093-5.331-8.232-8.945-12.419-10.848l-1.999-1.431c-1.332-.951-2.568-2.098-3.711-3.429-1.142-1.331-1.997-2.663-2.568-3.997-.572-1.335-.098-2.43 1.427-3.289 1.525-.859 4.281-1.276 8.28-1.276l5.708.853c3.807.763 8.516 3.042 14.133 6.851 5.614 3.806 10.229 8.754 13.846 14.842 4.38 7.806 9.657 13.754 15.846 17.847 6.184 4.093 12.419 6.136 18.699 6.136 6.28 0 11.704-.476 16.274-1.423 4.565-.952 8.848-2.383 12.847-4.285 1.713-12.758 6.377-22.559 13.988-29.41-10.848-1.14-20.601-2.857-29.264-5.14-8.658-2.286-17.605-5.996-26.835-11.14-9.235-5.137-16.896-11.516-22.985-19.126-6.09-7.614-11.088-17.61-14.987-29.979-3.901-12.374-5.852-26.648-5.852-42.826 0-23.035 7.52-42.637 22.557-58.817-7.044-17.318-6.379-36.732 1.997-58.24 5.52-1.715 13.706-.428 24.554 3.853 10.85 4.283 18.794 7.952 23.84 10.994 5.046 3.041 9.089 5.618 12.135 7.708 17.705-4.947 35.976-7.421 54.818-7.421s37.117 2.474 54.823 7.421l10.849-6.849c7.419-4.57 16.18-8.758 26.262-12.565 10.088-3.805 17.802-4.853 23.134-3.138 8.562 21.509 9.325 40.922 2.279 58.24 15.036 16.18 22.559 35.787 22.559 58.817 0 16.178-1.958 30.497-5.853 42.966-3.9 12.471-8.941 22.457-15.125 29.979-6.191 7.521-13.901 13.85-23.131 18.986-9.232 5.14-18.182 8.85-26.84 11.136-8.662 2.286-18.415 4.004-29.263 5.146 9.894 8.562 14.842 22.077 14.842 40.539v60.237c0 3.422 1.19 6.279 3.572 8.562 2.379 2.279 6.136 2.95 11.276 1.995 44.163-14.653 80.185-41.062 108.068-79.226 27.88-38.161 41.825-81.126 41.825-128.906-.01-39.771-9.818-76.454-29.414-110.049z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>   </a>
            <?php endif; ?>
            <?php if (!empty($user['twitter'])): ?>
              <a href="https://x.com/<?= htmlspecialchars($user['twitter']) ?>" target="_blank" class="social-link" data-tooltip="X / Twitter" style="background:#f5f6fa;border-radius:12px;padding:10px 14px;display:flex;align-items:center;gap:8px;text-decoration:none;box-shadow:0 1px 4px #ececec;">
                <!-- Modern X/Twitter SVG -->
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" x="0" y="0" viewBox="0 0 1227 1227" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M613.5 0C274.685 0 0 274.685 0 613.5S274.685 1227 613.5 1227 1227 952.315 1227 613.5 952.315 0 613.5 0z" fill="#000000" opacity="1" data-original="#000000" class=""></path><path fill="#ffffff" d="m680.617 557.98 262.632-305.288h-62.235L652.97 517.77 470.833 252.692H260.759l275.427 400.844-275.427 320.142h62.239l240.82-279.931 192.35 279.931h210.074L680.601 557.98zM345.423 299.545h95.595l440.024 629.411h-95.595z" opacity="1" data-original="#ffffff"></path></g></svg>  </a>
            <?php endif; ?>
            <?php if (!empty($user['website'])): ?>
              <a href="<?= htmlspecialchars($user['website']) ?>" target="_blank" class="social-link" data-tooltip="Website" style="background:#f5f6fa;border-radius:12px;padding:10px 14px;display:flex;align-items:center;gap:8px;text-decoration:none;box-shadow:0 1px 4px #ececec;">
                <!-- Modern Globe SVG -->
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" x="0" y="0" viewBox="0 0 52 52" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M21.213 13.925A13.063 13.063 0 0 0 14.479 20h4.167a16.756 16.756 0 0 1 2.567-6.075zM18 26a28.351 28.351 0 0 1 .286-4h-4.65a12.9 12.9 0 0 0 0 8h4.65A28.351 28.351 0 0 1 18 26zM31.3 20c-1.077-4.338-3.239-7-5.3-7s-4.227 2.662-5.3 7zM20 26a26.651 26.651 0 0 0 .294 4h11.412a27.358 27.358 0 0 0 0-8H20.294A26.651 26.651 0 0 0 20 26zM14.479 32a13.063 13.063 0 0 0 6.734 6.075A16.756 16.756 0 0 1 18.646 32zM34 26a28.351 28.351 0 0 1-.286 4h4.65a12.9 12.9 0 0 0 0-8h-4.65A28.351 28.351 0 0 1 34 26z" fill="#000000" opacity="1" data-original="#000000" class=""></path><path d="M26 2a24 24 0 1 0 24 24A24.028 24.028 0 0 0 26 2zm0 39a15 15 0 1 1 15-15 15.017 15.017 0 0 1-15 15z" fill="#000000" opacity="1" data-original="#000000" class=""></path><path d="M20.7 32c1.077 4.338 3.239 7 5.3 7s4.227-2.662 5.3-7zM30.787 13.925A16.756 16.756 0 0 1 33.354 20h4.167a13.063 13.063 0 0 0-6.734-6.075zM30.787 38.075A13.063 13.063 0 0 0 37.521 32h-4.167a16.756 16.756 0 0 1-2.567 6.075z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>  </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Remove: Share My Profile and QR code sections -->

<!-- Contact Me Modal -->
<div id="contact-modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.35);z-index:9999;align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:12px;max-width:400px;width:90vw;padding:32px 24px;position:relative;">
    <button id="close-contact-modal" style="position:absolute;top:12px;right:16px;background:none;border:none;font-size:1.5em;cursor:pointer;">&times;</button>
    <h2 style="margin-bottom:18px;">Contact <?= htmlspecialchars($user['name'] ?: $user['username']) ?></h2>
    <form id="contact-form">
      <div class="form-group">
        <label for="contact-email">Your Email</label>
        <input type="email" id="contact-email" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="contact-message">Message</label>
        <textarea id="contact-message" class="form-control" rows="4" required></textarea>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Send Message</button>
    </form>
    <div id="contact-success" style="display:none;margin-top:18px;" class="alert alert-success">Message sent!</div>
  </div>
</div>

<script>
// Tab functionality
try {
  document.querySelectorAll('.tab-nav li').forEach(tab => {
    tab.addEventListener('click', function() {
      const tabId = this.querySelector('a').getAttribute('href').substring(1);
      document.querySelectorAll('.tab-nav li').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
      this.classList.add('active');
      const tabContent = document.getElementById(tabId);
      if (tabContent) {
        tabContent.classList.add('active');
      } else {
        console.warn('Tab content not found:', tabId);
      }
    });
  });
} catch (e) { console.error('Tab navigation error:', e); }
// Donation widget functionality
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
  const user = <?= json_encode($user) ?>;
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

// Copy profile link
document.getElementById('copy-profile-link').onclick = function() {
  navigator.clipboard.writeText("<?= $profileUrl ?>").then(() => {
    this.textContent = "Copied!";
    setTimeout(() => { this.textContent = "Copy Profile Link"; }, 1500);
  });
};

// Contact modal logic
const contactBtn = document.getElementById('contact-me-btn');
const contactModal = document.getElementById('contact-modal');
const closeContactModal = document.getElementById('close-contact-modal');
if (contactBtn && contactModal && closeContactModal) {
  contactBtn.onclick = () => { contactModal.style.display = 'flex'; };
  closeContactModal.onclick = () => { contactModal.style.display = 'none'; };
  contactModal.onclick = (e) => { if (e.target === contactModal) contactModal.style.display = 'none'; };
  document.getElementById('contact-form').onsubmit = function(e) {
    e.preventDefault();
    // Simulate sending (AJAX/email integration can be added)
    document.getElementById('contact-success').style.display = 'block';
    setTimeout(() => { contactModal.style.display = 'none'; document.getElementById('contact-success').style.display = 'none'; }, 1800);
    this.reset();
  };
}
</script>
<?php include 'includes/footer.php'; ?>
<style>
/* Remove blue-btn and hover effects for SVG-only icons */
.blue-btn,
.download-btn,
.delete-btn {
  background: transparent !important;
  color: inherit !important;
  border: none;
  box-shadow: none;
  transition: none;
  padding: 0 !important;
}
.download-btn .download-icon,
.delete-btn .delete-icon {
  transition: none;
}
.download-btn:hover .download-icon,
.delete-btn:hover .delete-icon {
  animation: none;
}
</style>