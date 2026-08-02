# CE Construction — PROJECT STATUS

> Este documento es la fuente oficial de verdad del proyecto.
> Se actualiza al finalizar cada módulo. No se reinicia ni se resume: solo se agrega/actualiza estado.

**Última actualización:** Sprint 4 completado — Módulo de Proyectos (archive + single + galería/lightbox + relacionados + schema). El cliente optó explícitamente por continuar con este sprint de contenido antes que el sprint de correcciones de QA recomendado en la versión anterior; los 29 hallazgos de `QA_REPORT.md` siguen pendientes de aprobación, sin cambios.

**Versión de proyecto correspondiente:** v0.4.0 (ver `CHANGELOG.md`)

---

## 1. Estado actual del proyecto

El tema tiene:
- Backend 100% funcional (CPTs, Customizer, metaboxes, formulario de cotización con AJAX/Nonce/email/adjuntos, SEO backend).
- Sistema de diseño frontend completo (CSS) y capa de interactividad (JS) implementados y verificados sintácticamente.
- Capa de plantillas del **Home** completa y funcional: `header.php`, `footer.php`, `front-page.php` y los 10 template-parts que arman la portada.

El tema **aún no es instalable/activable de forma segura en WordPress** porque falta `index.php` (WordPress exige como mínimo `style.css` + `index.php` para reconocer un tema). Ver sección de Riesgos.

---

## 2. Módulos terminados

| # | Módulo | Archivos | Estado |
|---|--------|----------|--------|
| 1 | Bootstrap del tema | `functions.php`, `inc/setup.php` | ✅ |
| 2 | Carga de assets | `inc/enqueue.php` | ✅ |
| 3 | Customizer | `inc/customizer.php` | ✅ |
| 4 | Custom Post Types (6) | `inc/cpt-servicios.php`, `inc/cpt-proyectos.php`, `inc/cpt-testimonios.php`, `inc/cpt-equipo.php`, `inc/cpt-clientes.php`, `inc/cpt-faq.php` | ✅ |
| 5 | Metaboxes / campos personalizados | `inc/meta-boxes.php` | ✅ |
| 6 | Formulario de Cotización (backend) | `inc/quote-form.php` | ✅ |
| 7 | SEO backend | `inc/seo.php` | ✅ (breadcrumbs enganchados en `header.php`) |
| 8 | Helpers reutilizables | `inc/helpers.php` | ✅ (creado en este módulo por dependencia real) |
| 9 | Sistema de diseño CSS | `assets/css/main.css` | ✅ |
| 10 | Sistema JS modular | `assets/js/main.js` | ✅ (1 bugfix aplicado en este módulo, ver Decisiones) |
| 11 | **Header / Footer / Front Page** | `header.php`, `footer.php`, `front-page.php`, `template-parts/*` (10 archivos) | ✅ |
| 12 | **Sprint 3 — Módulo Servicios (frontend completo)** | `archive-servicio.php`, `single-servicio.php`, `template-parts/content-servicio.php`, `template-parts/sidebar-servicios.php`, `template-parts/page-hero.php`, extensión de `inc/helpers.php`, extensión de `inc/seo.php`, extensión de `assets/css/main.css`, extensión de `assets/js/main.js` | ✅ |
| 13 | **Sprint QA — Auditoría de Integración (sin corrección de código)** | `QA_REPORT.md` (29 hallazgos: 1 crítico, 8 altos, 9 medios, 5 bajos, 6 mejoras futuras) | ✅ Auditoría completa; correcciones pendientes de tu aprobación |
| 14 | **Sprint 4 — Módulo Proyectos (frontend completo)** | `archive-proyecto.php`, `single-proyecto.php`, `template-parts/content-proyecto.php`, `template-parts/sidebar-proyectos.php`, extensión de `inc/helpers.php`, extensión de `inc/seo.php`, extensión de `assets/css/main.css` (sin cambios en `assets/js/main.js`: el lightbox ya existente detecta el nuevo marcado automáticamente) | ✅ |

