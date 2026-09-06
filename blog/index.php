<?php
// El listado del Journal se genera leyendo la carpeta. Antes estaba escrito a
// mano: los artículos que Maria creaba desde el panel se guardaban aquí pero no
// aparecían en ninguna parte, así que no los encontraba nadie.

function trozo(string $html, string $patron): string {
    return preg_match($patron, $html, $m) ? trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES, 'UTF-8')) : '';
}

$MESES = [1=>'Janvier','Février','Mars','Avril','Mai','Juin',
          'Juillet','Août','Septembre','Octobre','Novembre','Décembre'];

$posts = [];
foreach (glob(__DIR__ . '/*.html') as $ruta) {
    $fichero = basename($ruta);
    if ($fichero === 'index.html') continue;
    $html = file_get_contents($ruta);

    $titulo = trozo($html, '~<h1[^>]*>(.*?)</h1>~s') ?: trozo($html, '~<title>(.*?)(?:\s*—.*?)?</title>~s');
    if ($titulo === '') continue;

    // La etiqueta puede venir del artículo del panel (hero-label) o del manual.
    $tag = trozo($html, '~class="hero-label"[^>]*>(.*?)</span>~s')
        ?: trozo($html, '~class="(?:article-tag|blog-tag|featured-label)"[^>]*>(.*?)</span>~s')
        ?: 'Journal';

    // Entradilla: la meta description si la hay; si no, el primer párrafo real.
    $extracto = trozo($html, '~<meta name="description" content="([^"]*)"~');
    if ($extracto === '') {
        preg_match_all('~<p(?![^>]*class="(?:article-meta|post-meta)")[^>]*>(.*?)</p>~s', $html, $ms);
        $extracto = isset($ms[1][0]) ? trim(html_entity_decode(strip_tags($ms[1][0]), ENT_QUOTES, 'UTF-8')) : '';
    }
    if (mb_strlen($extracto) > 170) $extracto = mb_substr($extracto, 0, 167) . '…';

    // Fecha: la que trae escrita el artículo manda sobre la del fichero. Ordenar
    // por filemtime a secas es una trampa: volver a desplegar un artículo viejo
    // lo pondría de portada.
    $texto = trozo($html, '~class="(?:article-meta|post-meta)"[^>]*>(.*?)</p>~s');
    $clave = 0;
    if (preg_match('~(' . implode('|', $MESES) . ')\s+(\d{4})~ui', $texto, $m)) {
        $mes   = array_search(ucfirst(mb_strtolower($m[1])), $MESES, true) ?: 1;
        $fecha = ucfirst(mb_strtolower($m[1])) . ' ' . $m[2];
        $clave = ((int) $m[2]) * 100 + $mes;
    } else {
        $fecha = $MESES[(int) date('n', filemtime($ruta))] . ' ' . date('Y', filemtime($ruta));
        $clave = ((int) date('Y', filemtime($ruta))) * 100 + (int) date('n', filemtime($ruta));
    }

    $posts[] = [
        'file' => $fichero, 'titulo' => $titulo, 'tag' => $tag,
        'extracto' => $extracto, 'fecha' => $fecha,
        'clave' => $clave, 'orden' => filemtime($ruta),
    ];
}

// El más reciente arriba, y de portada.
// Por mes de publicación; dentro del mismo mes, lo último escrito arriba.
usort($posts, fn($a, $b) => [$b['clave'], $b['orden']] <=> [$a['clave'], $a['orden']]);

