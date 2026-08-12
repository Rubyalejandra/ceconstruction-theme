# CE Construction — CHANGELOG.md

> Historial de versiones del proyecto. Este archivo es acumulativo: cada nueva versión se agrega al final, nunca se reescribe una versión anterior.

---

## v0.1.0 — Arquitectura y Backend
**Módulo:** Bootstrap, CPT, Customizer, SEO, Formulario (backend)
### Añadido
- Arquitectura modular (`functions.php` bootstrap + `inc/*`), `style.css` con design tokens base.
- `inc/setup.php`, `inc/enqueue.php`, `inc/customizer.php` (7 secciones).
- 6 Custom Post Types + taxonomías.
- `inc/meta-boxes.php`, `inc/quote-form.php` (backend completo con nonce, validación, adjuntos, `wp_mail`), `inc/seo.php` (meta tags, OG, Schema Organization).
### Decisiones clave
- Ver `DECISIONS.md`: D-001, D-002, D-004.

## v0.2.0 — Sistema de Diseño y Plantillas del Home
**Módulo:** Sistema CSS, Sistema JS, Header, Footer, Front Page
### Añadido
- `assets/css/main.css`, `assets/js/main.js` (12 módulos ES6), `inc/helpers.php`, `header.php`, `footer.php`, `front-page.php` + 10 template-parts del home.
### Corregido
- `ModuleModals`: `querySelector` → `querySelectorAll`. Ver D-008.
### Decisiones clave
- Ver `DECISIONS.md`: D-003, D-005 a D-008.

## v0.2.1 — Cierre de etapa y documentación de transferencia
### Añadido
- `HANDOFF.md`, `CHANGELOG.md`, `DECISIONS.md` (D-001 a D-008).

## v0.3.0 — Sprint 3: Módulo de Servicios (frontend completo)
### Añadido
- `archive-servicio.php`, `single-servicio.php`, `page-hero.php`, `content-servicio.php`, `sidebar-servicios.php`, relacionados en `inc/helpers.php`, schema en `inc/seo.php`, sección 20 de `main.css`, `ModuleAccordion`.
### Decisiones clave
- Ver `DECISIONS.md`: D-009 a D-013.

## v0.3.1 — Sprint de QA e Integración
### Añadido
- `QA_REPORT.md`: 29 hallazgos (1 Crítico, 8 Altos, 9 Medios, 5 Bajos, 6 Mejoras futuras). Sin cambios de código.

## v0.4.0 — Sprint 4: Módulo de Proyectos (frontend completo)
### Añadido
- `archive-proyecto.php`, `single-proyecto.php`, `content-proyecto.php`, `sidebar-proyectos.php`, relación inversa en `inc/helpers.php`, schema Project en `inc/seo.php`, sección 21 de `main.css`.
### Decisiones clave
- Ver `DECISIONS.md`: D-014 a D-016.

## v0.4.1 — Sprint 5, Fase 1: Correcciones de QA (Críticos y Altos)
### Corregido
- QA-001 a QA-009 en `inc/quote-form.php`, `inc/meta-boxes.php`, `inc/cpt-servicios.php`, `footer.php`, `functions.php`, `style.css`, `assets/css/main.css`.
### Decisiones clave
- Ver `DECISIONS.md`: D-017 a D-024.

## v0.5.0 — Sprint 5, Fase 3: Módulo de Equipo y Clientes
### Añadido
- `archive-equipo.php`, `single-equipo.php`, `archive-clientes.php`, `single-clientes.php`, `content-equipo.php`, `content-cliente.php`, schema Person/Organization-cliente, sección 22 de `main.css`.
### Cambiado
- `inc/cpt-clientes.php`: `has_archive` false→true.
### Decisiones clave
- Ver `DECISIONS.md`: D-025 a D-027.

