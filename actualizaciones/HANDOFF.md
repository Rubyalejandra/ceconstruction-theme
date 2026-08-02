# CE Construction — HANDOFF.md
### Documento oficial de transferencia entre sesiones

> Este documento, junto con `PROJECT_STATUS.md`, `TODO.md`, `TREE.md`, `CHANGELOG.md` y `DECISIONS.md`, es la fuente oficial del estado del proyecto. Si esta conversación se corta por límite de tokens, cualquier sesión nueva debe poder retomar el trabajo exactamente desde aquí, sin releer el historial completo de chat.

**Versión de referencia:** v0.2.1 (ver `CHANGELOG.md`)
**Última sesión de trabajo:** Módulo "Header, Footer y Front Page" + cierre de etapa (documentación)

---

## 1. Resumen ejecutivo

CE Construction es un tema profesional de WordPress a medida, desarrollado 100% en PHP/HTML5/CSS3/JS moderno (sin builders visuales), con arquitectura modular. A la fecha:

- **Backend completo:** 6 Custom Post Types + 1 CPT interno de cotizaciones, Theme Customizer con 7 secciones, metaboxes seguros, formulario de cotización funcional (AJAX + email + adjuntos + nonce), SEO propio con auto-desactivación si hay plugin SEO.
- **Frontend completo para el Home:** sistema de diseño CSS (862 líneas), sistema JS modular (611 líneas, 12 módulos), `header.php`, `footer.php`, `front-page.php` y sus 10 template-parts.
- **Pendiente crítico:** el tema **no tiene `index.php`**, archivo mínimo que WordPress exige junto a `style.css` para reconocer un tema como válido. No se puede garantizar una activación 100% segura hasta resolver esto.
- **Pendiente funcional:** todas las plantillas de contenido individual (Servicios, Proyectos, Blog, páginas genéricas, 404).

El proyecto sigue una metodología de "módulo por módulo con aprobación explícita del cliente antes de avanzar", y mantiene documentación viva (los 6 archivos `.md` mencionados arriba) que se actualiza al cierre de cada módulo.

---

## 2. Arquitectura implementada

- **Patrón de carga:** `functions.php` es un bootstrap puro; no contiene lógica de negocio. Cada funcionalidad vive en su propio archivo dentro de `inc/`, cargado condicionalmente vía `file_exists()` en `ce_construction_require_modules()`.
- **Separación de responsabilidades:** un archivo = una funcionalidad (un CPT por archivo, un módulo JS por objeto literal, una sección del home por template-part).
- **Seguridad por diseño:** nonces específicos por acción (no globales), sanitización explícita de cada `$_POST`/`$_FILES`, verificación de capacidades (`current_user_can`) antes de cualquier escritura.
- **Progressive enhancement en frontend:** las secciones del home dependientes de contenido (CPT) se auto-ocultan si no hay posts publicados (`ce_cpt_has_posts()`), en vez de mostrar placeholders vacíos.
- **JS namespace pattern:** cada módulo de `main.js` es un objeto (`ModuleXxx`) con su propio `init()`, que verifica la existencia de su marcado en el DOM antes de operar — ningún módulo puede romper a otro.
- **CSS con Design Tokens:** todas las propiedades visuales (color, espaciado, radio, sombra, transición) están centralizadas en variables CSS (`:root`), sincronizadas dinámicamente con el Customizer vía `inc/customizer.php` → `ce_construction_customizer_css()`.

---

## 3. Estructura del tema

Ver `TREE.md` para el árbol completo y actualizado con estado por archivo. Resumen de carpetas:

```
ce-construction-theme/
├── style.css, functions.php, header.php, footer.php, front-page.php
├── PROJECT_STATUS.md, TODO.md, TREE.md, HANDOFF.md, CHANGELOG.md, DECISIONS.md
├── inc/            → toda la lógica PHP modular (CPTs, Customizer, metaboxes, formulario, SEO, helpers)
├── template-parts/ → las 10 secciones del home + futuros parciales de contenido
└── assets/
    ├── css/main.css
    ├── js/main.js
    └── img/ (vacía, pendiente de assets reales del cliente)
```

---

## 4. Módulos completamente terminados

