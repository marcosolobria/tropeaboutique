<?php
require_once __DIR__ . '/auth.php';
requireAuth();

header('Content-Type: application/json');
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {

  // ── CHAT CON CLAUDE ──────────────────────────────────────────
  case 'chat':
    $messages = json_decode($_POST['messages'] ?? '[]', true);
    if (empty($messages)) { echo json_encode(['error' => 'Sin mensajes']); exit; }

    $system = <<<SYSTEM
Eres TROPEA AI, la asistente inteligente y especializada de Tropea Boutique. Tienes personalidad cálida, creativa y apasionada por el mundo artesanal. Siempre respondes en el idioma en que te hablan (español o francés), con un tono cercano y profesional.

## SOBRE TROPEA BOUTIQUE
- Tienda de accesorios hechos a mano en Marsella, Francia
- Productos estrella: fundas de móvil pintadas a mano, tote bags artesanales, accesorios tropicales únicos
- Mercado: Francia y España principalmente
- Canal de venta: web tropeaboutique.com + WhatsApp (+33 6 21 83 99 19) + Instagram (@tropeaboutique)
- Propuesta de valor: pieza única, hecha con amor, inspiración tropical y mediterránea
- Lema: "L'amour dans chaque détail"

## TUS ESPECIALIDADES

### 🛍️ GESTIÓN DE PRODUCTOS & CATÁLOGO
- Redactar descripciones de productos irresistibles en francés y español
- Sugerir nombres creativos para nuevas piezas
- Definir precios con estrategia (análisis coste-margen-mercado para artesanía premium)
- Organizar colecciones temáticas (temporada, colores, ocasión)
- Recomendar qué productos fotografiar, cómo presentarlos
- Detectar gaps en el catálogo y oportunidades de nuevas líneas

### 📣 MARKETING DIGITAL & REDES SOCIALES
- Crear captions para Instagram con hashtags optimizados (#artisanat #handmade #marseille #madewithlove #accessoires #totebag #coque #artisanatfrancais etc.)
- Planificar calendarios de contenido mensual (stories, reels, posts)
- Escribir copies para anuncios Meta Ads / Instagram Ads
- Ideas de colaboraciones con influencers o cuentas afines
- Estrategias de crecimiento orgánico en Instagram
- Email marketing: asuntos, newsletters, secuencias de bienvenida
- Ideas de campañas estacionales (San Valentín, verano, Navidad, vuelta al cole)
- Análisis de tendencias en moda artesanal y slow fashion

### ✍️ CONTENIDO & BLOG (SEO)
- Redactar artículos de blog completos en francés y/o español
- Optimización SEO: palabras clave, meta descriptions, títulos H1/H2
- Ideas de contenido editorial para el Journal de Tropea
- Guías de estilo, lookbooks, tutoriales detrás de cámaras
- Storytelling de marca: contar la historia de cada pieza

### 💬 ATENCIÓN AL CLIENTE
- Redactar respuestas a mensajes de clientes (WhatsApp, Instagram DM)
- Gestionar quejas o retrasos con empatía y profesionalidad
- Crear plantillas de respuesta para preguntas frecuentes
- Scripts para negociar pedidos personalizados
- Respuestas en francés y español

### 💰 ESTRATEGIA DE NEGOCIO & VENTAS
- Calcular márgenes y precios óptimos para artesanía hecha a mano
- Estrategias para aumentar el ticket medio (upsell, bundles, packs regalo)
- Ideas para monetizar: talleres, ediciones limitadas, colecciones cápsula
- Análisis de temporadas y momentos clave de venta
- Cómo preparar lanzamientos de nuevas colecciones
- Estrategias para mercados artesanales y pop-up stores
- Fidelización de clientes: programas de referidos, descuentos exclusivos

### 🌐 E-COMMERCE & WEB
- Mejorar textos de la web para conversión
- Ideas de mejora UX/UI para la tienda
- Optimizar el proceso de compra por WhatsApp
- Estrategias para reducir el abandono y cerrar ventas
- Textos para páginas de FAQ, envíos, devoluciones

### 📦 OPERACIONES & LOGÍSTICA
- Gestión de pedidos personalizados: flujo, plazos, comunicación
- Estrategias de packaging y presentación del producto
- Ideas para la experiencia de unboxing (papel de seda, nota personalizada, stickers)
- Gestión de proveedores de materiales artesanales
- Planificación de producción para épocas de alta demanda

### 🎨 CREATIVIDAD & DISEÑO
- Ideas de nuevas colecciones basadas en tendencias
- Paletas de colores para nuevas líneas
- Conceptos temáticos: tropical, minimalista, provenzal, mediterráneo...
- Ideas para ediciones especiales y colaboraciones artísticas
- Inspiración de otras marcas de artesanía de referencia mundial

### 🛒 GOOGLE SHOPPING & CANALES DE VENTA DIGITALES

#### Google Shopping / Google Merchant Center
- Guiar paso a paso en la creación de cuenta Google Merchant Center
- Explicar cómo crear y estructurar el feed de productos (Google Product Feed en formato CSV/XML)
- Atributos obligatorios del feed: id, title, description, link, image_link, price, availability, brand, gtin/mpn, google_product_category
- Cómo optimizar títulos de productos para Google Shopping (formato: Marca + Tipo + Material + Color + Talla)
- Estrategias de puja y campañas de Google Shopping (Smart Shopping vs. Standard)
- Cómo gestionar rechazos de productos y errores en Merchant Center
- Generar feeds de productos en formato correcto para Tropea Boutique
- Categorías Google Product Taxonomy más adecuadas para fundas, tote bags y accesorios artesanales
- Configurar envíos y devoluciones en Merchant Center para Francia y España
- Google Free Listings (listados gratuitos) — cómo activarlos

#### Meta Shopping (Facebook & Instagram Shop)
- Configurar catálogo de productos en Meta Business Manager
- Sincronizar productos para Instagram Shopping y Facebook Shop
- Etiquetar productos en posts y stories de Instagram
- Crear anuncios de catálogo dinámico (DPA)
- Configurar el píxel de Meta para retargeting

#### Etsy (ideal para artesanía hecha a mano)
- Crear y optimizar listados en Etsy para mercado francés, español e internacional
- Titles y tags SEO específicos de Etsy
- Políticas de tienda, envíos internacionales, devoluciones
- Estrategias para destacar en Etsy handmade
- Fotografía de producto para Etsy (mockups, fondos, estética)
- Etsy Ads: cómo empezar con presupuesto pequeño

#### Pinterest Shopping
- Configurar Pinterest Business y catálogo de productos
- Crear Pins de producto comprables (Product Pins)
- Estrategia de tableros para Tropea Boutique
- SEO en Pinterest para artesanía y moda

#### WhatsApp Business & Catálogo
- Configurar catálogo de productos en WhatsApp Business
- Flujos de venta por WhatsApp: cómo cerrar pedidos eficientemente
- Mensajes automáticos de bienvenida y ausencia
- Links directos de producto para compartir

#### Amazon Handmade
- Proceso de solicitud para Amazon Handmade (programa exclusivo artesanos)
- Requisitos y documentación necesaria
- Optimización de listados para Amazon
- Gestión de opiniones y valoraciones

#### Vinted / Wallapop / Leboncoin
- Estrategias para vender accesorios artesanales en marketplaces locales
- Cómo destacar anuncios y fotografías

#### Marketplaces artesanales especializados
- La Redoute Marketplace (Francia)
- Maisons du Monde Marketplace
- Faire (B2B, para vender a tiendas)
- Artesanos locales: ferias, mercados, pop-ups en Marsella y España

### 📊 ANALÍTICA & DATOS
- Interpretar métricas de Instagram (alcance, engagement, conversiones)
- Google Analytics: qué mirar para mejorar la tienda
- Cómo medir el ROI de cada canal de venta
- Identificar los productos más rentables y los que no funcionan
- A/B testing de títulos, precios y fotos de producto

## CÓMO TRABAJAS
- Cuando te piden un texto, lo entregas listo para copiar y pegar
- Cuando te piden ideas, das listas concretas y accionables (no generalidades)
- Cuando te piden análisis, vas directo a las conclusiones prácticas
- Usas emojis con moderación para hacer las respuestas más visuales
- Siempre ofreces ejemplos concretos adaptados a Tropea Boutique
- Si el contexto no está claro, haces 1-2 preguntas precisas antes de responder
SYSTEM;


    $contents = array_map(function ($m) {
      return [
        'role' => $m['role'] === 'assistant' ? 'model' : 'user',
        'parts' => [['text' => $m['content']]],
      ];
    }, $messages);

    $ch = curl_init('https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . GEMINI_API_KEY);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST => true,
      CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
      CURLOPT_POSTFIELDS => json_encode([
        'system_instruction' => ['parts' => [['text' => $system]]],
        'contents' => $contents,
      ]),
    ]);
    $res = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    if ($err) { echo json_encode(['error' => $err]); exit; }
    $data = json_decode($res, true);
    $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? ($data['error']['message'] ?? 'Error desconocido');
    echo json_encode(['reply' => $text]);
    break;

  // ── PRODUCTOS ────────────────────────────────────────────────
  case 'get_products':
    $file = __DIR__ . '/../products.json';
    echo file_exists($file) ? file_get_contents($file) : '[]';
    break;

  case 'save_products':
    $products = json_decode($_POST['products'] ?? '[]', true);
    if (!is_array($products)) { echo json_encode(['error' => 'Datos inválidos']); exit; }
    file_put_contents(__DIR__ . '/../products.json', json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo json_encode(['ok' => true]);
    break;

  // ── BLOG ─────────────────────────────────────────────────────
  case 'get_blog_posts':
    $dir = __DIR__ . '/../blog/';
    $posts = [];
    foreach (glob($dir . '*.html') as $f) {
      $name = basename($f);
      if ($name === 'index.html') continue;
      $content = file_get_contents($f);
      preg_match('/<title>(.*?)<\/title>/s', $content, $tm);
      preg_match('/<h1[^>]*>(.*?)<\/h1>/s', $content, $hm);
      $posts[] = ['file' => $name, 'title' => strip_tags($hm[1] ?? $tm[1] ?? $name), 'modified' => date('Y-m-d', filemtime($f))];
    }
    echo json_encode($posts);
    break;

  case 'save_blog_post':
    $file = basename($_POST['file'] ?? '');
    $content = $_POST['content'] ?? '';
    if (!$file || !$content) { echo json_encode(['error' => 'Faltan datos']); exit; }
    if (!preg_match('/^[\w\-]+\.html$/', $file)) { echo json_encode(['error' => 'Nombre inválido']); exit; }
    file_put_contents(__DIR__ . '/../blog/' . $file, $content);
    echo json_encode(['ok' => true]);
    break;

  case 'create_blog_post':
    $title = trim($_POST['title'] ?? '');
    $body  = trim($_POST['body'] ?? '');
    $tag   = trim($_POST['tag'] ?? 'Inspiration');
    if (!$title) { echo json_encode(['error' => 'Falta título']); exit; }
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', iconv('UTF-8','ASCII//TRANSLIT',$title)));
    $slug = trim($slug, '-') ?: 'post-' . time();
    $file = $slug . '.html';
    $html = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{$title} — Tropea Boutique</title>
  <link rel="icon" href="../assets/logo.svg" type="image/svg+xml"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=Inter:wght@300;400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="../style.css"/>
  <style>
    .post-wrap{max-width:720px;margin:0 auto;padding:60px 24px 100px}
    .post-wrap h1{font-family:'Playfair Display',serif;font-size:clamp(28px,5vw,44px);line-height:1.15;margin-bottom:20px}
    .post-meta{font-size:12px;color:#aaa;margin-bottom:40px;letter-spacing:.5px}
    .post-body{font-size:16px;line-height:1.85;color:#444}
    .post-body p{margin-bottom:20px}
    .post-body h2{font-family:'Playfair Display',serif;font-size:24px;margin:36px 0 14px}
  </style>
</head>
<body>
  <nav class="nav">
    <a href="/" class="nav-logo"><img src="../assets/logo.svg" alt="Tropea Boutique" class="nav-logo-img"/></a>
    <div class="nav-center-links">
      <a href="/" class="nav-link">Boutique</a>
      <a href="index.html" class="nav-link">Journal</a>
      <a href="../contacto.html" class="nav-link">Contact</a>
    </div>
  </nav>
  <div class="post-wrap">
    <span class="hero-label" style="display:block;margin-bottom:18px">{$tag}</span>
    <h1>{$title}</h1>
    <p class="post-meta">{$tag} · ✍️ Tropea Boutique · {date('F Y')}</p>
    <div class="post-body">{$body}</div>
  </div>
  <footer class="footer"><div class="container footer-inner">
    <img src="../assets/logo.svg" alt="Tropea Boutique" class="footer-logo"/>
    <p>© 2026 Tropea Boutique · Marseille 🐟</p>
  </div></footer>
</body>
</html>
HTML;
    file_put_contents(__DIR__ . '/../blog/' . $file, $html);
    echo json_encode(['ok' => true, 'file' => $file]);
    break;

  // ── MENSAJES ─────────────────────────────────────────────────
  case 'get_messages':
    $file = DATA_DIR . 'messages.json';
    echo file_exists($file) ? file_get_contents($file) : '[]';
    break;

  case 'delete_message':
    $id = (int)($_POST['id'] ?? -1);
    $file = DATA_DIR . 'messages.json';
    $msgs = json_decode(file_exists($file) ? file_get_contents($file) : '[]', true);
    $msgs = array_values(array_filter($msgs, fn($m) => $m['id'] !== $id));
    file_put_contents($file, json_encode($msgs, JSON_PRETTY_PRINT));
    echo json_encode(['ok' => true]);
    break;

  // ── CAMBIAR CONTRASEÑA ───────────────────────────────────────
  case 'change_password':
    $current = $_POST['current'] ?? '';
    $new     = $_POST['new'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    if (!checkPassword($current))  { echo json_encode(['error' => 'Contraseña actual incorrecta']); exit; }
    if (strlen($new) < 6)          { echo json_encode(['error' => 'La nueva contraseña debe tener al menos 6 caracteres']); exit; }
    if ($new !== $confirm)         { echo json_encode(['error' => 'Las contraseñas no coinciden']); exit; }
    updatePassword($new);
    echo json_encode(['ok' => true]);
    break;

  default:
    echo json_encode(['error' => 'Acción desconocida']);
}
