# CE Construction — TREE.md

> Árbol completo del proyecto con estado por archivo.
> ✅ Implementado &nbsp;|&nbsp; 🟡 En desarrollo &nbsp;|&nbsp; ⬜ Pendiente

> **Actualizado en la sesión de cierre del Sprint UX-8 (Entregables UX-8.1/D-108 y UX-8.2/D-109-D-110) — PRIMERA VERSIÓN ESTABLE (v1.0.0, ver `docs/DECISIONS.md` D-111).** Sección previa (cierre de UX-7/UX-10/UX-11) conservada; se incorporan aquí los archivos de UX-8.1 y UX-8.2.

```
ce-construction-theme/
│
├── style.css                          ✅ Version: 1.0.0 — PRIMERA VERSIÓN ESTABLE (SemVer, D-111)
├── functions.php                      ✅ CE_THEME_VERSION derivada de wp_get_theme() (QA-030, D-044). $modules incluye inc/section-shortcode.php (UX-6.2), inc/home-builder.php (UX-1.1), inc/hero-image-position.php (UX-11) e inc/quote-attachments.php/inc/form-guards.php (Sprint 8)
├── header.php                         ✅ tabindex="-1" en <main> (QA-017, Sprint 8 Entregable 8.1)
├── footer.php                         ✅ 🔧 UX-7.5: logo del footer vía ce_render_footer_logo(). 🔧 UX-7.10: llamada a template-parts/offer-popup.php, después del modal de Cotización
├── front-page.php                     ✅ Loop data-driven sobre inc/home-builder.php (UX-1.1) + ce_construction_get_home_section_args() (UX-6.2/D-060)
├── index.php                          ✅
├── page.php                           ✅ 🆕 UX-6.1 (D-059) — resuelve QA-041. 🔧 UX-6.3 — ce-content-breakout (D-061)
├── single.php                         ✅ 🔧 UX-6.3 — ce-content-breakout (D-061)
├── comments.php                       ✅
├── 404.php                            ✅
├── archive.php                        ✅ Sprint 7, Entregable 7.2
├── archive-servicio.php               ✅
├── single-servicio.php                ✅ 🔧 UX-2.2: accordion FAQ vía content-faq-accordion.php compartido
├── archive-proyecto.php               ✅
├── archive-equipo.php                 ✅
├── single-equipo.php                  ✅
├── archive-clientes.php               ✅
├── single-clientes.php                ✅
├── single-proyecto.php                ✅ 🔧 UX-8.1 (D-108): mosaico de galería reescrito para ítems mixtos (imagen/video), reutiliza ModuleLightbox ya extendido desde UX-7.8
├── screenshot.png                     ✅ Sprint 7, Entregable 7.4
│
├── docs/
│   ├── PROJECT_STATUS.md, TODO.md, TREE.md, HANDOFF.md, CHANGELOG.md,
│   │   DECISIONS.md, QA_REPORT.md, ARCHITECTURE.md                       ✅ (reconciliados en el cierre de UX-7; cierre de versión v1.0.0 reflejado en PROJECT_STATUS/TODO/HANDOFF/CHANGELOG/DECISIONS/TREE)
│   ├── CURRENT_SPRINT.md              ✅ Tracker oficial del Sprint 8 (QA) — completo y cerrado (D-107)
│   ├── CURRENT_UX_SPRINT.md           ✅ 🔧 Sprint UX-8 cerrado (UX-8.1/UX-8.2) — nota de cierre v1.0.0 añadida
│   ├── UX_CONVERSION_ANALISIS_Y_PLAN.md ✅ (sin cambios en este cierre — diferido, D-034)
│
├── inc/
│   ├── setup.php                      ✅
│   ├── enqueue.php                    ✅ ce_construction_asset_version() vía filemtime() (QA-030, D-044). Encolado de admin-home-builder.js/admin-hero-slides.js/admin-stats-items.js/admin-trust-badges.js/admin-proyecto-gallery.js — 🔧 UX-8.1 (D-108): ceProyectoGalleryData ampliada con etiquetas de video — 🔧 UX-8.2 (D-109): etiquetas de favorita/destacado añadidas
│   ├── customizer.php                 ✅ Secciones añadidas por UX-1.2 (Home Builder), UX-4.1/4.2 (Hero video/slider), UX-5.1 (CTA Secundario), UX-6 (sin cambios), UX-7.2 (layout Hero + Quote Form), UX-7.4 (icono/color CTA), UX-7.5 (logo footer), UX-7.6 (Estadísticas), UX-7.7 (Insignias de Confianza), UX-7.9 (Financiamiento), UX-7.10 (Popup de Oferta, incl. refuerzo D-080), UX-10.3 (Google Reviews/Trustindex), UX-11 (D-086: ce_hero_overlay_color/direction/extent; descripción de ce_hero_type actualizada tras D-084)
│   ├── helpers.php                    ✅ ce_get_hero_slide_ids() (UX-4.2), ce_construction_get_hero_media_state() (UX-7.1, ahora exclusiva del Home, slides con posición desde UX-11/D-083), ce_get_testimonio_video() (UX-7.8), ce_get_offer_popup_data() (UX-7.10, ampliada en D-080), ce_construction_decode_trust_badges()/ce_construction_get_trust_badges()/ce_construction_trust_badge_title() (UX-7.7), ce_render_footer_logo() (UX-7.5), ce_construction_get_page_hero_image_url()/ce_construction_hex_to_rgb()/ce_construction_get_hero_overlay_gradient_css() (UX-11, D-084/D-086) — 🔧 UX-8.1 (D-108): ce_construction_decode_proyecto_media_json()/ce_construction_get_proyecto_media_raw()/ce_construction_get_proyecto_media_items() (galería mixta de Proyecto) — 🔧 UX-8.2 (D-109, reemplazada por D-110): ce_construction_resolve_home_gallery_images() (plural, múltiples favoritas) + ce_construction_get_home_gallery_images()
│   ├── cpt-servicios.php              ✅
│   ├── cpt-proyectos.php              ✅
│   ├── cpt-testimonios.php            ✅ (registro del CPT sin cambios; metadatos de video de UX-7.8 gestionados desde meta-boxes.php)
│   ├── cpt-equipo.php                 ✅
│   ├── cpt-clientes.php               ✅
│   ├── cpt-faq.php                    ✅
│   ├── meta-boxes.php                 ✅ 🔧 UX-7.8: metabox ce_testimonio_video (_ce_testimonio_video_id/_ce_testimonio_video_url) — 🔧 UX-8.1 (D-108): ce_render_proyecto_gallery() reescrita (galería mixta imagen/video); guardado deriva _ce_proyecto_galeria automáticamente desde _ce_proyecto_media — 🔧 UX-8.2 (D-109/D-110): checkbox _ce_proyecto_destacado en ce_render_proyecto_fields(); botón ★/☆ de favorita por ítem de imagen (sin exclusividad tras D-110)
│   ├── quote-form.php                 ✅ sin cambios en toda la fase UX (protegido explícitamente, D-053/D-056/D-064/D-079) — 🔧 Sprint 8, Entregable 8.3 (D-096, QA-031): subida redirigida a carpeta protegida + renombrado aleatorio + columna "Adjunto" en el admin
│   ├── form-guards.php                ✅ 🆕 Sprint 8, Entregable 8.4 (D-098/D-099) — QA-032/033/034: rate-limit e idempotencia atómicos
│   ├── quote-attachments.php          ✅ 🆕 Sprint 8, Entregable 8.3 (D-096) — QA-031: carpeta protegida + endpoint autenticado de descarga
│   ├── seo.php                        ✅ ce_construction_output_json_ld() con endurecimiento contra </script> (QA-014); canonical (QA-038, D-102); Twitter Card completo y BreadcrumbList Persona/Cliente/BlogPosting (QA-039/QA-040, D-106)
│   ├── widgets.php                    ✅ Sprint 7, Entregable 7.1
│   ├── home-builder.php               ✅ 🆕 UX-1.1. Claves registradas a la fecha: hero, about, services, projects, stats, why_us, testimonials, gallery, cta, quote_form, team, clients, faq, cta_secondary (UX-5.1), trust_badges (UX-7.7), financing (UX-7.9), testimonials_full (UX-10.1/10.2), google_reviews (UX-10.3). El Popup de Oferta (UX-7.10) y la Galería del Home (UX-8.2) NO se registran aquí — la primera no es una sección posicionable, la segunda ya tenía su propia clave `gallery` desde el origen
│   ├── section-shortcode.php          ✅ 🆕 UX-6.2 (D-060/D-062) — [ce_section key="..."]
│   ├── hero-image-position.php        ✅ 🆕 UX-11 (D-085) — posición de fondo configurable por imagen (alternativa nativa al punto focal, sin plugin)
│
├── template-parts/                    ✅ 27 archivos
│   ├── hero.php                       🔧 UX-4.1 (tipo imagen/video/overlay) + UX-4.2 (slider) + UX-7.1 (media state, ahora exclusiva del Home) + UX-7.2 (layout de columnas + slot Quote Form, D-064/D-065) + UX-11 (D-085/D-086/D-089: posición de imagen, overlay configurable, panel del formulario sin recorte)
│   ├── page-hero.php                  🔧 UX-11 (D-084) — ya NO comparte el modo video/slider global del Home; usa siempre imagen destacada propia + fallback a la imagen del Home; overlay configurable (D-086) y posición de imagen (D-085) igual que hero.php
│   ├── about.php, services.php, projects.php, stats.php, why-us.php,
│   │   content-servicio.php, content-proyecto.php,
│   │   content-equipo.php, content-cliente.php, content-fallback.php,
│   │   sidebar-servicios.php, sidebar-proyectos.php, no-results.php     ✅ sin cambios en la fase UX
│   ├── gallery.php                                                      ✅ 🔧 **Reescrito íntegramente — UX-8.2 (D-109/D-110):** consume ce_construction_get_home_gallery_images() (curación: destacados + favoritas múltiples + relleno, tope 8); responsive con carrusel móvil (ModuleHomeGallerySlider)
│   ├── team.php, clients.php                                            ✅ 🆕 UX-2.1 (D-047)
│   ├── faq.php, content-faq-accordion.php                               ✅ 🆕 UX-2.2 (D-048)
│   ├── cta.php                                                          🔧 UX-5.1 — variant=secondary (cta_secondary, D-056). 🔧 UX-7.4 — icono/color configurables
│   ├── quote-form.php                                                   🔧 UX-3.2 — contexto modal (D-053). 🔧 UX-7.2 — contexto 'hero' + badge (D-064/D-065). 🔧 UX-7.7 — modo compacto de trust-badges.php en contexto 'hero'. 🔧 UX-11 (D-091/D-092/D-093) — expansión progresiva, opacidad de panel configurable, recompactado
│   ├── testimonials.php                                                 🔧 UX-10.2 — CTA "Ver todos los testimonios" condicional
│   ├── content-testimonio-card.php                                      ✅ 🆕 (extraída como partial reutilizable) 🔧 UX-7.8 — $args['video_enabled'], poster/Play condicional
│   ├── trust-badges.php                                                 ✅ 🆕 UX-7.7 (D-071) — modo normal + modo compacto ($args['compact'])
│   ├── financing.php                                                    ✅ 🆕 UX-7.9 (D-078)
│   ├── offer-popup.php                                                  ✅ 🆕 UX-7.10 (D-079, ampliado visualmente en D-080, movimiento JS en D-081)
│   ├── testimonials-full.php                                            ✅ 🆕 UX-10.1/10.2 (D-073) — grid + paginación, solo CPT propio
│   └── google-reviews.php                                               ✅ 🆕 UX-10.3 (D-075/D-076) — embed Trustindex tal cual, sección independiente
│
└── assets/
    ├── css/main.css                   ✅ 34+ secciones. 9: iconos sociales del header (UX-11/QA-043, D-090). 10: altura del Hero reducida (UX-11/D-088). 24: QA-018. 25: modal Cotización (UX-3.2). 26/27: Hero video/slider (UX-4.1/4.2, exclusivos del Home desde UX-11). 28 (+28 bis): layout Hero/Quote Form (UX-7.2/D-065) + corrección de recorte, panel separado y opacidad configurable (UX-11/D-089/D-092). 29: Estadísticas (UX-7.6). 30: Insignias de Confianza (UX-7.7). 31: Google Reviews (UX-10.3). 32: video en testimonios (UX-7.8). 33: Popup de Oferta (UX-7.10, base D-079 + refuerzo D-080 + movimiento JS D-081). 34: galería mixta de Proyecto (.ce-gallery-item--video) + repeater admin (UX-8.1, D-108). **Nueva (UX-8.2, D-109):** `.ce-home-gallery`/`.ce-home-gallery__track` — carrusel móvil de la Galería del Home, reutiliza componentes de slider ya existentes (`.ce-slider-arrow`/`.ce-slider-dot`/`.ce-slider-pause`). `.ce-content-breakout` (UX-6.3/D-061), `.ce-max-w-content` sin modificar
    ├── js/main.js                     ✅ 16 módulos. Añadidos en la fase UX: createSliderController() (fábrica compartida, UX-4.2) + ModuleHeroSlider (UX-4.2); ModuleLightbox extendido para video local/embebido (UX-7.8, D-077); ModuleOfferPopup (UX-7.10, D-079, con rebote/nudge de D-081); ModuleHeroFormProgressive (UX-11, D-091/D-093); FocusTrap (Sprint 8, Entregable 8.6, D-104); **ModuleHomeGallerySlider (UX-8.2, D-109)** — carrusel accesible de la Galería del Home, reutiliza createSliderController()
    ├── js/admin-home-builder.js       ✅ UX-1.2 — control custom Home Builder (drag&drop)
    ├── js/admin-hero-slides.js        ✅ UX-4.2 — control custom ce_hero_slides
    ├── js/admin-stats-items.js        ✅ UX-7.6 — control custom del repeater de Estadísticas
    ├── js/admin-trust-badges.js       ✅ UX-7.7 — control custom del repeater de Insignias de Confianza
    ├── js/admin-proyecto-gallery.js   ✅ 🔧 UX-8.1 (D-108) — reescrito para el repeater de galería mixta (imagen/video local/video URL). 🔧 UX-8.2 (D-109, ajustado en D-110) — botón de favorita por ítem, sin exclusividad
    └── img/                           ⬜ Vacía (assets/images/ contiene un archivo de prueba subido por el usuario, fuera del árbol versionado del tema)
```

---

### Verificación de cierre — Sprint UX-8 completo + Primera Versión Estable (v1.0.0, esta sesión)

Con la aprobación explícita de UX-8.1 (D-108) y UX-8.2 (D-109 + D-110), **el Sprint UX-8 queda completo**. No queda ningún Sprint ni Entregable pendiente en todo el proyecto. El usuario declaró esta la Primera Versión Estable del tema — `style.css` actualizado de `Version: 0.8.5` a `Version: 1.0.0` (SemVer, ver `docs/DECISIONS.md` D-111). Ningún archivo del Sprint 8 (QA) fue tocado por este cierre.
