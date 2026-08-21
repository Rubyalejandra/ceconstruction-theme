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
| **UX-6** | **Arquitectura de reutilización de secciones fuera del Home Builder** (`page.php` + mecanismo shortcode) | ✅ **Completado y aprobado.** UX-6.1, UX-6.2 (shortcode `[ce_section]`, confirmado frente a bloque Gutenberg) y UX-6.3 (fix de ancho) aprobados explícitamente por el usuario. Ver `docs/DECISIONS.md` D-062. |
| **UX-7** | Consistencia y configurabilidad: unificación de Hero Home/interior, layout de columnas + Quote Form en Hero, aprovechamiento de sidebars, CTA icono/color, logo independiente Header/Footer — 🆕 ampliado tras benchmark competitivo (DayBrook Homes / Re-Bath): estadísticas configurables (UX-7.6), insignias de confianza/licencias (UX-7.7), testimonio en video (UX-7.8), bloque de financiamiento (UX-7.9) | 🟡 **Propuesto — pendiente de tu aprobación explícita.** Ver `docs/DECISIONS.md` D-058 para el detalle del benchmark y la corrección de un falso positivo sobre las estadísticas (no muestran "0" en producción — era una lectura incorrecta de la animación JS). |
| **UX-8** | Video en Proyectos (YouTube/TikTok/WordPress vía oEmbed nativo) | 🟡 **Propuesto — futuro, sin iniciar.** Prioridad baja. |
| UX-9 (renumerado de UX-6 original) | Registro de backlog formal de Responsive (documental, sin código) | ⬜ No iniciado — mismo alcance que el "Sprint UX-6" del plan original, solo renumerado para no colisionar con el nuevo Sprint UX-6 de la auditoría (ver D-057) |
| **UX-10 🆕** | Página de Testimonios: CPT propio + Google Reviews (Opción Híbrida C confirmada por el usuario) — requerimiento urgente para producción, fuera del catálogo original del benchmark | 🟢 **En curso.** Secuencia confirmada por el usuario: Opción 2 de D-072 (UX-10 ahora, en paralelo, con UX-7 pausado en UX-7.7 aprobado; UX-7 se retoma en UX-7.8 solo después de que UX-10 esté completo). UX-10.1 + UX-10.2 entregados, pendientes de tu aprobación explícita — ver sección dedicada más abajo y `docs/DECISIONS.md` D-073. |

### Hallazgo derivado a Sprint 8 (QA), no a esta fase
La auditoría detectó que `.ce-header__social` (iconos sociales de la barra superior del header) no tiene reglas base de `display`/`gap`/tamaño en `assets/css/main.css` (a diferencia de `.ce-footer__social`, que sí las tiene) — confirmado visualmente en captura, los iconos aparecen apelmazados. Es una **corrección de algo ya roto (bug/QA), no una funcionalidad UX nueva** — se documenta aquí como referencia cruzada, candidata a `QA-043` dentro del **Sprint 8** ("Cierre de Hallazgos QA", pausado en el Entregable 8.2). **Este documento no modifica ningún archivo del Sprint 8** (`CURRENT_SPRINT.md`/`QA_REPORT.md`/`HANDOFF.md` sin tocar) — queda a la espera de que decidas si se resuelve al retomar el Sprint 8, o de forma aislada antes.

## Sprint UX-6 — "Arquitectura de reutilización de secciones fuera del Home Builder" — Estado: ✅ **Completado y aprobado** (UX-6.1 + UX-6.2 + UX-6.3, los tres aprobados explícitamente — ver `docs/DECISIONS.md` D-062)

| Entregable | Objetivo | Estado |
|---|---|---|
| UX-6.1 | `page.php`: hero interno (`template-parts/page-hero`) + `the_content()` para cualquier Página, siguiendo el patrón ya establecido por `single.php` | ✅ **Completado y aprobado** |
| UX-6.2 | Mecanismo de reutilización de secciones (shortcode `[ce_section key="..."]`) | ✅ **Aprobado.** Mecanismo confirmado por el usuario: shortcode (no bloque Gutenberg). |
| UX-6.3 | Fix de ancho: `[ce_section]` se encogía a 640px dentro de `single.php`/`page.php` (bug detectado validando UX-6.2 en WordPress real) | ✅ **Aprobado**, junto con UX-6.2, en la misma sesión. |

### Trabajo realizado (Entregable UX-6.2)
Nuevo `inc/section-shortcode.php`: registra `[ce_section key="..."]`, resuelve `key` contra `ce_construction_home_sections()` (`inc/home-builder.php`) y delega en `get_template_part( ..., null, $args )` — el mismo mecanismo que ya usa `front-page.php`, sin reimplementarlo. No depende de que la sección esté activa en el Home Builder (`ce_construction_get_active_home_order()`): son dos consumos independientes del mismo registro, tal como pedía el criterio de aceptación.

Se detectó, al implementar, que `front-page.php` tenía una única línea de lógica de `$args` no trivial (`cta_secondary` → `variant=secondary`, D-056) que el shortcode también necesitaba para no renderizar contenido incorrecto en esa clave. Se extrajo a `ce_construction_get_home_section_args()` (nueva, `inc/home-builder.php`), usada ahora por ambos consumidores — `front-page.php` sin cambio de comportamiento. Detalle completo de la decisión, alternativas descartadas y guardas de seguridad (clave vacía/inexistente, límite de anidamiento defensivo): `docs/DECISIONS.md` D-060.

### Trabajo realizado (Entregable UX-6.3 — fix de ancho)
Al validar UX-6.2 en WordPress real, el usuario detectó (y confirmó en DevTools) que `[ce_section key="about"]` en una Página se renderizaba encogido a 640px con grandes espacios laterales, en vez del ancho completo del sitio que tiene en el Home. Causa: `single.php`/`page.php` envolvían todo `the_content()` (meta info + `<article>` + tags/paginación/comentarios) en un único `<div class="ce-max-w-content">` (`max-width:640px`), y cualquier `[ce_section]` embebido heredaba ese límite.

