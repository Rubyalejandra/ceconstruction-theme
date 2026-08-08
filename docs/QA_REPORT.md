# CE Construction — QA_REPORT.md
### Sprint de QA e Integración

> Este reporte es el resultado de una revisión exhaustiva y verificada línea por línea de todo el código generado hasta el Sprint 3 (Módulo Servicios). Ningún archivo fue modificado durante esta auditoría original. Cada hallazgo fue confirmado contra el código real en disco.

**Alcance auditado:** los 31 archivos PHP/CSS/JS del tema (Sprints 1-3).

**Metodología:** lectura completa de cada archivo, verificación de balance de llaves/paréntesis, `node --check` sobre JS, trazado manual de flujos de datos sensibles, y cálculo numérico de contraste de color (fórmula WCAG 2.x).

---

## Resumen ejecutivo

| Severidad | Cantidad de hallazgos | Estado |
|---|---|---|
| 🔴 Crítico | 1 | ✅ Corregido en v0.4.1 |
| 🟠 Alto | 8 | ✅ Corregidos en v0.4.1 |
| 🟡 Medio | 9 | 🟡 1 corregido (QA-018, v0.7.2, Sprint 7 Entregable 7.3) / 8 sin corregir |
| 🟢 Bajo | 5 | ⬜ Sin corregir (fuera de alcance) |
| 🔵 Mejora futura | 6 | ⬜ Sin implementar |
| **Total** | **29** | **10 corregidos / 19 pendientes** |

**Actualización (Sprint 5, Fase 1, v0.4.1):** los 9 hallazgos Críticos/Altos (QA-001 a QA-009) corregidos — ver `DECISIONS.md` D-017 a D-024. **Actualización (Sprint 7, Entregable 7.3, v0.7.2):** de los 9 hallazgos Medios, se corrigió únicamente **QA-018**, con aprobación explícita puntual del usuario para ese hallazgo — ver `DECISIONS.md` D-039. Los 8 hallazgos Medios restantes (QA-010 a QA-017), los 5 Bajos y las 6 Mejoras futuras siguen sin corrección.

---

## 🔴 CRÍTICO

### QA-001 — Bypass de validación de tipo de archivo en el formulario de cotización
- **Estado:** ✅ **CORREGIDO en v0.4.1** — ver `DECISIONS.md` D-017.
- **Archivo afectado:** `inc/quote-form.php`
- **Descripción:** La validación comparaba `$_FILES['attachment']['type']` (MIME falsificable del cliente) y `wp_check_filetype()` solo verificaba la lista global de WordPress, no el whitelist real (pdf/jpg/png/webp).
- **Recomendación aplicada:** `wp_check_filetype_and_ext()` + whitelist explícito.

---

## 🟠 ALTO

### QA-002 — Archivos adjuntos nunca registrados como attachment: fuga de disco sin límite
- **Estado:** ✅ **CORREGIDO en v0.4.1** — ver `DECISIONS.md` D-017.

### QA-003 — Retención de datos personales sin límite
- **Estado:** ✅ **CORREGIDO en v0.4.1** — ver `DECISIONS.md` D-018.

### QA-004 — Sin rate-limiting en el endpoint AJAX público
- **Estado:** ✅ **CORREGIDO en v0.4.1** — ver `DECISIONS.md` D-019.

### QA-005 — Fallo verificado de contraste WCAG AA
- **Estado:** ✅ **CORREGIDO en v0.4.1** — ver `DECISIONS.md` D-020.

### QA-006 — Sidebar "Footer - Columna 1" registrado pero nunca renderizado
- **Estado:** ✅ **CORREGIDO en v0.4.1** — ver `DECISIONS.md` D-021.

### QA-007 — `save_post` sin guardia `wp_is_post_revision()`
- **Estado:** ✅ **CORREGIDO en v0.4.1** — ver `DECISIONS.md` D-022.

### QA-008 — `CE_THEME_VERSION` hardcodeada
- **Estado:** ✅ **CORREGIDO en v0.4.1** — ver `DECISIONS.md` D-023.

### QA-009 — CPT Servicio sin `page-attributes`
- **Estado:** ✅ **CORREGIDO en v0.4.1** — ver `DECISIONS.md` D-024.

*(Detalle completo de descripción/riesgo/recomendación de QA-001 a QA-009 sin cambios respecto a versiones previas de este documento — no se repite aquí para no exceder el alcance de esta actualización, que solo modifica el estado de QA-018.)*

---

## 🟡 MEDIO

### QA-010 — Filtro `script_loader_tag` redundante con `wp_script_add_data('defer')`
- **Estado:** ⬜ Sin corregir (no incluido en la aprobación del Entregable 7.3 — solo se aprobó QA-018).
- **Archivo afectado:** `inc/enqueue.php`
- **Descripción:** El código usa `wp_script_add_data('defer')` (soporte nativo WP 6.3+) **y además** un filtro manual `ce_construction_add_defer_attribute()` que hace lo mismo vía `str_replace()`. Deuda técnica, no bug activo.
- **Recomendación:** Eliminar el filtro manual.

### QA-011 — `transport: postMessage` en 3 colores del Customizer sin script de preview
- **Estado:** ⬜ Sin corregir.
- **Archivo afectado:** `inc/customizer.php`
- **Descripción:** 3 ajustes de color declaran `postMessage` pero no hay script en `customize_preview_init` que aplique el cambio en vivo — la vista previa no se actualiza hasta publicar.
- **Recomendación:** Quitar `transport: postMessage` (dejar el default `refresh`).

