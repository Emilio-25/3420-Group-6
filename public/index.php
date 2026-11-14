<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = 'Phase 4 Dashboard';
require __DIR__ . '/../src/partials/header.php';
?>
<h1>StatTrax Phase 4 Dashboard</h1>
<p>This LAMP prototype demonstrates the database views, stored procedures, and triggers required for Phase 4.</p>

<div class="card-grid">
    <section class="card">
        <h2>Views</h2>
        <p><strong>PlayerMatchSummary</strong>, <strong>MediaEngagement</strong>, and <strong>PendingReports</strong>.</p>
        <p><a href="matches.php">Match view</a> | <a href="community.php">Media view</a> | <a href="reports.php">Report view</a></p>
    </section>
    <section class="card">
        <h2>Stored Procedures</h2>
        <p>Create a media post, delete a comment, update a report, and calculate player statistics.</p>
        <p><a href="stats.php">Statistics</a> | <a href="comments.php">Delete comment</a></p>
    </section>
    <section class="card">
        <h2>Triggers</h2>
        <p>INSERT, UPDATE, and DELETE events on Report are written automatically to ReportHistory.</p>
        <p><a href="trigger_demo.php">Run trigger demo</a></p>
    </section>
</div>

<section class="panel">
    <h2>Suggested report workflow</h2>
    <ol>
        <li>Run <code>database/00_full_install.sql</code>.</li>
        <li>Open each page and perform its test.</li>
        <li>Capture a screenshot of the returned rows or changed database state.</li>
        <li>Paste each screenshot into the matching evidence box in the included Phase 4 report.</li>
    </ol>
</section>
<?php require __DIR__ . '/../src/partials/footer.php'; ?>
