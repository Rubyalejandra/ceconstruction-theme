# CE Construction — Fase "Optimización UX / Conversión"
## Análisis arquitectónico + Plan de Sprints y Entregables (para aprobación)

> Este documento corresponde a la **Fase de Análisis y Planificación** (secciones 1 y 16 del brief). No se ha implementado ningún cambio de código. No se ha tocado ningún archivo del tema ni de `docs/`.

> **📌 ADENDA (post UX-5.1) — ver §8 "Auditoría UX/Arquitectura post UX-5.1 y roadmap ampliado".** Los Sprints UX-1 a UX-5 descritos en las secciones 1 a 7 de este documento ya están **completados y cerrados** (ver `docs/CURRENT_UX_SPRINT.md` para el detalle de cada Entregable y `docs/DECISIONS.md` D-045 a D-057). Las secciones 1 a 7 se conservan **sin modificar, como registro histórico** de la planificación original — no reflejan el estado actual de decisiones (por ejemplo, R-1 sobre `quote-form.php` ya quedó resuelto en UX-1.1/UX-3). El estado vigente del roadmap, incluidos los Sprints nuevos UX-6/UX-7/UX-8 y la renumeración del Sprint Responsive a UX-9, está en §8.

---

## 0. Trazabilidad y estado del Sprint 8 (pausado, no cerrado)

Verificado contra el zip `ce-construction-theme_1208202.zip` (estado real, v0.8.1):

- Sprint 7: ✅ COMPLETADO (4/4 Entregables aprobados).
- **Sprint 8 "Cierre de Hallazgos QA": EN CURSO.**
  - Entregable 8.1: ✅ Completado y aprobado.
  - **Entregable 8.2 (QA-030 — cache-busting de assets): 🟡 Entregado, pendiente de tu aprobación final.** Código ya presente en `functions.php`, `inc/enqueue.php`, `style.css` (v0.8.1).
  - Entregables 8.3 a 8.7: propuestos, sin iniciar (dependen de decisiones arquitectónicas tuyas: QA-031, R-4, etc.).

**Este documento NO modifica ese estado.** QA-030/Entregable 8.2 sigue exactamente como está: entregado, esperando tu aprobación explícita. Cuando la apruebes, el Sprint 8 se retoma en 8.3 desde donde quedó — independientemente de cuántos Sprints de esta nueva fase se ejecuten mientras tanto.

La nueva fase se numerará de forma independiente (propuesta: **Sprint UX-1, UX-2, UX-3...**) para no colisionar con la numeración 8.x ya reservada por el Sprint pausado.

---

## 1. Hallazgo bloqueante detectado en la revisión previa

Antes de diseñar nada, se revisó el zip contra las referencias reales del código (regla del brief: "identifica cualquier funcionalidad existente que pueda verse afectada").

**`template-parts/quote-form.php` no existe en el zip entregado**, pese a ser invocado en 4 puntos activos del tema:

```
front-page.php:      get_template_part( 'template-parts/quote-form' );
single-servicio.php:  get_template_part( 'template-parts/quote-form' );
single-proyecto.php:  get_template_part( 'template-parts/quote-form' );
```
Y referenciado por ancla `#ce-quote-form` desde 6 puntos más (`header.php` x2, `template-parts/cta.php`, `template-parts/hero.php`, `template-parts/sidebar-servicios.php`, `template-parts/sidebar-proyectos.php`, `footer.php`).

`get_template_part()` sobre un archivo inexistente no rompe la página (WordPress lo ignora silenciosamente), pero **hoy, en el estado real del zip, la sección de cotización integrada simplemente no se renderiza** — todos esos botones "Cotizar" apuntan a un ancla `#ce-quote-form` que no existe en el DOM.

Esto es exactamente el componente central del punto 5 del brief (Modo de formulario de cotización), así que **necesito confirmar antes de planificar el Entregable correspondiente**:

- (a) ¿El archivo se omitió al generar el zip (por tamaño/selección de archivos) y sigue existiendo en tu copia de trabajo?, o
- (b) ¿Falta genuinamente y debo reconstruirlo como parte de esta fase (reutilizando el markup ya documentado en `ARCHITECTURE.md` sección 8 y el contrato de 3 partes `inc/quote-form.php` ↔ `template-parts/quote-form.php` ↔ `ModuleQuoteForm` de `main.js`)?

No voy a asumir ninguna de las dos: lo marco como pregunta abierta en el plan (ver Sprint UX-1, Entregable UX-1.1) y sigo con el resto del análisis, que no depende de esto.

---

## 2. Arquitectura actual relevante

### 2.1 Home (`front-page.php`)
Orden fijo, hardcodeado, de 10 `get_template_part()` secuenciales. Cada sección:
- vive en su propio archivo en `template-parts/`,
- se auto-oculta si su CPT no tiene contenido (`ce_cpt_has_posts()`),
- no tiene ningún concepto de "activo/inactivo" ni "posición" — el orden es el orden del código fuente.

### 2.2 Secciones existentes ↔ mapeo con el brief
| Sección pedida en el brief | Template-part existente | Estado |
|---|---|---|
| Hero | `template-parts/hero.php` | ✅ Existe (imagen estática vía Customizer) |
| About | `template-parts/about.php` | ✅ Existe |
| Services | `template-parts/services.php` | ✅ Existe |
| Projects | `template-parts/projects.php` | ✅ Existe |
| Stats | `template-parts/stats.php` | ✅ Existe |
| Why Us | `template-parts/why-us.php` | ✅ Existe |
| Testimonials | `template-parts/testimonials.php` | ✅ Existe |
| Gallery | `template-parts/gallery.php` | ✅ Existe |
| Team | — | ⬜ No existe como sección de Home (existe el CPT `miembro_equipo` + `archive-equipo.php`/`single-equipo.php`, pero no hay `template-parts/team.php` para el Home) |
| Clients | — | ⬜ No existe como sección de Home (existe el CPT `cliente` + `archive-clientes.php`, pero no hay `template-parts/clients.php` para el Home; sí existe `content-cliente.php`, pensado para un loop de archivo, reutilizable) |
| FAQ | — | ⬜ No existe como sección de Home (existe el CPT `ce_faq`, consumido hoy solo dentro de `single-servicio.php` vía `.ce-accordion`, no como sección propia) |
| CTA | `template-parts/cta.php` | ✅ Existe |
| Quote Form | `template-parts/quote-form.php` | ⚠️ Referenciado pero ausente del zip (ver §1) |

**Conclusión:** 9 de 13 secciones ya existen como componentes maduros y reutilizables. Team, Clients y FAQ necesitan un nuevo `template-part` de Home cada una (reutilizando el CPT y helpers ya existentes — cero lógica de negocio nueva, solo el envoltorio de sección). Quote Form depende de la respuesta a §1.

### 2.3 Sistema de configuración actual (Customizer)
`inc/customizer.php` ya establece el patrón: 7 secciones (`ce_section_brand`, `_typography`, `_contact`, `_social`, `_hero`, `_cta`, `_footer`), cada una con `add_setting()`/`add_control()` sencillos, leídos en frontend vía `get_theme_mod()`. Es el mecanismo natural para extender con:
- una sección `ce_section_home_builder` (orden + activo/inactivo por sección),
- una sección `ce_section_hero` extendida (tipo de fondo, video, slider),
- una sección `ce_section_quote_form` (modo: integrado/popup/desactivado),
- CTAs adicionales reutilizando el patrón ya usado en `ce_section_cta`.

