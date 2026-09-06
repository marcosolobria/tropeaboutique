<?php require_once __DIR__ . '/auth.php'; requireAuth(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard — Tropea Boutique</title>
  <link rel="icon" href="/assets/logo.svg" type="image/svg+xml"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=Inter:wght@300;400;500&display=swap" rel="stylesheet"/>
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    :root{--bg:#0f0f0f;--surface:#1a1a1a;--surface2:#222;--border:rgba(255,255,255,.08);--pink:#d4607a;--pink-light:#F4A7B9;--text:#f0f0f0;--muted:rgba(255,255,255,.4);--radius:14px}
    body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text);display:flex;min-height:100vh}

    /* SIDEBAR */
    .sidebar{width:240px;flex-shrink:0;background:var(--surface);border-right:1px solid var(--border);display:flex;flex-direction:column;padding:24px 0;position:fixed;top:0;bottom:0;left:0;z-index:10}
    .sidebar-logo{padding:0 20px 24px;border-bottom:1px solid var(--border)}
    .sidebar-logo img{height:36px;filter:invert(1) brightness(2);opacity:.85}
    .sidebar-logo span{display:block;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--pink-light);margin-top:6px;opacity:.7}
    .nav-items{flex:1;padding:16px 0;overflow-y:auto}
    .nav-item{display:flex;align-items:center;gap:12px;padding:11px 20px;font-size:13px;font-weight:500;color:var(--muted);cursor:pointer;transition:all .15s;border-left:2px solid transparent}
    .nav-item:hover{color:var(--text);background:rgba(255,255,255,.04)}
    .nav-item.active{color:var(--pink-light);border-left-color:var(--pink);background:rgba(212,96,122,.08)}
    .nav-item .icon{font-size:16px;width:20px;text-align:center}
    .sidebar-footer{padding:16px 20px;border-top:1px solid var(--border)}
    .logout-btn{display:flex;align-items:center;gap:8px;font-size:12px;color:var(--muted);cursor:pointer;background:none;border:none;font-family:'Inter',sans-serif;transition:color .2s;padding:0}
    .logout-btn:hover{color:#fff}

    /* MAIN */
    .main{flex:1;margin-left:240px;display:flex;flex-direction:column;min-height:100vh}
    .topbar{padding:20px 32px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
    .topbar h2{font-family:'Playfair Display',serif;font-size:22px;font-weight:400}
    .topbar-right{font-size:12px;color:var(--muted)}
    .content{flex:1;padding:32px;overflow-y:auto}

    /* PANELS */
    .panel{display:none}
    .panel.active{display:block}

    /* CARDS */
    .card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:24px;margin-bottom:20px}
    .card-title{font-size:12px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--pink-light);margin-bottom:16px}

    /* STATS */
    .stats-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;margin-bottom:24px}
    .stat{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:20px;text-align:center}
    .stat-num{font-family:'Playfair Display',serif;font-size:36px;color:var(--pink-light)}
    .stat-label{font-size:11px;color:var(--muted);margin-top:4px;letter-spacing:.5px}

    /* CHAT */
    .chat-wrap{display:flex;flex-direction:column;height:calc(100vh - 180px)}
    .chat-messages{flex:1;overflow-y:auto;padding:16px 0;display:flex;flex-direction:column;gap:16px}
    .msg{display:flex;gap:12px;align-items:flex-start;max-width:85%}
    .msg.user{align-self:flex-end;flex-direction:row-reverse}
    .msg-avatar{width:32px;height:32px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:14px}
    .msg.ai .msg-avatar{background:rgba(212,96,122,.2);color:var(--pink-light)}
    .msg.user .msg-avatar{background:rgba(255,255,255,.1)}
    .msg-bubble{padding:12px 16px;border-radius:16px;font-size:14px;line-height:1.65}
    .msg.ai .msg-bubble{background:var(--surface2);border:1px solid var(--border);border-radius:4px 16px 16px 16px}
    .msg.user .msg-bubble{background:var(--pink);color:#fff;border-radius:16px 4px 16px 16px}
    .msg.ai .msg-bubble p{margin-bottom:8px} .msg.ai .msg-bubble p:last-child{margin-bottom:0}
    .chat-input-row{display:flex;gap:10px;padding-top:16px;border-top:1px solid var(--border);margin-top:auto}
    .chat-input{flex:1;background:var(--surface2);border:1px solid var(--border);border-radius:12px;padding:12px 16px;font-size:14px;color:var(--text);font-family:'Inter',sans-serif;outline:none;resize:none;min-height:48px;max-height:160px;transition:border-color .2s}
    .chat-input:focus{border-color:var(--pink)}
    .send-btn{background:var(--pink);border:none;border-radius:12px;padding:0 20px;color:#fff;cursor:pointer;font-size:18px;transition:background .2s;flex-shrink:0}
    .send-btn:hover{background:#c0506a}
    .send-btn:disabled{opacity:.4;cursor:not-allowed}
    .typing{display:none;align-items:center;gap:6px;padding:12px 16px;background:var(--surface2);border:1px solid var(--border);border-radius:4px 16px 16px 16px;width:fit-content}
    .typing span{width:7px;height:7px;background:var(--pink);border-radius:50%;animation:bounce .8s ease infinite}
    .typing span:nth-child(2){animation-delay:.15s}
    .typing span:nth-child(3){animation-delay:.3s}
    @keyframes bounce{0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)}}

    /* PRODUCTS */
    .products-toolbar{display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;align-items:center}
    .btn{padding:10px 18px;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;border:none;font-family:'Inter',sans-serif;transition:all .15s}
    .btn-primary{background:var(--pink);color:#fff} .btn-primary:hover{background:#c0506a}
    .btn-ghost{background:rgba(255,255,255,.06);color:var(--text);border:1px solid var(--border)} .btn-ghost:hover{background:rgba(255,255,255,.1)}
    .btn-danger{background:rgba(212,96,122,.15);color:var(--pink-light);border:1px solid rgba(212,96,122,.2)} .btn-danger:hover{background:rgba(212,96,122,.25)}
    .btn-sm{padding:6px 12px;font-size:12px;border-radius:8px}
    .products-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px}
    .product-card{background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius);padding:18px;position:relative}
    .product-card-name{font-family:'Playfair Display',serif;font-size:16px;margin-bottom:6px}
    .product-card-meta{font-size:12px;color:var(--muted);margin-bottom:12px}
    .product-card-actions{display:flex;gap:8px}
    .product-price{font-size:18px;font-weight:700;color:var(--pink-light);margin-bottom:12px}
    .product-badge{display:inline-block;font-size:9px;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:3px 8px;border-radius:20px;background:rgba(212,96,122,.15);color:var(--pink-light);margin-bottom:10px}

    /* FORM */
    .form-group{margin-bottom:16px}
    .form-group label{display:block;font-size:11px;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:var(--muted);margin-bottom:7px}
    .form-group input,.form-group textarea,.form-group select{width:100%;background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:11px 14px;font-size:14px;color:var(--text);font-family:'Inter',sans-serif;outline:none;transition:border-color .2s}
    .form-group input:focus,.form-group textarea:focus,.form-group select:focus{border-color:var(--pink)}
    .form-group textarea{resize:vertical;min-height:90px}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}

    /* MODAL */
    .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:100;align-items:center;justify-content:center;padding:20px;backdrop-filter:blur(4px)}
    .modal-overlay.open{display:flex}
    .modal{background:var(--surface);border:1px solid var(--border);border-radius:20px;width:100%;max-width:560px;max-height:90vh;overflow-y:auto;padding:32px}
    .modal-title{font-family:'Playfair Display',serif;font-size:22px;margin-bottom:24px}
    .modal-actions{display:flex;gap:10px;margin-top:24px;justify-content:flex-end}

    /* MESSAGES */
    .msg-card{background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius);padding:20px;margin-bottom:12px}
    .msg-card-header{display:flex;justify-content:space-between;align-items:start;margin-bottom:10px}
    .msg-card-name{font-weight:600;font-size:15px}
    .msg-card-date{font-size:11px;color:var(--muted)}
    .msg-card-email{font-size:12px;color:var(--pink-light);margin-bottom:10px}
    .msg-card-body{font-size:14px;color:rgba(255,255,255,.7);line-height:1.65}

    /* BLOG */
    .blog-list-item{display:flex;align-items:center;justify-content:space-between;padding:14px 16px;background:var(--surface2);border:1px solid var(--border);border-radius:10px;margin-bottom:10px}
    .blog-item-title{font-size:14px;font-weight:500}
    .blog-item-date{font-size:11px;color:var(--muted);margin-top:3px}
    .blog-item-actions{display:flex;gap:8px}

    /* ALERT */
    .alert{padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:16px}
    .alert-success{background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.2);color:#86efac}
    .alert-error{background:rgba(212,96,122,.1);border:1px solid rgba(212,96,122,.2);color:var(--pink-light)}

    /* QUICK SUGGESTIONS */
    .quick-suggestions{display:flex;gap:8px;flex-wrap:wrap;padding:12px 0 8px;border-top:1px solid var(--border)}
    .quick-suggestions button{background:rgba(212,96,122,.1);border:1px solid rgba(212,96,122,.2);color:var(--pink-light);border-radius:20px;padding:7px 14px;font-size:12px;font-family:'Inter',sans-serif;cursor:pointer;transition:all .15s;white-space:nowrap}
    .quick-suggestions button:hover{background:rgba(212,96,122,.22);border-color:var(--pink)}

    /* EMPTY */
    .empty{text-align:center;padding:60px 20px;color:var(--muted);font-size:14px}
    .empty-icon{font-size:48px;display:block;margin-bottom:12px}

    @media(max-width:768px){
      .sidebar{transform:translateX(-100%)} .main{margin-left:0}
      .form-row{grid-template-columns:1fr}
    }
  </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <img src="/assets/logo.svg" alt="Tropea Boutique"/>
    <span>Admin Panel</span>
  </div>
  <nav class="nav-items">
    <div class="nav-item active" data-panel="home">
      <span class="icon">🏠</span> Inicio
    </div>
    <div class="nav-item" data-panel="chat">
      <span class="icon">✨</span> Asistente IA
    </div>
    <div class="nav-item" data-panel="products">
      <span class="icon">🛍️</span> Productos
    </div>
    <div class="nav-item" data-panel="blog">
      <span class="icon">📖</span> Blog / Journal
    </div>
    <div class="nav-item" data-panel="messages">
      <span class="icon">💌</span> Mensajes
    </div>
    <div class="nav-item" data-panel="settings">
      <span class="icon">⚙️</span> Ajustes
    </div>
  </nav>
  <div class="sidebar-footer">
    <button class="logout-btn" onclick="logout()">← Cerrar sesión</button>
  </div>
