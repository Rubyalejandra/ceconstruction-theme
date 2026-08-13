# CE Construction — ARCHITECTURE.md
### Arquitectura real del proyecto (no propuesta)

> Este documento describe la arquitectura **tal como existe en el código hoy**, verificada archivo por archivo contra el sistema de ficheros real, no un diseño aspiracional. Debe mantenerse actualizado en cada sprint que agregue o modifique archivos — es responsabilidad de quien cierre un sprint reflejar aquí cualquier cambio estructural.

**Versión de referencia:** v0.4.1 (post Sprint 5, Fase 1 — correcciones de QA)

---

## 1. Estructura completa de carpetas

```
ce-construction-theme/
├── style.css                  Cabecera del tema (obligatoria en WP) + design tokens de respaldo
├── functions.php              Bootstrap: única puerta de entrada, carga inc/*
├── header.php                 Documento HTML: <head>, header, menú móvil, breadcrumbs
├── footer.php                 Footer, botones flotantes, modales, wp_footer()
├── front-page.php             Página de inicio (ensambla template-parts)
├── archive-servicio.php       Listado de Servicios
├── single-servicio.php        Ficha individual de Servicio
├── archive-proyecto.php       Listado de Proyectos
├── single-proyecto.php        Ficha individual de Proyecto
│
├── inc/                       Lógica PHP modular (un archivo = una responsabilidad)
├── template-parts/            Fragmentos de plantilla reutilizables (secciones/componentes)
├── assets/
│   ├── css/main.css           Sistema de diseño completo del tema
│   ├── js/main.js             Módulos JS del tema (patrón namespace, ES6)
│   └── img/                   Imágenes estáticas del tema (vacía; pendiente de assets del cliente)
│
└── *.md (raíz)                Documentación de control del proyecto (no forma parte del
                                tema instalable; ver sección 8 de este documento)
```

**Archivos de plantilla de WordPress aún pendientes** (no existen todavía; ver `TODO.md`): `page.php`, `single.php`, `comments.php`, `404.php`, `archive.php` genérico. `index.php` (Entregable 6A) ya resuelve el fallback para estos contextos mientras tanto.

---

## 2. Función y responsabilidad de cada directorio

### `/` (raíz del tema)
Contiene el "esqueleto" de WordPress: la cabecera del tema (`style.css`), el bootstrap (`functions.php`), y las plantillas de más alto nivel que WordPress resuelve directamente vía su Template Hierarchy (`front-page.php`, `archive-{cpt}.php`, `single-{cpt}.php`, `header.php`, `footer.php`). Ningún archivo de la raíz contiene lógica de negocio extensa: delegan a `inc/` y ensamblan `template-parts/`.

### `inc/`
Toda la lógica PHP que no es "una plantilla que WordPress carga directamente". Convención: **un archivo por responsabilidad concreta** (un CPT por archivo, un dominio funcional por archivo). Ningún archivo de `inc/` se auto-ejecuta; todos son cargados explícitamente por `functions.php` vía `require_once` dentro de `ce_construction_require_modules()`.

### `template-parts/`
Fragmentos de plantilla HTML+PHP reutilizables, invocados vía `get_template_part()`. Dos categorías conviven aquí:
- **Secciones de página completa** (ej. `hero.php`, `about.php`, `services.php`): pensadas para una sola plantilla específica (el home).
- **Componentes reutilizables entre varias plantillas** (ej. `page-hero.php`, `cta.php`, `quote-form.php`, `content-servicio.php`, `sidebar-servicios.php`): diseñados desde su creación para invocarse desde múltiples archivos de nivel superior.

### `assets/`
Todo lo que se sirve tal cual al navegador vía `wp_enqueue_style()`/`wp_enqueue_script()`. **No contiene lógica PHP.** `main.css` y `main.js` son monolíticos por diseño (un solo archivo cargado en cada página, seccionado internamente con comentarios numerados) — ver sección 9 (convenciones) para el porqué de esta elección.

---

## 3. Función de cada archivo principal

