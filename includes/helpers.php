<?php

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect_to(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function old(string $key, string $default = ''): string
{
    return e($_POST[$key] ?? $_GET[$key] ?? $default);
}

function post_value(string $key, string $default = ''): string
{
    return trim((string) ($_POST[$key] ?? $default));
}

function current_user(): ?array
{
    return $_SESSION['auth_user'] ?? null;
}

function dashboard_path_for_role(string $role): string
{
    return match ($role) {
        'donor' => 'donor-dashboard.php',
        'coordinator' => 'admin-dashboard.php',
        'tester' => 'tester-report.php',
        default => 'index.php',
    };
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function login_user(array $user): void
{
    $_SESSION['auth_user'] = [
        'id' => (int) $user['id'],
        'role' => $user['role'],
        'full_name' => $user['full_name'],
        'email' => $user['email'],
        'donor_id' => isset($user['donor_id']) ? (int) $user['donor_id'] : null,
    ];
}

function logout_user(): void
{
    unset($_SESSION['auth_user']);
}

function require_login(?string $role = null): void
{
    $user = current_user();

    if ($user === null) {
        set_flash('error', 'Please log in to access that page.');
        redirect_to('login.php');
    }

    if ($role !== null && $user['role'] !== $role) {
        set_flash('error', 'You do not have permission to view that page.');
        redirect_to('index.php');
    }
}

function db_all(string $sql, array $params = []): array
{
    global $pdo;

    if (!$pdo instanceof PDO) {
        return [];
    }

    $statement = $pdo->prepare($sql);
    $statement->execute($params);

    return $statement->fetchAll();
}

function db_one(string $sql, array $params = []): ?array
{
    global $pdo;

    if (!$pdo instanceof PDO) {
        return null;
    }

    $statement = $pdo->prepare($sql);
    $statement->execute($params);
    $row = $statement->fetch();

    return $row ?: null;
}