</aside>

<!-- MAIN -->
<main class="main">
  <div class="topbar">
    <h2 id="panel-title">Inicio</h2>
    <div class="topbar-right">Tropea Boutique · <?= date('d M Y') ?></div>
  </div>
  <div class="content">

    <!-- HOME -->
    <div class="panel active" id="panel-home">
      <div class="stats-row">
        <div class="stat"><div class="stat-num" id="stat-products">—</div><div class="stat-label">Productos</div></div>
        <div class="stat"><div class="stat-num" id="stat-posts">—</div><div class="stat-label">Artículos blog</div></div>
        <div class="stat"><div class="stat-num" id="stat-messages">—</div><div class="stat-label">Mensajes</div></div>
      </div>
      <div class="card">
        <div class="card-title">Acciones rápidas</div>
        <div style="display:flex;gap:10px;flex-wrap:wrap">
          <button class="btn btn-primary" onclick="showPanel('products')">+ Nuevo producto</button>
          <button class="btn btn-ghost" onclick="showPanel('blog')">✍️ Nuevo artículo</button>
          <button class="btn btn-ghost" onclick="showPanel('chat')">✨ Consultar a la IA</button>
          <a href="/" target="_blank" class="btn btn-ghost" style="text-decoration:none">🌐 Ver tienda</a>
        </div>
      </div>
      <div class="card">
        <div class="card-title">Bienvenida 🌸</div>
        <p style="font-size:14px;color:rgba(255,255,255,.6);line-height:1.7">Hola! Desde aquí puedes gestionar toda tu tienda Tropea. Usa el <strong style="color:var(--pink-light)">Asistente IA</strong> para ayudarte con textos, ideas de productos, respuestas a clientes y mucho más.</p>
      </div>
    </div>

    <!-- CHAT IA -->
    <div class="panel" id="panel-chat">
      <div class="chat-wrap">
        <div class="chat-messages" id="chat-messages">
          <div class="msg ai">
            <div class="msg-avatar">✨</div>
            <div class="msg-bubble">¡Hola! Soy <strong>TROPEA AI</strong>, tu asistente especializada 🌸<br/><br/>Puedo ayudarte con marketing, descripciones de productos, contenido para Instagram, artículos del blog, estrategia de precios, respuestas a clientes y mucho más. ¿Por dónde empezamos?</div>
          </div>
        </div>
        <div class="typing" id="typing" style="display:none;padding:4px 0 8px">
          <div style="display:flex;align-items:center;gap:6px;padding:10px 14px;background:var(--surface2);border:1px solid var(--border);border-radius:4px 16px 16px 16px;width:fit-content">
            <span style="width:7px;height:7px;background:var(--pink);border-radius:50%;animation:bounce .8s ease infinite;display:block"></span>
            <span style="width:7px;height:7px;background:var(--pink);border-radius:50%;animation:bounce .8s ease .15s infinite;display:block"></span>
            <span style="width:7px;height:7px;background:var(--pink);border-radius:50%;animation:bounce .8s ease .3s infinite;display:block"></span>
          </div>
        </div>
        <!-- Sugerencias rápidas -->
        <div class="quick-suggestions" id="quick-suggestions">
          <button onclick="quickPrompt('Escribe un caption para Instagram de una nueva funda de móvil pintada a mano, con hashtags')">📱 Caption Instagram</button>
          <button onclick="quickPrompt('Dame 5 ideas creativas para una nueva colección de verano para Tropea Boutique')">🌊 Ideas colección</button>
          <button onclick="quickPrompt('Redacta la descripción de producto (FR y ES) para una funda de móvil con diseño tropical pintada a mano, precio 28€')">🛍️ Descripción producto</button>
          <button onclick="quickPrompt('Crea un plan de contenido para Instagram para esta semana (7 posts/stories)')">📅 Plan semanal</button>
          <button onclick="quickPrompt('Escribe una respuesta empática a un cliente que dice que su pedido lleva 10 días y no ha llegado')">💬 Respuesta cliente</button>
          <button onclick="quickPrompt('Dame estrategias para aumentar las ventas de Tropea Boutique en verano')">💰 Estrategia ventas</button>
          <button onclick="quickPrompt('Escribe un artículo de blog de 300 palabras sobre por qué regalar una pieza artesanal es más especial que un regalo de tienda')">✍️ Artículo blog</button>
          <button onclick="quickPrompt('Cómo debería preparar Tropea Boutique la campaña de San Valentín? Dame ideas concretas')">❤️ Campaña San Valentín</button>
          <button onclick="quickPrompt('Guíame paso a paso para dar de alta Tropea Boutique en Google Merchant Center y aparecer en Google Shopping')">🛒 Google Shopping</button>
          <button onclick="quickPrompt('Cómo configuro el catálogo de Tropea Boutique en Instagram Shopping y Facebook Shop? Explícame todos los pasos')">📲 Instagram & Facebook Shop</button>
          <button onclick="quickPrompt('Quiero abrir una tienda Etsy para Tropea Boutique. Dame los pasos, qué títulos y tags usar, y cómo destacar en artesanía hecha a mano')">🧵 Etsy Handmade</button>
          <button onclick="quickPrompt('Genera un feed de productos en formato CSV para Google Merchant Center con los accesorios de Tropea Boutique (fundas móvil y tote bags). Incluye todos los atributos obligatorios: id, title, description, link, image_link, price, availability, brand, google_product_category')">📊 Feed CSV Shopping</button>
        </div>
        <div class="chat-input-row">
          <textarea class="chat-input" id="chat-input" placeholder="Escribe tu mensaje... (Enter para enviar, Shift+Enter para nueva línea)" rows="1"></textarea>
          <button class="send-btn" id="send-btn" onclick="sendChat()">➤</button>
        </div>
      </div>
    </div>

    <!-- PRODUCTOS -->
    <div class="panel" id="panel-products">
      <div class="products-toolbar">
        <button class="btn btn-primary" onclick="openProductModal()">+ Añadir producto</button>
        <button class="btn btn-ghost" id="save-products-btn" onclick="saveProducts()" style="display:none">💾 Guardar cambios</button>
        <button class="btn btn-ghost" onclick="loadProducts()">↺ Recargar</button>
      </div>
      <div class="products-grid" id="products-grid">
        <div class="empty"><span class="empty-icon">🛍️</span>Cargando productos...</div>
      </div>
    </div>

    <!-- BLOG -->
    <div class="panel" id="panel-blog">
      <div style="display:flex;gap:10px;margin-bottom:20px">
        <button class="btn btn-primary" onclick="openBlogModal()">+ Nuevo artículo</button>
        <button class="btn btn-ghost" onclick="loadBlogPosts()">↺ Recargar</button>
      </div>
      <div id="blog-list"><div class="empty"><span class="empty-icon">📖</span>Cargando...</div></div>
    </div>

    <!-- MENSAJES -->
    <div class="panel" id="panel-messages">
      <div style="margin-bottom:20px">
        <button class="btn btn-ghost" onclick="loadMessages()">↺ Recargar mensajes</button>
      </div>
      <div id="messages-list"><div class="empty"><span class="empty-icon">💌</span>Cargando mensajes...</div></div>
    </div>

    <!-- AJUSTES -->
    <div class="panel" id="panel-settings">
      <div class="card" style="max-width:480px">
        <div class="card-title">Cambiar contraseña</div>
        <div id="pw-alert"></div>
        <div class="form-group"><label>Contraseña actual</label><input type="password" id="pw-current" placeholder="••••••"/></div>
        <div class="form-group"><label>Nueva contraseña</label><input type="password" id="pw-new" placeholder="••••••"/></div>
        <div class="form-group"><label>Confirmar nueva</label><input type="password" id="pw-confirm" placeholder="••••••"/></div>
        <button class="btn btn-primary" onclick="changePassword()">Cambiar contraseña</button>
      </div>
    </div>

  </div>
