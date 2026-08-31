# CE Construction — CONTEXT_MAP.md
### Índice de contexto por funcionalidad (NO es documentación funcional)

> Este documento es exclusivamente un **índice** para reducir tokens al iniciar una sesión o un Entregable nuevo: dice **qué archivos y qué decisiones leer** para tocar una funcionalidad concreta, sin repetir su contenido. No sustituye a `ARCHITECTURE.md`, `DECISIONS.md`, `CURRENT_SPRINT.md` ni `CURRENT_UX_SPRINT.md` — son la fuente de verdad; este archivo solo apunta a ellos.
>
> **Cómo usarlo:** antes de implementar un Entregable, localizar la sección de funcionalidad relevante aquí, leer únicamente los archivos de "Código" y las decisiones listadas en "Decisiones clave" (no todo `DECISIONS.md`), y solo entonces tocar código. Si una funcionalidad no aparece aquí, buscarla en `TREE.md`/`ARCHITECTURE.md` completos y considerar añadirla a este índice.
>
> **Mantenimiento:** al cerrar un Entregable que crea/modifica archivos de una sección ya listada aquí, actualizar esa sección (archivos + última decisión). Si el Entregable crea una funcionalidad nueva, añadir una sección nueva. Este archivo se actualiza en cada cierre de Entregable relevante — no se difiere como `CHANGELOG.md`/`TREE.md`.

---

## Formulario de Cotización (Quote Form)

### Código principal
- `inc/quote-form.php` — CPT `cotizacion` + handler AJAX + cron de retención
- `inc/form-guards.php` — rate-limiting atómico + idempotencia (tabla `{prefix}ce_form_guards`)
- `inc/quote-attachments.php` — carpeta protegida de adjuntos + endpoint de descarga autenticado
- `template-parts/quote-form.php` — markup, 3 contextos (normal / `modal` / `hero`)

### Dependencias
- `functions.php` — `CE_THEME_DB_VERSION`, orden de carga (`form-guards.php` antes que `quote-form.php`)
- `inc/enqueue.php` — `wp_localize_script( ceConstructionData )` (nonce, ajaxUrl)
- `inc/helpers.php` — `ce_get_quote_cta_url()`, bandera `ce_construction_quote_form_rendered_inline*()`
- `inc/customizer.php` — sección "CE: Formulario de Cotización" (`ce_quote_form_mode`)
- `assets/js/main.js` — `ModuleQuoteForm` (multi-instancia por clase `.ce-quote-form-instance`), `ModuleHeroFormProgressive`
- `footer.php` — imprime el modal (`#ce-quote-modal`)
- `template-parts/hero.php` — imprime la instancia `context='hero'`
- `single-servicio.php`, `single-proyecto.php` — invocan la instancia normal de forma incondicional

### Documentación
- `ARCHITECTURE.md` §8 (flujo completo con diagrama)
- `QA_REPORT.md` QA-001 a QA-004, QA-015, QA-031 a QA-034, QA-044

### Decisiones clave
D-049, D-050, D-051, D-052, D-053 (arquitectura de contexto/modos), D-056 (sin variante corta), D-064, D-065 (contexto `hero`), D-091, D-092, D-093 (expansión progresiva/opacidad/recompactado del panel del Hero), D-096, D-097 (QA-031, adjuntos protegidos), D-098, D-099 (QA-032/033/034, form-guards), D-100 (QA-044)

---

## Header

### Código
- `header.php`
- `inc/customizer.php` (contacto, redes, logo — sin sección propia de header más allá de lo nativo)
- `assets/css/main.css` sección 9 (Navbar/Header) y sección 24 (responsive `.ce-header__top`)
- `assets/js/main.js` — `ModuleMobileNav`, `ModuleStickyHeader`

### Dependencias
- `inc/helpers.php` — `ce_render_social_icons()`, `ce_get_phone_href()`, `ce_get_quote_cta_url()`

### Documentación
- `ARCHITECTURE.md` §3, §5
- `QA_REPORT.md` QA-017, QA-018, QA-037, QA-043

### Decisiones clave
D-039 (QA-018 responsive), D-087, D-090 (QA-043 iconos sociales, resuelto en Sprint UX-11)

---

## Footer

### Código
- `footer.php`
- `inc/setup.php` (sidebars `footer-1`/`footer-2`)
- `inc/widgets.php` (2 widgets custom)
- `assets/css/main.css` sección 12 (Footer)

