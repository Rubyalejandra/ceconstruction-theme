# CE Construction — CHANGELOG.md

> Historial de versiones del proyecto. Este archivo es acumulativo: cada nueva versión se agrega al final, nunca se reescribe una versión anterior.
>
> **Nota:** las versiones v0.1.0 a v0.6.3 se mantienen exactamente como en las entregas previas de este documento (ver repositorio/entregas anteriores para el detalle completo). Esta actualización solo añade/corrige las entradas del Sprint 7.

---

## v0.7.0 — Sprint 7, Entregable 7.1: inc/widgets.php

**Módulo:** Widgets personalizados (primer Entregable del Sprint 7)
**Estado:** ✅ Completado — aprobado por el usuario.

### Añadido
- `inc/widgets.php`: `CE_Construction_Widget_Contact` (teléfono/correo/dirección/horario, con fallback automático a los theme mods del Customizer) y `CE_Construction_Widget_Social` (iconos de redes sociales vía `ce_get_social_links()`), ambos registrados en `widgets_init`. Diseñados para dar uso real al sidebar "Footer - Columna 1" (`footer-1`), renderizable desde la corrección QA-006 (v0.4.1) pero vacío hasta ahora.

### Sin cambios
- `functions.php`, `assets/css/main.css`, `assets/js/main.js`.

### Decisiones clave
- Ver `DECISIONS.md`: D-036.

### Nota de mantenimiento
- Se confirma la eliminación de `inc/enqueue_1.php` del repositorio oficial (duplicado accidental de `inc/enqueue.php`). `inc/enqueue.php` es y ha sido siempre el único archivo de encolado de assets.

---

## v0.7.1 — Sprint 7, Entregable 7.2: archive.php genérico

**Módulo:** Fallback de archivo genérico (segundo Entregable del Sprint 7)
**Estado:** ✅ Completado — aprobado por el usuario.

### Añadido
- `archive.php`: cubre categoría/etiqueta/autor/fecha (blog) y los CPTs `testimonio`/`ce_faq`. Reutiliza `template-parts/page-hero.php` y `template-parts/content-fallback.php`.

### Sin cambios
- `inc/helpers.php`, `inc/seo.php`, `assets/css/main.css`, `assets/js/main.js`.

### Decisiones clave
- Ver `DECISIONS.md`: D-037.

### Observación (sin corregir en este Entregable)
- `ce_construction_breadcrumbs()` no tiene rama dedicada para los contextos que cubre `archive.php` — limitación preexistente, no introducida aquí.

---

## Nota de proceso — nueva regla permanente (D-038)

Se incorpora una nueva regla permanente a la metodología de Gestión de Sprints y Entregables: **ningún Entregable se considera finalizado hasta que todos sus archivos hayan sido entregados y aprobados explícitamente por el usuario**; el siguiente Entregable no inicia sin esa aprobación previa. Ver `DECISIONS.md` D-038 y `HANDOFF.md` sección 16.

Los Entregables 7.1 y 7.2 quedaron entregados en un cierre previo bajo esta regla. El usuario, tras recibirlos, instruyó explícitamente continuar con el Entregable 7.3 — esa instrucción directa de avanzar constituye la aprobación explícita exigida por D-038, por lo que 7.1 y 7.2 quedan marcados como **Completados** a partir de ese momento. No se trata de una inferencia automática: es la señal de aprobación que la propia regla contempla (aprobar los archivos, o instruir directamente avanzar).

---

## v0.7.2 — Sprint 7, Entregable 7.3: corrección QA-018 (Hallazgos QA Medios, alcance parcial aprobado)

**Módulo:** Corrección selectiva de hallazgos Medios de `QA_REPORT.md` (tercer Entregable del Sprint 7)
**Estado:** Entregado — pendiente de aprobación explícita del usuario (ver `DECISIONS.md` D-038)

### Corregido
- **QA-018 (Medio):** `assets/css/main.css` — `.ce-header__top` (teléfono/correo/horario/redes sociales) no tenía ninguna regla `@media` que la adaptara para viewports pequeños (320-375px). Se añadió una regla `@media (max-width: 767.98px)` (sección 24, nueva, al final del archivo) que envuelve y centra el contenido en móvil. Cambio 100% aditivo.

### Alcance explícitamente excluido de este Entregable
- QA-010 a QA-017 (los otros 8 hallazgos Medios) — el usuario aprobó explícitamente corregir únicamente QA-018 en este Entregable. Permanecen documentados y sin tocar en `QA_REPORT.md`.

### Sin cambios
- `header.php`, `inc/helpers.php`, `inc/seo.php`, `assets/js/main.js` — la corrección fue puramente CSS.

### Decisiones clave
- Ver `DECISIONS.md`: D-039.

### Verificaciones ejecutadas
- **Sintaxis PHP:** no aplica — ningún archivo PHP fue modificado en este Entregable.
- **Sintaxis CSS:** balance de llaves/paréntesis verificado (script Python) sobre `assets/css/main.css` completo tras la adición — 365/365 llaves (balance 0), 560/560 paréntesis (balance 0), 12 bloques `@media` totales.
- **JavaScript:** sin cambios en `assets/js/main.js`; no aplica `node --check` (node v22.22.2 disponible en el entorno, confirmado, pero sin archivo que verificar).
- **Dependencias e includes:** sin cambios — `assets/css/main.css` ya está enqueado en `inc/enqueue.php` sin modificación; ninguna regla nueva depende de markup adicional en `header.php`.

---

## Próximas versiones (planeadas, no confirmadas)

- **v0.7.3 (propuesta):** Entregable 7.4 — `screenshot.png`. No inicia hasta que el Entregable 7.3 (v0.7.2) sea aprobado.
- **v0.8.0 (propuesta):** Corrección del resto de hallazgos QA Medios (QA-010 a QA-017), Bajos y Mejoras futuras, sujeto a aprobación explícita de cuáles corregir.
- **v0.9.0 (propuesta):** Auditoría de accesibilidad y performance (incluye QA-026/QA-027: auto-hospedar fuentes/Font Awesome).
