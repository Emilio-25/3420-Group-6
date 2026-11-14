<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = 'Player Statistics';
$result = [];

$userId = (int)($_POST['user_id'] ?? 1);
$start = $_POST['start_date'] ?? '2026-10-01 00:00:00';
$end = $_POST['end_date'] ?? '2026-10-31 23:59:59';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $result = callProcedure('CALL GetPlayerStatistics(?, ?, ?)', [$userId, $start, $end]);
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}
require __DIR__ . '/../src/partials/header.php';
?>
<h1>GetPlayerStatistics Procedure</h1>
<p>This page calls the statistical stored procedure for a selected user and date range.</p>
<form method="post" class="panel">
    <div class="form-row">
        <label>User ID<input type="number" min="1" name="user_id" value="<?= e($userId) ?>"></label>
        <label>Start datetime<input name="start_date" value="<?= e($start) ?>"></label>
        <label>End datetime<input name="end_date" value="<?= e($end) ?>"></label>
    </div>
    <button type="submit">Calculate Statistics</button>
</form>
<?php if (isset($error)): ?><div class="notice error"><?= e($error) ?></div><?php endif; ?>
<?php if ($result): ?>
<div class="table-wrap"><table><thead><tr>
<?php foreach (array_keys($result[0]) as $h): ?><th><?= e($h) ?></th><?php endforeach; ?>
</tr></thead><tbody><tr>
<?php foreach ($result[0] as $v): ?><td><?= e($v) ?></td><?php endforeach; ?>
</tr></tbody></table></div>
<?php endif; ?>
<?php require __DIR__ . '/../src/partials/footer.php'; ?>