### Dependencias
- `inc/helpers.php` — `ce_render_footer_logo()`, `ce_render_social_icons()`, `ce_get_whatsapp_number()`
- `inc/customizer.php` — sección "CE: Footer" (`ce_footer_logo`, `ce_footer_about`, `ce_footer_copyright`)
- `template-parts/offer-popup.php`, modal de cotización — impresos desde aquí

### Documentación
- `ARCHITECTURE.md` §3, §5
- `QA_REPORT.md` QA-006

### Decisiones clave
D-021 (QA-006 sidebar footer-1), D-036 (widgets), D-069 (logo independiente footer, UX-7.5)

---

## Home Builder (registro y orden de secciones)

### Código
- `inc/home-builder.php` — registro central (`ce_construction_home_sections()`, orden por defecto, `ce_construction_get_active_home_order()`, `ce_construction_get_home_section_args()`)
- `front-page.php` — loop data-driven
- `inc/customizer.php` — sección "CE: Home Builder" (`ce_home_sections_order`, `CE_Customize_Home_Sections_Control`)
- `assets/js/admin-home-builder.js` — drag&drop (jQuery UI Sortable)
- `inc/section-shortcode.php` — `[ce_section key="..."]` (reutilización fuera del Home)

### Dependencias
- `inc/enqueue.php` — encolado del script admin (`customize_controls_enqueue_scripts`)
- `page.php` — contexto típico de uso del shortcode

### Documentación
- `ARCHITECTURE.md` §6 (diagrama completo del flujo del Home)
- `UX_CONVERSION_ANALISIS_Y_PLAN.md` §3.1, §8.4 (Sprint UX-6)

### Decisiones clave
D-045 (UX-1.1, registro central), D-046 (UX-1.2, panel admin), D-059 (`page.php`, UX-6.1), D-060 (shortcode, UX-6.2), D-061 (`.ce-content-breakout`, UX-6.3), D-062 (cierre Sprint UX-6)

---

## Secciones del Home Builder — inventario de claves registradas

Cada clave abajo tiene su propia sección de este índice si tiene lógica propia relevante; esta tabla es solo el mapa clave→template.

| Clave | Template | Activa por defecto |
|---|---|---|
| `hero` | `template-parts/hero.php` | Sí |
| `about` | `template-parts/about.php` | Sí |
| `services` | `template-parts/services.php` | Sí |
| `projects` | `template-parts/projects.php` | Sí |
| `stats` | `template-parts/stats.php` | Sí |
| `why_us` | `template-parts/why-us.php` | Sí |
| `testimonials` | `template-parts/testimonials.php` | Sí |
| `gallery` | `template-parts/gallery.php` | Sí |
| `cta` | `template-parts/cta.php` | Sí |
| `quote_form` | `template-parts/quote-form.php` | Sí |
| `team` | `template-parts/team.php` | No |
| `clients` | `template-parts/clients.php` | No |
| `faq` | `template-parts/faq.php` | No |
| `cta_secondary` | `template-parts/cta.php` (variant) | No |
| `trust_badges` | `template-parts/trust-badges.php` | No |
| `testimonials_full` | `template-parts/testimonials-full.php` | No |
| `google_reviews` | `template-parts/google-reviews.php` | No |
| `financing` | `template-parts/financing.php` | No |

**Nota:** el Popup de Oferta (`template-parts/offer-popup.php`) **no** está en este registro — no es una sección posicionable, se imprime globalmente desde `footer.php`.

---

## Hero (Home + interior)

### Código
- `template-parts/hero.php` — Hero de Home (imagen/video/slider, layout de columnas, Quote Form embebido)
- `template-parts/page-hero.php` — Hero interno (solo imagen desde UX-11, D-084)
- `inc/hero-image-position.php` — posición de fondo configurable por imagen (UX-11)
- `inc/customizer.php` — sección "CE: Sección Hero" (todos los controles de Hero)
- `assets/js/admin-hero-slides.js` — control custom `ce_hero_slides`
- `assets/js/main.js` — `createSliderController()`, `ModuleHeroSlider`
- `assets/css/main.css` secciones 10, 19 (altura), 26 (video), 27 (slider), 28/28 bis (layout+Quote Form), 20 (page-hero)

### Dependencias
- `inc/helpers.php` — `ce_construction_get_hero_media_state()` (solo Home), `ce_construction_get_page_hero_image_url()`, `ce_construction_get_hero_overlay_gradient_css()`, `ce_construction_hex_to_rgb()`
- `template-parts/quote-form.php` — contexto `hero`
- `template-parts/trust-badges.php` — modo compacto dentro de `.ce-hero-quote-card`