## v0.6.0 — Entregable 6A: index.php (bloqueador crítico resuelto)
### Añadido
- `index.php`, `content-fallback.php`, `no-results.php`.
### Corregido
- `.ce-mt-6`/`.ce-mb-6` faltantes en `main.css`.
### Decisiones clave
- Ver `DECISIONS.md`: D-028, D-029.

## v0.6.1 — Sprint 6B, Entregable 6B.1: page.php
### Añadido
- `page.php`.

## v0.6.2 — Sprint 6B, Entregable 6B.2: single.php + comments.php
### Añadido
- `single.php`, `comments.php`, schema BlogPosting, sección 23 de `main.css`.
### Decisiones clave
- Ver `DECISIONS.md`: D-032, D-033.

## v0.6.3 — Sprint 6B, Entregable 6B.3: 404.php (Sprint 6B COMPLETADO)
### Añadido
- `404.php` (`status_header(404)`, `nocache_headers()`, sección "Quizás te interese").
### Decisiones clave
- Ver `DECISIONS.md`: D-035.

## v0.7.0 — Sprint 7, Entregable 7.1: inc/widgets.php — ✅ Completado
### Añadido
- `inc/widgets.php` (`CE_Construction_Widget_Contact`, `CE_Construction_Widget_Social`).
### Decisiones clave
- Ver `DECISIONS.md`: D-036.
### Nota de mantenimiento
- Confirmada la eliminación de `inc/enqueue_1.php` (duplicado accidental) del repositorio oficial.

## v0.7.1 — Sprint 7, Entregable 7.2: archive.php genérico — ✅ Completado
### Añadido
- `archive.php` (fallback para categoría/etiqueta/autor/fecha y CPTs `testimonio`/`ce_faq`).
### Decisiones clave
- Ver `DECISIONS.md`: D-037.

## v0.7.2 — Sprint 7, Entregable 7.3: corrección QA-018 — ✅ Completado
### Corregido
- `assets/css/main.css` — sección 24 (aditiva), responsive de `.ce-header__top`.
### Decisiones clave
- Ver `DECISIONS.md`: D-039.

## v0.7.3 — Sprint 7, Entregable 7.4: screenshot.png — ✅ Completado
### Añadido
- `screenshot.png` (1200×900px, mockup propio con los tokens de diseño reales del tema, sin fotografías del cliente).
### Decisiones clave
- Ver `DECISIONS.md`: D-040.

---

## 🎉 SPRINT 7 COMPLETADO

Los 4 Entregables del Sprint 7 ("Extras y refinamiento QA") están aprobados. El tema cubre en este punto: todo el contenido de "primera clase" (Servicios, Proyectos, Equipo, Clientes, Blog, páginas genéricas, 404, archivo genérico), widgets custom, 1 hallazgo QA Medio corregido (QA-018), y vista previa en wp-admin (`screenshot.png`).

---

## v0.8.0 — Sprint 8, Entregable 8.1: correcciones triviales de bajo riesgo (QA-010, QA-011, QA-013 parcial, QA-014, QA-015, QA-017)

**Módulo:** Cierre de hallazgos QA re-priorizados contra el `QA_REPORT.md` consolidado (QA-001 a QA-042)
**Estado:** ✅ Completado — aprobado explícitamente por el usuario.

### Corregido
- **QA-010** (`inc/enqueue.php`): eliminado el filtro manual `ce_construction_add_defer_attribute()` sobre `script_loader_tag`, redundante con `wp_script_add_data( 'defer', true )` (soporte nativo desde WP 6.3, el proyecto apunta a WP 7.x).
- **QA-011** (`inc/customizer.php`): quitado `'transport' => 'postMessage'` de los 3 ajustes de color del Customizer (Primario/Secundario/Acento); no existía ningún script en `customize_preview_init` que lo aprovechara, así que el comportamiento real ya era un refresh — ahora el código refleja eso explícitamente (default `refresh`).
- **QA-014** (`inc/seo.php`): nueva función `ce_construction_output_json_ld( $data )` que aplica `JSON_UNESCAPED_SLASHES` y neutraliza `</script>` literal antes de imprimir. Reemplaza los 8 `echo` directos de JSON-LD existentes (Organización, Servicio + BreadcrumbList, Proyecto + BreadcrumbList, Persona, Cliente, BlogPosting) — mismos schemas, mismos datos, sin cambios de contenido.
- **QA-017** (`header.php`): añadido `tabindex="-1"` a `<main id="ce-main-content">` para que el skip-link mueva el foco de teclado correctamente.

