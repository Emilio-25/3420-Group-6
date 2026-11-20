<?php
require_once __DIR__ . '/bootstrap.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$pageTitle = 'Player Login';
$bodyClass = 'auth-page';
$identifier = trim((string)($_POST['identifier'] ?? ''));
$next = basename((string)($_GET['next'] ?? $_POST['next'] ?? 'index.php'));

if (!preg_match('/^[A-Za-z0-9_-]+\.php$/', $next)) {
    $next = 'index.php';
}

if (empty($_SESSION['player_login_csrf'])) {
    $_SESSION['player_login_csrf'] = bin2hex(random_bytes(32));
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = (string)($_POST['csrf_token'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if (!hash_equals($_SESSION['player_login_csrf'], $submittedToken)) {
        $error = 'Your login session expired. Refresh the page and try again.';
    } elseif ($identifier === '' || $password === '') {
        $error = 'Enter your username or email and password.';
    } else {
        try {
            $row = oneRow(
                'SELECT UserID, Username, Email, Password FROM `User` WHERE Username = ? OR Email = ? LIMIT 1',
                [$identifier, $identifier]
            );

            if ($row && password_verify($password, $row['Password'])) {
                loginUser([
                    'role' => 'user',
                    'id' => (int)$row['UserID'],
                    'display' => $row['Username'],
                    'email' => $row['Email'],
                ]);

                unset($_SESSION['player_login_csrf']);
                header('Location: ' . $next);
                exit;
            }

            $error = 'Invalid username/email or password.';
        } catch (Throwable $e) {
            error_log('[StatTrax player login] ' . $e->getMessage());
            $error = 'StatTrax could not reach the database. Please try again shortly.';
        }
    }
}

require __DIR__ . '/../src/partials/header.php';
?>
<section class="auth-shell">
    <div class="auth-copy">
        <span class="section-kicker">PLAYER ACCESS</span>
        <h1>Enter StatTrax</h1>
        <p>
            Sign in to view your League of Legends match history, generate performance
            reports, update your profile, and participate in the StatTrax community.
        </p>

        <div class="credential-card">
            <strong>Your player hub</strong>
            <span>Match history · analytics · community · profile</span>
        </div>

        <div class="auth-copy-actions">
            <span>New to StatTrax?</span>
            <a href="register.php">Create a player account →</a>
        </div>
    </div>

    <form method="post" class="panel auth-panel" autocomplete="on">
        <input type="hidden" name="next" value="<?= e($next) ?>">
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['player_login_csrf']) ?>">

        <div class="auth-panel-heading">
            <span class="section-kicker">SIGN IN</span>
            <h2>Player Login</h2>
        </div>

        <label for="identifier">Username or email
            <input
                id="identifier"
                name="identifier"
                value="<?= e($identifier) ?>"
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

        <button type="submit">Login</button>

        <div class="auth-form-links">
            <a href="register.php">Create account</a>
            <span>•</span>
            <a href="admin_login.php">Admin? Login here</a>
        </div>
    </form>
</section>
<?php require __DIR__ . '/../src/partials/footer.php'; ?>