$EMOJIS  = ['🐚','🌺','🎁','🌊','✨','🎨','💌','🌴','🪸','🦋'];
$FONDOS  = ['bg-rose','bg-sand','bg-mint','bg-sky'];
$destacado = array_shift($posts);
$categorias = [];
foreach (array_merge($destacado ? [$destacado] : [], $posts) as $p) {
    foreach (array_map('trim', explode('·', $p['tag'])) as $c) {
        if ($c !== '') $categorias[$c] = true;
    }
}
$categorias = array_slice(array_keys($categorias), 0, 6);
function e($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Journal — Tropea Boutique | Inspirations & Coulisses</title>
  <meta name="description" content="Le journal de Tropea Boutique : inspirations, coulisses de création et conseils autour de l'artisanat fait main à Marseille."/>
  <link rel="icon" href="../assets/logo.svg" type="image/svg+xml"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=Inter:wght@300;400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="../style.css?v=20260906e"/>
  <style>
    /* ---- Blog page styles ---- */
    .blog-page { background: var(--cream); min-height: 100vh; }

    /* Hero */
    .blog-hero {
      padding: 80px 24px 64px; text-align: center;
      background: linear-gradient(150deg, #fff8fb 0%, #fdf0e6 100%);
      border-bottom: 1px solid rgba(0,0,0,.06);
    }
    .blog-hero .hero-label { display: block; margin-bottom: 18px; font-size: 11px; font-weight: 600; letter-spacing: 3.5px; text-transform: uppercase; color: var(--pink-dark); }
    .blog-hero h1 { font-family: 'Playfair Display', serif; font-size: clamp(36px,6vw,60px); line-height: 1.1; margin-bottom: 16px; }
    .blog-hero h1 em { color: var(--pink-dark); font-style: italic; }
    .blog-hero p { color: #666; font-size: 16px; line-height: 1.7; max-width: 480px; margin: 0 auto; }

    /* Category pills */
    .blog-categories { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; padding: 36px 24px 0; }
    .cat-pill {
      padding: 8px 20px; border-radius: 50px; font-size: 12px; font-weight: 600;
      letter-spacing: .5px; cursor: pointer; transition: all .2s;
      border: 1.5px solid rgba(0,0,0,.1); background: white; color: #666;
    }
    .cat-pill.active, .cat-pill:hover { background: var(--black); color: white; border-color: var(--black); }

    /* Featured article */
    .blog-featured { max-width: 1100px; margin: 48px auto 0; padding: 0 24px; }
    .featured-card {
      display: grid; grid-template-columns: 1fr 1fr; border-radius: 24px;
      overflow: hidden; background: white; box-shadow: 0 8px 40px rgba(0,0,0,.08);
      text-decoration: none; color: var(--black); transition: transform .3s, box-shadow .3s;
    }
    .featured-card:hover { transform: translateY(-6px); box-shadow: 0 20px 60px rgba(0,0,0,.13); }
    .featured-img {
      background: linear-gradient(135deg, #fce8f3, #fdf0e6);
      display: flex; align-items: center; justify-content: center;
      font-size: 100px; min-height: 340px;
    }
    .featured-body { padding: 48px 40px; display: flex; flex-direction: column; justify-content: center; gap: 16px; }
    .featured-label { font-size: 10px; font-weight: 700; letter-spacing: 2.5px; text-transform: uppercase; color: var(--pink-dark); }
    .featured-body h2 { font-family: 'Playfair Display', serif; font-size: clamp(22px,3vw,30px); line-height: 1.25; }
    .featured-body p { font-size: 15px; color: #666; line-height: 1.75; }
    .featured-meta { display: flex; align-items: center; gap: 16px; font-size: 12px; color: #999; margin-top: 4px; }
    .read-btn { display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: var(--pink-dark); letter-spacing: .3px; margin-top: 8px; }
    .read-btn:hover { gap: 12px; }
    @media (max-width: 700px) { .featured-card { grid-template-columns: 1fr; } .featured-img { min-height: 220px; font-size: 72px; } .featured-body { padding: 28px; } }

    /* Articles grid */
    .blog-section-title { max-width: 1100px; margin: 60px auto 24px; padding: 0 24px; font-family: 'Playfair Display', serif; font-size: 22px; color: #999; font-weight: 400; font-style: italic; }
    .blog-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px,1fr)); gap: 28px; padding: 0 24px 100px; max-width: 1100px; margin: 0 auto; }
    .blog-card {
      background: white; border-radius: 20px; overflow: hidden;
      box-shadow: 0 4px 24px rgba(0,0,0,.07);
      transition: transform .25s, box-shadow .25s;
      text-decoration: none; color: var(--black); display: flex; flex-direction: column;
    }
    .blog-card:hover { transform: translateY(-6px); box-shadow: 0 16px 48px rgba(0,0,0,.12); }
    .blog-card-img {
      height: 220px; display: flex; align-items: center; justify-content: center;
      font-size: 72px; position: relative;
    }
    .blog-card-img.bg-rose  { background: linear-gradient(135deg, #fce8f3, #fdf0e6); }
    .blog-card-img.bg-sand  { background: linear-gradient(135deg, #fdf0e6, #fce8d3); }
    .blog-card-img.bg-mint  { background: linear-gradient(135deg, #e8f5ef, #edf8f2); }
    .blog-card-img.bg-sky   { background: linear-gradient(135deg, #e8f0fd, #eaf5ff); }
    .blog-card-body { padding: 26px; flex: 1; display: flex; flex-direction: column; gap: 10px; }
    .blog-tag { font-size: 10px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: var(--pink-dark); }
    .blog-card h2 { font-family: 'Playfair Display', serif; font-size: 19px; line-height: 1.3; }
    .blog-card p { font-size: 13px; color: #777; line-height: 1.7; flex: 1; }
    .blog-card-footer { display: flex; align-items: center; justify-content: space-between; margin-top: 8px; }
    .blog-date { font-size: 11px; color: #bbb; }
    .read-more { font-size: 12px; font-weight: 600; color: var(--pink-dark); letter-spacing: .3px; }

    /* CTA strip */
    .blog-cta {
      background: var(--black); color: white; text-align: center;
      padding: 64px 24px; margin-bottom: 0;
    }
    .blog-cta h2 { font-family: 'Playfair Display', serif; font-size: clamp(24px,4vw,38px); margin-bottom: 12px; }
    .blog-cta p { color: rgba(255,255,255,.65); font-size: 15px; margin-bottom: 32px; }
    .blog-cta .btn-primary { background: var(--pink-dark); }
    .blog-cta .btn-primary:hover { background: #c0506a; }
  </style>
</head>
<body class="blog-page">

  <!-- NAV -->
  <nav class="nav">
    <a href="/" class="nav-logo"><img src="../assets/logo.svg" alt="Tropea Boutique" class="nav-logo-img"/></a>
    <div class="nav-center-links">
      <a href="/" class="nav-link" data-fr="Boutique" data-es="Tienda" data-en="Shop">Boutique</a>
      <a href="../sobre-mi.html" class="nav-link" data-fr="Qui suis-je" data-es="Sobre mí" data-en="About me">Qui suis-je</a>
      <a href="./" class="nav-link" style="color:var(--black);font-weight:600" data-fr="Journal" data-es="Blog" data-en="Journal">Journal</a>
      <a href="../contacto.html" class="nav-link" data-fr="Contact" data-es="Contacto" data-en="Contact">Contact</a>
    </div>
    <div class="nav-right">
      <div class="nav-langs">
        <button class="lang-btn" data-lang="fr">FR</button>
        <span class="lang-sep">|</span>
        <button class="lang-btn" data-lang="es">ES</button>
        <span class="lang-sep">|</span>
        <button class="lang-btn" data-lang="en">EN</button>
      </div>
      <a href="https://www.instagram.com/tropeaboutique" target="_blank" class="nav-ig">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
      </a>
    </div>
  </nav>

  <!-- HERO -->
  <div class="blog-hero">
    <span class="hero-label" data-fr="Journal" data-es="Blog" data-en="Journal">Journal</span>
    <h1 data-fr="Nos histoires<br/><em>d&#39;amour</em> artisanal" data-es="Nuestras historias<br/>de <em>amor</em> artesanal" data-en="Our handmade<br/><em>love</em> stories">Nos histoires<br/><em>d'amour</em> artisanal</h1>
    <p data-fr="Inspirations, coulisses de création et la poésie derrière chaque pièce faite main." data-es="Inspiraciones, la trastienda del taller y la poesía que hay detrás de cada pieza hecha a mano." data-en="Inspiration, behind the scenes and the poetry behind every handmade piece.">Inspirations, coulisses de création et la poésie derrière chaque pièce faite main.</p>
  </div>

  <!-- CATEGORIES -->
  <div class="blog-categories">
    <button class="cat-pill active" data-cat="*" data-fr="Tout" data-es="Todo" data-en="All">Tout</button>
<?php foreach ($categorias as $c): ?>
    <button class="cat-pill" data-cat="<?= e($c) ?>"><?= e($c) ?></button>
<?php endforeach; ?>
  </div>

<?php if ($destacado): ?>
  <!-- FEATURED -->
  <div class="blog-featured">
    <a class="featured-card" href="<?= e($destacado['file']) ?>" data-tag="<?= e($destacado['tag']) ?>">
      <div class="featured-img"><?= $EMOJIS[crc32($destacado['file']) % count($EMOJIS)] ?></div>
      <div class="featured-body">
        <span class="featured-label">À la une · <?= e($destacado['tag']) ?></span>
        <h2><?= e($destacado['titulo']) ?></h2>
        <p><?= e($destacado['extracto']) ?></p>
        <div class="featured-meta"><span><?= e($destacado['fecha']) ?></span></div>
        <span class="read-btn" data-fr="Lire l&#39;article →" data-es="Leer el artículo →" data-en="Read the article →">Lire l'article →</span>
      </div>
    </a>
  </div>
<?php endif; ?>

<?php if ($posts): ?>
  <!-- AUTRES ARTICLES -->
  <p class="blog-section-title" data-fr="Autres histoires" data-es="Otras historias" data-en="More stories">Autres histoires</p>
  <div class="blog-grid">
<?php foreach ($posts as $i => $p): ?>
    <a class="blog-card" href="<?= e($p['file']) ?>" data-tag="<?= e($p['tag']) ?>">
      <div class="blog-card-img <?= $FONDOS[$i % count($FONDOS)] ?>"><?= $EMOJIS[crc32($p['file']) % count($EMOJIS)] ?></div>
      <div class="blog-card-body">
        <span class="blog-tag"><?= e($p['tag']) ?></span>
        <h2><?= e($p['titulo']) ?></h2>
        <p><?= e($p['extracto']) ?></p>
        <div class="blog-card-footer">
          <span class="blog-date"><?= e($p['fecha']) ?></span>
          <span class="read-more" data-fr="Lire →" data-es="Leer →" data-en="Read →">Lire →</span>
        </div>
      </div>
    </a>
<?php endforeach; ?>
  </div>
<?php endif; ?>

  <!-- FOOTER -->
  <footer class="footer">
    <div class="container footer-inner">
      <img src="../assets/logo.svg" alt="Tropea Boutique" class="footer-logo"/>
      <p>© 2026 Tropea Boutique · Marseille 🐟</p>
      <div class="footer-links" style="display:flex;gap:16px;flex-wrap:wrap">
        <a href="/">Boutique</a>
        <a href="../sobre-mi.html">Qui suis-je</a>
        <a href="../contacto.html" data-fr="Contact" data-es="Contacto" data-en="Contact">Contact</a>
        <a href="../privacidad.html">Confidentialité</a>
        <a href="../terminos.html">Conditions</a>
        <a href="mailto:info@tropeaboutique.com">info@tropeaboutique.com</a>
      </div>
    </div>
  </footer>

  <script src="../lang.js?v=20260906e"></script>
  <script>
    // Las pastillas filtran de verdad; antes solo se coloreaban.
    document.querySelectorAll('.cat-pill').forEach(pill => {
      pill.addEventListener('click', () => {
        document.querySelectorAll('.cat-pill').forEach(p => p.classList.remove('active'));
        pill.classList.add('active');
        const cat = pill.dataset.cat;
        document.querySelectorAll('[data-tag]').forEach(card => {
          const visible = cat === '*' || card.dataset.tag.includes(cat);
          card.closest('.blog-featured, .blog-card') === null
            ? card.style.display = visible ? '' : 'none'
            : card.style.display = visible ? '' : 'none';
        });
      });
    });
  </script>
</body>
</html>
