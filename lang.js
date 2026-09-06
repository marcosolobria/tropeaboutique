/* Selector de idioma común a todas las páginas.
 *
 * Antes cada página hacía la suya: la portada tenía botones sin memoria, «Sobre
 * mí» tenía su propia copia, y contacto y los legales se traducían solos con
 * textContent — que se come los <br/> y los <em> de los textos. Esto lo unifica.
 *
 * Cada texto traducible lleva data-fr y, si existe, data-es y data-en. Sin la
 * traducción, cae al francés, que es el idioma de la casa.
 */
(function () {
  const IDIOMAS = ['fr', 'es', 'en'];
  const CLAVE = 'tropea-lang';

  function inicial() {
    try {
      const guardado = localStorage.getItem(CLAVE);
      if (IDIOMAS.includes(guardado)) return guardado;
    } catch (e) { /* navegación privada: se sigue con el del navegador */ }
    const navegador = (navigator.language || 'fr').slice(0, 2).toLowerCase();
    return IDIOMAS.includes(navegador) ? navegador : 'fr';
  }

  let actual = inicial();

  function aplicar() {
    document.documentElement.lang = actual;

    document.querySelectorAll('[data-fr]').forEach(el => {
      const texto = el.dataset[actual] || el.dataset.fr;
      // En un campo de formulario lo que se traduce es el marcador, no el valor.
      if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
        el.placeholder = texto;
      } else {
        el.innerHTML = texto;
      }
    });

    document.querySelectorAll('.lang-btn').forEach(b => {
      b.classList.toggle('active', b.dataset.lang === actual);
    });

    // La portada repinta los productos al enterarse.
    document.dispatchEvent(new CustomEvent('tropea:lang', { detail: actual }));
  }

  function cambiar(nuevo) {
    if (!IDIOMAS.includes(nuevo)) return;
    actual = nuevo;
    try { localStorage.setItem(CLAVE, nuevo); } catch (e) { /* da igual */ }
    aplicar();
  }

  window.TropeaLang = {
    get actual() { return actual; },
    cambiar,
    aplicar,
  };

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.lang-btn').forEach(btn => {
      btn.addEventListener('click', () => cambiar(btn.dataset.lang));
    });
    aplicar();
  });
})();
