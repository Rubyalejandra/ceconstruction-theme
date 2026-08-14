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

---

# FASE PARALELA: Optimización UX / Conversión

> A partir de aquí, las entradas corresponden a la nueva fase de trabajo "Optimización UX / Conversión", ejecutada en paralelo al Sprint 8 ("Cierre de Hallazgos QA"), que se **pausa sin cerrarse** en su Entregable 8.2 (QA-030), entregado y pendiente de aprobación (ver `CURRENT_SPRINT.md`, sin modificar). Ver `docs/CURRENT_UX_SPRINT.md` para el seguimiento dedicado de esta fase y `docs/UX_CONVERSION_ANALISIS_Y_PLAN.md` para el análisis y plan completo aprobado.

## v0.8.2 — Sprint UX-1, Entregable UX-1.1: Home Builder — registro central de secciones del Home

**Módulo:** Home Builder — base arquitectónica (sin panel de administración todavía)
**Estado:** Entregado — pendiente de aprobación explícita del usuario (ver `DECISIONS.md` D-038, aplicado también a esta nueva fase)

### Añadido
- **`inc/home-builder.php`** (nuevo): registro central de las 13 secciones de Home previstas en el brief de UX/Conversión (`ce_construction_home_sections()`), orden por defecto que reproduce exactamente el orden anterior de `front-page.php` (`ce_construction_default_home_order()`), y punto de extensión filtrable para la configuración futura (`ce_construction_get_active_home_order()`, filtro `ce_home_active_order`).

### Cambiado
- **`front-page.php`:** la lista fija de 10 `get_template_part()` se reemplaza por un `foreach` sobre el registro de `inc/home-builder.php`. Resultado visual idéntico al anterior (verificado: mismo orden, mismas 10 secciones activas).
- **`functions.php`:** `inc/home-builder.php` añadido a `ce_construction_require_modules()`.
- **`style.css`:** cabecera `Version:` sincronizada de `0.8.1` a `0.8.2`, siguiendo la convención ya vigente del proyecto (ver `DECISIONS.md` D-044) de mantener esta cabecera sincronizada con cada cambio de código. Sin relación con el mecanismo de cache-busting de QA-030 (que usa `filemtime()`, no esta cabecera, y permanece intacto).

### Sin cambios
- Ningún archivo del Sprint 8 (`inc/enqueue.php`, `inc/customizer.php`, `inc/seo.php`, `header.php`, `docs/QA_REPORT.md`, `docs/CURRENT_SPRINT.md`). QA-030/Entregable 8.2 permanece exactamente en el estado en que se pausó.
- Ningún template-part existente. `template-parts/team.php`, `clients.php`, `faq.php` (Sprint UX-2) todavía no existen — quedan registrados en `inc/home-builder.php` para uso futuro, pero fuera del orden activo por defecto.
- Ninguna funcionalidad de configuración/administración: no hay todavía ninguna forma de reordenar o desactivar secciones desde WordPress (llega en UX-1.2).

### Decisiones clave
- Ver `DECISIONS.md`: D-045.

### Documentación actualizada
- `docs/ARCHITECTURE.md` (nueva sección de Home Builder, actualización del flujo de renderizado del Front Page y de la tabla de módulos de `inc/`).
- `docs/TREE.md` (`inc/home-builder.php` añadido; `front-page.php` anotado como data-driven).
- `docs/TODO.md` (nueva sección de backlog para la fase UX/Conversión).
- `docs/PROJECT_STATUS.md` (nota aditiva de la fase paralela, sin alterar el estado documentado del Sprint 8).
- `docs/CURRENT_UX_SPRINT.md` (nuevo — referencia oficial de esta fase, análoga a `CURRENT_SPRINT.md` pero para el track UX).

