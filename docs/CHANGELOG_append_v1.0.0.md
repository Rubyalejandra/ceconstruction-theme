## v1.0.0 — PRIMERA VERSIÓN ESTABLE — Sprint UX-8 completo (Entregables UX-8.1 + UX-8.2)

**Módulo:** Galería de Proyectos (medios mixtos) + Curación de la Galería del Home — cierre del Sprint UX-8 y declaración de la primera versión estable del tema
**Estado:** ✅ Aprobado explícitamente por el usuario — Sprint UX-8 cerrado en su totalidad

### Añadido
- **UX-8.1 — Galería mixta del Proyecto (imagen y/o video, reordenable):** nueva fuente de verdad `_ce_proyecto_media` (con migración de solo lectura desde `_ce_proyecto_galeria`), metabox reescrito con repeater de 3 acciones (añadir imágenes, añadir video de la Biblioteca, añadir video por URL), reordenable por botones. Mosaico mixto en `single-proyecto.php`, reutilizando `ModuleLightbox` ya extendido para video desde UX-7.8, sin ningún cambio de JS de frontend. Ver `DECISIONS.md` D-108.
- **UX-8.2 — Curación de la Galería del Home:** checkbox `_ce_proyecto_destacado` por proyecto + flag `favorite` por ítem de imagen dentro de `_ce_proyecto_media` (ampliado por D-110 a **múltiples favoritas por proyecto, sin tope por proyecto** — el tope global de 8 sigue aplicando sobre el total). Resolución curada centralizada en `ce_construction_get_home_gallery_images()` (`inc/helpers.php`): favoritas → primera imagen → destacada del post, por proyecto; destacados primero, relleno con no-destacados hasta 8; sin destacados, la sección se oculta por completo. Responsive: grid en desktop/tablet (sin cambios), carrusel de 1 imagen por vista en móvil (`ModuleHomeGallerySlider`, reutiliza `createSliderController()`/D-055 con el botón de pausa accesible de QA-035/D-102 — sin slider nuevo). Ver `DECISIONS.md` D-109 y D-110.

### Cambiado
- **`template-parts/gallery.php`:** reescrito íntegramente para consumir la nueva resolución curada.
- **`single-proyecto.php`:** bloque de galería reescrito para el mosaico mixto (UX-8.1).
- **`inc/meta-boxes.php`:** `ce_render_proyecto_gallery()` reescrita (galería mixta); guardado deriva `_ce_proyecto_galeria` automáticamente desde `_ce_proyecto_media`; checkbox `_ce_proyecto_destacado` en `ce_render_proyecto_fields()`; botón ★/☆ de favorita por ítem de imagen (sin exclusividad tras D-110).
- **`inc/helpers.php`:** funciones nuevas `ce_construction_decode_proyecto_media_json()`, `ce_construction_get_proyecto_media_raw()`, `ce_construction_get_proyecto_media_items()` (UX-8.1); `ce_construction_resolve_home_gallery_images()` (plural, D-110, reemplaza a la función singular de D-109) y `ce_construction_get_home_gallery_images()` (UX-8.2).
- **`inc/enqueue.php`:** `ceProyectoGalleryData` ampliada con etiquetas del flujo de galería mixta y de favorita/destacado.
- **`assets/js/admin-proyecto-gallery.js`:** reescrito íntegramente para el repeater de galería mixta; botón de favorita simplificado tras D-110 (ya no es mutuamente excluyente entre ítems del mismo proyecto).
- **`assets/js/main.js`:** nuevo módulo `ModuleHomeGallerySlider` (carrusel móvil de la Galería del Home, reutiliza `createSliderController()` sin duplicar lógica).
- **`assets/css/main.css`:** sección 34 (galería mixta de Proyecto, `.ce-gallery-item--video` + repeater admin, UX-8.1) y nueva sección para `.ce-home-gallery`/`.ce-home-gallery__track` (UX-8.2), ambas aditivas.

### Sin cambios
- `inc/quote-form.php` (handler AJAX del formulario de cotización) — protegido explícitamente en toda esta fase.
- `.ce-gallery-grid`/`.ce-gallery-item` (regla ya existente, reutilizada tal cual), `inc/seo.php`, `ModuleLightbox`, `createSliderController()`.
- Ningún archivo del Sprint 8 (Cierre de Hallazgos QA, cerrado desde D-107).

### Versionado — Primera Versión Estable
- **`style.css`:** cabecera `Version:` actualizada de `0.8.5` a **`1.0.0`**, siguiendo Semantic Versioning (SemVer, `MAYOR.MENOR.PARCHE`) — esquema formalizado explícitamente como estándar del proyecto en `DECISIONS.md` D-111. Con el Sprint 8 (QA) cerrado en su totalidad (D-107) y la fase de Optimización UX/Conversión completa (Sprints UX-1 a UX-11 más UX-8), el tema alcanza su primera versión estable sin ningún hallazgo de severidad Alta o Media abierto y sin ningún Entregable pendiente de aprobación.

### Decisiones clave
Ver `DECISIONS.md`: D-108, D-109, D-110 (alcance funcional del Sprint UX-8), D-111 (aprobación final del Sprint + versionado a v1.0.0).

---

## 🎉 SPRINT UX-8 COMPLETADO — PRIMERA VERSIÓN ESTABLE DEL TEMA (v1.0.0)

Con la aprobación de UX-8.1 y UX-8.2, **no queda ningún Sprint ni Entregable pendiente en todo el proyecto** (Sprint 8 de QA cerrado desde D-107; fase UX-1 a UX-11 cerrada; Sprint UX-8 cerrado en esta sesión). El tema queda declarado en su **Primera Versión Estable (v1.0.0)**. Cualquier trabajo nuevo requiere que el usuario defina y apruebe explícitamente su alcance antes de iniciar cualquier implementación, conforme a D-038.