</main>

<!-- MODAL PRODUCTO -->
<div class="modal-overlay" id="product-modal">
  <div class="modal">
    <h3 class="modal-title" id="product-modal-title">Nuevo producto</h3>
    <input type="hidden" id="product-idx" value="-1"/>
    <div class="form-row">
      <div class="form-group"><label>Nombre</label><input type="text" id="p-name" placeholder="Coque Tropea Rose"/></div>
      <div class="form-group"><label>Precio (€)</label><input type="number" id="p-price" placeholder="25" step="0.5"/></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Colección</label><input type="text" id="p-collection" placeholder="Coques"/></div>
      <div class="form-group"><label>Etiqueta (opcional)</label><input type="text" id="p-badge" placeholder="Nouveau"/></div>
    </div>
    <div class="form-group"><label>Descripción FR</label><textarea id="p-desc-fr" placeholder="Description en français..."></textarea></div>
    <div class="form-group"><label>Descripción ES</label><textarea id="p-desc-es" placeholder="Descripción en español..."></textarea></div>
    <div class="form-group">
      <label>Foto del producto</label>
      <input type="file" id="p-photo" accept="image/*" onchange="uploadPhoto(this)"/>
      <p id="p-photo-status" style="margin:6px 0 0;font-size:13px;color:#888"></p>
      <img id="p-photo-preview" alt="" style="display:none;margin-top:10px;max-width:160px;border-radius:8px"/>
      <label style="margin-top:12px;display:block">…o pega una URL</label>
      <input type="text" id="p-image" placeholder="/assets/products/… o https://…" oninput="previewPhoto()"/>
      <p style="margin:6px 0 0;font-size:12px;color:#888">
        Los enlaces de Google Drive «para compartir» no muestran la foto en la web:
        sube el archivo aquí y se guarda en el propio servidor.
      </p>
    </div>
    <div class="modal-actions">
      <button class="btn btn-ghost" onclick="closeProductModal()">Cancelar</button>
      <button class="btn btn-primary" onclick="saveProduct()">Guardar producto</button>
    </div>
  </div>
