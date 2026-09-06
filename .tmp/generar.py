# -*- coding: utf-8 -*-
"""Reescribe los artículos cortos del panel en versión trilingüe.

Además arregla dos fallos que traían de la plantilla vieja:
  - la fecha salía como el texto literal {date('F Y')};
  - el cuerpo se metía en crudo, sin <p>, así que se leía como un bloque.
"""
import json, html, pathlib

D = json.load(open('.tmp/articulos.json', encoding='utf-8'))

CATS = {'Inspiration': ('Inspiración','Inspiration'),
        'Création': ('Creación','Making'),
        'Artisanat': ('Artesanía','Craft'),
        'Idées cadeaux': ('Ideas de regalo','Gift ideas')}
MESES = {'Septembre 2026': ('Septiembre 2026','September 2026')}

def cat(tag, i):
    if i == 0: return tag
    return ' · '.join(CATS.get(t.strip(), (t.strip(), t.strip()))[i-1] for t in tag.split('·'))

def attrs(fr, es, en):
    q = lambda s: html.escape(s, quote=True)
    return f'data-fr="{q(fr)}" data-es="{q(es)}" data-en="{q(en)}"'

PLANTILLA = '''<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{titulo_fr} — Tropea Boutique</title>
  <meta name="description" content="{resumen}"/>
  <link rel="canonical" href="https://tropeaboutique.com/blog/{slug}.html"/>
  <meta property="og:title" content="{titulo_fr}"/>
  <meta property="og:description" content="{resumen}"/>
  <meta property="og:type" content="article"/>
  <meta property="og:url" content="https://tropeaboutique.com/blog/{slug}.html"/>
  <link rel="icon" href="../assets/logo.svg" type="image/svg+xml"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=Inter:wght@300;400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="../style.css?v=20260906f"/>
  <style>
    .post-wrap{{max-width:720px;margin:0 auto;padding:60px 24px 100px}}
    .post-wrap h1{{font-family:'Playfair Display',serif;font-size:clamp(28px,5vw,44px);line-height:1.15;margin-bottom:20px}}
    .post-meta{{font-size:12px;color:#aaa;margin-bottom:40px;letter-spacing:.5px}}
    .post-body{{font-size:16px;line-height:1.85;color:#444}}
    .post-body p{{margin-bottom:20px}}
    .post-back{{display:inline-block;margin-top:24px;font-size:13px;font-weight:600;color:var(--pink-dark)}}
  </style>
</head>
<body>
  <nav class="nav">
    <a href="/" class="nav-logo"><img src="../assets/logo.svg" alt="Tropea Boutique" class="nav-logo-img"/></a>
    <div class="nav-center-links">
      <a href="/" class="nav-link" data-fr="Boutique" data-es="Tienda" data-en="Shop">Boutique</a>
      <a href="../sobre-mi.html" class="nav-link" data-fr="Qui suis-je" data-es="Sobre mí" data-en="About me">Qui suis-je</a>
      <a href="./" class="nav-link" data-fr="Journal" data-es="Blog" data-en="Journal">Journal</a>
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
  <div class="post-wrap">
    <span class="hero-label" style="display:block;margin-bottom:18px" {attr_tag}>{tag_fr}</span>
    <h1 {attr_titulo}>{titulo_fr}</h1>
    <p class="post-meta" {attr_meta}>{meta_fr}</p>
    <div class="post-body">
{cuerpo}
    </div>
    <a href="./" class="post-back" data-fr="← Tous les articles" data-es="← Todos los artículos" data-en="← All articles">← Tous les articles</a>
  </div>
  <footer class="footer"><div class="container footer-inner">
    <img src="../assets/logo.svg" alt="Tropea Boutique" class="footer-logo"/>
    <p>© 2026 Tropea Boutique · Marseille 🐟</p>
    <div class="footer-links" style="display:flex;gap:16px;flex-wrap:wrap">
      <a href="/" data-fr="Boutique" data-es="Tienda" data-en="Shop">Boutique</a>
      <a href="./" data-fr="Journal" data-es="Blog" data-en="Journal">Journal</a>
      <a href="../contacto.html" data-fr="Contact" data-es="Contacto" data-en="Contact">Contact</a>
      <a href="mailto:info@tropeaboutique.com">info@tropeaboutique.com</a>
    </div>
  </div></footer>
  <script src="../lang.js?v=20260906f"></script>
</body>
</html>
'''

for slug, a in D.items():
    tags = [cat(a['tag'], i) for i in range(3)]
    fecha = [a['fecha'], *MESES[a['fecha']]]
    metas = [f"{tags[i]} · ✍️ Maria Yañez · {fecha[i]}" for i in range(3)]
    cuerpo = '\n'.join(
        f"      <p {attrs(p['fr'], p['es'], p['en'])}>{html.escape(p['fr'])}</p>"
        for p in a['parrafos'])
    resumen = html.escape(a['parrafos'][0]['fr'][:152] + '…', quote=True)

    pathlib.Path(f'blog/{slug}.html').write_text(PLANTILLA.format(
        slug=slug, resumen=resumen,
        titulo_fr=html.escape(a['titulo']['fr']),
        attr_titulo=attrs(a['titulo']['fr'], a['titulo']['es'], a['titulo']['en']),
        tag_fr=html.escape(tags[0]), attr_tag=attrs(*tags),
        meta_fr=html.escape(metas[0]), attr_meta=attrs(*metas),
        cuerpo=cuerpo), encoding='utf-8')
    print('reescrito:', slug)
