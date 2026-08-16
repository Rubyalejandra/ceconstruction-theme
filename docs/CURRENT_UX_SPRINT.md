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

## Sprint UX-3 — "CTA centralizado + Modo del formulario de cotización" — Estado: ✅ **Completado.**

| Entregable | Objetivo | Estado |
|---|---|---|
| UX-3.1 | CTA de cotización centralizado (`ce_get_quote_cta_url()`) + modo configurable (`ce_quote_form_mode`: integrado/popup/desactivado) | ✅ Completado |
| UX-3.2 | Modal de cotización (popup real), con coexistencia integrado+popup | ✅ Completado |

> **Nota de versionado (arrastrada de UX-3.1):** `style.css` sigue en `Version: 0.8.5` (corrección de un desfase acumulado desde UX-1.2). Ni UX-3.2 ni UX-4.1 aplicaron bump adicional, por instrucción explícita en ambos Entregables — versión propuesta pendiente de confirmar: `0.8.6`. Ver `CHANGELOG.md`.

> ✅ **Cerrado — investigación de HTTP 400 en el envío del formulario de cotización.** Causa confirmada: en una subida de ZIP anterior a la revisada, `inc/quote-form.php` no se incluyó en el paquete — el usuario confirmó que fue un error propio de carga (no del código), no relacionado con el mecanismo honeypot que se había planteado como hipótesis. En el ZIP efectivamente revisado (`UX_3_2_15082026.zip`), `inc/quote-form.php` ya estaba presente, registrado en `functions.php`, y era hash-idéntico al entregado en UX-3.2 — verificado en su momento (`md5sum`) antes de descartar la hipótesis del honeypot como causa. **No se aplicó ningún cambio de código** a `inc/quote-form.php` ni a `template-parts/quote-form.php` para cerrar este hallazgo — no había ningún bug que corregir en ninguno de los dos.

## Sprint actual de esta fase

**Sprint UX-4 — "Hero configurable"** — Estado: **En curso.**

| Entregable | Objetivo | Estado |
|---|---|---|
| UX-4.1 | Hero configurable: tipo imagen/video + overlay ajustable | 🟡 Entregado — aprobación implícita al instruir continuar con UX-4.2 (ver nota abajo) |
| UX-4.2 | Modo slider del Hero | 🟡 **Entregado — pendiente de tu aprobación explícita** |

> **Nota sobre la aprobación de UX-4.1:** este cierre de UX-4.2 se ejecutó por instrucción explícita tuya de continuar directamente ("si no existe contradicción, continúa directamente"), sin objeciones sobre UX-4.1. Se registra como aprobación implícita a efectos de este documento, pero sigue sujeta a que la confirmes (o señales alguna corrección) junto con UX-4.2 — ninguna de las dos queda formalmente cerrada (D-038) hasta tu confirmación explícita.

## Trabajo realizado (Entregable UX-4.2)

`ce_hero_type` admite ahora un tercer valor, `slider`: varias imágenes de fondo (`ce_hero_slides`, nuevo control custom del Customizer `CE_Customize_Hero_Slides_Control` con miniaturas, añadir/quitar/reordenar) en bucle automático, renderizadas en `template-parts/hero.php` con el mismo criterio de capas y fallback silencioso que el modo video de UX-4.1. En frontend, un nuevo `ModuleHeroSlider` (`assets/js/main.js`) reutiliza una fábrica compartida (`createSliderController()`) extraída de `ModuleTestimonialSlider` — sin duplicar la mecánica de slide, sin cambio de comportamiento en el slider de Testimonios. Ver `CHANGELOG.md` (entrada de UX-4.2) y `DECISIONS.md` D-055 para el detalle completo.

Este Entregable retomó 4 archivos ya trabajados en una sesión anterior (`inc/customizer.php`, `assets/js/admin-hero-slides.js`, `inc/enqueue.php`, `assets/js/main.js`) y completó lo que faltaba: `inc/helpers.php::ce_get_hero_slide_ids()` (no existía, pese a que `inc/customizer.php` ya la invocaba), el registro de `ModuleHeroSlider.init()` en el bootstrap de `assets/js/main.js` (el módulo estaba definido pero nunca se llamaba), la rama del modo slider en `template-parts/hero.php` (sin ningún cambio previo), y la sección 27 de `assets/css/main.css` (sin crear todavía).

