# CE Construction — CHANGELOG.md

> Historial de versiones del proyecto. Este archivo es acumulativo: cada nueva versión se agrega al final, nunca se reescribe una versión anterior.

---

## v0.1.0 — Arquitectura y Backend

**Módulo:** Bootstrap, CPT, Customizer, SEO, Formulario (backend)

### Añadido
- Arquitectura modular del tema: `functions.php` como bootstrap que carga módulos independientes desde `inc/`.
- `style.css` con la cabecera oficial del tema y design tokens base (colores, tipografía, espaciados, radios, sombras, transiciones).
- `inc/setup.php`: soporte de `title-tag`, `post-thumbnails`, `custom-logo`, `html5`, menús de navegación (`primary`, `footer`), sidebars de footer, tamaños de imagen custom (`ce-hero`, `ce-card`, `ce-thumb`).
- `inc/enqueue.php`: carga de Google Fonts (Poppins + Inter), Font Awesome 6.5.1, `style.css` + `assets/css/main.css`, `assets/js/main.js` con `defer`, `wp_localize_script` (`ceConstructionData`).
- `inc/customizer.php`: 7 secciones — Identidad/Colores, Tipografía, Contacto/WhatsApp/Horario, Redes Sociales, Hero, CTA, Footer.
- 6 Custom Post Types: `servicio`, `proyecto`, `testimonio`, `miembro_equipo`, `cliente`, `ce_faq`, con taxonomías `categoria_servicio`, `categoria_proyecto`, `estado_proyecto`.
- `inc/meta-boxes.php`: metaboxes con nonce + sanitización + permisos para los 5 CPTs de contenido; selector de galería vía `wp.media` para Proyectos.
- `inc/quote-form.php`: CPT interno `cotizacion` (administrable, no público), handler AJAX `ce_submit_quote` con validación server-side, honeypot anti-spam, manejo seguro de adjuntos (tipo MIME + tamaño máx. 5MB), envío de correo vía `wp_mail` con adjunto y `Reply-To`.
- `inc/seo.php`: meta description, Open Graph, Schema.org JSON-LD (`GeneralContractor`), función de breadcrumbs (aún no enganchada en esta versión).

### Decisiones clave
- Ver `DECISIONS.md`: D-001, D-002, D-004.

---

## v0.2.0 — Sistema de Diseño y Plantillas del Home

**Módulo:** Sistema CSS, Sistema JS, Header, Footer, Front Page

### Añadido
- `assets/css/main.css` (862 líneas): reset moderno, variables, tipografía fluida, contenedor + grid mobile-first, sistema de botones, cards, formularios con estados de validación, navbar (sticky + menú móvil off-canvas), hero, secciones genéricas (stats, why-us, testimonials, gallery, cta), footer, componentes flotantes (WhatsApp, back-to-top), modales, lightbox, breadcrumbs, utilidades, animaciones, responsive (breakpoints 576/768/992/1200px).
- `assets/js/main.js` (611 líneas): 12 módulos ES6 auto-inicializables — scroll suave, menú responsive, sticky header, back-to-top, WhatsApp flotante, contadores animados (`IntersectionObserver`), slider de testimonios (autoplay + swipe + dots + flechas), lightbox con navegación por teclado, modales, formulario de cotización (validación en vivo + envío `fetch` AJAX + drag&drop de adjunto), lazy loading con fallback, scroll-reveal.
- `inc/helpers.php`: funciones reutilizables — `ce_get_social_links()`, `ce_render_social_icons()`, `ce_get_whatsapp_number()`, `ce_get_gallery_ids()`, `ce_render_service_icon()`, `ce_get_phone_href()`, `ce_get_short_excerpt()`, `ce_cpt_has_posts()`.
- `header.php`: barra superior de contacto rápido, header principal (logo, menú, teléfono, botón cotizar, toggle móvil), menú móvil off-canvas, breadcrumbs enganchados vía `ce_construction_breadcrumbs()`.
- `footer.php`: información de contacto, redes sociales, mapa embebido, horario, menú de footer, copyright, botones flotantes (WhatsApp + volver arriba), modales de éxito/error.
- `front-page.php`: ensamblado de 10 secciones vía `get_template_part()`.
- 10 template-parts: `hero.php`, `about.php`, `services.php`, `projects.php`, `stats.php`, `why-us.php`, `testimonials.php`, `gallery.php`, `cta.php`, `quote-form.php` (markup).

