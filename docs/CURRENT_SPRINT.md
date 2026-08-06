# CE Construction — CURRENT_SPRINT.md
### Referencia oficial del Sprint en curso

> Documento pequeño, preciso y siempre actualizado al finalizar cada Entregable. Su objetivo es permitir continuar un Sprint interrumpido sin releer toda la documentación del proyecto. No duplica información ya detallada en `PROJECT_STATUS.md` — solo resume lo esencial para retomar el trabajo de inmediato.

---

## Sprint actual

**Sprint 7 — (sin nombre formal aún; propuesto en `PROJECT_STATUS.md` sección 7 como "Widgets, archivo genérico, QA Medios y screenshot")**
**Estado:** No iniciado

> El Sprint 6B ("Blog y páginas genéricas") quedó **COMPLETADO** con sus 3 Entregables (6B.1 `page.php`, 6B.2 `single.php`+`comments.php`, 6B.3 `404.php`). Ver `HANDOFF.md` y `CHANGELOG.md` v0.6.3 para el cierre formal. Este documento ya refleja el nuevo Sprint 7 propuesto, pendiente de tu aprobación para iniciar su primer Entregable.

## Entregables

- Entregable 7.1 — ⬜ Pendiente (`inc/widgets.php` — widgets custom) — **siguiente, si se aprueba iniciar el Sprint 7**
- Entregable 7.2 — ⬜ Pendiente (`archive.php` genérico — fallback para archivos sin plantilla dedicada, ej. Testimonios/FAQ)
- Entregable 7.3 — ⬜ Pendiente (Hallazgos QA Medios: QA-010 a QA-018 de `QA_REPORT.md`, con aprobación explícita de cuáles corregir)
- Entregable 7.4 — ⬜ Pendiente (`screenshot.png` — puede depender de definiciones visuales finales del cliente)

## Trabajo realizado (último Entregable cerrado: 6B.3, Sprint anterior)

`404.php`: página de error dedicada con `status_header(404)` + `nocache_headers()` explícitos, numeral "404" estilizado, mensaje de error, formulario de búsqueda reutilizado (`template-parts/no-results.php`), y sección "Quizás te interese" con tarjetas de enlace rápido a Servicios/Proyectos/Equipo/Inicio condicionadas a contenido publicado. Sin cambios en `inc/helpers.php`, `inc/seo.php`, `main.css` ni `main.js`. Ningún Entregable del Sprint 7 ha comenzado todavía.

## Archivos creados (Sprint 7, acumulado)

Ninguno todavía — Sprint 7 no ha iniciado.

## Archivos modificados (Sprint 7, acumulado)

Ninguno todavía — Sprint 7 no ha iniciado.

## Documentación actualizada (en este cierre)

Solo `CURRENT_SPRINT.md` (este archivo) — todos los demás documentos de control (`PROJECT_STATUS.md`, `TODO.md`, `TREE.md`, `CHANGELOG.md`, `DECISIONS.md`, `HANDOFF.md`) ya reflejaban correctamente el cierre del Sprint 6B y la propuesta del Sprint 7 desde el cierre del Entregable 6B.3; no requirieron ningún cambio adicional en esta actualización.

## Próximo Entregable

**7.1 — `inc/widgets.php`** (primer Entregable propuesto del Sprint 7), pendiente de tu aprobación explícita para iniciar.

## Riesgos abiertos (específicos de este Sprint)

- El Entregable 7.3 (hallazgos QA Medios) requiere que definas explícitamente cuáles de los 9 hallazgos Medios (QA-010 a QA-018) autorizas corregir antes de iniciar ese Entregable — no se corregirá ninguno sin esa aprobación puntual.
- El Entregable 7.4 (`screenshot.png`) puede depender de assets visuales reales del cliente (logo, fotografías de proyectos) que aún no se han provisto — podría quedar pendiente de insumos externos al desarrollo del tema en sí.

## Observaciones

- Ningún Entregable de este Sprint ha comenzado. El orden 7.1 → 7.2 → 7.3 → 7.4 es una propuesta inicial (ver `PROJECT_STATUS.md` sección 7); el cliente puede reordenar o excluir Entregables antes de aprobar el inicio del Sprint.
- La política de actualización incremental de documentación (`DECISIONS.md` D-034) sigue vigente: al cerrar cada Entregable de este Sprint, actualizar únicamente los documentos que realmente cambien.