### Verificaciones ejecutadas
- **Sintaxis PHP:** balance de llaves/paréntesis verificado (Python) en `inc/home-builder.php` (5/5 llaves, 65/65 paréntesis), `front-page.php` (3/3 llaves, 19/19 paréntesis) y `functions.php` (4/4 llaves, 26/26 paréntesis) — los 3 balanceados. `php -l` no disponible en el entorno de desarrollo (limitación metodológica ya documentada en `ARCHITECTURE.md`).
- **Regresión visual del Home:** confirmado por inspección de código que `ce_construction_default_home_order()` reproduce, en el mismo orden, exactamente las 10 llamadas a `get_template_part()` que `front-page.php` tenía antes de este Entregable.
- **Colisión de nombres:** verificado (grep) que ninguna de las funciones nuevas (`ce_construction_home_sections`, `ce_construction_default_home_order`, `ce_construction_get_active_home_order`) ni el filtro `ce_home_sections`/`ce_home_active_order` existían previamente en el proyecto.
- **Dependencias e includes:** `inc/home-builder.php` no depende de que ningún otro módulo se cargue antes que él (no usa ninguna función de otro archivo de `inc/` en tiempo de carga, solo define funciones); se registra en `functions.php` después de `inc/helpers.php`, consistente con la nota ya existente en `ARCHITECTURE.md` sobre que el orden de `helpers.php` en el array no es crítico por la misma razón.
- **Regresión sobre Sprint 8:** confirmado (diff) que ningún archivo tocado por el Entregable 8.2 (`functions.php` en la parte de `CE_THEME_VERSION`, `inc/enqueue.php`, `style.css` en la parte del mecanismo QA-030) cambió su lógica; `style.css` solo recibió el bump de número de versión, sin tocar el comentario ni la lógica de QA-030 documentada en `DECISIONS.md` D-044.

---

## v0.8.3 — Sprint UX-1, Entregable UX-1.2: Home Builder — panel de administración (activar/desactivar y reordenar)

**Módulo:** Home Builder — persistencia real desde WordPress (Customizer)
**Estado:** Entregado — pendiente de aprobación explícita del usuario (ver `DECISIONS.md` D-038)

### Añadido
- **`assets/js/admin-home-builder.js`** (nuevo): inicializa jQuery UI Sortable (ya incluido en WordPress core, sin librerías nuevas) y las casillas de activar/desactivar del control custom del Home Builder; serializa el resultado a JSON en el `theme_mod` correspondiente.
- **`inc/customizer.php`:** nueva sección `ce_section_home_builder` ("CE: Home Builder"), nuevo setting `ce_home_sections_order` (JSON `[{key, enabled}]`, persistencia del orden y estado de las 13 secciones), y nuevo control custom `CE_Customize_Home_Sections_Control`. Funciones de apoyo: `ce_construction_decode_home_sections_order()`, `ce_construction_sanitize_home_sections_order()`, `ce_construction_default_home_sections_order_json()`, y `ce_construction_filter_home_active_order()` (enganchada al filtro `ce_home_active_order` ya expuesto por UX-1.1).
- **`inc/enqueue.php`:** nueva función `ce_construction_enqueue_home_builder_control_script()`, encolada solo en `customize_controls_enqueue_scripts` (admin del Customizer, nunca en frontend), reutilizando `ce_construction_asset_version()` (QA-030) para el cache-busting del nuevo asset.

### Sin cambios
- **`inc/home-builder.php` y `front-page.php`: NO se tocaron**, tal como exigía el alcance de este Entregable — la integración se hizo enteramente a través del filtro `ce_home_active_order` que UX-1.1 ya dejó expuesto.
- `functions.php` (ambos archivos modificados ya estaban registrados en `ce_construction_require_modules()` desde antes de UX-1).
- Ningún archivo del Sprint 8 más allá de la adición aditiva en `inc/enqueue.php` señalada explícitamente en `DECISIONS.md` D-046 (ver esa entrada para el detalle; ningún cambio de lógica en QA-030/QA-010).

