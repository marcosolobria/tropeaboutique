<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://tropeaboutique.com');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed']); exit;
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
echo json_encode(['ok' => true]);
