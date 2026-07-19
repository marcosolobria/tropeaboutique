<?php
require_once __DIR__ . '/../env.php';
// Rellena estos valores en .env tras crear el bot con @BotFather
// y obtener el user_id de María (lo imprime el bot al primer mensaje)
define('BOT_TOKEN',    getenv('BOT_TOKEN'));
define('ALLOWED_USER', getenv('ALLOWED_USER')); // ID numérico de Telegram de María
