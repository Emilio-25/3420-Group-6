<?php
require_once __DIR__ . '/bootstrap.php';
requireRole('user');
$pageTitle = 'My Profile';
$userId = currentUserId();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $gameName = trim($_POST['riot_game_name'] ?? '');
    $tag = trim($_POST['riot_tag_line'] ?? '');

    try {
        $stmt = db()->prepare('UPDATE `User` SET Username=?, Email=?, RiotGameName=?, RiotTagLine=? WHERE UserID=?');
        $stmt->execute([$username, $email, $gameName ?: null, $tag ?: null, $userId]);
        $_SESSION['auth']['display'] = $username;
        $_SESSION['auth']['email'] = $email;
        $message = 'Profile updated successfully.';
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$user = oneRow('SELECT UserID, Username, Email, CreationDate, RiotGameName, RiotTagLine FROM `User` WHERE UserID=?', [$userId]);
require __DIR__ . '/../src/partials/header.php';
?>
<h1>My Profile</h1>
<p>This page demonstrates role-restricted UPDATE functionality: a player can update only their own account.</p>
<?php if (isset($message)): ?><div class="notice"><?= e($message) ?></div><?php endif; ?>
<?php if (isset($error)): ?><div class="notice error"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="panel">
    <div class="form-row">
        <label>Username<input name="username" value="<?= e($user['Username'] ?? '') ?>" required></label>
        <label>Email<input type="email" name="email" value="<?= e($user['Email'] ?? '') ?>" required></label>
    </div>
    <div class="form-row">
        <label>Riot game name<input name="riot_game_name" value="<?= e($user['RiotGameName'] ?? '') ?>"></label>
        <label>Riot tag line<input name="riot_tag_line" value="<?= e($user['RiotTagLine'] ?? '') ?>"></label>
    </div>
    <button type="submit">Save Profile</button>
</form>
<section class="panel compact-panel">
    <strong>Account created:</strong> <?= e($user['CreationDate'] ?? '') ?>
</section>
<?php require __DIR__ . '/../src/partials/footer.php'; ?>
