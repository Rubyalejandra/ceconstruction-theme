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

**Sprint UX-3 — "CTA centralizado + Modo del formulario de cotización"** — Estado: ✅ **Completado** (pendiente de tu aprobación de UX-3.2 para cierre formal, ver nota abajo).

| Entregable | Objetivo | Estado |
|---|---|---|
| UX-3.1 | CTA de cotización centralizado (`ce_get_quote_cta_url()`) + modo configurable (`ce_quote_form_mode`: integrado/popup/desactivado) | ✅ Completado |
| UX-3.2 | Modal de cotización (popup real), con coexistencia integrado+popup | 🟡 **Entregado (retomado y finalizado tras interrupción de sesión) — pendiente de tu aprobación** |

> **Nota:** igual que en el cierre de los Sprints UX-1 y UX-2, ambos Entregables completan el Sprint UX-3 tal como estaba planificado; el Sprint se da por completado formalmente cuando también apruebes UX-3.2.

> **Nota de versionado (arrastrada de UX-3.1):** `style.css` quedó en `Version: 0.8.5` tras UX-3.1 (corrección de un desfase acumulado desde UX-1.2). **UX-3.2 no aplicó ningún bump adicional** por instrucción explícita del Entregable — versión propuesta para cuando confirmes: `0.8.6`. Ver `CHANGELOG.md`, entrada de UX-3.2.

## Trabajo realizado (Entregable UX-3.2, incluida su continuación/corrección D-053)

El diseño inicial de este Entregable (`footer.php` imprimiendo `#ce-quote-modal` solo en modo `modal`, suprimiendo la instancia integrada) resultó insuficiente: el usuario necesitaba que el formulario integrado y el popup **coexistieran** — visible el formulario donde ya se mostraba (Home/Servicios/Proyectos), y los 7 CTA del sitio abriendo el popup en cualquier página, incluidas las que nunca tuvieron formulario embebido (`archive-servicio.php`/`archive-proyecto.php`).

Tras una interrupción de sesión, se retomaron sin descartarlos los cambios parciales ya aplicados a `inc/helpers.php` (mecanismo de bandera `ce_construction_quote_form_rendered_inline()`, con su corrección del `static` compartido ya resuelta) y `template-parts/quote-form.php` (llamada a la bandera), y se completó la integración: `ce_get_quote_cta_url()` ahora devuelve `#ce-quote-modal` tanto en `integrated` como en `modal` (los 7 CTA no necesitaron tocarse, ya centralizados desde UX-3.1); `footer.php` imprime el modal siempre que el modo no sea `disabled`; el `<form>` del modal usa `id="ce-quote-form-modal"` (y sus campos internos, sufijo `-modal`) solo cuando colisionaría con una instancia integrada ya impresa en la misma página; `ModuleQuoteForm` (`assets/js/main.js`) se refactorizó de singleton a localizador multi-instancia por clase (`.ce-quote-form-instance`). También se detectó y corrigió un gap adicional: la guarda de supresión de la instancia normal no cubría `disabled`, lo que habría dejado un formulario funcional en `single-servicio.php`/`single-proyecto.php` incluso con la cotización desactivada.

Ver `CHANGELOG.md` (entrada de UX-3.2, con su subsección de continuación) y `DECISIONS.md` D-053 (que documenta también el diagnóstico completo de qué parte de la sesión interrumpida era correcta y qué faltaba) para el detalle completo.

**Decisiones señaladas explícitamente durante la implementación (no ampliaron el alcance por iniciativa propia sin documentarlo):** las 2 adiciones a `assets/js/main.js` y la nueva clase CSS de la entrega original no estaban nombradas literalmente en el encargo, pero se determinó que eran directamente necesarias para que el modal abriera/cerrara correctamente y pudiera alojar el formulario — quedaron documentadas en `DECISIONS.md` D-051. El gap de `disabled` sin cubrir por la guarda de supresión, detectado durante la continuación de esta sesión, también se documentó explícitamente (D-053) antes de corregirlo, en vez de aplicarse en silencio.

