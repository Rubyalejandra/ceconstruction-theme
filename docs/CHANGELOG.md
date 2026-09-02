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

---

## Sprint UX-3, Entregable UX-3.2: modal de cotización (declarado bajo v0.8.5 — bump de `style.css` pendiente de tu confirmación)

**Módulo:** Modo Popup del formulario de cotización (`#ce-quote-modal`)
**Estado:** Entregado — pendiente de aprobación explícita del usuario (ver `DECISIONS.md` D-038)

> **Nota de versionado:** siguiendo la instrucción explícita de este Entregable, **no se aplicó ningún bump a `style.css`** (permanece en `Version: 0.8.5`, tal como quedó tras UX-3.1). Versión propuesta para cuando confirmes: **`0.8.6`** — corresponde a un cambio funcional real (nuevo modo de interacción, 5 archivos tocados) sobre `0.8.5`, siguiendo la misma convención ya usada en Entregables anteriores (un incremento de versión por Entregable con cambios de código). Indícamelo y lo aplico en el próximo Entregable o en una corrección puntual, según prefieras.

### Añadido
- **`#ce-quote-modal`** (nuevo bloque en `footer.php`): mismo patrón estructural que `#ce-modal-success`/`#ce-modal-error` (`.ce-modal-overlay > .ce-modal[role=dialog][aria-modal][aria-labelledby]`), impreso **únicamente** cuando `ce_quote_form_mode === 'modal'`. Reutiliza `template-parts/quote-form.php` con `$args['context'] = 'modal'` para el contenido — sin duplicar ningún campo del formulario.
- **`$args['context']` en `template-parts/quote-form.php`:** invocado con `context='modal'` omite el `<section>`/encabezado/tarjeta de la presentación integrada e imprime únicamente el `<form id="ce-quote-form">`. Invocado sin `context` (comportamiento histórico) no cambia.
- **`.ce-modal--form`** (nuevo modificador CSS, `assets/css/main.css` sección 25, 100% aditiva): amplía `.ce-modal` a `max-width: 620px` + `max-height: 90vh; overflow-y: auto` + `text-align: left`, necesario para alojar el formulario completo (7 campos + adjunto) sin recortarse.

### Modificado
- **`template-parts/quote-form.php`:** además del soporte de `$args['context']`, se añadió una guarda de auto-ocultamiento — cuando `ce_quote_form_mode === 'modal'`, cualquier invocación **sin** `context='modal'` no imprime nada. Resuelve simultáneamente los 3 puntos de invocación existentes del archivo (`front-page.php` vía Home Builder, `single-servicio.php`, `single-proyecto.php`) sin tocar ninguno de esos 3 archivos — ver `DECISIONS.md` D-051.
- **`assets/js/main.js`:**
  - `ModuleSmoothScroll`: si el ancla clickeada apunta a un elemento con clase `.ce-modal-overlay`, llama a `ModuleModals.open()` en vez de hacer scroll — sin esto, ningún CTA con `href="#ce-quote-modal"` habría abierto realmente el modal.
  - `ModuleQuoteForm`: detecta si el formulario vive dentro de un `.ce-modal-overlay` (`this.parentModalOverlay`) y lo cierra con `ModuleModals.close()` antes de abrir `#ce-modal-success`/`#ce-modal-error`, evitando overlays superpuestos.
  - Ningún módulo nuevo: ambas adiciones reutilizan `ModuleModals.open()`/`close()`, ya existentes.
- **`inc/customizer.php`:** actualizado únicamente el *label* de la opción `'modal'` del control `ce_quote_form_mode` (se retira la nota "(próximamente)", ahora incorrecta). Setting, sanitize callback y sección: sin cambios.

### Sin cambios
- **`inc/home-builder.php`, `front-page.php`, `header.php`, `template-parts/cta.php`, `template-parts/hero.php`, `template-parts/sidebar-servicios.php`, `template-parts/sidebar-proyectos.php`, `inc/helpers.php`, `single-servicio.php`, `single-proyecto.php`, `inc/quote-form.php` (handler AJAX), `inc/enqueue.php`, `style.css`: sin cambios en este Entregable** — verificado por diff contra el ZIP de UX-3.1. La centralización de UX-3.1 (`ce_get_quote_cta_url()`, D-049) hizo innecesario tocar los 7 puntos de CTA: ya apuntaban a la función centralizada, que ahora devuelve `#ce-quote-modal` en modo `modal` sin ningún cambio adicional en esos archivos.
- Ningún archivo del Sprint 8 (Sprint 8 sigue pausado, sin tocar en este Entregable — verificado por diff).

### Comportamiento
- **Modo `integrated` (sin cambios respecto a UX-3.1):** los 7 CTA apuntan a `#ce-quote-form`; `#ce-quote-modal` **no se genera** en ninguna página.
- **Modo `modal`:**
  - `#ce-quote-modal` existe una sola vez por página (impreso globalmente desde `footer.php`, presente en Home, Servicios y Proyectos por igual).
  - La sección integrada del formulario **deja de imprimirse** en sus 3 puntos de invocación (Home vía Home Builder, `single-servicio.php`, `single-proyecto.php`) — evita `id="ce-quote-form"` duplicado.
  - Los 7 CTA (incluidos los de `sidebar-servicios.php`/`sidebar-proyectos.php`) abren el modal, **aunque la página actual no contenga ningún formulario embebido**.
  - Envío exitoso/erróneo dentro del modal: cierra `#ce-quote-modal` y abre `#ce-modal-success`/`#ce-modal-error` — mismo flujo AJAX/nonce documentado en `ARCHITECTURE.md` §8, sin cambios en el handler server-side.
- **Modo `disabled` (sin cambios respecto a UX-3.1):** los 7 CTA se ocultan; `#ce-quote-modal` no se genera.

### Decisiones clave
- Ver `DECISIONS.md`: D-051.

### Verificaciones ejecutadas
- **Sintaxis PHP:** balance de llaves/paréntesis (Python) en los 3 archivos PHP tocados/reescritos: `template-parts/quote-form.php` (0/0), `footer.php` (0/0), `inc/customizer.php` (0/0) — todos balanceados. `php -l` no disponible en el entorno (limitación metodológica ya documentada).
- **Sintaxis JS:** `node --check assets/js/main.js` → sin errores.
- **Sintaxis CSS:** balance de llaves (Python) en `assets/css/main.css` tras la sección 25 nueva → 0 de diferencia.
- **Alcance del diff:** confirmado (`diff -rq` contra el ZIP de UX-3.1) que únicamente 5 archivos cambiaron: `assets/css/main.css`, `assets/js/main.js`, `footer.php`, `inc/customizer.php`, `template-parts/quote-form.php`. Cero archivos nuevos o eliminados fuera de esos 5 (todos ya existían, ninguno se creó ni se borró). `style.css` confirmado sin cambios.
- **Archivos explícitamente confirmados sin cambios (diff 1:1):** `inc/home-builder.php`, `front-page.php`, `inc/quote-form.php`, `inc/enqueue.php`, `header.php`, `template-parts/cta.php`, `template-parts/hero.php`, `template-parts/sidebar-servicios.php`, `template-parts/sidebar-proyectos.php`, `inc/helpers.php`.
- **Puntos de invocación de `quote-form.php` cubiertos por la guarda:** confirmado (`grep`) que existen exactamente 3 invocaciones sin `context='modal'` en todo el árbol (`front-page.php` vía registro de Home Builder, `single-servicio.php`, `single-proyecto.php`) — los 3 quedan cubiertos por la misma guarda, sin tocar ninguno de esos 3 archivos.
- **`#ce-quote-modal` impreso una sola vez:** confirmado por lectura que el bloque en `footer.php` está dentro de un único `if`, sin repetición.
- **Regresión sobre Sprint 8:** confirmado (diff) que ningún archivo del Sprint 8 fue tocado.

