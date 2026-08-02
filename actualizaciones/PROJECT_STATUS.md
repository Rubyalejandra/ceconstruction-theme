# CE Construction — PROJECT STATUS

> Este documento es la fuente oficial de verdad del proyecto.
> Se actualiza al finalizar cada módulo. No se reinicia ni se resume: solo se agrega/actualiza estado.

**Última actualización:** Sprint 5 completado (en dos sesiones, la primera interrumpida por límite de mensajes y verificada/continuada en la segunda) — Fase 1: corregidos los 9 hallazgos Críticos/Altos de `QA_REPORT.md`. Fase 2: creado `ARCHITECTURE.md`. Fase 3: Módulo de Equipo y Clientes (archive + single + schema.org para ambos CPTs).

**Versión de proyecto correspondiente:** v0.5.0 (ver `CHANGELOG.md`)

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
| 13 | **Sprint QA — Auditoría de Integración (sin corrección de código)** | `QA_REPORT.md` (29 hallazgos: 1 crítico, 8 altos, 9 medios, 5 bajos, 6 mejoras futuras) | ✅ Auditoría completa; correcciones Críticas/Altas aplicadas en Sprint 5 (ver módulo 15) |
| 14 | **Sprint 4 — Módulo Proyectos (frontend completo)** | `archive-proyecto.php`, `single-proyecto.php`, `template-parts/content-proyecto.php`, `template-parts/sidebar-proyectos.php`, extensión de `inc/helpers.php`, extensión de `inc/seo.php`, extensión de `assets/css/main.css` (sin cambios en `assets/js/main.js`: el lightbox ya existente detecta el nuevo marcado automáticamente) | ✅ |
| 15 | **Sprint 5, Fase 1 — Correcciones de QA (Críticos/Altos)** | QA-001 a QA-009 corregidos en `inc/quote-form.php`, `inc/meta-boxes.php`, `inc/cpt-servicios.php`, `footer.php`, `functions.php`, `style.css`, `assets/css/main.css` | ✅ |
| 16 | **Sprint 5, Fase 2 — Documentación de arquitectura** | `ARCHITECTURE.md` (estructura de carpetas, flujos de carga/renderizado/CPT/formulario/CSS-JS, convenciones) | ✅ |
| 17 | **Sprint 5, Fase 3 — Módulo Equipo y Clientes** | `archive-equipo.php`, `single-equipo.php`, `archive-clientes.php`, `single-clientes.php`, `template-parts/content-equipo.php`, `template-parts/content-cliente.php`, extensión de `inc/seo.php` (Schema `Person`/`Organization`), extensión de `assets/css/main.css`, cambio necesario en `inc/cpt-clientes.php` (`has_archive`) | ✅ |

## 3. Módulos en desarrollo

Ninguno activo. Sprint 5 completado en sus 3 fases. **Los 20 hallazgos Medios/Bajos/Mejoras futuras de `QA_REPORT.md` siguen sin corregir** (fuera de alcance explícito del Sprint 5, que se limitó a Críticos/Altos). Ver sección 9 de este documento.

## 4. Módulos pendientes