No hay ningún Page Builder ni framework de opciones de terceros (ACF, Kirki, CMB2) — todo es Customizer API nativo. Mantener esa convención es consistente con "sin builders visuales" (declarado en la cabecera de `style.css`).

### 2.4 CTAs actuales
Todos los CTAs de cotización están **hardcodeados** a `href="#ce-quote-form"` (ancla) en 6 archivos distintos, sin pasar por ninguna función central. Esto es exactamente el problema que el punto 5 del brief pide resolver: hoy, cambiar el "modo" de cotización exigiría editar 6 archivos a mano.

### 2.5 Hero actual
`template-parts/hero.php`: un único modo (imagen de fondo estática vía `ce_hero_image` theme_mod, con overlay CSS fijo `.ce-hero__overlay` en `main.css`). No hay soporte de video ni slider. El overlay no es configurable (opacidad fija en el CSS, no en theme_mod).

### 2.6 Helpers y convenciones reutilizables
- `ce_cpt_has_posts( $post_type )` (`inc/helpers.php`, caché estática) — patrón ya usado para auto-ocultar secciones; el Home Builder debe **respetarlo**, no reemplazarlo: una sección puede estar "activa" en configuración y aun así no renderizar nada si su CPT está vacío.
- `get_template_part( 'template-parts/x', null, array(...) )` con `$args` — patrón ya usado por `page-hero.php`, `sidebar-servicios.php`. El Home Builder debe usar este mismo mecanismo de paso de argumentos, no inventar uno nuevo.
- Prefijo `ce_`/`ce_construction_` sin excepciones (verificado en `ARCHITECTURE.md` §10) — se mantiene.
- Extensión aditiva sobre reescritura — se mantiene: todo lo nuevo se añade al final de los archivos ya aprobados (`customizer.php`, `main.css`, `main.js`), nunca reescribiendo bloques existentes.

---

## 3. Propuesta de arquitectura (resumen técnico)

### 3.1 Home Builder — registro de secciones (patrón "registry", no hardcode)

Nuevo archivo `inc/home-builder.php` (una responsabilidad = un archivo, consistente con la convención del proyecto):

```php
function ce_construction_home_sections() {
    return apply_filters( 'ce_home_sections', array(
        'hero'         => array( 'label' => __('Hero'), 'template' => 'template-parts/hero' ),
        'about'        => array( 'label' => __('Quiénes Somos'), 'template' => 'template-parts/about' ),
        'services'     => array( 'label' => __('Servicios'), 'template' => 'template-parts/services' ),
        'projects'     => array( 'label' => __('Proyectos'), 'template' => 'template-parts/projects' ),
        'stats'        => array( 'label' => __('Estadísticas'), 'template' => 'template-parts/stats' ),
        'why_us'       => array( 'label' => __('Por Qué Elegirnos'), 'template' => 'template-parts/why-us' ),
        'testimonials' => array( 'label' => __('Testimonios'), 'template' => 'template-parts/testimonials' ),
        'gallery'      => array( 'label' => __('Galería'), 'template' => 'template-parts/gallery' ),
        'team'         => array( 'label' => __('Equipo'), 'template' => 'template-parts/team' ),        // nuevo
        'clients'      => array( 'label' => __('Clientes'), 'template' => 'template-parts/clients' ),    // nuevo
        'faq'          => array( 'label' => __('Preguntas Frecuentes'), 'template' => 'template-parts/faq' ), // nuevo
        'cta'          => array( 'label' => __('CTA'), 'template' => 'template-parts/cta' ),
        'quote_form'   => array( 'label' => __('Formulario de Cotización'), 'template' => 'template-parts/quote-form' ),
    ) );
}
```
El `apply_filters` es intencional: cumple el requisito explícito del brief ("extensible... sin rediseñar toda la arquitectura") sin añadir complejidad hoy.

`front-page.php` deja de tener la lista fija de `get_template_part()` y pasa a:
```php
foreach ( ce_construction_get_active_home_order() as $section_key ) {
    get_template_part( ce_construction_home_sections()[ $section_key ]['template'] );
}
```
Esto es **la única modificación estructural a `front-page.php`**: pasa de 10 líneas fijas a un loop de 3 líneas. Ningún `template-part` cambia su firma ni su contenido interno por esto.

### 3.2 Orden + activo/inactivo — persistencia
Opción evaluada y descartada: un `add_setting`/`add_control` por sección (13 controles sueltos) — no permite reordenar de forma nativa en el Customizer sin JS custom de drag&drop.
**Opción elegida:** un único `theme_mod` tipo `sortable` (`ce_home_sections_order`), guardado como JSON `[{key:'hero', enabled:true}, {key:'services', enabled:false}, ...]`, con un control custom (`WP_Customize_Control` propio con JS mínimo de arrastrar/soltar sobre `<li>`, sin librerías externas — reutilizando el patrón ya usado en `meta-boxes.php` para el selector de galería con jQuery, que ya está disponible en `wp-admin`). Esto es consistente con "sin duplicar/depender de librerías innecesarias" del brief.
Fallback: si el `theme_mod` no existe (instalación nueva), se usa el orden por defecto = orden de declaración en `ce_construction_home_sections()`, todas activas — **cero cambio de comportamiento para instalaciones existentes** hasta que el admin toque la nueva pantalla.

### 3.3 CTA centralizado (elimina el hardcode de 6 archivos)
Nueva función en `inc/helpers.php` (extensión aditiva, mismo archivo que ya centraliza `ce_get_whatsapp_number()`, `ce_get_phone_href()`):
```php
function ce_get_quote_cta_url() {
    $mode = get_theme_mod( 'ce_quote_form_mode', 'integrated' ); // integrated | modal | disabled
    if ( 'disabled' === $mode )  return '';
    if ( 'modal' === $mode )     return '#ce-quote-modal';       // dispara JS, no ancla de scroll
    return '#ce-quote-form';                                     // integrado, comportamiento actual
}
```
Los 6 puntos que hoy hardcodean `#ce-quote-form` pasan a llamar `ce_get_quote_cta_url()`, y si el resultado es vacío (modo desactivado), el botón/enlace no se imprime (`if`-guard, ya es el patrón usado en todo el tema para theme_mods opcionales). **Cambio quirúrgico**: una línea por archivo, sin tocar el resto del markup de cada botón.