### Raíz
| Archivo | Responsabilidad |
|---|---|
| `style.css` | Cabecera obligatoria del tema (metadatos leídos por WordPress) + variables CSS de respaldo si `main.css` no cargara. |
| `functions.php` | Define constantes (`CE_THEME_VERSION`, `CE_THEME_DIR`, `CE_THEME_URI`) y ejecuta `ce_construction_require_modules()`, la única función que hace `require_once` sobre `inc/*.php`. |
| `header.php` | `<!DOCTYPE>` → `<head>` → `wp_head()` → barra superior de contacto → header principal (logo/menú/CTA) → menú móvil off-canvas → apertura de `<main>` → breadcrumbs (vía `ce_construction_breadcrumbs()`). |
| `footer.php` | Cierre de `<main>` → footer (contacto/redes/mapa/horario/enlaces) → botones flotantes (WhatsApp/volver arriba) → modales de éxito/error → `wp_footer()` → cierre de `</html>`. |
| `front-page.php` | `get_header()` → 10 `get_template_part()` en secuencia → `get_footer()`. Sin lógica propia. |
| `archive-servicio.php` / `archive-proyecto.php` | `get_header()` → hero interno (`page-hero`) → loop con `content-{cpt}.php` → paginación → sidebar opcional → CTA → `get_footer()`. |
| `single-servicio.php` / `single-proyecto.php` | `get_header()` → hero interno → metadatos/contenido → navegación prev/next → "relacionados" → sidebar → CTA → formulario de cotización → `get_footer()`. |
| `index.php` | Fallback obligatorio de WordPress (Entregable 6A). Cubre, con ramas condicionales explícitas, 4 contextos sin plantilla propia todavía: `is_search()`, `is_404()`, `is_archive()` (genérico) e `is_singular()` (single de blog/página). Reutiliza exclusivamente componentes ya existentes; no requirió extender `helpers.php`/`seo.php`/`main.js`. |

### `inc/`
| Archivo | Responsabilidad |
|---|---|
| `setup.php` | `add_theme_support()`, `register_nav_menus()`, `register_sidebar()` (footer-1, footer-2), tamaños de imagen custom, filtros de excerpt. |
| `enqueue.php` | Encola Google Fonts, Font Awesome, `style.css`, `assets/css/main.css`, `assets/js/main.js` (con `defer`), y `wp_localize_script()` de `ceConstructionData` (nonce del formulario, número de WhatsApp, i18n). 🆕 Sprint UX-1, Entregable UX-1.2: también encola `assets/js/admin-home-builder.js`, exclusivamente en `customize_controls_enqueue_scripts` (admin del Customizer, nunca frontend) — mantiene la convención de ser el único archivo válido de encolado de assets del proyecto (ver sección 10). |
| `customizer.php` | Registra las secciones del Theme Customizer y `ce_construction_customizer_css()`, que inyecta `<style>` en `wp_head` sobreescribiendo las variables CSS con los valores guardados. 🆕 Sprint UX-1, Entregable UX-1.2: suma la sección `ce_section_home_builder` (8 secciones en total) — persistencia del orden/activo-inactivo de las secciones del Home (`theme_mod` `ce_home_sections_order`) mediante el control custom `CE_Customize_Home_Sections_Control`, enganchado al filtro `ce_home_active_order` expuesto por `inc/home-builder.php`. Ver sección 6 y `DECISIONS.md` D-046. |
| `helpers.php` | Funciones puras reutilizables sin efectos secundarios de registro (redes sociales, WhatsApp, galería, iconos, excerpt corto, `ce_cpt_has_posts()`, y las 3 funciones de relación heurística entre Servicios/Proyectos). |
| `home-builder.php` | 🆕 Sprint UX-1, Entregable UX-1.1. Registro central de secciones del Home (`ce_construction_home_sections()`), orden por defecto (`ce_construction_default_home_order()`) y orden activo filtrable (`ce_construction_get_active_home_order()`, filtro `ce_home_active_order`). Sin hooks propios; consumido por `front-page.php` en tiempo de render. Ver sección 6 y `DECISIONS.md` D-045. |
| `cpt-servicios.php`, `cpt-proyectos.php`, `cpt-testimonios.php`, `cpt-equipo.php`, `cpt-clientes.php`, `cpt-faq.php` | Un `register_post_type()` (y taxonomías cuando aplica) por archivo. |
| `meta-boxes.php` | `add_meta_box()` + funciones `ce_render_*_fields()` de renderizado + `ce_construction_save_meta_boxes()`, el único punto de guardado para los 5 CPTs de contenido (con nonce, sanitización, `current_user_can`, guardas de autosave/revisión). |
| `quote-form.php` | CPT interno `cotizacion` + handler AJAX `ce_construction_handle_quote_form()` (nonce, rate-limiting, honeypot, validación, subida de adjunto como attachment real, `wp_mail()`) + cron de purga por retención. |
| `seo.php` | Meta tags/Open Graph genéricos (`is_singular()`/resto), Schema.org de Organización (portada), breadcrumbs HTML, y Schema.org específico por CPT (`Service`+`BreadcrumbList` para Servicio, `CreativeWork`/`Project`+`BreadcrumbList` para Proyecto). |