### QA-012 — Consultas de "relacionados" sin caché (hasta 4 `WP_Query` extra por página)
- **Estado:** ⬜ Sin corregir.
- **Archivo afectado:** `inc/helpers.php`
- **Descripción:** `ce_get_related_services()`/`ce_get_related_projects()` no usan memoización ni transient, a diferencia de `ce_cpt_has_posts()`.
- **Recomendación:** Transient de corta duración o caché estática por request.

### QA-013 — Duplicación de variables CSS/reset entre `style.css` y `main.css`
- **Estado:** ⬜ Sin corregir.
- **Archivo afectado:** `style.css`, `assets/css/main.css`
- **Descripción:** Ambos declaran el mismo bloque `:root`/reset; el comentario de `style.css` afirma un `@import` inexistente.
- **Recomendación:** Corregir el comentario y evaluar unificar.

### QA-014 — JSON-LD sin endurecimiento contra `</script>`
- **Estado:** ⬜ Sin corregir.
- **Archivo afectado:** `inc/seo.php`
- **Descripción:** El contenido editorial se inyecta en bloques `<script type="application/ld+json">` sin escapar `</script>` literal.
- **Recomendación:** `JSON_UNESCAPED_SLASHES` o `str_replace('</script>', '<\/script>', $json)`.

### QA-015 — Variable `$attachment_name` calculada pero nunca usada
- **Estado:** ⬜ Sin corregir.
- **Archivo afectado:** `inc/quote-form.php`
- **Descripción:** Código muerto, sin impacto funcional.

### QA-016 — `<script>` inline en metabox sin `wp_enqueue_script`/dependencia declarada
- **Estado:** ⬜ Sin corregir.
- **Archivo afectado:** `inc/meta-boxes.php` (`ce_render_proyecto_gallery()`)
- **Descripción:** El selector de galería imprime `<script>` jQuery inline sin declarar `jquery` como dependencia formal.
- **Recomendación:** Mover a `assets/js/admin-gallery.js` con `wp_add_inline_script()`.

### QA-017 — Skip-link sin `tabindex="-1"` en `<main>`
- **Estado:** ⬜ Sin corregir.
- **Archivo afectado:** `header.php`
- **Descripción:** El skip-link apunta a `#ce-main-content`, que no tiene `tabindex="-1"` — el foco de teclado no se mueve al activarlo en varios navegadores.
- **Recomendación:** Añadir `tabindex="-1"` a `<main id="ce-main-content">`.

### QA-018 — Barra superior del header sin adaptación responsive explícita
- **Estado:** ✅ **CORREGIDO en v0.7.2** (Sprint 7, Entregable 7.3, con aprobación explícita del usuario) — ver `DECISIONS.md` D-039 y `CHANGELOG.md`.
- **Archivo afectado:** `assets/css/main.css` (`.ce-header__top`)
- **Descripción:** Se confirmó que no existía ninguna regla `@media` que adaptara `.ce-header__top` (teléfono + correo + horario + iconos sociales) para viewports pequeños (320-375px), pudiendo desbordar horizontalmente o comprimirse de forma ilegible.
- **Recomendación aplicada:** Nueva sección 24 (100% aditiva, al final de `assets/css/main.css`): `@media (max-width: 767.98px)` que aplica `flex-wrap: wrap` y centra `.ce-header__top .ce-container`, `.ce-header__contact` y `.ce-header__social`.
- **Impacto de la corrección:** Cambio acotado a un solo archivo CSS, sin riesgo de romper el layout de escritorio (la regla solo aplica por debajo de 768px). Verificado: balance de llaves/paréntesis correcto en el archivo completo tras la adición (365/365 llaves, 560/560 paréntesis).

---

## 🟢 BAJO

*(QA-019 a QA-023 — sin cambios respecto a versiones previas de este documento; ninguno fue tocado en este Entregable. Ver el detalle completo en el repositorio/entregas anteriores: reutilización de `.ce-modal__close` fuera de contexto, flechas de slider con 1 solo testimonio, discrepancia de versión ya resuelta como efecto colateral de QA-008, concatenación de HTML en JS del admin, y verificación positiva de `rel="noopener"` en todos los enlaces externos.)*

## 🔵 MEJORAS FUTURAS

*(QA-024 a QA-029 — sin cambios. Duplicación de breadcrumbs HTML/JSON-LD, `@id` compartido de Organization, preconnect de fuentes, auto-hospedaje de Google Fonts/Font Awesome, logging de fallos de `wp_mail()`, y evaluación de unificar los 4 CPTs simples — con recomendación de **no** unificarlos, ya documentada.)*

---

## Código muerto detectado (resumen)
- QA-006 (sidebar `footer-1` registrado, nunca usado) — ✅ Corregido en v0.4.1.
- QA-015 (`$attachment_name` calculado, nunca usado) — ⬜ Sin corregir.

---

## Cierre del Sprint de QA

**Actualización (Sprint 5, v0.4.1):** los 9 hallazgos Críticos y Altos corregidos como Fase 1 del Sprint 5 — ver `DECISIONS.md` D-017 a D-024.

**Actualización (Sprint 7, Entregable 7.3, v0.7.2):** de los 9 hallazgos Medios (QA-010 a QA-018), se corrigió únicamente **QA-018**, con autorización explícita puntual del usuario para ese hallazgo específico (no para el resto de Medios). Corrección documentada en `CHANGELOG.md` (v0.7.2) y `DECISIONS.md` (D-039).

Los 19 hallazgos restantes (8 Medios: QA-010 a QA-017; 5 Bajos; 6 Mejoras futuras) permanecen exactamente como en la auditoría original — no fueron tocados, conforme a la instrucción explícita de limitar el alcance de esta corrección únicamente a QA-018.