Solución: nueva clase `.ce-content-breakout` (`assets/css/main.css`), añadida junto a `.ce-service-content` **solo** en `single.php` y `page.php` (no en `single-servicio.php`/`single-proyecto.php`/`single-equipo.php`, que también usan `.ce-service-content` pero quedan fuera de este bug). Convierte ese wrapper en un grid de 3 columnas: el contenido normal de `the_content()` sigue centrado a 640px (mismo ancho de lectura de siempre); los hijos `.ce-section` (lo que renderiza `[ce_section]`) ocupan el ancho completo del `.ce-container`, cancelando con margen negativo el padding del contenedor exterior para quedar pixel-idénticos al Home. `.ce-max-w-content` (compartida en ~15 archivos del proyecto) **no se modificó**. El `<article>` se sacó de `.ce-max-w-content` en ambos archivos (condición necesaria para que el grid tenga ancho disponible); la meta info y los tags/paginación/comentarios siguen envueltos en `.ce-max-w-content`, sin cambio visual. El hero (`template-parts/page-hero.php`) y el Home (`front-page.php`) quedan fuera de la cadena de ancestros afectada — verificado por lectura, sin relación con este cambio. Detalle completo, alternativas descartadas (breakout vía `100vw`, vía `calc()` contra `--ce-container-max`): `docs/DECISIONS.md` D-061.

### Archivos creados / modificados (Entregable UX-6.2)
- Creados: `inc/section-shortcode.php` (raíz de `/inc`).
- Modificados: `inc/home-builder.php` (nueva función `ce_construction_get_home_section_args()`, aditiva), `front-page.php` (1 línea: usa la función anterior en vez del ternario inline, sin cambio de comportamiento), `functions.php` (1 línea nueva en `$modules`, necesaria para cargar el archivo nuevo).
- Sin cambios: `page.php` (UX-6.1, instrucción explícita de no tocarlo — verificado por diff), `template-parts/page-hero.php`, `comments.php`, todo el sistema de cotización (UX-3), el Hero de Home (UX-4), los CTA (UX-5), y todo el Sprint 8.
- Eliminados: ninguno.

### Archivos creados / modificados (Entregable UX-6.3 — fix de ancho)
- Modificados: `single.php` y `page.php` (restructuración de wrappers: `<article>` fuera de `.ce-max-w-content`, clase `ce-content-breakout` añadida a `.ce-service-content`; meta info y tags/paginación/comentarios siguen en `.ce-max-w-content` sin cambio visual), `assets/css/main.css` (nueva regla aditiva `.ce-content-breakout`; `.ce-max-w-content` intacta).
- Sin cambios: `inc/section-shortcode.php` (el shortcode nunca fue la causa del bug), `single-servicio.php`, `single-proyecto.php`, `single-equipo.php` (comparten `.ce-service-content` pero no se les añadió la nueva clase — quedan fuera de alcance de este bug), `template-parts/page-hero.php`, `front-page.php`, Sprint 8.
- Eliminados: ninguno.

### Documentación actualizada (en este cierre)
`docs/DECISIONS.md` (D-060, D-061), este mismo archivo. Sin cambios en `ARCHITECTURE.md`/`TREE.md`/`CHANGELOG.md` en este cierre (mismo criterio ya aplicado en el cierre de UX-6.1: se difieren a un punto de cierre de Sprint más significativo — ver "Pendiente explícito" abajo), ni en `CURRENT_SPRINT.md`/`QA_REPORT.md`/`HANDOFF.md` (Sprint 8, sin tocar).

### Validaciones ejecutadas
UX-6.2: sin entorno WordPress/navegador real disponible en ese momento (misma limitación metodológica ya documentada para UX-3.2/UX-6.1): balance de llaves/paréntesis y de tags `<?php` (sin `?>` de cierre, convención ya vigente en el proyecto) verificado en los 4 archivos tocados/creados, sin `php -l` disponible en este entorno. Trazado lógico manual del flujo del shortcode contra `front-page.php` (mismo `get_template_part()`, mismo registro central, mismo cálculo de `$args`).

UX-6.3: causa confirmada por el propio usuario en DevTools sobre WordPress real (`.ce-max-w-content` con `max-width:640px`, comprobado desactivando la regla manualmente) antes de implementar el fix. Balance de llaves/paréntesis/tags `<?php` verificado en `single.php`/`page.php` tras la restructuración; balance de llaves/paréntesis verificado en `main.css` completo tras la inserción.

**Pendiente de prueba funcional real en WordPress (ambos Entregables):** con caché purgada, crear/revisar una Página con `[ce_section key="about"]` y confirmar que (a) renderiza "Quiénes Somos" idéntica a la del Home, incluso con la sección desactivada en el Home Builder, y (b) ocupa el ancho completo del `.ce-container` del sitio, alineada igual que en el Home, mientras el resto del texto de la Página conserva el ancho de lectura de siempre.

### Pendiente explícito de este Entregable
- ~~`docs/CHANGELOG.md`: sin entrada añadida~~ — **resuelto en el cierre del Sprint UX-6** (D-062): entradas de UX-6.1, UX-6.2 y UX-6.3 añadidas.
- ~~`docs/TREE.md`: no refleja `inc/section-shortcode.php`~~ — **resuelto en el cierre del Sprint UX-6** (D-062): árbol actualizado.
- Prueba funcional real en WordPress de UX-6.2 (ver validaciones arriba) — **sigue pendiente**, no resuelta por la aprobación explícita de este cierre (UX-6.3 sí fue validada por el usuario en WordPress real, ver D-061).
- ~~Con UX-6.3 implementado, el Sprint UX-6 queda completo, pendiente de tu aprobación explícita de los tres~~ — **✅ resuelto: los tres aprobados explícitamente por el usuario, Sprint UX-6 formalmente cerrado (D-062).**

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
- ~~🆕 Pendiente de tu aprobación explícita: inicio de UX-6.2 (shortcode), elección de shortcode vs. bloque Gutenberg~~ — **resuelto (D-062): shortcode confirmado explícitamente, UX-6.2 y UX-6.3 aprobados.**
- Sigue pendiente: decisión sobre cuándo resolver `QA-043` (iconos sociales del header) — ver `docs/UX_CONVERSION_ANALISIS_Y_PLAN.md` §8.7. Sin cambios en esta sesión.

