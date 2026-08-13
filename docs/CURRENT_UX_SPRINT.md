# CE Construction — CURRENT_UX_SPRINT.md
### Referencia oficial de la fase "Optimización UX / Conversión" (track paralelo al Sprint 8)

> Este documento es el equivalente de `CURRENT_SPRINT.md` para la nueva fase de trabajo "Optimización UX / Conversión", solicitada explícitamente por el usuario para ejecutarse **en paralelo** al Sprint 8 ("Cierre de Hallazgos QA"), que se pausa sin cerrarse. `CURRENT_SPRINT.md` no se modifica como consecuencia de este documento: sigue siendo la fuente de verdad del Sprint 8, congelada en el punto exacto donde se pausó.

---

## Estado del Sprint 8 en el momento de la pausa (no modificado por esta fase)

Sprint 8 — "Cierre de Hallazgos QA": **EN CURSO, PAUSADO.**
- Entregable 8.1: ✅ Completado y aprobado.
- **Entregable 8.2 (QA-030): 🟡 Entregado, pendiente de aprobación del usuario — se pausa exactamente en este punto.**
- Entregables 8.3 a 8.7: propuestos, sin iniciar.

Ver `CURRENT_SPRINT.md` para el detalle completo (sin cambios). El Sprint 8 se retomará en el Entregable 8.3 una vez el usuario apruebe el 8.2, sin que el trabajo de esta fase paralela lo condicione.

---

## Sprint actual de esta fase

**Sprint UX-1 — "Home Builder: base arquitectónica"** — Estado: **En curso.**

| Entregable | Objetivo | Estado |
|---|---|---|
| UX-1.1 | Registro central de secciones del Home (`inc/home-builder.php`) + refactor de `front-page.php` a loop data-driven | ✅ Entregado (Sprint UX-1 sigue en curso: aprobación conjunta pendiente con UX-1.2, ver nota abajo) |
| UX-1.2 | Panel de administración (Customizer): activar/desactivar y reordenar secciones | 🟡 **Entregado — pendiente de tu aprobación** |

Plan completo de Sprints UX-1 a UX-6: ver `docs/UX_CONVERSION_ANALISIS_Y_PLAN.md`.

> **Nota:** UX-1.1 se ejecutó y entregó en una sesión previa siguiendo la misma regla D-038 (no se inició UX-1.2 sin confirmar antes coherencia del ZIP con el estado real de UX-1.1). Ambos Entregables completan el Sprint UX-1 tal como estaba planificado; el Sprint se da por completado formalmente cuando se apruebe también UX-1.2.

## Trabajo realizado (Entregable UX-1.2)

Persistencia real del Home Builder desde WordPress: nueva sección "CE: Home Builder" en el Customizer (`inc/customizer.php`), con un control custom (`CE_Customize_Home_Sections_Control`) que permite activar/desactivar cada sección (casilla) y reordenarlas (drag&drop, jQuery UI Sortable — ya incluido en WordPress core, sin librerías nuevas). El orden y estado se guardan en un único `theme_mod` (`ce_home_sections_order`, JSON `[{key, enabled}]`) y se aplican al Home mediante el filtro `ce_home_active_order` que UX-1.1 ya había dejado expuesto — **`inc/home-builder.php` y `front-page.php` no se tocaron en este Entregable**, tal como estaba previsto. Ver `CHANGELOG.md` v0.8.3 y `DECISIONS.md` D-046 para el detalle completo.

**Team, Clients y FAQ:** visibles en el panel con la casilla deshabilitada y la nota "(próximamente)" — no se pueden activar hasta que sus template-parts existan (Sprint UX-2).

## Archivos creados / modificados (Sprint UX-1, Entregable UX-1.2)
- Creados: `assets/js/admin-home-builder.js`.
- Modificados: `inc/customizer.php`, `inc/enqueue.php` (adición aditiva y no relacionada al final del archivo — ver `DECISIONS.md` D-046 para la nota explícita sobre por qué se tocó un archivo asociado al Sprint 8).
- Sin cambios: `inc/home-builder.php`, `front-page.php`, `functions.php` (por diseño, tal como exigía el alcance del Entregable).

## Documentación actualizada (en este cierre)
`DECISIONS.md` (D-046), `CHANGELOG.md` (v0.8.3), `ARCHITECTURE.md` (sección 3, sección 4, sección 6 actualizada, historial de cambios), y este mismo archivo. `TODO.md` no requirió cambios adicionales (la sección 26, añadida en UX-1.1, ya cubre el backlog de ambos Entregables de este Sprint). Sin cambios en `CURRENT_SPRINT.md`, `QA_REPORT.md` ni `HANDOFF.md` (Sprint 8, sin tocar).

## Próximo Entregable
**Sprint UX-2, Entregable UX-2.1** — `template-parts/team.php` y `template-parts/clients.php` (secciones de Home para Equipo y Clientes, reutilizando `content-equipo.php`/`content-cliente.php` como partial de card, mismo patrón que `template-parts/projects.php`). No inicia sin tu aprobación explícita de UX-1.2. Ver el prompt de continuación entregado junto con este Entregable para el detalle exacto de alcance.

## Riesgos y pendientes abiertos
- ~~El control "sortable" del Customizer no tiene precedente exacto en el proyecto~~ — resuelto en UX-1.2 reutilizando `jquery-ui-sortable` (ya incluido en WordPress core); riesgo R-2 del plan cerrado sin necesidad de la alternativa de bajo riesgo (`<select>` numéricos).
- `HANDOFF.md` sigue pendiente de actualización — se evaluará en un punto de cierre de sesión/Sprint más significativo (criterio D-034 ya vigente en el proyecto).
- Ningún riesgo detectado que afecte al Sprint 8 pausado; ver nota explícita en `DECISIONS.md` D-046 sobre la adición aditiva en `inc/enqueue.php`.