### 3.4 Hero configurable (imagen / video / slider)
El componente **no se duplica**: `template-parts/hero.php` se extiende con una rama condicional sobre un nuevo theme_mod `ce_hero_type` (`image` | `video` | `slider`), reutilizando el mismo `.ce-hero`/`.ce-hero__overlay`/`.ce-hero__content` ya estilados. Solo el *fondo* cambia de mecanismo:
- `image` (default, = comportamiento actual, sin regresión): `style="background-image:..."`.
- `video`: `<video autoplay muted loop playsinline>` absolutamente posicionado detrás de `.ce-hero__overlay` (mismo patrón de capas, nuevo elemento).
- `slider`: reutiliza el patrón ya existente de `ModuleTestimonialSlider` en `main.js` (track + slides + autoplay), generalizado a un `ModuleHeroSlider` — mismo enfoque de módulo ES6 auto-inicializable, no una librería nueva.
Overlay: se convierte la opacidad fija del CSS en una variable inline controlada por un nuevo theme_mod `ce_hero_overlay_opacity` (rango 0–1), sin tocar la regla CSS existente (solo se le añade `opacity: var(--ce-hero-overlay-opacity, 1)` a la regla ya presente en `main.css`).

### 3.5 Modal de cotización (Modo 2)
Reutiliza el patrón ya existente de `.ce-modal-overlay`/`.ce-modal`/`ModuleModals` (`footer.php` + `main.js`), que hoy ya maneja los modales de éxito/error del propio formulario de cotización. Un tercer `.ce-modal-overlay` (`id="ce-quote-modal"`) envuelve el mismo `template-parts/quote-form` (parámetro `$args['context'] = 'modal'` para desactivar el `<section>`/heading duplicado cuando se renderiza dentro del modal). Cero JS nuevo de apertura/cierre: `ModuleModals.open('ce-quote-modal')` ya existe genéricamente.

---

## 4. Riesgos, dependencias y limitaciones

| # | Riesgo/Limitación | Severidad | Mitigación propuesta |
|---|---|---|---|
| R-1 | `template-parts/quote-form.php` ausente (§1) — bloquea Modo 1 y 2 hasta confirmar | 🔴 Bloqueante | Confirmar (a)/(b) antes de planificar el Entregable de Quote Form |
| R-2 | Control Customizer "sortable" propio (drag&drop) es la única pieza sin precedente exacto en el código actual | 🟠 Media | Aislar en un Entregable propio, con fallback no-JS (lista numérica de `<select>` como alternativa si el drag&drop no es viable en el plazo) |
| R-3 | Team/Clients/FAQ como secciones de Home son archivos nuevos, no solo configuración | 🟡 Media | Ya está mapeado en §2.2; reutilizan 100% el CPT y helpers existentes, riesgo bajo |
| R-4 | Slider de Hero introduce un segundo "slider" en el JS (ya existe uno para testimonios) — vigilar no duplicar lógica | 🟢 Baja | Generalizar `ModuleTestimonialSlider` en un helper común en vez de copiar/pegar |
| R-5 | Cambiar `front-page.php` de lista fija a loop configurable es un cambio estructural sobre un archivo ya aprobado en Sprints previos | 🟡 Media | Ya documentado como excepción legítima en `ARCHITECTURE.md` §10 ("extensión aditiva... las únicas excepciones son correcciones/mejoras explícitamente aprobadas") — requiere tu aprobación explícita por tratarse de `front-page.php` |
| R-6 | Ninguna funcionalidad de Sprint 7/8 depende de la lista fija de `front-page.php` | — | Verificado: `archive-*.php`/`single-*.php` no incluyen `front-page.php` ni dependen de su orden interno |
| R-7 | El Sprint 8 pausado (QA-030 a QA-042) no toca ningún archivo que esta fase vaya a modificar, salvo `inc/enqueue.php` (QA-030 ya aplicado) — sin conflicto de merge | — | Ninguna acción necesaria, solo mencionarlo en el impacto |

**Impacto en documentación:** requiere actualizar `ARCHITECTURE.md` (nueva sección de Home Builder + diagrama de flujo del Home), `TREE.md`, `DECISIONS.md` (una entrada D-04x por decisión de diseño de §3), `CHANGELOG.md`, `TODO.md`/`PROJECT_STATUS.md`/`CURRENT_SPRINT.md` — pero como una **rama paralela** a la numeración 8.x, dejando esos documentos con dos frentes activos (Sprint 8 pausado + Sprint UX-1 en curso) claramente diferenciados, tal como pide el brief.

---

## 5. Plan de Sprints y Entregables propuesto (fase UX/Conversión)

Numeración independiente de `Sprint 8` para no interferir con su pausa. Orden por dependencias (arquitectura base → contenido → configuración → JS/UX avanzado).

### Sprint UX-1 — "Home Builder: base arquitectónica"
**Objetivo:** hacer el Home data-driven (registro de secciones + orden + activo/inactivo), sin aún tocar Hero/CTA/Quote Form.
**Dependencias:** confirmación de R-1 (§1).
**Riesgos:** R-2, R-5.

- **UX-1.1 — Confirmación de `quote-form.php` + registro central de secciones**
  - Alcance: resolver R-1; crear `inc/home-builder.php` con `ce_construction_home_sections()`; refactor de `front-page.php` a loop (comportamiento visual idéntico al actual, 0 cambios perceptibles para el visitante).
  - Fuera de alcance: UI de administración, drag&drop, secciones nuevas (Team/Clients/FAQ).
  - Archivos: nuevo `inc/home-builder.php`; modificado `functions.php` (registro del módulo), `front-page.php`.
  - Criterios de aceptación: el Home se ve pixel-idéntico al estado actual del zip; `ce_construction_home_sections()` devuelve las 13 claves; el orden por defecto reproduce el orden actual.
  - Pruebas: verificación visual del Home, balance de llaves, revisión de que ningún otro archivo dependía del orden fijo anterior.

- **UX-1.2 — Panel de administración: orden + activo/inactivo**
  - Alcance: nueva sección de Customizer `ce_section_home_builder`; control custom sortable; persistencia en `ce_home_sections_order`; `front-page.php` consume el orden guardado.
  - Fuera de alcance: secciones nuevas (Team/Clients/FAQ) — se muestran en el panel pero renderizan vacío si no existe su `template-part` aún (documentar como pendiente de UX-2).
  - Archivos: `inc/customizer.php` (extensión aditiva), nuevo JS mínimo si el control lo requiere (`assets/js/admin-home-builder.js`), `inc/home-builder.php`.
  - Criterios de aceptación: activar/desactivar una sección la oculta/muestra en frontend sin recargar código; reordenar cambia el orden real de renderizado; una instalación sin configurar previa mantiene el orden/activación actual.
  - Pruebas: activar/desactivar cada una de las secciones existentes; reordenar y verificar en frontend; probar con `theme_mod` vacío (instalación nueva).

### Sprint UX-2 — "Secciones de Home faltantes: Team, Clients, FAQ"
**Objetivo:** completar el catálogo de 13 secciones del brief.
**Dependencias:** UX-1.1 (registro de secciones).
**Riesgos:** R-3 (bajo).

- **UX-2.1 — `template-parts/team.php` y `template-parts/clients.php`**
  - Alcance: dos nuevos template-parts de Home, reutilizando `content-equipo.php`/`content-cliente.php` como partial de card dentro de un `WP_Query` acotado (siguiendo el patrón exacto de `template-parts/projects.php`), con auto-ocultamiento vía `ce_cpt_has_posts()`.
  - Archivos nuevos: `template-parts/team.php`, `template-parts/clients.php`. Modificado: `inc/home-builder.php` (deja de apuntar a rutas inexistentes).
  - Criterios de aceptación: ambas secciones aparecen en el Home si están activas y su CPT tiene contenido; se auto-ocultan si el CPT está vacío (igual que `projects`/`testimonials`).
  - Pruebas: con y sin contenido en `miembro_equipo`/`cliente`.

