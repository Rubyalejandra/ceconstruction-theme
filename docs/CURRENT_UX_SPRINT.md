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

## Sprint UX-1 — "Home Builder: base arquitectónica" — Estado: ✅ **Completado.**

| Entregable | Objetivo | Estado |
|---|---|---|
| UX-1.1 | Registro central de secciones del Home (`inc/home-builder.php`) + refactor de `front-page.php` a loop data-driven | ✅ Completado |
| UX-1.2 | Panel de administración (Customizer): activar/desactivar y reordenar secciones | ✅ Completado |

Plan completo de Sprints UX-1 a UX-6: ver `docs/UX_CONVERSION_ANALISIS_Y_PLAN.md`.

## Sprint actual de esta fase

**Sprint UX-2 — "Secciones de Home faltantes: Team, Clients, FAQ"** — Estado: **En curso.**

| Entregable | Objetivo | Estado |
|---|---|---|
| UX-2.1 | `template-parts/team.php` y `template-parts/clients.php` (secciones de Home para Equipo y Clientes) | 🟡 **Entregado — pendiente de tu aprobación** |
| UX-2.2 | `template-parts/faq.php` (sección de Home para Preguntas Frecuentes, evaluando extraer el accordion compartido con `single-servicio.php`) | ⬜ No iniciado — requiere tu aprobación de UX-2.1 |

## Trabajo realizado (Entregable UX-2.1)

Dos nuevas secciones de Home, completando 11 de las 13 secciones previstas en el brief (solo falta FAQ, UX-2.2). Ambas siguen el mismo patrón exacto de `template-parts/projects.php`: `WP_Query` acotado, auto-ocultamiento vía `ce_cpt_has_posts()`, y reutilización 100% de los partials de tarjeta ya existentes desde el Sprint 5 (`content-equipo.php`/`content-cliente.php`), cada una con el wrapper de grid correcto para su partial (`.ce-grid.ce-grid--4` para Equipo, `.ce-clients-grid` para Clientes — ver `DECISIONS.md` D-047 para el porqué de esa distinción). `inc/home-builder.php` solo recibió actualización de comentarios (sus 3 funciones no cambiaron de lógica). `inc/customizer.php` **no requirió ningún cambio**: el `file_exists()` genérico ya implementado en UX-1.2 detecta automáticamente que los nuevos template-parts existen y habilita sus casillas en el panel "CE: Home Builder" sin intervención de código. Ver `CHANGELOG.md` v0.8.4 y `DECISIONS.md` D-047 para el detalle completo.

**FAQ:** sigue visible en el panel con la casilla deshabilitada y la nota "(próximamente)" — no se puede activar hasta que exista `template-parts/faq.php` (Entregable UX-2.2).

## Archivos creados / modificados (Sprint UX-2, Entregable UX-2.1)
- Creados: `template-parts/team.php`, `template-parts/clients.php`.
- Modificados: `inc/home-builder.php` (solo comentarios en las entradas `team`/`clients` y en el docblock de `ce_construction_default_home_order()` — ninguna función cambió de lógica ni de valor de retorno).
- Sin cambios: `inc/customizer.php`, `front-page.php`, `functions.php`, `assets/css/main.css`, `assets/js/main.js` (por diseño, tal como exigía el alcance del Entregable — ambas secciones reutilizan estilos y JS ya existentes).

## Documentación actualizada (en este cierre)
`DECISIONS.md` (D-047), `CHANGELOG.md` (v0.8.4), `ARCHITECTURE.md` (sección 3: nueva fila de `template-parts/`; sección 6: nota actualizada sobre Team/Clients/FAQ; historial de cambios), `TREE.md` (fila de `template-parts/` actualizada), `TODO.md` (sección 26 actualizada), y este mismo archivo. Sin cambios en `CURRENT_SPRINT.md`, `QA_REPORT.md` ni `HANDOFF.md` (Sprint 8, sin tocar).

## Próximo Entregable
**Sprint UX-2, Entregable UX-2.2** — `template-parts/faq.php` (sección de Home para Preguntas Frecuentes, CPT `ce_faq`, reutilizando el mismo `.ce-accordion` ya usado en `single-servicio.php`; evaluar al iniciar el entregable si se extrae un partial compartido `content-faq-accordion.php` para no duplicar markup entre Home y single-servicio). No inicia sin tu aprobación explícita de UX-2.1. Ver el prompt de continuación entregado junto con este Entregable para el detalle exacto de alcance.

## Riesgos y pendientes abiertos
- ~~El control "sortable" del Customizer no tiene precedente exacto en el proyecto~~ — resuelto en UX-1.2 reutilizando `jquery-ui-sortable` (ya incluido en WordPress core); riesgo R-2 del plan cerrado sin necesidad de la alternativa de bajo riesgo (`<select>` numéricos).
- ~~R-3 (Team/Clients como secciones de Home son archivos nuevos, no solo configuración)~~ — resuelto en UX-2.1: riesgo confirmado como bajo, sin sorpresas (reutilización completa de CPT/helpers/partials ya existentes).
- `HANDOFF.md` sigue pendiente de actualización — se evaluará en un punto de cierre de sesión/Sprint más significativo (criterio D-034 ya vigente en el proyecto).
- Ningún riesgo detectado que afecte al Sprint 8 pausado; ningún archivo compartido con el Sprint 8 fue tocado en este Entregable.