### `template-parts/`
| Archivo | Usado por | Rol |
|---|---|---|
| `hero.php`, `about.php`, `services.php`, `projects.php`, `stats.php`, `why-us.php`, `testimonials.php`, `gallery.php` | `front-page.php` únicamente | Secciones fijas del home. |
| `cta.php`, `quote-form.php` | `front-page.php`, `single-servicio.php`, `archive-servicio.php`, `single-proyecto.php`, `archive-proyecto.php` | Componentes reutilizables de conversión. |
| `page-hero.php` | `archive-servicio.php`, `single-servicio.php`, `archive-proyecto.php`, `single-proyecto.php` | Hero interno parametrizable vía `$args`. |
| `content-servicio.php`, `content-proyecto.php` | Sus respectivos `archive-*.php` | Partial de tarjeta para el loop principal. |
| `sidebar-servicios.php`, `sidebar-proyectos.php` | Sus respectivos `archive-*.php`/`single-*.php` | Sidebar opcional (listado + tarjeta de contacto). |

---

## 4. Flujo de carga del tema

```
WordPress carga el tema
        │
        ▼
functions.php se ejecuta primero (siempre, en cada request)
        │
        ├── define() de constantes (CE_THEME_VERSION, CE_THEME_DIR, CE_THEME_URI)
        │
        └── ce_construction_require_modules()
                │
                ├── inc/setup.php        (hooks: after_setup_theme, widgets_init)
                ├── inc/enqueue.php      (hook: wp_enqueue_scripts)
                ├── inc/customizer.php   (hook: customize_register, wp_head)
                ├── inc/cpt-servicios.php    (hook: init)
                ├── inc/cpt-proyectos.php    (hook: init)
                ├── inc/cpt-testimonios.php  (hook: init)
                ├── inc/cpt-equipo.php       (hook: init)
                ├── inc/cpt-clientes.php     (hook: init)
                ├── inc/cpt-faq.php          (hook: init)
                ├── inc/meta-boxes.php   (hooks: add_meta_boxes, save_post, admin_enqueue_scripts)
                ├── inc/quote-form.php   (hooks: init [CPT cotizacion], wp_ajax_*, after_switch_theme, switch_theme)
                ├── inc/seo.php          (hook: wp_head, múltiples callbacks)
                ├── inc/helpers.php      (sin hooks propios — solo funciones puras invocadas por otros archivos)
                ├── inc/widgets.php      (hook: widgets_init)
                └── inc/home-builder.php (🆕 Sprint UX-1 — sin hooks propios; registro de secciones
                                           del Home, consumido por front-page.php en tiempo de render)
        │
        ▼
WordPress resuelve la Template Hierarchy según la URL solicitada
(front-page.php / archive-servicio.php / single-proyecto.php / etc.)
        │
        ▼
La plantilla resuelta llama a get_header() → header.php
        │
        ▼
La plantilla ensambla su contenido (llamadas a get_template_part()
sobre archivos de template-parts/, que a su vez invocan funciones
ya definidas en inc/helpers.php, inc/seo.php, etc.)
        │
        ▼
La plantilla llama a get_footer() → footer.php → wp_footer()
        │
        ▼
wp_footer() dispara la impresión de assets/js/main.js (enqueado
con defer en inc/enqueue.php) — el JS se ejecuta al final del
documento, después de que todo el HTML ya existe en el DOM.
```

**Importante:** `inc/helpers.php` no engancha ningún hook propio — es una librería de funciones puras. Su posición en el array de `ce_construction_require_modules()` no es crítica en la práctica porque ninguna función de otro archivo de `inc/` la invoca durante la fase de carga (`require_once`); todas las llamadas a funciones de `helpers.php` ocurren más tarde, durante el renderizado de plantillas (después de que `functions.php` ya terminó de cargar todo). Esto es una decisión implícita del proyecto, no una garantía reforzada por código — ver sección 7 (riesgos de mantenimiento) más abajo.

---

## 5. Dependencias entre módulos

