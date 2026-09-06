<?php
// El sitemap se genera leyendo la carpeta, igual que el listado del Journal.
// Uno escrito a mano se queda viejo en cuanto Maria publica desde el panel, que
// es justo el problema que tenía el blog.
header('Content-Type: application/xml; charset=UTF-8');

const BASE = 'https://tropeaboutique.com';

$MESES = [1=>'Janvier','Février','Mars','Avril','Mai','Juin',
          'Juillet','Août','Septembre','Octobre','Novembre','Décembre'];

$urls = [];

// Páginas fijas. La portada primero y con la prioridad más alta.
$fijas = [
    ''                 => ['1.0', 'weekly'],
    'sobre-mi.html'    => ['0.8', 'monthly'],
    'contacto.html'    => ['0.8', 'monthly'],
    'blog/'            => ['0.9', 'weekly'],
    'privacidad.html'  => ['0.3', 'yearly'],
    'terminos.html'    => ['0.3', 'yearly'],
];
foreach ($fijas as $ruta => [$prioridad, $frecuencia]) {
    $fichero = __DIR__ . '/' . ($ruta === '' ? 'index.html' : ($ruta === 'blog/' ? 'blog/index.php' : $ruta));
    if (!file_exists($fichero)) continue;
    $urls[] = [BASE . '/' . $ruta, date('Y-m-d', filemtime($fichero)), $frecuencia, $prioridad];
}

// Artículos del Journal, los de siempre y los que cree el panel a partir de hoy.
foreach (glob(__DIR__ . '/blog/*.html') as $ruta) {
    $fichero = basename($ruta);
    if ($fichero === 'index.html') continue;
    $html = file_get_contents($ruta);

    // Sin <h1> no es un artículo publicable: fuera del sitemap.
    if (!preg_match('~<h1[^>]*>(.*?)</h1>~s', $html)) continue;

    // La fecha escrita en el artículo manda sobre la del fichero, que cambia
    // cada vez que se despliega y mandaría a Google una señal falsa.
    $fecha = date('Y-m-d', filemtime($ruta));
    if (preg_match('~class="(?:article-meta|post-meta)"[^>]*>(.*?)</p>~s', $html, $m)
        && preg_match('~(' . implode('|', $MESES) . ')\s+(\d{4})~ui', strip_tags($m[1]), $d)) {
        $mes = array_search(ucfirst(mb_strtolower($d[1])), $MESES, true) ?: 1;
        $fecha = sprintf('%04d-%02d-01', (int) $d[2], $mes);
    }

    $urls[] = [BASE . '/blog/' . rawurlencode($fichero), $fecha, 'monthly', '0.7'];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as [$loc, $fecha, $frecuencia, $prioridad]) {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($loc, ENT_XML1) . "</loc>\n";
    echo "    <lastmod>$fecha</lastmod>\n";
    echo "    <changefreq>$frecuencia</changefreq>\n";
    echo "    <priority>$prioridad</priority>\n";
    echo "  </url>\n";
}
echo "</urlset>\n";
