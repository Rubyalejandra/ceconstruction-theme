# CE Construction — TREE.md

> Árbol completo del proyecto con estado por archivo.
> ✅ Implementado &nbsp;|&nbsp; 🟡 En desarrollo &nbsp;|&nbsp; ⬜ Pendiente

> **Actualizado en la sesión de cierre del Sprint UX-7 / UX-10, y nuevamente tras el Sprint UX-11.** Este archivo estaba desactualizado desde el cierre del Sprint UX-6 (criterio D-034: se difería a un punto de cierre de Sprint más significativo). Con UX-7/UX-10 y ahora UX-11 formalmente cerrados y aprobados, se incorporan aquí todos los archivos nuevos/modificados de UX-7.1 a UX-7.10, UX-10.1 a UX-10.3, y el Entregable único de UX-11 (D-083 a D-090).

```
ce-construction-theme/
│
├── style.css                          ✅ Version: 0.8.5
├── functions.php                      ✅ CE_THEME_VERSION derivada de wp_get_theme() (QA-030, D-044). $modules incluye inc/section-shortcode.php (UX-6.2), inc/home-builder.php (UX-1.1) e inc/hero-image-position.php (UX-11)
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
├── single-proyecto.php                ✅
├── screenshot.png                     ✅ Sprint 7, Entregable 7.4
│
├── docs/
│   ├── PROJECT_STATUS.md, TODO.md, TREE.md, HANDOFF.md, CHANGELOG.md,
│   │   DECISIONS.md, QA_REPORT.md, ARCHITECTURE.md                       ✅ (Sprint 8 — reconciliados en la sesión de cierre de UX-7)
│   ├── CURRENT_SPRINT.md              ✅ 🔧 Reconstruido en la sesión de cierre de UX-7 (había sido sobrescrito por error con una copia de CURRENT_UX_SPRINT.md) — tracker oficial del Sprint 8
│   ├── CURRENT_UX_SPRINT.md           ✅ 🔧 Bloque "ESTADO FINAL" añadido — Sprint UX-7 y UX-10 cerrados
│   ├── UX_CONVERSION_ANALISIS_Y_PLAN.md ✅
│
├── inc/
│   ├── setup.php                      ✅
│   ├── enqueue.php                    ✅ ce_construction_asset_version() vía filemtime() (QA-030, D-044). Encolado de admin-home-builder.js/admin-hero-slides.js/admin-stats-items.js/admin-trust-badges.js
│   ├── customizer.php                 ✅ Secciones añadidas por UX-1.2 (Home Builder), UX-4.1/4.2 (Hero video/slider), UX-5.1 (CTA Secundario), UX-6 (sin cambios), UX-7.2 (layout Hero + Quote Form), UX-7.4 (icono/color CTA), UX-7.5 (logo footer), UX-7.6 (Estadísticas), UX-7.7 (Insignias de Confianza), UX-7.9 (Financiamiento), UX-7.10 (Popup de Oferta, incl. refuerzo D-080), UX-10.3 (Google Reviews/Trustindex), UX-11 (D-086: ce_hero_overlay_color/direction/extent; descripción de ce_hero_type actualizada tras D-084)
│   ├── helpers.php                    ✅ ce_get_hero_slide_ids() (UX-4.2), ce_construction_get_hero_media_state() (UX-7.1, ahora exclusiva del Home, slides con posición desde UX-11/D-083), ce_get_testimonio_video() (UX-7.8), ce_get_offer_popup_data() (UX-7.10, ampliada en D-080), ce_construction_decode_trust_badges()/ce_construction_get_trust_badges()/ce_construction_trust_badge_title() (UX-7.7), ce_render_footer_logo() (UX-7.5), ce_construction_get_page_hero_image_url()/ce_construction_hex_to_rgb()/ce_construction_get_hero_overlay_gradient_css() (UX-11, D-084/D-086)
│   ├── cpt-servicios.php              ✅
│   ├── cpt-proyectos.php              ✅
│   ├── cpt-testimonios.php            ✅ (registro del CPT sin cambios; metadatos de video de UX-7.8 gestionados desde meta-boxes.php)
│   ├── cpt-equipo.php                 ✅
│   ├── cpt-clientes.php               ✅
│   ├── cpt-faq.php                    ✅
│   ├── meta-boxes.php                 ✅ 🔧 UX-7.8: nuevo metabox ce_testimonio_video (_ce_testimonio_video_id/_ce_testimonio_video_url), nonce y guardado independientes
│   ├── quote-form.php                 ✅ sin cambios en toda la fase UX (protegido explícitamente, D-053/D-056/D-064/D-079)
│   ├── seo.php                        ✅ ce_construction_output_json_ld() con endurecimiento contra </script> (QA-014, Sprint 8 Entregable 8.1)
│   ├── widgets.php                    ✅ Sprint 7, Entregable 7.1
│   ├── home-builder.php               ✅ 🆕 UX-1.1. Claves registradas a la fecha: hero, about, services, projects, stats, why_us, testimonials, gallery, cta, quote_form, team, clients, faq, cta_secondary (UX-5.1), trust_badges (UX-7.7), financing (UX-7.9), testimonials_full (UX-10.1/10.2), google_reviews (UX-10.3). El Popup de Oferta (UX-7.10) NO se registra aquí — no es una sección posicionable
│   └── section-shortcode.php          ✅ 🆕 UX-6.2 (D-060/D-062) — [ce_section key="..."]
│   └── hero-image-position.php        ✅ 🆕 UX-11 (D-085) — posición de fondo configurable por imagen (alternativa nativa al punto focal, sin plugin)
│
├── template-parts/                    ✅ 27 archivos
│   ├── hero.php                       🔧 UX-4.1 (tipo imagen/video/overlay) + UX-4.2 (slider) + UX-7.1 (media state, ahora exclusiva del Home) + UX-7.2 (layout de columnas + slot Quote Form, D-064/D-065) + UX-11 (D-085/D-086/D-089: posición de imagen, overlay configurable, panel del formulario sin recorte)
│   ├── page-hero.php                  🔧 UX-11 (D-084) — REESCRITO: ya NO comparte el modo video/slider global del Home (revierte esa parte de D-063); usa siempre imagen destacada propia + fallback a la imagen del Home; overlay configurable (D-086) y posición de imagen (D-085) igual que hero.php
│   ├── about.php, services.php, projects.php, stats.php, why-us.php,
│   │   gallery.php, content-servicio.php, content-proyecto.php,
│   │   content-equipo.php, content-cliente.php, content-fallback.php,
│   │   sidebar-servicios.php, sidebar-proyectos.php, no-results.php     ✅ sin cambios en la fase UX
│   ├── team.php, clients.php                                            ✅ 🆕 UX-2.1 (D-047)
│   ├── faq.php, content-faq-accordion.php                               ✅ 🆕 UX-2.2 (D-048)
│   ├── cta.php                                                          🔧 UX-5.1 — variant=secondary (cta_secondary, D-056). 🔧 UX-7.4 — icono/color configurables
│   ├── quote-form.php                                                   🔧 UX-3.2 — contexto modal (D-053). 🔧 UX-7.2 — contexto 'hero' + badge (D-064/D-065). 🔧 UX-7.7 — modo compacto de trust-badges.php en contexto 'hero'
│   ├── testimonials.php                                                 🔧 UX-10.2 — CTA "Ver todos los testimonios" condicional
│   ├── content-testimonio-card.php                                      ✅ 🆕 (extraída como partial reutilizable) 🔧 UX-7.8 — $args['video_enabled'], poster/Play condicional
│   ├── trust-badges.php                                                 ✅ 🆕 UX-7.7 (D-071) — modo normal + modo compacto ($args['compact'])
│   ├── financing.php                                                    ✅ 🆕 UX-7.9 (D-078)
│   ├── offer-popup.php                                                  ✅ 🆕 UX-7.10 (D-079, ampliado visualmente en D-080)
│   ├── testimonials-full.php                                            ✅ 🆕 UX-10.1/10.2 (D-073) — grid + paginación, solo CPT propio
│   └── google-reviews.php                                               ✅ 🆕 UX-10.3 (D-075/D-076) — embed Trustindex tal cual, sección independiente
│
└── assets/
    ├── css/main.css                   ✅ 33 secciones. 9: iconos sociales del header (UX-11/QA-043, D-090). 10: altura del Hero reducida (UX-11/D-088). 24: QA-018. 25: modal Cotización (UX-3.2). 26/27: Hero video/slider (UX-4.1/4.2, exclusivos del Home desde UX-11). 28 (+28 bis): layout Hero/Quote Form (UX-7.2/D-065) + corrección de recorte y panel separado (UX-11/D-089). 29: Estadísticas (UX-7.6). 30: Insignias de Confianza (UX-7.7). 31: Google Reviews (UX-10.3). 32: video en testimonios (UX-7.8). 33: Popup de Oferta (UX-7.10, base D-079 + refuerzo D-080 + movimiento JS D-081). .ce-content-breakout añadida (UX-6.3/D-061), .ce-max-w-content sin modificar
    ├── js/main.js                     ✅ 15 módulos. Añadidos en la fase UX: createSliderController() (fábrica compartida, UX-4.2) + ModuleHeroSlider (UX-4.2); ModuleLightbox extendido para video local/embebido (UX-7.8, D-077, Decisión 6); ModuleOfferPopup nuevo (UX-7.10, D-079, con rebote/nudge de D-081) + 1 línea aditiva en ModuleQuoteForm (evento ce:quoteFormSuccess)
    ├── js/admin-home-builder.js       ✅ UX-1.2 — control custom Home Builder (drag&drop)
    ├── js/admin-hero-slides.js        ✅ UX-4.2 — control custom ce_hero_slides
    ├── js/admin-stats-items.js        ✅ UX-7.6 — control custom del repeater de Estadísticas
    ├── js/admin-trust-badges.js       ✅ UX-7.7 — control custom del repeater de Insignias de Confianza
    └── img/                           ⬜ Vacía (assets/images/ contiene un archivo de prueba subido por el usuario, fuera del árbol versionado del tema)
```

---

### Verificación de cierre de etapa (Sprint UX-7 + Sprint UX-10 — sesión de cierre)

Con UX-7.10 aprobado explícitamente (D-079 base + D-080 refuerzo visual + D-081 efecto de movimiento JS, sin reversión), **el Sprint UX-7 queda completo (UX-7.1 a UX-7.10)**. El Sprint UX-10 (UX-10.1 a UX-10.3, UX-10.4 absorbido) ya estaba completo y aprobado de una sesión anterior. Este árbol se actualiza para reflejar todos los archivos tocados por ambos Sprints, diferido hasta este punto conforme al criterio D-034 ya vigente en el proyecto (actualización incremental en el cierre de Sprint más significativo, no por cada Entregable individual).

Ningún archivo del Sprint 8 (`inc/enqueue.php` en su lógica de QA-030, `inc/customizer.php` en sus 3 colores de QA-011, `inc/seo.php` en `ce_construction_output_json_ld()`, `header.php` en su `tabindex`, `style.css` en su comentario de QA-013) fue modificado por ningún Entregable de la fase UX — verificado Entregable por Entregable durante toda la fase, y reconfirmado en este cierre.
