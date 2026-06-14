/* ===== CONFIG ===== */
// Reemplazar con tus credenciales de Supabase
const SUPABASE_URL = 'https://YOUR_PROJECT.supabase.co';
const SUPABASE_ANON_KEY = 'YOUR_ANON_KEY';
const WA_NUMBER = '34XXXXXXXXX'; // número de WhatsApp de María

/* ===== DEMO PRODUCTS (mientras no hay Supabase) ===== */
const DEMO_PRODUCTS = [
  {
    id: 1, name: 'Coque AMOR', name_fr: 'Coque AMOR',
    collection: 'Verão',
    description_fr: 'Coque faite main avec perles, coquillages et lettres dorées. Chaque pièce est unique, réalisée avec amour à Marseille.',
    description_es: 'Funda hecha a mano con perlas, conchas y letras doradas. Cada pieza es única, hecha con amor en Marsella.',
    price: 24, currency: '€',
    image_url: 'https://placehold.co/600x600/fce8f0/d4607a?text=AMOR+🐚',
    available: true
  },
  {
    id: 2, name: 'Tote Bag Panama', name_fr: 'Tote Bag Panama',
    collection: 'Panama',
    description_fr: 'Tote bag peint à la main avec motifs tropicaux inspirés de la culture panaméenne. Toile 100% naturelle.',
    description_es: 'Tote bag pintado a mano con motivos tropicales inspirados en la cultura panameña. Lona 100% natural.',
    price: 29, currency: '€',
    image_url: 'https://placehold.co/600x600/f0ece0/4a7a3a?text=Panama+🦎',
    available: true
  },
  {
    id: 3, name: 'Coque Perles Rosées', name_fr: 'Coque Perles Rosées',
    collection: 'Verão',
    description_fr: 'Coque rose pastel décorée de perles nacrées, étoiles de mer et fleurs dorées. Modèle exclusif.',
    description_es: 'Funda rosa pastel decorada con perlas nacaradas, estrellas de mar y flores doradas. Modelo exclusivo.',
    price: 22, currency: '€',
    image_url: 'https://placehold.co/600x600/fce8f0/d4607a?text=Perles+🌸',
    available: true
  },
  {
    id: 4, name: 'Tote Bag Tropical', name_fr: 'Tote Bag Tropical',
    collection: 'Panama',
    description_fr: 'Grand tote bag avec illustration tropicale peinte à la main. Idéal pour la plage ou le quotidien.',
    description_es: 'Gran tote bag con ilustración tropical pintada a mano. Ideal para la playa o el día a día.',
    price: 32, currency: '€',
    image_url: 'https://placehold.co/600x600/e8f4e8/2a7a4a?text=Tropical+🌺',
    available: true
  },
  {
    id: 5, name: 'Coque Étoiles', name_fr: 'Coque Étoiles',
    collection: 'Verão',
    description_fr: 'Coque crème avec étoiles de mer en résine, micro-perles et charm tortue dorée.',
    description_es: 'Funda crema con estrellas de mar en resina, micro-perlas y charm tortuga dorada.',
    price: 26, currency: '€',
    image_url: 'https://placehold.co/600x600/fdf6ef/b8905a?text=Étoiles+⭐',
    available: true
  },
  {
    id: 6, name: 'Coque Papillons', name_fr: 'Coque Papillons',
    collection: 'Nouvelle',
    description_fr: 'Coque transparente ornée de papillons roses, perles et fleurs en 3D. Élégante et poétique.',
    description_es: 'Funda transparente ornada con mariposas rosas, perlas y flores en 3D. Elegante y poética.',
    price: 23, currency: '€',
    image_url: 'https://placehold.co/600x600/fce8f0/c060a0?text=Papillons+🦋',
    available: true
  },
];

/* ===== STATE ===== */
let lang = 'fr';
let currentCollection = 'all';
let products = [];

/* ===== INIT ===== */
document.addEventListener('DOMContentLoaded', () => {
  initLang();
  loadProducts();
  initModal();
  initFilters();
  document.querySelector('#wa-contact').href = `https://wa.me/${WA_NUMBER}`;
});

/* ===== LANGUAGE ===== */
function initLang() {
  document.querySelectorAll('.lang-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      lang = btn.dataset.lang;
      document.querySelectorAll('.lang-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      applyLang();
    });
  });
}

function applyLang() {
  document.querySelectorAll('[data-fr]').forEach(el => {
    const text = el.dataset[lang] || el.dataset.fr;
    if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
      el.placeholder = text;
    } else {
      el.innerHTML = text;
    }
  });
  renderProducts();
}

