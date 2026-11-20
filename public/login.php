<?php
require_once __DIR__ . '/bootstrap.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$pageTitle = 'Login';
$role = $_POST['role'] ?? 'user';
$identifier = trim($_POST['identifier'] ?? '');
$next = basename($_GET['next'] ?? $_POST['next'] ?? 'index.php');
if (!preg_match('/^[A-Za-z0-9_-]+\.php$/', $next)) {
    $next = 'index.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($role === 'admin') {
            $row = oneRow('SELECT AdminID, Email, Password, AdminLevel FROM `Admin` WHERE Email = ?', [$identifier]);
            if ($row && password_verify($_POST['password'] ?? '', $row['Password'])) {
                loginUser([
                    'role' => 'admin',
                    'id' => (int)$row['AdminID'],
                    'display' => $row['Email'],
                    'level' => $row['AdminLevel'],
                ]);
                header('Location: ' . $next);
                exit;
            }
        } else {
            $row = oneRow('SELECT UserID, Username, Email, Password FROM `User` WHERE Username = ? OR Email = ?', [$identifier, $identifier]);
            if ($row && password_verify($_POST['password'] ?? '', $row['Password'])) {
                loginUser([
                    'role' => 'user',
                    'id' => (int)$row['UserID'],
                    'display' => $row['Username'],
                    'email' => $row['Email'],
                ]);
                header('Location: ' . $next);
                exit;
            }
        }
        $error = 'Invalid login credentials.';
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$bodyClass = 'auth-page';
require __DIR__ . '/../src/partials/header.php';
?>
<section class="auth-shell">
    <div class="auth-copy">
        <span class="section-kicker">SECURE ACCESS</span>
        <h1>Enter StatTrax</h1>
        <p>Choose a role to demonstrate the two required GUI user groups. Users get player/community tools; admins get moderation and audit controls.</p>
        <div class="credential-card">
            <strong>Demo user</strong>
            <span>Joshua / player123</span>
        </div>
        <div class="credential-card">
            <strong>Demo admin</strong>
            <span>admin@stattrax.local / admin123</span>
        </div>
    </div>
    <form method="post" class="panel auth-panel">
        <input type="hidden" name="next" value="<?= e($next) ?>">
        <label>Role
            <select name="role">
                <option value="user" <?= $role === 'user' ? 'selected' : '' ?>>Player / User</option>
                <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Administrator</option>
            </select>
        </label>
        <label>Username or email
            <input name="identifier" value="<?= e($identifier) ?>" autocomplete="username" required>
        </label>
        <label>Password
            <input type="password" name="password" autocomplete="current-password" required>
        </label>
        <?php if (isset($error)): ?><div class="notice error"><?= e($error) ?></div><?php endif; ?>
        <button type="submit">Login</button>
    </form>
</section>
<?php require __DIR__ . '/../src/partials/footer.php'; ?>