- **UX-2.2 — `template-parts/faq.php`**
  - Alcance: nueva sección de Home que reutiliza el mismo `.ce-accordion` ya usado en `single-servicio.php`, con el mismo CPT `ce_faq`, extraído a una función compartida si hay duplicación relevante (evaluar en el entregable: mover el bloque accordion a un partial `content-faq-accordion.php` reutilizado por ambos puntos, para no duplicar markup entre Home y single-servicio).
  - Archivos nuevos: `template-parts/faq.php` (+ posible `template-parts/content-faq-accordion.php`). Modificado: `single-servicio.php` (si se extrae el accordion compartido), `inc/home-builder.php`.
  - Criterios de aceptación: FAQ funciona igual en Home que en single-servicio; sin duplicación de markup del accordion; JS de accordion (`ModuleAccordion`, ya genérico) funciona en ambos contextos sin cambios.
  - Pruebas: accordion en Home, accordion en single-servicio (regresión), verificar que ambos comparten el mismo `ModuleAccordion` sin conflicto de IDs.

### Sprint UX-3 — "CTA centralizado + Modo del formulario de cotización"
**Objetivo:** puntos 4 y 6 del brief (CTA único + modos de cotización).
**Dependencias:** UX-1.1; resolución de R-1.
**Riesgos:** R-1 (si `quote-form.php` debe reconstruirse desde cero, este Sprint crece).

- **UX-3.1 — `ce_get_quote_cta_url()` + migración de los 6 puntos hardcodeados**
  - Alcance: nueva función en `inc/helpers.php`; nueva sección Customizer `ce_section_quote_form` (control `mode`: integrado/popup/desactivado); actualización de `header.php` (x2), `template-parts/cta.php`, `template-parts/hero.php`, `template-parts/sidebar-servicios.php`, `template-parts/sidebar-proyectos.php`, `footer.php`.
  - Fuera de alcance: el modal en sí (UX-3.2).
  - Criterios de aceptación: cambiar el modo desde el Customizer cambia el comportamiento de los 6 botones sin tocar código; modo "desactivado" oculta los 6 botones automáticamente.
  - Pruebas: los 3 modos, uno por uno, verificando los 6 puntos de CTA en frontend.

- **UX-3.2 — Modal de cotización (Modo 2)**
  - Alcance: nuevo `.ce-modal-overlay#ce-quote-modal` en `footer.php`; parámetro de contexto en `template-parts/quote-form.php` para render dentro de modal; reutilización de `ModuleModals`.
  - Dependencia dura: requiere que `template-parts/quote-form.php` exista (R-1).
  - Criterios de aceptación: en modo popup, los CTA abren el modal (no hacen scroll); el formulario funciona igual dentro del modal (mismo endpoint AJAX, mismos nonces); en modo integrado, el modal no se imprime (evitar HTML duplicado/ID duplicado).
  - Pruebas: envío de formulario dentro del modal (regresión funcional completa del flujo AJAX ya documentado en `ARCHITECTURE.md` §8).

### Sprint UX-4 — "Hero configurable (imagen / video / slider)"
**Objetivo:** punto 6 del brief.
**Dependencias:** ninguna dura (independiente de UX-1 a UX-3, puede ejecutarse en paralelo si se prioriza).
**Riesgos:** R-4.

- **UX-4.1 — Theme mods de tipo de Hero + modo imagen/video (overlay configurable)**
  - Alcance: `ce_hero_type`, `ce_hero_video`, `ce_hero_overlay_opacity` en Customizer; rama condicional en `template-parts/hero.php`; ajuste aditivo en `main.css` para el `<video>` de fondo y la variable de opacidad.
  - Criterios de aceptación: modo imagen = comportamiento actual sin regresión; modo video reproduce en loop/mute/autoplay; overlay ajustable sin editar CSS.
  - Pruebas: los 2 modos, en desktop; verificar que el CTA/título siguen legibles con overlay en el mínimo configurado.

- **UX-4.2 — Modo slider**
  - Alcance: `ce_hero_slides` (repeater de imágenes vía Customizer, o CPT/página auxiliar si el repeater nativo resulta insuficiente — decisión técnica a confirmar al iniciar el entregable); nuevo `ModuleHeroSlider` en `main.js` generalizando `ModuleTestimonialSlider`.
  - Criterios de aceptación: slider funcional con autoplay, sin duplicar la lógica ya escrita para testimonios.
  - Pruebas: slider con 1, 2 y N imágenes; verificar que no colisiona con `ModuleTestimonialSlider` si ambos están en la misma página (Home).

### Sprint UX-5 — "Múltiples CTA / estrategias de conversión + objetivo de plantilla"
**Objetivo:** puntos 7, 8 (parcial), 10 del brief — flexibilidad de recorridos de conversión.
**Dependencias:** UX-1 (Home Builder) y UX-3 (CTA centralizado) completos.
**Riesgos:** bajos — es composición de piezas ya construidas en Sprints anteriores.

- **UX-5.1 — CTA secundario reutilizable + "formulario corto" como variante de Quote Form**
  - Alcance: evaluar si "formulario corto" es (a) una variante del mismo `template-parts/quote-form.php` con menos campos (`$args['variant'] = 'short'`) o (b) una sección nueva — **decisión de diseño a tomar al inicio del entregable**, con el criterio de reutilizar el CPT `cotizacion` y el endpoint AJAX existente sin duplicar validación de servidor.
  - Criterios de aceptación: el admin puede construir, solo con el Home Builder de UX-1/UX-2, ambas estrategias de ejemplo del brief (A y B) sin tocar código.
  - Pruebas: armar ambos recorridos de ejemplo del brief y verificar en frontend.

- **UX-5.2 — Documentación de "objetivo de plantilla" (landing / corporativo / construcción / remodelación / servicios)**
  - Alcance: 100% documental — guía de configuración en `docs/` (nuevo `docs/HOME_BUILDER_GUIDE.md` o sección dedicada en `ARCHITECTURE.md`) explicando cómo combinar Home Builder + Hero + CTA + Quote Form para cada uno de los 5 casos de uso del brief. Sin código nuevo.
  - Criterios de aceptación: guía cubre los 5 casos de uso listados en el punto 10 del brief.

### Sprint UX-6 — "Registro de trabajo futuro: Responsive" (documental, sin código)
**Objetivo:** punto 9 del brief — dejar constancia formal sin ejecutar la revisión.
**Dependencias:** ninguna.

- **UX-6.1 — Entrada de backlog formal**
  - Alcance: nueva entrada en `TODO.md`/`PROJECT_STATUS.md` ("Sprint Responsive — propuesto, no iniciado") listando explícitamente los 10 puntos del brief (header superior, espaciados, nav móvil, iconos, Hero móvil, CTA, formularios, grids, imágenes, tipografía, footer). Sin tocar CSS.
  - Nota: si algún punto de UX-1 a UX-5 requiere un ajuste responsive puntual e indispensable para que la funcionalidad nueva funcione (p. ej. el control sortable del Customizer en pantallas pequeñas de wp-admin, o el `<video>` de Hero en móvil), se resuelve dentro de ese Entregable específico y se documenta como excepción puntual — no como inicio adelantado del Sprint Responsive completo.

