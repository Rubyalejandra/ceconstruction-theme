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
- `archive-servicio.php`: listado de servicios con hero interno (`template-parts/page-hero.php`), layout de 2 columnas + sidebar opcional, paginación estilizada, sección CTA final.
- `single-servicio.php`: hero interno, contenido completo del servicio, navegación entre servicios (anterior/siguiente), servicios relacionados, proyectos relacionados, FAQ relacionadas (accordion), sidebar opcional, CTA y formulario de cotización integrado.
- `template-parts/page-hero.php`: hero interno reutilizable, parametrizable vía `$args` de `get_template_part()` (pensado para reutilizarse también en el próximo módulo de Proyectos).
- `template-parts/content-servicio.php`: partial de tarjeta de servicio, reutilizado en el archive y en la sección de servicios relacionados.
- `template-parts/sidebar-servicios.php`: sidebar opcional con listado de otros servicios + tarjeta de contacto rápido.
- `inc/helpers.php` (extensión aditiva): `ce_get_related_services()`, `ce_get_related_projects()`.
- `inc/seo.php` (extensión aditiva): `ce_construction_schema_service()` — Schema.org `Service` + `BreadcrumbList` en JSON-LD para el single de servicio.
- `assets/css/main.css` (extensión aditiva, sección 20): `.ce-page-hero`, `.ce-layout-with-sidebar`, `.ce-sidebar*`, `.ce-service-nav*`, `.ce-accordion*`, `.ce-service-content`, `.ce-service-icon-badge`, estilos de `.page-numbers` (paginación nativa de WordPress).
- `assets/js/main.js` (extensión aditiva): `ModuleAccordion` (toggle de FAQ con comportamiento "single open", ARIA `aria-expanded` sincronizado), enganchado al bootstrap ya existente.

### Decisiones clave
- Ver `DECISIONS.md`: D-009, D-010, D-011, D-012, D-013.

### Alcance explícitamente excluido de este sprint (por indicación del cliente)
- `index.php` sigue pendiente (crítico) — el cliente definió el alcance del Sprint 3 únicamente como el módulo de Servicios, sin incluirlo.

### Deuda técnica conocida al cierre de esta versión
- Relación Servicio↔Proyecto es heurística (por coincidencia de nombre de taxonomía), no un campo relacional explícito.
- FAQ relacionadas muestran las más recientes del sitio, no filtradas por servicio (no existe relación de datos entre `ce_faq` y `servicio`).
- Sigue pendiente `index.php`, plantillas de Proyectos, Blog/páginas genéricas, `inc/widgets.php`, `screenshot.png`.
- Ver `TODO.md` para el detalle completo.

---

## v0.3.1 — Sprint de QA e Integración

**Módulo:** Auditoría completa (sin desarrollo de código nuevo, sin correcciones aplicadas)

### Añadido
- `QA_REPORT.md`: auditoría exhaustiva y verificada línea por línea de los 31 archivos del tema (Sprints 1-3), con 29 hallazgos clasificados (1 Crítico, 8 Altos, 9 Medios, 5 Bajos, 6 Mejoras futuras). Cada hallazgo incluye archivo afectado, descripción, riesgo, recomendación e impacto de corregirlo.

### Metodología aplicada
- Lectura completa de arquitectura general, CPTs, Customizer, metaboxes, formulario de cotización, SEO, helpers, header/footer, template-parts, CSS y JS.
- Verificación de balance de sintaxis (llaves/paréntesis) en todo el PHP tocado.
- `node --check` sobre `assets/js/main.js`.
- Cálculo numérico real (fórmula de contraste WCAG 2.x) para los pares de color institucionales usados en texto, confirmando un fallo de contraste AA en `--ce-color-secondary` sobre fondo blanco (2.67:1 medido, mínimo requerido 4.5:1).
- Búsqueda dirigida de patrones de incompatibilidad con PHP 8.x (`create_function()`, `each()`, `extract()`) — sin hallazgos.
- Trazado manual del flujo de datos del archivo adjunto del formulario de cotización, de principio a fin, detectando el hallazgo crítico QA-001.

### Hallazgo crítico (sin corregir, pendiente de aprobación)
- **QA-001:** la validación de tipo de archivo adjunto en `inc/quote-form.php` puede evadirse falsificando el `Content-Type` del navegador combinado con una extensión permitida globalmente por WordPress (no restringida al whitelist real PDF/JPG/PNG/WEBP que el propio código pretende implementar).