</div>

<!-- MODAL BLOG -->
<div class="modal-overlay" id="blog-modal">
  <div class="modal">
    <h3 class="modal-title">Nuevo artículo</h3>
    <div class="form-group"><label>Título</label><input type="text" id="b-title" placeholder="Mes inspirations du moment..."/></div>
    <div class="form-group"><label>Categoría</label>
      <select id="b-tag">
        <option>Création · Artisanat</option>
        <option>Inspiration</option>
        <option>Coulisses</option>
        <option>Idées cadeaux</option>
        <option>Tote Bags · Peinture</option>
      </select>
    </div>
    <div class="form-group"><label>Contenido (HTML permitido)</label><textarea id="b-body" style="min-height:180px" placeholder="<p>Texto del artículo...</p>"></textarea></div>
    <div style="margin-bottom:16px">
      <button class="btn btn-ghost btn-sm" onclick="generateBlogWithAI()">✨ Generar con IA</button>
    </div>
    <div class="modal-actions">
      <button class="btn btn-ghost" onclick="closeBlogModal()">Cancelar</button>
      <button class="btn btn-primary" onclick="createBlogPost()">Publicar artículo</button>
    </div>
  </div>
</div>

<script>
// ── STATE ──────────────────────────────────────────────────────
let products = [];
let chatHistory = [];
let panelTitles = {home:'Inicio',chat:'Asistente IA ✨',products:'Productos',blog:'Blog / Journal',messages:'Mensajes',settings:'Ajustes'};

