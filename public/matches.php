<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = 'Player Match Summary';
$username = trim($_GET['username'] ?? '');

try {
    if ($username !== '') {
        $rows = allRows(
            'SELECT * FROM PlayerMatchSummary WHERE Username = ? ORDER BY PlayedAt DESC',
            [$username]
        );
    } else {
        $rows = allRows('SELECT * FROM PlayerMatchSummary ORDER BY PlayedAt DESC');
    }
} catch (Throwable $e) {
    $error = $e->getMessage();
    $rows = [];
}
require __DIR__ . '/../src/partials/header.php';
?>
<h1>PlayerMatchSummary View</h1>
<p>This page reads directly from the <code>PlayerMatchSummary</code> view.</p>
<form method="get" class="panel">
    <label>Filter by username
        <input name="username" value="<?= e($username) ?>" placeholder="Joshua">
    </label>
    <button type="submit">Run View Query</button>
</form>
<?php if (isset($error)): ?><div class="notice error"><?= e($error) ?></div><?php endif; ?>
<div class="table-wrap">
<table>
    <thead><tr><th>User</th><th>Match</th><th>Mode</th><th>Played</th><th>Champion</th><th>K/D/A</th><th>Damage</th><th>Gold</th><th>Result</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
        <tr>
            <td><?= e($r['Username']) ?></td>
            <td><?= e($r['MatchID']) ?></td>
            <td><?= e($r['GameMode']) ?></td>
            <td><?= e($r['PlayedAt']) ?></td>
            <td><?= e($r['Champion']) ?></td>
            <td><?= e($r['Kills']) ?>/<?= e($r['Deaths']) ?>/<?= e($r['Assists']) ?></td>
            <td><?= e($r['DamageDealt']) ?></td>
            <td><?= e($r['GoldEarned']) ?></td>
            <td><?= e($r['WinOrLoss']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php require __DIR__ . '/../src/partials/footer.php'; ?>