### Corregido
- `assets/js/main.js` → `ModuleModals`: cambio de `querySelector` a `querySelectorAll` para enlazar correctamente los dos botones de cierre presentes en cada modal (la X y el botón de acción). Ver `DECISIONS.md` D-008.

### Decisiones clave
- Ver `DECISIONS.md`: D-003, D-005, D-006, D-007, D-008.

### Deuda técnica conocida al cierre de esta versión
- Falta `index.php` (requerido por WordPress para un tema válido).
- Faltan plantillas de contenido: `archive-servicio.php`, `single-servicio.php`, `archive-proyecto.php`, `single-proyecto.php`, `single.php`, `page.php`, `comments.php`, `404.php`.
- Falta `inc/widgets.php`.
- Falta `screenshot.png`.
- Ver `TODO.md` para el detalle completo.

---

## v0.2.1 — Cierre de etapa y documentación de transferencia

**Módulo:** Documentación (sin cambios de código del tema)

### Añadido
- `HANDOFF.md`: documento maestro de transferencia entre sesiones, con prompt de continuación incluido.
- `CHANGELOG.md`: este archivo.
- `DECISIONS.md`: registro formal de 8 decisiones arquitectónicas (D-001 a D-008) con ID, fecha, alternativas descartadas e impacto.

### Cambiado
- `PROJECT_STATUS.md`: se agregó la sección 8 (Documentos de transferencia) y se actualizó la cabecera con la versión de referencia.
- `TODO.md`: se agregó la sección 12 (Documentación de transferencia entre sesiones).
- `TREE.md`: se agregaron los 3 nuevos archivos de documentación al árbol y una nota de verificación de cierre.

### Notas
- Ningún archivo de código del tema (`*.php`, `*.css`, `*.js`) fue modificado en esta versión — es una entrega exclusivamente de documentación y cierre de etapa, tal como fue solicitado explícitamente.

---

## v0.3.0 — Sprint 3: Módulo de Servicios (frontend completo)

**Módulo:** Servicios — archive, single, relacionados, schema

### Añadido
- `archive-servicio.php`, `single-servicio.php`, `template-parts/page-hero.php`, `template-parts/content-servicio.php`, `template-parts/sidebar-servicios.php`.
- `inc/helpers.php` (extensión aditiva): `ce_get_related_services()`, `ce_get_related_projects()`.
- `inc/seo.php` (extensión aditiva): `ce_construction_schema_service()`.
- `assets/css/main.css` (sección 20): page-hero, sidebar, accordion, navegación entre servicios.
- `assets/js/main.js` (extensión aditiva): `ModuleAccordion`.

### Decisiones clave
- Ver `DECISIONS.md`: D-009 a D-013.

### Deuda técnica conocida al cierre de esta versión
- Relación Servicio↔Proyecto heurística, FAQ sin relación real, `index.php` pendiente, plantillas de Proyectos/Blog pendientes, `inc/widgets.php`, `screenshot.png`.

---

## v0.3.1 — Sprint de QA e Integración

**Módulo:** Auditoría completa (sin desarrollo de código nuevo, sin correcciones aplicadas)

### Añadido
- `QA_REPORT.md`: 29 hallazgos (1 Crítico, 8 Altos, 9 Medios, 5 Bajos, 6 Mejoras futuras).

### Notas
- Ningún archivo de código del tema fue modificado en esta versión.

---

## v0.4.0 — Sprint 4: Módulo de Proyectos (frontend completo)

**Módulo:** Proyectos — archive, single, galería/lightbox, relacionados, schema

