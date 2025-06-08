<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>



<?php include 'includes/header.php'; ?>

<main class="container">
  <section class="hero animated-gradient">
    <div class="container">
      <h1 class="hero-title animate-on-scroll">Welcome to CodeJar</h1>
      <p class="hero-subtitle animate-on-scroll">Share your code, get donations, and grow your developer profile</p>
      <div class="cta-buttons animate-on-scroll" style="animation-delay: 0.6s">
        <a href="register.php" class="btn btn-primary btn-pulse">Get Started</a>
        <a href="#features" class="btn btn-secondary">Learn More</a>
      </div>
    </div>
  </section>

  <section id="features" class="section-padding">
    <h2 class="section-title" style="font-size:2rem;color:var(--primary);margin-bottom:32px;text-align:center;letter-spacing:0.5px;">Why Choose CodeJar?</h2>
    <div class="features-grid" style="display:flex;gap:32px;justify-content:center;align-items:stretch;flex-wrap:wrap;">
      <div class="card" style="flex:1 1 260px;max-width:340px;min-width:220px;border-radius:18px;box-shadow:0 2px 16px rgba(108,92,231,0.08);padding:0;overflow:hidden;transition:box-shadow 0.2s;">
        <div class="card-body" style="padding:32px 28px 28px 28px;text-align:center;">
          <div style="background:linear-gradient(135deg,#6c5ce7 60%,#a29bfe 100%);width:60px;height:60px;display:flex;align-items:center;justify-content:center;border-radius:16px;margin:0 auto 18px auto;">
            <i class="fas fa-code" style="font-size:2rem;color:#fff;"></i>
          </div>
          <h3 style="font-size:1.25rem;font-weight:700;margin-bottom:10px;color:var(--primary);">Share Your Code</h3>
          <p style="color:#555;font-size:1rem;line-height:1.6;">Upload and share your projects with the community in simple ZIP files.</p>
        </div>
      </div>
      <div class="card" style="flex:1 1 260px;max-width:340px;min-width:220px;border-radius:18px;box-shadow:0 2px 16px rgba(108,92,231,0.08);padding:0;overflow:hidden;transition:box-shadow 0.2s;">
        <div class="card-body" style="padding:32px 28px 28px 28px;text-align:center;">
          <div style="background:linear-gradient(135deg,#fd79a8 60%,#a29bfe 100%);width:60px;height:60px;display:flex;align-items:center;justify-content:center;border-radius:16px;margin:0 auto 18px auto;">
            <i class="fas fa-hand-holding-usd" style="font-size:2rem;color:#fff;"></i>
          </div>
          <h3 style="font-size:1.25rem;font-weight:700;margin-bottom:10px;color:#fd79a8;">Get Donations</h3>
          <p style="color:#555;font-size:1rem;line-height:1.6;">Receive support from users who appreciate your work through Khalti or eSewa.</p>
        </div>
      </div>
      <div class="card" style="flex:1 1 260px;max-width:340px;min-width:220px;border-radius:18px;box-shadow:0 2px 16px rgba(108,92,231,0.08);padding:0;overflow:hidden;transition:box-shadow 0.2s;">
        <div class="card-body" style="padding:32px 28px 28px 28px;text-align:center;">
          <div style="background:linear-gradient(135deg,#a29bfe 60%,#6c5ce7 100%);width:60px;height:60px;display:flex;align-items:center;justify-content:center;border-radius:16px;margin:0 auto 18px auto;">
            <i class="fas fa-users" style="font-size:2rem;color:#fff;"></i>
          </div>
          <h3 style="font-size:1.25rem;font-weight:700;margin-bottom:10px;color:#6c5ce7;">Build Your Profile</h3>
          <p style="color:#555;font-size:1rem;line-height:1.6;">Create a public profile to showcase your skills and projects to potential employers.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="section-padding bg-light">
    <h2 class="section-title slide-in-right">Recent Uploads</h2>
    <div class="upload-grid">
      <?php
      // PHP code to fetch and display recent uploads
      $recentUploads = getRecentUploads(6); // Assume this function exists
      foreach ($recentUploads as $upload):
        $isOwner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $upload['user_id'];
      ?>
        <div class="card upload-card" style="position:relative;padding:0;overflow:hidden;">
          <a href="view_upload.php?id=<?= $upload['id'] ?>" style="text-decoration:none;color:inherit;display:block;">
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
              <div class="upload-file-title" style="color:#888;font-size:13px;margin-top:4px;">
                <strong>File:</strong> <?= htmlspecialchars(basename($upload['file_path'])) ?>
              </div>
            </div>
          </a>
          <div class="upload-meta" style="background: #f5f6fa; border-top: 1px solid #ececec; padding: 10px 16px; display: flex; align-items: center; gap: 12px;">
            <span class="upload-author">By <?= htmlspecialchars($upload['username']) ?></span>
            <span class="upload-date"><?= formatDate($upload['created_at']) ?></span>
            <a href="download.php?id=<?= $upload['id'] ?>" class="btn btn-sm btn-primary" onclick="event.stopPropagation();" style="background:transparent;border:none;">
              <!-- Download SVG Icon Only -->
              <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="26" height="26" x="0" y="0" viewBox="0 0 48 48" style="vertical-align:middle;"><g><path d="M24 0a24 24 0 1 0 24 24A24 24 0 0 0 24 0zm7.873 39.46H16.127a2 2 0 1 1 0-4h15.746a2 2 0 0 1 0 4zm1.414-15.044L25.408 32.3a2 2 0 0 1-2.828 0l-7.867-7.866a2 2 0 0 1 2.828-2.834L22 26.059V8.365a2 2 0 0 1 4 0v17.682l4.459-4.459a2 2 0 0 1 2.828 2.828z" fill="#000000" opacity="1"></path></g></svg>
            </a>
            <?php if ($isOwner): ?>
              <form method="post" action="delete_upload.php" style="display:inline;" onClick="event.stopPropagation();">
                <input type="hidden" name="upload_id" value="<?= $upload['id'] ?>">
                <button type="submit" class="btn btn-sm btn-danger" style="background:transparent;border:none;" onclick="return confirm('Are you sure you want to delete this upload?');">
                  <!-- Trash SVG Icon Only -->
                  <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="26" height="26" x="0" y="0" viewBox="0 0 512 512" style="vertical-align:middle;"><g><path fill="#0c0c0c" fill-rule="evenodd" d="M256 0C114.842 0 0 114.842 0 256s114.839 256 256 256 256-114.841 256-256S397.16 0 256 0zm-49.921 114.329a8.325 8.325 0 0 1 8.32-8.324h83.2a8.341 8.341 0 0 1 8.322 8.334v20.577h-99.842zm136.8 279.759A12.745 12.745 0 0 1 330.067 406h-149.1a12.873 12.873 0 0 1-12.807-11.963L155.407 207.4h201.081l-13.614 186.688zM376.02 190.5H135.98v-19.339a19.357 19.357 0 0 1 19.339-19.341l201.359-.006a19.365 19.365 0 0 1 19.338 19.348v19.336zM217.35 361.508V243a8.449 8.449 0 0 1 16.9.006v118.502a8.451 8.451 0 1 1-16.9 0zm60.292 0V243a8.451 8.451 0 0 1 16.9.006v118.507a8.452 8.452 0 1 1-16.9 0z" opacity="1"></path></g></svg>
                </button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-4">
      <a href="browse.php"
         class="btn"
         style="
           display: inline-flex;
           align-items: center;
           gap: 8px;
           background: linear-gradient(90deg, #6c5ce7 0%, #a29bfe 100%);
           color: #fff;
           font-weight: 600;
           font-size: 1rem;
           border-radius: 24px;
           padding: 10px 22px;
           box-shadow: 0 2px 8px rgba(108,92,231,0.10);
           transition: background 0.18s, box-shadow 0.18s, transform 0.13s;
           border: none;
           text-decoration: none;
           margin-top: 12px;
           line-height: 1.2;
         "
         onmouseover="this.style.background='linear-gradient(90deg,#a29bfe 0%,#6c5ce7 100%)';this.style.transform='translateY(-1px)';this.style.boxShadow='0 4px 12px rgba(108,92,231,0.16)';"
         onmouseout="this.style.background='linear-gradient(90deg,#6c5ce7 0%,#a29bfe 100%)';this.style.transform='none';this.style.boxShadow='0 2px 8px rgba(108,92,231,0.10)';"
      >
        <i class="fas fa-search" style="font-size:1.1em;"></i>
        <span style="display:inline-block;">Browse<br>All Uploads</span>
      </a>
    </div>
  </section>
</main>

<?php include 'includes/footer.php'; ?>

<script>
// Remove or disable any file preview popup/modal on homepage
// If you have JS that triggers a modal or popup for uploads, disable it here
// Example: document.querySelectorAll('.upload-card').forEach(card => { card.onclick = null; });
</script>