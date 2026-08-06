# CE Construction — HANDOFF.md
### Documento oficial de transferencia entre sesiones

> Este documento, junto con `PROJECT_STATUS.md`, `TODO.md`, `TREE.md`, `CHANGELOG.md`, `DECISIONS.md`, `QA_REPORT.md` y `ARCHITECTURE.md`, es la fuente oficial del estado del proyecto. Si esta conversación se corta por límite de mensajes/tokens, cualquier sesión nueva debe poder retomar el trabajo exactamente desde aquí, sin releer el historial completo de chat.

**Versión de referencia:** v0.6.2 (ver `CHANGELOG.md`)
**Última sesión de trabajo:** Sprint 6B ("Blog y páginas genéricas"), Entregable 6B.2 — `single.php` + `comments.php` completados. Esta actualización de `HANDOFF.md` corresponde a un cambio metodológico importante (política de actualización incremental de documentación, ver sección 16), no a un nuevo Entregable de código — bajo la nueva regla, `HANDOFF.md` ya no se actualiza automáticamente en cada Entregable.

---

## 1. Resumen ejecutivo

CE Construction es un tema profesional de WordPress a medida, desarrollado 100% en PHP/HTML5/CSS3/JS moderno (sin builders visuales), con arquitectura modular. A la fecha:

- **Backend completo:** 6 Custom Post Types + 1 CPT interno de cotizaciones, Theme Customizer con 7 secciones, metaboxes seguros, formulario de cotización funcional (AJAX + email + adjuntos + nonce + rate-limiting + retención automática), SEO propio con auto-desactivación si hay plugin SEO.
- **Frontend completo:** Home, Servicios (archive+single), Proyectos (archive+single, con galería/lightbox), Equipo (archive+single) y Clientes (archive+single) — los 6 CPTs de contenido ya tienen presencia completa en el frontend. `index.php` (Entregable 6A) cubre single de blog/página, archivos genéricos, búsqueda y 404 como fallback funcional.
- **QA:** auditoría completa realizada (`QA_REPORT.md`, 29 hallazgos); los 9 Críticos/Altos ya corregidos (v0.4.1); los 20 Medios/Bajos/Mejoras futuras siguen documentados y sin tocar.
- **Documentación de arquitectura:** `ARCHITECTURE.md` describe la arquitectura real (no propuesta) del proyecto completo.
- **Bloqueador crítico resuelto:** `index.php` ya existe — el tema cumple el mínimo que WordPress exige (`style.css` + `index.php`) para ser reconocido y activado con seguridad.
- **Pendiente funcional:** `404.php` (Entregable 6B.3, cierra el Sprint 6B), `inc/widgets.php`, `screenshot.png`.

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

**Aún pendiente:** `404.php` (Entregable 6B.3, cierra el Sprint 6B), `archive.php` genérico, `inc/widgets.php`, `screenshot.png`.

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
19. `page.php` (Sprint 6B, Entregable 6B.1)
20. `single.php` + `comments.php` (Sprint 6B, Entregable 6B.2)

Detalle línea por línea en `TODO.md` y `CHANGELOG.md` (v0.1.0 a v0.5.0).

## 5. Módulos parcialmente implementados

| Módulo | Qué existe | Qué falta |
|---|---|---|
| Formulario de cotización | Backend + AJAX + validación + email + adjuntos reales + rate-limiting + retención, todo funcional | Fallback sin JavaScript (fuera de alcance, D-005) |
| SEO | Meta tags, OG, Schema (6 tipos: Organization, Service, Project, Person, Organization-cliente, BlogPosting), breadcrumbs | Sitemap XML propio (o delegar a plugin) |
| QA | 9/29 hallazgos corregidos | 20 hallazgos Medios/Bajos/Mejoras futuras sin tocar (fuera de alcance del Sprint 5) |
| Componentes reutilizables del brief | Hero, Cards, Buttons, Forms, Modals, Alerts, Navbar, Footer, Breadcrumb, Gallery, Counter, Testimonials, CTA, Accordion | Timeline (sin sección que lo use aún), Sidebar de blog (sin plantilla de blog aún) |

## 6. Módulos pendientes (no iniciados)

1. `404.php` (Entregable 6B.3, siguiente y último — cierra el Sprint 6B)
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
- **D-030:** adopción de la metodología permanente de Gestión de Sprints y Entregables.
- **D-031:** `page.php` reutiliza siempre el hero interno, independientemente de si la página tiene imagen destacada.

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