### Añadido
- `archive-proyecto.php`, `single-proyecto.php`, `template-parts/content-proyecto.php`, `template-parts/sidebar-proyectos.php`.
- `inc/helpers.php` (extensión aditiva): `ce_get_related_services_for_project()`.
- `inc/seo.php` (extensión aditiva): `ce_construction_schema_project()`.
- `assets/css/main.css` (sección 21): ficha de metadatos de proyecto.

### Sin cambios
- `assets/js/main.js`.

### Decisiones clave
- Ver `DECISIONS.md`: D-014 a D-016.

---

## v0.4.1 — Sprint 5, Fase 1: Correcciones de QA (Críticos y Altos)

**Módulo:** Corrección de los 9 hallazgos Críticos/Altos de `QA_REPORT.md` (v0.3.1)

### Corregido
- QA-001 a QA-009 en `inc/quote-form.php`, `inc/meta-boxes.php`, `inc/cpt-servicios.php`, `footer.php`, `functions.php`, `style.css`, `assets/css/main.css`.

### Decisiones clave
- Ver `DECISIONS.md`: D-017 a D-024.

---

## v0.5.0 — Sprint 5, Fase 3: Módulo de Equipo y Clientes

**Módulo:** Equipo y Clientes — archive, single, schema.org

### Añadido
- `archive-equipo.php`, `single-equipo.php`, `archive-clientes.php`, `single-clientes.php`, `template-parts/content-equipo.php`, `template-parts/content-cliente.php`.
- `inc/seo.php` (extensión aditiva): `ce_construction_schema_person()`, `ce_construction_schema_client_organization()`.
- `assets/css/main.css` (sección 22): tarjetas de equipo, grid de logos de clientes.

### Cambiado (necesario, documentado)
- `inc/cpt-clientes.php`: `has_archive` false→true (ver `DECISIONS.md` D-025).

### Decisiones clave
- Ver `DECISIONS.md`: D-025 a D-027.

---

## v0.6.0 — Entregable 6A: index.php (bloqueador crítico resuelto)

**Módulo:** Plantilla de respaldo obligatoria de WordPress

### Añadido
- `index.php`, `template-parts/content-fallback.php`, `template-parts/no-results.php`.

### Corregido
- Bug preexistente: `.ce-mt-6`/`.ce-mb-6` faltaban en `assets/css/main.css`.

### Decisiones clave
- Ver `DECISIONS.md`: D-028, D-029.

---

## v0.6.1 — Sprint 6B, Entregable 6B.1: page.php

### Añadido
- `page.php`.

---

## v0.6.2 — Sprint 6B, Entregable 6B.2: single.php + comments.php

### Añadido
- `single.php`, `comments.php`.
- `inc/seo.php` (extensión aditiva): `ce_construction_schema_blog_post()`.
- `assets/css/main.css` (sección 23): árbol de comentarios.

### Decisiones clave
- Ver `DECISIONS.md`: D-032, D-033.

---

## Nota de proceso (sin cambio de código, tras el Entregable 6B.2)

Se refinó la metodología permanente de Gestión de Sprints y Entregables (D-030) con una política de actualización incremental de documentación. Ver `DECISIONS.md` D-034 y `ARCHITECTURE.md` sección 10.

---

## v0.6.3 — Sprint 6B, Entregable 6B.3: 404.php (Sprint 6B COMPLETADO)

**Módulo:** Página de error 404 dedicada — cierre del Sprint 6B ("Blog y páginas genéricas")

### Añadido
- `404.php`: `status_header(404)` + `nocache_headers()`, numeral 404 estilizado, formulario de búsqueda reutilizado, sección "Quizás te interese".

### Decisiones clave
- Ver `DECISIONS.md`: D-035.

## 🎉 SPRINT 6B COMPLETADO

Los 3 Entregables del Sprint 6B ("Blog y páginas genéricas") están terminados:
- 6B.1 — `page.php` (v0.6.1)
- 6B.2 — `single.php` + `comments.php` (v0.6.2)
- 6B.3 — `404.php` (v0.6.3)

