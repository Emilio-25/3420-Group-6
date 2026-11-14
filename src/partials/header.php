<?php
$pageTitle = $pageTitle ?? 'StatTrax';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> - StatTrax</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="site-header">
    <div class="wrap header-inner">
        <a class="brand" href="index.php">StatTrax</a>
        <nav>
            <a href="matches.php">Matches</a>
            <a href="stats.php">Stats</a>
            <a href="community.php">Community</a>
            <a href="comments.php">Comments</a>
            <a href="reports.php">Reports</a>
            <a href="trigger_demo.php">Trigger Demo</a>
        </nav>
    </div>
</header>
<main class="wrap">