### Hallazgos altos (sin corregir, pendientes de aprobación)
- QA-002: archivos adjuntos huérfanos, nunca registrados como `attachment`, sin límite de disco.
- QA-003: retención de datos personales sin política definida (riesgo de cumplimiento).
- QA-004: sin rate-limiting en el endpoint AJAX público del formulario.
- QA-005: fallo de contraste WCAG AA verificado numéricamente en `--ce-color-secondary` sobre blanco.
- QA-006: sidebar "Footer - Columna 1" registrado en `inc/setup.php` pero nunca renderizado en `footer.php`.
- QA-007: falta guardia `wp_is_post_revision()` en `ce_construction_save_meta_boxes()`.
- QA-008: `CE_THEME_VERSION` hardcodeada, nunca sincronizada — cache-busting de `main.css`/`main.js` roto entre releases.
- QA-009: CPT Servicio sin soporte `page-attributes` — el `orderby=menu_order` de `template-parts/services.php` no es funcional para el administrador.

### Notas
- **Ningún archivo de código del tema fue modificado en esta versión** — es una entrega exclusivamente de auditoría, conforme a instrucción explícita del cliente. Todas las correcciones (críticas, altas, medias, bajas y mejoras futuras) quedan documentadas en `QA_REPORT.md`, a la espera de aprobación explícita antes de aplicarse.

---

## v0.4.0 — Sprint 4: Módulo de Proyectos (frontend completo)

**Módulo:** Proyectos — archive, single, galería/lightbox, relacionados, schema

### Añadido
- `archive-proyecto.php`: listado de proyectos con hero interno (reutilizando `template-parts/page-hero.php` por segunda vez), layout de 2 columnas + sidebar opcional, paginación estilizada, sección CTA final.
- `single-proyecto.php`: hero interno, ficha de metadatos (cliente, ubicación, fecha de entrega, estado), contenido completo, galería con lightbox, navegación entre proyectos (anterior/siguiente), servicios relacionados, sidebar opcional, CTA y formulario de cotización integrado.
- `template-parts/content-proyecto.php`: partial de tarjeta de proyecto, reutilizado en el archive.
- `template-parts/sidebar-proyectos.php`: sidebar opcional con listado de otros proyectos + tarjeta de contacto rápido.
- `inc/helpers.php` (extensión aditiva): `ce_get_related_services_for_project()` — relación inversa a `ce_get_related_projects()` del Sprint 3.
- `inc/seo.php` (extensión aditiva): `ce_construction_schema_project()` — Schema.org `["CreativeWork", "Project"]` + `BreadcrumbList` en JSON-LD para el single de proyecto, incluyendo cliente (`sourceOrganization`), ubicación (`contentLocation`), fecha (`dateCreated`) y galería de imágenes.
- `assets/css/main.css` (extensión aditiva, sección 21): `.ce-project-meta-grid` y sus elementos, para la ficha visual de cliente/ubicación/fecha/estado.

### Sin cambios
- `assets/js/main.js`: **no requirió ninguna modificación**. La galería de `single-proyecto.php` reutiliza el mismo patrón de marcado (`.ce-gallery-item[data-full]`) que `template-parts/gallery.php` (home) ya usaba desde el Sprint 2; `ModuleLightbox` escanea ese selector globalmente y detecta el nuevo contenido sin cambios de código.

### Decisiones clave
- Ver `DECISIONS.md`: D-014, D-015, D-016.

### Corrección de una omisión del Sprint anterior
- `TREE.md` no incluía `QA_REPORT.md` en el árbol tras el Sprint de QA (v0.3.1). Se corrigió en este sprint al actualizar la documentación.

### Deuda técnica conocida al cierre de esta versión
- Los 29 hallazgos de `QA_REPORT.md` (v0.3.1) siguen sin corregir, incluido el crítico QA-001. El cliente optó explícitamente por continuar con Sprint 4 antes de abordarlos.
- `index.php` sigue pendiente (crítico de arquitectura).
- Faltan plantillas de Blog/páginas genéricas (`single.php`, `page.php`, `comments.php`, `404.php`), `inc/widgets.php`, `screenshot.png`.
- La relación Servicio↔Proyecto (en ambas direcciones) sigue siendo heurística por coincidencia de nombre de taxonomía, no un campo relacional explícito (ver D-010 y D-015).
- Ver `TODO.md` para el detalle completo.