### Comportamiento
- **Sin `theme_mod` guardado (instalación nueva o panel nunca abierto):** el Home se comporta exactamente igual que en UX-1.1 (orden por defecto, sin regresión).
- **Con `theme_mod` guardado:** el orden y el activo/inactivo de cada sección se leen del valor guardado; una sección desactivada no se renderiza; el orden de renderizado sigue el orden guardado por el administrador.
- **Team / Clients / FAQ:** visibles en el panel con la casilla deshabilitada y la nota "(próximamente)" (sus template-parts llegan en el Sprint UX-2); no pueden activarse desde el panel hasta entonces.

### Decisiones clave
- Ver `DECISIONS.md`: D-046.

### Verificaciones ejecutadas
- **Sintaxis PHP:** balance de llaves/paréntesis verificado (Python) en `inc/customizer.php` (40/40 llaves, 257/257 paréntesis) e `inc/enqueue.php` (6/6 llaves, 73/73 paréntesis) — ambos balanceados. `php -l` no disponible en el entorno de desarrollo (limitación metodológica ya documentada).
- **JavaScript:** `node --check assets/js/admin-home-builder.js` — sin errores de sintaxis.
- **Colisión de nombres:** verificado (grep) que ninguna de las funciones/clase nuevas (`ce_construction_decode_home_sections_order`, `ce_construction_sanitize_home_sections_order`, `ce_construction_default_home_sections_order_json`, `ce_construction_filter_home_active_order`, `CE_Customize_Home_Sections_Control`, `ce_construction_enqueue_home_builder_control_script`, `ce_construction_home_builder_control_styles`) ni el setting `ce_home_sections_order` existían previamente en el proyecto.
- **Trazado lógico manual (sin entorno WordPress real, misma limitación metodológica documentada en `ARCHITECTURE.md`):**
  - Sin `theme_mod` guardado: `get_theme_mod( 'ce_home_sections_order', '' )` devuelve `''` → `ce_construction_filter_home_active_order()` devuelve el `$default_order` recibido sin modificar → idéntico a UX-1.1.
  - Al desactivar una sección desde el panel: su `enabled` pasa a `false` en el JSON serializado por `admin-home-builder.js` → `ce_construction_filter_home_active_order()` la excluye de `$active` → `front-page.php` no la renderiza (guarda `isset()` ya existente desde UX-1.1 ni siquiera es necesaria en este caso: la clave simplemente no aparece en el array de orden activo).
  - Al reordenar por drag&drop: `serialize()` reconstruye el array en el orden real del DOM tras el `sortable.update` → se conserva ese orden a través de `sanitize`/`decode` (ambos preservan el orden de aparición) → `front-page.php` renderiza en ese orden.
  - Sección sin template-part (p. ej. `team`): checkbox forzado a no-marcado en el render (`$available` en `false`) independientemente del valor guardado; si un JSON manual forzara `enabled: true` de todas formas, `get_template_part()` sobre un archivo inexistente no genera error ni contenido (comportamiento nativo ya documentado en `inc/home-builder.php`).
- **Regresión sobre Sprint 8:** confirmado (diff) que `header.php`, `inc/seo.php`, `docs/QA_REPORT.md`, `docs/CURRENT_SPRINT.md` y `docs/HANDOFF.md` no fueron tocados; el único archivo compartido con el Sprint 8 (`inc/enqueue.php`) solo recibió una adición aditiva no relacionada, sin alterar ninguna línea de la corrección QA-030/QA-010 ya existente.

---

## v0.8.4 — Sprint UX-2, Entregable UX-2.1: secciones de Home — Equipo y Clientes

**Módulo:** Home Builder — nuevas secciones (Team, Clients)
**Estado:** Entregado — pendiente de aprobación explícita del usuario (ver `DECISIONS.md` D-038)