### Continuación de sesión + corrección arquitectónica (D-053) — coexistencia integrado+popup
El diseño original de este Entregable (arriba, D-051) hacía que `integrated` y `modal` fueran mutuamente excluyentes. El usuario requería que **ambos coexistieran**: formulario integrado visible donde ya existía, y los 7 CTA del sitio abriendo el popup en cualquier página (incluidas las que nunca tuvieron formulario embebido, como `archive-servicio.php`/`archive-proyecto.php`).

Se retomaron sin descartarlos los cambios parciales de una sesión anterior (`ce_construction_mark_quote_form_rendered_inline()`/`ce_construction_quote_form_rendered_inline()`/`ce_construction_quote_form_rendered_inline_get()` en `inc/helpers.php`, con la corrección ya aplicada del `static` compartido; la llamada a la bandera en `template-parts/quote-form.php`) y se completó la integración:

- **`inc/helpers.php` — `ce_get_quote_cta_url()`:** nueva semántica — `integrated` y `modal` devuelven ambos `#ce-quote-modal`; solo `disabled` devuelve `''`. **Ninguno de los 7 puntos de CTA necesitó tocarse** (ya llamaban a esta función desde UX-3.1).
- **`footer.php`:** el modal se imprime siempre que el modo no sea `disabled` (antes, solo en `modal`).
- **`template-parts/quote-form.php`:** el `<form>` del modal recibe `id="ce-quote-form-modal"` (y sus campos internos, sufijo `-modal`; los `name=` nunca se tocan) únicamente cuando ya existe una instancia integrada impresa en la misma petición (bandera de UX-3.1/sesión anterior) — evita IDs duplicados cuando ambas coexisten. Guarda de supresión de la instancia normal extendida para cubrir también `disabled` (gap detectado en esta continuación: sin esto, `single-servicio.php`/`single-proyecto.php` habrían seguido mostrando un formulario funcional con la cotización desactivada).
- **`assets/js/main.js` — `ModuleQuoteForm`:** refactorizado de singleton (`#ce-quote-form` fijo) a localizador por clase (`.ce-quote-form-instance`), instanciando un controlador independiente por cada formulario encontrado — necesario porque ahora puede haber hasta 2 instancias funcionales simultáneas en la misma página.
- **`inc/customizer.php`:** etiquetas del control `ce_quote_form_mode` actualizadas a la semántica real ("Integrado + Popup" / "Solo Popup" / "Desactivado").

Ver `DECISIONS.md` D-053 para el detalle completo, incluido el diagnóstico línea por línea de qué parte de la sesión anterior era correcta y qué faltaba.

**Comportamiento final (reemplaza la sección "Comportamiento" de arriba, ya desactualizada tras esta corrección):**
- **`integrated`:** formulario integrado visible donde ya se mostraba (Home Builder / single-servicio.php / single-proyecto.php) **y** los 7 CTA abren el popup. Si coexisten en la misma página, el popup usa `id="ce-quote-form-modal"` para evitar colisión.
- **`modal`:** ninguna página muestra el formulario integrado; los 7 CTA abren el popup, siempre con `id="ce-quote-form"` (nunca hay colisión posible).
- **`disabled`:** ni el formulario integrado ni el popup se imprimen en ningún punto; los 7 CTA se ocultan.

### Verificaciones ejecutadas (continuación)
- Balance de llaves/paréntesis (Python) en los 4 archivos PHP re-tocados: `inc/helpers.php` (36/36, 165/165), `inc/customizer.php` (41/41, 284/284), `footer.php` (2/2, 97/97), `template-parts/quote-form.php` (4/4, 96/96) — todos balanceados.
- `node --check assets/js/main.js` → sin errores.
- Confirmado (`grep`) que no queda ningún `id="ce_..."` sin sufijar condicionalmente en `template-parts/quote-form.php`.
- Confirmado (diff) que `header.php`, `template-parts/hero.php`, `template-parts/cta.php`, `template-parts/sidebar-servicios.php`, `template-parts/sidebar-proyectos.php`, `style.css`, `inc/enqueue.php`, `inc/home-builder.php`, `front-page.php`, `inc/quote-form.php` **no cambiaron** en esta continuación.
- Trazado lógico manual (no hay entorno WordPress real disponible) de los 10 escenarios de validación solicitados: integrated+formulario integrado, integrated+CTA→popup, modal+popup, Servicios→popup, Proyectos→popup, página sin formulario integrado→popup, ausencia de IDs duplicados en todas las combinaciones, nonce/AJAX sin cambios, `ModuleQuoteForm` multi-instancia, labels de CTA intactos (verificado por `grep` que ningún texto de botón se tocó) — los 10 confirmados correctos por trazado de código.
- Ningún archivo del Sprint 8 tocado (verificado por diff).

---

## Sprint UX-4, Entregable UX-4.1: Hero configurable (imagen/video + overlay) — sin bump de `style.css`

**Módulo:** Hero configurable — modos imagen/video, overlay ajustable
**Estado:** Entregado — pendiente de aprobación explícita del usuario (ver `DECISIONS.md` D-038)

> **Nota de versionado:** siguiendo tu instrucción explícita, **no se aplicó ningún bump a `style.css`** (permanece en `Version: 0.8.5`). Versión propuesta para cuando confirmes: **`0.8.6`** (3 archivos con cambio funcional real). Indícamelo cuando quieras aplicarlo.

### Añadido
- **`inc/customizer.php`:** 3 controles nuevos dentro de la sección ya existente "CE: Sección Hero" — `ce_hero_type` (select imagen/video), `ce_hero_video` (media control, solo video), `ce_hero_overlay_opacity` (number, 0–1). 2 `sanitize_callback` nuevos: `ce_construction_sanitize_hero_type()`, `ce_construction_sanitize_hero_overlay_opacity()` (esta última acota estrictamente el valor en servidor, no confía en los `input_attrs` del navegador).
- **`assets/css/main.css`** (sección 26, 100% aditiva): `.ce-hero__video` (capa de fondo alternativa a `background-image`) y la propiedad `opacity` en `.ce-hero__overlay` (consumida vía custom property `--ce-hero-overlay-opacity`, sin tocar el `background`/gradiente original de esa clase).

### Cambiado
- **`template-parts/hero.php`:** rama condicional que imprime `<video autoplay muted loop playsinline>` cuando `ce_hero_type === 'video'` y hay un video subido; en caso contrario (incluido "tipo video sin video subido"), usa la imagen de fondo tal como siempre. El overlay imprime su opacidad configurada como custom property inline. **El bloque de cálculo de `$btn1_url`/`$btn2_url` (incluido el fix de D-050) no se tocó** — verificado línea por línea.

