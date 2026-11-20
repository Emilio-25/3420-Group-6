<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = 'Dashboard';
$bodyClass = 'home-page';
$auth = currentUser();
require __DIR__ . '/../src/partials/header.php';
?>

<section class="game-hero">
    <div class="hero-grid-glow" aria-hidden="true"></div>
    <div class="hero-copy">
        <div class="eyebrow"><span class="status-dot"></span> PHASE 5 GUI ONLINE</div>
        <h1><span>STAT</span>TRAX</h1>
        <p class="hero-tagline">Track the match. Read the stats. Control the data.</p>
        <p class="hero-description">
            A League of Legends stat-tracking and community database application powered by PHP and MySQL.
            The GUI now supports player and administrator roles, CRUD operations, database programming, and exportable reports.
        </p>
        <div class="hero-actions">
            <?php if (!$auth): ?>
                <a class="game-btn primary" href="login.php">Login to StatTrax</a>
            <?php else: ?>
                <a class="game-btn primary" href="matches.php">View Match History</a>
                <a class="game-btn secondary" href="stats.php">Player Analytics</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="hero-console" aria-label="Phase 5 system status">
        <div class="console-top">
            <span>STATTRAX // PHASE 05</span>
            <span class="console-live">LIVE</span>
        </div>
        <div class="console-screen">
            <div class="console-row"><span>USER GROUPS</span><strong>02</strong></div>
            <div class="console-row"><span>CRUD</span><strong>READY</strong></div>
            <div class="console-row"><span>REPORT EXPORT</span><strong>CSV/PDF</strong></div>
            <div class="console-row"><span>STACK</span><strong>LAMP</strong></div>
        </div>
        <div class="console-footer">AUTH STATUS: <span><?= $auth ? e(strtoupper($auth['role'])) : 'GUEST' ?></span></div>
    </div>
</section>

<section class="quick-stats" aria-label="Phase 5 requirement summary">
    <div class="quick-stat"><span class="quick-label">ACCESS</span><strong>2</strong><span>User Groups</span></div>
    <div class="quick-stat"><span class="quick-label">DATA OPS</span><strong>CRUD</strong><span>Create · Read · Update · Delete</span></div>
    <div class="quick-stat"><span class="quick-label">REPORTS</span><strong>CSV</strong><span>Export + Print/PDF</span></div>
    <div class="quick-stat"><span class="quick-label">DATABASE</span><strong>10</strong><span>Views · Procedures · Triggers</span></div>
</section>

<section class="mode-section">
    <div class="section-heading">
        <div><span class="section-kicker">SELECT MODULE</span><h2><?= $auth ? 'Your available dashboards' : 'Login unlocks role-based dashboards' ?></h2></div>
        <span class="section-line"></span>
    </div>

    <div class="game-card-grid">
        <a class="game-card match-card" href="<?= $auth ? 'matches.php' : 'login.php' ?>">
            <div class="card-number">01</div><div class="card-icon">K/D/A</div>
            <div class="card-content"><span class="card-category">PLAYER DATA</span><h3>Match History</h3><p>Read player match performance through the PlayerMatchSummary database view.</p><span class="card-link">OPEN MATCH VIEW →</span></div>
        </a>
        <a class="game-card stats-card" href="<?= $auth ? 'stats.php' : 'login.php' ?>">
            <div class="card-number">02</div><div class="card-icon">AVG</div>
            <div class="card-content"><span class="card-category">ANALYTICS</span><h3>Player Statistics</h3><p>Aggregate and group champion performance, then export it to CSV or save it as PDF.</p><span class="card-link">RUN ANALYTICS →</span></div>
        </a>
        <a class="game-card community-card" href="<?= $auth ? 'community.php' : 'login.php' ?>">
            <div class="card-number">03</div><div class="card-icon">GG</div>
            <div class="card-content"><span class="card-category">COMMUNITY</span><h3>Media Feed</h3><p>Players create and delete their own posts, while all signed-in users can read engagement data.</p><span class="card-link">OPEN COMMUNITY →</span></div>
        </a>

        <?php if (currentRole() === 'user'): ?>
        <a class="game-card admin-card" href="profile.php">
            <div class="card-number">04</div><div class="card-icon">ID</div>
            <div class="card-content"><span class="card-category">PLAYER ACCOUNT</span><h3>My Profile</h3><p>Update your own StatTrax identity and Riot account fields without modifying anyone else's record.</p><span class="card-link">EDIT PROFILE →</span></div>
        </a>
        <?php elseif (currentRole() === 'admin'): ?>
        <a class="game-card admin-card" href="reports.php">
            <div class="card-number">04</div><div class="card-icon">MOD</div>
            <div class="card-content"><span class="card-category">ADMIN TOOLS</span><h3>Report Queue</h3><p>Review pending reports and change moderation status through a stored procedure.</p><span class="card-link">OPEN REPORTS →</span></div>
        </a>
        <a class="game-card comments-card" href="comments.php">
            <div class="card-number">05</div><div class="card-icon">CHAT</div>
            <div class="card-content"><span class="card-category">ADMIN TOOLS</span><h3>Comment Control</h3><p>Delete selected comments through the Phase 4 DeleteComment stored procedure.</p><span class="card-link">MANAGE COMMENTS →</span></div>
        </a>
        <a class="game-card trigger-card" href="trigger_demo.php">
            <div class="card-number">06</div><div class="card-icon">LOG</div>
            <div class="card-content"><span class="card-category">AUDIT SYSTEM</span><h3>Trigger Lab</h3><p>Demonstrate INSERT, UPDATE, and DELETE triggers and inspect ReportHistory.</p><span class="card-link">RUN TRIGGER DEMO →</span></div>
        </a>
        <?php endif; ?>
    </div>
</section>

<section class="mission-panel">
    <div class="mission-title"><span>PHASE 5 CHECKLIST</span><h2>GUI Requirements</h2></div>
    <div class="mission-steps">
        <div><span>01</span><p>Player and administrator login with different controls.</p></div>
        <div><span>02</span><p>CRUD operations available through the application interface.</p></div>
        <div><span>03</span><p>Grouped aggregate player report with CSV and Print/PDF export.</p></div>
        <div><span>04</span><p>Views, stored procedures, triggers, and every database table used by the application.</p></div>
    </div>
</section>

<?php require __DIR__ . '/../src/partials/footer.php'; ?>