1. **Entregable 6B.3 (siguiente y último del Sprint 6B): `404.php`.**
3. **Extras de menor prioridad:** `inc/widgets.php`, `screenshot.png`, `archive.php` genérico.
4. **Hallazgos QA Medios/Bajos** de `QA_REPORT.md` (con aprobación explícita de cuáles corregir).
5. **Auditoría transversal final:** accesibilidad, performance (Core Web Vitals, auto-hospedar fuentes — QA-026/027), revisión cruzada de sanitización/escaping.

## 13. Próximo sprint recomendado

**Sprint 6B en curso — próximo y último Entregable: 6B.3 "404.php"**

Justificación: `page.php` (6B.1) y `single.php` + `comments.php` (6B.2) ya están resueltos. `404.php` es independiente y de bajo acoplamiento (no depende de ningún otro archivo pendiente). Al completarse, el Sprint 6B queda **COMPLETADO** en su totalidad, y corresponde actualizar el Roadmap y dividir el Sprint 7 (propuesto: `inc/widgets.php` + `screenshot.png` + hallazgos QA Medios) en sus Entregables correspondientes.

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
- `inc/seo.php` — sin este archivo, ninguno de los 6 tipos de Schema.org del proyecto se emite, y las breadcrumbs HTML dejan de funcionar en todo el sitio.
- `header.php`/`footer.php` — cargados por **toda** plantilla; un error de sintaxis aquí rompe el sitio completo.
- `index.php` — fallback obligatorio de WordPress; sin este archivo, single de blog/página, archivos genéricos, búsqueda y 404 no tendrían ninguna plantilla que WordPress pudiera resolver.
- `assets/css/main.css`/`assets/js/main.js` — ya enqueados en `inc/enqueue.php`; si se eliminan, todas las páginas cargan sin estilos ni interactividad.

---

## 16. Metodología permanente: Gestión automática de Sprints y Entregables

> **Regla permanente del proyecto**, incorporada tras el Entregable 6A. Aplica a todo el desarrollo restante del proyecto, sin excepción, hasta que se documente explícitamente lo contrario.

### Principio general

Cada Sprint debe dividirse automáticamente en uno o más **Entregables**. Cada Entregable representa una **unidad funcional completa**, lista para producción. El único propósito de esta división es facilitar el desarrollo y evitar interrupciones por límite de contexto o de mensajes (como ya ocurrió una vez en el Sprint 5). **La división nunca debe reducir la calidad del código ni simplificar la implementación** — la reducción aplica exclusivamente al *alcance* de trabajo realizado en cada sesión, nunca a su profundidad o rigor.

### Reglas para los Entregables

Cada Entregable debe:
- Tener un objetivo único y claramente definido.
- Ser una unidad funcional completa (no un archivo a medias, no una función parcial).
- Poder finalizarse completamente en una sola sesión.
- Mantener exactamente los mismos estándares de calidad del proyecto (arquitectura, modularidad, seguridad, SEO, accesibilidad, rendimiento, UX/UI, mantenibilidad, documentación).
- Respetar toda la arquitectura existente (ver `ARCHITECTURE.md`).
- Mantener compatibilidad con WordPress 7.x y PHP 8.x.
- Seguir WordPress Coding Standards.
- No generar código duplicado.
- No reescribir archivos existentes salvo por un bug crítico.
- Realizar únicamente modificaciones aditivas cuando sea necesario ampliar archivos existentes (`helpers.php`, `seo.php`, `main.css`, `main.js`, o cualquier otro ya aprobado).

### Calidad: la regla que no admite excepciones

Reducir el alcance de un Entregable **nunca** significa reducir la calidad. Cada archivo se desarrolla con el mismo nivel de rigor descrito en toda la documentación del proyecto (`ARCHITECTURE.md`, `DECISIONS.md`), sin importar cuán pequeño sea el Entregable.

### Flujo obligatorio al finalizar cada Entregable

1. Verificar sintaxis PHP.
2. Verificar sintaxis JavaScript.
3. Verificar dependencias e includes.
4. Actualizar toda la documentación que haya cambiado (`PROJECT_STATUS.md`, `TODO.md`, `TREE.md`, `CHANGELOG.md`, `DECISIONS.md`, `HANDOFF.md`, `ARCHITECTURE.md` si corresponde).
5. Marcar el Entregable como **Completado**.
6. Indicar cuál es el siguiente Entregable recomendado del Sprint actual.
7. Generar automáticamente el prompt para continuar con ese siguiente Entregable.
8. Detenerse y esperar aprobación explícita del cliente.
9. No generar código duplicado.
10. No reescribir archivos existentes salvo por un bug crítico.
11. Realizar únicamente modificaciones aditivas cuando sea necesario ampliar archivos existentes.