### Sin cambios
- **`inc/home-builder.php`, `front-page.php`, `functions.php`, `header.php`, `template-parts/cta.php`, `template-parts/sidebar-servicios.php`, `template-parts/sidebar-proyectos.php`, `footer.php`, `template-parts/quote-form.php`, `inc/quote-form.php`, `inc/helpers.php`, `assets/js/main.js`, `style.css`: sin cambios en este Entregable** — verificado por diff. Este Entregable no toca nada del sistema de cotización (Sprint UX-3) ni del Home Builder (Sprint UX-1/UX-2).
- Ningún archivo del Sprint 8.

### Comportamiento
- **Tipo `image` (por defecto):** idéntico al comportamiento anterior a este Entregable — overlay a intensidad completa (`opacity: 1`, mismo valor por defecto de `ce_hero_overlay_opacity`), sin `<video>` en el DOM. Cero regresión visual.
- **Tipo `video` con video subido:** el `<video>` reemplaza la imagen como fondo; el overlay sigue funcionando igual (encima del video). Título/CTA siguen legibles con el overlay al valor configurado.
- **Tipo `video` sin video subido:** fallback automático a imagen (o al color sólido de respaldo de `.ce-hero` si tampoco hay imagen) — nunca se imprime un `<video>` sin fuente.
- **Overlay:** ajustable de `0` (fondo a la vista, sin oscurecer) a `1` (como hasta ahora) desde el Customizer, sin editar CSS.

### Decisiones clave
- Ver `DECISIONS.md`: D-054.

### Verificaciones ejecutadas
- **Sintaxis PHP:** balance de llaves/paréntesis (Python) en `inc/customizer.php` (45/45, 326/326) y `template-parts/hero.php` (2/2, 57/57) — ambos balanceados.
- **Sintaxis CSS:** balance de llaves (Python) en `assets/css/main.css` tras la sección 26 nueva — sin diferencia.
- **Alcance del diff:** confirmado (`diff -rq`) que únicamente 3 archivos cambiaron: `inc/customizer.php`, `template-parts/hero.php`, `assets/css/main.css`. Cero archivos nuevos o eliminados.
- **Preservación de D-050:** confirmado por lectura que el bloque `$btn1_url = get_theme_mod( 'ce_hero_btn1_url', '' ); if ( '' === $btn1_url ) { ... }` permanece exactamente igual, sin ninguna línea modificada.
- **Preservación del sistema de cotización:** confirmado (diff) que `footer.php`, `template-parts/quote-form.php`, `inc/quote-form.php`, `inc/helpers.php` y `assets/js/main.js` no cambiaron — este Entregable no interactúa con la investigación pendiente del HTTP 400 (ver nota en `CURRENT_UX_SPRINT.md`).
- **Regresión sobre Sprint 8:** confirmado (diff) que ningún archivo del Sprint 8 fue tocado.

---

## Sprint UX-4, Entregable UX-4.2: Hero configurable — modo slider — sin bump de `style.css`

**Módulo:** Hero configurable — modo slider (varias imágenes, autoplay)
**Estado:** Entregado — pendiente de aprobación explícita del usuario (ver `DECISIONS.md` D-038)

> **Nota de versionado:** siguiendo tu instrucción explícita, **no se aplicó ningún bump a `style.css`** (permanece en `Version: 0.8.5`). Versión propuesta, arrastrada desde UX-3.1/UX-4.1 y sin cambios: **`0.8.6`**. Indícamelo cuando quieras aplicarlo.

### Retomado de la sesión anterior (sin descartar)
Se partió de 4 archivos ya trabajados en una sesión previa (`inc/customizer.php`, `assets/js/admin-hero-slides.js`, `inc/enqueue.php`, `assets/js/main.js`) y se completó lo que faltaba para que el Entregable quedara funcional de extremo a extremo:
- **`inc/helpers.php` — `ce_get_hero_slide_ids()`:** no existía todavía pese a que `inc/customizer.php` ya la invocaba (`ce_construction_sanitize_hero_slides()` y `CE_Customize_Hero_Slides_Control::render_content()`) — sin ella el control y el sanitize callback habrían producido un fatal error. Creada siguiendo el mismo criterio que `ce_get_gallery_ids()`.
- **`assets/js/main.js` — `ModuleHeroSlider.init();`:** el módulo estaba definido (`createSliderController()` + `ModuleHeroSlider`) pero nunca se registraba en el bootstrap de inicialización — el slider no se habría activado nunca en el navegador. Añadida la línea que faltaba junto al resto de `Module*.init()`.
- **`template-parts/hero.php`:** no tenía ningún cambio de la sesión anterior — la rama del modo slider no existía. Añadida en este cierre.
- **`assets/css/main.css`:** sección 27 (slider del Hero) no existía — añadida en este cierre.

### Añadido
- **`inc/customizer.php`** (ya recibido de la sesión anterior, verificado y conservado sin cambios): tercer valor `slider` en `ce_hero_type`; nuevo setting+control `ce_hero_slides` (`CE_Customize_Hero_Slides_Control`, selector múltiple de imágenes con miniaturas, añadir/quitar/reordenar); `ce_construction_sanitize_hero_slides()`; estilos admin del control (`customize_controls_print_styles`).
- **`assets/js/admin-hero-slides.js`** (ya recibido de la sesión anterior, verificado y conservado sin cambios): inicialización del control custom — añadir imágenes vía `wp.media`, quitar, reordenar con botones ↑/↓, serialización a IDs separados por comas.
- **`inc/enqueue.php`** (ya recibido de la sesión anterior, verificado y conservado sin cambios): `ce_construction_enqueue_hero_slides_control_script()`, encolado en `customize_controls_enqueue_scripts`.
- **`inc/helpers.php`** (nuevo, este cierre): `ce_get_hero_slide_ids()` — parsea/sanea la cadena de IDs de `ce_hero_slides`, misma fuente de verdad para el sanitize callback, el preview del control y el frontend.
- **`assets/js/main.js`** (completado, este cierre): `createSliderController()` — fábrica compartida extraída de `ModuleTestimonialSlider` (refactorizado para usarla, sin cambio de comportamiento); `ModuleHeroSlider` (nuevo, sin dots/flechas/swipe/pausa-en-hover — fondo decorativo), ahora sí registrado en el bootstrap.
- **`assets/css/main.css`** (sección 27, 100% aditiva, este cierre): `.ce-hero-slider`/`.ce-hero-slider__track`/`.ce-hero-slider__slide` — mismo patrón de capa que `.ce-hero__video` (sección 26).

### Cambiado
- **`template-parts/hero.php`** (este cierre): cómputo de `$hero_slide_urls`/`$hero_is_slider` (con fallback silencioso a imagen si `ce_hero_slides` está vacío, mismo criterio que el modo video de D-054); nueva rama `elseif ( $hero_is_slider )` que imprime el track de slides; la condición de `background-image` inline del `<section>` ahora también excluye el modo slider. **El bloque de cálculo de `$btn1_url`/`$btn2_url` (incluido el fix de D-050) no se tocó** — verificado línea por línea.