/* ===== PRODUCTS ===== */
async function loadProducts() {
  try {
    if (SUPABASE_URL.includes('YOUR_PROJECT')) {
      products = DEMO_PRODUCTS;
    } else {
      const res = await fetch(`${SUPABASE_URL}/rest/v1/products?select=*&order=created_at.desc`, {
        headers: { 'apikey': SUPABASE_ANON_KEY, 'Authorization': `Bearer ${SUPABASE_ANON_KEY}` }
      });
      products = await res.json();
    }
    buildCollectionFilters();
    renderProducts();
  } catch (e) {
    products = DEMO_PRODUCTS;
    buildCollectionFilters();
    renderProducts();
  }
}

function buildCollectionFilters() {
  const collections = [...new Set(products.map(p => p.collection))].filter(Boolean);
  const container = document.getElementById('collection-filters');
  container.innerHTML = collections.map(col =>
    `<button class="filter-btn" data-collection="${col}">${col}</button>`
  ).join('');
  document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      currentCollection = btn.dataset.collection;
      document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      renderProducts();
    });
  });
  document.querySelector('.filter-btn[data-collection="all"]').addEventListener('click', () => {
    currentCollection = 'all';
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    document.querySelector('.filter-btn[data-collection="all"]').classList.add('active');
    renderProducts();
  });
}

function renderProducts() {
  const grid = document.getElementById('products-grid');
  const empty = document.getElementById('empty-msg');
  const filtered = currentCollection === 'all'
    ? products
    : products.filter(p => p.collection === currentCollection);

  if (!filtered.length) {
    grid.innerHTML = '';
    empty.style.display = 'block';
    empty.textContent = lang === 'fr'
      ? 'Aucun article dans cette collection pour le moment.'
      : 'No hay artículos en esta colección por ahora.';
    return;
  }
  empty.style.display = 'none';
  grid.innerHTML = filtered.map(p => cardHTML(p)).join('');
  grid.querySelectorAll('.product-card').forEach((card, i) => {
    card.addEventListener('click', () => openModal(filtered[i]));
  });
}

function cardHTML(p) {
  const name = lang === 'fr' ? (p.name_fr || p.name) : (p.name_es || p.name);
  const price = `${p.price}${p.currency || '€'}`;
  return `
    <article class="product-card">
      <div class="card-img-wrap">
        <img class="card-img" src="${p.image_url}" alt="${name}" loading="lazy"/>
        ${p.collection ? `<span class="card-badge">${p.collection}</span>` : ''}
      </div>
      <div class="card-body">
        ${p.collection ? `<p class="card-collection">${p.collection}</p>` : ''}
        <h3 class="card-name">${name}</h3>
        <p class="card-price">${price}</p>
      </div>
    </article>`;
}

/* ===== MODAL ===== */
function initModal() {
  document.getElementById('modal-close').addEventListener('click', closeModal);
  document.getElementById('modal-overlay').addEventListener('click', e => {
    if (e.target === document.getElementById('modal-overlay')) closeModal();
  });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
}

function openModal(p) {
  const name = lang === 'fr' ? (p.name_fr || p.name) : (p.name_es || p.name);
  const desc = lang === 'fr' ? p.description_fr : (p.description_es || p.description_fr);
  document.getElementById('modal-img').src = p.image_url;
  document.getElementById('modal-img').alt = name;
  document.getElementById('modal-collection').textContent = p.collection || '';
  document.getElementById('modal-title').textContent = name;
  document.getElementById('modal-desc').textContent = desc || '';
  document.getElementById('modal-price').textContent = `${p.price}${p.currency || '€'}`;
  const waText = lang === 'fr'
    ? `Bonjour ! Je suis intéressé(e) par "${name}" (${p.price}€). Est-ce disponible ?`
    : `¡Hola! Me interesa "${name}" (${p.price}€). ¿Está disponible?`;
  document.getElementById('modal-wa').href = `https://wa.me/${WA_NUMBER}?text=${encodeURIComponent(waText)}`;
  document.getElementById('modal-overlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeModal() {
  document.getElementById('modal-overlay').classList.remove('open');
  document.body.style.overflow = '';
}

/* ===== COLLECTION FILTER ALL BTN ===== */
function initFilters() {
  document.querySelector('.filter-btn[data-collection="all"]').addEventListener('click', () => {
    currentCollection = 'all';
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    document.querySelector('.filter-btn[data-collection="all"]').classList.add('active');
    renderProducts();
  });
}
