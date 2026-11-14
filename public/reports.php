<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = 'Pending Reports';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reportId = (int)($_POST['report_id'] ?? 0);
    $status = trim($_POST['status'] ?? 'Resolved');
    $adminId = (int)($_POST['admin_id'] ?? 1);
    try {
        $res = callProcedure('CALL UpdateReportStatus(?, ?, ?)', [$reportId, $status, $adminId]);
        $message = 'UpdateReportStatus executed. Rows updated: ' . ($res[0]['RowsUpdated'] ?? 0);
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

try {
    $rows = allRows('SELECT * FROM PendingReports ORDER BY CreationDate DESC');
} catch (Throwable $e) {
    $error = $error ?? $e->getMessage();
    $rows = [];
}
require __DIR__ . '/../src/partials/header.php';
?>
<h1>PendingReports View</h1>
<p>The table uses the <code>PendingReports</code> view. The form calls <code>UpdateReportStatus</code>.</p>
<?php if (isset($message)): ?><div class="notice"><?= e($message) ?></div><?php endif; ?>
<?php if (isset($error)): ?><div class="notice error"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="panel">
    <div class="form-row">
        <label>Report ID<input type="number" min="1" name="report_id" value="1" required></label>
        <label>Status<select name="status"><option>Resolved</option><option>Reviewed</option><option>Rejected</option><option>Pending</option></select></label>
        <label>Admin ID<input type="number" min="1" name="admin_id" value="1" required></label>
    </div>
    <button type="submit">Call UpdateReportStatus</button>
</form>
<div class="table-wrap"><table>
<thead><tr><th>Report</th><th>Reporter</th><th>Media</th><th>Reason</th><th>Status</th><th>Created</th></tr></thead>
<tbody><?php foreach ($rows as $r): ?><tr><td><?= e($r['ReportID']) ?></td><td><?= e($r['Username']) ?></td><td><?= e($r['MediaID']) ?></td><td><?= e($r['Reason']) ?></td><td><?= e($r['Status']) ?></td><td><?= e($r['CreationDate']) ?></td></tr><?php endforeach; ?></tbody>
</table></div>
<?php require __DIR__ . '/../src/partials/footer.php'; ?>