### Sin cambios
- **`inc/home-builder.php`, `front-page.php`, `functions.php`, `header.php`, `template-parts/cta.php`, `template-parts/sidebar-servicios.php`, `template-parts/sidebar-proyectos.php`, `footer.php`, `template-parts/quote-form.php`, `inc/quote-form.php`, `style.css`: sin cambios en este Entregable** — verificado por diff. `ModuleTestimonialSlider` conserva exactamente el mismo comportamiento observable (selectores, `dotLabel`, `defaultDelay`) pese a su refactor interno.
- Ningún archivo del Sprint 8.

### Comportamiento
- **Tipo `slider` con 1 o más imágenes:** las imágenes rotan automáticamente de fondo (autoplay 6s, igual que el slider de Testimonios), sin dots ni flechas (es un fondo decorativo). El overlay configurable (UX-4.1) sigue funcionando igual, encima del slider.
- **Tipo `slider` sin ninguna imagen configurada:** fallback automático a imagen de fondo normal (o color sólido de respaldo de `.ce-hero`) — nunca se imprime un slider vacío.
- **Coexistencia con el slider de Testimonios en la misma página (Home):** ambos operan de forma aislada (selectores y estado propios), sin colisión.

### Decisiones clave
- Ver `DECISIONS.md`: D-055.

### Verificaciones ejecutadas
- **Sintaxis PHP:** balance de llaves/paréntesis (Python) en `inc/customizer.php` (58/58, 384/384), `inc/helpers.php` (38/38, 187/187) y `template-parts/hero.php` (4/4, 80/80) — todos balanceados.
- **Sintaxis JS:** `node --check assets/js/main.js` → sin errores.
- **Sintaxis CSS:** balance de llaves (Python) en `assets/css/main.css` tras la sección 27 nueva (372/372) — sin diferencia respecto al total esperado.
- **Alcance del diff:** confirmado que, de los 4 archivos recibidos de la sesión anterior, `inc/customizer.php`, `assets/js/admin-hero-slides.js` e `inc/enqueue.php` se conservan **exactamente iguales** (verificado por diff/hash) — el único de los 4 con trabajo añadido fue `assets/js/main.js` (la línea de registro en el bootstrap).
- **Preservación de UX-4.1 (imagen/video):** confirmado por lectura que las ramas `image`/`video` de `template-parts/hero.php` y sus theme_mods (`ce_hero_video`, `ce_hero_overlay_opacity`) no cambiaron de comportamiento — la única condición tocada fue la de `background-image` inline, ampliada para excluir también el nuevo modo slider.
- **Preservación del sistema de cotización y del Sprint 8:** confirmado (diff) que `footer.php`, `template-parts/quote-form.php`, `inc/quote-form.php` y todos los archivos del Sprint 8 no cambiaron.
- Trazado lógico manual (no hay entorno WordPress real disponible) del flujo completo: añadir imágenes en el control → guardar → `ce_hero_type = slider` → render del track → autoplay — confirmado correcto por lectura de código. Misma limitación metodológica ya documentada en `CURRENT_UX_SPRINT.md` (sin navegador real disponible en este entorno).

---

## Sprint UX-5, Entregable UX-5.1: CTA secundario reutilizable — sin bump de `style.css`

**Estado:** Entregado — pendiente de aprobación explícita (ver `DECISIONS.md` D-038)

### Decisión de diseño (Quote Form corto)
El usuario decidió explícitamente **no** crear una variante `short` ni un segundo formulario. `template-parts/quote-form.php` e `inc/quote-form.php` **no se tocaron** en este Entregable. Ver `DECISIONS.md` D-056.

### Añadido/Cambiado
- `inc/home-builder.php`: nueva entrada `cta_secondary` en el registro (reutiliza `template-parts/cta.php`, no un archivo nuevo).
- `front-page.php`: 1 línea condicional — pasa `$args['variant']='secondary'` solo para `cta_secondary`.
- `template-parts/cta.php`: lee `$args['variant']`, usa prefijo `ce_cta_`/`ce_cta2_` según corresponda; fix D-050 preservado y parametrizado para ambas variantes.
- `inc/customizer.php`: nueva sección "CE: CTA Secundario" (4 campos, mismo patrón que la sección CTA ya existente).

### Sin cambios
- Sistema de cotización completo (`inc/quote-form.php`, `template-parts/quote-form.php`, `inc/helpers.php`, `footer.php`, `assets/js/main.js`), Hero (UX-4), Sprint 8, `style.css`.

### Verificaciones
- Balance de llaves/paréntesis OK en los 4 archivos tocados (Python).
- Diff confirma exactamente 4 archivos modificados, cero nuevos, todo lo demás intacto.
- Sin colisión de prioridad de sección en el Customizer (29/30/31/32/33/34/35/35/36/37 — CTA secundaria comparte 35 con CTA primaria, adyacentes por orden de registro).

---

## Sprint UX-6, Entregable UX-6.1: `page.php` — hero interno + `the_content()` para Páginas — sin bump de `style.css`

**Estado:** ✅ Aprobado (D-062). Entrada diferida hasta el cierre formal del Sprint UX-6 (mismo criterio ya usado en UX-6.1/UX-6.2 al momento de su entrega — ver `DECISIONS.md` D-034).

### Añadido
- `page.php` (nuevo, raíz del tema): Template Hierarchy nativa de WordPress — cualquier Página (`post_type=page`) deja de caer en `index.php`. Reutiliza `template-parts/page-hero.php` (eyebrow genérico vía `get_bloginfo('name')`, mismo criterio que `archive.php`) + `the_content()`, con soporte de Página protegida por contraseña, paginación de contenido y comentarios vía `comments.php` (ya genérico desde 6B.2).

### Sin cambios
- `template-parts/page-hero.php`, `comments.php`, `index.php`, Home Builder (UX-1/UX-2), sistema de cotización (UX-3), Hero de Home (UX-4), CTA (UX-5), Sprint 8.

### Decisiones clave
- Ver `DECISIONS.md`: D-059.

---

## Sprint UX-6, Entregable UX-6.2: shortcode `[ce_section key="..."]` — sin bump de `style.css`

**Estado:** ✅ Aprobado (D-062), mecanismo confirmado explícitamente por el usuario frente a la alternativa de bloque Gutenberg. Entrada diferida hasta el cierre formal del Sprint UX-6.

### Añadido
- `inc/section-shortcode.php` (nuevo): registra `[ce_section key="..."]`, resuelve `key` contra `ce_construction_home_sections()` y delega en `get_template_part( ..., null, $args )` — mismo mecanismo que `front-page.php`, sin reimplementarlo. No depende de `ce_construction_get_active_home_order()`: renderiza la sección aunque esté desactivada en el Home Builder. Guardas: `key` vacía/inexistente → cadena vacía; límite defensivo de anidamiento (3 niveles); `sanitize_key()` sobre el atributo.

### Cambiado
- `inc/home-builder.php`: nueva función `ce_construction_get_home_section_args()` (aditiva) — extrae la única lógica de `$args` no trivial que tenía `front-page.php` (`cta_secondary` → `variant=secondary`, D-056), para que el shortcode no la reimplemente ni quede desincronizada.
- `front-page.php`: 1 línea — usa `ce_construction_get_home_section_args()` en vez del ternario inline, sin cambio de comportamiento.
- `functions.php`: 1 línea nueva en el array `$modules` para cargar `inc/section-shortcode.php`.

