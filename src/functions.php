<?php
function e(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function allRows(string $sql, array $params = []): array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function oneRow(string $sql, array $params = []): ?array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row === false ? null : $row;
}

function callProcedure(string $sql, array $params = []): array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
    $stmt->closeCursor();
    return $rows;
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['auth']);
}

function currentRole(): ?string
{
    return $_SESSION['auth']['role'] ?? null;
}

function currentUser(): ?array
{
    return $_SESSION['auth'] ?? null;
}

function loginUser(array $auth): void
{
    session_regenerate_id(true);
    $_SESSION['auth'] = $auth;
}

function logoutUser(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: login.php?next=' . urlencode(basename($_SERVER['PHP_SELF'] ?? 'index.php')));
        exit;
    }
}

function requireRole(string $role): void
{
    requireLogin();
    if (currentRole() !== $role) {
        http_response_code(403);
        $pageTitle = 'Access Denied';
        require __DIR__ . '/partials/header.php';
        echo '<section class="panel"><h1>Access denied</h1><p>This module is restricted to the ' . e($role) . ' role.</p><a class="game-btn secondary" href="index.php">Return to dashboard</a></section>';
        require __DIR__ . '/partials/footer.php';
        exit;
    }
}

function currentUserId(): ?int
{
    return currentRole() === 'user' ? (int)($_SESSION['auth']['id'] ?? 0) : null;
}

function currentAdminId(): ?int
{
    return currentRole() === 'admin' ? (int)($_SESSION['auth']['id'] ?? 0) : null;
}
