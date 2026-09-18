# Actualización de docs/CURRENT_UX_SPRINT.md

Este archivo contiene dos bloques de texto para aplicar sobre `docs/CURRENT_UX_SPRINT.md`:

---

## BLOQUE 1 — Insertar como nuevo párrafo, inmediatamente después del primer párrafo del bloque "🏁 ESTADO FINAL de esta fase (sesión de cierre) — Sprint UX-7 CERRADO" (al inicio del documento)

> **🆕 Actualización posterior (cierre del Sprint UX-8 — PRIMERA VERSIÓN ESTABLE):** con la aprobación explícita de UX-8.1 (D-108) y UX-8.2 (D-109 + D-110) en una sesión posterior a este cierre de fase, **el Sprint UX-8 ("Galería de Proyectos: medios flexibles y curación del Home") queda también completo y cerrado**. No queda ningún Sprint ni Entregable pendiente en todo el proyecto (Sprint 8 de QA cerrado desde D-107; fase UX-1 a UX-11 cerrada; Sprint UX-8 cerrado). El usuario declaró explícitamente esta la **Primera Versión Estable del tema — v1.0.0** (`style.css`, ver `docs/DECISIONS.md` D-111). Ver el detalle completo del Sprint UX-8 más abajo, en su sección dedicada, ahora cerrada.

---

## BLOQUE 2 — Reemplaza íntegramente la sección "## 🆕 Sprint UX-8 — ..." (última sección del documento, desde su encabezado hasta el final del archivo)

## Sprint UX-8 — "Galería de Proyectos: medios flexibles y curación del Home" — Estado: ✅ **COMPLETADO Y APROBADO — cierra con la Primera Versión Estable (v1.0.0)**

> Retoma y amplía el "Sprint UX-8 — Video en Proyectos" que quedaba como backlog futuro en `docs/UX_CONVERSION_ANALISIS_Y_PLAN.md` §8.4, a solicitud explícita del usuario tras el cierre completo del Sprint 8 (D-107). El alcance original (un único campo de video separado) se amplió, por decisión explícita del usuario, a una galería mixta reordenable — ver `docs/DECISIONS.md` D-108.

| Entregable | Objetivo | Estado |
|---|---|---|
| UX-8.1 | Galería mixta del Proyecto (imagen y/o video, reordenable) | ✅ **Aprobado explícitamente por el usuario.** Ver `docs/DECISIONS.md` D-108. |
| UX-8.2 | Curación de la Galería del Home (proyecto destacado + imagen(es) favorita(s), múltiples favoritas por proyecto tras D-110; responsive con carrusel móvil accesible) | ✅ **Aprobado explícitamente por el usuario**, con el alcance combinado de D-109 + D-110. Ver `docs/DECISIONS.md` D-109 y D-110. |

### Trabajo realizado (UX-8.1)

Nueva fuente de verdad `_ce_proyecto_media` (JSON), con migración de solo lectura desde `_ce_proyecto_galeria` y derivación automática de vuelta hacia ese mismo meta en cada guardado (para no tocar `template-parts/gallery.php` ni `inc/seo.php` en ese Entregable). Metabox reescrito (`ce_render_proyecto_gallery()`) con repeater de 3 acciones — añadir imágenes, añadir video de la Biblioteca, añadir video por URL — reordenable por botones. Frontend (`single-proyecto.php`) renderiza el mosaico mixto reutilizando `ModuleLightbox` ya extendido para video desde UX-7.8, sin ningún cambio de JS de frontend. Ajuste puntual posterior (misma sesión): corrección de apariencia nativa del botón de video (`appearance: none`) y uso de la miniatura real devuelta por oEmbed para video externo, cuando está disponible.

### Trabajo realizado (UX-8.2, alcance final tras D-110)

Curación explícita de la Galería del Home, reemplazando la selección automática "primeros proyectos publicados, cronológico". Checkbox `_ce_proyecto_destacado` por proyecto + flag `favorite` por ítem de imagen dentro de `_ce_proyecto_media`. **Alcance final (D-110, sustituye el límite de 1 favorita por proyecto de D-109):** cualquier cantidad de imágenes puede marcarse como favorita en un mismo proyecto, sin exclusividad — todas las favoritas de un proyecto entran al mosaico; el único límite es el tope global de 8 imágenes, que puede cortar a mitad de las favoritas de un mismo proyecto si es necesario. Resolución curada centralizada en `ce_construction_get_home_gallery_images()` (`inc/helpers.php`): favoritas → primera imagen → destacada del post, por proyecto; destacados primero, relleno con no-destacados hasta 8 (ambos grupos aportando todas sus favoritas por igual); sin destacados, la sección se oculta por completo (sin fallback al comportamiento anterior). Responsive: grid en desktop/tablet (sin cambios), carrusel de 1 imagen por vista en móvil (`ModuleHomeGallerySlider`, reutiliza `createSliderController()`/D-055 con el botón de pausa accesible de QA-035/D-102 — sin slider nuevo).

### Archivos creados / modificados (UX-8.1 + UX-8.2 combinados)
- Modificados: `inc/meta-boxes.php`, `inc/helpers.php`, `inc/enqueue.php`, `single-proyecto.php`, `assets/js/main.js`, `assets/css/main.css`.
- Reescrito íntegramente: `template-parts/gallery.php` (UX-8.2), `assets/js/admin-proyecto-gallery.js` (UX-8.1, ajustado en D-110).
- Sin cambios: `inc/seo.php`, `ModuleLightbox`, `createSliderController()`, `.ce-gallery-grid`/`.ce-gallery-item` (regla existente), y todo el Sprint 8 (QA, cerrado).

### Documentación actualizada en este cierre
`docs/DECISIONS.md` (D-111 — aprobación + versionado), `docs/CHANGELOG.md` (entrada v1.0.0), `docs/PROJECT_STATUS.md`, `docs/TODO.md`, `docs/HANDOFF.md`, `docs/TREE.md`, este mismo archivo. **Diferido, sin cambios de alcance** (criterio D-034): `docs/ARCHITECTURE.md`, `docs/QA_REPORT.md`, `docs/CONTEXT_MAP.md`, `docs/UX_CONVERSION_ANALISIS_Y_PLAN.md`.

### Validaciones ejecutadas
Ver el detalle completo en `docs/DECISIONS.md` D-108 (UX-8.1), D-109 y D-110 (UX-8.2) — balance de sintaxis PHP/JS verificado en todos los archivos tocados; sin entorno WordPress/navegador real disponible durante el desarrollo (misma limitación metodológica ya documentada en el proyecto). El usuario confirmó explícitamente haber probado UX-8.1 y UX-8.2 en WordPress real antes de esta aprobación (incluida la corrección del botón de video y la miniatura real de UX-8.1).

## 🎉 SPRINT UX-8 CERRADO — PRIMERA VERSIÓN ESTABLE (v1.0.0)

Con el Sprint UX-8 completo y aprobado, **no queda ningún Sprint ni Entregable pendiente en todo el proyecto**. El usuario declaró esta la Primera Versión Estable del tema, reflejada en la cabecera `Version:` de `style.css` (`0.8.5` → `1.0.0`, SemVer — ver `docs/DECISIONS.md` D-111). Cualquier trabajo nuevo (un Sprint 9, mejoras del backlog de "Mejoras futuras" de QA, u otra línea) requiere que el usuario defina y apruebe explícitamente su alcance antes de iniciar cualquier implementación, conforme a D-038.