### Documentación
- `ARCHITECTURE.md` §3 (tabla de template-parts)
- `UX_CONVERSION_ANALISIS_Y_PLAN.md` §3.4, §8.4 (UX-4, UX-7.1, UX-7.2)

### Decisiones clave
D-054 (UX-4.1, imagen/video), D-055 (UX-4.2, slider), D-063 (UX-7.1, unificación Home/interior), D-064, D-065 (UX-7.2, layout+Quote Form), D-083 a D-090 (Sprint UX-11 completo: panel sin recorte, altura, Hero interno separado, posición de imagen, overlay configurable, header)

---

## CTA (primario / secundario / financiamiento)

### Código
- `template-parts/cta.php` — variantes `primary`/`secondary`/`sidebar` (mismo archivo)
- `template-parts/financing.php` — sección independiente, mismo patrón de campos
- `inc/customizer.php` — secciones "CE: Sección CTA", "CE: CTA Secundario", "CE: Financiamiento"

### Dependencias
- `inc/helpers.php` — `ce_get_quote_cta_url()`, `ce_construction_hex_darken()`, `ce_get_whatsapp_number()`
- `inc/home-builder.php` — claves `cta`, `cta_secondary`, `financing`

### Documentación
- `UX_CONVERSION_ANALISIS_Y_PLAN.md` §8.4 (UX-7.4, UX-7.9)

### Decisiones clave
D-049 (centralización, UX-3.1), D-050 (fix theme_mod vacío), D-056 (CTA secundario, UX-5.1), D-068 (icono/color, UX-7.4), D-078 (Financiamiento, UX-7.9)

---

## Sidebars (Servicios / Proyectos)

### Código
- `template-parts/sidebar-servicios.php`
- `template-parts/sidebar-proyectos.php`
- `inc/customizer.php` — sección "CE: Sidebars (Servicios/Proyectos)"

### Dependencias
- `template-parts/cta.php` (variant `sidebar`)
- `template-parts/content-testimonio-card.php` (`$args['compact']`)
- `inc/helpers.php` — `ce_get_random_testimonio()`

### Documentación
- `UX_CONVERSION_ANALISIS_Y_PLAN.md` §8.4 (UX-7.3)

### Decisiones clave
D-067 (UX-7.3, slot de sidebar)

---

## Testimonios (teaser, página completa, video)

### Código
- `inc/cpt-testimonios.php` — CPT (sin cambios desde su registro original)
- `inc/meta-boxes.php` — campos del testimonio + metabox de video (`ce_testimonio_video`)
- `template-parts/testimonials.php` — teaser/slider del Home
- `template-parts/testimonials-full.php` — página completa (grid + paginación)
- `template-parts/content-testimonio-card.php` — card individual reutilizable (`$args['compact']`, `$args['video_enabled']`)
- `assets/css/main.css` secciones 11, 32 (video)
- `assets/js/main.js` — `ModuleTestimonialSlider`, `ModuleLightbox` (extendido para video)

### Dependencias
- `inc/helpers.php` — `ce_get_random_testimonio()`, `ce_get_testimonio_video()`
- `page.php` + `[ce_section key="testimonials_full"]` — página dedicada

### Documentación
- `UX_CONVERSION_ANALISIS_Y_PLAN.md` §8.4, §8.8 (benchmark, UX-7.8, UX-10)

### Decisiones clave
D-048 (extracción de card no aplica aquí — ver FAQ), D-067 (card extraída a partial, UX-7.3), D-072 a D-076 (Sprint UX-10 completo: página + CTA + Google Reviews, incluida la corrección de premisa de D-074/D-075), D-077 (UX-7.8, video)

---

## Trust Badges (Insignias de Confianza)

### Código
- `template-parts/trust-badges.php` — modo normal + modo compacto
- `inc/customizer.php` — sección "CE: Insignias de Confianza" (`CE_Customize_Trust_Badges_Control`)
- `assets/js/admin-trust-badges.js`
- `assets/css/main.css` sección 30

### Dependencias
- `inc/helpers.php` — `ce_construction_decode_trust_badges()`, `ce_construction_get_trust_badges()`, `ce_construction_trust_badge_title()`
- `template-parts/quote-form.php` — modo compacto dentro de `.ce-hero-quote-card`

### Documentación
- `UX_CONVERSION_ANALISIS_Y_PLAN.md` §8.4, §8.8 (UX-7.7)

### Decisiones clave
D-065 (nota de integración en Hero), D-071 (implementación completa, UX-7.7)

---

