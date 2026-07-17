<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

load_env_file(dirname(__DIR__) . '/.env');

$pdo = null;
$database_error = null;

try {
    $pdo = get_pdo();
} catch (Throwable $exception) {
    $database_error = 'Database connection is not configured yet. Update your MySQL settings before using the backend features.';
}