// ── NAV ────────────────────────────────────────────────────────
function showPanel(name) {
  document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  document.getElementById('panel-' + name).classList.add('active');
  document.querySelector(`[data-panel="${name}"]`).classList.add('active');
  document.getElementById('panel-title').textContent = panelTitles[name] || name;
  if (name === 'products') loadProducts();
  if (name === 'blog') loadBlogPosts();
  if (name === 'messages') loadMessages();
  if (name === 'home') loadStats();
}
document.querySelectorAll('.nav-item').forEach(el => {
  el.addEventListener('click', () => showPanel(el.dataset.panel));
});

function logout() {
  if (confirm('¿Cerrar sesión?')) window.location.href = '/admin/logout.php';
}

// ── API ────────────────────────────────────────────────────────
async function api(data) {
  const fd = new FormData();
  // Los ficheros van tal cual; lo demás, si es objeto, en JSON.
  Object.entries(data).forEach(([k,v]) =>
    fd.append(k, (v instanceof Blob) ? v : (typeof v === 'object' ? JSON.stringify(v) : v)));
  const res = await fetch('/admin/api.php', {method:'POST', body: fd});
  return res.json();
}

function showAlert(containerId, msg, type='success') {
  const el = document.getElementById(containerId);
  if (!el) return;
  el.innerHTML = `<div class="alert alert-${type}">${msg}</div>`;
  setTimeout(() => { el.innerHTML = ''; }, 4000);
}