1. Bootstrap del tema (`functions.php`, `inc/setup.php`)
2. Carga de assets (`inc/enqueue.php`)
3. Theme Customizer (`inc/customizer.php`)
4. 6 Custom Post Types + taxonomías
5. Metaboxes / campos personalizados (`inc/meta-boxes.php`)
6. Formulario de Cotización — backend completo (`inc/quote-form.php`)
7. SEO backend (`inc/seo.php`), con breadcrumbs ya enganchados en `header.php`
8. Helpers reutilizables (`inc/helpers.php`)
9. Sistema de diseño CSS (`assets/css/main.css`)
10. Sistema JS modular (`assets/js/main.js`)
11. Header, Footer, Front Page + 10 template-parts del home

Detalle línea por línea en `TODO.md` (secciones 1-8) y `CHANGELOG.md` (v0.1.0, v0.2.0).

## 5. Módulos parcialmente implementados

| Módulo | Qué existe | Qué falta |
|---|---|---|
| Formulario de cotización | Backend + AJAX + validación + email + markup, todo funcional | Fallback sin JavaScript (fuera de alcance por decisión D-005, revisable) |
| SEO | Meta tags, OG, Schema, breadcrumbs | Sitemap XML propio (o decidir delegar a plugin) |
| Componentes reutilizables del brief | Hero, Cards, Buttons, Forms, Modals, Alerts, Navbar, Footer, Breadcrumb, Gallery, Counter, Testimonials, CTA | Accordion (para FAQ), Timeline (sin sección que lo use aún), Sidebar (sin plantilla de blog aún) |

## 6. Módulos pendientes (no iniciados)

Por prioridad (igual que en `PROJECT_STATUS.md` sección 4):

1. **`index.php`** — 🔴 crítico, requerido por WordPress.
2. `archive-servicio.php`, `single-servicio.php`
3. `archive-proyecto.php`, `single-proyecto.php`
4. `single.php`, `page.php`, `comments.php`, `404.php`
5. `archive.php` genérico (fallback Equipo/Clientes/FAQ)
6. `inc/widgets.php`
7. `screenshot.png`
8. Auditoría transversal de accesibilidad y performance

---

## 7. Dependencias entre módulos

- `header.php` / `footer.php` dependen de `inc/customizer.php` (theme mods) y `inc/helpers.php` (funciones de render).
- `front-page.php` depende de los 10 template-parts + de todos los CPTs ya registrados + de `assets/css/main.css` y `assets/js/main.js` ya enqueados.
- `template-parts/quote-form.php` (markup) depende del nonce emitido en `inc/enqueue.php` (`ceConstructionData.quoteNonce`) y del endpoint AJAX en `inc/quote-form.php`.
- `template-parts/testimonials.php` depende de la estructura de metadatos guardada por `inc/meta-boxes.php` (`_ce_testimonio_nombre`, `_ce_testimonio_cargo`, `_ce_testimonio_rating`) y del módulo JS `ModuleTestimonialSlider`.
- `template-parts/gallery.php` depende de `_ce_proyecto_galeria` (guardado en `inc/meta-boxes.php` vía `wp.media`) y del módulo JS `ModuleLightbox`.
- Cualquier plantilla nueva de contenido individual (`single-proyecto.php`, etc.) **deberá** reutilizar `ce_get_gallery_ids()`, `ce_get_short_excerpt()`, `ce_render_service_icon()` de `inc/helpers.php` en vez de duplicar lógica.

---

## 8. Riesgos conocidos

Ver `PROJECT_STATUS.md` sección 6 para la tabla completa con severidad. Resumen:

- 🔴 **Falta `index.php`** — bloquea una activación 100% garantizada del tema.
- 🟡 Secciones del home pueden verse "cortas" en una instalación limpia sin contenido cargado (comportamiento esperado, por diseño).
- 🟡 Dependencia de CDNs externos (Google Fonts, Font Awesome) — impacto en Core Web Vitals y consideraciones de privacidad/GDPR.
- 🟢 Sin fallback sin-JS en el formulario de cotización (decisión aceptada, revisable).
- 🟢 Falta `screenshot.png` (cosmético).

## 9. Bugs conocidos

| ID | Descripción | Estado |
|---|---|---|
| BUG-001 | `ModuleModals` en `main.js` usaba `querySelector` (singular) para enlazar el botón de cierre de cada modal, pero cada modal tiene 2 botones con la clase `.ce-modal__close` (la X y el botón de acción). Solo el primero quedaba funcional. | ✅ Corregido en el módulo Header/Footer/Front Page (ver `DECISIONS.md` D-008 y `CHANGELOG.md` v0.2.0). |
| BUG-002 | Un `mkdir -p` con expansión de llaves (`{a,b,c}`) no se expandió correctamente en el entorno de ejecución, generando carpetas con nombres literales corruptos en vez de `template-parts/` y `assets/`. | ✅ Corregido manualmente antes de crear ningún archivo dentro de esas rutas; no afectó ningún archivo de contenido. |

