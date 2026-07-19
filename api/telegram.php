<?php
// Tropea Boutique — Telegram Bot Webhook
// María manda foto + caption → se guarda el producto en products.json

require __DIR__ . '/config.php';
define('PRODUCTS_FILE', __DIR__ . '/../products.json');
define('IMG_DIR',       __DIR__ . '/../assets/products/');

@mkdir(IMG_DIR, 0755, true);

// Leer el update de Telegram
$input  = file_get_contents('php://input');
$update = json_decode($input, true);

if (!$input || !$update) { http_response_code(200); exit; }

$msg     = $update['message'] ?? $update['channel_post'] ?? null;
if (!$msg) { http_response_code(200); exit; }

$chat_id = $msg['chat']['id'];
$user_id = (string)($msg['from']['id'] ?? '');
$caption = $msg['caption'] ?? $msg['text'] ?? '';

// Seguridad: solo acepta mensajes de María
if (ALLOWED_USER && $user_id !== ALLOWED_USER) {
    tg_send($chat_id, "⛔ No autorizado.");
    exit;
}

// Comandos de texto
if (isset($msg['text'])) {
    $text = trim($msg['text']);
    if ($text === '/start') {
        tg_send($chat_id, "¡Hola! 👋 Soy el bot de Tropea Boutique.\n\nMándame una foto con el caption así:\n\n🔹 *Nombre del producto*\n💰 Precio: 24\n📦 Colección: Verano\n📝 Descripción FR: ...\n📝 Descripción ES: ...\n\nO usa el formato corto:\n`nombre | precio | colección | desc_fr | desc_es`");
    } elseif ($text === '/lista') {
        $products = load_products();
        if (empty($products)) {
            tg_send($chat_id, "No hay productos todavía.");
        } else {
            $list = "📋 *Productos actuales:*\n\n";
            foreach ($products as $p) {
                $list .= "• *{$p['name']}* — {$p['price']}€ [{$p['collection']}] (ID: {$p['id']})\n";
            }
            tg_send($chat_id, $list);
        }
    } elseif (str_starts_with($text, '/borrar ')) {
        $id = (int)substr($text, 8);
        $products = load_products();
        $before   = count($products);
        $products = array_values(array_filter($products, fn($p) => $p['id'] !== $id));
        save_products($products);
        $after = count($products);
        tg_send($chat_id, $before > $after ? "✅ Producto #$id borrado." : "No encontré el producto #$id.");
    } else {
        tg_send($chat_id, "Mándame una foto con el caption del producto, o usa /lista para ver los productos.");
    }
    exit;
}

// Foto recibida → crear producto
if (isset($msg['photo'])) {
    if (empty($caption)) {
        tg_send($chat_id, "📸 Foto recibida. Falta el caption con los datos del producto.\n\nFormato: `nombre | precio | colección | desc_fr | desc_es`");
        exit;
    }

    // Parsear el caption
    $product = parse_caption($caption);

    // Descargar la foto (calidad máxima)
    $photos   = $msg['photo'];
    $best     = end($photos);
    $file_id  = $best['file_id'];
    $img_url  = download_telegram_file($file_id);

    if ($img_url) {
        $product['image_url'] = $img_url;
    } else {
        tg_send($chat_id, "⚠️ No pude descargar la foto. El producto se guardó sin imagen.");
        $product['image_url'] = '';
    }

    // Guardar producto
    $products = load_products();
    $product['id'] = time();
    $product['created_at'] = date('c');
    $product['available']  = true;
    $products[] = $product;
    save_products($products);

    $preview = "✅ *Producto añadido:*\n\n"
        . "📌 *{$product['name']}*\n"
        . "💰 {$product['price']}€\n"
        . "📦 {$product['collection']}\n"
        . "🇫🇷 {$product['description_fr']}\n"
        . "🇪🇸 {$product['description_es']}\n\n"
        . "ID: `{$product['id']}`\n"
        . "Para borrarlo: /borrar {$product['id']}";

    tg_send($chat_id, $preview);
    exit;
}

tg_send($chat_id, "Mándame una foto con caption, o escribe /start para ver las instrucciones.");

// ── Funciones ───────────────────────────────────────────────

function parse_caption(string $caption): array {
    // Formato corto: nombre | precio | colección | desc_fr | desc_es
    $parts = array_map('trim', explode('|', $caption));
    if (count($parts) >= 5) {
        return [
            'name'           => $parts[0],
            'name_fr'        => $parts[0],
            'name_es'        => $parts[0],
            'price'          => (float)preg_replace('/[^0-9.]/', '', $parts[1]),
            'currency'       => '€',
            'collection'     => $parts[2],
            'description_fr' => $parts[3],
            'description_es' => $parts[4],
        ];
    }

    // Formato libre: extraer precio con regex, usar caption como descripción
    preg_match('/(\d+(?:[.,]\d+)?)\s*€?/', $caption, $m);
    $price = isset($m[1]) ? (float)str_replace(',', '.', $m[1]) : 0;

    // Intentar extraer colección
    preg_match('/\b(Verano|Été|Panama|Nouvelle|Collection\s+\S+)\b/i', $caption, $col);
    $collection = $col[1] ?? 'Nouvelle';

    $name = trim(preg_replace('/\d+\s*€?/', '', strtok($caption, "\n")));

    return [
        'name'           => $name ?: 'Nuevo producto',
        'name_fr'        => $name ?: 'Nouveau produit',
        'name_es'        => $name ?: 'Nuevo producto',
        'price'          => $price,
        'currency'       => '€',
        'collection'     => $collection,
        'description_fr' => $caption,
        'description_es' => $caption,
    ];
}

function download_telegram_file(string $file_id): string {
    $token = BOT_TOKEN;
    $res   = file_get_contents("https://api.telegram.org/bot{$token}/getFile?file_id={$file_id}");
    $data  = json_decode($res, true);
    if (!($data['ok'] ?? false)) return '';

    $file_path = $data['result']['file_path'];
    $url       = "https://api.telegram.org/file/bot{$token}/{$file_path}";

    $img_data = file_get_contents($url);
    if (!$img_data) return '';

    $ext      = pathinfo($file_path, PATHINFO_EXTENSION) ?: 'jpg';
    $filename = 'product_' . time() . '.' . $ext;
    $local    = IMG_DIR . $filename;

    file_put_contents($local, $img_data);
    return "/assets/products/{$filename}";
}

function load_products(): array {
    if (!file_exists(PRODUCTS_FILE)) return [];
    return json_decode(file_get_contents(PRODUCTS_FILE), true) ?? [];
}

function save_products(array $products): void {
    file_put_contents(PRODUCTS_FILE, json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function tg_send(string $chat_id, string $text): void {
    $token = BOT_TOKEN;
    $url   = "https://api.telegram.org/bot{$token}/sendMessage";
    $data  = http_build_query(['chat_id' => $chat_id, 'text' => $text, 'parse_mode' => 'Markdown']);
    @file_get_contents($url . '?' . $data);
    http_response_code(200);
}