// ── STATS ──────────────────────────────────────────────────────
async function loadStats() {
  const [p, b, m] = await Promise.all([
    api({action:'get_products'}),
    api({action:'get_blog_posts'}),
    api({action:'get_messages'}),
  ]);
  document.getElementById('stat-products').textContent = Array.isArray(p) ? p.length : '—';
  document.getElementById('stat-posts').textContent = Array.isArray(b) ? b.length : '—';
  document.getElementById('stat-messages').textContent = Array.isArray(m) ? m.length : '—';
}

// ── CHAT ──────────────────────────────────────────────────────
function renderMarkdown(text) {
  return text
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.*?)\*/g, '<em>$1</em>')
    .replace(/\n\n/g, '</p><p>')
    .replace(/\n/g, '<br/>')
    .replace(/^/, '<p>').replace(/$/, '</p>');
}

function addMessage(role, text) {
  const wrap = document.getElementById('chat-messages');
  const div = document.createElement('div');
  div.className = 'msg ' + role;
  div.innerHTML = `<div class="msg-avatar">${role==='user'?'👤':'✨'}</div><div class="msg-bubble">${role==='ai'?renderMarkdown(text):escHtml(text)}</div>`;
  wrap.appendChild(div);
  wrap.scrollTop = wrap.scrollHeight;
}

function escHtml(t){ return t.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

async function sendChat() {
  const input = document.getElementById('chat-input');
  const text = input.value.trim();
  if (!text) return;
  input.value = ''; input.style.height = 'auto';
  addMessage('user', text);
  chatHistory.push({role:'user', content: text});
  document.getElementById('send-btn').disabled = true;
  document.getElementById('typing').style.display = 'flex';
  document.getElementById('chat-messages').scrollTop = 99999;
  const res = await api({action:'chat', messages: chatHistory});
  document.getElementById('typing').style.display = 'none';
  document.getElementById('send-btn').disabled = false;
  const reply = res.reply || res.error || 'Error al conectar';
  addMessage('ai', reply);
  chatHistory.push({role:'assistant', content: reply});
}

document.getElementById('chat-input').addEventListener('keydown', e => {
  if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendChat(); }
});
document.getElementById('chat-input').addEventListener('input', function() {
  this.style.height = 'auto'; this.style.height = Math.min(this.scrollHeight, 160) + 'px';
});

// ── PRODUCTS ──────────────────────────────────────────────────
async function loadProducts() {
  const data = await api({action:'get_products'});
  products = Array.isArray(data) ? data : [];
  renderProducts();
}