## Google Reviews (Trustindex)

### Código
- `template-parts/google-reviews.php` — embed tal cual, sin normalización
- `inc/customizer.php` — sección "CE: Google Reviews (Trustindex)" (`ce_google_reviews_embed`, `wp_kses()` dedicado)

### Dependencias
- `inc/home-builder.php` — clave `google_reviews`, independiente de `testimonials`/`testimonials_full`

### Documentación
- `UX_CONVERSION_ANALISIS_Y_PLAN.md` §8.4 (UX-10.3/UX-10.4)

### Decisiones clave
D-072 (propuesta original, Opción Híbrida C), D-074 (bloqueo: Trustindex sin API pública), D-075 (corrección de premisa: sección independiente, sin normalizar), D-076 (implementación)

---

## Popup de Oferta

### Código
- `template-parts/offer-popup.php`
- `inc/customizer.php` — sección "CE: Popup de Oferta" (8 controles)
- `assets/js/main.js` — `ModuleOfferPopup`, más 1 línea aditiva en `ModuleQuoteForm` (evento `ce:quoteFormSuccess`)
- `assets/css/main.css` sección 33

### Dependencias
- `inc/helpers.php` — `ce_get_offer_popup_data()`
- `footer.php` — punto único de impresión
- `assets/js/main.js` — `ModuleModals` (reutilizado, no reimplementado), `ModuleSmoothScroll`

### Documentación
- `UX_CONVERSION_ANALISIS_Y_PLAN.md` §8.4 (UX-7.10)

### Decisiones clave
D-065 (origen de la idea), D-079 (implementación base: temporizador, cookies, apertura/cierre), D-080 (refuerzo visual: icono, insignia, colores, responsive), D-081 (efecto de movimiento JS: rebote + nudge)

---

## Reutilización fuera del Home (Páginas / shortcode)

### Código
- `page.php`
- `single.php` (mismo mecanismo de breakout de ancho)
- `inc/section-shortcode.php`
- `assets/css/main.css` — `.ce-content-breakout` (sección 20, dentro del bloque Sprint 3/módulo Servicios)

### Dependencias
- `inc/home-builder.php` — `ce_construction_get_home_section_args()`
- `comments.php` (ya genérico post/page desde Sprint 6B)

### Documentación
- `UX_CONVERSION_ANALISIS_Y_PLAN.md` §8.3, §8.4 (UX-6)

### Decisiones clave
D-059 (UX-6.1), D-060 (UX-6.2), D-061 (UX-6.3, fix de ancho), D-062 (cierre Sprint UX-6)

---

## CPTs de contenido (Servicios, Proyectos, Equipo, Clientes, FAQ)

### Código
- `inc/cpt-servicios.php`, `inc/cpt-proyectos.php`, `inc/cpt-equipo.php`, `inc/cpt-clientes.php`, `inc/cpt-faq.php`
- `inc/meta-boxes.php` — campos de cada CPT + guardado centralizado
- `archive-*.php` / `single-*.php` por CPT
- `template-parts/content-*.php` — partial de card por CPT
- `template-parts/sidebar-servicios.php` / `sidebar-proyectos.php`
- `template-parts/page-hero.php` — hero interno compartido

### Dependencias
- `inc/helpers.php` — `ce_cpt_has_posts()`, `ce_get_related_services()`, `ce_get_related_projects()`, `ce_get_related_services_for_project()`, `ce_get_gallery_ids()`, `ce_get_short_excerpt()`, `ce_render_service_icon()`
- `inc/seo.php` — schema por CPT

### Documentación
- `ARCHITECTURE.md` §3, §7 (flujo de 3 capas: registro → metaboxes → consumo)

### Decisiones clave
D-009 a D-016 (Sprint 3/4, arquitectura de Servicios/Proyectos), D-025 a D-027 (Sprint 5 Fase 3, Equipo/Clientes)

---

## FAQ (accordion compartido)

### Código
- `inc/cpt-faq.php`
- `template-parts/faq.php` — sección de Home
- `template-parts/content-faq-accordion.php` — partial de ítem, compartido con `single-servicio.php`
- `assets/js/main.js` — `ModuleAccordion`

### Documentación
- `UX_CONVERSION_ANALISIS_Y_PLAN.md` §8.4 (UX-2.2)

### Decisiones clave
D-048 (extracción del partial compartido, UX-2.2)

---

## SEO

### Código
- `inc/seo.php` — meta tags, Open Graph, Schema.org (8 bloques JSON-LD), breadcrumbs

### Dependencias
- `header.php` — imprime breadcrumbs globalmente