| # | Módulo | Archivos esperados | Prioridad |
|---|--------|---------------------|-----------|
| 12 | Archivo raíz obligatorio | `index.php` | 🔴 Crítica (bloquea activación segura del tema) |
| 13 | ~~Servicios (frontend)~~ | ~~`archive-servicio.php`, `single-servicio.php`~~ | ✅ Completado en Sprint 3 (ver módulo 12 en sección 2) |
| 14 | ~~Proyectos (frontend)~~ | ~~`archive-proyecto.php`, `single-proyecto.php` (con galería + lightbox)~~ | ✅ Completado en Sprint 4 (ver módulo 14 en sección 2) |
| 15 | Blog y páginas genéricas | `single.php`, `page.php`, `comments.php`, `404.php` | Media |
| 16 | ~~Archivos de equipo/clientes~~ | ~~`archive-equipo.php`, `single-equipo.php`, `archive-clientes.php`, `single-clientes.php`~~ | ✅ Completado en Sprint 5 (ver módulo 17 en sección 2) |
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
16. **Sprint 5, Fase 1 — Correcciones QA acotadas a Críticos/Altos**: se corrigieron únicamente los 9 hallazgos QA-001 a QA-009 de `QA_REPORT.md`, sin tocar ningún hallazgo Medio/Bajo/Mejora futura, conforme a instrucción explícita. Cada corrección documentada individualmente en `DECISIONS.md` (D-017 a D-024).
17. **Sprint 5, Fase 3 — `has_archive` habilitado para el CPT Cliente**: cambio necesario (no discrecional) para que `archive-clientes.php`, pedido explícitamente en este sprint, fuera alcanzable vía URL amigable. Ver `DECISIONS.md` D-025 para el detalle completo y el cambio acoplado en `inc/seo.php`.
18. **Sprint 5, Fase 3 — Alcance más ligero para Equipo/Clientes**: a diferencia de Servicios/Proyectos, estas plantillas no incluyen CTA, formulario de cotización ni sidebar, porque el brief del Sprint 5 no los solicitó. Ver `DECISIONS.md` D-026.

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
| ~~Bypass de validación de tipo de archivo (QA-001)~~ | ✅ Resuelto en v0.4.1 | El formulario de cotización validaba el tipo de archivo adjunto usando el MIME falsificable enviado por el cliente y una lista de extensiones demasiado amplia (global de WordPress), no el whitelist real (PDF/JPG/PNG/WEBP). Ver `QA_REPORT.md` QA-001 para el detalle técnico completo. | Corregido — ver `DECISIONS.md` D-017. |
| ~~Archivos adjuntos huérfanos sin límite de disco (QA-002)~~ | ✅ Resuelto en v0.4.1 | Los adjuntos del formulario ahora se registran como attachment real de WordPress, vinculados a la cotización. | Corregido — ver `DECISIONS.md` D-017. |
| ~~Sin rate-limiting en el formulario público (QA-004)~~ | ✅ Resuelto en v0.4.1 | El endpoint AJAX ahora limita a 3 envíos/10 min por IP, además del honeypot. | Corregido — ver `DECISIONS.md` D-019. |
| ~~Fallo de contraste WCAG AA verificado (QA-005)~~ | ✅ Resuelto en v0.4.1 | Nueva variable `--ce-color-secondary-text` (5.17:1 sobre blanco) aplicada donde correspondía. | Corregido — ver `DECISIONS.md` D-020. |
| ~~Sidebar de footer "Columna 1" registrado pero nunca renderizado (QA-006)~~ | ✅ Resuelto en v0.4.1 | Ahora se renderiza condicionalmente (`is_active_sidebar`) dentro de la columna "Enlaces" del footer. | Corregido — ver `DECISIONS.md` D-021. |
| ~~Retención de datos personales sin política (QA-003)~~ | ✅ Resuelto en v0.4.1 | Cron diario de purga configurable (365 días por defecto). | Corregido — ver `DECISIONS.md` D-018; el plazo exacto sigue siendo una decisión de negocio a confirmar con el cliente. |
| ~~`save_post` sin guardia de revisión (QA-007)~~ | ✅ Resuelto en v0.4.1 | Añadida guardia `wp_is_post_revision()`. | Corregido — ver `DECISIONS.md` D-022. |
| ~~`CE_THEME_VERSION` sin sincronizar (QA-008)~~ | ✅ Resuelto en v0.4.1 | Sincronizada a `0.4.1` en `functions.php` y `style.css`. | Corregido — ver `DECISIONS.md` D-023. |
| ~~CPT Servicio sin `page-attributes` (QA-009)~~ | ✅ Resuelto en v0.4.1 | Añadido soporte `page-attributes`, habilitando el campo "Orden". | Corregido — ver `DECISIONS.md` D-024. |
| Relación inversa Proyecto↔Servicio heurística (Sprint 4) | 🟡 Media | Ver fila equivalente arriba (Sprint 3), mismo mecanismo en dirección inversa. | Sin cambios — sigue siendo la misma limitación aceptada. |
| `has_archive` habilitado para Cliente (Sprint 5) | 🟢 Baja (cambio ya aplicado, no un riesgo abierto) | Se documenta aquí por trazabilidad: `inc/cpt-clientes.php` cambió `has_archive` de `false` a `true` para que `archive-clientes.php` fuera alcanzable. | Ninguna acción pendiente — ver `DECISIONS.md` D-025. |
| Relación inversa Proyecto↔Servicio heurística (Sprint 4) | 🟡 Media | "Servicios relacionados" en `single-proyecto.php` depende de la misma coincidencia textual de nombres de taxonomía que el riesgo ya documentado para Servicios (ver fila anterior de Sprint 3); mismo mecanismo, ahora en dirección inversa. | Misma mitigación propuesta: campo relacional explícito en un sprint futuro si se requiere precisión editorial. |

