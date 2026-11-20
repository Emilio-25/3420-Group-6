<?php
function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    /*
     * Production option:
     *   /etc/stattrax-db.php
     *
     * Keep this file outside /var/www/html and outside Git.
     * Environment variables still work and take precedence.
     */
    $serverConfig = [];
    $configFile = getenv('STATTRAX_DB_CONFIG') ?: '/etc/stattrax-db.php';

    if (is_readable($configFile)) {
        $loaded = require $configFile;
        if (is_array($loaded)) {
            $serverConfig = $loaded;
        }
    }

    $host = getenv('STATTRAX_DB_HOST') ?: ($serverConfig['host'] ?? 'localhost');
    $port = getenv('STATTRAX_DB_PORT') ?: ($serverConfig['port'] ?? '3306');
    $name = getenv('STATTRAX_DB_NAME') ?: ($serverConfig['name'] ?? 'stattrax');
    $user = getenv('STATTRAX_DB_USER') ?: ($serverConfig['user'] ?? null);
    $pass = getenv('STATTRAX_DB_PASS');

    if ($pass === false || $pass === '') {
        $pass = $serverConfig['pass'] ?? null;
    }

    // Avoid silently using MySQL root on a public deployment.
    if ($user === null || $pass === null) {
        throw new RuntimeException('StatTrax database credentials are not configured.');
    }

    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