### Añadido
- **`template-parts/team.php`** (nuevo): sección de Home para el CPT `miembro_equipo`. `WP_Query` acotado (`posts_per_page => 8`, `post_status => 'publish'`), auto-ocultamiento vía `ce_cpt_has_posts( 'miembro_equipo' )`, reutiliza `content-equipo.php` como partial de card dentro de `.ce-grid.ce-grid--4` (mismo wrapper que ya usa `archive-equipo.php`). Enlace final a `get_post_type_archive_link( 'miembro_equipo' )`.
- **`template-parts/clients.php`** (nuevo): sección de Home para el CPT `cliente`. `WP_Query` acotado (`posts_per_page => 10`, `post_status => 'publish'`), auto-ocultamiento vía `ce_cpt_has_posts( 'cliente' )`, reutiliza `content-cliente.php` como partial de card dentro de `.ce-clients-grid` (mismo wrapper que ya usa `archive-clientes.php` — NO el `.ce-grid` genérico, ver `DECISIONS.md` D-047 punto 3). Enlace final a `get_post_type_archive_link( 'cliente' )`.

### Modificado
- **`inc/home-builder.php`:** actualización **exclusivamente de comentarios** en las entradas `team`/`clients` de `ce_construction_home_sections()` y en el docblock de `ce_construction_default_home_order()`, para reflejar que sus template-parts ya existen. **Ninguna de las 3 funciones del archivo cambió de lógica ni de valor de retorno** — verificado por diff.

### Sin cambios
- **`inc/customizer.php`: NO se modificó.** El `file_exists()` ya implementado en `CE_Customize_Home_Sections_Control::render_content()` (UX-1.2) resuelve la disponibilidad de cada sección de forma genérica sobre el registro — al existir ya `template-parts/team.php`/`clients.php`, sus casillas en el panel "CE: Home Builder" dejan de forzarse deshabilitadas y de mostrar "(próximamente)" automáticamente, sin ningún cambio de código en este archivo. Ver `DECISIONS.md` D-047 punto 6.
- `front-page.php`, `functions.php`, `assets/css/main.css`, `assets/js/main.js`: sin cambios (ninguna clase CSS ni módulo JS nuevo requerido — ambas secciones reutilizan estilos ya existentes desde el Sprint 5).
- Ningún archivo del Sprint 8 (Sprint 8 sigue pausado, sin tocar en este Entregable).

### Comportamiento
- **Sin activar en el panel del Home Builder:** ninguna de las dos secciones aparece en el Home (comportamiento por defecto, `ce_construction_default_home_order()` no las incluye — sin cambios respecto a UX-1.1/UX-1.2).
- **Activadas en el panel, con contenido publicado en su CPT:** la sección se renderiza en la posición configurada.
- **Activadas en el panel, sin contenido publicado en su CPT (`miembro_equipo`/`cliente` vacíos):** la sección se auto-oculta (`ce_cpt_has_posts()` hace `return;` antes de imprimir cualquier HTML) — mismo comportamiento que `projects.php`/`testimonials.php`/`services.php`/`gallery.php`, sin necesidad de que el administrador la desactive manualmente si más adelante carga contenido.

### Decisiones clave
- Ver `DECISIONS.md`: D-047.

### Verificaciones ejecutadas
- **Sintaxis PHP:** balance de llaves/paréntesis verificado (Python) en `template-parts/team.php` (2/2 llaves, 26/26 paréntesis), `template-parts/clients.php` (2/2 llaves, 25/25 paréntesis) e `inc/home-builder.php` tras la edición de comentarios (5/5 llaves, 69/69 paréntesis, mismas 3 funciones que antes del cambio). `php -l` no disponible en el entorno de desarrollo (limitación metodológica ya documentada en `ARCHITECTURE.md` §10).
- **Verificación de `inc/customizer.php` sin tocar:** simulación manual de `file_exists()` sobre las 3 rutas de `team`/`clients`/`faq` con el árbol de archivos real post-Entregable: `template-parts/team.php` y `template-parts/clients.php` resuelven `true`; `template-parts/faq.php` resuelve `false` (confirma que la casilla de FAQ debe seguir mostrando "(próximamente)" hasta UX-2.2, sin intervención de este Entregable).
- **Reutilización de partials sin duplicación de markup:** confirmado (lectura) que `team.php`/`clients.php` no reimplementan ningún `<article>`/`<div>` de tarjeta — delegan 100% en `content-equipo.php`/`content-cliente.php` ya existentes desde el Sprint 5.
- **Wrapper de grid correcto por componente:** confirmado (lectura de `content-cliente.php` y `main.css` sección 22) que `.ce-clients-grid__item` requiere el contenedor `.ce-clients-grid`, no `.ce-grid`; `clients.php` usa el wrapper correcto. Confirmado (lectura de `content-equipo.php` y `archive-equipo.php`) que `team.php` usa el mismo `.ce-grid.ce-grid--4` que ya usa el archivo de Equipo.
- **Auto-ocultamiento:** verificado por lectura que ambos archivos nuevos hacen `return;` inmediato tras `! ce_cpt_has_posts( ... )`, antes de instanciar el `WP_Query` y antes de imprimir cualquier `<section>` — mismo patrón que `projects.php`, `services.php`, `testimonials.php`, `gallery.php`.
- **Colisión de nombres:** verificado (grep) que ninguna variable nueva (`$equipo_home_query`, `$clientes_home_query`) colisiona con variables ya usadas en otros template-parts del Home (`$proyectos_query`, `$servicios_query`, `$testimonios_query` ya usan otros nombres).
- **Regresión sobre Sprint 8:** confirmado (diff) que ningún archivo del Sprint 8 fue tocado; `inc/customizer.php` (compartido con UX-1.2, no con el Sprint 8) tampoco recibió cambios en este Entregable.