## 3. Módulos en desarrollo

Ninguno activo. Sprint 4 (Proyectos) completado. **Los 29 hallazgos de `QA_REPORT.md` siguen sin corregir**, pendientes de tu aprobación explícita (1 crítico, 8 altos, 9 medios, 5 bajos, 6 mejoras futuras). Ver sección 9 de este documento.

## 4. Módulos pendientes

| # | Módulo | Archivos esperados | Prioridad |
|---|--------|---------------------|-----------|
| 12 | Archivo raíz obligatorio | `index.php` | 🔴 Crítica (bloquea activación segura del tema) |
| 13 | ~~Servicios (frontend)~~ | ~~`archive-servicio.php`, `single-servicio.php`~~ | ✅ Completado en Sprint 3 (ver módulo 12 en sección 2) |
| 14 | ~~Proyectos (frontend)~~ | ~~`archive-proyecto.php`, `single-proyecto.php` (con galería + lightbox)~~ | ✅ Completado en Sprint 4 (ver módulo 14 en sección 2) |
| 15 | Blog y páginas genéricas | `single.php`, `page.php`, `comments.php`, `404.php` | Media |
| 16 | Archivos de equipo/clientes (opcional) | `archive-miembro_equipo.php` o reutilizar `archive.php` genérico | Baja |
| 17 | Widgets personalizados | `inc/widgets.php` | Baja |
| 18 | `screenshot.png` del tema | — | Baja (cosmético, no bloquea funcionalidad) |
| 19 | Revisión final de accesibilidad (ARIA, foco, contraste) | transversal | Media |
| 20 | Revisión final de performance (Core Web Vitals, tamaño de imágenes) | transversal | Media |

## 5. Decisiones arquitectónicas tomadas