---

## 6. Orden de ejecución recomendado y motivo

```
UX-1.1 → UX-1.2 → UX-2.1 → UX-2.2 → UX-3.1 → UX-3.2 → UX-5.1 → UX-5.2
                                  ↘ (paralelo, sin dependencia dura)
                                    UX-4.1 → UX-4.2
UX-6.1: en cualquier momento (documental puro, cero riesgo)
```
UX-1 primero porque todo lo demás depende del registro central de secciones. UX-2 antes que UX-5 porque "múltiples estrategias de CTA" necesita las 13 secciones ya disponibles para tener sentido como demostración. UX-3 depende de resolver R-1 (bloqueante) antes de poder cerrarse completamente — **UX-3.1 puede avanzar sin R-1** (el CTA centralizado no necesita que el formulario exista, solo necesita saber a dónde apuntar), pero UX-3.2 sí lo necesita.

---

## 7. Pregunta abierta antes de aprobar el plan

Necesito tu respuesta sobre **R-1** (§1) antes de fijar el alcance definitivo de UX-3.2: ¿`template-parts/quote-form.php` existe en tu copia de trabajo y faltó en este zip, o debo reconstruirlo como parte de UX-1.1?

Con esa confirmación, el plan queda listo para tu aprobación explícita conforme a la metodología del proyecto (D-038): no se iniciará ningún Entregable de esta fase, ni se tocará ningún archivo, hasta que apruebes el plan y me indiques con cuál Entregable empezar.

---

## 8. Auditoría UX/Arquitectura post UX-5.1 y roadmap ampliado

> **Estado vigente del roadmap.** Esta sección documenta la auditoría UX/Conversión/Arquitectura realizada sobre el estado real del proyecto tras UX-5.1 (a petición explícita del usuario, sin implementar cambios de código), **aprobada por el usuario como línea base de trabajo**. Ver `docs/DECISIONS.md` D-057 para la decisión formal de adopción y la resolución de la colisión de numeración con el Sprint UX-6 original (§8.4). **Ningún Sprint de esta sección está aprobado para implementación** — cada uno se marca explícitamente su estado; solo UX-1 a UX-5 (§1-§7) están cerrados.

### 8.1 Cierre de Sprints UX-1 a UX-5

| Sprint | Objetivo | Estado |
|---|---|---|
| UX-1 | Home Builder: base arquitectónica | ✅ Completado |
| UX-2 | Secciones de Home faltantes: Team, Clients, FAQ | ✅ Completado |
| UX-3 | CTA centralizado + Modo del formulario de cotización | ✅ Completado |
| UX-4 | Hero configurable (imagen/video/slider) | ✅ Completado |
| UX-5 | Múltiples CTA / estrategias de conversión | ✅ Completado (UX-5.1) |

Detalle Entregable por Entregable: ver `docs/CURRENT_UX_SPRINT.md`. Decisiones de diseño de cada uno: `docs/DECISIONS.md` D-045 a D-056.

### 8.2 Pendiente del plan original (sin cambios de alcance)

- **UX-5.2** — Documentación de "objetivo de plantilla" (landing/corporativo/construcción/remodelación/servicios). Sigue tal como se definió en §5 de este documento — 100% documental, sin código nuevo. **No iniciado.**
- **Sprint Responsive** — ver §8.4 (renumerado de UX-6 a **UX-9**, mismo alcance sin ningún cambio).

### 8.3 Nuevo requisito arquitectónico detectado: reutilización de secciones fuera del Home Builder

La auditoría detectó que el objetivo del usuario ("evolucionar el tema desde un Home configurable hacia un sistema donde las secciones y componentes existentes puedan reutilizarse y configurarse también en páginas y entradas") **no está cubierto por la arquitectura actual**:

- `inc/home-builder.php` (`ce_construction_home_sections()` / `ce_construction_get_active_home_order()`) solo se consume desde `front-page.php`. Ningún `single-*.php` ni el editor de contenido puede invocar sus secciones.
- **No existe `page.php`** en el árbol del tema — WordPress hace caer cualquier Página (`post_type=page`) en `index.php` (sin hero, sin secciones, sin CTA).
- **Evidencia de campo:** la entrada "About Us" (capturada en la auditoría) contiene literalmente el texto `[ce_quienes_somos]` en su contenido — un intento de usar un shortcode que **no está registrado en ningún punto del código** (verificado: cero resultados de `add_shortcode`/`register_block_type` en todo el árbol). Esto confirma que el requisito de reutilización ya era una expectativa operativa real del proyecto, no solo una recomendación de esta auditoría.

Este hallazgo es el más prioritario de todo el roadmap ampliado: sin resolverlo, ninguna sección existente (About, Services, Team, FAQ, CTA, etc.) puede usarse fuera del Home, y el contenido editorial del sitio (Páginas, entradas) queda desconectado del sistema de diseño y configuración ya construido en UX-1 a UX-5.

### 8.4 Sprints propuestos — UX-6, UX-7, UX-8, UX-9

**Ninguno de los siguientes Sprints está aprobado. Todos están "propuestos / pendientes de tu aprobación explícita" (D-038).** La numeración UX-6/UX-7/UX-8 es la usada por la auditoría ya aprobada como base de trabajo; ver D-057 para la nota de renumeración del Sprint Responsive.

---

#### Sprint UX-6 — "Arquitectura de reutilización de secciones fuera del Home Builder" — 🟡 **Propuesto, pendiente de aprobación explícita.**
**Objetivo:** resolver §8.3 — permitir que las secciones ya existentes (Hero, About, Services, Team, FAQ, CTA, Quote Form, etc.) se usen dentro de Páginas y entradas, sin duplicar markup ni crear una segunda arquitectura.
**Prioridad:** Crítica — desbloquea el resto del roadmap de reutilización (UX-7 depende parcialmente de este Sprint).
**Dependencias:** ninguna — puede iniciarse de inmediato una vez aprobado.

- **UX-6.1 — `page.php`** — ✅ **Completado.**
  - Alcance: nueva plantilla de Página siguiendo el patrón ya establecido por `single.php` (Sprint 6B) — `template-parts/page-hero` + `the_content()`, respetando el sistema de diseño existente. Sin secciones del Home Builder todavía (eso es UX-6.2).
  - Fuera de alcance: el mecanismo de shortcode/bloque en sí.
  - Criterios de aceptación: cualquier Página de WordPress se renderiza con el hero interno y el estilo del tema, en vez de caer en `index.php`.
  - Ver `docs/CURRENT_UX_SPRINT.md` (Sprint UX-6) y `docs/DECISIONS.md` D-059 para el detalle completo del cierre.