## Archivos creados / modificados (Sprint UX-3, Entregable UX-3.2, estado final tras D-053)
- Creados: ninguno.
- Modificados: `inc/helpers.php` (`ce_get_quote_cta_url()` + mecanismo de bandera de instancia), `template-parts/quote-form.php`, `footer.php`, `assets/js/main.js`, `assets/css/main.css`, `inc/customizer.php` (labels del control `ce_quote_form_mode`).
- Sin cambios: `inc/home-builder.php`, `front-page.php`, `header.php`, `template-parts/cta.php`, `template-parts/hero.php`, `template-parts/sidebar-servicios.php`, `template-parts/sidebar-proyectos.php`, `single-servicio.php`, `single-proyecto.php`, `inc/quote-form.php` (handler AJAX), `inc/enqueue.php`, `style.css`.
- Eliminados: ninguno.

## Documentación actualizada (en este cierre)
`DECISIONS.md` (D-051 original + D-053, continuación/corrección), `CHANGELOG.md` (entrada de UX-3.2, con subsección de continuación), `ARCHITECTURE.md` (sección 8: nota aditiva sobre el punto de entrada configurable al formulario, sin reescribir el diagrama ya documentado), y este mismo archivo. Sin cambios en `TREE.md`/`TODO.md` (sin archivos nuevos ni backlog que actualizar) ni en `CURRENT_SPRINT.md`, `QA_REPORT.md` o `HANDOFF.md` (Sprint 8, sin tocar).

## Próximo Entregable
**Sprint UX-4, Entregable UX-4.1** — Hero configurable (ver `docs/UX_CONVERSION_ANALISIS_Y_PLAN.md` §5 para el alcance exacto). No inicia sin tu aprobación explícita de UX-3.2. Ver el prompt de continuación entregado junto con este Entregable para el detalle exacto de alcance.

## Riesgos y pendientes abiertos
- ~~Interacción entre `ce_quote_form_mode='modal'` y la sección `quote_form` del Home Builder (IDs duplicados)~~ — **resuelto en UX-3.2** mediante la bandera de instancia y el id dinámico en `template-parts/quote-form.php` (ver `DECISIONS.md` D-053).
- ~~"Nota de interacción con el Home Builder" de D-049 (CTA apuntando a una ancla `#ce-quote-form` inexistente si la sección se desactivaba en el Home Builder dejando el modo en `integrated`)~~ — **resuelta como efecto colateral de D-053**: los CTA ya no apuntan a esa ancla, apuntan siempre al popup (impreso globalmente por `footer.php`), independientemente del estado de esa sección en el Home Builder.
- **Vigente (documentado, no bloqueante, sin cambios desde UX-3.1):** en modo `disabled`, un botón con `ce_cta_btn_url`/`ce_hero_btn1_url` personalizado explícitamente por el administrador no se oculta (comportamiento intencional, ver `DECISIONS.md` D-049).
- **Vigente (documentado, no bloqueante):** limitación metodológica del entorno de desarrollo (sin WordPress/navegador real) — las verificaciones de UX-3.2 (incluida su continuación D-053) fueron trazado lógico manual, balance de sintaxis y `node --check`, no una prueba funcional en un navegador real. D-050 ya dejó constancia de que este tipo de verificación puede no detectar comportamientos específicos del runtime de WordPress; se aplica el mismo criterio de precaución a la interacción JS del modal multi-instancia hasta que se pruebe en un entorno real.
- Pendiente de confirmación del usuario: bump de `style.css` a `0.8.6` (propuesto, no aplicado — ver nota de versionado arriba).
- `HANDOFF.md` sigue pendiente de actualización — se evaluará en un punto de cierre de sesión/Sprint más significativo (criterio D-034 ya vigente en el proyecto). El cierre formal del Sprint UX-3 (una vez apruebes UX-3.2) es un candidato razonable para ese punto de actualización.
- Ningún riesgo detectado que afecte al Sprint 8 pausado; ningún archivo compartido con el Sprint 8 fue tocado en este Entregable.
