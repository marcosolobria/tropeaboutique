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

// El salt y el hash ya no viven en config.php, sino en el .env de fuera de
// public_html. Esta función seguía reescribiendo config.php con una expresión
// regular que ya no casaba con nada: cambiar la contraseña no hacía nada y el
// panel decía que sí.
function updatePassword($newPassword) {
    $salt = bin2hex(random_bytes(16));
    $hash = hash('sha256', $salt . $newPassword);

    $env = dirname(__DIR__, 2) . '/.env';
    if (!is_file($env)) { $env = dirname(__DIR__) . '/.env'; }
    if (!is_writable($env)) { return false; }

    $lineas = file($env, FILE_IGNORE_NEW_LINES);
    $puestos = ['ADMIN_PASSWORD_SALT' => false, 'ADMIN_PASSWORD_HASH' => false];
    foreach ($lineas as $i => $linea) {
        foreach (['ADMIN_PASSWORD_SALT' => $salt, 'ADMIN_PASSWORD_HASH' => $hash] as $clave => $valor) {
            if (strpos($linea, $clave . '=') === 0) {
                $lineas[$i] = $clave . '=' . $valor;
                $puestos[$clave] = true;
            }
        }
    }
    if (!$puestos['ADMIN_PASSWORD_SALT']) { $lineas[] = 'ADMIN_PASSWORD_SALT=' . $salt; }
    if (!$puestos['ADMIN_PASSWORD_HASH']) { $lineas[] = 'ADMIN_PASSWORD_HASH=' . $hash; }

    return file_put_contents($env, implode("\n", $lineas) . "\n") !== false;
}