## 7. Próximo módulo recomendado

**Sprint 6 (recomendado): `index.php` (crítico de arquitectura, aún pendiente) + Blog y páginas genéricas (`single.php`, `page.php`, `comments.php`, `404.php`).**

Razón: con Servicios, Proyectos, Equipo y Clientes completos, los tipos de contenido personalizado del tema están terminados. `index.php` sigue siendo el único bloqueador técnico crítico de arquitectura sin resolver (WordPress lo exige como mínimo junto a `style.css`). Las plantillas de Blog/páginas genéricas son el siguiente contenido de mayor visibilidad pendiente.

Los 20 hallazgos Medios/Bajos/Mejoras futuras de `QA_REPORT.md` siguen sin corregir — quedan disponibles para un sprint de refinamiento futuro si el cliente lo autoriza.

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

---

## 11. Resumen del Sprint 5 (3 fases, ejecutado en 2 sesiones)

**Nota de continuidad:** la primera sesión de este sprint se interrumpió por límite de mensajes tras completar el código de las Fases 1 y 2, y parte de la Fase 3, sin haber actualizado aún la documentación. La segunda sesión verificó exhaustivamente (vía grep/hashes) que las 9 correcciones de QA y el CSS/schema de Equipo/Clientes ya estaban correctamente aplicados en disco antes de continuar, evitando duplicar trabajo. Se completaron los 4 archivos de plantilla faltantes (archive-equipo.php, single-equipo.php, archive-clientes.php, single-clientes.php) y se corrigió una inconsistencia de nomenclatura CSS detectada en template-parts/content-cliente.php (nunca entregado/aprobado, ver DECISIONS.md D-027).

- **Fase 1 (Correcciones QA):** los 9 hallazgos Críticos/Altos de QA_REPORT.md corregidos. Ver módulo 15 en sección 2 y DECISIONS.md D-017 a D-024.
- **Fase 2 (Documentación de arquitectura):** ARCHITECTURE.md creado, describiendo la arquitectura real (no propuesta) del proyecto: estructura de carpetas, responsabilidad de cada directorio/archivo, flujo de carga del tema, dependencias entre módulos, flujo de renderizado del home, flujo de los CPT, flujo del formulario de cotización, flujo de CSS/JS, y convenciones de organización.
- **Fase 3 (Módulo Equipo y Clientes):** archive-equipo.php, single-equipo.php, archive-clientes.php, single-clientes.php + 2 template-parts nuevos + Schema.org Person/Organization + cambio necesario de has_archive en el CPT Cliente (documentado en D-025). Alcance deliberadamente más ligero que Servicios/Proyectos (sin CTA/sidebar/formulario), conforme al brief explícito del sprint (D-026).