### Sin cambios
- `page.php` (UX-6.1, no tocado), `template-parts/page-hero.php`, `comments.php`, sistema de cotización (UX-3), Hero de Home (UX-4), CTA (UX-5), Sprint 8. `style.css` sin bump.

### Decisiones clave
- Ver `DECISIONS.md`: D-060.

### Verificaciones ejecutadas
- Sin entorno WordPress/PHP real disponible: balance de llaves/paréntesis y de tags `<?php` verificado en los 4 archivos tocados/creados; trazado lógico manual del flujo del shortcode contra `front-page.php`. **Pendiente de prueba funcional real en WordPress** (crear una Página con `[ce_section key="about"]` y confirmar que renderiza igual que en el Home, incluso con la sección desactivada en el Home Builder).

---

## Sprint UX-6, Entregable UX-6.3: `.ce-content-breakout` — fix de ancho de `[ce_section]` en `single.php`/`page.php` — sin bump de `style.css`

**Estado:** ✅ Aprobado (D-062). Entrada diferida hasta el cierre formal del Sprint UX-6.

### Bug corregido
`[ce_section key="..."]` embebido en una Página o entrada se encogía a 640px con grandes espacios laterales, en vez de ocupar el ancho completo del sitio como en el Home — causado por `.ce-max-w-content` (`max-width:640px`), que envolvía todo `the_content()` en `single.php`/`page.php`. Confirmado por el usuario en DevTools (WordPress real) antes de implementar el fix.

### Añadido
- `assets/css/main.css`: nueva clase `.ce-content-breakout` (100% aditiva) — convierte el wrapper en un grid de 3 columnas: el contenido normal de `the_content()` sigue centrado a `min(640px, 100%)` (mismo ancho de lectura de siempre); los hijos directos `.ce-section` ocupan el ancho completo del `.ce-container`, cancelando con margen negativo el padding del contenedor exterior. `.ce-max-w-content` (compartida en ~15 archivos del proyecto) **no se modificó**.

### Cambiado
- `single.php`, `page.php`: restructuración de wrappers — el `<article>` se sacó de `.ce-max-w-content` y se le añadió `ce-content-breakout` junto a `.ce-service-content`; la meta info y los tags/paginación/comentarios siguen envueltos en `.ce-max-w-content`, sin cambio visual.

### Sin cambios
- `inc/section-shortcode.php` (el shortcode nunca fue la causa del bug), `single-servicio.php`/`single-proyecto.php`/`single-equipo.php` (comparten `.ce-service-content` pero quedan fuera de alcance de este bug), `template-parts/page-hero.php`, `front-page.php`, Sprint 8. `style.css` sin bump.

### Decisiones clave
- Ver `DECISIONS.md`: D-061.

### Verificaciones ejecutadas
- Causa confirmada por el usuario en DevTools sobre WordPress real antes de implementar. Balance de llaves/paréntesis/tags `<?php` verificado en `single.php`/`page.php` tras la restructuración; balance de llaves/paréntesis verificado en `main.css` completo tras la inserción. **Pendiente de prueba funcional real en WordPress** con caché purgada (confirmar ancho completo del `.ce-container` y que el resto del texto conserva el ancho de lectura de siempre).

---

## Sprint UX-6 — Cierre formal (UX-6.1 + UX-6.2 + UX-6.3)

**Estado:** ✅ **Completado y aprobado.** Los tres Entregables aprobados explícitamente por el usuario en la misma sesión (ver `DECISIONS.md` D-062). Sin cambios de código en este cierre — puramente documental (actualización de este archivo, `TREE.md` y `DECISIONS.md`, diferida hasta este punto conforme a D-034).

---

## Sprint UX-7 — "Consistencia y configurabilidad: Hero, CTA, Header/Footer" — Cierre formal (UX-7.1 a UX-7.10)

**Estado:** ✅ **CERRADO Y APROBADO.** Los 10 Entregables aprobados explícitamente por el usuario (UX-7.10 en la sesión de cierre de esta fase). Entrada consolidada a nivel de Sprint (detalle técnico completo de cada Entregable ya documentado individualmente en `docs/DECISIONS.md` D-063 a D-081 y en `docs/CURRENT_UX_SPRINT.md`), añadida en esta sesión conforme al criterio D-034 (se difería a un punto de cierre de Sprint más significativo; este es ese punto).

### Añadido
- **UX-7.1** — Unificación del Hero (Home + interior): `ce_construction_get_hero_media_state()` compartida entre `hero.php`/`page-hero.php`; Hero interno gana video/slider y overlay de 3 stops compartido con opacidad configurable (D-063).
- **UX-7.2** — Layout de Hero en 1/2/3 columnas + Quote Form embebido (`ce_hero_layout`, `ce_hero_show_quote_form`); tercer contexto `'hero'` en `quote-form.php` (D-064). Ajuste puntual: breakpoint intermedio, `.ce-hero--has-form`, badge "Respuesta en 24h" (D-065).
- **UX-7.3** — Aprovechamiento de espacios vacíos en sidebars de Servicios/Proyectos (D-067).
- **UX-7.4** — Icono y color de botón configurables para el CTA (D-068).
- **UX-7.5** — Logo independiente del Footer (`ce_footer_logo`, con fallback automático al logo del sitio) vía `ce_render_footer_logo()` (D-069).
- **UX-7.6** — Estadísticas configurables desde el Customizer, repeater con control custom (`assets/js/admin-stats-items.js`) (D-070).
- **UX-7.7** — Franja de Insignias de Confianza/Licencias (`template-parts/trust-badges.php`, repeater con imagen opcional vía `wp.media`), con modo compacto integrado en la tarjeta del Quote Form del Hero (D-071).
- **UX-7.8** — Testimonio en video (adjunto local o URL externa vía `wp_oembed_get()`), extensión retrocompatible de `ModuleLightbox` para reproducir video sin crear un lightbox nuevo (D-077).
- **UX-7.9** — Bloque de Financiamiento / opciones de pago (`template-parts/financing.php`, mismo patrón de campos que `cta_secondary`) (D-078).
- **UX-7.10** — Popup de Oferta / captura de leads: overlay independiente del Quote Form, temporizador configurable, 2 cookies de navegador (supresión corta/larga), refuerzo visual (icono, insignia, colores de oferta, `prefers-reduced-motion`) y efecto de movimiento vía JS (rebote de entrada + nudge acotado del icono, sin sobrecargar) (D-079, D-080, D-081).

### Sin cambios
- `inc/quote-form.php` (handler AJAX) — protegido explícitamente en todo el Sprint UX-7.
- Ningún archivo del Sprint 8 — verificado Entregable por Entregable durante toda la fase.

### Decisiones clave
- Ver `DECISIONS.md`: D-058 (benchmark competitivo y propuesta del Sprint), D-063 a D-071 y D-077 a D-081.

### Documentación actualizada
- `docs/TREE.md`, `docs/CURRENT_UX_SPRINT.md` (bloque "ESTADO FINAL"), `docs/TODO.md`, `docs/PROJECT_STATUS.md` — todos en esta misma sesión de cierre.

