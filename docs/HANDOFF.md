# CE Construction — HANDOFF.md
### Documento oficial de transferencia entre sesiones

> Este documento, junto con `PROJECT_STATUS.md`, `TODO.md`, `TREE.md`, `CHANGELOG.md`, `DECISIONS.md`, `QA_REPORT.md` y `ARCHITECTURE.md`, es la fuente oficial del estado del proyecto. Si esta conversación se corta por límite de mensajes/tokens, cualquier sesión nueva debe poder retomar el trabajo exactamente desde aquí, sin releer el historial completo de chat.

**Versión de referencia:** v0.6.0 (ver `CHANGELOG.md`)
**Última sesión de trabajo:** Entregable 6A — `index.php` (bloqueador crítico de arquitectura resuelto).

---

## 1. Resumen ejecutivo

CE Construction es un tema profesional de WordPress a medida, desarrollado 100% en PHP/HTML5/CSS3/JS moderno (sin builders visuales), con arquitectura modular. A la fecha:

- **Backend completo:** 6 Custom Post Types + 1 CPT interno de cotizaciones, Theme Customizer con 7 secciones, metaboxes seguros, formulario de cotización funcional (AJAX + email + adjuntos + nonce + rate-limiting + retención automática), SEO propio con auto-desactivación si hay plugin SEO.
- **Frontend completo:** Home, Servicios (archive+single), Proyectos (archive+single, con galería/lightbox), Equipo (archive+single) y Clientes (archive+single) — los 6 CPTs de contenido ya tienen presencia completa en el frontend. `index.php` (Entregable 6A) cubre single de blog/página, archivos genéricos, búsqueda y 404 como fallback funcional.
- **QA:** auditoría completa realizada (`QA_REPORT.md`, 29 hallazgos); los 9 Críticos/Altos ya corregidos (v0.4.1); los 20 Medios/Bajos/Mejoras futuras siguen documentados y sin tocar.
- **Documentación de arquitectura:** `ARCHITECTURE.md` describe la arquitectura real (no propuesta) del proyecto completo.
- **Bloqueador crítico resuelto:** `index.php` ya existe — el tema cumple el mínimo que WordPress exige (`style.css` + `index.php`) para ser reconocido y activado con seguridad.
- **Pendiente funcional:** Blog y páginas genéricas dedicadas (`single.php`, `page.php`, `comments.php`, `404.php` — hoy cubiertas por el fallback de `index.php`), `inc/widgets.php`, `screenshot.png`.

El proyecto sigue una metodología de "sprint por sprint con aprobación explícita del cliente antes de avanzar", y mantiene documentación viva (los 7 archivos `.md` mencionados arriba) que se actualiza al cierre de cada sprint.

---

## 2. Arquitectura implementada

Ver `ARCHITECTURE.md` (nuevo desde el Sprint 5) para el detalle exhaustivo: estructura de carpetas, responsabilidad de cada archivo, flujo de carga del tema, dependencias entre módulos, flujo de renderizado del Front Page, flujo de los CPT, flujo del formulario de cotización, flujo de CSS/JS, y convenciones de organización. Resumen de los principios clave:

- **Patrón de carga:** `functions.php` es un bootstrap puro. Cada funcionalidad vive en su propio archivo dentro de `inc/`, cargado condicionalmente vía `file_exists()` en `ce_construction_require_modules()`.
- **Separación de responsabilidades:** un archivo = una funcionalidad (un CPT por archivo, un módulo JS por objeto literal, una sección/componente por template-part).
- **Seguridad por diseño:** nonces específicos por acción, sanitización explícita, verificación de capacidades, rate-limiting y validación de archivos por extensión real (no MIME de cliente) desde el Sprint 5.
- **Progressive enhancement:** las secciones dependientes de contenido (CPT) se auto-ocultan si no hay posts publicados (`ce_cpt_has_posts()`).
- **JS namespace pattern:** cada módulo de `main.js` es un objeto (`ModuleXxx`) con su propio `init()`.
- **CSS con Design Tokens:** variables CSS centralizadas, sincronizadas dinámicamente con el Customizer.
- **Extensión aditiva:** desde el Sprint 3, toda ampliación a un archivo ya aprobado se hace agregando código al final (o en la sección correspondiente cuando es estrictamente necesario), nunca reescribiendo.

---

## 3. Estructura del tema

Ver `TREE.md` para el árbol completo y actualizado con estado por archivo, y `ARCHITECTURE.md` sección 1 para la explicación de cada carpeta. Resumen:

```
ce-construction-theme/
├── style.css, functions.php, header.php, footer.php, front-page.php
├── archive-servicio.php, single-servicio.php
├── archive-proyecto.php, single-proyecto.php
├── archive-equipo.php, single-equipo.php
├── archive-clientes.php, single-clientes.php
├── PROJECT_STATUS.md, TODO.md, TREE.md, HANDOFF.md, CHANGELOG.md,
│   DECISIONS.md, QA_REPORT.md, ARCHITECTURE.md
├── inc/            → toda la lógica PHP modular
├── template-parts/ → 10 secciones del home + 8 componentes reutilizables de contenido
└── assets/
    ├── css/main.css (22 secciones)
    ├── js/main.js (13 módulos ES6)
    └── img/ (vacía, pendiente de assets reales del cliente)
```

**Aún pendiente:** `page.php`, `single.php`, `comments.php`, `404.php`, `archive.php` genérico, `inc/widgets.php`, `screenshot.png`.

---

## 4. Módulos completamente terminados

1. Bootstrap del tema (`functions.php`, `inc/setup.php`)
2. Carga de assets (`inc/enqueue.php`)
3. Theme Customizer (`inc/customizer.php`)
4. 6 Custom Post Types + taxonomías
5. Metaboxes / campos personalizados (`inc/meta-boxes.php`)
6. Formulario de Cotización — backend completo + rate-limiting + retención (`inc/quote-form.php`)
7. SEO backend (`inc/seo.php`) — meta tags, OG, Schema Organization/Service/Project/Person/Organization-cliente, breadcrumbs
8. Helpers reutilizables (`inc/helpers.php`)
9. Sistema de diseño CSS (`assets/css/main.css`, 22 secciones)
10. Sistema JS modular (`assets/js/main.js`, 13 módulos)
11. Header, Footer, Front Page + 10 template-parts del home
12. Módulo Servicios completo (archive + single + relacionados + schema)
13. Auditoría de QA (`QA_REPORT.md`)
14. Módulo Proyectos completo (archive + single + galería/lightbox + relacionados + schema)
15. Correcciones QA Críticas/Altas (9 hallazgos, v0.4.1)
16. `ARCHITECTURE.md`
17. Módulo Equipo y Clientes completo (archive + single + schema para ambos)
18. `index.php` (Entregable 6A) — bloqueador crítico de arquitectura resuelto

Detalle línea por línea en `TODO.md` y `CHANGELOG.md` (v0.1.0 a v0.5.0).

## 5. Módulos parcialmente implementados

| Módulo | Qué existe | Qué falta |
|---|---|---|
| Formulario de cotización | Backend + AJAX + validación + email + adjuntos reales + rate-limiting + retención, todo funcional | Fallback sin JavaScript (fuera de alcance, D-005) |
| SEO | Meta tags, OG, Schema (5 tipos), breadcrumbs | Sitemap XML propio (o delegar a plugin) |
| QA | 9/29 hallazgos corregidos | 20 hallazgos Medios/Bajos/Mejoras futuras sin tocar (fuera de alcance del Sprint 5) |
| Componentes reutilizables del brief | Hero, Cards, Buttons, Forms, Modals, Alerts, Navbar, Footer, Breadcrumb, Gallery, Counter, Testimonials, CTA, Accordion | Timeline (sin sección que lo use aún), Sidebar de blog (sin plantilla de blog aún) |

## 6. Módulos pendientes (no iniciados)

1. `single.php`, `page.php`, `comments.php`, `404.php` (Blog y páginas genéricas — hoy cubiertas por el fallback funcional de `index.php`)
2. `archive.php` genérico (fallback específico para archivos; hoy `index.php` ya lo cubre de forma genérica)
3. `inc/widgets.php`
4. `screenshot.png`
5. Corrección de hallazgos Medios/Bajos/Mejoras futuras de `QA_REPORT.md` (con aprobación explícita)
6. Auditoría transversal de accesibilidad y performance

---

## 7. Dependencias entre módulos