```
header.php ────────────┬──> inc/customizer.php (theme mods: logo, contacto, redes)
                        └──> inc/helpers.php (ce_render_social_icons, ce_get_phone_href)
                        └──> inc/seo.php (ce_construction_breadcrumbs, en <main>)

footer.php ─────────────┬──> inc/customizer.php (theme mods: about, copyright, mapa)
                        ├──> inc/helpers.php (ce_render_social_icons, ce_get_whatsapp_number, ce_get_phone_href)
                        └──> inc/setup.php (dynamic_sidebar 'footer-1'/'footer-2', registrados ahí)

front-page.php ─────────┬──> los 10 template-parts del home
                        ├──> inc/cpt-*.php (todos los CPTs ya registrados)
                        └──> assets/css/main.css + assets/js/main.js (ya enqueados)

archive-servicio.php ───┬──> template-parts/page-hero.php
single-servicio.php     ├──> template-parts/content-servicio.php
                        ├──> template-parts/sidebar-servicios.php
                        ├──> template-parts/cta.php, quote-form.php
                        ├──> inc/helpers.php (ce_get_related_projects, ce_get_related_services,
                        │     ce_get_short_excerpt, ce_render_service_icon, ce_cpt_has_posts)
                        ├──> inc/meta-boxes.php (metadatos guardados: _ce_proyecto_*, _ce_testimonio_*)
                        └──> inc/seo.php (ce_construction_schema_service)

archive-proyecto.php ───┬──> template-parts/page-hero.php
single-proyecto.php     ├──> template-parts/content-proyecto.php
                        ├──> template-parts/sidebar-proyectos.php
                        ├──> template-parts/cta.php, quote-form.php
                        ├──> inc/helpers.php (ce_get_related_services_for_project,
                        │     ce_get_gallery_ids, ce_get_short_excerpt, ce_cpt_has_posts)
                        ├──> inc/meta-boxes.php (metadatos: _ce_proyecto_cliente/ubicacion/fecha/galeria)
                        └──> inc/seo.php (ce_construction_schema_project)

template-parts/quote-form.php (markup) ──> inc/enqueue.php (nonce localizado en ceConstructionData)
                                       └──> inc/quote-form.php (endpoint AJAX ce_submit_quote)

assets/js/main.js (ModuleQuoteForm) ──> inc/quote-form.php (mismo contrato: nombres de
                                         campos, nombre de la acción AJAX, estructura JSON)
```

**Regla de dependencia crítica documentada:** el handler AJAX (`inc/quote-form.php`), el markup del formulario (`template-parts/quote-form.php`) y el módulo JS (`ModuleQuoteForm` en `main.js`) forman un contrato de 3 partes que debe cambiar de forma sincronizada (ya advertido en `HANDOFF.md` sección 14).

---

## 6. Flujo de renderizado del Front Page

> **Actualizado en Sprint UX-1** (fase "Optimización UX / Conversión", paralela al Sprint 8 pausado — ver `docs/CURRENT_UX_SPRINT.md`). El Home dejó de tener una lista fija de secciones: ahora es **data-driven** a través del registro central de `inc/home-builder.php` (Entregable UX-1.1, `DECISIONS.md` D-045) **y configurable desde WordPress** a través del panel "CE: Home Builder" del Customizer (Entregable UX-1.2, `DECISIONS.md` D-046). El diagrama de abajo refleja el mecanismo real vigente, incluida la persistencia ya implementada.

```
Visitante solicita "/"
        │
        ▼
WordPress resuelve front-page.php (existe una Front Page configurada
o WP usa el "latest posts" — en este proyecto front-page.php siempre
gana por su sola existencia en la Template Hierarchy)
        │
        ▼
get_header()
   → barra superior de contacto (theme mods)
   → header principal (logo, wp_nav_menu 'primary', botón Cotizar)
   → menú móvil off-canvas
   → <main id="ce-main-content"> (sin breadcrumbs: is_front_page() es true)
        │
        ▼
$ce_home_sections = ce_construction_home_sections()
   → registro central (inc/home-builder.php): 13 claves → { label, template }
        │
        ▼
foreach ( ce_construction_get_active_home_order() as $key )
   → ce_construction_get_active_home_order() (inc/home-builder.php)
     aplica apply_filters( 'ce_home_active_order', $default_order )
        │
        ├── Sin theme_mod 'ce_home_sections_order' guardado
        │   (instalación nueva, o panel nunca abierto):
        │   ce_construction_filter_home_active_order() (inc/customizer.php,
        │   Entregable UX-1.2) devuelve $default_order sin modificar
        │   → hero → about → services → projects → stats → why_us
        │   → testimonials → gallery → cta → quote_form (10 secciones,
        │   idéntico al comportamiento de UX-1.1, cero regresión).
        │
        └── Con theme_mod guardado (administrador usó el panel
            "CE: Home Builder" del Customizer): se decodifica el
            JSON [{key, enabled}] guardado y se devuelven, en ese
            orden, únicamente las claves con enabled=true.
   → get_template_part( $ce_home_sections[ $key ]['template'] )
        │
        ▼
get_footer()
   → footer (contacto/redes/mapa/horario/enlaces, incluye footer-1/footer-2 si tienen widgets)
   → botones flotantes + modales
   → wp_footer() → main.js se ejecuta (defer)
```

