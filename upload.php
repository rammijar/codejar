<?php
require_once 'includes/header.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $file = $_FILES['code_file'];
    $posterPath = null;
    
    // Validation
    if (empty($title)) $errors[] = 'Title is required';
    if (empty($file['name'])) $errors[] = 'File is required';
    
    if (empty($errors)) {
        // Check file type and size
        $allowedTypes = ['application/zip', 'application/x-zip-compressed', 'application/octet-stream'];
        $maxSize = 10 * 1024 * 1024; // 10MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            $errors[] = 'Only ZIP files are allowed';
        } elseif ($file['size'] > $maxSize) {
            $errors[] = 'File size must be less than 10MB';
        } else {
            // Generate unique filename
            $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '.' . $fileExt;
            $filePath = CODE_UPLOAD_PATH . $fileName;
            
            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                // Handle poster upload
                if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
                    $posterTmp = $_FILES['poster']['tmp_name'];
                    $posterName = uniqid('poster_') . '_' . basename($_FILES['poster']['name']);
                    $posterDest = 'assets/uploads/posters/' . $posterName;
                    if (!is_dir('assets/uploads/posters')) {
                        mkdir('assets/uploads/posters', 0777, true);
                    }
                    if (move_uploaded_file($posterTmp, $posterDest)) {
                        $posterPath = $posterDest;
                    }
                }
                
                // Save to database
                global $pdo;
                $stmt = $pdo->prepare('INSERT INTO uploads (user_id, title, description, file_path, poster_path, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
                if ($stmt->execute([$_SESSION['user_id'], $title, $description, $fileName, $posterPath])) {
                    $success = true;
                } else {
                    $errors[] = 'Failed to save upload to database';
                    // Delete the uploaded file if DB insert failed
                    unlink($filePath);
                    if ($posterPath) {
                        unlink($posterPath);
                    }
                }
            } else {
                $errors[] = 'Failed to upload file';
            }
        }
    }
}
?>

<div class="upload-container" style="max-width: 540px; margin: 48px auto 32px auto;">
    <div class="card" style="box-shadow: var(--shadow-lg); border-radius: 18px;">
        <div class="card-header" style="background: #fafbff; border-bottom: 1px solid #ececec; border-radius: 18px 18px 0 0;">
            <h2 style="font-size: 2rem; color: var(--primary); font-weight: 700; margin-bottom: 0; letter-spacing: 0.5px;">
                <i class="fas fa-upload" style="margin-right: 10px; color: var(--secondary);"></i>
                Upload Your Code
            </h2>
        </div>
        <div class="card-body" style="padding: 32px;">
            <?php if ($success): ?>
                <div class="alert alert-success" style="font-size: 1.1rem;">
                    Your code has been uploaded successfully!
                    <a href="profile.php?username=<?= $_SESSION['username'] ?>" class="btn btn-sm btn-success" style="margin-left: 10px;">View Profile</a>
                </div>
            <?php elseif (!empty($errors)): ?>
                <div class="alert alert-danger" style="font-size: 1.1rem;">
                    <ul style="margin-bottom:0;">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <form method="POST" action="upload.php" enctype="multipart/form-data" id="upload-form">
                <div class="form-group" style="margin-bottom: 18px;">
                    <label for="title" style="font-weight: 600;">Title</label>
                    <input type="text" id="title" name="title" class="form-control" value="<?= isset($title) ? htmlspecialchars($title) : '' ?>" required>
                </div>
                <div class="form-group" style="margin-bottom: 18px;">
                    <label for="description" style="font-weight: 600;">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="4" required><?= isset($description) ? htmlspecialchars($description) : '' ?></textarea>
                </div>
                <div class="form-group" style="margin-bottom: 18px;">
                    <label for="code_file" style="font-weight: 600;">Code File <span style="color: #888; font-weight: 400;">(ZIP only, max 10MB)</span></label>
                    <div id="drop-zone" style="border: 2px dashed var(--primary); border-radius: 8px; padding: 18px 0; text-align: center; background: #fafbff; cursor: pointer; transition: border-color 0.2s;">
                        <div id="drop-zone-content">
                            <!-- Modern duffel icon (SVG) -->
                            <svg width="36" height="36" viewBox="0 0 48 48" style="margin-bottom: 8px;" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="6" y="16" width="36" height="20" rx="5" fill="#a29bfe" stroke="#6c5ce7" stroke-width="2"/>
                                <rect x="14" y="8" width="20" height="8" rx="4" fill="#6c5ce7"/>
                                <rect x="18" y="12" width="12" height="4" rx="2" fill="#fff"/>
                                <rect x="10" y="36" width="28" height="4" rx="2" fill="#6c5ce7"/>
                                <rect x="20" y="20" width="8" height="8" rx="2" fill="#fff"/>
                            </svg>
                            <div style="font-size: 1rem; color: #6c5ce7; font-weight: 500;">Drag &amp; drop your ZIP file here<br>or <span style="color:var(--accent);text-decoration:underline;cursor:pointer;">choose file</span></div>
                            <div id="file-name" style="margin-top: 6px; color: #555; font-size: 0.97rem;"></div>
                        </div>
                        <input type="file" id="code_file" name="code_file" class="form-control" accept=".zip" required style="display:none;">
                    </div>
                    <small style="color: #888;">Only ZIP files allowed. Max size: 10MB.</small>
                </div>
                <div class="form-group" style="margin-bottom: 22px;">
                    <label for="poster" style="font-weight: 600;">Poster/Thumbnail <span style="color: #888; font-weight: 400;">(optional, jpg/png, max 2MB)</span></label>
                    <div id="poster-drop-zone" style="border: 2px dashed #a29bfe; border-radius: 8px; padding: 14px 0; text-align: center; background: #fafbff; cursor: pointer; transition: border-color 0.2s; max-width: 220px; margin-bottom: 6px;">
                        <div id="poster-drop-zone-content">
                            <!-- Small image icon SVG -->
                            <svg width="28" height="28" viewBox="0 0 24 24" style="margin-bottom: 4px;" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="3" y="5" width="18" height="14" rx="3" fill="#a29bfe" stroke="#6c5ce7" stroke-width="1.5"/>
                                <circle cx="8" cy="10" r="2" fill="#fff"/>
                                <path d="M3 17l4.5-5.5a2 2 0 0 1 3 0L17 17" stroke="#fff" stroke-width="1.5" fill="none"/>
                            </svg>
                            <div style="font-size: 0.97rem; color: #6c5ce7; font-weight: 500;">Drag &amp; drop image<br>or <span style="color:var(--accent);text-decoration:underline;cursor:pointer;">choose</span></div>
                            <div id="poster-file-name" style="margin-top: 4px; color: #555; font-size: 0.95rem;"></div>
                            <div id="poster-preview" style="margin-top: 6px;"></div>
                        </div>
                        <input type="file" name="poster" id="poster" accept="image/jpeg,image/png" class="form-control" style="display:none;">
                    </div>
                    <small style="color: #888;">JPG/PNG only. Max size: 2MB.</small>
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="width:100%;font-size:1rem;padding:10px 0;">Upload Code</button>
            </form>
        </div>
    </div>
