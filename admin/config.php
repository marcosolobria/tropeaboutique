<?php
require_once __DIR__ . '/../env.php';
define('ADMIN_PASSWORD_SALT', getenv('ADMIN_PASSWORD_SALT'));
define('ADMIN_PASSWORD_HASH', getenv('ADMIN_PASSWORD_HASH'));
define('SESSION_NAME', 'tropea_admin');
define('DATA_DIR', __DIR__ . '/data/');
define('GEMINI_API_KEY', getenv('GEMINI_API_KEY'));
?>