1. **Nonces por módulo, no globales**: cada metabox y el formulario de cotización usan su propio nonce con acción específica (`ce_save_servicio_meta`, `ce_quote_form_action`, etc.), siguiendo el principio de menor privilegio.
2. **CPT interno `cotizacion`**: las solicitudes del formulario se guardan como un CPT no-público (`show_ui` sí, `public` no, `create_posts` deshabilitado) para que sean administrables desde wp-admin sin permitir creación manual accidental.
3. **`ce_cpt_has_posts()` como guardia de secciones**: las secciones del home (Servicios, Proyectos, Testimonios, Galería) se auto-ocultan si el CPT correspondiente no tiene contenido publicado, evitando secciones vacías en una instalación limpia.
4. **SEO propio desactivable**: `inc/seo.php` verifica `WPSEO_VERSION`/`RankMath` antes de imprimir metas/schema, para no duplicar salida si el cliente instala un plugin SEO dedicado a futuro.
5. **JS modular por objeto literal (namespace pattern)**: cada funcionalidad (`ModuleMobileNav`, `ModuleQuoteForm`, etc.) es un objeto independiente con su propio `init()`, auto-verificando la existencia de su marcado antes de operar. Facilita mantenimiento y evita errores en páginas donde falte un componente.
6. **Helpers centralizados en `inc/helpers.php`**: se adelantó su creación (originalmente módulo de menor prioridad) porque `header.php`/`footer.php`/`template-parts/*` dependían directamente de funciones como `ce_render_social_icons()`, `ce_get_whatsapp_number()`, `ce_get_gallery_ids()`.
7. **Formulario de cotización 100% AJAX**: no tiene fallback funcional sin JavaScript en esta fase (no hay acción `POST` tradicional en el `<form>`). Aceptado como decisión de alcance; puede añadirse un fallback en un módulo futuro si se requiere soporte sin JS.
8. **Sprint 3 — Relación Servicio↔Proyecto por coincidencia de taxonomía**: no existe un campo relacional directo entre los CPTs `servicio` y `proyecto`. La sección "Proyectos relacionados" en `single-servicio.php` infiere la relación comparando nombres de términos entre `categoria_servicio` y `categoria_proyecto`, con fallback a los más recientes. Ver `DECISIONS.md` D-010.
9. **Sprint 3 — `template-parts/page-hero.php` reutilizable**: se creó un hero interno genérico (distinto del hero de portada) parametrizado vía `$args` de `get_template_part()`, pensado para reutilizarse también en el futuro módulo de Proyectos. Ver `DECISIONS.md` D-009.
10. **Sprint 3 — Extensiones aditivas, no reescrituras**: `inc/helpers.php` e `inc/seo.php` se extendieron agregando funciones nuevas al final del archivo (funciones de relación y de Schema.org de Servicio/Breadcrumbs respectivamente), sin modificar ni una línea de las funciones ya existentes. Ver `DECISIONS.md` D-011, D-012.
11. **Sprint 3 — FAQ sin relación directa a Servicio**: al no existir un campo o taxonomía que vincule `ce_faq` con `servicio`, la sección "Preguntas Frecuentes" en `single-servicio.php` muestra las FAQ más recientes de forma general (no filtradas por servicio). Documentado como limitación conocida, no como bug. Ver `DECISIONS.md` D-013.
12. **Sprint 4 — Schema.org `Project` con `@type` múltiple**: Schema.org no define un tipo `Project` en su vocabulario estándar. Se usó `"@type": ["CreativeWork", "Project"]` (JSON-LD permite `@type` como array) para cumplir literalmente el requisito del brief ("Schema.org Project") sin sacrificar la validez del schema ante buscadores, que sí reconocen `CreativeWork`. Ver `DECISIONS.md` D-014.
13. **Sprint 4 — Relación inversa Proyecto→Servicio simétrica a D-010**: `ce_get_related_services_for_project()` usa la misma heurística de coincidencia de nombre de taxonomía que `ce_get_related_projects()` (Sprint 3), en dirección inversa. Ver `DECISIONS.md` D-015.
14. **Sprint 4 — Reutilización deliberada de clases CSS "de Servicios" para Proyectos**: `single-proyecto.php` reutiliza `.ce-service-nav` (navegación prev/next) y `.ce-service-content` (tipografía de cuerpo) en vez de crear clases `.ce-project-nav`/`.ce-project-content` duplicadas, ya que son estructuralmente idénticas y el nombre no tiene efecto semántico visible. Ver `DECISIONS.md` D-016.
15. **Sprint 4 — Sin cambios en `assets/js/main.js`**: la galería de `single-proyecto.php` reutiliza exactamente el mismo patrón de marcado (`.ce-gallery-item[data-full]`) que ya usa `template-parts/gallery.php` (home). `ModuleLightbox` ya escanea ese selector globalmente en todo el DOM al cargar, por lo que el lightbox del nuevo módulo de Proyectos funciona sin ninguna modificación al JS existente.

## 6. Riesgos detectados

