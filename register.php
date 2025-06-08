<?php
require_once 'includes/header.php';

$errors = [];
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'includes/auth_functions.php';

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (empty($username)) $errors[] = 'Username is required';
    if (empty($email)) $errors[] = 'Email is required';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address';
    if (empty($password)) $errors[] = 'Password is required';
    if ($password !== $confirm) $errors[] = 'Passwords do not match';

    if (empty($errors)) {
        if (getUserByUsername($username)) {
            $errors[] = 'Username already exists';
        } elseif (getUserByEmail($email)) {
            $errors[] = 'Email already registered';
        } else {
            if (registerUser($username, $email, $password)) {
                header('Location: login.php?msg=registered');
                exit;
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
        }
    }
}
?>

<div class="auth-container" style="display:flex;justify-content:center;align-items:flex-start;min-height:60vh;margin-top:40px;">
    <div class="card auth-card" style="max-width:350px;width:100%;margin:0 auto;padding:0;box-shadow:0 2px 16px rgba(108,92,231,0.10);border-radius:18px;">
        <div class="card-header" style="background:linear-gradient(135deg,#6c5ce7 0%,#a29bfe 100%);border-radius:18px 18px 0 0;padding:32px 24px;text-align:center;">
            <div style="margin-bottom:12px;">
                <i class="fas fa-user-plus" style="font-size:2.5rem;color:#fff;"></i>
            </div>
            <h2 style="font-size:1.5rem;color:#fff;font-weight:700;margin-bottom:0;">Create Your Account</h2>
            <div style="font-size:1rem;color:#e0e0e0;margin-top:6px;">Join CodeJar for free</div>
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

            <form method="POST" action="register.php" autocomplete="on">
                <div class="form-group" style="margin-bottom:18px;">
                    <label for="username" style="font-weight:600;">Username</label>
                    <input type="text" id="username" name="username" class="form-control"
                        value="<?= htmlspecialchars($username) ?>" required autofocus placeholder="Choose a username">
                </div>
                <div class="form-group" style="margin-bottom:18px;">
                    <label for="email" style="font-weight:600;">Email</label>
                    <input type="email" id="email" name="email" class="form-control"
                        value="<?= htmlspecialchars($email) ?>" required placeholder="Enter your email">
                </div>
                <div class="form-group" style="margin-bottom:18px;">
                    <label for="password" style="font-weight:600;">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required placeholder="Create a password">
                </div>
                <div class="form-group" style="margin-bottom:18px;">
                    <label for="confirm_password" style="font-weight:600;">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required placeholder="Repeat password">
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="width:100%;font-size:1rem;padding:10px 0;border-radius:24px;">Register</button>
            </form>

            <div class="auth-footer" style="margin-top:18px;text-align:center;font-size:0.97rem;">
                <span>Already have an account? <a href="login.php" style="color:var(--primary);font-weight:600;">Sign in</a></span>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>