</div>
<script>
// Modern drag & drop for code file
const dropZone = document.getElementById('drop-zone');
const codeFileInput = document.getElementById('code_file');
const fileNameDiv = document.getElementById('file-name');
const dropZoneContent = document.getElementById('drop-zone-content');

dropZone.addEventListener('click', function(e) {
    codeFileInput.click();
});
dropZone.addEventListener('dragover', function(e) {
    e.preventDefault();
    dropZone.style.borderColor = '#5649d1';
});
dropZone.addEventListener('dragleave', function(e) {
    e.preventDefault();
    dropZone.style.borderColor = 'var(--primary)';
});
dropZone.addEventListener('drop', function(e) {
    e.preventDefault();
    dropZone.style.borderColor = 'var(--primary)';
    if (e.dataTransfer.files.length) {
        codeFileInput.files = e.dataTransfer.files;
        showFileName();
    }
});
codeFileInput.addEventListener('change', showFileName);

function showFileName() {
    if (codeFileInput.files.length > 0) {
        const file = codeFileInput.files[0];
        if (file.size > 10 * 1024 * 1024) {
            fileNameDiv.innerHTML = '<span class="text-danger">File size exceeds 10MB limit.</span>';
            codeFileInput.value = '';
            return;
        }
        if (!file.name.endsWith('.zip')) {
            fileNameDiv.innerHTML = '<span class="text-danger">Only ZIP files are allowed.</span>';
            codeFileInput.value = '';
            return;
        }
        fileNameDiv.innerHTML = `<i class="fas fa-file-archive" style="color:var(--primary);margin-right:6px;"></i> ${file.name} <span style="color:#888;">(${(file.size / (1024 * 1024)).toFixed(2)} MB)</span>`;
    } else {
        fileNameDiv.innerHTML = '';
    }
}

// Modern drag & drop for poster image
const posterDropZone = document.getElementById('poster-drop-zone');
const posterInput = document.getElementById('poster');
const posterFileNameDiv = document.getElementById('poster-file-name');
const posterPreviewDiv = document.getElementById('poster-preview');
const posterDropZoneContent = document.getElementById('poster-drop-zone-content');

posterDropZone.addEventListener('click', function(e) {
    posterInput.click();
});
posterDropZone.addEventListener('dragover', function(e) {
    e.preventDefault();
    posterDropZone.style.borderColor = '#5649d1';
});
posterDropZone.addEventListener('dragleave', function(e) {
    e.preventDefault();
    posterDropZone.style.borderColor = '#a29bfe';
});
posterDropZone.addEventListener('drop', function(e) {
    e.preventDefault();
    posterDropZone.style.borderColor = '#a29bfe';
    if (e.dataTransfer.files.length) {
        posterInput.files = e.dataTransfer.files;
        showPosterFile();
    }
});
posterInput.addEventListener('change', showPosterFile);

function showPosterFile() {
    if (posterInput.files.length > 0) {
        const file = posterInput.files[0];
        if (file.size > 2 * 1024 * 1024) {
            posterFileNameDiv.innerHTML = '<span class="text-danger">Image size exceeds 2MB limit.</span>';
            posterPreviewDiv.innerHTML = '';
            posterInput.value = '';
            return;
        }
        if (!file.type.match('image/jpeg') && !file.type.match('image/png')) {
            posterFileNameDiv.innerHTML = '<span class="text-danger">Only JPG and PNG images are allowed.</span>';
            posterPreviewDiv.innerHTML = '';
            posterInput.value = '';
            return;
        }
        posterFileNameDiv.innerHTML = `<i class="fas fa-image" style="color:var(--primary);margin-right:6px;"></i> ${file.name} <span style="color:#888;">(${(file.size / (1024 * 1024)).toFixed(2)} MB)</span>`;
        // Show image preview
        const reader = new FileReader();
        reader.onload = function(e) {
            posterPreviewDiv.innerHTML = `<img src="${e.target.result}" alt="Poster Preview" style="max-width:60px;max-height:60px;border-radius:6px;margin-top:4px;border:1px solid #ececec;">`;
        };
        reader.readAsDataURL(file);
    } else {
        posterFileNameDiv.innerHTML = '';
        posterPreviewDiv.innerHTML = '';
    }
}
</script>
<?php require_once 'includes/footer.php'; ?>