### Documentación
- `ARCHITECTURE.md` §3
- `QA_REPORT.md` QA-014, QA-024, QA-025, QA-038, QA-039, QA-040

### Decisiones clave
D-014 (schema Project, tipo múltiple), D-042 (QA-014, endurecimiento contra `</script>`)

---

## Cache-busting y versionado de assets

### Código
- `functions.php` — `CE_THEME_VERSION` (derivada de `wp_get_theme()`)
- `inc/enqueue.php` — `ce_construction_asset_version()` (vía `filemtime()`), todos los `wp_enqueue_*`
- `style.css` — cabecera `Version:`

### Documentación
- `ARCHITECTURE.md` §9
- `QA_REPORT.md` QA-030

### Decisiones clave
D-044 (QA-030, Entregable 8.2)

---

## Sprint 8 — Robustez y seguridad del formulario (QA-030 a QA-044)

### Código
- `inc/quote-attachments.php` (QA-031)
- `inc/form-guards.php` (QA-032/QA-034)
- `inc/quote-form.php` (QA-032/033/034, secciones marcadas inline)
- `inc/enqueue.php` (QA-030, QA-010)
- `inc/customizer.php` (QA-011, colores sin `postMessage`)
- `inc/seo.php` (QA-014)
- `header.php` (QA-017)
- `assets/js/main.js` — `ModuleQuoteForm.updateFileLabel()` (QA-044)

### Documentación
- `CURRENT_SPRINT.md` — tracker oficial vigente del Sprint 8
- `QA_REPORT.md` — fuente única de hallazgos, secciones 4 y 5 (matriz de priorización)

### Decisiones clave
D-041 a D-044 (Sprint 8 8.1/8.2), D-095 (aprobación 8.1/8.2), D-096/D-097 (8.3, QA-031), D-098/D-099 (8.4, QA-032/033/034), D-100 (QA-044)

### Pendiente (no iniciado a la fecha de este índice)
- **8.5** — QA-012 (caché de "relacionados", `inc/helpers.php`), QA-016 (script inline de metabox, `inc/meta-boxes.php`), QA-035 (autoplay sin pausa accesible, `ModuleTestimonialSlider`), QA-038 (`<link rel="canonical">`, `inc/seo.php`)
- **8.6** — QA-036 (foco en overlays: `ModuleMobileNav`, `ModuleModals`, `header.php`, `footer.php`) — requiere decisión de diseño (R-4)
- **8.7** — Hallazgos Bajos: QA-019, QA-020, QA-021, QA-022, QA-037, QA-039, QA-040

---

## Documentación de control (meta — sobre el propio proyecto)

### Archivos
- `docs/PROJECT_STATUS.md` — estado oficial global
- `docs/CURRENT_SPRINT.md` — tracker del Sprint 8
- `docs/CURRENT_UX_SPRINT.md` — tracker de la fase UX (Sprints UX-1 a UX-11)
- `docs/TODO.md` — checklist maestro
- `docs/TREE.md` — árbol de archivos con estado
- `docs/DECISIONS.md` — registro append-only de decisiones (D-001 en adelante)
- `docs/CHANGELOG.md` — historial de versiones
- `docs/QA_REPORT.md` — hallazgos QA consolidados
- `docs/ARCHITECTURE.md` — arquitectura real verificada
- `docs/HANDOFF.md` — transferencia entre sesiones
- `docs/UX_CONVERSION_ANALISIS_Y_PLAN.md` — plan completo de la fase UX
- `docs/CONTEXT_MAP.md` — este documento

### Regla de actualización
Ver `ARCHITECTURE.md` §10 ("Actualización incremental de documentación", D-034): cada documento se actualiza solo cuando cambia lo que ese documento existe para registrar. `CONTEXT_MAP.md` es la excepción explícita — se actualiza en cada Entregable que toque una sección ya indexada, no se difiere.

---

## Estado de fases (resumen para no tener que abrir `PROJECT_STATUS.md` por esto)

- **Fase "Optimización UX / Conversión" (Sprints UX-1 a UX-11):** ✅ cerrada por completo. Backlog no bloqueante sin iniciar: UX-5.2 (doc. "objetivo de plantilla"), Sprint UX-8 ("Video en Proyectos"), Sprint UX-9 (registro documental Responsive).
- **Sprint 8 ("Cierre de Hallazgos QA"):** en curso. 8.1, 8.2, 8.3, 8.4 aprobados. 8.5, 8.6, 8.7 sin iniciar (ver arriba).