**Este Entregable tampoco tocó nada del sistema de cotización** ni del Home Builder — mismo alcance acotado que UX-4.1.

## Archivos creados / modificados (Sprint UX-4, Entregable UX-4.2)
- Creados: `assets/js/admin-hero-slides.js` (recibido de sesión anterior, verificado, sin cambios en este cierre).
- Modificados: `inc/customizer.php` (recibido de sesión anterior, verificado, sin cambios en este cierre), `inc/enqueue.php` (recibido de sesión anterior, verificado, sin cambios en este cierre), `inc/helpers.php` (nueva `ce_get_hero_slide_ids()`), `template-parts/hero.php` (rama del modo slider), `assets/js/main.js` (fábrica compartida + `ModuleHeroSlider` + registro en el bootstrap), `assets/css/main.css` (sección 27, aditiva).
- Sin cambios: todo el sistema de cotización (Sprint UX-3), el Home Builder (Sprint UX-1/UX-2), y las ramas imagen/video del Hero (UX-4.1).
- Eliminados: ninguno.

## Documentación actualizada (en este cierre)
`DECISIONS.md` (D-055), `CHANGELOG.md` (entrada de UX-4.2), `TREE.md` (nuevo archivo `assets/js/admin-hero-slides.js` + estado de `main.js`/`main.css`/`customizer.php`/`helpers.php`), y este mismo archivo. Sin cambios en `ARCHITECTURE.md` (sin cambio de arquitectura, solo generalización interna ya prevista en el plan) ni en `CURRENT_SPRINT.md`, `QA_REPORT.md` o `HANDOFF.md` (Sprint 8, sin tocar).

## Próximo Entregable
Con UX-4.2 entregado, el **Sprint UX-4 queda completo** (UX-4.1 + UX-4.2), pendiente de tu aprobación explícita de ambos. El siguiente Sprint según el orden recomendado de `docs/UX_CONVERSION_ANALISIS_Y_PLAN.md` §6 es **Sprint UX-5** ("Múltiples CTA / estrategias de conversión + objetivo de plantilla"), que no comienza sin tu aprobación explícita de UX-4 completo. El Sprint 8 (pausado en el Entregable 8.2) sigue exactamente donde estaba, disponible para retomar en 8.3 si lo prefieres en su lugar.

## Trabajo realizado (Entregable UX-4.1, sin cambios respecto al cierre anterior)

3 controles nuevos en la sección ya existente "CE: Sección Hero" del Customizer (`ce_hero_type`, `ce_hero_video`, `ce_hero_overlay_opacity`), consumidos por una rama condicional en `template-parts/hero.php` que decide entre imagen de fondo (comportamiento histórico, con fallback automático si el tipo es "video" pero no hay ningún video subido) o un `<video>` de fondo, más una nueva regla CSS aditiva para la opacidad del overlay vía custom property. El bloque de cálculo de los botones del Hero (incluido el fix de D-050) se verificó intacto, línea por línea, antes y después del cambio. Ver `CHANGELOG.md` (entrada de UX-4.1) y `DECISIONS.md` D-054 para el detalle completo.

**Este Entregable no tocó nada del sistema de cotización** (`inc/quote-form.php`, `template-parts/quote-form.php`, `inc/helpers.php`, `footer.php`, `assets/js/main.js`) — la investigación del HTTP 400 (nota de arriba) sigue exactamente donde estaba.

## Archivos creados / modificados (Sprint UX-4, Entregable UX-4.1)
- Creados: ninguno.
- Modificados: `inc/customizer.php` (3 controles + 2 sanitize callbacks, dentro/después de la sección Hero ya existente), `template-parts/hero.php` (rama condicional de tipo de fondo + overlay), `assets/css/main.css` (sección 26, aditiva).
- Sin cambios: todo lo demás — en particular, todo el sistema de cotización (Sprint UX-3) y el Home Builder (Sprint UX-1/UX-2) quedan exactamente como estaban.
- Eliminados: ninguno.

## Documentación actualizada (en este cierre)
`DECISIONS.md` (D-054), `CHANGELOG.md` (entrada de UX-4.1), y este mismo archivo. Sin cambios en `ARCHITECTURE.md`/`TREE.md`/`TODO.md` (sin archivos nuevos ni backlog que actualizar) ni en `CURRENT_SPRINT.md`, `QA_REPORT.md` o `HANDOFF.md` (Sprint 8, sin tocar).

