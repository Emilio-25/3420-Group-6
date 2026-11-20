<?php
require_once __DIR__ . '/bootstrap.php';
requireLogin();
$pageTitle = 'Player Statistics';

$userId = currentRole() === 'user' ? currentUserId() : (int)($_REQUEST['user_id'] ?? 1);
$start = $_REQUEST['start_date'] ?? '2026-10-01 00:00:00';
$end = $_REQUEST['end_date'] ?? '2026-10-31 23:59:59';
$summary = [];
$grouped = [];

try {
    $summary = callProcedure('CALL GetPlayerStatistics(?, ?, ?)', [$userId, $start, $end]);
    $grouped = allRows(
        'SELECT ps.Champion,
                COUNT(*) AS Matches,
                ROUND(AVG(ps.Kills),2) AS AverageKills,
                ROUND(AVG(ps.Deaths),2) AS AverageDeaths,
                ROUND(AVG(ps.Assists),2) AS AverageAssists,
                SUM(CASE WHEN ps.WinOrLoss = \'Win\' THEN 1 ELSE 0 END) AS Wins,
                ROUND(100 * SUM(CASE WHEN ps.WinOrLoss = \'Win\' THEN 1 ELSE 0 END) / COUNT(*),2) AS WinRate
         FROM PlayerStat ps
         JOIN `Match` m ON m.MatchID = ps.MatchID
         WHERE ps.UserID = ? AND m.PlayedAt BETWEEN ? AND ?
         GROUP BY ps.Champion
         ORDER BY Matches DESC, AverageKills DESC',
        [$userId, $start, $end]
    );
} catch (Throwable $e) {
    $error = $e->getMessage();
}

$exportQuery = http_build_query(['user_id' => $userId, 'start_date' => $start, 'end_date' => $end]);
require __DIR__ . '/../src/partials/header.php';
?>
<h1>Player Analytics</h1>
<form method="get" class="panel no-print">
    <div class="form-row">
        <?php if (currentRole() === 'admin'): ?>
        <label>User ID<input type="number" min="1" name="user_id" value="<?= e($userId) ?>"></label>
        <?php endif; ?>
        <label>Start datetime<input name="start_date" value="<?= e($start) ?>"></label>
        <label>End datetime<input name="end_date" value="<?= e($end) ?>"></label>
    </div>
    <button type="submit">Generate Report</button>
</form>
<?php if (isset($error)): ?><div class="notice error"><?= e($error) ?></div><?php endif; ?>

<?php if ($summary): ?>
<section class="report-block">
    <div class="report-heading">
        <div><span class="section-kicker">AGGREGATE SUMMARY</span><h2>Performance Overview</h2></div>
        <div class="inline-actions no-print">
            <a class="game-btn secondary" href="stats_export.php?<?= e($exportQuery) ?>">Export CSV</a>
            <button type="button" onclick="window.print()">Print / Save PDF</button>
        </div>
    </div>
    <div class="metric-grid">
        <?php foreach ($summary[0] as $label => $value): ?>
        <div class="metric-tile"><span><?= e($label) ?></span><strong><?= e($value) ?></strong></div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<section class="panel report-block">
    <div class="report-heading"><div><span class="section-kicker">GROUPED REPORT</span><h2>Champion Performance</h2></div></div>
    <div class="table-wrap"><table>
        <thead><tr><th>Champion</th><th>Matches</th><th>Avg Kills</th><th>Avg Deaths</th><th>Avg Assists</th><th>Wins</th><th>Win Rate</th></tr></thead>
        <tbody>
        <?php foreach ($grouped as $r): ?>
            <tr><td><?= e($r['Champion']) ?></td><td><?= e($r['Matches']) ?></td><td><?= e($r['AverageKills']) ?></td><td><?= e($r['AverageDeaths']) ?></td><td><?= e($r['AverageAssists']) ?></td><td><?= e($r['Wins']) ?></td><td><?= e($r['WinRate']) ?>%</td></tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>
</section>
<?php require __DIR__ . '/../src/partials/footer.php'; ?>