---

## Sprint UX-10 — "Página de Testimonios: CPT propio + Google Reviews" — Cierre formal (UX-10.1 a UX-10.3)

**Estado:** ✅ **COMPLETADO Y APROBADO.** Entrada consolidada a nivel de Sprint (detalle técnico completo ya documentado en `docs/DECISIONS.md` D-072 a D-076 y en `docs/CURRENT_UX_SPRINT.md`).

### Añadido
- **UX-10.1 + UX-10.2** — Página dedicada de Testimonios vía `page.php` + `[ce_section key="testimonials_full"]` (`template-parts/testimonials-full.php`: grid + paginación, solo CPT propio `testimonio`, reutiliza `content-testimonio-card.php` sin cambios); CTA "Ver todos los testimonios" en el teaser del Home (D-073).
- **UX-10.3** — Google Reviews vía Trustindex, incorporado como sección independiente del Home Builder (`template-parts/google-reviews.php`, campo `textarea` saneado con `wp_kses()`), embebido tal cual, sin normalización hacia `content-testimonio-card.php` — corrección de la premisa funcional original tras confirmarse que Trustindex no ofrece API pública (D-074, D-075, D-076).
- **UX-10.4** — sin Entregable propio: la combinatoria "solo CPT / solo Trustindex / ambos / ninguno" queda absorbida por el Home Builder existente (activar/desactivar/reordenar `testimonials` y `google_reviews` de forma independiente).

### Sin cambios
- `inc/cpt-testimonios.php`, `template-parts/content-testimonio-card.php` — ninguno de los 3 archivos señalados como conflicto de secuencia con UX-7.8 (D-072) fue modificado por UX-10.
- Ningún archivo del Sprint 8.

### Decisiones clave
- Ver `DECISIONS.md`: D-072 a D-076.

### Documentación actualizada
- `docs/TREE.md`, `docs/CURRENT_UX_SPRINT.md`, `docs/TODO.md`, `docs/PROJECT_STATUS.md` — todos en esta sesión de cierre (la de UX-7, ya que UX-10 se aprobó antes pero también tenía esta actualización diferida).

---

## 🎉 FASE "OPTIMIZACIÓN UX / CONVERSIÓN" — Sprints UX-1 a UX-7 y UX-10, CERRADOS

Con el cierre de UX-7 y UX-10, la fase paralela iniciada junto al Sprint 8 no tiene ningún Sprint ni Entregable UX **obligatorio** pendiente. Quedan como backlog no bloqueante, sin iniciar: UX-5.2 (documentación de "objetivo de plantilla"), Sprint UX-8 ("Video en Proyectos", propuesto/futuro) y Sprint UX-9 (registro documental de Responsive). Un hallazgo de QA (`.ce-header__social` sin estilo base) detectado durante la auditoría UX se registró formalmente como **QA-043** en `docs/QA_REPORT.md`, derivado al Sprint 8 (no es parte de esta fase). El **Sprint 8** ("Cierre de Hallazgos QA") permanece pausado, sin cerrarse, en el Entregable 8.2 (QA-030) — ver `docs/CURRENT_SPRINT.md` para su estado reconstruido.

---

## Sprint UX-11 — "Hero, Formulario del Hero y Header" — Completado y aprobado (Entregable único)

**Estado:** ✅ **COMPLETADO Y APROBADO.** Precedido de una fase de análisis/planificación explícitamente separada de la implementación (a pedido del usuario): primero se entregó un plan técnico/UX de 9 puntos sin tocar código, luego el usuario aprobó el plan completo con decisiones específicas por punto, y solo entonces se implementó.

### Añadido / Corregido
- **Punto 1 — Formulario del Hero:** corregido el recorte del botón "Enviar Solicitud" (causa real: `.ce-hero-quote-card` heredaba de `.ce-card` tanto `overflow: hidden` como `height: 100%`, combinado con el `overflow: hidden` de `.ce-hero` y el `align-items: center` de `.ce-hero__layout`). Panel rediseñado como componente visualmente separado del fondo (fondo translúcido + `backdrop-filter`, sombra `--ce-shadow-lg` consistente, más padding/gap) — inspirado en concepto en la referencia proporcionada por el usuario, sin copiarla. Ningún cambio en las instancias normal/modal del formulario.
- **Punto 2 — Altura del Hero:** reducción ~15% (`clamp(480px, 75vh, 760px)` base, `clamp(560px, 78vh, 820px)` en `xl`) — sin un `min-height` en píxeles que anulara la reducción en viewports habituales.
- **Punto 3 — Hero interno vs. Home:** `template-parts/page-hero.php` reescrito — ya no comparte el modo video/slider global del Home (modifica explícitamente D-063, no una reversión silenciosa); usa siempre la imagen destacada del post/página, con fallback a la imagen del Home solo si no existe.
- **Punto 4 — Imágenes de Hero:** verificado que WordPress no ofrece punto focal nativo para imágenes destacadas/adjuntos. Alternativa implementada, nativa y sin plugin: campo "Posición en Heroes" (9 posiciones predefinidas) en la pantalla clásica de edición de adjuntos, nuevo archivo `inc/hero-image-position.php`.
- **Punto 5 — Overlay configurable:** 4 controles en el Customizer (color, dirección, extensión, e intensidad ya existente) — nueva función `ce_construction_get_hero_overlay_gradient_css()` (`inc/helpers.php`). Valores por defecto matemáticamente equivalentes al gradiente fijo anterior — sin regresión visual.
- **Punto 6 — Header (QA-043):** espaciado/tamaño de `.ce-header__social` en la barra superior del header, incorporado a este Sprint por decisión explícita del usuario.

### Archivos modificados
`template-parts/hero.php`, `template-parts/page-hero.php` (reescrito), `inc/helpers.php`, `inc/customizer.php`, `assets/css/main.css`, `functions.php` (registro del nuevo módulo).

### Archivos nuevos
`inc/hero-image-position.php`.

### Sin cambios
`inc/quote-form.php`, cualquier instancia normal/modal del formulario de cotización (ver también el ajuste aditivo de más abajo).

### Decisiones clave
Ver `DECISIONS.md`: D-083 a D-090.

### Documentación actualizada
`docs/CURRENT_UX_SPRINT.md`, `docs/QA_REPORT.md` (QA-043 cerrado), `docs/TODO.md`, `docs/PROJECT_STATUS.md`, `docs/TREE.md`.

---

### 🆕 Ajuste puntual dentro de UX-11 (posterior al cierre de arriba): expansión progresiva del formulario del Hero

**Alcance:** exclusivamente la instancia `'hero'` de `template-parts/quote-form.php` (Punto 1 de este mismo Sprint). Sin ningún cambio en las instancias normal (`single-servicio.php`/`single-proyecto.php`) ni modal (`footer.php`).

