# CE Construction — DECISIONS.md

> Registro formal y acumulativo de decisiones arquitectónicas del proyecto.
> No se elimina ni se reescribe una decisión ya tomada: si cambia, se agrega una nueva entrada que referencia a la anterior.

---

> **Nota de este archivo:** las decisiones D-001 a D-035 se mantienen exactamente como en la versión previa de este documento (Nonces por módulo, CPT `cotizacion`, secciones auto-ocultas, SEO auto-desactivable, `page-hero.php` reutilizable, relaciones heurísticas Servicio↔Proyecto, Schema `Project` como `@type` múltiple, correcciones QA Críticas/Altas D-017 a D-024, `has_archive` de Cliente D-025, metodología permanente de Entregables D-030, política de actualización incremental de documentación D-034, `404.php` con experiencia visual más completa D-035). Esta entrega añade únicamente D-036 a D-039, correspondientes al Sprint 7.

---

### D-036 — Alcance de `inc/widgets.php`: 2 widgets orientados al footer, sin CSS/JS nuevo
- **Fecha:** Sprint 7, Entregable 7.1
- **Problema:** El brief no especifica qué widgets custom debía contener `inc/widgets.php`, solo que el archivo existiera ("widgets custom").
- **Solución elegida:** 2 widgets (`CE_Construction_Widget_Contact`, `CE_Construction_Widget_Social`) diseñados específicamente para dar uso real a `footer-1`, sidebar registrado desde el Sprint 1 pero vacío hasta este Entregable (ver QA-006). Ambos reutilizan exclusivamente helpers y clases CSS ya existentes (`ce_get_social_links()`, `.ce-footer__social`, `.ce-footer__contact-item`).
- **Alternativas descartadas:** Widgets de contenido dinámico (ej. "Servicios recientes") — descartado por exceder el alcance mínimo del Entregable y duplicar funcionalidad ya cubierta por `template-parts/services.php`/`projects.php`.
- **Motivo:** Entregar valor real e inmediato sin generar código o CSS duplicado.
- **Impacto:** Archivo 100% nuevo y aditivo; cero cambios en archivos ya aprobados.

---

### D-037 — `archive.php` como fallback dedicado, sin extender breadcrumbs de Testimonios/FAQ
- **Fecha:** Sprint 7, Entregable 7.2
- **Problema:** `archive.php` debía cubrir contextos (categoría/etiqueta/autor/fecha, archivo de Testimonios/FAQ) para los cuales `ce_construction_breadcrumbs()` no tiene rama específica.
- **Solución elegida:** Construir `archive.php` completamente funcional para su propio alcance (hero interno con título/subtítulo contextual, grid de tarjetas, paginación, estado vacío), sin extender `inc/seo.php` para agregar ramas de breadcrumbs nuevas.
- **Alternativas descartadas:** Extender `ce_construction_breadcrumbs()` en el mismo Entregable — descartado por exceder el alcance literal de "archive.php genérico" y no haber sido solicitado explícitamente.
- **Motivo:** Mantener cada Entregable acotado a su objetivo único (regla de la metodología de Entregables, `HANDOFF.md` sección 16).
- **Impacto:** Los breadcrumbs para estos contextos siguen mostrando solo "Inicio" sin nivel intermedio — comportamiento ya existente para cualquier contexto sin rama explícita, documentado como observación, no como bug nuevo.

---

### D-038 — Nueva regla permanente: aprobación explícita obligatoria al cierre de cada Entregable
- **Fecha:** Tras el cierre inicial de los Entregables 7.1 y 7.2
- **Problema:** La metodología de Entregables (D-030) ya exigía un flujo de cierre (verificación, documentación, marcado como Completado, propuesta del siguiente), pero no establecía de forma explícita que ese cierre requiriera la aprobación del usuario antes de iniciar el Entregable siguiente. Esto dejaba ambigüedad sobre si "entregar" un Entregable era equivalente a "finalizarlo".
- **Solución elegida:** Se incorpora como regla permanente: **ningún Entregable se considera finalizado hasta que se hayan entregado todos los archivos creados o modificados durante ese Entregable y el usuario los haya aprobado explícitamente.** No debe iniciarse el siguiente Entregable sin haber realizado previamente la entrega completa del anterior y haber esperado esa aprobación. Regla obligatoria para todos los Sprints futuros, sin excepción. La aprobación debe ser un acto explícito del usuario (aprobar los archivos entregados, o instruir directamente avanzar al siguiente Entregable) — nunca algo que Claude infiera o dé por hecho sin esa señal concreta.
- **Alternativas descartadas:** Mantener el criterio implícito previo (un Entregable se consideraba "Completado" en cuanto el código y la documentación quedaban listos, sin un paso de aprobación explícito separado) — descartado por dejar espacio a avanzar sobre trabajo no revisado por el cliente.
- **Motivo:** Alinear la metodología con el principio ya establecido en D-030 ("nunca reducir la calidad ni saltarse control") y dar al cliente un punto de control real antes de cada avance, especialmente relevante en Entregables que tocan código de producción.
- **Impacto:** Todos los Entregables se marcan como **"Entregado — pendiente de aprobación"** en `PROJECT_STATUS.md`/`TODO.md`/`CURRENT_SPRINT.md` hasta recibir la señal explícita de aprobación del usuario, momento en el cual pasan a **"Completado"**. Los Entregables 7.1 y 7.2 pasaron a "Completado" cuando el usuario, tras haberlos recibido en un cierre previo, instruyó explícitamente continuar con el Entregable 7.3 — esa instrucción directa de avanzar es la señal de aprobación exigida por esta regla, no una inferencia automática de Claude. `HANDOFF.md` sección 16 se actualiza con esta regla como parte del flujo obligatorio de cierre.

---

### D-039 — Corrección QA-018: responsive de `.ce-header__top` vía `flex-wrap` + centrado
- **Fecha:** Sprint 7, Entregable 7.3
- **Problema:** `.ce-header__top` (teléfono + correo + horario + iconos sociales) no tenía ninguna regla `@media` que la adaptara para viewports pequeños (320-375px), pudiendo desbordar horizontalmente o comprimirse de forma ilegible (QA-018, verificado por búsqueda exhaustiva en `assets/css/main.css`).
- **Solución elegida:** Se aplicó la alternativa propuesta en el propio hallazgo (`QA_REPORT.md`, opción de `flex-wrap`): una regla `@media (max-width: 767.98px)` que envuelve y centra `.ce-header__top .ce-container`, `.ce-header__contact` y `.ce-header__social`, añadida como sección 24 (nueva, al final) de `assets/css/main.css`.
- **Alternativas descartadas:** Ocultar `.ce-header__top` por debajo de 768px y mover esa información al menú móvil off-canvas — descartada porque habría requerido modificar el markup de `header.php` (el menú móvil actual no incluye teléfono/correo/horario), excediendo el alcance de una corrección puramente CSS y el alcance aprobado explícitamente por el cliente para este Entregable (solo QA-018).
- **Motivo:** Resolver el hallazgo real (desbordamiento/ilegibilidad en móvil) con el menor riesgo posible, sin tocar markup HTML ni otros archivos.
- **Impacto:** Cambio 100% aditivo en `assets/css/main.css` (~18 líneas), cero riesgo de romper el layout de escritorio (la regla solo aplica por debajo de 768px), cero cambios en `header.php`, `inc/helpers.php` o `assets/js/main.js`.
