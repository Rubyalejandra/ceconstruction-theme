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
| UX-1.1 | Registro central de secciones del Home (`inc/home-builder.php`) + refactor de `front-page.php` a loop data-driven | 🟡 **Entregado — pendiente de tu aprobación** |
| UX-1.2 | Panel de administración (Customizer): activar/desactivar y reordenar secciones | ⬜ No iniciado — no comienza sin tu aprobación explícita de UX-1.1 (misma regla D-038 ya vigente para el Sprint 8) |

Plan completo de Sprints UX-1 a UX-6: ver `docs/UX_CONVERSION_ANALISIS_Y_PLAN.md`.

## Trabajo realizado (Entregable UX-1.1)

Nuevo archivo `inc/home-builder.php`: registro central de las 13 secciones de Home previstas en el brief (`ce_construction_home_sections()`), orden por defecto que reproduce exactamente el orden anterior de `front-page.php` (`ce_construction_default_home_order()`), y punto de extensión filtrable para la configuración futura (`ce_construction_get_active_home_order()`). `front-page.php` refactorizado de una lista fija de 10 `get_template_part()` a un loop sobre ese registro. Ver `CHANGELOG.md` v0.8.2 y `DECISIONS.md` D-045 para el detalle completo.

**Resultado visual:** idéntico al estado anterior — cero regresión. Team, Clients y FAQ quedan registrados para uso futuro pero no forman parte del orden activo por defecto (sus template-parts no existen todavía).

## Archivos creados / modificados (Sprint UX-1, Entregable UX-1.1)
- Creados: `inc/home-builder.php`.
- Modificados: `front-page.php`, `functions.php`, `style.css` (bump de versión `0.8.1` → `0.8.2`).

## Documentación actualizada (en este cierre)
`DECISIONS.md` (D-045), `CHANGELOG.md` (v0.8.2), `ARCHITECTURE.md` (sección 3, sección 4, sección 6 reescrita, historial de cambios), `TREE.md` (`inc/home-builder.php`, anotación en `front-page.php`), `TODO.md` (nueva sección 26), `PROJECT_STATUS.md` (nota aditiva, sin alterar el estado del Sprint 8), y este mismo archivo (nuevo). Sin cambios en `CURRENT_SPRINT.md` ni en `QA_REPORT.md` (pertenecen exclusivamente al Sprint 8, que no fue tocado). `HANDOFF.md` no se actualizó en este Entregable — se evaluará su actualización en el cierre de un Entregable que constituya un punto de continuidad de sesión más significativo, conforme al criterio ya vigente en el proyecto (D-034); esto queda señalado como pendiente abierto, no como omisión silenciosa.

## Próximo Entregable
**UX-1.2** — Panel de administración en el Customizer para activar/desactivar y reordenar las secciones registradas en `inc/home-builder.php`. No inicia sin tu aprobación explícita de UX-1.1. Ver el prompt de continuación entregado junto con este Entregable para el detalle exacto de alcance.

## Riesgos y pendientes abiertos
- El control "sortable" del Customizer para UX-1.2 no tiene precedente exacto en el código actual del proyecto (`inc/customizer.php` solo usa controles nativos simples) — riesgo medio, ya señalado como R-2 en `docs/UX_CONVERSION_ANALISIS_Y_PLAN.md`, con una alternativa de bajo riesgo identificada (lista de `<select>` numéricos) si el drag&drop no resultara viable dentro del Entregable.
- `HANDOFF.md` queda pendiente de actualización (ver nota arriba).
- Ningún riesgo detectado que afecte al Sprint 8 pausado.