- `header.php`/`footer.php` dependen de `inc/customizer.php` y `inc/helpers.php`.
- `front-page.php` depende de los 10 template-parts + todos los CPTs + assets ya enqueados.
- `archive-{cpt}.php`/`single-{cpt}.php` (Servicios, Proyectos, Equipo, Clientes) dependen de `template-parts/page-hero.php` (hero interno reutilizable), sus respectivos `content-{cpt}.php`, `inc/meta-boxes.php` (metadatos), e `inc/seo.php` (schema específico).
- `template-parts/quote-form.php` + `ModuleQuoteForm` (main.js) + `inc/quote-form.php` forman un contrato de 3 partes que debe cambiar sincronizado.
- `inc/cpt-clientes.php` (`has_archive`) y la rama de breadcrumbs de `cliente` en `inc/seo.php` están acopladas (ver D-025) — si se revierte una, debe revertirse la otra.
- `assets/css/main.css`/`assets/js/main.js` son monolíticos por diseño (ver `ARCHITECTURE.md` sección 9); cada sprint de contenido añade una sección numerada al final, nunca archivos separados.

---

## 8. Riesgos conocidos

Ver `PROJECT_STATUS.md` sección 6 para la tabla completa. Resumen actualizado:

- ✅ **`index.php` resuelto (Entregable 6A)** — el tema ya cumple el mínimo que WordPress exige para activarse con seguridad.
- 🟡 Dependencia de CDNs externos (Google Fonts, Font Awesome) — impacto en Core Web Vitals (QA-026/027, sin corregir).
- 🟡 Relación Servicio↔Proyecto (ambas direcciones) sigue siendo heurística por coincidencia de taxonomía, no un campo relacional explícito.
- 🟢 Sin fallback sin-JS en el formulario de cotización (decisión aceptada, D-005).
- 🟢 Retención de cotizaciones con plazo por defecto de 365 días — el cliente debe confirmar o ajustar este valor (D-018).
- ✅ Los 9 hallazgos QA Críticos/Altos que eran riesgos activos hasta v0.4.0 **ya están resueltos** desde v0.4.1.

## 9. Bugs conocidos

| ID | Descripción | Estado |
|---|---|---|
| BUG-001 | `ModuleModals` usaba `querySelector` en vez de `querySelectorAll` para los botones de cierre de modal. | ✅ Corregido (Sprint Header/Footer/Front Page, D-008, v0.2.0). |
| BUG-002 | `mkdir -p` con llaves no se expandió correctamente, generando carpetas corruptas. | ✅ Corregido manualmente antes de crear contenido en esas rutas. |
| BUG-003 | `template-parts/content-cliente.php` se creó (sesión previa, interrumpida) con clases CSS (`.ce-client-card*`) distintas a las ya definidas en `main.css` (`.ce-clients-grid__item`), lo que habría generado CSS duplicado. | ✅ Corregido en Sprint 5: el archivo (nunca entregado/aprobado) se reescribió para alinear con la convención ya existente. Ver `DECISIONS.md` D-027. |
| BUG-004 | `inc/cpt-clientes.php` tenía `has_archive => false`, lo que habría hecho que `archive-clientes.php` (pedido en Sprint 5) fuera inalcanzable vía URL amigable. | ✅ Corregido: `has_archive` cambiado a `true`, con el ajuste acoplado correspondiente en las ramas de breadcrumbs de `inc/seo.php`. Ver `DECISIONS.md` D-025. |
| BUG-005 | `.ce-mt-6`/`.ce-mb-6` se usaban desde el Sprint 3 en 10 archivos ya aprobados, pero nunca se definieron en `main.css` — el margen no se aplicaba, sin que ninguna auditoría previa (incluido `QA_REPORT.md`) lo detectara. | ✅ Corregido en Entregable 6A al construir `index.php` (mismo patrón de clases). Ver `DECISIONS.md` D-029. |

Los 9 hallazgos QA Críticos/Altos (QA-001 a QA-009) también se trataron como bugs corregidos — ver `QA_REPORT.md` y `DECISIONS.md` D-017 a D-024 para el detalle completo de cada uno (no se repiten aquí para no duplicar información, ya documentada exhaustivamente en esos dos archivos).

No hay bugs abiertos conocidos a la fecha de este documento.

---

## 10. Decisiones arquitectónicas importantes

Registro completo y acumulativo en `DECISIONS.md` (D-001 a D-029 a la fecha). Las más relevantes para continuar el desarrollo:

- **D-001 a D-008:** nonces por acción, CPT `cotizacion` no público, secciones auto-ocultas, SEO auto-desactivable, sin fallback sin-JS, `helpers.php` adelantado, flotantes/modales en footer, bugfix de `ModuleModals`.
- **D-009 a D-016:** `page-hero.php` reutilizable, relación heurística Servicio↔Proyecto, extensiones aditivas de `helpers.php`/`seo.php`, FAQ sin relación directa, Schema `Project` como `@type` múltiple, relación inversa Proyecto→Servicio, reutilización de clases CSS entre módulos.
- **D-017 a D-024:** las 8 correcciones QA Críticas/Altas (una decisión por hallazgo o par de hallazgos acoplados).
- **D-025:** `has_archive` habilitado para Cliente (cambio necesario, no discrecional).
- **D-026:** alcance deliberadamente más ligero para Equipo/Clientes (sin CTA/sidebar/formulario).
- **D-027:** reconciliación de `content-cliente.php` con la convención CSS ya existente.
- **D-028:** `index.php` diseñado como fallback genérico completo (no placeholder), cubriendo 4 contextos con ramas condicionales explícitas.
- **D-029:** corrección del bug preexistente `.ce-mt-6`/`.ce-mb-6` (detectado, no introducido, al construir `index.php`).

---

## 11. Convenciones utilizadas durante el desarrollo

- **Prefijo global:** `ce_`/`ce_construction_` en el 100% de las funciones PHP.
- **Clases CSS:** metodología BEM con bloque raíz `ce-`.
- **Meta keys:** prefijo `_ce_`.
- **Nonces:** patrón `ce_[verbo]_[entidad]_[meta|action]`.
- **Módulos JS:** objeto `ModuleNombreEnPascalCase` con `init()`, auto-verificando su marcado antes de operar.
- **Idioma:** `__()`/`esc_html__()` con text domain `ce-construction`, español por defecto.
- **Comentarios de archivo:** docblock inicial con `@package CE_Construction`.
- **Verificación de sintaxis:** balance de llaves/paréntesis (`grep -o`) + `node --check` para JS, ante la ausencia de PHP/WordPress real en el entorno de desarrollo.
- **Extensión aditiva por defecto:** desde el Sprint 3, ampliar un archivo aprobado significa agregar código al final (o dentro de la sección correspondiente cuando es estrictamente necesario, siempre documentando qué cambia y por qué), nunca reescribir sin autorización explícita.
- **Correcciones documentadas, no silenciosas:** todo bugfix (incluidos los de QA) se registra como una entrada formal en `DECISIONS.md` con ID, alternativas descartadas e impacto — nunca como un cambio no explicado.

---

## 12. Orden recomendado para continuar

1. **Blog y páginas genéricas:** `single.php`, `page.php`, `comments.php`, `404.php` (reemplazan el fallback de `index.php` con plantillas dedicadas y estilo completo).
2. **Extras de menor prioridad:** `inc/widgets.php`, `screenshot.png`, `archive.php` genérico.
3. **Hallazgos QA Medios/Bajos** de `QA_REPORT.md` (con aprobación explícita de cuáles corregir).
4. **Auditoría transversal final:** accesibilidad, performance (Core Web Vitals, auto-hospedar fuentes — QA-026/027), revisión cruzada de sanitización/escaping.

## 13. Próximo sprint recomendado

**Sprint 6B: "Blog y páginas genéricas"**
- `single.php`, `page.php`, `comments.php`, `404.php`

Justificación: con `index.php` resuelto, el tema ya es activable con seguridad. `single.php`/`page.php`/`404.php` ofrecerán una experiencia más refinada que el fallback genérico de `index.php` (que seguirá existiendo como el fallback final que WordPress exige, pero dejará de ser invocado en estos contextos específicos en cuanto existan las plantillas dedicadas). `comments.php` propio reemplazará el fallback de compatibilidad nativa de WordPress que usa hoy `index.php`.

---

## 14. Advertencias para evitar romper compatibilidad

