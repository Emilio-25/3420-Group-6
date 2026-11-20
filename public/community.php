<?php
require_once __DIR__ . '/bootstrap.php';
requireLogin();
$pageTitle = 'Community';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && currentRole() === 'user') {
    $action = $_POST['action'] ?? 'create';
    $userId = currentUserId();
    try {
        if ($action === 'create') {
            $filePath = trim($_POST['file_path'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $created = callProcedure('CALL CreateMediaPost(?, ?, ?)', [$userId, $filePath, $description]);
            $message = 'Media post created. New MediaID: ' . ($created[0]['NewMediaID'] ?? 'unknown');
        } elseif ($action === 'delete_media') {
            $stmt = db()->prepare('DELETE FROM `Media` WHERE MediaID=? AND UserID=?');
            $stmt->execute([(int)($_POST['media_id'] ?? 0), $userId]);
            $message = 'Your media post was deleted. Rows deleted: ' . $stmt->rowCount();
        } elseif ($action === 'report') {
            $reason = trim($_POST['reason'] ?? 'Community report');
            $stmt = db()->prepare("INSERT INTO `Report` (Reason, Status, UserID, MediaID) VALUES (?, 'Pending', ?, ?)");
            $stmt->execute([$reason, $userId, (int)($_POST['media_id'] ?? 0)]);
            $message = 'Report submitted to the moderation queue.';
        }
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
<h1>Community Feed</h1>
<p>The feed comes from the <code>MediaEngagement</code> view. Player accounts can create posts, delete their own posts, and submit moderation reports.</p>
<?php if (isset($message)): ?><div class="notice"><?= e($message) ?></div><?php endif; ?>
<?php if (isset($error)): ?><div class="notice error"><?= e($error) ?></div><?php endif; ?>

<?php if (currentRole() === 'user'): ?>
<form method="post" class="panel">
    <input type="hidden" name="action" value="create">
    <h2>Create Media Post</h2>
    <label>File path<input name="file_path" value="/uploads/new_highlight.mp4" required></label>
    <label>Description<textarea name="description">New StatTrax highlight</textarea></label>
    <button type="submit">Create Post</button>
</form>
<?php else: ?>
<div class="notice">Administrator view: community content is read-only here. Moderation actions are available in Reports and Comments.</div>
<?php endif; ?>

<div class="community-grid">
<?php foreach ($rows as $r): ?>
    <article class="panel media-card">
        <div class="media-card-top">
            <div>
                <span class="section-kicker">MEDIA #<?= e($r['MediaID']) ?></span>
                <h2><?= e($r['Username']) ?></h2>
            </div>
            <span class="metric-chip"><?= e($r['LikeCount']) ?> likes · <?= e($r['CommentCount']) ?> comments</span>
        </div>
        <p><?= e($r['Description']) ?></p>
        <code><?= e($r['FilePath']) ?></code>
        <small><?= e($r['CreationDate']) ?></small>
        <?php if (currentRole() === 'user'): ?>
            <div class="inline-actions">
                <?php if ((int)$r['UserID'] === currentUserId()): ?>
                <form method="post" onsubmit="return confirm('Delete this media post?');">
                    <input type="hidden" name="action" value="delete_media">
                    <input type="hidden" name="media_id" value="<?= e($r['MediaID']) ?>">
                    <button class="danger" type="submit">Delete My Post</button>
                </form>
                <?php else: ?>
                <form method="post" class="report-inline">
                    <input type="hidden" name="action" value="report">
                    <input type="hidden" name="media_id" value="<?= e($r['MediaID']) ?>">
                    <input name="reason" value="Inappropriate or spam content" aria-label="Report reason">
                    <button type="submit" class="secondary-action">Report</button>
                </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </article>
<?php endforeach; ?>
</div>
<?php require __DIR__ . '/../src/partials/footer.php'; ?>