---

## v0.8.5 — Sprint UX-2, Entregable UX-2.2: sección de Home — Preguntas Frecuentes (Sprint UX-2 COMPLETADO)

**Módulo:** Home Builder — sección faltante (FAQ) + extracción de partial compartido
**Estado:** Entregado — pendiente de aprobación explícita del usuario (ver `DECISIONS.md` D-038)

### Añadido
- **`template-parts/content-faq-accordion.php`** (nuevo): partial de ítem individual de accordion FAQ, extraído del bloque "FAQ relacionadas" de `single-servicio.php` (existente desde el Sprint 3). Debe invocarse dentro de un loop estándar y dentro de un `.ce-accordion` ya abierto por el llamador — mismo patrón que `content-servicio.php`/`content-proyecto.php`/`content-equipo.php`/`content-cliente.php`. Usa `get_the_ID()` para el `id`/`aria-controls` del panel (unicidad intrínseca, sin depender de un contador pasado por el llamador — ver `DECISIONS.md` D-048 punto 3).
- **`template-parts/faq.php`** (nuevo): sección de Home para el CPT `ce_faq`. `WP_Query` acotado (`posts_per_page => 8`, `post_status => 'publish'`, `no_found_rows => true`), auto-ocultamiento vía `ce_cpt_has_posts( 'ce_faq' )`, reutiliza `content-faq-accordion.php` dentro de `.ce-accordion.ce-max-w-content` (utilidad ya existente, sin CSS nuevo). Sin enlace "Ver todas": `ce_faq` no tiene archivo público (`has_archive => false`, sin cambios).

### Modificado
- **`single-servicio.php`:** sustituido únicamente el bloque interno del `while` del accordion FAQ (el `<div class="ce-accordion__item">` completo + cálculo de `$faq_index`/`$panel_id`) por `get_template_part( 'template-parts/content-faq-accordion' )`. **Único bloque tocado del archivo** — verificado por diff línea a línea contra el ZIP de UX-2.1: el resto del archivo (metadatos, navegación prev/next, relacionados, sidebar) es idéntico.
- **`inc/home-builder.php`:** actualización **exclusivamente de comentarios** en la entrada `faq` de `ce_construction_home_sections()` y en el docblock de `ce_construction_default_home_order()`, reflejando que las 3 secciones (`team`/`clients`/`faq`) ya tienen template-part real y que el Sprint UX-2 queda completado. **Ninguna de las 3 funciones del archivo cambió de lógica ni de valor de retorno** — verificado por diff.