---

## v0.7.0 — Sprint 7, Entregable 7.1: inc/widgets.php

**Módulo:** Widgets personalizados (primer Entregable del Sprint 7)
**Estado:** Entregado — pendiente de aprobación explícita del usuario (ver `DECISIONS.md` D-038)

### Añadido
- `inc/widgets.php`: `CE_Construction_Widget_Contact` (teléfono/correo/dirección/horario, con fallback automático a los theme mods del Customizer si el campo del widget se deja vacío) y `CE_Construction_Widget_Social` (iconos de redes sociales vía `ce_get_social_links()`, ya existente en `inc/helpers.php`), ambos registrados en `widgets_init`. Diseñados para dar uso real al sidebar "Footer - Columna 1" (`footer-1`), renderizable desde la corrección QA-006 (v0.4.1) pero vacío hasta ahora.

### Sin cambios
- `functions.php` (el módulo ya estaba referenciado condicionalmente vía `file_exists()` desde el bootstrap original), `assets/css/main.css`, `assets/js/main.js` — ambos widgets reutilizan exclusivamente clases y funciones ya existentes (`.ce-footer__social`, `.ce-footer__contact-item`, `ce_get_social_links()`).

### Decisiones clave
- Ver `DECISIONS.md`: D-036.

### Nota de mantenimiento
- Se confirma la eliminación de `inc/enqueue_1.php` del repositorio oficial (duplicado accidental de `inc/enqueue.php`, detectado durante este Entregable). `inc/enqueue.php` es y ha sido siempre el único archivo de encolado de assets cargado por `functions.php`; no se requirió ningún cambio de código por este evento.

---

## v0.7.1 — Sprint 7, Entregable 7.2: archive.php genérico

**Módulo:** Fallback de archivo genérico (segundo Entregable del Sprint 7)
**Estado:** Entregado — pendiente de aprobación explícita del usuario (ver `DECISIONS.md` D-038)

### Añadido
- `archive.php`: cubre categoría/etiqueta/autor/fecha (blog) y los CPTs `testimonio`/`ce_faq`, los únicos 2 de los 6 CPTs de contenido sin `archive-{cpt}.php` propio. Reutiliza `template-parts/page-hero.php` y `template-parts/content-fallback.php` (ya usados por `index.php`), sin duplicar markup.

### Sin cambios
- `inc/helpers.php`, `inc/seo.php`, `assets/css/main.css`, `assets/js/main.js`.

### Decisiones clave
- Ver `DECISIONS.md`: D-037.

### Observación (sin corregir en este Entregable)
- `ce_construction_breadcrumbs()` no tiene rama dedicada para los contextos que ahora cubre `archive.php` (categoría/etiqueta/autor/fecha, `is_post_type_archive('testimonio')`/`('ce_faq')`) — limitación preexistente, no introducida aquí. Candidata para un futuro Entregable de refinamiento SEO si se desea.

---

## Nota de proceso (nueva regla permanente, tras el cierre de los Entregables 7.1 y 7.2)

Se incorpora una nueva regla permanente a la metodología de Gestión de Sprints y Entregables: **ningún Entregable se considera finalizado hasta que todos sus archivos creados o modificados hayan sido entregados y aprobados explícitamente por el usuario**; el siguiente Entregable no inicia sin esa aprobación previa. Ver `DECISIONS.md` D-038 y `HANDOFF.md` sección 16 para el registro formal completo. No se modificó ningún archivo de código en esta nota.

---

## Próximas versiones (planeadas, no confirmadas)

- **v0.7.2 (propuesta):** Entregable 7.3 — Hallazgos QA Medios (con aprobación explícita de cuáles corregir). No inicia hasta que 7.1 y 7.2 sean aprobados.
- **v0.7.3 (propuesta):** Entregable 7.4 — `screenshot.png`.
- **v0.8.0 (propuesta):** Auditoría de accesibilidad y performance (incluye QA-026/QA-027: auto-hospedar fuentes/Font Awesome).