---

## v0.4.1 — Sprint 5, Fase 1: Correcciones de QA (Críticos y Altos)

**Módulo:** Corrección de los 9 hallazgos Críticos/Altos de `QA_REPORT.md` (v0.3.1)

### Corregido
- **QA-001 (Crítico):** `inc/quote-form.php` — la validación de tipo de archivo adjunto usaba el MIME falsificable del cliente y una lista de extensiones demasiado amplia. Ahora usa `wp_check_filetype_and_ext()` + whitelist explícito (`pdf, jpg, jpeg, png, webp`).
- **QA-002 (Alto):** `inc/quote-form.php` — los archivos adjuntos ahora se registran como attachment real de WordPress (`wp_insert_attachment()` + `wp_generate_attachment_metadata()`), vinculados a la cotización vía `post_parent`, en vez de quedar huérfanos en disco.
- **QA-003 (Alto):** `inc/quote-form.php` — nuevo cron diario de retención (`ce_construction_quote_cleanup_event`) que purga cotizaciones más antiguas que `ce_construction_quote_retention_days()` (365 días por defecto, filtrable).
- **QA-004 (Alto):** `inc/quote-form.php` — rate-limiting por IP (máx. 3 envíos/10 min) vía transient, entre el honeypot y la sanitización de campos.
- **QA-005 (Alto):** `assets/css/main.css` — nueva variable `--ce-color-secondary-text` (5.17:1 sobre blanco, verificado) aplicada a `.ce-eyebrow` y estados hover/current de enlaces sobre fondo claro que antes medían 2.67:1.
- **QA-006 (Alto):** `footer.php` — el sidebar "Footer - Columna 1" (`footer-1`), registrado desde el Sprint 2 pero nunca renderizado, ahora se muestra condicionalmente (`is_active_sidebar`) dentro de la columna "Enlaces".
- **QA-007 (Alto):** `inc/meta-boxes.php` — añadida guardia `wp_is_post_revision()` faltante en `ce_construction_save_meta_boxes()`.
- **QA-008 (Alto):** `functions.php` / `style.css` — `CE_THEME_VERSION` sincronizada a `0.4.1` (restablece el cache-busting de `main.css`/`main.js`). Resuelve también QA-022 (Bajo) como efecto colateral.
- **QA-009 (Alto):** `inc/cpt-servicios.php` — añadido soporte `page-attributes`, habilitando el campo "Orden" que `template-parts/services.php` ya necesitaba para su `orderby=menu_order`.

### Decisiones clave
- Ver `DECISIONS.md`: D-017 a D-024 (una decisión por hallazgo corregido, D-017 cubre QA-001+QA-002 conjuntamente por estar en el mismo bloque de código).

### Notas
- Los hallazgos Medios, Bajos y Mejoras futuras de `QA_REPORT.md` **no se tocaron**, conforme a instrucción explícita ("corrige únicamente los Críticos y Altos").
- Esta versión fue desarrollada en dos sesiones de conversación (la primera se interrumpió por límite de mensajes); se verificó al reanudar que las 9 correcciones ya estaban aplicadas correctamente en el código antes de continuar, evitando duplicar trabajo.

---

## v0.5.0 — Sprint 5, Fase 3: Módulo de Equipo y Clientes

**Módulo:** Equipo y Clientes — archive, single, schema.org

### Añadido
- `archive-equipo.php`: listado de miembros del equipo con hero interno, grid de 4 columnas, paginación.
- `single-equipo.php`: ficha individual con foto circular, cargo, enlace a LinkedIn y biografía (`the_content()`).
- `archive-clientes.php`: grid de logos de clientes (2-5 columnas según viewport), paginación.
- `single-clientes.php`: logo grande y enlace al sitio web del cliente (sin depender de `the_content()`, ya que el CPT `cliente` no soporta editor).
- `template-parts/content-equipo.php`: partial de tarjeta de miembro del equipo (foto circular, cargo, LinkedIn).
- `template-parts/content-cliente.php`: partial de logo de cliente, alineado con las clases `.ce-clients-grid__item` ya existentes en `main.css`.
- `inc/seo.php` (extensión aditiva): `ce_construction_schema_person()` (Schema.org `Person` con `worksFor`, `jobTitle`, `sameAs`) y `ce_construction_schema_client_organization()` (Schema.org `Organization` con `logo`/`url`), más las ramas de breadcrumbs para `miembro_equipo`/`cliente` (estas últimas ya estaban parcialmente implementadas desde el Sprint 1; se completó la rama de archivo de `cliente`, antes inalcanzable).
- `assets/css/main.css` (extensión aditiva, sección 22 "Sprint 5"): `.ce-team-card*`, `.ce-team-bio*`, `.ce-clients-grid*`, `.ce-client-single*`, y las clases puntuales que faltaban (`.ce-team-card__placeholder`, estilo de fallback de texto en `.ce-clients-grid__item span`).