## Riesgos y pendientes abiertos
- ~~Investigación del HTTP 400 en el envío del formulario de cotización~~ — **cerrado**: causa confirmada como error de carga del usuario en un ZIP anterior (`inc/quote-form.php` no incluido en ese paquete), no un bug de código. Ver nota completa arriba, en la sección del Sprint UX-3.
- ~~Interacción entre `ce_quote_form_mode='modal'` y la sección `quote_form` del Home Builder (IDs duplicados)~~ — **resuelto en UX-3.2** mediante la bandera de instancia y el id dinámico en `template-parts/quote-form.php` (ver `DECISIONS.md` D-053).
- ~~"Nota de interacción con el Home Builder" de D-049 (CTA apuntando a una ancla `#ce-quote-form` inexistente si la sección se desactivaba en el Home Builder dejando el modo en `integrated`)~~ — **resuelta como efecto colateral de D-053**: los CTA ya no apuntan a esa ancla, apuntan siempre al popup (impreso globalmente por `footer.php`), independientemente del estado de esa sección en el Home Builder.
- **Vigente (documentado, no bloqueante, sin cambios desde UX-3.1):** en modo `disabled`, un botón con `ce_cta_btn_url`/`ce_hero_btn1_url` personalizado explícitamente por el administrador no se oculta (comportamiento intencional, ver `DECISIONS.md` D-049).
- **Vigente (documentado, no bloqueante):** limitación metodológica del entorno de desarrollo (sin WordPress/navegador real) — las verificaciones de UX-3.2 (incluida su continuación D-053) fueron trazado lógico manual, balance de sintaxis y `node --check`, no una prueba funcional en un navegador real. D-050 ya dejó constancia de que este tipo de verificación puede no detectar comportamientos específicos del runtime de WordPress; se aplica el mismo criterio de precaución a la interacción JS del modal multi-instancia hasta que se pruebe en un entorno real.
- Pendiente de confirmación del usuario: bump de `style.css` a `0.8.6` (propuesto, no aplicado — ver nota de versionado arriba).
- `HANDOFF.md` sigue pendiente de actualización — se evaluará en un punto de cierre de sesión/Sprint más significativo (criterio D-034 ya vigente en el proyecto). El cierre formal del Sprint UX-3 (una vez apruebes UX-3.2) es un candidato razonable para ese punto de actualización.
- Ningún riesgo detectado que afecte al Sprint 8 pausado; ningún archivo compartido con el Sprint 8 fue tocado en este Entregable.

---

## ✅ Cierre del Sprint UX-6 (esta sesión)

UX-6.1, UX-6.2 y UX-6.3 quedan **aprobados explícitamente por el usuario** en esta sesión (mecanismo de UX-6.2 confirmado: shortcode, no bloque Gutenberg). El **Sprint UX-6 queda formalmente cerrado**, conforme a D-038. Ningún archivo de código fue tocado en este cierre — el código de UX-6.2/UX-6.3 ya estaba presente y correcto en el ZIP revisado; se verificó por lectura antes de dar el cierre por bueno. Detalle completo: `docs/DECISIONS.md` D-062.

**Documentación actualizada en este cierre** (diferida hasta este punto, ver criterio D-034 de "punto de cierre de Sprint más significativo"): `CHANGELOG.md` (entradas de UX-6.1, UX-6.2, UX-6.3), `TREE.md` (`page.php`, `inc/section-shortcode.php`, `single.php`, `assets/css/main.css`, `inc/home-builder.php`, `front-page.php`, `functions.php`), `DECISIONS.md` (D-062), este mismo archivo. Sin cambios en `PROJECT_STATUS.md`/`HANDOFF.md`/`QA_REPORT.md`/`CURRENT_SPRINT.md` (Sprint 8, sin tocar) ni en `ARCHITECTURE.md` (sin cambio de arquitectura respecto a lo ya descrito en D-059/D-060/D-061).

**Pendiente, sin cambios de alcance:**
- Prueba funcional real en WordPress de UX-6.2 (UX-6.3 sí fue validada por el usuario en WordPress real, ver D-061).
- Bump de `style.css` a `0.8.6` (propuesto, no aplicado — sin autorización en esta sesión).
- `QA-043` (iconos sociales del header) — sin decisión sobre cuándo resolverlo.

### Próximo Sprint — decisión tomada, actualizado tras el inicio de UX-7

Con el Sprint UX-6 cerrado, el usuario priorizó e inició el **Sprint UX-7** (Consistencia y configurabilidad), en modalidad Entregable por Entregable — ver la sección dedicada más abajo para el detalle y el estado de UX-7.1.

**UX-5.2** — Documentación de "objetivo de plantilla" (landing/corporativo/construcción/remodelación/servicios) — sigue **sin iniciar**, pendiente del plan original; no compite con UX-7 salvo que el usuario decida reordenar.

---

## Sprint UX-7 — "Consistencia y configurabilidad: Hero, CTA, Header/Footer" — Estado: **En curso.**

**Modalidad de aprobación elegida por el usuario:** Entregable por Entregable (no el Sprint completo de una vez), conforme a D-038.