Cada sección sigue usando `ce_cpt_has_posts( $post_type )` (con caché estática por request) como guardia de entrada interna — si el CPT correspondiente no tiene contenido publicado, la sección hace `return;` inmediato sin imprimir HTML ni ejecutar consultas adicionales innecesarias. **Esto no cambia con el Home Builder:** la guarda de "¿el CPT tiene contenido?" vive dentro de cada template-part (como siempre), mientras que la guarda nueva de "¿la sección está activa/en qué posición?" vive un nivel por encima, en `front-page.php` + `inc/home-builder.php`. Son dos responsabilidades independientes que se combinan, no se sustituyen entre sí.

Team, Clients y FAQ están registrados en `ce_construction_home_sections()` desde este Entregable, pero **no forman parte todavía de `ce_construction_default_home_order()`**: sus template-parts (`template-parts/team.php`, `clients.php`, `faq.php`) se crean en el Sprint UX-2.

---

## 7. Flujo de los CPT

Todos los CPTs siguen el mismo patrón de 3 capas:

```
CAPA 1 — Registro (inc/cpt-{nombre}.php, hook 'init')
   register_post_type() + register_taxonomy() si aplica
        │
        ▼
CAPA 2 — Campos personalizados (inc/meta-boxes.php)
   add_meta_box() (hook 'add_meta_boxes')
        → ce_render_{cpt}_fields( $post )  — imprime el HTML del campo + wp_nonce_field()
        ▼
   Al guardar en wp-admin:
   ce_construction_save_meta_boxes( $post_id )  (hook 'save_post', se ejecuta para TODOS
        los post types, pero cada bloque interno verifica su propio nonce específico
        antes de tocar cualquier meta — ver DECISIONS.md D-001)
        │
        ├── Verifica DOING_AUTOSAVE (return si es autosave)
        ├── Verifica wp_is_post_revision() (return si es una revisión — QA-007)
        ├── Verifica el nonce específico del CPT (ej. 'ce_save_servicio_meta')
        ├── Verifica current_user_can( 'edit_post', $post_id )
        └── update_post_meta() con sanitización explícita por campo
        │
        ▼
CAPA 3 — Consumo en el frontend (template-parts/*.php, archive-*.php, single-*.php)
   WP_Query o el Loop estándar → get_post_meta() / get_the_terms()
   → funciones de inc/helpers.php formatean/renderizan el dato
   → escaping explícito (esc_html/esc_attr/esc_url) en el punto de salida
```

**Relación entre CPTs de contenido (Servicio ↔ Proyecto):** no existe un campo relacional explícito en Capa 1/2. La Capa 3 infiere la relación por coincidencia de nombre de taxonomía (`ce_get_related_projects()`, `ce_get_related_services()`, `ce_get_related_services_for_project()` en `inc/helpers.php`), documentado como decisión heurística en `DECISIONS.md` (D-010, D-015).

**CPT `cotizacion` (caso especial):** no sigue la Capa 2 clásica de metaboxes de administración — sus meta se escriben programáticamente desde `inc/quote-form.php` en el momento de la inserción (`wp_insert_post()` + `update_post_meta()` inmediato), nunca desde un formulario de edición en wp-admin. Es intencionalmente `create_posts => do_not_allow` (ver D-002).

---

## 8. Flujo del formulario de cotización