## Sprint UX-4 — Estado: ✅ Completado (UX-4.1 + UX-4.2), aprobación implícita al instruir continuar directamente a UX-5.1.

## Sprint actual de esta fase

**Sprint UX-5 — "Múltiples CTA / estrategias de conversión"** — Estado: ✅ **Completado.**

| Entregable | Objetivo | Estado |
|---|---|---|
| UX-5.1 | CTA secundario reutilizable + estrategias A/B vía Home Builder | ✅ Completado — confirmado explícitamente por el usuario ("UX-5.1 YA ESTÁ COMPLETADO") al solicitar la auditoría UX/Arquitectura post UX-5.1 |
| UX-5.2 | Documentación de "objetivo de plantilla" (landing/corporativo/construcción/remodelación/servicios) | ⬜ No iniciado — pendiente del plan original, sin cambios de alcance (ver `docs/UX_CONVERSION_ANALISIS_Y_PLAN.md` §8.2) |

## Trabajo realizado (Entregable UX-5.1)
Decisión explícita del usuario: sin variante corta del Quote Form — `inc/quote-form.php`/`template-parts/quote-form.php` sin tocar (ver `DECISIONS.md` D-056). CTA secundario reutilizable: `template-parts/cta.php` se registra dos veces en el Home Builder (`cta`/`cta_secondary`, mismo archivo, sin duplicar), distinguido por `$args['variant']` desde `front-page.php`, con contenido independiente vía nueva sección "CE: CTA Secundario" en el Customizer. Fix D-050 preservado y parametrizado para ambas variantes. No activo por defecto (mismo criterio que team/clients/faq).

## Archivos creados / modificados (Entregable UX-5.1)
- Creados: ninguno.
- Modificados: `inc/home-builder.php`, `front-page.php`, `template-parts/cta.php`, `inc/customizer.php`.
- Sin cambios: `inc/quote-form.php`, `template-parts/quote-form.php`, `inc/helpers.php`, `footer.php`, `assets/js/main.js`, `template-parts/hero.php`, todo el Sprint 8.

## Documentación actualizada
`DECISIONS.md` (D-056), `CHANGELOG.md`, este archivo.

## Próximo Entregable (histórico — ver estado vigente más abajo)
**Sprint UX-5, Entregable UX-5.2** — Documentación de casos de uso (landing/corporativo/construcción/remodelación/servicios). No inicia sin tu aprobación explícita de UX-5.1.

---

## 🆕 Auditoría UX/Arquitectura post UX-5.1 — estado vigente del roadmap

El usuario solicitó una auditoría UX/Conversión/Arquitectura sobre el estado real del proyecto (sin implementar cambios), y la aprobó explícitamente como línea base de trabajo. Detalle completo, comparativas y criterios de aceptación de cada Sprint nuevo: `docs/UX_CONVERSION_ANALISIS_Y_PLAN.md` §8. Decisión formal de adopción y renumeración: `docs/DECISIONS.md` D-057. **Esta sección es puramente documental — ningún Sprint listado aquí está aprobado para implementación**, salvo que se indique "✅ Completado".

| Sprint | Objetivo | Estado |
|---|---|---|
| UX-1 a UX-5 | Home Builder, secciones faltantes, CTA centralizado + modal, Hero configurable, CTA secundario | ✅ **Completados** (ver tablas de arriba) |
| UX-5.2 | Documentación de "objetivo de plantilla" | ⬜ No iniciado — pendiente del plan original |
| **UX-6** | **Arquitectura de reutilización de secciones fuera del Home Builder** (`page.php` + mecanismo shortcode) | 🟡 **En curso.** Aprobación explícita del usuario recibida para iniciar el Sprint. UX-6.1 (`page.php`) ✅ completado — ver detalle abajo. UX-6.2 (shortcode) sin iniciar. |
| **UX-7** | Consistencia y configurabilidad: unificación de Hero Home/interior, layout de columnas + Quote Form en Hero, aprovechamiento de sidebars, CTA icono/color, logo independiente Header/Footer — 🆕 ampliado tras benchmark competitivo (DayBrook Homes / Re-Bath): estadísticas configurables (UX-7.6), insignias de confianza/licencias (UX-7.7), testimonio en video (UX-7.8), bloque de financiamiento (UX-7.9) | 🟡 **Propuesto — pendiente de tu aprobación explícita.** Ver `docs/DECISIONS.md` D-058 para el detalle del benchmark y la corrección de un falso positivo sobre las estadísticas (no muestran "0" en producción — era una lectura incorrecta de la animación JS). |
| **UX-8** | Video en Proyectos (YouTube/TikTok/WordPress vía oEmbed nativo) | 🟡 **Propuesto — futuro, sin iniciar.** Prioridad baja. |
| UX-9 (renumerado de UX-6 original) | Registro de backlog formal de Responsive (documental, sin código) | ⬜ No iniciado — mismo alcance que el "Sprint UX-6" del plan original, solo renumerado para no colisionar con el nuevo Sprint UX-6 de la auditoría (ver D-057) |