function renderProducts() {
  const grid = document.getElementById('products-grid');
  if (!products.length) {
    grid.innerHTML = '<div class="empty"><span class="empty-icon">🛍️</span>No hay productos todavía.<br/>Añade el primero.</div>';
    return;
  }
  grid.innerHTML = products.map((p,i) => `
    <div class="product-card">
      ${p.badge ? `<div class="product-badge">${escHtml(p.badge)}</div>` : ''}
      <div class="product-card-name">${escHtml(p.name||p.name_fr||'')}</div>
      <div class="product-card-meta">${escHtml(p.collection||'')}</div>
      <div class="product-price">${p.price ? p.price+'€' : '—'}</div>
      <div class="product-card-actions">
        <button class="btn btn-ghost btn-sm" onclick="editProduct(${i})">✏️ Editar</button>
        <button class="btn btn-danger btn-sm" onclick="deleteProduct(${i})">🗑️ Borrar</button>
      </div>
    </div>`).join('');
  document.getElementById('save-products-btn').style.display = 'inline-flex';
}

function openProductModal(idx) {
  const p = idx !== undefined ? products[idx] : null;
  document.getElementById('product-modal-title').textContent = p ? 'Editar producto' : 'Nuevo producto';
  document.getElementById('product-idx').value = idx !== undefined ? idx : -1;
  document.getElementById('p-name').value = p?.name || p?.name_fr || '';
  document.getElementById('p-price').value = p?.price || '';
  document.getElementById('p-collection').value = p?.collection || '';
  document.getElementById('p-badge').value = p?.badge || '';
  document.getElementById('p-desc-fr').value = p?.desc_fr || p?.description || '';
  document.getElementById('p-desc-es').value = p?.desc_es || '';
  document.getElementById('p-image').value = p?.image || '';
  document.getElementById('p-photo').value = '';
  document.getElementById('p-photo-status').textContent = '';
  previewPhoto();
  document.getElementById('product-modal').classList.add('open');
}

function previewPhoto() {
  const url = document.getElementById('p-image').value.trim();
  const img = document.getElementById('p-photo-preview');
  img.style.display = url ? 'block' : 'none';
  if (url) img.src = url;
}

async function uploadPhoto(input) {
  const file = input.files && input.files[0];
  if (!file) return;
  const status = document.getElementById('p-photo-status');
  status.textContent = '⏳ Subiendo…';
  const res = await api({
    action: 'upload_image',
    photo: file,
    name: document.getElementById('p-name').value.trim() || 'producto',
  });
  if (res.url) {
    document.getElementById('p-image').value = res.url;
    previewPhoto();
    status.textContent = '✅ Subida. Recuerda pulsar «Guardar productos» al final.';
  } else {
    status.textContent = '❌ ' + (res.error || 'No se pudo subir');
    input.value = '';
  }
}
function closeProductModal() { document.getElementById('product-modal').classList.remove('open'); }
function editProduct(i) { openProductModal(i); }
function deleteProduct(i) {
  if (!confirm('¿Eliminar este producto?')) return;
  products.splice(i, 1);
  renderProducts();
}

function saveProduct() {
  const idx = parseInt(document.getElementById('product-idx').value);
  const p = {
    name: document.getElementById('p-name').value.trim(),
    name_fr: document.getElementById('p-name').value.trim(),
    price: parseFloat(document.getElementById('p-price').value) || 0,
    collection: document.getElementById('p-collection').value.trim(),
    badge: document.getElementById('p-badge').value.trim(),
    desc_fr: document.getElementById('p-desc-fr').value.trim(),
    desc_es: document.getElementById('p-desc-es').value.trim(),
    image: document.getElementById('p-image').value.trim(),
  };
  if (!p.name) { alert('El nombre es obligatorio'); return; }
  if (idx >= 0) products[idx] = p; else products.push(p);
  closeProductModal();
  renderProducts();
}

async function saveProducts() {
  const res = await api({action:'save_products', products});
  if (res.ok) showAlert('',''); // no container, use toast
  alert(res.ok ? '✅ Productos guardados' : '❌ Error: ' + res.error);
}

// ── BLOG ──────────────────────────────────────────────────────
async function loadBlogPosts() {
  const posts = await api({action:'get_blog_posts'});
  const el = document.getElementById('blog-list');
  if (!Array.isArray(posts) || !posts.length) {
    el.innerHTML = '<div class="empty"><span class="empty-icon">📖</span>No hay artículos todavía.</div>';
    return;
  }
  el.innerHTML = posts.map(p => `
    <div class="blog-list-item">
      <div><div class="blog-item-title">${escHtml(p.title)}</div><div class="blog-item-date">📅 ${p.modified}</div></div>
      <div class="blog-item-actions">
        <a href="/blog/${p.file}" target="_blank" class="btn btn-ghost btn-sm" style="text-decoration:none">🔗 Ver</a>
      </div>
    </div>`).join('');
}