No hay bugs abiertos conocidos a la fecha de este documento.

---

## 10. Decisiones arquitectónicas importantes

Registro completo y acumulativo en `DECISIONS.md` (D-001 a D-008). Resumen de las más relevantes para continuar el desarrollo:

- **D-001:** nonces específicos por acción, no globales.
- **D-002:** CPT `cotizacion` no público pero administrable vía wp-admin.
- **D-003:** secciones del home se ocultan si el CPT no tiene contenido publicado (`ce_cpt_has_posts()`).
- **D-004:** SEO propio se auto-desactiva si detecta Yoast/RankMath.
- **D-005:** formulario de cotización sin fallback sin-JS (riesgo aceptado).
- **D-006:** `inc/helpers.php` se adelantó por dependencia real de header/footer.
- **D-007:** botones flotantes y modales viven dentro de `footer.php`, no en template-parts separados (reversible).
- **D-008:** bugfix de selector en `ModuleModals` documentado explícitamente, no aplicado en silencio.

---

## 11. Convenciones utilizadas durante el desarrollo

- **Prefijo global:** todas las funciones PHP usan el prefijo `ce_` o `ce_construction_` para evitar colisiones de nombres con plugins/otros temas.
- **Clases CSS:** metodología BEM con el bloque raíz `ce-` (ej. `ce-card__title`, `ce-card__title--dark` si aplicara un modificador).
- **Meta keys:** todas con guion bajo inicial y prefijo `_ce_` (ej. `_ce_proyecto_cliente`), siguiendo la convención de WordPress para meta "oculto"/interno.
- **Nonces:** nombre de acción con patrón `ce_[verbo]_[entidad]_[meta|action]` (ej. `ce_save_servicio_meta`, `ce_quote_form_action`).
- **Módulos JS:** un objeto por funcionalidad, nombrado `ModuleNombreEnPascalCase`, con método `init()` obligatorio, auto-verificando su propio marcado antes de ejecutar lógica.
- **Idioma:** todo el texto visible usa funciones de traducción de WordPress (`__()`, `esc_html__()`, etc.) con text domain `ce-construction`, en español por defecto.
- **Comentarios de archivo:** cada archivo PHP inicia con un docblock explicando su propósito y su ubicación en la arquitectura (`@package CE_Construction`).
- **Verificación de sintaxis:** dado que el entorno de desarrollo no tiene PHP instalado, cada entrega de código PHP se valida con un chequeo manual de balance de llaves (`grep -o '{' | wc -l` vs `}`); el JS se valida con `node --check`.

---

## 12. Orden recomendado para continuar

1. **`index.php`** (crítico, bajo esfuerzo, desbloquea activación segura del tema).
2. **Servicios:** `archive-servicio.php` + `single-servicio.php`.
3. **Proyectos:** `archive-proyecto.php` + `single-proyecto.php` (con galería completa + lightbox + metadatos de cliente/ubicación/fecha/estado).
4. **Blog y páginas genéricas:** `single.php`, `page.php`, `comments.php`, `404.php`.
5. **Extras de menor prioridad:** `inc/widgets.php`, `screenshot.png`, `archive.php` genérico para Equipo/Clientes/FAQ.
6. **Auditoría transversal final:** accesibilidad (ARIA, foco, contraste), performance (Core Web Vitals, auto-hospedar fuentes), y revisión cruzada de sanitización/escaping en todas las plantillas.

## 13. Próximo sprint recomendado

**Sprint: "Contenido — Servicios y Proyectos"**
- `index.php`
- `archive-servicio.php`, `single-servicio.php`
- `archive-proyecto.php`, `single-proyecto.php`

Justificación: son las dos entidades de contenido más visibles del sitio (después del home) y desbloquean, junto con `index.php`, una instalación de WordPress completamente funcional y navegable de punta a punta para pruebas con el cliente.

---

## 14. Advertencias para evitar romper compatibilidad