| Entregable | Objetivo | Estado |
|---|---|---|
| UX-7.1 | Unificación del Hero (Home + interior) | ✅ **Aprobado explícitamente por el usuario** en la sesión anterior, junto con la instrucción de iniciar UX-7.2. |
| UX-7.2 | Layout de Hero de 1/2/3 columnas + Quote Form opcional en Hero | ✅ **Aprobado explícitamente por el usuario** en esta sesión (incluye el ajuste puntual D-065), junto con la instrucción de iniciar UX-7.3. Ver `docs/DECISIONS.md` D-066. |
| UX-7.3 | Aprovechamiento de espacios vacíos en sidebars | ✅ **Aprobado explícitamente por el usuario** en esta sesión, junto con la instrucción de iniciar UX-7.4 en modalidad Entregable por Entregable. Ver `docs/DECISIONS.md` D-067. |
| UX-7.4 | CTA: icono y color de botón configurables | ✅ **Aprobado explícitamente por el usuario** en esta sesión, junto con la instrucción de iniciar UX-7.5 en modalidad Entregable por Entregable. Ver `docs/DECISIONS.md` D-068. |
| UX-7.5 | Logo independiente Header/Footer | ✅ **Aprobado explícitamente por el usuario** en esta sesión, junto con la instrucción de iniciar UX-7.6 en modalidad Entregable por Entregable. Ver `docs/DECISIONS.md` D-069. |
| UX-7.6 | Estadísticas configurables desde el Customizer | ✅ **Aprobado explícitamente por el usuario** en esta sesión, junto con la instrucción de iniciar UX-7.7 en modalidad Entregable por Entregable. Ver `docs/DECISIONS.md` D-070. |
| UX-7.7 | Franja de insignias de confianza / licencias — 🆕 nota (D-065): una vez construido, el componente de insignias/bullets también debe quedar disponible para la tarjeta del Quote Form del Hero (`.ce-hero-quote-card`) | 🟡 **Entregado — pendiente de tu aprobación explícita.** Ver nota debajo de esta tabla y `docs/DECISIONS.md` D-071. |
| UX-7.8 | Testimonio en video | Sin iniciar |
| UX-7.9 | Bloque de financiamiento / opciones de pago | Sin iniciar |
| UX-7.10 🆕 | Popup de oferta / captura de leads al cargar la página — Entregable nuevo (D-065), no una extensión de UX-7.2. Preguntas abiertas antes de implementar: frecuencia de aparición (cada carga vs. limitada por cookie/transient), relación con el modal existente de Quote Form (`ce_quote_form_mode = 'modal'`) — ¿coexisten como 2 modales distintos, o se fusionan?, y contenido del popup (¿reutiliza el componente de insignias de UX-7.7, o es solo texto/CTA?) | ⬜ Propuesto, sin iniciar, sin aprobar |

### ✅ Cierre de UX-7.6 (esta sesión)

UX-7.6 queda **aprobado explícitamente por el usuario** en esta sesión, junto con la instrucción de iniciar UX-7.7 en modalidad Entregable por Entregable (D-038). Ningún archivo de código adicional fue tocado en este cierre. Detalle: `docs/DECISIONS.md` D-070.

### 🆕 UX-7.7 entregado — pendiente de tu aprobación explícita

**Verificación previa de coherencia documental (paso obligatorio antes de tocar código):** se releyó `docs/CURRENT_UX_SPRINT.md` (esta tabla) y `docs/UX_CONVERSION_ANALISIS_Y_PLAN.md` §8.4/§8.8 (alcance de UX-7.7). Se confirmó la nota de D-065 (integración pendiente en `.ce-hero-quote-card`) como parte normal del alcance a resolver en este Entregable, no como una decisión fuera de alcance. **No se encontró ninguna discrepancia.**

Se implementó UX-7.7 conforme al alcance de `docs/UX_CONVERSION_ANALISIS_Y_PLAN.md` §8.4: nueva sección del Home Builder (`template-parts/trust-badges.php`), con un control repeater del Customizer ("CE: Insignias de Confianza") — mismo patrón general que el repeater de Estadísticas (UX-7.6), combinado con el selector de imagen vía `wp.media` ya usado por el slider del Hero (UX-4.2), porque cada insignia admite imagen opcional + etiqueta + número de licencia opcional + enlace de verificación opcional. A diferencia de Estadísticas, esta sección no tenía ningún contenido previo que preservar: por defecto está vacía y oculta hasta que el administrador añade su primera insignia. Se registra en `inc/home-builder.php` junto al resto, quedando disponible también vía `[ce_section key="trust_badges"]` (UX-6.2). Detalle técnico completo, incluidas las 8 decisiones tomadas y las alternativas descartadas: `docs/DECISIONS.md` D-071.

**Resolución de la nota pendiente de D-065 (integración en el Hero):** `template-parts/trust-badges.php` gana un modo compacto (`$args['compact']`), invocado por `template-parts/quote-form.php` exclusivamente en el contexto `'hero'`, justo debajo del formulario dentro de `.ce-hero-quote-card` — reutiliza el mismo componente de insignias, sin crear un segundo sistema. Documentado explícitamente en D-071 para que puedas corregirlo si la forma concreta no es la que esperabas.

**Resumen del comportamiento nuevo:**
- Nueva sección "CE: Insignias de Confianza" en el Customizer, con un repeater: cada fila tiene una imagen opcional (botón "Seleccionar imagen", vía la biblioteca de Medios), Etiqueta, Número de licencia (opcional) y Enlace de verificación (opcional), más botones para mover antes/después y quitar la fila, y un botón "Añadir insignia" al final.
- Por defecto (sin tocar nada): el panel aparece **vacío** y la franja de insignias no se muestra en ningún punto del sitio — cero cambio visual hasta que el administrador configure al menos una insignia y publique.
- Si hay al menos una insignia configurada: aparece como una franja de tarjetas en el Home (si la sección `trust_badges` se activa en el Home Builder — no está activa por defecto, mismo criterio que Equipo/Clientes/FAQ) y/o dentro de la página vía `[ce_section key="trust_badges"]`, y también como una fila compacta de iconos/miniaturas dentro de la tarjeta del formulario del Hero, si ese formulario está activado (`ce_hero_show_quote_form`, UX-7.2).
- Sin imagen configurada en una insignia: se muestra un icono genérico de escudo + la etiqueta de texto, nunca un espacio vacío.