### Cambiado (necesario, documentado)
- `inc/cpt-clientes.php`: `'has_archive'` cambiado de `false` a `true` — sin este cambio, `archive-clientes.php` (entregable explícito de este sprint) habría sido inalcanzable vía URL amigable. Ver `DECISIONS.md` D-025.
- `inc/seo.php`: rama de breadcrumbs de `is_singular('cliente')` actualizada para enlazar al archivo (antes asumía que no existía) + nueva rama `is_post_type_archive('cliente')`. Cambio acoplado y consecuencia directa del punto anterior.

### Sin cambios
- `assets/js/main.js`: no requirió ninguna modificación — Equipo y Clientes no usan ningún componente interactivo nuevo (sin slider, sin lightbox, sin accordion).

### Decisiones clave
- Ver `DECISIONS.md`: D-025, D-026 (alcance deliberadamente más ligero, sin CTA/sidebar/formulario), D-027 (reconciliación de CSS de `content-cliente.php`).

### Deuda técnica conocida al cierre de esta versión
- Los hallazgos Medios/Bajos/Mejoras futuras de `QA_REPORT.md` siguen sin corregir (fuera de alcance de este sprint).
- `index.php` sigue pendiente (crítico de arquitectura).
- Faltan plantillas de Blog/páginas genéricas (`single.php`, `page.php`, `comments.php`, `404.php`), `inc/widgets.php`, `screenshot.png`.
- Ver `TODO.md` para el detalle completo.

---

## v0.6.0 — Entregable 6A: index.php (bloqueador crítico resuelto)

**Módulo:** Plantilla de respaldo obligatoria de WordPress

### Añadido
- `index.php`: plantilla de respaldo genérica y completamente funcional (no un placeholder). Cubre los 4 contextos que hoy recaen sobre ella (aún sin `single.php`/`page.php`/`archive.php`/`search.php`/`404.php` propios): vista single de blog/página, archivos genéricos (categoría/autor/fecha/CPTs sin archive propio como Testimonios/FAQ), resultados de búsqueda, y 404.
- `template-parts/content-fallback.php`: tarjeta genérica reutilizada en los loops de búsqueda y archivo genérico.
- `template-parts/no-results.php`: estado vacío reutilizable (búsqueda sin resultados, archivo vacío, 404) con formulario de búsqueda propio del sistema de diseño.

### Corregido
- **Bug preexistente (detectado al construir `index.php`):** `assets/css/main.css` — las utilidades `.ce-mt-6`/`.ce-mb-6` se usaban desde el Sprint 3 en 10 archivos ya aprobados (`single-servicio.php`, `single-proyecto.php`, `archive-servicio.php`, `archive-proyecto.php`, `archive-equipo.php`, `archive-clientes.php`, `single-clientes.php`, `single-equipo.php`) pero nunca se habían definido — el margen simplemente no se aplicaba. Corregidas de forma aditiva, junto a la familia `.ce-mt-*`/`.ce-mb-*` ya existente, sin tocar ninguna regla previa.

### Sin cambios
- `inc/helpers.php`, `inc/seo.php`, `assets/js/main.js`: `index.php` reutilizó exclusivamente funciones/componentes ya existentes, sin requerir ninguna extensión.

### Decisiones clave
- Ver `DECISIONS.md`: D-028 (diseño de `index.php` como fallback genérico completo, no placeholder), D-029 (corrección del bug de utilidades `.ce-mt-6`/`.ce-mb-6`).