```
Visitante llena el formulario en template-parts/quote-form.php
        │  (incluye: wp_nonce_field('ce_quote_form_action', ...),
        │   honeypot oculto 'ce_website', campos name/email/phone/
        │   company/service/message/attachment)
        ▼
JS: ModuleQuoteForm (assets/js/main.js)
        │
        ├── Validación en cliente (UX, no autoritativa)
        ├── FormData + action=ce_submit_quote + ce_quote_nonce
        │   (nonce obtenido de ceConstructionData.quoteNonce,
        │    localizado en inc/enqueue.php vía wp_create_nonce)
        └── fetch(CE.ajaxUrl, { method: 'POST', body: formData })
        │
        ▼
PHP: ce_construction_handle_quote_form() (inc/quote-form.php)
   enganchada a wp_ajax_ce_submit_quote Y wp_ajax_nopriv_ce_submit_quote
   (acepta tanto visitantes anónimos como usuarios logueados)
        │
        ├── 1. wp_verify_nonce() — rechaza si es inválido
        ├── 2. Honeypot — rechaza si el campo oculto viene relleno
        ├── 2 bis. Rate-limiting por IP (transient, máx. 3 envíos/10 min) — QA-004
        ├── 3. Sanitización de todos los campos ($_POST)
        ├── 4. Validación server-side (autoritativa; is_email, regex teléfono, longitudes mínimas)
        ├── 5. Si hay adjunto:
        │      ├── wp_check_filetype_and_ext() + whitelist de extensión real — QA-001
        │      ├── wp_handle_upload() (mueve el archivo a wp-content/uploads/)
        │      └── (el registro como attachment ocurre en el paso 6, una vez
        │           existe el post_id padre)
        ├── 6. wp_insert_post() → crea el CPT 'cotizacion' (post_status: private)
        │      └── Si hubo adjunto: wp_insert_attachment() + wp_generate_attachment_metadata()
        │          vinculado como post_parent = cotización — QA-002
        ├── 7. wp_mail() al correo configurado en el Customizer (o admin_email de fallback),
        │      con el adjunto (si existe) adjuntado por ruta física
        └── wp_send_json_success() / wp_send_json_error() — siempre termina la ejecución
        │
        ▼
JS: ModuleQuoteForm recibe la respuesta JSON
        ├── Éxito → ModuleModals.open('ce-modal-success') + reset del formulario
        └── Error → ModuleModals.open('ce-modal-error') + marca campos inválidos devueltos por el servidor
        │
        ▼
(Proceso independiente, en segundo plano)
Cron diario 'ce_construction_quote_cleanup_event' (programado en after_switch_theme)
        └── ce_construction_purge_old_quotes() borra cotizaciones + su attachment
            vinculado más antiguas que ce_construction_quote_retention_days()
            (365 días por defecto, filtrable) — QA-003
```

---

## 9. Flujo de carga de CSS y JavaScript

```
inc/enqueue.php, enganchado a 'wp_enqueue_scripts' (se ejecuta en cada
request de frontend, antes de que header.php imprima <head>):

1. wp_enqueue_style('ce-google-fonts', ... , null)          [CDN externo, sin dependencia]
2. wp_enqueue_style('font-awesome', ..., '6.5.1')            [CDN externo, sin dependencia]
3. wp_enqueue_style('ce-construction-style', style.css, [],
                     ce_construction_asset_version('style.css'))
4. wp_enqueue_style('ce-construction-main', main.css,
                     ['ce-construction-style'],
                     ce_construction_asset_version('assets/css/main.css'))  [depende de #3]
5. wp_enqueue_script('ce-construction-main', main.js, [],
                     ce_construction_asset_version('assets/js/main.js'), true)
   wp_script_add_data('ce-construction-main', 'defer', true)  [soporte nativo WP 6.3+]

   NOTA (QA-030, Sprint 8, Entregable 8.2): la versión de estos 3 assets
   ya no viene de CE_THEME_VERSION, sino de ce_construction_asset_version()
   (inc/enqueue.php), que devuelve filemtime() del archivo real en disco.
   El cache-busting es ahora automático: cambia solo en cuanto el archivo
   cambia, sin depender de que nadie actualice ninguna constante a mano.
   CE_THEME_VERSION sigue existiendo (ahora derivada de
   wp_get_theme()->get('Version'), que lee la cabecera de style.css) pero
   es puramente informativa, ya no interviene en el cache-busting. Ver
   DECISIONS.md D-044.
6. wp_localize_script(...) → inyecta window.ceConstructionData ANTES del <script>
   (ajaxUrl, quoteNonce, whatsapp, i18n)

Orden real de impresión en el HTML final:
   <head>
     <link ce-google-fonts>
     <link font-awesome>
     <link ce-construction-style (style.css)>
     <link ce-construction-main (main.css)>
     <style id="ce-customizer-vars">  ← inyectado por inc/customizer.php en wp_head,
                                         DESPUÉS de main.css, para poder sobreescribir
                                         sus variables :root con los valores guardados
   </head>
   <body>
     ... todo el HTML de la página ...
     <script>window.ceConstructionData = {...}</script>
     <script defer src="main.js"></script>
   </body>

main.js se ejecuta al final del documento (defer + posición física al final
del <body> vía wp_footer()) → document.addEventListener('DOMContentLoaded', ...)
inicializa los 13 módulos ES6 (ver TODO.md sección 7), cada uno auto-verificando
si su marcado existe en el DOM antes de operar.
```