### Archivos creados / modificados (Sprint UX-7, Entregable UX-7.7)
- **Nuevos:** `template-parts/trust-badges.php`, `assets/js/admin-trust-badges.js`.
- `inc/helpers.php` — 3 funciones nuevas (`ce_construction_decode_trust_badges()`, `ce_construction_get_trust_badges()`, `ce_construction_trust_badge_title()`).
- `inc/home-builder.php` — registro de la nueva clave `trust_badges`.
- `inc/customizer.php` — nueva sección "CE: Insignias de Confianza" + 1 `add_setting`/`add_control` (control custom `CE_Customize_Trust_Badges_Control`), `sanitize_callback` nuevo, y la definición de la clase del control + estilos admin (patrón combinado de `ce_hero_slides` y `ce_stats_custom_items`).
- `inc/enqueue.php` — nueva función de encolado del script admin (`customize_controls_enqueue_scripts`).
- `template-parts/quote-form.php` — llamada al modo compacto de `trust-badges.php`, exclusivamente en el contexto `'hero'`, justo después del `<form>`.
- `assets/css/main.css` — sección 30, aditiva (`.ce-trust-badges-list`, `.ce-trust-badge`, y sus variantes `--compact`).
- Sin cambios: Home Builder (fuera del registro nuevo), sistema de cotización (`inc/quote-form.php`), CTA, sidebars, Hero (fuera de la integración puntual ya descrita), logo Header/Footer, Sprint 8, `style.css` (sin bump).

### Próximo paso
Con UX-7.7 entregado, el Sprint UX-7 **no avanza a UX-7.8** sin tu aprobación explícita de UX-7.7 (D-038, modalidad Entregable por Entregable ya elegida por el usuario).

### 🆕 Actualización tras la aprobación de UX-7.7: requerimiento urgente y nuevo Sprint UX-10 (esta sesión)

Con UX-7.7 ya aprobado (ver arriba), el usuario solicitó evaluar — como **requerimiento urgente para producción** — una página de Testimonios que combine el CPT propio con Google Reviews (Opción Híbrida C confirmada: proveedor tipo Trustindex + normalización al formato de card ya existente, o Opción B como repliegue). Se confirmó la factibilidad arquitectónica sin conflicto de fondo, pero se detectó un **conflicto de secuencia real con UX-7.8** (ambos Entregables tocan `inc/cpt-testimonios.php`, `template-parts/testimonials.php` y `content-testimonio-card.php`). Registrado como **Sprint UX-10** (nuevo, ver `docs/UX_CONVERSION_ANALISIS_Y_PLAN.md` §8.4 para el alcance completo por Entregable, y `docs/DECISIONS.md` D-072 para el análisis del conflicto y las 3 opciones de secuencia presentadas). **El Sprint UX-7 sigue exactamente donde estaba** (pausado, pendiente de tu aprobación de UX-7.7 → UX-7.8) — esta sección es puramente documental, ningún archivo de código fue tocado.

**Pendiente de tu decisión explícita antes de iniciar el primer Entregable de UX-10 (UX-10.1):** elegir una de las 3 opciones de secuencia de D-072. Recomendación: Opción 2 (UX-10 ahora, en paralelo, con UX-7 pausado en UX-7.7 — mismo patrón ya usado con el Sprint 8).

---

## 🆕 Sprint UX-10 — "Página de Testimonios: CPT propio + Google Reviews" — Estado: **En curso.**

**Secuencia confirmada por el usuario:** Opción 2 de D-072. Sprint UX-10 se ejecuta ahora, en paralelo, con el **Sprint UX-7 pausado exactamente donde está** (UX-7.7 aprobado, UX-7.8 sin iniciar). UX-7 se retoma en UX-7.8 solo después de que UX-10 esté completo. Esta pausa no modifica nada de la tabla del Sprint UX-7 de arriba, que permanece como referencia congelada del punto de pausa.

| Entregable | Objetivo | Estado |
|---|---|---|
| UX-10.1 | Página dedicada de Testimonios: `page.php` + `[ce_section key="testimonials_full"]` (decisión de diseño confirmada por el usuario: Página + shortcode, no `archive-testimonio.php` nativo), grid completo con paginación, solo CPT propio `testimonio` | 🟡 **Entregado — pendiente de tu aprobación explícita.** Ver `docs/DECISIONS.md` D-073. |
| UX-10.2 | CTA "Ver todos los testimonios" en el teaser del Home, enlazando a la página de UX-10.1 | 🟡 **Entregado — pendiente de tu aprobación explícita.** Cerrado en el mismo Entregable que UX-10.1 (mismo enlace de destino, por instrucción explícita del usuario). Ver `docs/DECISIONS.md` D-073. |
| UX-10.3 | Integración de Google Reviews vía Trustindex (proveedor confirmado por el usuario, debe soportar cualquier plan contratado) | ⬜ **No inicia sin tu aprobación explícita de UX-10.1 + UX-10.2** (instrucción explícita del usuario). |
| UX-10.4 | Selector de fuente en el Customizer (propios/Google/ambos) | ⬜ No iniciado — depende de UX-10.1 y UX-10.3. |

### Trabajo realizado (Entregables UX-10.1 + UX-10.2)

Nuevo `template-parts/testimonials-full.php`: grid (`.ce-grid.ce-grid--3`, mismo patrón que `archive-clientes.php`) + paginación de un `WP_Query` propio (acotado a `post_type => testimonio`), reutilizando sin cambios `content-testimonio-card.php` (`$args['compact'] = true`, la misma card que ya usan el teaser del Home, los sidebars de Servicios/Proyectos y el selector aleatorio de sidebar) y `template-parts/no-results.php` para el estado vacío. Registrado como nueva clave `testimonials_full` en `inc/home-builder.php` — disponible de inmediato vía `[ce_section key="testimonials_full"]` (mecanismo ya existente desde UX-6.2, sin tocar `inc/section-shortcode.php`). El "hero interno" pedido en el criterio de aceptación lo resuelve `page.php` (ya existente desde UX-6.1: hereda automáticamente el título/extracto/imagen destacada de la Página que cree el administrador) — `testimonials-full.php` no duplica ese hero, solo aporta su propio encabezado de sección (eyebrow + título), igual que el resto de secciones reutilizables fuera del Home (`team.php`, `clients.php`, `faq.php`, `trust-badges.php`).

