<?php
require_once __DIR__ . '/bootstrap.php';
requireRole('admin');
$pageTitle = 'Trigger Demo';

$demoId = (int)($_SESSION['demo_report_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'insert') {
            $stmt = db()->prepare("INSERT INTO `Report` (Reason, Status, UserID, MediaID) VALUES (?, 'Pending', ?, ?)");
            $stmt->execute(['Trigger demo from PHP', (int)$_POST['user_id'], (int)$_POST['media_id']]);
            $demoId = (int)db()->lastInsertId();
            $_SESSION['demo_report_id'] = $demoId;
            $message = "Inserted ReportID {$demoId}; INSERT trigger should now be in ReportHistory.";
        } elseif ($action === 'update' && $demoId > 0) {
            $stmt = db()->prepare("UPDATE `Report` SET Status='Reviewed', ReviewerAdminID=? WHERE ReportID=?");
            $stmt->execute([currentAdminId(), $demoId]);
            $message = "Updated ReportID {$demoId}; UPDATE trigger should now be in ReportHistory.";
        } elseif ($action === 'delete' && $demoId > 0) {
            $stmt = db()->prepare('DELETE FROM `Report` WHERE ReportID=?');
            $stmt->execute([$demoId]);
            $message = "Deleted ReportID {$demoId}; DELETE trigger should now be in ReportHistory.";
        }
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

try {
    $history = allRows('SELECT * FROM ReportHistory ORDER BY HistoryID DESC LIMIT 30');
} catch (Throwable $e) {
    $error = $error ?? $e->getMessage();
    $history = [];
}
require __DIR__ . '/../src/partials/header.php';
?>
<h1>Report Trigger Demonstration</h1>
<p>This page provides easy evidence for the required INSERT, UPDATE, and DELETE triggers.</p>
<?php if (isset($message)): ?><div class="notice"><?= e($message) ?></div><?php endif; ?>
<?php if (isset($error)): ?><div class="notice error"><?= e($error) ?></div><?php endif; ?>
<div class="card-grid">
<form method="post" class="card">
    <h2>1. INSERT</h2>
    <input type="hidden" name="action" value="insert">
    <label>User ID<input type="number" name="user_id" min="1" value="1"></label>
    <label>Media ID<input type="number" name="media_id" min="1" value="2"></label>
    <button type="submit">Insert Test Report</button>
</form>
<form method="post" class="card">
    <h2>2. UPDATE</h2>
    <p>Current demo ReportID: <strong><?= e($demoId ?: 'none') ?></strong></p>
    <input type="hidden" name="action" value="update">
        <button type="submit">Update Test Report</button>
</form>
<form method="post" class="card">
    <h2>3. DELETE</h2>
    <p>Current demo ReportID: <strong><?= e($demoId ?: 'none') ?></strong></p>
    <input type="hidden" name="action" value="delete">
    <button type="submit" class="danger">Delete Test Report</button>
</form>
</div>
<section class="panel">
<h2>ReportHistory</h2>
<div class="table-wrap"><table>
<thead><tr><th>HistoryID</th><th>ReportID</th><th>Action</th><th>Old Status</th><th>New Status</th><th>Changed</th></tr></thead>
<tbody><?php foreach ($history as $r): ?><tr><td><?= e($r['HistoryID']) ?></td><td><?= e($r['ReportID']) ?></td><td><?= e($r['ActionType']) ?></td><td><?= e($r['OldStatus']) ?></td><td><?= e($r['NewStatus']) ?></td><td><?= e($r['ChangedAt']) ?></td></tr><?php endforeach; ?></tbody>
</table></div>
</section>
<?php require __DIR__ . '/../src/partials/footer.php'; ?>