### Notas
- El tema ya cumple el mínimo que WordPress exige (`style.css` + `index.php`) para ser reconocido y activado con seguridad.
- Limitación conocida y aceptada: los comentarios de blog usan la plantilla de compatibilidad nativa de WordPress hasta que se cree `comments.php` (sprint de Blog futuro).
- Ver `TODO.md` para el detalle completo.

---

## v0.6.1 — Sprint 6B, Entregable 6B.1: page.php

**Módulo:** Páginas genéricas de WordPress (primer Entregable del Sprint 6B, dividido conforme a la nueva metodología permanente de Gestión de Sprints y Entregables)

### Añadido
- `page.php`: plantilla para páginas genéricas de WordPress. Reutiliza `template-parts/page-hero.php` y `.ce-service-content` para consistencia visual total con Servicios/Proyectos/Equipo/Clientes. Incluye soporte completo de página protegida por contraseña (`post_password_required()`/`get_the_password_form()`), `wp_link_pages()` para contenido paginado internamente, y comentarios condicionales si el admin los habilita en una página.

### Sin cambios
- `inc/helpers.php`, `inc/seo.php`, `assets/css/main.css`, `assets/js/main.js`: no requirieron ninguna extensión — todas las clases y funciones necesarias ya existían.

### Notas
- Desde esta versión, WordPress da prioridad automática a `page.php` sobre `index.php` para cualquier página (comportamiento nativo de la Template Hierarchy); `index.php` sigue existiendo sin cambios como fallback final.
- Limitación conocida heredada (sin cambios): los comentarios siguen usando el fallback de compatibilidad nativo de WordPress hasta que `comments.php` exista (Entregable 6B.2, siguiente).
- Ver `DECISIONS.md` para el detalle de la primera aplicación práctica de la metodología de Entregables (D-030).

---

## v0.6.2 — Sprint 6B, Entregable 6B.2: single.php + comments.php

**Módulo:** Entrada individual de blog + plantilla de comentarios (unidad funcional acoplada)

### Añadido
- `single.php`: entrada individual de blog. Hero interno reutilizado (`template-parts/page-hero.php`), meta de autor/fecha/categorías, `wp_link_pages()`, tags como badges (`.ce-badge`), navegación entre entradas (reutiliza `.ce-service-nav`, mismo patrón de Servicios/Proyectos — D-016), comentarios integrados vía `comments_template()`.
- `comments.php`: reemplaza el fallback de compatibilidad nativo de WordPress que usaban `index.php`/`page.php` hasta ahora. Callback propio (`ce_construction_render_comment`) para hilos anidados con avatar/meta/enlace de respuesta, paginación de comentarios (`the_comments_pagination()`), formulario (`comment_form()`) con markup integrado a `.ce-form`/`.ce-field`/`.ce-btn` ya existentes.
- `inc/seo.php` (extensión aditiva): `ce_construction_schema_blog_post()` — Schema.org `BlogPosting` para entradas de blog, manteniendo la misma consistencia de calidad SEO que el resto de tipos de contenido.
- `assets/css/main.css` (extensión aditiva, sección 23): árbol de comentarios (avatar, meta, hilos anidados, estado "comentarios cerrados"). El formulario reutiliza `.ce-form`/`.ce-field`/`.ce-btn` ya existentes, sin duplicar estilos.

### Sin cambios
- `inc/helpers.php`, `assets/js/main.js`: no requirieron ninguna modificación.

### Decisiones clave
- Ver `DECISIONS.md`: D-032 (single.php + comments.php desarrollados como unidad acoplada), D-033 (callback de comentario definido localmente en comments.php, no en helpers.php).

### Notas
- Limitación heredada del Entregable 6B.1 ya resuelta: tanto `single.php` como `page.php` (si el admin habilita comentarios en una página) usan ahora `comments.php` con el estilo completo del sistema de diseño.
- Ver `TODO.md` para el detalle completo.

---

## Próximas versiones (planeadas, no confirmadas)

- **v0.6.3 (propuesta):** Entregable 6B.3 — `404.php` (cierre del Sprint 6B).
- **v0.7.0 (propuesta):** Sprint 7 — Widgets custom, `screenshot.png`, hallazgos Medios de `QA_REPORT.md` (con aprobación explícita), dividido en sus Entregables correspondientes conforme a la metodología permanente.
- **v0.8.0 (propuesta):** Auditoría de accesibilidad y performance (incluye QA-026/QA-027: auto-hospedar fuentes/Font Awesome).