`template-parts/testimonials.php` (teaser del Home, sin cambios en su query/slider) gana el CTA "Ver todos los testimonios" al cierre, condicionado a un nuevo theme_mod de texto `ce_testimonials_page_url` (nueva sección "CE: Testimonios" en el Customizer) — sin configurar, el CTA no se imprime, cero cambio visual por defecto.

**Este Entregable NO tocó** `inc/cpt-testimonios.php` ni `content-testimonio-card.php` — los 2 de los 3 archivos señalados en D-072 como conflicto de secuencia con UX-7.8 permanecen exactamente como estaban. Tampoco tocó el sistema de cotización, el resto del Home Builder, ni el Sprint 8.

Detalle técnico completo (5 decisiones + alternativas descartadas): `docs/DECISIONS.md` D-073.

### Archivos creados / modificados (Entregables UX-10.1 + UX-10.2)
- **Creado:** `template-parts/testimonials-full.php`.
- **Modificados:** `inc/home-builder.php` (registro aditivo de la clave `testimonials_full`), `template-parts/testimonials.php` (CTA condicional al final; query/slider/card sin cambios), `inc/customizer.php` (nueva sección "CE: Testimonios", 1 `add_setting`/`add_control` tipo `url`).
- **Sin cambios:** `inc/cpt-testimonios.php`, `template-parts/content-testimonio-card.php`, `inc/section-shortcode.php`, `template-parts/sidebar-servicios.php`, `template-parts/sidebar-proyectos.php`, todo el sistema de cotización, todo el Sprint 8, `style.css` (sin bump).
- Eliminados: ninguno.

### Documentación actualizada (en este cierre)
`docs/DECISIONS.md` (D-073), `docs/UX_CONVERSION_ANALISIS_Y_PLAN.md` (§8.4: tabla de UX-10 actualizada, decisión de diseño y proveedor ya no pendientes), este mismo archivo. **Diferido a un punto de cierre de Sprint más significativo (criterio D-034):** `CHANGELOG.md`, `TREE.md`, `TODO.md`, `PROJECT_STATUS.md`, `HANDOFF.md`. Sin cambios en `ARCHITECTURE.md` (sin cambio de arquitectura: reutiliza el mecanismo de `page.php` + shortcode ya documentado desde UX-6) ni en `CURRENT_SPRINT.md`/`QA_REPORT.md` (Sprint 8, sin tocar).

### Validaciones ejecutadas
Sin entorno WordPress/navegador real disponible (misma limitación metodológica de Entregables anteriores): balance de tags `<?php`/llaves/paréntesis verificado en los 3 archivos PHP tocados/creados. Trazado lógico manual del flujo del shortcode contra el registro nuevo (misma ruta que ya resuelve `[ce_section key="trust_badges"]`) y de la paginación del `WP_Query` secundario (`get_query_var('paged')`/`get_query_var('page')` + `paginate_links()` con `base`/`format` explícitos). **Pendiente de prueba funcional real en WordPress**, incluida: crear la Página de Testimonios con el shortcode y confirmar el hero heredado, la paginación con más de 9 testimonios publicados, y la aparición del CTA en el Home tras configurar `ce_testimonials_page_url`.

### Próximo paso
Con UX-10.1 + UX-10.2 entregados, **UX-10.3 (integración de Google Reviews vía Trustindex) no inicia sin tu aprobación explícita de ambos** (D-038, instrucción explícita del usuario en esta misma sesión). El proveedor concreto ya está confirmado (Trustindex, compatible con cualquier plan contratado) — al aprobar, el siguiente paso natural es que indiques las credenciales/plan concretos de la cuenta para poder iniciar UX-10.3.

### ✅ Cierre de UX-7.4 (esta sesión)

UX-7.4 queda **aprobado explícitamente por el usuario** en esta sesión, junto con la instrucción de iniciar UX-7.5 en modalidad Entregable por Entregable (D-038). Ningún archivo de código adicional fue tocado en este cierre. Detalle: `docs/DECISIONS.md` D-068.

### 🆕 UX-7.5 entregado — pendiente de tu aprobación explícita (histórico — ya aprobado, ver más arriba)

Se implementó UX-7.5 conforme al alcance de `docs/UX_CONVERSION_ANALISIS_Y_PLAN.md` §8.4 (nuevo theme_mod opcional `ce_footer_logo`, con fallback automático al logo del sitio si no se configura), sin ambigüedad que requiriera detenerse a consultar. Detalle técnico completo, incluidas las 5 decisiones tomadas y las alternativas descartadas: `docs/DECISIONS.md` D-069.

**Resumen del comportamiento nuevo:**
- Nuevo campo "Logo del Footer (opcional)" en "CE: Footer" (Customizer) — pensado para una variante de logo distinta para el fondo oscuro del footer (p. ej. una versión en blanco/clara).
- Si no se configura: el footer sigue mostrando el logo nativo del sitio ("Identidad del sitio"), exactamente igual que antes de este Entregable — **cero cambio visual por defecto**.
- `header.php` **no fue tocado** — sigue usando exclusivamente el logo nativo del sitio, conforme al alcance explícito del plan.
- Nuevo helper `ce_render_footer_logo()` (`inc/helpers.php`) centraliza los 3 niveles de fallback (logo del footer → logo nativo del sitio → nombre del sitio en texto).

### Archivos creados / modificados (Sprint UX-7, Entregable UX-7.5)
- **Nuevo:** ninguno.
- `inc/customizer.php` — 1 control nuevo (`ce_footer_logo`, `WP_Customize_Media_Control`) en la sección "CE: Footer" ya existente.
- `inc/helpers.php` — nueva función `ce_render_footer_logo()`.
- `footer.php` — bloque de logo reemplazado por una llamada a la nueva función; resto del archivo sin cambios.
- Sin cambios: `header.php`, `assets/css/main.css` (la regla `.ce-footer__brand img` ya existente cubre el nuevo markup), sidebars, Home Builder, CTA, sistema de cotización, Sprint 8, `style.css` (sin bump).

