import re, sys, json
ruta = sys.argv[1]
s = open(ruta, encoding='utf-8').read()
cuerpo = re.search(r'<article[^>]*>(.*?)</article>', s, re.S) or re.search(r'<div class="article-wrap">(.*?)</div>\s*<footer', s, re.S)
texto = cuerpo.group(1) if cuerpo else s
bloques = re.findall(r'<(h1|h2|h3|p|li)(?![^>]*class="article-meta")[^>]*>(.*?)</\1>', texto, re.S)
for i,(tag,c) in enumerate(bloques):
    c = re.sub(r'\s+',' ',c).strip()
    print(f'[{i}] <{tag}> {c}')
