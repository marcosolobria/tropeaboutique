# Graph Report - tropeaboutique  (2026-06-21)

## Corpus Check
- 17 files · ~17,388 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 92 nodes · 109 edges · 23 communities (16 shown, 7 thin omitted)
- Extraction: 73% EXTRACTED · 25% INFERRED · 2% AMBIGUOUS · INFERRED: 27 edges (avg confidence: 0.89)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `bcca206a`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- [[_COMMUNITY_Admin API & Contact Flow|Admin API & Contact Flow]]
- [[_COMMUNITY_Frontend Catalog UI|Frontend Catalog UI]]
- [[_COMMUNITY_Admin Authentication|Admin Authentication]]
- [[_COMMUNITY_Telegram Bot Integration|Telegram Bot Integration]]
- [[_COMMUNITY_OG Image Branding (SVG)|OG Image Branding (SVG)]]
- [[_COMMUNITY_Product Catalog Data|Product Catalog Data]]
- [[_COMMUNITY_Logo Composition|Logo Composition]]
- [[_COMMUNITY_OG Image Branding (PNG)|OG Image Branding (PNG)]]
- [[_COMMUNITY_Graphify Tooling Config|Graphify Tooling Config]]
- [[_COMMUNITY_Admin Auth Flow Concept|Admin Auth Flow Concept]]
- [[_COMMUNITY_Contact-to-Telegram Flow Concept|Contact-to-Telegram Flow Concept]]
- [[_COMMUNITY_Telegram-to-Catalog Flow Concept|Telegram-to-Catalog Flow Concept]]
- [[_COMMUNITY_.claudelaunch.json Config|.claude/launch.json Config]]
- [[_COMMUNITY_.claudesettings.local.json|.claude/settings.local.json]]
- [[_COMMUNITY_Community 21|Community 21]]

## God Nodes (most connected - your core abstractions)
1. `dashboard.php fetch('/admin/api.php')` - 9 edges
2. `renderProducts()` - 7 edges
3. `Open Graph Image (og.svg)` - 7 edges
4. `api/telegram.php Telegram webhook entrypoint` - 7 edges
5. `products.json (product catalog store)` - 6 edges
6. `isLoggedIn()` - 5 edges
7. `checkPassword()` - 5 edges
8. `load_products()` - 5 edges
9. `save_products()` - 5 edges
10. `Tropea Boutique Brand` - 5 edges

## Surprising Connections (you probably didn't know these)
- `admin/api.php case 'get_products'` --semantically_similar_to--> `load_products()`  [INFERRED] [semantically similar]
  admin/api.php → api/telegram.php
- `admin/api.php case 'save_products'` --semantically_similar_to--> `save_products()`  [INFERRED] [semantically similar]
  admin/api.php → api/telegram.php
- `DEMO_PRODUCTS fallback array` --semantically_similar_to--> `products.json (product catalog store)`  [INFERRED] [semantically similar]
  app.js → products.json
- `SUPABASE_URL / SUPABASE_ANON_KEY placeholders` --semantically_similar_to--> `products.json (product catalog store)`  [INFERRED] [semantically similar]
  app.js → products.json
- `api/save_message.php POST handler` --semantically_similar_to--> `tg_send()`  [AMBIGUOUS] [semantically similar]
  api/save_message.php → api/telegram.php

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **Tropea Boutique brand identity: fish icon + wordmark + accent** — logo_fish_icon, logo_tropea_wordmark, logo_boutique_wordmark, logo_pink_accent_divider [INFERRED 0.85]
- **Tropea Boutique Brand Identity Composition** — og_fish_icon, og_brand_name_tropea, og_boutique_subtitle [INFERRED 0.75]
- **Open Graph share-image composition presenting Tropea Boutique brand identity** — og_svg, og_svg_fish_logo, og_svg_decorative_fish, og_svg_tagline, og_svg_subtitle, og_svg_domain_label, og_svg_color_palette, tropea_boutique_brand [INFERRED 0.85]
- **Contact form submission and message storage flow** — contacto_html_contact_form, contacto_html_save_message_fetch, save_message_php_handler, messages_json_data, api_php_get_messages [INFERRED 0.85]
- **Admin login, session check, and logout flow** — index_php_login_form, auth_php_checkpassword, auth_php_islogged_in, auth_php_requireauth, logout_php_destroy_session, config_php_admin_password_hash [EXTRACTED 1.00]
- **Telegram bot photo/caption to product catalog flow** — telegram_php_webhook, telegram_php_parse_caption, telegram_php_download_telegram_file, telegram_php_save_products, products_json_store [EXTRACTED 1.00]

## Communities (23 total, 7 thin omitted)