- **UX-6.2 — Mecanismo de reutilización de secciones (shortcode)**
  - Alcance: **recomendación de la auditoría — shortcode, no bloque Gutenberg** (ver comparación abajo). Nuevo `inc/section-shortcode.php` (aditivo, un archivo = una responsabilidad, consistente con la convención del proyecto): registra `[ce_section key="about"]` (u otra sintaxis a definir en el Entregable) que internamente llama `get_template_part( ce_construction_home_sections()[$key]['template'], null, $args )` — el mismo mecanismo que ya usa `front-page.php`, sin reimplementarlo.
  - Comparación de opciones evaluadas (sin implementar ninguna todavía):
    | Opción | A favor | En contra |
    |---|---|---|
    | **Shortcode** (recomendada) | Mínimo esfuerzo; reutiliza `get_template_part()` + `$args` ya existente; funciona en editor clásico y de bloques (bloque nativo "Shortcode"); cero dependencias nuevas; consistente con "sin builders/frameworks de terceros" ya declarado en `style.css` | Sin preview visual en el editor de bloques |
    | Bloque Gutenberg dinámico | Mejor UX de edición (preview real) | Introduce el primer build step de JS/React (`block.json`, `render_callback`) que el proyecto no tiene hoy; mayor esfuerzo y superficie de mantenimiento |
    | Page builder de terceros | — | Descartado sin evaluación: contradice la convención ya establecida del proyecto |
  - Criterios de aceptación: una Página o entrada puede incluir `[ce_section key="about"]` (o la sintaxis que se defina) y renderizar exactamente la misma sección "Quiénes Somos" que el Home, sin copiar markup; el shortcode resuelto no requiere que la sección esté activa en el Home Builder (son consumos independientes del mismo registro).
  - Nota explícita: la elección de bloque Gutenberg como mejora futura queda registrada aquí como alternativa descartada por ahora, no eliminada del roadmap — reevaluable si el uso real del shortcode revela necesidad de mejor UX de edición.

---

#### Sprint UX-7 — "Consistencia y configurabilidad: Hero, CTA, Header/Footer" — 🟡 **Propuesto, pendiente de aprobación explícita.**
**Objetivo:** cerrar las inconsistencias y limitaciones de configuración detectadas en Hero, CTA, sidebars y Header/Footer.
**Prioridad:** Alta a Media según Entregable (detallado abajo).
**Dependencias:** UX-7.1 y UX-7.2 se benefician de que `page.php` exista (UX-6.1) para tener un contexto real de Página donde usar el Hero unificado, pero no son un bloqueo técnico duro. UX-7.3 a UX-7.5 son independientes.

- **UX-7.1 — Unificación del Hero (Home + interior)** — Prioridad Alta
  - Alcance: `template-parts/hero.php` y `template-parts/page-hero.php` son hoy dos componentes separados con overlay duplicado en CSS (secciones 10 y 20 de `main.css`). Unificar en un componente parametrizable (`$args['variant']`, mismo patrón ya usado en `cta.php` desde UX-5.1 y en `quote-form.php` con `context`), heredando video/slider/overlay configurable también en el Hero interno.
  - Criterios de aceptación: page-hero puede usar imagen/video/slider igual que el Hero de Home, sin duplicar la lógica de `ce_hero_type`; el CTA de Home y el interno comparten la misma fuente de overlay en CSS.

- **UX-7.2 — Layout de Hero de 1/2/3 columnas + integración opcional de Quote Form** — Prioridad Media
  - Alcance: `ce_hero_layout` (theme_mod) + slot opcional para `quote-form` con un tercer valor de `context` (p. ej. `'hero'`), reutilizando el componente de formulario ya existente sin crear un formulario ni endpoint nuevo (mismo `inc/quote-form.php`, mismo nonce, misma sanitización).
  - Depende de: UX-7.1 (Hero unificado).

- **UX-7.3 — Aprovechamiento de espacios vacíos en sidebars (Servicios/Proyectos)** — Prioridad Media
  - Alcance: `template-parts/sidebar-servicios.php` (confirmado visualmente en la auditoría: espacio vacío considerable en archivos con muchas cards) y, por el mismo patrón, `template-parts/sidebar-proyectos.php`. Añadir slot opcional para CTA secundario o testimonio corto reutilizando componentes ya existentes (`cta.php` variant, o una card de testimonio individual) — no crear un componente nuevo si uno existente cubre la necesidad.

- **UX-7.4 — CTA: icono y color de botón configurables** — Prioridad Media
  - Alcance: `template-parts/cta.php` tiene hoy iconos (`fa-paper-plane`/`fa-whatsapp`) y colores de botón fijos en markup/CSS. Nuevos theme_mods: selector de icono FA acotado a una lista curada (no input libre, por consistencia visual) + color picker nativo del Customizer para el color primario del botón.

- **UX-7.5 — Logo independiente Header/Footer** — Prioridad Media
  - Alcance: hoy `has_custom_logo()`/`the_custom_logo()` (logo nativo de WordPress) se usa de forma idéntica en `header.php` y `footer.php` — un único asset para ambos contextos, sin posibilidad de una variante de logo distinta para el fondo oscuro del footer. Nuevo theme_mod opcional `ce_footer_logo` (imagen), con fallback automático al logo del sitio si no se configura — extiende el mecanismo nativo, no lo reemplaza.
  - **Nota explícita — no incluye el bug de iconos sociales del header:** ese hallazgo (regla CSS `.ce-header__social` sin `display`/`gap` propios, a diferencia de `.ce-footer__social`) se clasifica como corrección de QA, no como funcionalidad UX — ver §8.5 y `docs/CURRENT_UX_SPRINT.md`. Se deriva al Sprint 8 (`QA-043`, candidato), **no a este Sprint UX-7**.

- **UX-7.6 — Estadísticas configurables desde el Customizer** — Prioridad Alta 🆕 (benchmark competitivo, §8.8)
  - Estado actual verificado en código: `template-parts/stats.php` — los 4 valores (`350+`/`280+`/`12+`/`60+`), sus etiquetas y sus iconos están escritos directamente en un array de PHP (`apply_filters( 'ce_stats_items', array(...) )`), con cantidad fija de exactamente 4 elementos. Cambiar un número, renombrar una etiqueta, o agregar/quitar una estadística requiere hoy editar código PHP — no hay ningún `theme_mod` ni control de Customizer.
  - Limitación: el cliente no puede ajustar sus propias cifras (proyectos realizados, clientes, años de experiencia, empleados) sin depender de un desarrollador, ni cambiar cuántas estadísticas mostrar.
  - Propuesta: nuevo control custom de Customizer tipo repeater (mismo patrón ya usado por `CE_Customize_Hero_Slides_Control`, UX-4.2 — añadir/quitar/reordenar) para número, sufijo, etiqueta e icono de cada estadística, cantidad variable (no fija en 4). El filtro `ce_stats_items` existente se conserva como mecanismo de fallback/extensión para desarrolladores, sin eliminarlo.
  - Clasificación: **B** (existe, limitado/hardcoded).

