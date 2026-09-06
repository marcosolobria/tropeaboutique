<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://tropeaboutique.com');

define('BUZON', 'info@tropeaboutique.com');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed']); exit;
}

// Trampa para bots: el campo va oculto en el formulario, una persona nunca lo
// rellena. Se responde "ok" a propósito, para que el bot no sepa que ha fallado.
if (trim($_POST['website'] ?? '') !== '') {
    echo json_encode(['ok' => true]); exit;
}

$name    = trim(strip_tags($_POST['name'] ?? ''));
$email   = trim(strip_tags($_POST['email'] ?? ''));
$type    = trim(strip_tags($_POST['type'] ?? ''));
$message = trim(strip_tags($_POST['message'] ?? ''));

if (!$name || !$email || !$message) {
    echo json_encode(['error' => 'Faltan campos']); exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Email inválido']); exit;
}
// Un salto de línea en estos campos permitiría inyectar cabeceras en el correo.
if (preg_match('/[\r\n]/', $name . $email . $type)) {
    echo json_encode(['error' => 'Datos inválidos']); exit;
}

// Freno simple por IP: 5 envíos por hora. Evita que el buzón acabe inundado.
$ip = $_SERVER['REMOTE_ADDR'] ?? 'desconocida';
$rateFile = sys_get_temp_dir() . '/tropea-rate-' . md5($ip);
$hits = array_values(array_filter(
    file_exists($rateFile) ? (json_decode(file_get_contents($rateFile), true) ?: []) : [],
    fn($t) => $t > time() - 3600
));
if (count($hits) >= 5) {
    http_response_code(429);
    echo json_encode(['error' => 'Has enviado demasiados mensajes seguidos. Prueba dentro de un rato.']);
    exit;
}
$hits[] = time();
@file_put_contents($rateFile, json_encode($hits));

// 1) Guardar en el panel de administración
$dataFile = __DIR__ . '/../admin/data/messages.json';
$msgs = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];
if (!is_array($msgs)) $msgs = [];

$msgs[] = [
    'id'      => time() . rand(100,999),
    'name'    => $name,
    'email'   => $email,
    'type'    => $type,
    'message' => $message,
    'date'    => date('d/m/Y H:i'),
];

file_put_contents($dataFile, json_encode($msgs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// 2) Avisar por correo. El remitente es el propio dominio (si no, SPF lo manda a
// spam); para responder al cliente basta con darle a "Responder".
$asunto = '=?UTF-8?B?' . base64_encode('🌸 Nuevo mensaje de ' . $name . ($type ? " ($type)" : '')) . '?=';
$cuerpo = "Nuevo mensaje desde tropeaboutique.com\n\n"
        . "Nombre:  $name\n"
        . "Email:   $email\n"
        . "Tipo:    " . ($type ?: '—') . "\n"
        . "Fecha:   " . date('d/m/Y H:i') . "\n\n"
        . "-----------------------------------\n"
        . $message . "\n"
        . "-----------------------------------\n\n"
        . "Responde a este correo y le llegará directamente a " . $name . ".\n";

$cabeceras = [
    'From: Tropea Boutique <' . BUZON . '>',
    'Reply-To: ' . $name . ' <' . $email . '>',
    'Content-Type: text/plain; charset=UTF-8',
    'X-Mailer: tropeaboutique.com',
];

$enviado = @mail(BUZON, $asunto, $cuerpo, implode("\r\n", $cabeceras));

// El mensaje queda guardado en el panel aunque el correo falle: nunca se pierde.
echo json_encode(['ok' => true, 'mail' => $enviado]);