**Por qué `main.css`/`main.js` son monolíticos y no se dividen por módulo:** cada sprint de contenido (Servicios, Proyectos) añadió una nueva sección numerada al final de estos dos archivos (secciones 20-22 en `main.css` a la fecha de este documento) en vez de crear `services.css`, `proyectos.css`, etc. separados. Esto es una decisión deliberada, no una omisión: reduce el número de requests HTTP (un solo archivo CSS y uno JS para todo el sitio) y evita que WordPress tenga que resolver dependencias de carga condicional entre archivos de estilos por tipo de contenido. La contrapartida documentada (ver `QA_REPORT.md` QA-012 y `PROJECT_STATUS.md`) es que ambos archivos crecen indefinidamente; se acepta como tradeoff de performance vs. modularidad de archivo.

---

## 10. Convenciones de organización utilizadas

- **Prefijo de funciones:** `ce_` o `ce_construction_` en el 100% de las funciones PHP del proyecto (ninguna excepción encontrada al verificar `grep -rhoE "^function " inc/*.php`).
- **Un archivo, una responsabilidad:** cada CPT en su propio archivo `inc/cpt-{nombre}.php`, incluso cuando 4 de ellos son estructuralmente casi idénticos (~30 líneas) — decisión explícita de **no** unificarlos en un solo archivo iterado por array, para mantener la convención "un archivo = un concepto de negocio" (ver `QA_REPORT.md` QA-029, donde se evaluó y se descartó unificarlos).
- **Extensión aditiva sobre reescritura:** desde el Sprint 3, toda ampliación a un archivo ya aprobado (`inc/helpers.php`, `inc/seo.php`, `assets/css/main.css`) se realiza agregando código al final del archivo con un bloque de comentario que indica en qué sprint se añadió y por qué, nunca reescribiendo lo existente. Las únicas excepciones son correcciones de bugs puntuales (Sprint 5, Fase 1), documentadas explícitamente línea por línea en `CHANGELOG.md`/`DECISIONS.md`.
- **Componentización progresiva:** un componente no se generaliza "por si acaso" — se construye específico para su primer uso (`template-parts/hero.php` para el home) y solo se extrae a una versión parametrizable reutilizable (`template-parts/page-hero.php`) cuando un segundo módulo lo necesita genuinamente (ver `DECISIONS.md` D-009).
- **Duplicación deliberada documentada:** cuando evitar duplicación exigiría modificar un archivo ya aprobado fuera del alcance del sprint activo, el proyecto prefiere una duplicación pequeña y documentada (ver D-010, D-013) sobre tocar código fuera de alcance sin autorización.
- **Reutilización de clases CSS entre módulos de contenido distintos:** clases estructurales sin significado visual específico (ej. `.ce-service-nav`, `.ce-service-content`) se comparten entre Servicios y Proyectos en vez de duplicarse con otro nombre (ver D-016).
- **Seguridad por defecto:** todo formulario/metabox usa un nonce con acción específica (nunca un nonce global reutilizado), toda escritura a la base de datos verifica `current_user_can()`, y toda salida a HTML pasa por una función de escaping de WordPress.
- **Verificación sin entorno WordPress real:** dado que el entorno de desarrollo no tiene PHP/WordPress instalados (ver limitación metodológica en `QA_REPORT.md`), cada entrega se verifica con balance de llaves/paréntesis (`grep -o` conteo) y `node --check` sobre JS, documentado explícitamente en cada cierre de sprint.
- **Gestión de Sprints por Entregables (convención de proceso, no de código):** desde la metodología permanente adoptada tras el Entregable 6A (ver `HANDOFF.md` sección 16 y `DECISIONS.md` D-030), todo Sprint se planifica dividido en Entregables — unidades funcionales completas, terminables en una sola sesión, sin reducir nunca la calidad del código por reducir el alcance de trabajo de una sesión.
- **Actualización incremental de documentación (refinamiento de la convención anterior, tras el Entregable 6B.2):** cada documento de control se actualiza únicamente cuando cambia lo que ese documento específico existe para registrar — nunca "por rutina". `HANDOFF.md` dejó de actualizarse en cada Entregable y solo lo hace al cerrar un Sprint completo, al cerrar una sesión continuable, o ante un cambio importante de arquitectura. El detalle operativo de corto plazo (Entregables del Sprint activo, archivos tocados, próximo paso) vive ahora en `CURRENT_SPRINT.md`, documento nuevo y compacto que sí se actualiza en cada Entregable. Ver `DECISIONS.md` D-034.

---

## Historial de cambios de este documento