- **UX-7.7 — Franja de insignias de confianza / licencias** — Prioridad Alta 🆕 (benchmark competitivo, §8.8)
  - Contexto: no existe hoy ningún componente para mostrar insignias de confianza (licencias estatales, seguros, afiliaciones, certificaciones, ratings de plataformas externas) — hallazgo de la comparación con DayBrook Homes (Equal Housing Lender / BBB / Thumbtack) y Re-Bath. **Requisito explícito del usuario:** el cliente real de este proyecto cuenta con una licencia de contratista válida para operar en su estado, y necesita poder mostrarla — no es un elemento decorativo, es una insignia de credibilidad legal real.
  - Propuesta: nueva sección del Home Builder (`template-parts/trust-badges.php`, registrada en `inc/home-builder.php` junto al resto), con un control repeater de Customizer (mismo patrón que UX-7.6/UX-4.2: imagen o texto + número de licencia opcional + enlace de verificación opcional por insignia) — cantidad variable, sin límite fijo de insignias.
  - Requisito de reutilización (explícito del usuario, aplica a los 4 Entregables de este bloque): la sección se registra en `inc/home-builder.php` con el mismo mecanismo que ya usan `hero`/`about`/`services`/etc., para quedar automáticamente disponible vía `[ce_section key="trust_badges"]` en cuanto exista el shortcode de UX-6.2 — sin duplicar markup entre Home y páginas internas.
  - Clasificación: **D** (funcionalidad nueva).

- **UX-7.8 — Testimonio en video** — Prioridad Media 🆕 (benchmark competitivo, §8.8)
  - Contexto: `inc/cpt-testimonios.php` ya registra el CPT `testimonio` (`title`+`editor`+`thumbnail`, sin campo de video) — a diferencia de las estadísticas, los testimonios **ya son administrables libremente desde wp-admin** (agregar/quitar/editar sin tocar código); el único hallazgo real del benchmark es la ausencia de soporte de video, no la arquitectura de testimonios en sí.
  - Propuesta: nuevo campo meta opcional en el CPT `testimonio` (adjunto de video de WordPress o URL externa vía oEmbed, mismo patrón ya construido para `ce_hero_video` en UX-4.1 y propuesto para `_ce_proyecto_video_url` en UX-8.1 — sin duplicar el mecanismo de reproducción de video, una tercera reutilización del mismo patrón). `template-parts/testimonials.php` muestra el video si existe, o cae al testimonio de texto normal si no.
  - Clasificación: **D** (funcionalidad nueva).

- **UX-7.9 — Bloque de financiamiento / opciones de pago** — Prioridad Media 🆕 (benchmark competitivo, §8.8)
  - Contexto: ni DayBrook ni Re-Bath son constructoras generales, pero ambas destacan una franja de financiamiento con CTA propio ("0% interés 12 meses", "Pre-aprobación sin afectar tu crédito") — hallazgo aplicable a CE Construction independientemente del tipo de proyecto, si el cliente ofrece o desea comunicar opciones de pago/financiamiento.
  - Propuesta: nueva sección del Home Builder (`template-parts/financing.php`), campos vía Customizer (título, texto, texto de botón, URL de botón — mismo patrón exacto ya usado por `cta.php`/`cta_secondary`, UX-5.1, sin inventar un patrón nuevo). Registrada en `inc/home-builder.php` para quedar disponible vía shortcode (UX-6.2) igual que UX-7.7.
  - Clasificación: **D** (funcionalidad nueva).

---

#### Sprint UX-8 — "Video en Proyectos (YouTube / TikTok / WordPress)" — 🟡 **Propuesto, futuro, sin iniciar.**
**Objetivo:** punto 6 del brief de auditoría — soporte de video en el CPT Proyecto, además de la galería de imágenes ya existente.
**Prioridad:** Baja–Media — mejora futura, explícitamente no implementada en esta fase.
**Dependencias:** ninguna.

- **UX-8.1 — Campo de video del Proyecto**
  - Alcance: nuevo campo meta `_ce_proyecto_video_url` en `single-proyecto.php`/`content-proyecto.php`, usando **oEmbed nativo de WordPress** (cubre YouTube y TikTok sin ninguna librería nueva) para URLs externas, más una opción de adjunto de video local reutilizando el mismo patrón ya construido para `ce_hero_video` (UX-4.1) — sin duplicar el mecanismo de subida/reproducción.
  - Criterios de aceptación: un Proyecto puede mostrar un video (externo embebido, o archivo de WordPress) junto a su galería de imágenes existente, sin afectar proyectos que no lo configuren.

---

#### Sprint UX-9 — "Registro de trabajo futuro: Responsive" (documental, sin código) — ⬜ No iniciado.
> **Nota de renumeración (D-057):** este Sprint es idéntico en alcance, contenido y criterios de aceptación al que el plan original (§5 de este documento) numeraba como **"Sprint UX-6"**. Se renumera a **UX-9** únicamente para no colisionar con el nuevo Sprint UX-6 (§8.4, arriba), nombrado así por la auditoría aprobada por el usuario. Ningún carácter del alcance original cambió.

**Objetivo:** punto 9 del brief original — dejar constancia formal sin ejecutar la revisión.
**Dependencias:** ninguna.

- **UX-9.1 — Entrada de backlog formal**
  - Alcance: nueva entrada en `TODO.md`/`PROJECT_STATUS.md` ("Sprint Responsive — propuesto, no iniciado") listando explícitamente los 10 puntos del brief original (header superior, espaciados, nav móvil, iconos, Hero móvil, CTA, formularios, grids, imágenes, tipografía, footer). Sin tocar CSS.
  - Nota (sin cambios respecto al plan original): si algún punto de los Sprints UX-6/UX-7/UX-8 requiere un ajuste responsive puntual e indispensable para que la funcionalidad nueva funcione, se resuelve dentro de ese Entregable específico y se documenta como excepción puntual — no como inicio adelantado de este Sprint completo.

### 8.5 Clasificación bug/QA vs. funcionalidad UX nueva

La auditoría distinguió explícitamente qué hallazgos son correcciones de algo ya roto frente a funcionalidad nueva:

| Hallazgo | Clasificación | Ruta |
|---|---|---|
| Iconos sociales del header sin estilo base (`.ce-header__social` sin `display`/`gap`, a diferencia de `.ce-footer__social` — confirmado visualmente) | **Bug/QA** | Candidato `QA-043`, **Sprint 8** (no esta fase; Sprint 8 no se toca en este documento) |
| Ausencia de `page.php` / mecanismo de reutilización de secciones | Funcionalidad nueva (arquitectura) | Sprint UX-6 |
| Hero Home/interior duplicado, sin layout de columnas | Existe pero limitado (B) / nueva (D) según el punto | Sprint UX-7 |
| CTA icono/color no configurables | Existe pero limitado (B) | Sprint UX-7 (UX-7.4) |
| Logo único Header/Footer | Existe pero limitado (B) | Sprint UX-7 (UX-7.5) |
| Estadísticas hardcodeadas en PHP, cantidad fija | Existe pero limitado (B) | Sprint UX-7 (UX-7.6) 🆕 |
| Sin insignias de confianza/licencias | Funcionalidad nueva (D) | Sprint UX-7 (UX-7.7) 🆕 |
| Testimonios sin soporte de video | Funcionalidad nueva (D) | Sprint UX-7 (UX-7.8) 🆕 |
| Sin bloque de financiamiento/opciones de pago | Funcionalidad nueva (D) | Sprint UX-7 (UX-7.9) 🆕 |
| Video en Proyectos | Funcionalidad nueva (D) | Sprint UX-8 |

