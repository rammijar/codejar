<?php
require_once 'includes/header.php';

$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'includes/auth_functions.php';

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username)) $errors[] = 'Username or email is required';
    if (empty($password)) $errors[] = 'Password is required';

    if (empty($errors)) {
        if (loginUser($username, $password)) {
            header('Location: profile.php?username=' . $_SESSION['username']);
            exit;
        } else {
            $errors[] = 'Invalid username/email or password';
        }
    }
}
?>

<div class="auth-container" style="display:flex;justify-content:center;align-items:flex-start;min-height:60vh;margin-top:40px;">
    <div class="card auth-card" style="max-width:350px;width:100%;margin:0 auto;padding:0;box-shadow:0 2px 16px rgba(108,92,231,0.10);border-radius:18px;">
        <div class="card-header" style="background:linear-gradient(135deg,#6c5ce7 0%,#a29bfe 100%);border-radius:18px 18px 0 0;padding:32px 24px;text-align:center;">
            <div style="margin-bottom:12px;">
                <i class="fas fa-sign-in-alt" style="font-size:2.5rem;color:#fff;"></i>
            </div>
            <h2 style="font-size:1.5rem;color:#fff;font-weight:700;margin-bottom:0;">Sign In</h2>
            <div style="font-size:1rem;color:#e0e0e0;margin-top:6px;">Welcome back to CodeJar</div>
        </div>
        <div class="card-body" style="padding:28px 24px 18px 24px;">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" style="font-size:0.97rem;">
                    <ul style="margin-bottom:0;">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" autocomplete="on">
                <div class="form-group" style="margin-bottom:18px;">
                    <label for="username" style="font-weight:600;">Username or Email</label>
                    <input type="text" id="username" name="username" class="form-control" 
                        value="<?= htmlspecialchars($username) ?>" required autofocus placeholder="Enter username or email">
                </div>
                <div class="form-group" style="margin-bottom:18px;">
                    <label for="password" style="font-weight:600;">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required placeholder="Enter password">
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="width:100%;font-size:1rem;padding:10px 0;border-radius:24px;">Login</button>
            </form>

            <div class="auth-footer" style="margin-top:18px;text-align:center;font-size:0.97rem;">
                <span>Don't have an account? <a href="register.php" style="color:var(--primary);font-weight:600;">Sign up</a></span>
                <br>
                <a href="forgot_password.php" style="color:#888;font-size:0.95em;">Forgot password?</a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>