### Próximo paso (histórico — superado, ver estado vigente arriba)
~~Con UX-7.5 entregado, el Sprint UX-7 no avanza a UX-7.6 sin tu aprobación explícita de UX-7.5~~ — **resuelto:** UX-7.5 aprobado explícitamente por el usuario en la sesión siguiente, junto con la instrucción de iniciar UX-7.6. UX-7.6 ya fue entregado (ver sección de arriba) y es ahora el que está pendiente de aprobación.

### Trabajo realizado (Entregable UX-7.2)

Antes de implementar se detectó una ambigüedad real: `docs/UX_CONVERSION_ANALISIS_Y_PLAN.md` §8.4 no especifica qué representa "1/2/3 columnas" ni cómo se activa el Quote Form embebido. Conforme a la instrucción explícita de esta sesión de detenerse ante cualquier decisión fuera del alcance confirmado, se consultó al usuario antes de tocar código (3 preguntas, ver `DECISIONS.md` D-064 para el detalle completo de las respuestas y su resolución técnica).

`ce_hero_layout` (nuevo theme_mod, Customizer, sección "CE: Sección Hero", solo Home) representa la proporción de ancho entre el contenido y el Quote Form: `'1'` = ancho completo (por defecto, sin cambio de comportamiento), `'2'` = 7/5, `'3'` = 8/4. `ce_hero_show_quote_form` (checkbox independiente, misma sección) activa el formulario embebido, combinable con cualquier layout. `template-parts/quote-form.php` gana un tercer contexto (`$args['context'] = 'hero'`), reutilizando exactamente el mismo `<form>`/handler/nonce de siempre (`inc/quote-form.php`, sin tocar), con id fijo `ce-quote-form-hero` sin colisión con la instancia integrada ni el modal. `assets/js/main.js` no requirió ningún cambio (ya generaliza por clase, D-053). Ver `DECISIONS.md` D-064 para el detalle técnico completo, incluida la resolución explícita de la combinación "layout=1 + formulario activado" (se apilan, ambos a ancho completo).

**Este Entregable no tocó** `inc/quote-form.php` (handler AJAX), el Home Builder (UX-1/UX-2), el shortcode/`page.php` (UX-6), el Hero interno (`template-parts/page-hero.php`, permanece de una columna por decisión explícita del usuario), ni el Sprint 8.

### Archivos creados / modificados (Sprint UX-7, Entregable UX-7.2 — incluye el ajuste puntual D-065)
- Creados: ninguno.
- Modificados: `inc/customizer.php` (2 controles nuevos + 2 sanitize callbacks, dentro/después de la sección Hero ya existente), `template-parts/hero.php` (wrapper de layout condicional + slot de Quote Form; 🆕 D-065: `.ce-hero--has-form` condicional + omisión de `.ce-hero__scroll`), `template-parts/quote-form.php` (tercer contexto `'hero'`; 🆕 D-065: badge de "Respuesta en 24h" en ese contexto), `assets/css/main.css` (sección 28, aditiva; 🆕 D-065: sección "28 bis", aditiva, con los 6 puntos del ajuste puntual).
- Sin cambios: `inc/quote-form.php`, `template-parts/page-hero.php`, `assets/js/main.js`, Home Builder (UX-1/UX-2), shortcode (UX-6), Sprint 8, `style.css` (sin bump).
- Eliminados: ninguno.

### Documentación actualizada (en este cierre)
`DECISIONS.md` (D-064, D-065), `UX_CONVERSION_ANALISIS_Y_PLAN.md` (§8.4: nota en UX-7.7 + entrada UX-7.10), este mismo archivo (tabla de UX-7 + esta sección). **Diferido a un punto de cierre de Sprint más significativo (criterio D-034, mismo diferimiento ya aplicado en UX-7.1):** `CHANGELOG.md`, `TREE.md`.

### Validaciones ejecutadas
Sin entorno WordPress/navegador real disponible (misma limitación metodológica documentada en Entregables anteriores): balance de tags `<?php`/llaves/paréntesis verificado en los 3 archivos PHP tocados y en `main.css` completo tras la inserción; trazado lógico manual de los 3 contextos de `quote-form.php` (normal/modal/hero) confirmando que ninguna combinación duplica un `id` en el DOM. **Pendiente de prueba funcional real en WordPress**, incluida la verificación visual de los 2 layouts de columnas en desktop/mobile.

**🆕 Ajuste puntual D-065 (misma limitación metodológica):** balance de llaves/paréntesis/tags `<?php` reverificado en `template-parts/hero.php` y `template-parts/quote-form.php` tras los cambios, y balance de llaves `{}` reverificado en `assets/css/main.css` completo (401/401) tras la inserción de la sección "28 bis". **Pendiente de prueba funcional real en WordPress**, incluida la verificación visual del breakpoint intermedio 768–991.98px y de `.ce-hero--has-form` en los 3 tipos de fondo (imagen/video/slider).

### 🆕 Ajuste puntual dentro de UX-7.2 (misma sesión, aún sin aprobar)

Antes de recibir tu aprobación de UX-7.2, se ejecutó un ajuste puntual de bajo riesgo sobre los mismos archivos ya entregados (amparado por la excepción de `docs/UX_CONVERSION_ANALISIS_Y_PLAN.md` §8.2/§8.4): breakpoint intermedio 768–991.98px (`6fr/6fr`) para los layouts de 2 columnas, modificador `.ce-hero--has-form` (altura automática en vez de forzar `88vh`/`92vh`), omisión de `.ce-hero__scroll` con el formulario activo, franja de acento + badge corto ("Respuesta en 24h") en `.ce-hero-quote-card`, spacing compacto del formulario solo en ese contexto, y un breakpoint `max-width:575.98px` adicional para la tarjeta. **No se tocó** `inc/quote-form.php`, no se creó variante corta del formulario (D-056 vigente), y no se agregó ninguna animación de entrada. Ver `docs/DECISIONS.md` D-065 para el detalle técnico completo, incluidas 2 decisiones de alcance documentadas sin implementar (componente de insignias → absorbido en UX-7.7; popup de oferta → nuevo `UX-7.10`, fila añadida a la tabla de arriba).

