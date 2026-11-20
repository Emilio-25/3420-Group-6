<?php
function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('STATTRAX_DB_HOST') ?: 'localhost';
    $port = getenv('STATTRAX_DB_PORT') ?: '3306';
    $name = getenv('STATTRAX_DB_NAME') ?: 'stattrax';
    $user = getenv('STATTRAX_DB_USER') ?: 'root';
    $pass = getenv('STATTRAX_DB_PASS') ?: '';

    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
