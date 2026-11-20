<?php
require_once __DIR__ . '/bootstrap.php';
requireRole('admin');
$pageTitle = 'Comment Procedure';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commentId = (int)($_POST['comment_id'] ?? 0);
    try {
        $res = callProcedure('CALL DeleteComment(?)', [$commentId]);
        $message = 'DeleteComment executed. Rows deleted: ' . ($res[0]['RowsDeleted'] ?? 0);
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

try {
    $rows = allRows('SELECT c.CommentID, c.CommentText, c.CreationDate, u.Username, c.MediaID FROM `Comment` c JOIN `User` u ON c.UserID=u.UserID ORDER BY c.CommentID');
} catch (Throwable $e) {
    $error = $error ?? $e->getMessage();
    $rows = [];
}
require __DIR__ . '/../src/partials/header.php';
?>
<h1>DeleteComment Procedure</h1>
<p>Use the demo comment (normally CommentID 4 after a fresh install), then capture before/after evidence.</p>
<?php if (isset($message)): ?><div class="notice"><?= e($message) ?></div><?php endif; ?>
<?php if (isset($error)): ?><div class="notice error"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="panel">
    <label>Comment ID<input type="number" min="1" name="comment_id" value="4" required></label>
    <button type="submit" class="danger">Call DeleteComment</button>
</form>
<div class="table-wrap"><table>
<thead><tr><th>ID</th><th>User</th><th>Media</th><th>Comment</th><th>Created</th></tr></thead>
<tbody><?php foreach ($rows as $r): ?><tr><td><?= e($r['CommentID']) ?></td><td><?= e($r['Username']) ?></td><td><?= e($r['MediaID']) ?></td><td><?= e($r['CommentText']) ?></td><td><?= e($r['CreationDate']) ?></td></tr><?php endforeach; ?></tbody>
</table></div>
<?php require __DIR__ . '/../src/partials/footer.php'; ?>