### Corregido parcialmente
- **QA-013** (`style.css`): corregido el comentario de cabecera, que afirmaba un `@import` de `main.css` inexistente. La unificación real de los bloques `:root` duplicados entre `style.css` y `assets/css/main.css` queda en backlog (decisión arquitectónica pendiente de aprobación).

### Verificado sin cambio de código
- **QA-015** (`inc/quote-form.php`): se confirmó que `$attachment_name` sí se usa (en `post_title` al registrar el adjunto como attachment de Media Library) — el hallazgo, tal como estaba descrito ("variable calculada pero nunca usada"), no se reproduce en el código actual.

### Sin cambios
- Ningún archivo PHP/CSS/JS fuera de los 5 listados arriba. `assets/js/main.js`, `inc/helpers.php`, `inc/meta-boxes.php` no se tocaron (QA-012 y QA-016 quedan para Entregables posteriores).

### Decisiones clave
- Ver `DECISIONS.md`: D-042 (incluye por qué la agrupación de D-041 quedó superseded).

### Verificaciones ejecutadas
- **Sintaxis PHP:** verificación de balance de llaves/paréntesis en los 4 archivos PHP modificados (`header.php`, `inc/enqueue.php`, `inc/customizer.php`, `inc/seo.php`) — todos balanceados.
- **Dependencias e includes:** sin cambios en la lista de módulos cargados por `functions.php`; ninguna función nueva colisiona con nombres existentes (`ce_construction_output_json_ld` es la única función nueva, verificado que no existía previamente en el proyecto).
- **CSS:** cambio en `style.css` es exclusivamente un comentario; no se tocó ninguna regla ni variable.

---

## 🔎 Hallazgo adicional detectado durante la planificación del Sprint 8 (no corregido en este Entregable)

Al re-planificar el Sprint 8 contra el `QA_REPORT.md` consolidado se confirmó **QA-030** (Alto, de la auditoría integral post-Sprint 7): `CE_THEME_VERSION` en `functions.php` y `Version:` en `style.css` siguen fijos en `0.4.1` pese a que el proyecto ya está en v0.8.0, rompiendo el cache-busting de `assets/css/main.css`/`assets/js/main.js` en cualquier despliegue desde entonces. No se corrigió en este Entregable por su alcance amplio (afecta el versionado de todos los assets encolados) — ver Roadmap abajo.

---

## Roadmap actualizado — Sprint 8 (re-planificado, ver `DECISIONS.md` D-042)

> La agrupación anterior de esta sección (8.1: QA-010/014/015/017, 8.2: QA-011/012/013/016, 8.3: Bajos, 8.4: Mejoras futuras) queda **superseded** — se generó antes de consolidar `QA_REPORT.md` con la auditoría integral (QA-030 a QA-042) y antes de verificar QA-015 contra el código real.

- **8.1** — ✅ Desarrollado (este release, v0.8.0): QA-010, QA-011, QA-013 (parcial), QA-014, QA-015 (verificación), QA-017.
- **8.2** (propuesto, no iniciado) — QA-030 (Alto: centralizar el versionado de assets).
- **8.3** (propuesto, no iniciado) — QA-016 (mover el script inline de galería a `assets/js/admin-gallery.js` con dependencia `jquery` declarada).
- **8.4** (propuesto, no iniciado) — QA-012 (caché de las consultas de "relacionados" en `inc/helpers.php`).
- **8.5** (propuesto, requiere decisión arquitectónica previa) — QA-031 (Alto: adjuntos de cotización potencialmente accesibles por URL directa).
- **Backlog sin Entregable asignado:** QA-032 a QA-040 (auditoría integral), QA-019 a QA-029 (Bajos/Mejoras futuras históricos), QA-041 (nota metodológica), QA-042 (mejora futura).

