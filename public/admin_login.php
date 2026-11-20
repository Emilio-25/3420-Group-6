<?php
require_once __DIR__ . '/bootstrap.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$pageTitle = 'Administrator Login';
$bodyClass = 'auth-page';
$email = trim((string)($_POST['email'] ?? ''));
$next = basename((string)($_GET['next'] ?? $_POST['next'] ?? 'index.php'));

if (!preg_match('/^[A-Za-z0-9_-]+\.php$/', $next)) {
    $next = 'index.php';
}

if (empty($_SESSION['admin_login_csrf'])) {
    $_SESSION['admin_login_csrf'] = bin2hex(random_bytes(32));
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = (string)($_POST['csrf_token'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if (!hash_equals($_SESSION['admin_login_csrf'], $submittedToken)) {
        $error = 'Your login session expired. Refresh the page and try again.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Enter your administrator email and password.';
    } else {
        try {
            $row = oneRow(
                'SELECT AdminID, Email, Password, AdminLevel FROM `Admin` WHERE Email = ? LIMIT 1',
                [$email]
            );

            if ($row && password_verify($password, $row['Password'])) {
                loginUser([
                    'role' => 'admin',
                    'id' => (int)$row['AdminID'],
                    'display' => $row['Email'],
                    'level' => $row['AdminLevel'],
                ]);

                unset($_SESSION['admin_login_csrf']);
                header('Location: ' . $next);
                exit;
            }

            $error = 'Invalid administrator credentials.';
        } catch (Throwable $e) {
            error_log('[StatTrax admin login] ' . $e->getMessage());
            $error = 'StatTrax could not reach the database. Please try again shortly.';
        }
    }
}

require __DIR__ . '/../src/partials/header.php';
?>
<section class="auth-shell admin-auth-shell">
    <div class="auth-copy">
        <span class="section-kicker admin-kicker">ADMINISTRATOR ACCESS</span>
        <h1>Control Room</h1>
        <p>
            Administrator access is reserved for StatTrax moderation and database
            demonstration tools.
        </p>

        <div class="credential-card admin-access-card">
            <strong>Admin tools</strong>
            <span>Report queue · comments · trigger audit · moderation</span>
        </div>

        <div class="auth-copy-actions">
            <span>Not an administrator?</span>
            <a href="login.php">Return to player login →</a>
        </div>
    </div>

    <form method="post" class="panel auth-panel admin-auth-panel" autocomplete="on">
        <input type="hidden" name="next" value="<?= e($next) ?>">
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['admin_login_csrf']) ?>">

        <div class="auth-panel-heading">
            <span class="section-kicker admin-kicker">RESTRICTED</span>
            <h2>Admin Login</h2>
        </div>

        <label for="email">Administrator email
            <input
                id="email"
                type="email"
                name="email"
                value="<?= e($email) ?>"
                autocomplete="username"
                autocapitalize="none"
                spellcheck="false"
                required
            >
        </label>

        <label for="password">Password
            <input
                id="password"
                type="password"
                name="password"
                autocomplete="current-password"
                required
            >
        </label>

        <?php if ($error): ?>
            <div class="notice error" role="alert"><?= e($error) ?></div>
        <?php endif; ?>

        <button type="submit" class="admin-login-button">Administrator Login</button>

        <div class="auth-form-links">
            <a href="login.php">← Player login</a>
        </div>
    </form>
</section>
<?php require __DIR__ . '/../src/partials/footer.php'; ?>