**Archivos adicionales tocados por este ajuste (sobre los ya listados arriba para UX-7.2):** `template-parts/quote-form.php` (badge en el contexto `'hero'`, ver D-065) — no estaba en la lista original de UX-7.2 porque en ese Entregable ese archivo solo ganó el contexto en sí, sin badge todavía.

Este ajuste **no cambia el estado de aprobación de UX-7.2**: sigue "Entregado — pendiente de tu aprobación explícita", ahora incluyendo también este ajuste (D-038).

### Próximo Entregable
Con UX-7.2 (D-064 + el ajuste D-065) entregado, el Sprint UX-7 **no avanza a UX-7.3** sin tu aprobación explícita de UX-7.2 completo (D-038, modalidad Entregable por Entregable ya elegida por el usuario).

### Trabajo realizado (Entregable UX-7.1)

`template-parts/hero.php` (Home) y `template-parts/page-hero.php` (Hero interno) comparten ahora la resolución de video/slider de `ce_hero_type` vía una función nueva y única, `ce_construction_get_hero_media_state()` (`inc/helpers.php`) — sin reimplementar esa lógica en un segundo archivo. El Hero interno gana capacidad de video/slider (en modo `video`/`slider`, usa el mismo `ce_hero_video`/`ce_hero_slides` globales que el Home; en modo `image`, por defecto, sigue usando la imagen destacada propia de cada Página/entrada, sin cambio de comportamiento). El overlay de ambos Heros pasa a compartir la misma fuente de gradiente en CSS (`--ce-hero-overlay-gradient`) y la misma opacidad configurable (`ce_hero_overlay_opacity`, ya existente desde UX-4.1) — antes el Hero interno tenía un gradiente propio, ligeramente distinto, y sin control de opacidad. Ver `DECISIONS.md` D-063 para el detalle completo, incluidas las alternativas descartadas.

**Cambio visual esperado (no un bug):** el overlay del Hero interno pasa de un gradiente de 2 stops a uno de 3 stops (el mismo del Home) — resultado intencional de la consistencia pedida por este Entregable.

### Archivos creados / modificados (Sprint UX-7, Entregable UX-7.1)
- Creados: ninguno.
- Modificados: `inc/helpers.php` (nueva función `ce_construction_get_hero_media_state()`, aditiva), `template-parts/hero.php` (refactor interno, sin cambio de comportamiento), `template-parts/page-hero.php` (nueva capacidad video/slider + overlay compartido, modo imagen sin cambios), `assets/css/main.css` (gradiente de overlay extraído a variable compartida; `.ce-page-hero__overlay` gana `background`/`opacity`; comentarios de las secciones 26/27 actualizados), `inc/customizer.php` (descripciones de `ce_hero_type` y `ce_hero_overlay_opacity` actualizadas, sin cambio de lógica).
- Sin cambios: Home Builder (UX-1/UX-2), sistema de cotización (UX-3), shortcode/`page.php` (UX-6), Sprint 8, `style.css` (sin bump).
- Eliminados: ninguno.

### Documentación actualizada (en este cierre)
`DECISIONS.md` (D-063), este mismo archivo. **Diferido a un punto de cierre de Sprint más significativo (criterio D-034):** `CHANGELOG.md`, `TREE.md`.

### Validaciones ejecutadas
Sin entorno WordPress/navegador real disponible (misma limitación metodológica documentada en Entregables anteriores): balance de tags `<?php`/llaves/paréntesis en los 3 archivos PHP tocados/creados y en `main.css` completo; trazado lógico manual de los 9 llamadores existentes de `page-hero.php`, confirmando que ninguno pasa argumentos distintos a los ya soportados. **Pendiente de prueba funcional real en WordPress.**

### Próximo Entregable (histórico — superado, ver estado vigente arriba)
~~Con UX-7.1 entregado, el Sprint UX-7 no avanza a UX-7.2 sin tu aprobación explícita de UX-7.1~~ — **resuelto:** UX-7.1 aprobado explícitamente por el usuario en la sesión siguiente, junto con la instrucción de iniciar UX-7.2 directamente. UX-7.2 ya fue entregado (ver sección de arriba) y es ahora el que está pendiente de aprobación.

### Prompt documental para el siguiente Sprint UX (preparado, sin ejecutar)

```
CE Construction — Sprint UX-6 CERRADO Y APROBADO (UX-6.1 + UX-6.2 + UX-6.3),
ver docs/CURRENT_UX_SPRINT.md y docs/DECISIONS.md D-062.

Te adjunto el ZIP con el estado actual del tema y docs/CURRENT_UX_SPRINT.md,
docs/DECISIONS.md, docs/CHANGELOG.md, docs/TREE.md,
docs/UX_CONVERSION_ANALISIS_Y_PLAN.md para el detalle completo.

Quiero continuar la fase "Optimización UX / Conversión" con:
[elige uno — ninguno está aprobado todavía]
  (a) Sprint UX-5.2 — Documentación de "objetivo de plantilla"
      (landing/corporativo/construcción/remodelación/servicios).
  (b) Sprint UX-7 — Consistencia y configurabilidad (Hero Home/interior,
      columnas + Quote Form en Hero, sidebars, CTA icono/color, logo
      independiente Header/Footer, + UX-7.6 a UX-7.9 del benchmark
      competitivo DayBrook Homes / Re-Bath — ver DECISIONS.md D-058).
      Indica si apruebas el Sprint completo o quieres aprobarlo
      Entregable por Entregable (UX-7.1, UX-7.2, ...).

Antes de implementar: analiza la documentación .md relevante y confirma
coherencia con CURRENT_UX_SPRINT.md. Si encuentras documentación
desactualizada, actualízala. Si hay discrepancia, detente y consúltame.
Si aparece alguna decisión fuera del alcance indicado arriba, detente y
consúltame antes de tocar código. Aplica D-038 (aprobación explícita
obligatoria por Entregable) en todo momento. Entrega archivos completos
con ruta exacta, no diffs.
```