- El formulario del Hero se muestra inicialmente colapsado hasta el campo "Servicio requerido" (Nombre, Correo, Teléfono, Empresa, Servicio requerido siempre visibles). El resto (Mensaje, Adjuntar archivo, botón "Enviar Solicitud") se revela con una expansión suave de altura al enfocar (`focus`, sin necesidad de escribir) cualquiera de los campos visibles.
- **Progressive enhancement obligatorio:** sin JavaScript (o si falla), el formulario se muestra completo desde el inicio — el colapso lo aplica únicamente el nuevo módulo JS, nunca el HTML/CSS de origen.
- Nuevo módulo independiente `ModuleHeroFormProgressive` (`assets/js/main.js`), desacoplado de `ModuleQuoteForm` (mismo criterio de separación ya usado por `ModuleOfferPopup`, D-079) — no se tocó la validación/envío/AJAX existente.
- El campo "Servicio requerido" sigue siendo obligatorio y validado igual (cliente y servidor), esté colapsado o expandido.
- **Archivo tocado de "quote-form":** únicamente `template-parts/quote-form.php` (el markup), y solo dentro de la rama `$ce_quote_is_hero` — nuevo `<div class="ce-hero-quote-form__extra">` envolviendo Mensaje/Adjuntar archivo/botón. `inc/quote-form.php` (handler AJAX) **no se tocó**.
- **Archivos modificados:** `template-parts/quote-form.php`, `assets/js/main.js` (nuevo módulo `ModuleHeroFormProgressive` + registro en el bootstrap), `assets/css/main.css` (sección 28 bis, punto 6 nuevo, aditivo).
- **Decisión clave:** ver `DECISIONS.md` D-091.

---

### 🆕 Ajuste puntual dentro de UX-11 (continuación): opacidad configurable del panel + recompactar al salir del formulario

**Alcance:** exclusivamente la instancia `'hero'` de `template-parts/quote-form.php`, misma que el ajuste anterior. Sin ningún cambio en las instancias normal ni modal.

- **Opacidad configurable del panel (D-092):** nuevo control en el Customizer ("CE: Sección Hero" → "Opacidad del panel del Formulario de Cotización (Hero)"), rango 0–1, mismo mecanismo que "Intensidad del overlay oscuro". Por defecto `0.97` — idéntico al aspecto anterior. Se aplica vía variable CSS `--ce-hero-quote-card-opacity`, impresa en línea por `template-parts/quote-form.php` (contexto `'hero'` únicamente), consumida por `assets/css/main.css` con el valor anterior como *fallback* (cero regresión).
- **Recompactar al salir del formulario (D-093):** el formulario ahora también se vuelve a compactar automáticamente al salir por completo de él (sin haber enviado), y se expande de nuevo al reingresar — puede repetirse indefinidamente. Corrige además, de paso, un pequeño parpadeo visual que tenía el colapso inicial al cargar la página.
- **Archivos modificados:** `inc/customizer.php` (1 setting + 1 control + 1 sanitize_callback nuevos), `template-parts/quote-form.php` (atributo `style` con la variable CSS, dentro de la rama `'hero'`), `assets/css/main.css` (1 línea de `background` cambiada a `var()` con fallback), `assets/js/main.js` (`ModuleHeroFormProgressive` ampliado: expandir/compactar repetible + corrección del parpadeo inicial).
- **Decisiones clave:** ver `DECISIONS.md` D-092, D-093.

---

## 🎉 Sprint UX-11 — CERRADO Y APROBADO EN SU TOTALIDAD

Con la aprobación explícita del usuario, quedan cerrados los 6 puntos base (D-083 a D-090) **y** los 3 ajustes puntuales posteriores (D-091: expansión progresiva del formulario del Hero; D-092: opacidad configurable del panel; D-093: recompactado al salir del formulario). No queda ningún punto del Sprint UX-11 pendiente de aprobación. Ver `docs/DECISIONS.md` D-094.

## Sprint 8 — Entregables 8.1 y 8.2 aprobados

El usuario confirmó explícitamente la aprobación del **Entregable 8.1** (QA-010, QA-011, QA-013 parcial, QA-014, QA-015, QA-017 — resolviendo una inconsistencia de estado entre documentos previos) y del **Entregable 8.2** (QA-030, cache-busting por `filemtime()`). El Sprint 8 se retoma en el **Entregable 8.3** (QA-031). Ver `docs/DECISIONS.md` D-095.

---

## Sprint 8, Entregable 8.3 — QA-031: adjuntos de cotización protegidos

**Estado:** ✅ Aprobado de forma definitiva. Alcance aprobado explícitamente antes de escribir código (`docs/DECISIONS.md` D-096); las 4 pruebas funcionales reales que quedaron pendientes de esa implementación fueron ejecutadas por el usuario con resultado exitoso en un entorno real (Apache/2.4.68 Debian, PHP 8.2.33), cerrando el ciclo de aprobación (`docs/DECISIONS.md` D-097).

### Añadido
- `inc/quote-attachments.php`: carpeta dedicada `wp-content/uploads/cotizaciones/` bloqueada a nivel de servidor (`.htaccess` + índice vacío, generados automáticamente); endpoint autenticado `admin_post_ce_download_quote_attachment` (sesión + capacidad + nonce + verificación de ruta); renombrado aleatorio del archivo físico al subirlo.

### Modificado
- `inc/quote-form.php`: la subida del adjunto ahora usa la carpeta protegida y el renombrado aleatorio; nueva columna "Adjunto" en el listado admin de Cotizaciones con enlace de descarga seguro (reemplaza cualquier exposición de la URL directa).
- `functions.php`: registro del nuevo módulo.

### Sin cambios
Cualquier template del formulario de cotización (normal, modal, Hero), `assets/js/main.js`, validación de tipo/tamaño de archivo, envío de correo, cron de retención.

### Decisiones clave
Ver `DECISIONS.md`: D-096 (implementación) y D-097 (aprobación final, tras pruebas funcionales reales).

---

## Sprint 8, Entregable 8.3 — Aprobación final (cierre)

El usuario ejecutó en un entorno real (Apache/2.4.68 Debian, PHP 8.2.33) las 4 pruebas funcionales listadas en `docs/DECISIONS.md` D-096, con resultado exitoso en las cuatro: envío del formulario con adjunto (cotización creada + correo enviado sin cambios), descarga del adjunto por un administrador autenticado desde el listado admin, bloqueo del enlace de descarga tras cerrar la sesión de administrador, y bloqueo del acceso directo a la URL física del archivo desde una ventana no autenticada. Con esto, **QA-031 / Entregable 8.3 queda cerrado y aprobado**, sin cambios de código adicionales. Ver `docs/DECISIONS.md` D-097.

---

## Sprint 8, Entregable 8.4 — QA-032, QA-033, QA-034: robustez del formulario de cotización

**Estado:** ✅ Aprobado explícitamente por el usuario, tras pruebas funcionales reales (ver `docs/DECISIONS.md` D-098, D-099, D-101).

### Añadido
- `inc/form-guards.php`: tabla propia `{prefix}ce_form_guards` con migración de esquema versionada (`CE_THEME_DB_VERSION`, enganchada a `admin_init` + `after_switch_theme`, sin coste en frontend); reclamación atómica de rate-limit por IP (HMAC con `wp_salt('auth')`, nunca la IP en texto plano); reclamación atómica de idempotencia con modelo de checkpoints; purga de filas vencidas reutilizando el cron diario ya existente de QA-003.

