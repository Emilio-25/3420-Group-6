<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = 'Community';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int)($_POST['user_id'] ?? 0);
    $filePath = trim($_POST['file_path'] ?? '');
    $description = trim($_POST['description'] ?? '');
    try {
        $created = callProcedure('CALL CreateMediaPost(?, ?, ?)', [$userId, $filePath, $description]);
        $message = 'Media post created. New MediaID: ' . ($created[0]['NewMediaID'] ?? 'unknown');
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

try {
    $rows = allRows('SELECT * FROM MediaEngagement ORDER BY CreationDate DESC');
} catch (Throwable $e) {
    $error = $error ?? $e->getMessage();
    $rows = [];
}
require __DIR__ . '/../src/partials/header.php';
?>
<h1>MediaEngagement View</h1>
<p>The feed below comes from the <code>MediaEngagement</code> view. The form calls <code>CreateMediaPost</code>.</p>
<?php if (isset($message)): ?><div class="notice"><?= e($message) ?></div><?php endif; ?>
<?php if (isset($error)): ?><div class="notice error"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="panel">
    <h2>Create Media Post</h2>
    <div class="form-row">
        <label>User ID<input type="number" min="1" name="user_id" value="1" required></label>
        <label>File path<input name="file_path" value="/uploads/new_highlight.mp4" required></label>
    </div>
    <label>Description<textarea name="description">New StatTrax highlight</textarea></label>
    <button type="submit">Call CreateMediaPost</button>
</form>
<div class="table-wrap"><table>
<thead><tr><th>MediaID</th><th>User</th><th>Description</th><th>File</th><th>Likes</th><th>Comments</th><th>Created</th></tr></thead>
<tbody><?php foreach ($rows as $r): ?><tr>
<td><?= e($r['MediaID']) ?></td><td><?= e($r['Username']) ?></td><td><?= e($r['Description']) ?></td><td><?= e($r['FilePath']) ?></td><td><?= e($r['LikeCount']) ?></td><td><?= e($r['CommentCount']) ?></td><td><?= e($r['CreationDate']) ?></td>
</tr><?php endforeach; ?></tbody>
</table></div>
<?php require __DIR__ . '/../src/partials/footer.php'; ?>