- **No renombrar** ninguna función con prefijo `ce_`/`ce_construction_` sin actualizar todas sus referencias — no hay un autoloader ni namespaces PHP, todo es por nombre de función global.
- **No renombrar** los `slug` de los CPTs (`servicio`, `proyecto`, `testimonio`, `miembro_equipo`, `cliente`, `ce_faq`, `cotizacion`) ni de las taxonomías (`categoria_servicio`, `categoria_proyecto`, `estado_proyecto`) — romperían permalinks y metadatos ya guardados en cualquier instalación de prueba existente.
- **No eliminar** las claves de `get_theme_mod()` usadas en `inc/customizer.php` sin revisar antes cada template-part que las consume (`header.php`, `footer.php`, `hero.php`, `cta.php`).
- **No modificar** la firma del handler AJAX (`action=ce_submit_quote`, nombre de campos `name`, `email`, `phone`, `company`, `service`, `message`, `attachment`, `ce_quote_nonce`) sin actualizar simultáneamente `ModuleQuoteForm` en `main.js` y el markup en `template-parts/quote-form.php` — los tres deben cambiar juntos.
- **No agregar** un segundo nonce global "de reemplazo" — la convención del proyecto (D-001) es un nonce por acción específica.
- **Todo archivo PHP nuevo** debe iniciar con `if ( ! defined( 'ABSPATH' ) ) { exit; }` como los existentes, por seguridad.
- **Toda salida a HTML** debe pasar por la función de escaping correspondiente (`esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`) — no imprimir variables directamente, siguiendo el estándar ya usado en todos los archivos entregados.
- **Antes de asumir que un archivo existe** porque fue mencionado en una respuesta anterior del chat: verificar contra `TREE.md` (fuente de verdad) o contra el propio sistema de archivos — ya ocurrió un caso (BUG-002) donde una carpeta esperada no se había creado realmente.

## 15. Lista de archivos críticos del proyecto

Estos archivos, si se dañan o eliminan, rompen funcionalidad transversal del tema completo (no solo una sección):

- `functions.php` — sin este archivo, ningún módulo de `inc/` se carga.
- `inc/enqueue.php` — sin este archivo, no se cargan CSS/JS ni se localiza `ceConstructionData` (rompe el formulario de cotización, WhatsApp flotante, y todo el diseño visual).
- `inc/customizer.php` — sin este archivo, el sitio pierde toda personalización (colores, contacto, redes, hero, CTA, footer vuelven a sus defaults hardcodeados).
- `inc/helpers.php` — sin este archivo, `header.php`, `footer.php` y varios template-parts producen errores fatales de PHP (funciones no definidas).
- `inc/quote-form.php` — sin este archivo, el endpoint AJAX del formulario no existe (error 400/`0` en la respuesta AJAX).
- `header.php` / `footer.php` — son cargados por **toda** plantilla vía `get_header()`/`get_footer()`; un error de sintaxis aquí rompe el sitio completo.
- `assets/css/main.css` / `assets/js/main.js` — ya están enqueados en `inc/enqueue.php`; si se eliminan, todas las páginas cargan sin estilos ni interactividad.

---

# Prompt para continuar el proyecto

Copia y pega el siguiente prompt en una conversación nueva para retomar el proyecto exactamente donde quedó, sin perder contexto:

```
Estoy retomando el desarrollo del tema de WordPress "CE Construction".
Te adjunto los archivos de control del proyecto: PROJECT_STATUS.md, TODO.md,
TREE.md, CHANGELOG.md y DECISIONS.md (y este mismo HANDOFF.md).

Estos archivos son la fuente oficial y verificada del estado del proyecto.
No asumas nada del historial de chat que no esté reflejado en ellos.

Reglas para continuar:
- Conserva toda la arquitectura y las convenciones ya documentadas en
  HANDOFF.md (secciones 2, 10 y 11) y en DECISIONS.md.
- No reinicies el proyecto ni reescribas archivos ya marcados como ✅ en
  TREE.md, salvo que exista un error real (en cuyo caso, indícamelo
  explícitamente antes de tocar el archivo).
- Antes de generar código, confirma el inventario leyendo TREE.md y TODO.md,
  y dime si detectas alguna inconsistencia contra lo que te comparto.
- Trabajaremos módulo por módulo. Cada entrega debe contener únicamente los
  archivos del siguiente módulo pendiente según el orden recomendado en
  HANDOFF.md sección 12 (empezando por index.php si aún no existe).
- Al finalizar cada módulo, actualiza los 6 documentos de control
  (PROJECT_STATUS.md, TODO.md, TREE.md, CHANGELOG.md, DECISIONS.md y este
  HANDOFF.md si corresponde) antes de detenerte, y espera mi aprobación
  antes de avanzar al siguiente módulo.

El siguiente módulo a desarrollar es: [indica aquí el módulo, o escribe
"el que recomiendes según HANDOFF.md sección 13" para que continúes con
el sprint recomendado].
```