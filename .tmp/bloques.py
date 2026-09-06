# -*- coding: utf-8 -*-
"""Bloques traducibles dentro de <article>: h1, h2, h3, p, li y los dos
recuadros con texto (article-highlight y cta-box)."""
import re, sys

SIMPLES = re.compile(r'<(h1|h2|h3|p|li|span)([^>]*)>(.*?)</\1>', re.S)
RECUADRO = re.compile(r'<div class="article-highlight">(.*?)</div>', re.S)

def region(s):
    return re.search(r'<article[^>]*>(.*?)</article>', s, re.S)

def bloques(s):
    m = region(s)
    if not m:
        return []
    out = []
    for b in SIMPLES.finditer(m.group(1)):
        attrs, cont = b.group(2), b.group(3).strip()
        if 'article-meta' in attrs:
            continue
        if not re.search(r'[A-Za-zÀ-ÿ]', re.sub(r'<[^>]*>', '', cont)):
            continue
        out.append((b.group(1), attrs, re.sub(r'\s+', ' ', cont), b.group(0)))
    for b in RECUADRO.finditer(m.group(1)):
        out.append(('div', ' class="article-highlight"', re.sub(r'\s+', ' ', b.group(1)).strip(), b.group(0)))
    return out

if __name__ == '__main__':
    for i, (t, a, c, _) in enumerate(bloques(open(sys.argv[1], encoding='utf-8').read())):
        print(f'[{i}] {t}: {c}')
