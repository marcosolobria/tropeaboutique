# -*- coding: utf-8 -*-
"""Añade data-es y data-en a los bloques de un artículo ya escrito en francés.

El francés se queda como contenido visible del HTML: es el original, y es lo que
ve un buscador que no ejecute JavaScript. lang.js hace el resto.
"""
import html, json, sys, pathlib, re
sys.path.insert(0, '.tmp')
from bloques import bloques

ruta, mapa_json = sys.argv[1], sys.argv[2]
mapa = json.load(open(mapa_json, encoding='utf-8'))
s = pathlib.Path(ruta).read_text(encoding='utf-8')

q = lambda x: html.escape(x, quote=True)
puestos, faltan = 0, []

for tag, attrs, cont, entero in bloques(s):
    if 'data-fr=' in attrs:
        continue
    trad = mapa.get(cont)
    if not trad:
        faltan.append(cont[:70]); continue
    nuevos = f' data-fr="{q(cont)}" data-es="{q(trad["es"])}" data-en="{q(trad["en"])}"'
    reemplazo = entero.replace(f'<{tag}{attrs}>', f'<{tag}{attrs}{nuevos}>', 1)
    s = s.replace(entero, reemplazo, 1)
    puestos += 1

# lang.js y el selector, si el artículo aún no los tiene
if 'lang.js' not in s:
    s = s.replace('</body>', '  <script src="../lang.js?v=20260906f"></script>\n</body>', 1)
if 'nav-langs' not in s:
    selector = '''    <div class="nav-langs" style="margin-left:auto;margin-right:16px">
      <button class="lang-btn" data-lang="fr">FR</button>
      <span class="lang-sep">|</span>
      <button class="lang-btn" data-lang="es">ES</button>
      <span class="lang-sep">|</span>
      <button class="lang-btn" data-lang="en">EN</button>
    </div>
'''
    s = s.replace('    <a href="https://www.instagram.com/tropeaboutique"', selector + '    <a href="https://www.instagram.com/tropeaboutique"', 1)

pathlib.Path(ruta).write_text(s, encoding='utf-8')
print(f'{ruta}: {puestos} bloques traducidos')
for f in faltan:
    print('   SIN TRADUCIR:', f)