### Sin cambios
- **`inc/customizer.php`: NO se modificó.** Igual que en UX-2.1, el `file_exists()` genérico de `CE_Customize_Home_Sections_Control` detecta automáticamente que `template-parts/faq.php` ya existe y habilita su casilla en el panel "CE: Home Builder" sin intervención de código. Ver `DECISIONS.md` D-048 punto 7. Con esto, las 3 secciones que dependían de este mecanismo (team, clients, faq) quedan validadas.
- `inc/cpt-faq.php`, `front-page.php`, `functions.php`, `assets/css/main.css`, `assets/js/main.js` (incluido `ModuleAccordion`, confirmado ya genérico — ver `DECISIONS.md` D-048 punto 4): sin cambios.
- Ningún archivo del Sprint 8 (Sprint 8 sigue pausado, sin tocar en este Entregable).

### Comportamiento
- **Sin activar en el panel del Home Builder:** la sección FAQ no aparece en el Home (comportamiento por defecto, sin cambios).
- **Activada en el panel, con preguntas publicadas en `ce_faq`:** la sección se renderiza en la posición configurada, con el mismo comportamiento de accordion (un solo ítem abierto a la vez) que ya tenía `single-servicio.php`.
- **Activada en el panel, sin preguntas publicadas:** la sección se auto-oculta (`ce_cpt_has_posts()` hace `return;` antes de imprimir cualquier HTML) — mismo comportamiento que `team.php`/`clients.php`/`projects.php`.
- **`single-servicio.php`:** el bloque "FAQ relacionadas" sigue funcionando exactamente igual que antes (misma consulta, mismo wrapper, mismo comportamiento visual) — el único cambio observable es el sufijo interno del atributo `id`/`aria-controls` (de un índice secuencial a `get_the_ID()`), sin efecto visible ni funcional para el usuario final.

### Decisiones clave
- Ver `DECISIONS.md`: D-048.

### Verificaciones ejecutadas
- **Sintaxis PHP:** balance de llaves/paréntesis verificado (Python) en `template-parts/faq.php`, `template-parts/content-faq-accordion.php`, `single-servicio.php` e `inc/home-builder.php` tras las ediciones: los 4 archivos balanceados (0 de diferencia en llaves y paréntesis). `php -l` no disponible en el entorno de desarrollo (limitación metodológica ya documentada en `ARCHITECTURE.md` §10).
- **`inc/home-builder.php`:** confirmado por `grep` que las 3 funciones (`ce_construction_home_sections()`, `ce_construction_default_home_order()`, `ce_construction_get_active_home_order()`) siguen presentes sin cambios de firma; diff contra el ZIP de UX-2.1 confirma que solo cambiaron comentarios.
- **`single-servicio.php`:** diff línea a línea contra el ZIP de UX-2.1 confirma que el único cambio es el bloque interno del `while` del accordion FAQ — el resto del archivo (204 líneas originales) es idéntico.
- **`inc/customizer.php`, `front-page.php`, `functions.php`: sin cambios**, confirmado por diff contra el ZIP de UX-2.1 (0 diferencias en los 3 archivos).
- **`file_exists()` para `faq`:** simulación manual sobre el árbol de archivos real post-Entregable confirma que `template-parts/faq.php` ahora resuelve `true` — la casilla de FAQ en el panel del Home Builder debe habilitarse automáticamente, sin código adicional.
- **`ModuleAccordion` sin colisión entre contextos:** confirmado por lectura de `assets/js/main.js` que el comportamiento "un solo ítem abierto" está acotado vía `item.closest('.ce-accordion')` (no es un estado global compartido entre todas las instancias de `.ce-accordion` de la página) — soporta múltiples accordions independientes sin cambios. Los IDs de panel (`ce-faq-panel-{ID de post}`) son únicos por diseño (IDs de WordPress ya son únicos globalmente), eliminando cualquier posibilidad de colisión de `id`/`aria-controls` entre el accordion de Home y el de `single-servicio.php`, incluso en el hipotético caso de coexistir en el mismo documento.
- **Auto-ocultamiento de `faq.php`:** verificado por lectura que hace `return;` inmediato tras `! ce_cpt_has_posts( 'ce_faq' )`, antes de instanciar el `WP_Query` y antes de imprimir cualquier `<section>` — mismo patrón que `team.php`/`clients.php`/`projects.php`.
- **Reutilización sin duplicación de markup:** confirmado (lectura) que `faq.php` y el bloque modificado de `single-servicio.php` no reimplementan ningún `<div class="ce-accordion__item">` — ambos delegan 100% en `content-faq-accordion.php`.
- **Regresión sobre Sprint 8:** confirmado (diff) que ningún archivo del Sprint 8 fue tocado.