function openBlogModal() {
  document.getElementById('b-title').value = '';
  document.getElementById('b-body').value = '';
  document.getElementById('blog-modal').classList.add('open');
}
function closeBlogModal() { document.getElementById('blog-modal').classList.remove('open'); }

async function createBlogPost() {
  const title = document.getElementById('b-title').value.trim();
  const body  = document.getElementById('b-body').value.trim();
  const tag   = document.getElementById('b-tag').value;
  if (!title || !body) { alert('Rellena título y contenido'); return; }
  const res = await api({action:'create_blog_post', title, body, tag});
  if (res.ok) { closeBlogModal(); loadBlogPosts(); alert('✅ Artículo publicado'); }
  else alert('❌ ' + res.error);
}

async function generateBlogWithAI() {
  const title = document.getElementById('b-title').value.trim();
  if (!title) { alert('Escribe primero el título'); return; }
  const btn = event.target;
  btn.textContent = '⏳ Generando...'; btn.disabled = true;
  chatHistory = [];
  const res = await api({action:'chat', messages:[{role:'user',content:`Escribe el contenido HTML de un artículo de blog para Tropea Boutique sobre: "${title}". Usa párrafos <p> y subtítulos <h2>. Tono cálido, inspirador, en francés. Unas 300 palabras.`}]});
  btn.textContent = '✨ Generar con IA'; btn.disabled = false;
  if (res.reply) document.getElementById('b-body').value = res.reply;
  else alert('Error: ' + res.error);
}

// ── MESSAGES ──────────────────────────────────────────────────
async function loadMessages() {
  const msgs = await api({action:'get_messages'});
  const el = document.getElementById('messages-list');
  if (!Array.isArray(msgs) || !msgs.length) {
    el.innerHTML = '<div class="empty"><span class="empty-icon">💌</span>No hay mensajes todavía.</div>';
    return;
  }
  el.innerHTML = msgs.reverse().map(m => `
    <div class="msg-card">
      <div class="msg-card-header">
        <div>
          <div class="msg-card-name">${escHtml(m.name||'')}</div>
          <div class="msg-card-email">✉️ ${escHtml(m.email||'')}</div>
        </div>
        <div style="display:flex;gap:8px;align-items:center">
          <span class="msg-card-date">${m.date||''}</span>
          <button class="btn btn-danger btn-sm" onclick="deleteMessage(${m.id})">🗑️</button>
        </div>
      </div>
      ${m.type ? `<div style="margin-bottom:8px"><span style="font-size:11px;background:rgba(212,96,122,.15);color:var(--pink-light);padding:2px 8px;border-radius:20px">${escHtml(m.type)}</span></div>` : ''}
      <div class="msg-card-body">${escHtml(m.message||'')}</div>
    </div>`).join('');
}

async function deleteMessage(id) {
  if (!confirm('¿Eliminar mensaje?')) return;
  await api({action:'delete_message', id});
  loadMessages();
}

// ── PASSWORD ──────────────────────────────────────────────────
async function changePassword() {
  const res = await api({
    action:'change_password',
    current: document.getElementById('pw-current').value,
    new: document.getElementById('pw-new').value,
    confirm: document.getElementById('pw-confirm').value,
  });
  if (res.ok) {
    document.getElementById('pw-alert').innerHTML = '<div class="alert alert-success">✅ Contraseña cambiada correctamente</div>';
    document.getElementById('pw-current').value = '';
    document.getElementById('pw-new').value = '';
    document.getElementById('pw-confirm').value = '';
  } else {
    document.getElementById('pw-alert').innerHTML = `<div class="alert alert-error">❌ ${res.error}</div>`;
  }
}

// ── QUICK PROMPTS ─────────────────────────────────────────────
function quickPrompt(text) {
  document.getElementById('chat-input').value = text;
  document.getElementById('quick-suggestions').style.display = 'none';
  sendChat();
}

// Volver a mostrar sugerencias al borrar el input
document.getElementById('chat-input').addEventListener('input', function() {
  const qs = document.getElementById('quick-suggestions');
  if (!this.value.trim() && document.querySelectorAll('.msg').length <= 1) {
    qs.style.display = 'flex';
  }
});

// ── INIT ──────────────────────────────────────────────────────
loadStats();
</script>

</body>
</html>