### Tamaño de los Entregables

El tamaño de cada Entregable debe minimizar el consumo de contexto/mensajes, pero **nunca** limitar la arquitectura ni la calidad del código. Si un archivo requiere una implementación extensa para quedar correctamente terminado, debe desarrollarse completamente dentro de su Entregable — nunca se deja un archivo parcialmente implementado, y nunca se divide un mismo archivo entre varias entregas salvo que exista una razón técnica excepcional (documentada explícitamente si ocurre).

### Finalización de un Sprint

Cuando todos los Entregables de un Sprint estén completados:
- Marcar el Sprint como **COMPLETADO**.
- Actualizar el Roadmap del proyecto (ver `CHANGELOG.md` → "Próximas versiones").
- Indicar cuál es el siguiente Sprint recomendado.
- Dividir automáticamente ese siguiente Sprint en los Entregables que se consideren adecuados.
- Generar el prompt para iniciar el primer Entregable del nuevo Sprint.

### Antecedente que motivó esta regla

El Sprint 5 se interrumpió por límite de mensajes a mitad de desarrollo, requiriendo una sesión adicional de verificación y continuación (ver sección 9, y `PROJECT_STATUS.md`/`CHANGELOG.md` v0.5.0). Esta metodología formaliza, como práctica permanente, la división preventiva en unidades más pequeñas que ya se venía aplicando de forma implícita (Sprint 5 en 3 fases, luego el "Entregable 6A" independiente para `index.php`), evitando depender de que una interrupción ya haya ocurrido para trabajar de forma incremental.

---

### Refinamiento (tras el Entregable 6B.2): política de actualización incremental de documentación

> Ver `DECISIONS.md` D-034 para el registro formal completo de este refinamiento.

Al aplicar la metodología anterior durante varios Entregables consecutivos, se detectó que actualizar los 6-7 documentos de control en cada cierre generaba desgaste de contexto/mensajes sin aportar valor cuando varios de ellos no tenían ningún cambio real que registrar. Se adopta, como extensión permanente de esta misma sección 16, la siguiente política:

**Regla general:** cada documento de control se actualiza únicamente cuando cambia lo que ese documento específico existe para registrar. Antes de editar cualquier documento, se verifica explícitamente si tiene algo real que registrar; si no, se declara expresamente "sin cambios" en vez de tocarlo.

**Criterios de actualización por documento:**

| Documento | Se actualiza únicamente cuando... |
|---|---|
| `PROJECT_STATUS.md` | Cambia el estado del proyecto, del Sprint, o de un Entregable. |
| `TODO.md` | Se completa un Entregable, cambia el backlog, aparecen tareas nuevas, o cambia la prioridad de tareas existentes. |
| `CHANGELOG.md` | Hay modificación de código, modificación relevante de documentación, o una nueva versión del proyecto. |
| `TREE.md` | Cambia la estructura del proyecto (archivo/carpeta nueva, eliminada, o cambio estructural del árbol) — **no** por cambios de contenido de un archivo ya existente. |
| `ARCHITECTURE.md` | Cambia la arquitectura, el flujo del sistema, las dependencias, la organización interna del tema, o la metodología permanente de desarrollo — **no** por cambios normales de implementación. |
| `DECISIONS.md` | Existe una nueva decisión arquitectónica, técnica, metodológica, de compatibilidad, o de alcance — **no** cambios menores que no representen una decisión permanente. |
| `QA_REPORT.md` | Se corrige un hallazgo QA, aparece uno nuevo, o cambia el estado de uno existente. |
| `HANDOFF.md` | **Deja de actualizarse en cada Entregable.** Solo se actualiza ante: finalización completa de un Sprint; cierre de una sesión de trabajo que pueda continuarse después; o un cambio importante de arquitectura que afecte la continuidad del proyecto. |

**Nuevo documento permanente: `CURRENT_SPRINT.md`.** Referencia oficial y compacta del Sprint en curso, pensada para continuar un Sprint interrumpido sin releer toda la documentación del proyecto. Se actualiza **en cada Entregable** (a diferencia de `HANDOFF.md`, que ya no lo hace). Contiene como mínimo: Sprint actual y su estado, lista de Entregables con su estado, resumen muy breve del trabajo del último Entregable (sin duplicar `PROJECT_STATUS.md`), archivos creados/modificados del Sprint actual, qué documentación fue realmente actualizada, el próximo Entregable, riesgos abiertos específicos del Sprint, y observaciones para continuar sin perder contexto.

