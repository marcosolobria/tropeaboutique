<?php
require_once __DIR__ . '/config.php';
session_name(SESSION_NAME);
session_start();

function isLoggedIn() {
    return isset($_SESSION['tropea_auth']) && $_SESSION['tropea_auth'] === true;
}

function checkPassword($password) {
    $hash = hash('sha256', ADMIN_PASSWORD_SALT . $password);
    return hash_equals(ADMIN_PASSWORD_HASH, $hash);
}

function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: /admin/');
        exit;
    }
}

function updatePassword($newPassword) {
    $salt = bin2hex(random_bytes(16));
    $hash = hash('sha256', $salt . $newPassword);
    $config = file_get_contents(__DIR__ . '/config.php');
    $config = preg_replace("/define\('ADMIN_PASSWORD_SALT',\s*'[^']*'\)/", "define('ADMIN_PASSWORD_SALT', '$salt')", $config);
    $config = preg_replace("/define\('ADMIN_PASSWORD_HASH',\s*'[^']*'\)/", "define('ADMIN_PASSWORD_HASH', '$hash')", $config);
    return file_put_contents(__DIR__ . '/config.php', $config) !== false;
}
