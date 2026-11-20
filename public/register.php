<?php
require_once __DIR__ . '/bootstrap.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$pageTitle = 'Create Account';
$bodyClass = 'auth-page';

$username = trim((string)($_POST['username'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$error = null;

if (empty($_SESSION['register_csrf'])) {
    $_SESSION['register_csrf'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = (string)($_POST['csrf_token'] ?? '');
    $password = (string)($_POST['password'] ?? '');
    $confirmPassword = (string)($_POST['confirm_password'] ?? '');

    if (!hash_equals($_SESSION['register_csrf'], $submittedToken)) {
        $error = 'Your registration session expired. Refresh the page and try again.';
    } elseif (!preg_match('/^[A-Za-z0-9_]{3,50}$/', $username)) {
        $error = 'Username must be 3–50 characters and use only letters, numbers, or underscores.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 120) {
        $error = 'Enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $existing = oneRow(
                'SELECT UserID FROM `User` WHERE Username = ? OR Email = ? LIMIT 1',
                [$username, $email]
            );

            if ($existing) {
                $error = 'That username or email is already registered.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                if ($hash === false) {
                    throw new RuntimeException('Password hashing failed.');
                }

                $stmt = db()->prepare(
                    'INSERT INTO `User` (Username, Password, Email) VALUES (?, ?, ?)'
                );
                $stmt->execute([$username, $hash, $email]);

                $userId = (int)db()->lastInsertId();

                loginUser([
                    'role' => 'user',
                    'id' => $userId,
                    'display' => $username,
                    'email' => $email,
                ]);

                unset($_SESSION['register_csrf']);
                header('Location: index.php');
                exit;
            }
        } catch (PDOException $e) {
            error_log('[StatTrax registration] ' . $e->getMessage());

            if ((int)($e->errorInfo[1] ?? 0) === 1062) {
                $error = 'That username or email is already registered.';
            } else {
                $error = 'StatTrax could not create your account right now. Please try again shortly.';
            }
        } catch (Throwable $e) {
            error_log('[StatTrax registration] ' . $e->getMessage());
            $error = 'StatTrax could not create your account right now. Please try again shortly.';
        }
    }
}

require __DIR__ . '/../src/partials/header.php';
?>
<section class="auth-shell">
    <div class="auth-copy">
        <span class="section-kicker">NEW PLAYER</span>
        <h1>Join StatTrax</h1>
        <p>
            Create a player account to track match performance, generate analytics,
            manage your profile, and join the community feed.
        </p>

        <div class="credential-card">
            <strong>One account. Your stats.</strong>
            <span>Player profile · match history · reports · community</span>
        </div>

        <div class="auth-copy-actions">
            <span>Already have an account?</span>
            <a href="login.php">Back to player login →</a>
        </div>
    </div>

    <form method="post" class="panel auth-panel" autocomplete="on">
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['register_csrf']) ?>">

        <div class="auth-panel-heading">
            <span class="section-kicker">CREATE ACCOUNT</span>
            <h2>Player Registration</h2>
        </div>

        <label for="username">Username
            <input
                id="username"
                name="username"
                value="<?= e($username) ?>"
                minlength="3"
                maxlength="50"
                pattern="[A-Za-z0-9_]+"
                autocomplete="username"
                autocapitalize="none"
                spellcheck="false"
                required
            >
        </label>

        <label for="email">Email
            <input
                id="email"
                type="email"
                name="email"
                value="<?= e($email) ?>"
                maxlength="120"
                autocomplete="email"
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
                minlength="8"
                autocomplete="new-password"
                required
            >
        </label>

        <label for="confirm_password">Confirm password
            <input
                id="confirm_password"
                type="password"
                name="confirm_password"
                minlength="8"
                autocomplete="new-password"
                required
            >
        </label>

        <?php if ($error): ?>
            <div class="notice error" role="alert"><?= e($error) ?></div>
        <?php endif; ?>

        <button type="submit">Create Player Account</button>

        <div class="auth-form-links">
            <a href="login.php">Already registered? Login</a>
        </div>
    </form>
</section>
<?php require __DIR__ . '/../src/partials/footer.php'; ?>
