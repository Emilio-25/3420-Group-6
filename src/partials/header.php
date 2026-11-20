<?php
$pageTitle = $pageTitle ?? 'StatTrax';
$auth = currentUser();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> - StatTrax</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="<?= e($bodyClass ?? '') ?>">
<header class="site-header">
    <div class="wrap header-inner">
        <a class="brand" href="index.php">StatTrax</a>
        <nav>
            <a href="matches.php">Matches</a>
            <a href="stats.php">Stats</a>
            <a href="community.php">Community</a>
            <?php if (currentRole() === 'user'): ?>
                <a href="profile.php">Profile</a>
            <?php endif; ?>
            <?php if (currentRole() === 'admin'): ?>
                <a href="comments.php">Comments</a>
                <a href="reports.php">Reports</a>
                <a href="trigger_demo.php">Trigger Demo</a>
            <?php endif; ?>
        </nav>
        <div class="account-zone">
            <?php if ($auth): ?>
                <span class="role-chip <?= e($auth['role']) ?>"><?= e(strtoupper($auth['role'])) ?></span>
                <span class="account-name"><?= e($auth['display']) ?></span>
                <a class="account-link" href="logout.php">Logout</a>
            <?php else: ?>
                <a class="account-link" href="login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<main class="wrap">