- **No renombrar** ninguna función con prefijo `ce_`/`ce_construction_` sin actualizar todas sus referencias.
- **No renombrar** los `slug` de los CPTs ni taxonomías — romperían permalinks y metadatos ya guardados.
- **No eliminar** las claves de `get_theme_mod()` sin revisar cada template-part que las consume.
- **No modificar** la firma del handler AJAX del formulario de cotización sin actualizar simultáneamente `ModuleQuoteForm` (main.js) y el markup (`template-parts/quote-form.php`).
- **No revertir** `has_archive => true` en `inc/cpt-clientes.php` sin revertir también las ramas de breadcrumbs correspondientes en `inc/seo.php` (cambio acoplado, D-025).
- **No agregar** un segundo nonce global — la convención es un nonce por acción específica.
- **Todo archivo PHP nuevo** debe iniciar con `if ( ! defined( 'ABSPATH' ) ) { exit; }`.
- **Toda salida a HTML** debe pasar por la función de escaping correspondiente.
- **Antes de asumir que un archivo existe** por haberse mencionado en una respuesta anterior del chat: verificar contra `TREE.md` o el sistema de archivos real — ya ocurrieron casos (BUG-002, BUG-003, BUG-004) de discrepancias reales entre lo asumido y el disco.
- **El rate-limiting del formulario** usa `REMOTE_ADDR` sin soporte de proxies/CDN (`X-Forwarded-For`) — si el sitio se despliega detrás de un proxy/CDN, todos los visitantes compartirían el mismo límite. Revisar antes de producción si aplica.
- **El cron de retención** (`ce_construction_quote_cleanup_event`) se programa en `after_switch_theme` — si el tema nunca se "reactiva" (por ejemplo, en una instalación ya existente donde este código se agrega vía actualización), el cron podría no programarse automáticamente. Verificar con `wp_next_scheduled()` tras cualquier despliegue a producción.

## 15. Lista de archivos críticos del proyecto

- `functions.php` — sin este archivo, ningún módulo de `inc/` se carga.
- `inc/enqueue.php` — sin este archivo, no se cargan CSS/JS ni se localiza `ceConstructionData`.
- `inc/customizer.php` — sin este archivo, el sitio pierde toda personalización.
- `inc/helpers.php` — sin este archivo, varios archivos producen errores fatales de PHP (funciones no definidas), incluyendo ahora las 4 plantillas de Equipo/Proyectos que usan sus funciones de relación.
- `inc/quote-form.php` — sin este archivo, el endpoint AJAX del formulario no existe, y tampoco el cron de retención.
- `inc/seo.php` — sin este archivo, ninguno de los 5 tipos de Schema.org del proyecto se emite, y las breadcrumbs HTML dejan de funcionar en todo el sitio.
- `header.php`/`footer.php` — cargados por **toda** plantilla; un error de sintaxis aquí rompe el sitio completo.
- `index.php` — fallback obligatorio de WordPress; sin este archivo, single de blog/página, archivos genéricos, búsqueda y 404 no tendrían ninguna plantilla que WordPress pudiera resolver.
- `assets/css/main.css`/`assets/js/main.js` — ya enqueados en `inc/enqueue.php`; si se eliminan, todas las páginas cargan sin estilos ni interactividad.

---

# Prompt para continuar el proyecto

Copia y pega el siguiente prompt en una conversación nueva para retomar el proyecto exactamente donde quedó, sin perder contexto:

```
Estoy retomando el desarrollo del tema de WordPress "CE Construction".
Te adjunto los archivos de control del proyecto: PROJECT_STATUS.md, TODO.md,
TREE.md, CHANGELOG.md, DECISIONS.md, QA_REPORT.md, ARCHITECTURE.md y este
mismo HANDOFF.md.

Estos archivos son la fuente oficial y verificada del estado del proyecto.
No asumas nada del historial de chat que no esté reflejado en ellos.

Reglas para continuar:
- Conserva toda la arquitectura y las convenciones ya documentadas en
  HANDOFF.md (secciones 2, 10 y 11), ARCHITECTURE.md y DECISIONS.md.
- No reinicies el proyecto ni reescribas archivos ya marcados como ✅ en
  TREE.md, salvo que exista un error real (indícamelo explícitamente antes
  de tocar el archivo).
- Antes de generar código, confirma el inventario leyendo TREE.md y TODO.md,
  y dime si detectas alguna inconsistencia contra lo que te comparto.
- Trabajaremos sprint por sprint. Cada entrega debe contener únicamente los
  archivos del siguiente sprint pendiente según el orden recomendado en
  HANDOFF.md sección 12 (empezando por index.php si aún no existe).
- Al finalizar cada sprint, actualiza los 7 documentos de control
  (PROJECT_STATUS.md, TODO.md, TREE.md, CHANGELOG.md, DECISIONS.md,
  QA_REPORT.md si aplica, y ARCHITECTURE.md si cambia la arquitectura) antes
  de detenerte, y espera mi aprobación antes de avanzar al siguiente sprint.

El siguiente sprint a desarrollar es: [indica aquí el módulo, o escribe
"el que recomiendes según HANDOFF.md sección 13" para que continúes con
el sprint recomendado].
```