| Riesgo | Severidad | Detalle | Mitigación planeada |
|---|---|---|---|
| Falta `index.php` | 🔴 Alta | WordPress requiere `style.css` + `index.php` como mínimo para un tema válido. Hoy el tema depende 100% de `front-page.php`, lo cual cubre el home pero no garantiza un fallback seguro en el listado de temas de wp-admin. | Crear `index.php` en el próximo módulo de plantillas (ítem #12 de pendientes). |
| Secciones sin contenido en instalación limpia | 🟡 Media | Si el administrador no carga Servicios/Proyectos/Testimonios, esas secciones del home se ocultan automáticamente (por diseño), pero el home puede verse "corto" hasta que se cargue contenido. | Es comportamiento esperado; se documentará en un futuro README de instalación. |
| Dependencia de Google Fonts y CDN de Font Awesome | 🟡 Media | `inc/enqueue.php` carga fuentes e iconos desde CDNs externos. Afecta Core Web Vitals y cumplimiento GDPR en algunos países (carga de recursos de terceros). | Evaluar auto-hospedar fuentes/iconos en el módulo de performance (#20). |
| Sin fallback sin-JS en formulario de cotización | 🟢 Baja | Si el visitante tiene JS deshabilitado, el formulario no se puede enviar. | Decisión de alcance aceptada (ver Decisión #7); revisar si el cliente lo requiere. |
| `screenshot.png` ausente | 🟢 Baja | No bloquea funcionalidad, solo estética en el selector de temas de wp-admin. | Pendiente de diseño gráfico final del tema. |
| Relación Servicio↔Proyecto heurística (Sprint 3) | 🟡 Media | "Proyectos relacionados" en `single-servicio.php` depende de que los nombres de término de `categoria_servicio` y `categoria_proyecto` coincidan textualmente; si el admin nombra las categorías de forma distinta en ambos CPTs, el fallback mostrará proyectos recientes no necesariamente relacionados. | Evaluar en un futuro sprint un campo relacional explícito (ej. selector de proyectos relacionados en el metabox del servicio) si se requiere precisión editorial. |
| FAQ sin relación a Servicio (Sprint 3) | 🟢 Baja | La sección FAQ del single de servicio muestra las 5 FAQ más recientes del sitio, no necesariamente relacionadas con ese servicio específico. | Aceptado como decisión de alcance (D-013); se puede añadir una taxonomía compartida o campo relacional en un sprint futuro. |
| Bypass de validación de tipo de archivo (QA-001) | 🔴 Crítica | El formulario de cotización valida el tipo de archivo adjunto usando el MIME falsificable enviado por el cliente y una lista de extensiones demasiado amplia (global de WordPress), no el whitelist real (PDF/JPG/PNG/WEBP). Ver `QA_REPORT.md` QA-001 para el detalle técnico completo. | Pendiente de tu aprobación para aplicar el fix documentado en QA-001 (cambio acotado a `inc/quote-form.php`). |
| Archivos adjuntos huérfanos sin límite de disco (QA-002) | 🟠 Alta | Los adjuntos del formulario nunca se registran como attachment de WordPress ni se limpian; crecimiento de disco sin control. | Pendiente de aprobación — ver QA-002. |
| Sin rate-limiting en el formulario público (QA-004) | 🟠 Alta | El endpoint AJAX solo tiene honeypot; vulnerable a spam scripted. | Pendiente de aprobación — ver QA-004. |
| Fallo de contraste WCAG AA verificado (QA-005) | 🟠 Alta | `--ce-color-secondary` sobre blanco mide 2.67:1 (mínimo AA: 4.5:1 para texto normal). Afecta `.ce-eyebrow` y estados hover de varios enlaces. | Pendiente de aprobación — ver QA-005. |
| Sidebar de footer "Columna 1" registrado pero nunca renderizado (QA-006) | 🟠 Alta | Funcionalidad de administración silenciosamente rota: un admin puede agregar widgets a esa columna y nunca verlos en el sitio. | Pendiente de aprobación — ver QA-006. |
| Relación inversa Proyecto↔Servicio heurística (Sprint 4) | 🟡 Media | "Servicios relacionados" en `single-proyecto.php` depende de la misma coincidencia textual de nombres de taxonomía que el riesgo ya documentado para Servicios (ver fila anterior de Sprint 3); mismo mecanismo, ahora en dirección inversa. | Misma mitigación propuesta: campo relacional explícito en un sprint futuro si se requiere precisión editorial. |

## 7. Próximo módulo recomendado

**Sprint 5 (recomendado): "Correcciones de QA" — el hallazgo crítico QA-001 y los 8 hallazgos altos de `QA_REPORT.md` siguen sin corregir tras dos sprints de contenido consecutivos (Servicios y Proyectos).**

Razón: se mantiene la misma recomendación de la versión anterior. `index.php` también sigue pendiente (bloqueador técnico de arquitectura). Si el cliente prefiere continuar con más contenido antes de las correcciones, el siguiente candidato natural sería Blog y páginas genéricas (`single.php`, `page.php`, `comments.php`, `404.php`), reutilizando `template-parts/page-hero.php` una tercera vez.

Ningún hallazgo de `QA_REPORT.md` fue corregido en este sprint — todos siguen pendientes de tu aprobación explícita, incluido el crítico.

---

## 8. Documentos de transferencia entre sesiones

A partir de este cierre de etapa, el proyecto cuenta con documentación adicional pensada para retomar el trabajo sin pérdida de contexto, incluso en una conversación completamente nueva:

- **`HANDOFF.md`** — documento maestro de transferencia: arquitectura, estructura, módulos por estado, dependencias, riesgos, bugs conocidos, decisiones, convenciones, orden recomendado y advertencias de compatibilidad. Incluye al final un prompt de continuación listo para copiar/pegar.
- **`CHANGELOG.md`** — historial de versiones del proyecto (v0.1.0, v0.2.0, ...).
- **`DECISIONS.md`** — registro formal de decisiones arquitectónicas con ID, fecha, alternativas descartadas e impacto (complementa, con más detalle y trazabilidad, la sección 5 de este documento).

Estos tres archivos, junto con `PROJECT_STATUS.md`, `TODO.md` y `TREE.md`, son la fuente oficial de estado del proyecto.

---

## 9. Resumen del Sprint de QA

- **Documento generado:** `QA_REPORT.md` — 29 hallazgos verificados línea por línea contra el código real (no especulativos), clasificados: 1 Crítico, 8 Altos, 9 Medios, 5 Bajos, 6 Mejoras futuras.
- **Metodología:** lectura completa de los 31 archivos del tema, verificación de balance de sintaxis, `node --check` sobre JS, y cálculo numérico real de contraste WCAG para los colores institucionales usados en texto.
- **Hallazgo más relevante:** QA-001 (crítico) — bypass de validación de tipo de archivo adjunto en `inc/quote-form.php`.
- **Ningún archivo de código fue modificado** durante este sprint, conforme a instrucción explícita. Todas las correcciones quedan a la espera de aprobación.

---

## 10. Resumen del Sprint 4 — Módulo Proyectos

- **Archivos nuevos:** `archive-proyecto.php`, `single-proyecto.php`, `template-parts/content-proyecto.php`, `template-parts/sidebar-proyectos.php`.
- **Extensiones aditivas** (ninguna línea previa modificada): `inc/helpers.php` (`ce_get_related_services_for_project()`), `inc/seo.php` (`ce_construction_schema_project()`), `assets/css/main.css` (sección 21, ficha de metadatos).
- **Sin cambios en `assets/js/main.js`**: el lightbox, ya implementado en Sprint 1-2, detecta automáticamente el nuevo marcado de galería sin ninguna modificación (ver decisión D-016 en `DECISIONS.md`).
- **Cubre todo lo pedido:** hero interno (reutilizando `page-hero.php` por segunda vez, validando la decisión D-009 del Sprint 3), breadcrumbs (ya soportados desde `inc/seo.php` original), navegación entre proyectos, cliente/fecha/ubicación/estado en una ficha visual dedicada, galería + lightbox, servicios relacionados, CTA, formulario de cotización integrado, Schema.org (`CreativeWork`/`Project` + `BreadcrumbList`), SEO/OG (ya cubiertos por `ce_construction_meta_tags()` genérica), responsive, accesibilidad (ARIA en navegación, paginación, galería) e integración completa con el sistema CSS/JS existente.
- **Decisión notable:** Schema.org no tiene un tipo `Project` estándar; se usó `@type` como array `["CreativeWork", "Project"]` para cumplir el requisito literal sin sacrificar validez SEO (D-014).
