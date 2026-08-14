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

## Sprint UX-2 — "Secciones de Home faltantes: Team, Clients, FAQ" — Estado: ✅ **Completado.**

| Entregable | Objetivo | Estado |
|---|---|---|
| UX-2.1 | `template-parts/team.php` y `template-parts/clients.php` (secciones de Home para Equipo y Clientes) | ✅ Completado |
| UX-2.2 | `template-parts/faq.php` (sección de Home para Preguntas Frecuentes) + extracción de `content-faq-accordion.php` compartido con `single-servicio.php` | ✅ Completado |

## Sprint actual de esta fase

**Sprint UX-3 — "CTA centralizado + Modo del formulario de cotización"** — Estado: **En curso.**

| Entregable | Objetivo | Estado |
|---|---|---|
| UX-3.1 | CTA de cotización centralizado (`ce_get_quote_cta_url()`) + modo configurable (`ce_quote_form_mode`: integrado/popup/desactivado) | 🟡 **Entregado, con 1 corrección post-revisión aplicada (ver nota) — pendiente de tu aprobación** |
| UX-3.2 | Modal de cotización (popup real) | ⬜ No iniciado — no comienza sin tu aprobación explícita de UX-3.1 |

> **Nota:** entre el cierre de UX-1.2 y este Entregable se detectó y corrigió un desfase de versión en `style.css` (se había quedado en `0.8.2` mientras `CHANGELOG.md` ya documentaba hasta `v0.8.5`). Por decisión explícita del usuario, la corrección se aplicó dentro de este Entregable sin incrementar más allá de `0.8.5` — ver `DECISIONS.md` D-049.

## Trabajo realizado (Entregable UX-3.1)

Se centralizó en una única función (`ce_get_quote_cta_url()`, `inc/helpers.php`) el destino de los 7 puntos de CTA de cotización del tema (6 archivos: `header.php` aporta 2), que hasta ahora tenían la ancla `#ce-quote-form` hardcodeada de forma independiente. El comportamiento se controla desde una nueva sección del Customizer, "CE: Formulario de Cotización" (`ce_quote_form_mode`: integrado/popup/desactivado). En modo desactivado, los 7 puntos se ocultan automáticamente — para lograrlo fue necesario añadir una guarda nueva en `template-parts/cta.php` y `template-parts/hero.php`, que antes imprimían su botón de forma incondicional. El modo "popup" deja preparada la ancla `#ce-quote-modal`, pero el modal real (overlay + JS) es responsabilidad del Entregable UX-3.2, explícitamente fuera de este alcance. Ver `CHANGELOG.md` (entrada bajo v0.8.5) y `DECISIONS.md` D-049 para el detalle completo, incluida la nota sobre la interacción con el Home Builder y el caso límite de personalización previa de `ce_cta_btn_url`/`ce_hero_btn1_url`.

> **Corrección aplicada tras revisión del usuario (antes de aprobar el Entregable):** el modo "Integrado" no mostraba el botón CTA de `cta.php`/`hero.php` por un uso incorrecto del *fallback* de `get_theme_mod()` (dejaba de aplicarse en cuanto el theme_mod quedaba guardado como `''` tras cualquier publicación previa del Customizer). Corregido en ambos archivos con una comprobación explícita de cadena vacía. Ver `DECISIONS.md` D-050.

## Archivos creados / modificados (Sprint UX-3, Entregable UX-3.1)
- Creados: ninguno.
- Modificados: `inc/helpers.php`, `inc/customizer.php`, `header.php`, `footer.php`, `template-parts/cta.php`, `template-parts/hero.php`, `template-parts/sidebar-servicios.php`, `template-parts/sidebar-proyectos.php`, `style.css` (bump de versión `0.8.2` → `0.8.5`).
- Sin cambios: `inc/home-builder.php`, `front-page.php`, `functions.php`, `inc/enqueue.php`, `template-parts/quote-form.php` (por diseño, tal como exigía el alcance del Entregable).

## Documentación actualizada (en este cierre)
`DECISIONS.md` (D-049), `CHANGELOG.md` (entrada de UX-3.1, declarada bajo v0.8.5), y este mismo archivo. No se actualizaron `ARCHITECTURE.md`, `TREE.md` ni `TODO.md` en este Entregable — ninguno de los tres documenta contenido que haya cambiado como consecuencia directa de UX-3.1 (no hay archivos nuevos que añadir al árbol, y la arquitectura de CTA centralizado no altera ningún diagrama de flujo ya documentado en `ARCHITECTURE.md` de forma que requiera reescritura). Sin cambios en `CURRENT_SPRINT.md`, `QA_REPORT.md` ni `HANDOFF.md` (Sprint 8, sin tocar).

## Próximo Entregable
**Sprint UX-3, Entregable UX-3.2** — Modal de cotización (popup real). No inicia sin tu aprobación explícita de UX-3.1. Ver el prompt de continuación entregado junto con este Entregable para el detalle exacto de alcance.

## Riesgos y pendientes abiertos
- **Nuevo (documentado, no bloqueante):** interacción entre el modo `ce_quote_form_mode = 'integrated'` y la activación/desactivación de la sección `quote_form` en el Home Builder — si el administrador desactiva la sección pero deja el modo en "integrado", los CTA seguirán apuntando a una ancla que no existe en el DOM. Ver `DECISIONS.md` D-049.
- **Nuevo (documentado, no bloqueante):** en modo `disabled`, un botón con `ce_cta_btn_url`/`ce_hero_btn1_url` personalizado explícitamente por el administrador no se oculta (comportamiento intencional, ver `DECISIONS.md` D-049).
- `HANDOFF.md` sigue pendiente de actualización — se evaluará en un punto de cierre de sesión/Sprint más significativo (criterio D-034 ya vigente en el proyecto).
- Ningún riesgo detectado que afecte al Sprint 8 pausado; ningún archivo compartido con el Sprint 8 fue tocado en este Entregable.
