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

**Sprint UX-2 — "Secciones de Home faltantes: Team, Clients, FAQ"** — Estado: ✅ **Completado** (pendiente de tu aprobación de UX-2.2 para cierre formal, ver nota abajo).

| Entregable | Objetivo | Estado |
|---|---|---|
| UX-2.1 | `template-parts/team.php` y `template-parts/clients.php` (secciones de Home para Equipo y Clientes) | ✅ Completado |
| UX-2.2 | `template-parts/faq.php` (sección de Home para Preguntas Frecuentes) + extracción de `content-faq-accordion.php` compartido con `single-servicio.php` | 🟡 **Entregado — pendiente de tu aprobación** |

> **Nota:** igual que en el cierre del Sprint UX-1, ambos Entregables completan el Sprint UX-2 tal como estaba planificado; el Sprint se da por completado formalmente cuando también apruebes UX-2.2.

## Trabajo realizado (Entregable UX-2.2)

Última sección de Home pendiente: **las 13 secciones del catálogo del brief ya tienen template-part real.** `template-parts/faq.php` sigue el mismo patrón que `team.php`/`clients.php` (UX-2.1): `WP_Query` acotado sobre el CPT `ce_faq`, auto-ocultamiento vía `ce_cpt_has_posts()`. Reutiliza el mismo `.ce-accordion` que ya usaba `single-servicio.php` desde el Sprint 3, a través de un nuevo partial compartido, `template-parts/content-faq-accordion.php` (ítem individual de accordion, extraído del bloque "FAQ relacionadas" existente) — evaluado y confirmado que sí convenía la extracción, en vez de duplicar el markup. `single-servicio.php` recibió un único cambio quirúrgico: el bloque interno del `while` ahora invoca ese partial en vez de imprimir el `<div class="ce-accordion__item">` inline; el resto del archivo es idéntico (verificado por diff). El identificador del panel (`id`/`aria-controls`) pasó de un contador secuencial local a `get_the_ID()`, con unicidad intrínseca entre ambos contextos. `inc/home-builder.php` solo recibió actualización de comentarios (misma disciplina que UX-2.1). `inc/customizer.php` **no requirió ningún cambio** — se confirmó, no se asumió, releyendo `CE_Customize_Home_Sections_Control` antes de proceder. Ver `CHANGELOG.md` v0.8.5 y `DECISIONS.md` D-048 para el detalle completo.

**Con esto, ninguna de las 13 secciones del catálogo queda con la casilla forzada a "(próximamente)" en el panel del Home Builder.**

## Archivos creados / modificados (Sprint UX-2, Entregable UX-2.2)
- Creados: `template-parts/faq.php`, `template-parts/content-faq-accordion.php`.
- Modificados: `inc/home-builder.php` (solo comentarios en la entrada `faq` y en el docblock de `ce_construction_default_home_order()` — ninguna función cambió de lógica), `single-servicio.php` (solo el bloque interno del accordion FAQ — verificado por diff como el único cambio del archivo).
- Sin cambios: `inc/customizer.php`, `inc/cpt-faq.php`, `front-page.php`, `functions.php`, `assets/css/main.css`, `assets/js/main.js` (por diseño, tal como exigía el alcance del Entregable).

## Documentación actualizada (en este cierre)
`DECISIONS.md` (D-048), `CHANGELOG.md` (v0.8.5), `ARCHITECTURE.md` (sección 3: dos nuevas filas de `template-parts/`; sección 6: nota actualizada, Sprint UX-2 completo; historial de cambios), `TREE.md` (filas de `template-parts/`, `home-builder.php` y `single-servicio.php` actualizadas), `TODO.md` (sección 26 actualizada), y este mismo archivo. Sin cambios en `CURRENT_SPRINT.md`, `QA_REPORT.md` ni `HANDOFF.md` (Sprint 8, sin tocar).

## Próximo Entregable
**Sprint UX-3, Entregable UX-3.1** — CTA centralizado y modos de cotización (ver `docs/UX_CONVERSION_ANALISIS_Y_PLAN.md` §5 para el alcance exacto). No inicia sin tu aprobación explícita de UX-2.2. Ver el prompt de continuación entregado junto con este Entregable para el detalle exacto de alcance.

## Riesgos y pendientes abiertos
- ~~El control "sortable" del Customizer no tiene precedente exacto en el proyecto~~ — resuelto en UX-1.2 reutilizando `jquery-ui-sortable` (ya incluido en WordPress core); riesgo R-2 del plan cerrado sin necesidad de la alternativa de bajo riesgo (`<select>` numéricos).
- ~~R-3 (Team/Clients/FAQ como secciones de Home son archivos nuevos, no solo configuración)~~ — resuelto en UX-2.1/UX-2.2: riesgo confirmado como bajo en los 3 casos, sin sorpresas (reutilización completa de CPT/helpers/partials ya existentes).
- `HANDOFF.md` sigue pendiente de actualización — se evaluará en un punto de cierre de sesión/Sprint más significativo (criterio D-034 ya vigente en el proyecto). El cierre formal del Sprint UX-2 (una vez apruebes UX-2.2) es un candidato razonable para ese punto de actualización.
- Ningún riesgo detectado que afecte al Sprint 8 pausado; ningún archivo compartido con el Sprint 8 fue tocado en este Entregable.
