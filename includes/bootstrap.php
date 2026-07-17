<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

$pdo = null;
$database_error = null;

try {
    $pdo = get_pdo();
} catch (Throwable $exception) {
    $database_error = 'Database connection is not configured yet. Update your MySQL settings before using the backend features.';
}
