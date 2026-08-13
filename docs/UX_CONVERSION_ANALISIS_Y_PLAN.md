# CE Construction — Fase "Optimización UX / Conversión"
## Análisis arquitectónico + Plan de Sprints y Entregables (para aprobación)

> Este documento corresponde a la **Fase de Análisis y Planificación** (secciones 1 y 16 del brief). No se ha implementado ningún cambio de código. No se ha tocado ningún archivo del tema ni de `docs/`.

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