### Hallazgo derivado a Sprint 8 (QA), no a esta fase
La auditoría detectó que `.ce-header__social` (iconos sociales de la barra superior del header) no tiene reglas base de `display`/`gap`/tamaño en `assets/css/main.css` (a diferencia de `.ce-footer__social`, que sí las tiene) — confirmado visualmente en captura, los iconos aparecen apelmazados. Es una **corrección de algo ya roto (bug/QA), no una funcionalidad UX nueva** — se documenta aquí como referencia cruzada, candidata a `QA-043` dentro del **Sprint 8** ("Cierre de Hallazgos QA", pausado en el Entregable 8.2). **Este documento no modifica ningún archivo del Sprint 8** (`CURRENT_SPRINT.md`/`QA_REPORT.md`/`HANDOFF.md` sin tocar) — queda a la espera de que decidas si se resuelve al retomar el Sprint 8, o de forma aislada antes.

## Sprint UX-6 — "Arquitectura de reutilización de secciones fuera del Home Builder" — Estado: 🟡 En curso (UX-6.1 completado, UX-6.2 sin iniciar)

| Entregable | Objetivo | Estado |
|---|---|---|
| UX-6.1 | `page.php`: hero interno (`template-parts/page-hero`) + `the_content()` para cualquier Página, siguiendo el patrón ya establecido por `single.php` | ✅ **Completado** |
| UX-6.2 | Mecanismo de reutilización de secciones (shortcode `[ce_section key="..."]`) | ⬜ No iniciado — pendiente de tu aprobación explícita de arranque |

### Trabajo realizado (Entregable UX-6.1)
Nuevo `page.php` en la raíz del tema (Template Hierarchy nativa de WordPress: WordPress le da prioridad automática sobre `index.php` para cualquier Página, sin configuración adicional). Reutiliza `template-parts/page-hero.php` (sin modificarlo) con un `eyebrow` genérico (`get_bloginfo('name')`, mismo criterio que el eyebrow genérico ya usado por `archive.php`) y `the_content()` para el cuerpo, siguiendo exactamente el mismo patrón de `single.php`: soporte de Página protegida por contraseña (`post_password_required()`), paginación de contenido (`wp_link_pages()`), y comentarios vía `comments.php` (ya genérico para post/page desde el Entregable 6B.2, sin necesidad de modificarlo) si el administrador los habilita en una Página puntual. Sin secciones del Home Builder ni mecanismo de shortcode — eso es UX-6.2, explícitamente fuera de alcance de este Entregable. Sin cambios en `template-parts/page-hero.php` (layout/columnas/video es UX-7.1, tampoco tocado). Ver `docs/DECISIONS.md` D-059 para el detalle completo de las 3 decisiones de diseño de este cierre.

### Archivos creados / modificados (Entregable UX-6.1)
- Creados: `page.php` (raíz del tema).
- Modificados: ninguno.
- Sin cambios: `template-parts/page-hero.php`, `comments.php`, `index.php`, todo el Home Builder (UX-1/UX-2), todo el sistema de cotización (UX-3), el Hero de Home (UX-4), los CTA (UX-5), y todo el Sprint 8.
- Eliminados: ninguno.

### Documentación actualizada (en este cierre)
`docs/DECISIONS.md` (D-059), este mismo archivo. Sin cambios en `ARCHITECTURE.md`/`TREE.md`/`TODO.md`/`CHANGELOG.md` en este cierre (pendientes de actualización — ver "Pendientes" abajo), ni en `CURRENT_SPRINT.md`/`QA_REPORT.md`/`HANDOFF.md` (Sprint 8, sin tocar).

