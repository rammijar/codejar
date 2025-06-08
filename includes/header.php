<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/codejar');
}
require_once __DIR__ . '/auth_functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' | CodeJar' : 'CodeJar - Share Your Code' ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/animations.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container container">
            <a href="<?= BASE_URL ?>" class="nav-logo">CodeJar</a>
            <form method="get" action="<?= BASE_URL ?>/browse.php" style="display:inline-flex;align-items:center;gap:8px;margin-right:18px;">
                <input type="text" name="search" class="form-control" placeholder="Search code or user..." style="max-width:180px;height:36px;font-size:15px;border-radius:18px;padding:0 14px;">
                <button type="submit" class="btn btn-primary" style="padding:7px 18px;font-size:15px;border-radius:18px;">Search</button>
            </form>
            <ul class="nav-menu">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/profile.php?username=<?= htmlspecialchars($_SESSION['username'] ?? '') ?>" class="nav-link">My Profile</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/upload.php" class="nav-link">Upload</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/widgets.php" class="nav-link">Donation Widgets</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/settings.php" class="nav-link">Settings</a>
                    </li>
                    <?php if (isAdmin()): ?>
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/admin/dashboard.php" class="nav-link admin-link">
                                <i class="fas fa-user-shield"></i> Admin
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/logout.php" class="nav-link">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/register.php" class="nav-link">Register</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/login.php" class="nav-link">Login</a>
                    </li>
                <?php endif; ?>
                <!-- REMOVE dark mode toggle button -->
                <!-- <li class="nav-item">
                    <button class="theme-toggle" id="theme-toggle-btn" title="Toggle dark mode">
                        <i class="fas fa-moon"></i>
                    </button>
                </li> -->
            </ul>
        </div>
    </nav>

    <main class="container">

    <?php echo "<!-- HEADER LOADED -->"; ?>
    <!-- REMOVE dark mode toggle script -->
    <!--
    <script>
    (function() {
        const btn = document.getElementById('theme-toggle-btn');
        const icon = btn.querySelector('i');
        function setTheme(dark) {
            if (dark) {
                document.body.classList.add('dark-mode');
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                document.body.classList.remove('dark-mode');
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        }
        const darkPref = localStorage.getItem('theme') === 'dark' ||
            (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
        setTheme(darkPref);
        btn.onclick = function() {
            const isDark = document.body.classList.toggle('dark-mode');
            setTheme(isDark);
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        };
    })();
    </script>
    -->