**Flujo obligatorio actualizado al finalizar cada Entregable** (reemplaza el paso 4 original de esta sección para reflejar la actualización incremental):

1. Verificar sintaxis PHP.
2. Verificar sintaxis JavaScript.
3. Verificar dependencias e includes.
4. Actualizar **únicamente** la documentación que realmente haya cambiado (según la tabla de criterios de arriba) y actualizar siempre `CURRENT_SPRINT.md`.
5. Verificar la consistencia entre `CURRENT_SPRINT.md` y `PROJECT_STATUS.md`.
6. Indicar qué documentos fueron modificados y cuáles no requirieron cambios (y por qué).
7. Marcar el Entregable como **Completado**.
8. Indicar cuál es el siguiente Entregable recomendado del Sprint actual.
9. Generar automáticamente el prompt para continuar con ese siguiente Entregable.
10. Detenerse y esperar aprobación explícita del cliente.

**Al completarse el último Entregable de un Sprint**, además de lo anterior:
- Marcar el Sprint como **COMPLETADO**.
- Actualizar `HANDOFF.md` (una de sus 3 únicas causas de actualización).
- Actualizar el Roadmap del proyecto (`CHANGELOG.md` → "Próximas versiones") si corresponde.
- Dividir automáticamente el siguiente Sprint en Entregables.
- Actualizar `CURRENT_SPRINT.md` para reflejar el nuevo Sprint.
- Generar el prompt para iniciar el primer Entregable del siguiente Sprint.
- Detenerse y esperar aprobación explícita del cliente.

---

# Prompt para continuar el proyecto

Copia y pega el siguiente prompt en una conversación nueva para retomar el proyecto exactamente donde quedó, sin perder contexto:

```
Estoy retomando el desarrollo del tema de WordPress "CE Construction".
Te adjunto los archivos de control del proyecto: PROJECT_STATUS.md, TODO.md,
TREE.md, CHANGELOG.md, DECISIONS.md, QA_REPORT.md, ARCHITECTURE.md,
CURRENT_SPRINT.md y este mismo HANDOFF.md.

CURRENT_SPRINT.md es la referencia principal para continuar el Sprint en
curso sin releer todo lo demás. Estos archivos son la fuente oficial y
verificada del estado del proyecto. No asumas nada del historial de chat
que no esté reflejado en ellos.

Reglas para continuar:
- Conserva toda la arquitectura y las convenciones ya documentadas en
  HANDOFF.md (secciones 2, 10, 11 y 16), ARCHITECTURE.md y DECISIONS.md.
- Aplica la metodología permanente de Gestión de Sprints y Entregables
  (HANDOFF.md sección 16), incluida la política de actualización
  incremental de documentación: al cierre de cada Entregable, actualiza
  únicamente los documentos cuyo contenido realmente cambió (ver la tabla
  de criterios en HANDOFF.md sección 16), actualiza siempre
  CURRENT_SPRINT.md, e indica expresamente cuáles no requirieron cambios.
  HANDOFF.md NO se actualiza en cada Entregable — solo al cerrar un Sprint
  completo, una sesión continuable, o ante un cambio de arquitectura.
- No reinicies el proyecto ni reescribas archivos ya marcados como ✅ en
  TREE.md, salvo que exista un error real (indícamelo explícitamente antes
  de tocar el archivo).
- Antes de generar código, confirma el inventario leyendo CURRENT_SPRINT.md
  y TREE.md, y dime si detectas alguna inconsistencia contra lo que te
  comparto.
- Trabajaremos Entregable por Entregable dentro del Sprint actual, según
  CURRENT_SPRINT.md.
- Al finalizar cada Entregable, sigue el flujo obligatorio de HANDOFF.md
  sección 16 y espera mi aprobación antes de avanzar.
- Cuando se complete el último Entregable de un Sprint, márcalo como
  Sprint COMPLETADO, actualiza HANDOFF.md, actualiza el Roadmap, divide
  automáticamente el siguiente Sprint en Entregables, y actualiza
  CURRENT_SPRINT.md para reflejar el nuevo Sprint.

El siguiente Entregable a desarrollar es: [indícalo aquí, o escribe
"el que indique CURRENT_SPRINT.md" para que continúes con el Entregable
recomendado].
```