- **v0.4.1 (Sprint 5, Fase 2):** creación inicial de `ARCHITECTURE.md`, documentando el estado real del proyecto tras Sprints 1-4 y las correcciones de QA de la Fase 1 de este mismo sprint.
- **v0.5.0 (Sprint 5, Fase 3, cierre):** se completó el Módulo de Equipo y Clientes. Cambios relevantes a la arquitectura documentada: (1) `inc/cpt-clientes.php` cambió `has_archive` de `false` a `true` (ver sección 3 y `DECISIONS.md` D-025), habilitando `archive-clientes.php`; (2) `inc/seo.php` ganó dos nuevas funciones de Schema.org (`ce_construction_schema_person()`, `ce_construction_schema_client_organization()`) y completó la rama de breadcrumbs de archivo de Cliente, antes inalcanzable; (3) `assets/css/main.css` sumó la sección 22 (tarjetas de equipo, grid de logos de clientes); (4) `assets/js/main.js` no cambió — Equipo/Clientes no requieren ningún componente interactivo nuevo. El flujo de los CPT (sección 7) y de carga de CSS/JS (sección 9) descritos arriba siguen aplicando sin modificación al nuevo módulo.
- **v0.6.0 (Entregable 6A):** `index.php` añadido como fallback obligatorio de WordPress (ver sección 3, nueva fila de la tabla). No cambia ningún flujo descrito en las secciones 4-9 — `index.php` reutiliza exclusivamente lo ya documentado (helpers, clases CSS, paginación, breadcrumbs globales). Se corrigió, de paso, un bug preexistente no arquitectónico en `main.css` (utilidades `.ce-mt-6`/`.ce-mb-6` faltantes desde el Sprint 3) — ver `DECISIONS.md` D-029.
- **v0.6.0 (post Entregable 6A):** se incorporó a la sección 10 la convención de "Gestión de Sprints por Entregables" como nueva regla permanente de proceso (no de código) — ver `HANDOFF.md` sección 16 y `DECISIONS.md` D-030 para el detalle completo.
- **v0.6.2 (post Entregable 6B.2):** se refinó la convención de proceso de la sección 10 con la política de actualización incremental de documentación y la creación de `CURRENT_SPRINT.md` — ver `DECISIONS.md` D-034.
- **v0.8.1 (Sprint 8, Entregable 8.2):** corrección de QA-030. Cambio real de mecanismo (no solo de valor): el flujo de carga de CSS/JS descrito en la sección 9 ya no usa `CE_THEME_VERSION` como parámetro `$ver` de los 3 assets propios del tema — usa `ce_construction_asset_version()` (nueva función en `inc/enqueue.php`), basada en `filemtime()` real de cada archivo. `CE_THEME_VERSION` (sección 3, tabla de `functions.php`) pasa de ser un valor hardcodeado a derivarse de `wp_get_theme()->get('Version')`, y su rol pasa de "versión de cache-busting" a "versión informativa general del tema". Ver `DECISIONS.md` D-044.
- **v0.8.2 (Sprint UX-1, Entregable UX-1.1 — fase "Optimización UX / Conversión", paralela al Sprint 8 pausado en su Entregable 8.2):** Home Builder, base arquitectónica. Nuevo archivo `inc/home-builder.php` (sección 3, tabla de `inc/`); `front-page.php` deja de tener una lista fija de `get_template_part()` y pasa a iterar el registro central de secciones (sección 6, reescrita para reflejar el mecanismo data-driven). Sin cambios de comportamiento visible: el orden por defecto reproduce exactamente el orden anterior. Ver `DECISIONS.md` D-045 y `docs/CURRENT_UX_SPRINT.md` para el seguimiento dedicado de esta fase.
- **v0.8.3 (Sprint UX-1, Entregable UX-1.2 — fase "Optimización UX / Conversión", paralela al Sprint 8 pausado):** Home Builder, persistencia real. Nueva sección `ce_section_home_builder` en `inc/customizer.php` (sección 3, tabla de `inc/`; sección 4, diagrama de módulos) con el control custom `CE_Customize_Home_Sections_Control`, enganchada al filtro `ce_home_active_order` ya expuesto por UX-1.1 — sin modificar `inc/home-builder.php` ni `front-page.php` (sección 6, reescrita para reflejar la persistencia ya activa). `inc/enqueue.php` gana el encolado admin-only de `assets/js/admin-home-builder.js` (jQuery UI Sortable, ya incluido en WordPress core). Ver `DECISIONS.md` D-046 y `docs/CURRENT_UX_SPRINT.md`.