---

## 🎉 SPRINT UX-2 COMPLETADO

Las 13 secciones previstas en el brief de UX/Conversión ya tienen template-part real y están disponibles en el catálogo del Home Builder (`ce_construction_home_sections()`): Hero, Quiénes Somos, Servicios, Proyectos, Estadísticas, Por Qué Elegirnos, Testimonios, Galería, **Equipo, Clientes, Preguntas Frecuentes** (las 3 últimas, completadas en el Sprint UX-2), CTA y Formulario de Cotización. Ninguna de las 3 nuevas (team/clients/faq) se activa automáticamente: quedan disponibles para que el administrador las active y ordene explícitamente desde el panel "CE: Home Builder" del Customizer (UX-1.2). Sprint UX-2 formalmente completado a la espera de tu aprobación de UX-2.2 (regla D-038).

---

## Sprint UX-3, Entregable UX-3.1: CTA de cotización centralizado + modo configurable (declarado bajo v0.8.5 — sin bump adicional)

**Módulo:** CTA centralizado (`ce_get_quote_cta_url()`) + modos del formulario de cotización
**Estado:** Entregado — pendiente de aprobación explícita del usuario (ver `DECISIONS.md` D-038)

> **Nota de versionado (por decisión explícita del usuario):** `style.css` traía un desfase acumulado desde UX-1.2 (se quedó en `0.8.2` mientras `CHANGELOG.md` ya documentaba hasta `v0.8.5`). Ese desfase se corrigió como parte de este Entregable, pero **sin aplicar un segundo incremento a `0.8.6`** — tanto la corrección de versión como el trabajo funcional de UX-3.1 quedan declarados bajo `v0.8.5`. Ver `DECISIONS.md` D-049.

### Añadido
- **`ce_get_quote_cta_url()`** (nueva, en `inc/helpers.php`): fuente única de verdad de la URL/ancla de cualquier CTA de cotización del tema, según el `theme_mod` `ce_quote_form_mode`.
- **`inc/customizer.php`:** nueva sección "CE: Formulario de Cotización" (`ce_section_quote_form`), setting `ce_quote_form_mode` (`integrated` | `modal` | `disabled`, control `radio`), y `ce_construction_sanitize_quote_form_mode()`.

### Cambiado
- **`header.php`:** botón "Cotizar" (escritorio) y botón "Cotización Gratuita" (menú móvil) migrados a `ce_get_quote_cta_url()`; ambos se omiten automáticamente en modo `disabled`.
- **`footer.php`:** enlace "Cotización" del menú de respaldo del footer migrado; se omite en modo `disabled`.
- **`template-parts/cta.php`:** `ce_cta_btn_url` usa `ce_get_quote_cta_url()` como *default* (preserva cualquier URL personalizada ya guardada por el administrador); botón envuelto en guarda nueva para ocultarse cuando el valor efectivo resulta vacío.
- **`template-parts/hero.php`:** mismo tratamiento que `cta.php` sobre `ce_hero_btn1_url`.
- **`template-parts/sidebar-servicios.php` y `template-parts/sidebar-proyectos.php`:** botón "Cotizar ahora" migrado; se omite en modo `disabled`.
- **`style.css`:** `Version: 0.8.2` → `Version: 0.8.5` (corrección de desfase, ver nota de versionado arriba).

### Sin cambios
- **`inc/home-builder.php`, `front-page.php`, `functions.php`, `inc/enqueue.php`: sin cambios en este Entregable.**
- **`template-parts/quote-form.php`:** sin cambios — el modal (Entregable UX-3.2) es quien tocará este archivo (parámetro de contexto), no UX-3.1.
- Ningún archivo del Sprint 8 (`header.php` se tocó, pero únicamente para la migración del CTA descrita arriba — verificado por diff que la corrección QA-017 (`tabindex="-1"` en `<main>`) y el resto del Sprint 8 permanecen intactos).

