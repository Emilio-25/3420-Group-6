<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/functions.php';