### Community 0 - "Admin API & Contact Flow"
Cohesion: 0.15
Nodes (14): admin/api.php case 'chat' (Claude AI assistant proxy), admin/api.php case 'create_blog_post', admin/api.php case 'delete_message', admin/api.php case 'get_blog_posts', admin/api.php case 'get_messages', admin/api.php case 'get_products', admin/api.php case 'save_blog_post', CLAUDE_API_KEY constant (hardcoded secret) (+6 more)

### Community 1 - "Frontend Catalog UI"
Cohesion: 0.21
Nodes (9): applyLang(), buildCollectionFilters(), cardHTML(), DEMO_PRODUCTS, WA_NUMBER constant (WhatsApp number), loadProducts(), openModal(), products (+1 more)

### Community 2 - "Admin Authentication"
Cohesion: 0.22
Nodes (11): checkPassword(), isLoggedIn(), requireAuth(), updatePassword(), admin/api.php case 'change_password', ADMIN_PASSWORD_HASH constant, ADMIN_PASSWORD_SALT constant, SESSION_NAME constant (+3 more)

### Community 3 - "Telegram Bot Integration"
Cohesion: 0.33
Nodes (10): ALLOWED_USER constant (Telegram user id), BOT_TOKEN constant (Telegram bot), download_telegram_file(), load_products(), parse_caption(), save_products(), tg_send(), IMG_DIR constant (+2 more)

### Community 4 - "OG Image Branding (SVG)"
Cohesion: 0.43
Nodes (8): Open Graph Image (og.svg), Brand Color Palette (mint green gradient + pink F4A7B9 accent), Background Decorative Fish Silhouette, Bottom Label: TROPEABOUTIQUE.COM, Tropea Boutique Fish Logo Graphic, Subtitle: Accessoires faits main · Marseille · Accesorios artesanales, Tagline: L'amour dans chaque détail, Tropea Boutique Brand

### Community 5 - "Product Catalog Data"
Cohesion: 0.50
Nodes (5): admin/api.php case 'save_products', DEMO_PRODUCTS fallback array, loadProducts() (fetches /products.json), SUPABASE_URL / SUPABASE_ANON_KEY placeholders, products.json (product catalog store)

### Community 6 - "Logo Composition"
Cohesion: 0.70
Nodes (5): BOUTIQUE Wordmark Text, Fish Icon (moon-tail, rounded body, dorsal fin), Pink Divider Line and Dot Accent, Tropea Boutique Logo (SVG), TROPEA Wordmark Text

### Community 7 - "OG Image Branding (PNG)"
Cohesion: 0.67
Nodes (4): BOUTIQUE Subtitle Tagline, TROPEA Brand Wordmark, Stylized Fish Icon Graphic, Tropea Boutique Logo and OG Image

## Ambiguous Edges - Review These
- `isLoggedIn()` → `admin/logout.php session destroy`  [AMBIGUOUS]
  admin/logout.php · relation: references
- `tg_send()` → `api/save_message.php POST handler`  [AMBIGUOUS]
  api/save_message.php · relation: semantically_similar_to

## Knowledge Gaps
- **24 isolated node(s):** `DEMO_PRODUCTS`, `products`, `graphify`, `BOUTIQUE Subtitle Tagline`, `Brand Color Palette (mint green gradient + pink F4A7B9 accent)` (+19 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **7 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **What is the exact relationship between `isLoggedIn()` and `admin/logout.php session destroy`?**
  _Edge tagged AMBIGUOUS (relation: references) - confidence is low._
- **What is the exact relationship between `tg_send()` and `api/save_message.php POST handler`?**
  _Edge tagged AMBIGUOUS (relation: semantically_similar_to) - confidence is low._
- **Why does `dashboard.php fetch('/admin/api.php')` connect `Admin API & Contact Flow` to `Admin Authentication`, `Product Catalog Data`?**
  _High betweenness centrality (0.209) - this node is a cross-community bridge._
- **Why does `products.json (product catalog store)` connect `Product Catalog Data` to `Admin API & Contact Flow`, `Telegram Bot Integration`?**
  _High betweenness centrality (0.169) - this node is a cross-community bridge._
- **Why does `loadProducts() (fetches /products.json)` connect `Product Catalog Data` to `Frontend Catalog UI`?**
  _High betweenness centrality (0.144) - this node is a cross-community bridge._
- **Are the 9 inferred relationships involving `dashboard.php fetch('/admin/api.php')` (e.g. with `admin/api.php case 'change_password'` and `admin/api.php case 'chat' (Claude AI assistant proxy)`) actually correct?**
  _`dashboard.php fetch('/admin/api.php')` has 9 INFERRED edges - model-reasoned connections that need verification._
- **Are the 3 inferred relationships involving `products.json (product catalog store)` (e.g. with `admin/api.php case 'save_products'` and `DEMO_PRODUCTS fallback array`) actually correct?**
  _`products.json (product catalog store)` has 3 INFERRED edges - model-reasoned connections that need verification._