### Comportamiento
- **Modo `integrated` (por defecto):** idéntico al comportamiento histórico del tema — los 7 puntos (6 archivos) apuntan a `#ce-quote-form`. Cero regresión visual para instalaciones que no toquen esta configuración.
- **Modo `disabled`:** los 7 puntos se ocultan automáticamente, sin editar ningún archivo. Excepción documentada: si el administrador ya había personalizado explícitamente `ce_cta_btn_url`/`ce_hero_btn1_url` con una URL propia no relacionada con la cotización, ese botón específico no se oculta (ver `DECISIONS.md` D-049).
- **Modo `modal`:** los 7 puntos apuntan a `#ce-quote-modal`, ancla todavía sin destino real en el DOM (comportamiento inerte, no roto) hasta el Entregable UX-3.2.

### Decisiones clave
- Ver `DECISIONS.md`: D-049.

### Verificaciones ejecutadas
- **Sintaxis PHP:** balance de llaves/paréntesis verificado (Python) en los 8 archivos tocados — todos balanceados: `inc/helpers.php` (33/33, 141/141), `inc/customizer.php` (41/41, 282/282), `header.php` (0/0, 58/58), `footer.php` (2/2, 87/87), `template-parts/cta.php` (1/1, 21/21), `template-parts/hero.php` (1/1, 29/29), `template-parts/sidebar-servicios.php` (1/1, 36/36), `template-parts/sidebar-proyectos.php` (1/1, 36/36). `php -l` no disponible en el entorno de desarrollo (limitación metodológica ya documentada).
- **Cero anclas hardcodeadas restantes:** confirmado (`grep -rn "#ce-quote-form"` sobre todo el árbol PHP, excluyendo la propia definición en `inc/helpers.php`) que no queda ningún punto sin migrar.
- **Colisión de nombres:** verificado que `ce_get_quote_cta_url`, `ce_construction_sanitize_quote_form_mode`, `ce_quote_form_mode` y `ce_section_quote_form` no existían previamente en el proyecto.
- **Regresión sobre Sprint 8:** confirmado (diff) que `inc/seo.php`, `inc/enqueue.php`, `docs/QA_REPORT.md`, `docs/CURRENT_SPRINT.md` y `docs/HANDOFF.md` no fueron tocados; `header.php` cambió únicamente en los 2 puntos de CTA descritos, sin afectar la corrección QA-017 ya presente.
- **Regresión sobre UX-1/UX-2:** confirmado (diff) que `inc/home-builder.php` y `front-page.php` no fueron tocados; el registro de secciones y el orden activo del Home Builder no se ven afectados por este Entregable.

### Corrección aplicada tras revisión del usuario (antes de la aprobación del Entregable)
El usuario detectó que, en modo "Integrado" (el modo por defecto), el botón CTA de `template-parts/cta.php` no aparecía — contradiciendo el criterio de aceptación de este mismo Entregable. Causa: `get_theme_mod( 'ce_cta_btn_url', ce_get_quote_cta_url() )` y su equivalente en `template-parts/hero.php` solo aplican el segundo argumento como *fallback* mientras el theme_mod nunca se haya guardado — ambos campos carecen de `'default'` propio desde su registro original, así que quedan persistidos como `''` en cuanto el administrador publica cualquier cambio del Customizer, y a partir de ahí el *fallback* deja de aplicarse. **Corregido** en ambos archivos: se sustituyó por una comprobación explícita de cadena vacía antes de aplicar `ce_get_quote_cta_url()`, sin afectar a los otros 5 puntos de CTA (que llaman a la función directamente, sin pasar por `get_theme_mod()`). Ver `DECISIONS.md` D-050 para el detalle completo, incluida la causa por la que el trazado lógico manual original no lo detectó.