### Validaciones ejecutadas
Sin entorno WordPress/navegador real disponible (misma limitación metodológica ya documentada para UX-3.2/D-050): verificación por trazado lógico manual del flujo de `page.php` contra `single.php` (estructura idéntica: `get_header()` → loop → `page-hero` → contenido/contraseña → comentarios → `get_footer()`), balance de tags PHP (`<?php`/`?>`) y de estructuras alternativas (`if/endif`, `while/endwhile`) verificado línea por línea — sin `php -l` disponible en este entorno. **Pendiente de prueba funcional real en WordPress** (crear una Página de prueba y confirmar que renderiza con `page.php` en vez de `index.php`) antes de considerarlo verificado en runtime.

### Pendiente explícito de este Entregable
- `docs/CHANGELOG.md`: sin entrada añadida en este cierre — pendiente, mismo criterio que otros cierres de esta fase que difieren la actualización de `CHANGELOG.md`/`TREE.md` a un punto de cierre de Sprint más significativo.
- `docs/TREE.md`: no refleja todavía `page.php` como archivo nuevo en la raíz.
- Prueba funcional real en WordPress (ver validaciones arriba).

## Riesgos y pendientes abiertos (adicionales a los ya listados arriba)
- Pendiente de confirmación: bump de `style.css` (sigue en `0.8.5`) — sin cambios en este Entregable, no aplica bump.
- 🆕 Pendiente de tu aprobación explícita: inicio de UX-6.2 (shortcode), elección de shortcode vs. bloque Gutenberg (ya recomendado shortcode por la auditoría, sin decisión final tuya todavía), y decisión sobre cuándo resolver `QA-043` (iconos sociales del header) — ver `docs/UX_CONVERSION_ANALISIS_Y_PLAN.md` §8.7.

## Riesgos y pendientes abiertos
- ~~Investigación del HTTP 400 en el envío del formulario de cotización~~ — **cerrado**: causa confirmada como error de carga del usuario en un ZIP anterior (`inc/quote-form.php` no incluido en ese paquete), no un bug de código. Ver nota completa arriba, en la sección del Sprint UX-3.
- ~~Interacción entre `ce_quote_form_mode='modal'` y la sección `quote_form` del Home Builder (IDs duplicados)~~ — **resuelto en UX-3.2** mediante la bandera de instancia y el id dinámico en `template-parts/quote-form.php` (ver `DECISIONS.md` D-053).
- ~~"Nota de interacción con el Home Builder" de D-049 (CTA apuntando a una ancla `#ce-quote-form` inexistente si la sección se desactivaba en el Home Builder dejando el modo en `integrated`)~~ — **resuelta como efecto colateral de D-053**: los CTA ya no apuntan a esa ancla, apuntan siempre al popup (impreso globalmente por `footer.php`), independientemente del estado de esa sección en el Home Builder.
- **Vigente (documentado, no bloqueante, sin cambios desde UX-3.1):** en modo `disabled`, un botón con `ce_cta_btn_url`/`ce_hero_btn1_url` personalizado explícitamente por el administrador no se oculta (comportamiento intencional, ver `DECISIONS.md` D-049).
- **Vigente (documentado, no bloqueante):** limitación metodológica del entorno de desarrollo (sin WordPress/navegador real) — las verificaciones de UX-3.2 (incluida su continuación D-053) fueron trazado lógico manual, balance de sintaxis y `node --check`, no una prueba funcional en un navegador real. D-050 ya dejó constancia de que este tipo de verificación puede no detectar comportamientos específicos del runtime de WordPress; se aplica el mismo criterio de precaución a la interacción JS del modal multi-instancia hasta que se pruebe en un entorno real.
- Pendiente de confirmación del usuario: bump de `style.css` a `0.8.6` (propuesto, no aplicado — ver nota de versionado arriba).
- `HANDOFF.md` sigue pendiente de actualización — se evaluará en un punto de cierre de sesión/Sprint más significativo (criterio D-034 ya vigente en el proyecto). El cierre formal del Sprint UX-3 (una vez apruebes UX-3.2) es un candidato razonable para ese punto de actualización.
- Ningún riesgo detectado que afecte al Sprint 8 pausado; ningún archivo compartido con el Sprint 8 fue tocado en este Entregable.