### Modificado
- `inc/quote-form.php`: rate-limiting (QA-032) migrado de `get_transient()`/`set_transient()` a una única sentencia SQL atómica; rollback del archivo subido + `error_log()` con prefijo `[CE Construction]` sin datos personales si falla `wp_insert_post()`/`wp_insert_attachment()` (QA-033); idempotencia server-side completa con checkpoints, sin depender de JS ni de las 3 instancias del formulario más allá de un campo oculto (QA-034); nuevo meta `_ce_name`; envío de correo extraído a `ce_construction_send_quote_email()`.

### Añadido/Modificado (integración final)
- `functions.php`: constante `CE_THEME_DB_VERSION` + registro de `inc/form-guards.php` (antes de `inc/quote-form.php`) en `ce_construction_require_modules()`.
- `template-parts/quote-form.php`: campo oculto `ce_idempotency_key` junto al nonce existente, dentro del bloque compartido por las 3 instancias.

### Pendiente antes de cerrar este Entregable
- Pruebas funcionales reales: envío normal (con y sin adjunto, 3 instancias), doble clic/doble pestaña (una sola cotización y un solo correo), más de 3 envíos en 10 min (bloqueo del cuarto).

### Decisiones clave
Ver `DECISIONS.md`: D-098, D-099.

---

## QA-044 — Corrección puntual: etiqueta visual del adjunto tras envío exitoso

**Estado:** ✅ Corregido y entregado.

### Corregido
- `assets/js/main.js` (`ModuleQuoteForm`): la etiqueta del campo de adjunto ya no se queda mostrando el último archivo seleccionado tras un envío exitoso — se sincroniza correctamente con `form.reset()`.

### Decisiones clave
Ver `docs/DECISIONS.md`: D-100.

---

## Sprint 8, Entregable 8.4 — Aprobación final (cierre)

El usuario confirmó que ejecutó las pruebas funcionales reales pendientes de D-098/D-099 (envío normal con y sin adjunto en las 3 instancias; doble clic/doble pestaña sobre el mismo envío; más de 3 envíos en menos de 10 minutos) y aprobó el Entregable 8.4 de forma definitiva. A diferencia del cierre del Entregable 8.3, esta sesión no incluyó el desglose caso por caso de cada prueba. Con esto, **QA-032, QA-033 y QA-034 / Entregable 8.4 quedan cerrados y aprobados**, sin cambios de código adicionales. Ver `docs/DECISIONS.md` D-101.

---

## Sprint 8, Entregable 8.5 — QA-012, QA-016, QA-035, QA-038

**Estado:** ✅ Aprobado explícitamente por el usuario, tras pruebas funcionales reales (ver `docs/DECISIONS.md` D-102, D-103).

### Corregido / añadido
- **QA-012** — `inc/helpers.php`: caché de transient (1h) por id+límite para `ce_get_related_services()`, `ce_get_related_projects()` y `ce_get_related_services_for_project()`, con invalidación activa vía contador de versión al guardar `servicio`/`proyecto` o editar `categoria_servicio`/`categoria_proyecto`.
- **QA-016** — `inc/meta-boxes.php`: script inline de `ce_render_proyecto_gallery()` eliminado. Nuevo `assets/js/admin-proyecto-gallery.js`, encolado condicionalmente en `inc/enqueue.php` (`post.php`/`post-new.php` del CPT `proyecto`) con `jquery`/`media-editor` como dependencia formal.
- **QA-035** — `assets/js/main.js`: `createSliderController()` gana un botón de pausa/reanudación accesible por teclado/touch y pausa automática por `focusin`/`focusout`, activado solo en `ModuleTestimonialSlider` (flag `pausable`). Nuevos textos `pauseSlider`/`resumeSlider` en `ceConstructionData.i18n` (`inc/enqueue.php`). Nuevo estilo `.ce-slider-pause` en `assets/css/main.css`.
- **QA-038** — `inc/seo.php`: nueva función `ce_construction_get_canonical_url()` (+ helper `ce_construction_apply_canonical_pagination()`), invocada desde `ce_construction_meta_tags()` para imprimir `<link rel="canonical">` correcto según el tipo de contenido/archivo, con paginación auto-referencial.

### Decisiones clave
Ver `docs/DECISIONS.md`: D-102.

---

## Sprint 8, Entregable 8.5 — Aprobación final (cierre)

El usuario confirmó que ejecutó las pruebas funcionales reales pendientes de D-102 y aprobó el Entregable 8.5 de forma definitiva, sin desglose caso por caso. Con esto, **QA-012, QA-016, QA-035 y QA-038 / Entregable 8.5 quedan cerrados y aprobados**, sin cambios de código adicionales. El usuario autorizó, en la misma sesión, iniciar el Entregable 8.6. Ver `docs/DECISIONS.md` D-103.

---

## Sprint 8, Entregable 8.6 — QA-036: gestión de foco centralizada para overlays

**Estado:** ✅ Aprobado explícitamente por el usuario, tras pruebas funcionales reales (ver `docs/DECISIONS.md` D-104, D-105).

**Decisión de diseño resuelta:** R-4 (`docs/QA_REPORT.md`) dejaba abierta la elección entre una utilidad de foco centralizada o una corrección aislada por componente. Se optó por la centralizada, tal como el propio R-4 recomendaba.

### Corregido / añadido
- **`assets/js/main.js`** — nueva utilidad compartida `FocusTrap` (`.activate(container, { trigger, initialFocus })` / `.deactivate(container)`), junto con un nuevo helper `off()` (pareja de `on()`, ausente hasta ahora).
- **`ModuleMobileNav`** — `open()`/`close()` ahora usan `FocusTrap` (trigger: botón hamburguesa, foco inicial: botón de cerrar del panel).
- **`ModuleModals`** — `open(id, options)` gana un parámetro opcional `{ trigger }`; `close(overlay)` desactiva el trap. Actualizados los 4 call sites con disparador de usuario identificable (ancla de `ModuleSmoothScroll`, y los 3 de `ModuleQuoteForm` con `this.submitBtn`). `ModuleOfferPopup` (se abre por temporizador, sin disparador real) queda sin trigger explícito a propósito.
- **`ModuleLightbox`** — retrofit no disruptivo: su lógica manual previa de foco (mover foco al abrir, devolverlo al cerrar) se migró a `FocusTrap`, que ahora también añade el trap de `Tab` que faltaba. Sin cambios en su navegación prev/next ni en el manejo de tipos de medio.

### No incluido en este Entregable
QA-037 (`aria-label` estático del botón de menú móvil) — hallazgo BAJO relacionado pero distinto, agrupado en el Entregable 8.7.

### Decisiones clave
Ver `docs/DECISIONS.md`: D-104.

---

## Sprint 8, Entregable 8.6 — Aprobación final (cierre)

El usuario confirmó que las 4 pruebas funcionales reales listadas en D-104 fueron exitosas (menú móvil, modal genérico, modal de éxito/error del formulario de cotización, lightbox de galería) y aprobó el Entregable 8.6 de forma definitiva, sin desglose caso por caso. Con esto, **QA-036 / Entregable 8.6 queda cerrado y aprobado**, sin cambios de código adicionales. Ver `docs/DECISIONS.md` D-105.