### Sprint 9 (propuesto)
Auditoría de accesibilidad y performance (auto-hospedar Google Fonts/Font Awesome, Core Web Vitals) — relacionado con QA-026/QA-027/QA-042.

### Fuera de sprint
Reemplazo de `screenshot.png` por fotografías reales del cliente, cuando estén disponibles (ver `DECISIONS.md` D-040) — sin cambio de código requerido.

---

## v0.8.1 — Sprint 8, Entregable 8.2: corrección QA-030 (cache-busting de assets)

**Módulo:** Integridad de despliegue — versionado de assets propios del tema
**Estado:** Entregado — pendiente de aprobación explícita del usuario (ver `DECISIONS.md` D-038)

### Corregido
- **QA-030** (Alto): `CE_THEME_VERSION` y la cabecera `Version:` de `style.css` estaban congeladas en `0.4.1` desde el Sprint 5 pese a que el proyecto avanzó hasta v0.8.0, rompiendo el cache-busting real de `assets/css/main.css`/`assets/js/main.js` en cada despliegue desde entonces.

### Cambiado (mecanismo, no solo valor)
- **`inc/enqueue.php`:** nueva función `ce_construction_asset_version( $relative_path )`, que devuelve `filemtime()` del archivo real en disco. Aplicada a los 3 assets propios del tema (`style.css`, `assets/css/main.css`, `assets/js/main.js`) en sus respectivos `wp_enqueue_style()`/`wp_enqueue_script()`, reemplazando `CE_THEME_VERSION`. El cache-busting ahora se actualiza automáticamente en cada cambio de archivo, sin intervención manual.
- **`functions.php`:** `CE_THEME_VERSION` deja de ser un valor hardcodeado y pasa a derivarse de `wp_get_theme()->get( 'Version' )` (lee la cabecera de `style.css`). Se conserva como valor informativo general; ya no interviene en el cache-busting.
- **`style.css`:** cabecera `Version:` sincronizada de `0.4.1` a `0.8.1`, más comentario explicando su nuevo rol como fuente única de `CE_THEME_VERSION`.

### Sin cambios
- `assets/css/main.css`, `assets/js/main.js` (no requerían modificación de contenido para esta corrección), ni ningún otro archivo PHP/CSS/JS del proyecto.

### Documentación actualizada
- `ARCHITECTURE.md` sección 9 (flujo de carga de CSS/JS) y su historial de cambios — cambio real de mecanismo, no solo de valor.
- `docs/QA_REPORT.md` — QA-030 marcado como corregido (resumen ejecutivo, hallazgo detallado en sección 4.1, matriz de priorización, cierre del documento).

### Decisiones clave
- Ver `DECISIONS.md`: D-044.

### Verificaciones ejecutadas
- **Sintaxis PHP:** balance de llaves/paréntesis verificado (Python) en `functions.php` (4/4 llaves, 25/25 paréntesis) e `inc/enqueue.php` (5/5 llaves, 62/62 paréntesis) — ambos con balance 0. PHP no disponible en el entorno de desarrollo para `php -l` (limitación metodológica ya documentada).
- **Dependencias e includes:** confirmado que `ce_construction_asset_version()` está definida en `inc/enqueue.php` antes de sus 3 usos, y que estos solo se ejecutan dentro de `ce_construction_enqueue_assets()` (enganchada a `wp_enqueue_scripts`, mucho después de que `functions.php`/`CE_THEME_DIR` ya estén disponibles) — sin problema de orden de carga. Confirmado que no quedó ningún uso residual de `CE_THEME_VERSION` fuera de su definición, su fallback defensivo, y comentarios explicativos.
- **JavaScript:** no aplica — ningún archivo JS modificado en este Entregable.