### 8.6 Orden de ejecución recomendado (roadmap ampliado)

```
UX-6.1 (page.php) → UX-6.2 (shortcode de reutilización)
                        ↘ (dependencia blanda, no dura)
                          UX-7.1 (Hero unificado) → UX-7.2 (columnas + Quote Form en Hero)

UX-7.3 (sidebars) ⎫
UX-7.4 (CTA icono/color) ⎬ independientes entre sí y del resto — pueden ejecutarse en cualquier orden/paralelo
UX-7.5 (logo footer) ⎪
UX-7.6 (estadísticas configurables) ⎪ 🆕 independiente, sin dependencia de UX-6
UX-7.7 (insignias de confianza/licencias) ⎬ 🆕 se benefician de que UX-6.2 (shortcode) exista para reutilizarse
UX-7.8 (testimonio en video) ⎪ 🆕 independiente, reutiliza el patrón de ce_hero_video
UX-7.9 (bloque de financiamiento) ⎭ 🆕 se beneficia de que UX-6.2 exista, mismo patrón que cta.php

UX-8.1 (video Proyectos): independiente, baja prioridad, futuro

UX-5.2 (doc. objetivo de plantilla): independiente, documental puro, en cualquier momento
UX-9.1 (Responsive, renumerado): independiente, documental puro, en cualquier momento

QA-043 (iconos sociales header): Sprint 8, no esta fase — requiere que el usuario decida retomar Sprint 8 (pausado en Entregable 8.2)
```
UX-6 primero porque es la dependencia arquitectónica de mayor prioridad detectada en la auditoría: sin `page.php` ni el mecanismo de reutilización, ninguna sección puede usarse fuera del Home. UX-7.1/UX-7.2 se benefician de tener UX-6.1 ya resuelto (un contexto real de Página donde probar el Hero unificado), aunque no es un bloqueo técnico estricto. UX-7.7 y UX-7.9 (secciones nuevas, insignias y financiamiento) se benefician de UX-6.2 por la misma razón — quedan disponibles en páginas internas sin trabajo adicional una vez exista el shortcode, pero pueden construirse antes si se prioriza así. El resto de UX-7, UX-8, UX-5.2 y UX-9 no tienen dependencias entre sí.

### 8.7 Pregunta abierta para el usuario

Ningún Sprint de §8.4 está aprobado para implementación. Antes de iniciar cualquiera, se necesita tu confirmación explícita de:
1. Con cuál Sprint/Entregable empezar (se recomienda UX-6.1, por ser la dependencia de mayor prioridad).
2. Confirmar la recomendación de shortcode (vs. bloque Gutenberg) para UX-6.2 — o indicar si prefieres evaluar el bloque Gutenberg desde ahora pese al mayor esfuerzo.
3. Si el hallazgo de QA-043 (iconos sociales del header) debe esperar a que retomes el Sprint 8 pausado, o si prefieres resolverlo de forma aislada antes, pese a pertenecer formalmente a la numeración QA.

### 8.8 Benchmark competitivo (DayBrook Homes, Re-Bath Baltimore) — origen de UX-7.6 a UX-7.9

A petición del usuario, se compararon 2 sitios de competidores orientados 100% a conversión (landing de Google Ads de DayBrook Homes; página de ubicación local de Re-Bath) contra el Home real de CE Construction. Ambos son de nicho (solo remodelación de baños) con presupuesto de ads — su estrategia está más "afilada" que la de un sitio corporativo general, pero varios elementos son aplicables sin romper la arquitectura del proyecto.

**Hallazgos de los competidores, sin equivalente hoy en CE Construction:**
- **DayBrook:** formulario de cotización dentro del Hero (sin scroll); oferta con urgencia + bullets de valor; insignias de confianza (BBB, Equal Housing Lender, rating de Thumbtack) justo debajo del Hero; testimonio en video; FAQ extenso orientado a objeciones de venta (costo, financiamiento, garantía); sección de financiamiento con CTA propio.
- **Re-Bath:** nav de anclas internas; franja de financiamiento destacada (0% interés); slider de antes/después; formulario de cotización repetido más abajo en la página; alianza con diseñador reconocido como diferenciador.

**Corrección a una observación previa de esta conversación:** se había señalado que la sección de estadísticas del Home mostraba "0" en producción — se confirmó por lectura de código que es un falso positivo: `template-parts/stats.php` anima los números por JavaScript (`ModuleCounters`) desde `0` hasta el valor real (`data-count`) al cargar en un navegador; la herramienta de lectura usada no ejecuta JavaScript, por lo que capturó el estado inicial de la animación, no el estado real en producción. Se retira esa alarma.

**Hallazgo real confirmado por el usuario y verificado en código:** las estadísticas sí tienen un problema distinto y genuino — están hardcodeadas en un array de PHP (`template-parts/stats.php`), sin ningún `theme_mod`, con cantidad fija de 4 elementos. Cambiar un número, una etiqueta, o agregar/quitar una estadística requiere hoy editar código. Ver UX-7.6.

**Requisito explícito adicional del usuario:** el cliente cuenta con una licencia de contratista real, válida para operar en su estado, y necesita poder mostrarla como insignia de confianza — no es un elemento decorativo. Ver UX-7.7.

**Testimonios — verificado que NO es un problema arquitectónico:** a diferencia de las estadísticas, `inc/cpt-testimonios.php` ya registra un CPT propio (`testimonio`) administrable libremente desde wp-admin (agregar/quitar/editar sin tocar código) — el único hallazgo real es la ausencia de soporte de video, no la arquitectura de testimonios. Ver UX-7.8.

**Resultado:** 4 nuevos Entregables incorporados a Sprint UX-7 (§8.4, arriba): UX-7.6 (estadísticas configurables), UX-7.7 (insignias de confianza/licencias), UX-7.8 (testimonio en video), UX-7.9 (bloque de financiamiento) — todos diseñados, por requisito explícito del usuario, para quedar reutilizables también en páginas y entradas vía el mismo mecanismo de UX-6 (registro en `inc/home-builder.php`, sin arquitectura paralela). **Ninguno de los 4 está aprobado para implementación** — mismo estado "propuesto / pendiente de aprobación" que el resto de UX-7.

**Hallazgos del benchmark que NO se incorporaron como Entregable (quedan solo anotados, menor prioridad o requieren más definición):**
- Nav de anclas internas en el Home (Re-Bath) — mejora menor de UX, baja prioridad.
- Oferta con urgencia tipo "50% OFF este mes" (DayBrook) — es una táctica de landing page de ads; no está claro que encaje con el posicionamiento de marca de una constructora general. Requiere decisión de negocio del usuario antes de convertirse en Entregable, no es una mejora puramente técnica.
- FAQ orientado a objeciones de venta — el componente (`faq.php`, CPT `preguntas_frecuentes` si aplica) ya existe desde UX-2.2; esto es un ajuste de contenido/copy, no de código — no requiere Entregable nuevo.
- Personalización de copy por ubicación (Re-Bath) — de menor relevancia dado que CE Construction opera desde una sola oficina/ciudad, a diferencia de Re-Bath (franquicia multi-ubicación); se descarta por ahora.
