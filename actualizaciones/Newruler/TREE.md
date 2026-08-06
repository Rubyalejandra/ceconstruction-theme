# CE Construction — TREE.md

> Árbol completo del proyecto con estado por archivo.
> ✅ Implementado &nbsp;|&nbsp; 🟡 En desarrollo &nbsp;|&nbsp; ⬜ Pendiente
> Se actualiza al finalizar cada módulo. No se resume ni se reinicia.

```
ce-construction-theme/
│
├── style.css                          ✅ Cabecera del tema + design tokens — 🔧 QA-008 (Sprint 5): Version sincronizada a 0.4.1
├── functions.php                      ✅ Bootstrap, carga modular de inc/* — 🔧 QA-008 (Sprint 5): CE_THEME_VERSION sincronizada a 0.4.1
├── header.php                         ✅ Doctype, barra superior, header, menú móvil
├── footer.php                         ✅ Contacto, redes, mapa, footer, flotantes, modales — 🔧 QA-006 (Sprint 5): renderiza footer-1 condicionalmente
├── front-page.php                     ✅ Ensamblado de las 10 secciones del home
├── index.php                          ✅ Entregable 6A — fallback genérico (single/página, archivos genéricos, búsqueda, 404); único bloqueador crítico ya resuelto
├── page.php                           ✅ Entregable 6B.1 — páginas genéricas (contraseña, wp_link_pages, comentarios condicionales)
├── single.php                         ✅ Entregable 6B.2 — entrada de blog (meta, tags, navegación entre entradas, schema BlogPosting)
├── comments.php                       ✅ Entregable 6B.2 — callback propio, hilos anidados, formulario integrado
├── 404.php                            ⬜ Pendiente — Entregable 6B.3 (siguiente y último del Sprint 6B)
├── archive.php                        ⬜ Pendiente (fallback genérico)
├── archive-servicio.php               ✅ Sprint 3 — listado con hero interno, sidebar, paginación, CTA
├── single-servicio.php                ✅ Sprint 3 — single completo (ver detalle en TODO.md sección 13)
├── archive-proyecto.php               ✅ Sprint 4 — listado con hero interno, sidebar, paginación, CTA
├── archive-equipo.php                 ✅ Sprint 5 — listado de equipo con hero interno, grid 4 columnas
├── single-equipo.php                  ✅ Sprint 5 — ficha de miembro (foto, cargo, LinkedIn, biografía)
├── archive-clientes.php               ✅ Sprint 5 — grid de logos de clientes (has_archive habilitado, ver DECISIONS.md D-025)
├── single-clientes.php                ✅ Sprint 5 — ficha de cliente (logo + enlace al sitio)
├── single-proyecto.php                ✅ Sprint 4 — single completo (ver detalle en TODO.md sección 15)
├── screenshot.png                     ⬜ Pendiente (cosmético)
│
├── PROJECT_STATUS.md                  ✅ Documento de control
├── TODO.md                            ✅ Documento de control
├── TREE.md                            ✅ Documento de control (este archivo)
├── HANDOFF.md                         ✅ Documento maestro de transferencia entre sesiones + prompt de continuación
├── CHANGELOG.md                       ✅ Historial de versiones del proyecto (v0.1.0 → v0.4.0)
├── DECISIONS.md                       ✅ Registro formal de decisiones arquitectónicas (ID/fecha/alternativas/impacto)
├── QA_REPORT.md                       ✅ Reporte de auditoría — 29 hallazgos; 9 Críticos/Altos corregidos en v0.4.1 (Sprint 5), 20 Medios/Bajos/Mejoras futuras sin corregir
├── ARCHITECTURE.md                    ✅ 🆕 Sprint 5, Fase 2 — arquitectura real del proyecto (carpetas, flujos de carga/renderizado/CPT/formulario/CSS-JS, convenciones)
├── CURRENT_SPRINT.md                  ✅ 🆕 Referencia compacta y siempre actualizada del Sprint en curso (nuevo documento permanente, política de actualización incremental)
│
├── inc/
│   ├── setup.php                      ✅ theme_support, menús, sidebars, excerpt
│   ├── enqueue.php                    ✅ CSS/JS, Font Awesome, Google Fonts, wp_localize_script
│   ├── customizer.php                 ✅ 7 secciones del Theme Customizer
│   ├── helpers.php                    ✅ Funciones reutilizables (redes, WhatsApp, galería, iconos) + 🆕 relación Servicio↔Proyecto (Sprint 3) + 🆕 relación inversa Proyecto→Servicio (Sprint 4, funciones añadidas al final, nada existente modificado)
│   ├── cpt-servicios.php              ✅ CPT Servicios + taxonomía — 🔧 QA-009 (Sprint 5): soporte 'page-attributes' añadido
│   ├── cpt-proyectos.php              ✅ CPT Proyectos + 2 taxonomías
│   ├── cpt-testimonios.php            ✅ CPT Testimonios
│   ├── cpt-equipo.php                 ✅ CPT Equipo
│   ├── cpt-clientes.php               ✅ CPT Clientes — 🔧 Sprint 5: `has_archive` false→true (necesario para archive-clientes.php, ver DECISIONS.md D-025)
│   ├── cpt-faq.php                    ✅ CPT Preguntas Frecuentes
│   ├── meta-boxes.php                 ✅ Metaboxes + guardado seguro (5 CPTs de contenido) — 🔧 QA-007 (Sprint 5): guardia wp_is_post_revision() añadida
│   ├── quote-form.php                 ✅ CPT Cotización + handler AJAX + email + adjuntos — 🔧 Sprint 5: QA-001 (validación real de extensión), QA-002 (registro como attachment), QA-003 (cron de retención), QA-004 (rate-limiting por IP)
│   ├── seo.php                        ✅ Meta tags, OG, Schema.org (Organization), breadcrumbs HTML + 🆕 Schema `Service`/`BreadcrumbList` (Sprint 3) + 🆕 Schema `CreativeWork`/`Project`/`BreadcrumbList` (Sprint 4) + 🆕 Schema `Person`/`Organization` para Equipo/Clientes (Sprint 5, aditivo) + 🔧 rama de breadcrumbs de Cliente actualizada (acoplado al fix de has_archive, ver DECISIONS.md D-025) + 🆕 Schema `BlogPosting` (Entregable 6B.2, aditivo)
│   └── widgets.php                    ⬜ Pendiente (baja prioridad)
│
├── template-parts/
│   ├── hero.php                       ✅ Hero (imagen, overlay, título, subtítulo, 2 botones)
│   ├── about.php                      ✅ Quiénes Somos (historia, misión, visión, valores)
│   ├── services.php                   ✅ Grid de Servicios (CPT servicio)
│   ├── projects.php                   ✅ Grid de Proyectos (CPT proyecto + metadatos)
│   ├── stats.php                      ✅ Contadores animados
│   ├── why-us.php                     ✅ ¿Por qué elegirnos? (cards de iconos)
│   ├── testimonials.php               ✅ Slider de testimonios (CPT testimonio)
│   ├── gallery.php                    ✅ Galería con lightbox (desde galerías de proyecto)
│   ├── cta.php                        ✅ Sección CTA
│   ├── quote-form.php                 ✅ Markup del formulario de cotización
│   ├── page-hero.php                  ✅ 🆕 Sprint 3 — Hero interno reutilizable (parametrizable vía $args, usado por Servicios y pensado para Proyectos)
│   ├── content-servicio.php           ✅ 🆕 Sprint 3 — Partial de tarjeta de servicio (usado en archive + relacionados)
│   ├── sidebar-servicios.php          ✅ 🆕 Sprint 3 — Sidebar opcional (listado de servicios + tarjeta de contacto)
│   ├── content-proyecto.php           ✅ 🆕 Sprint 4 — Partial de tarjeta de proyecto (usado en el archive)
│   ├── sidebar-proyectos.php          ✅ 🆕 Sprint 4 — Sidebar opcional (listado de proyectos + tarjeta de contacto)
│   ├── content-equipo.php             ✅ 🆕 Sprint 5 — Partial de tarjeta de miembro del equipo (usado en el archive)
│   ├── content-cliente.php            ✅ 🆕 Sprint 5 — Partial de logo de cliente (usado en el archive; alineado a .ce-clients-grid__item)
│   ├── content-fallback.php           ✅ 🆕 Entregable 6A — Tarjeta genérica para loops de búsqueda/archivo (usado por index.php)
│   ├── no-results.php                 ✅ 🆕 Entregable 6A — Estado vacío reutilizable con formulario de búsqueda propio (usado por index.php)
│   ├── floating-buttons.php           ⬜ No creado como archivo aparte — implementado directamente dentro de footer.php (WhatsApp + volver arriba). Ver nota de arquitectura abajo.
│   ├── modals.php                     ⬜ No creado como archivo aparte — implementado directamente dentro de footer.php (modales éxito/error). Ver nota de arquitectura abajo.
│   ├── accordion.php                  ⬜ No creado como archivo aparte — implementado inline dentro de single-servicio.php (sección FAQ) + CSS/JS genéricos en assets/. Reutilizable en futuras plantillas sin duplicar código CSS/JS.
│   └── timeline.php                   ⬜ Pendiente (sin sección del brief que lo requiera aún; candidato para "Quiénes Somos" ampliado)
│
└── assets/
    ├── css/
    │   └── main.css                   ✅ Sistema de diseño completo (19 secciones, mobile-first) + 🆕 Sección 20 (Sprint 3): page-hero interno, sidebar, accordion, navegación entre servicios, paginación + 🆕 Sección 21 (Sprint 4): ficha de metadatos de proyecto + 🆕 Sección 22 (Sprint 5): tarjetas de equipo, grid de logos de clientes + 🔧 QA-005 (Sprint 5, Fase 1): variable `--ce-color-secondary-text` y overrides de contraste + 🔧 Entregable 6A: `.ce-mt-6`/`.ce-mb-6` (bug preexistente, en uso desde Sprint 3 pero nunca definidas) + 🆕 Sección 23 (Entregable 6B.2): árbol de comentarios
    ├── js/
    │   └── main.js                    ✅ 12 módulos ES6 (ver TODO.md sección 7) + 🆕 `ModuleAccordion` (Sprint 3), enganchado al bootstrap existente. **Sin cambios en Sprint 4** (el lightbox ya existente cubre la galería de Proyectos sin modificación).
    └── img/                           ⬜ Vacía — pendiente de imágenes reales del cliente (logo, hero, placeholders)
```

---

### Nota de arquitectura (desviación menor documentada)

El brief original sugería `floating-buttons.php` y `modals.php` como template-parts independientes. Se implementaron **directamente dentro de `footer.php`** en su lugar, porque:
1. Ambos son marcado que debe existir en **todas** las páginas del sitio (no solo el home), y `footer.php` ya se carga globalmente vía `get_footer()`.
2. Evita un `get_template_part()` adicional por carga de página para un bloque de marcado pequeño y estático.

Si se prefiere la separación estricta en archivos independientes (por ejemplo, para facilitar mantenimiento futuro por otro desarrollador), se puede extraer ese bloque de `footer.php` a `template-parts/floating-buttons.php` y `template-parts/modals.php` sin romper nada — es un cambio de bajo riesgo. **Pendiente de tu aprobación si quieres que se haga este ajuste.**

---

### Verificación de cierre de etapa (Sprint 4 — Módulo Proyectos)

Este árbol fue re-verificado contra el sistema de archivos real (`find . -type f`) al cierre del Sprint 4. Se agregaron 4 archivos nuevos (`archive-proyecto.php`, `single-proyecto.php`, `template-parts/content-proyecto.php`, `template-parts/sidebar-proyectos.php`) y se extendieron de forma aditiva (sin modificar código previo) `inc/helpers.php`, `inc/seo.php` y `assets/css/main.css`. **`assets/js/main.js` no requirió ningún cambio** — el lightbox existente detecta el nuevo marcado de galería automáticamente. Balance de llaves/paréntesis verificado en todos los archivos PHP tocados; `node --check` verificado sobre el JS sin cambios. También se corrigió una omisión del Sprint QA anterior: `QA_REPORT.md` no se había agregado a este árbol y ya se incluyó.

### Verificación de cierre de etapa (Sprint 5 — Correcciones QA + ARCHITECTURE.md + Módulo Equipo/Clientes)

Sprint ejecutado en 2 sesiones (la primera interrumpida por límite de mensajes). Al reanudar, se verificó con `grep`/hashes que las 9 correcciones QA y el trabajo parcial de Equipo/Clientes ya estaban aplicados correctamente en disco antes de continuar. Se agregaron: `ARCHITECTURE.md`, 4 plantillas nuevas (`archive-equipo.php`, `single-equipo.php`, `archive-clientes.php`, `single-clientes.php`), 2 template-parts (`content-equipo.php`, `content-cliente.php` — este último reescrito para alinear su nomenclatura CSS con `main.css`, ya que nunca había sido entregado/aprobado). Se corrigieron 9 hallazgos de `QA_REPORT.md` en `inc/quote-form.php`, `inc/meta-boxes.php`, `inc/cpt-servicios.php`, `footer.php`, `functions.php`, `style.css` y `assets/css/main.css`. Se realizó un cambio necesario y documentado en `inc/cpt-clientes.php` (`has_archive`) con su ajuste acoplado en `inc/seo.php`. `assets/js/main.js` no requirió ningún cambio. Balance de llaves/paréntesis y `node --check` verificados en todos los archivos tocados.

### Verificación de cierre de etapa (Entregable 6A — index.php)

Se agregó `index.php` (resuelve el único bloqueador crítico de arquitectura pendiente) + 2 template-parts nuevos (`content-fallback.php`, `no-results.php`). Se detectó y corrigió un bug preexistente en `assets/css/main.css`: las utilidades `.ce-mt-6`/`.ce-mb-6` se usaban desde el Sprint 3 en 10 archivos ya aprobados pero nunca se habían definido — corregido de forma aditiva, sin tocar ninguna regla existente. Sin cambios en `inc/helpers.php`, `inc/seo.php` ni `assets/js/main.js`. Balance de llaves/paréntesis y `node --check` verificados sin errores.

### Verificación de cierre de etapa (Sprint 6B, Entregable 6B.1 — page.php)

Primer Entregable del Sprint 6B ("Blog y páginas genéricas"), dividido conforme a la metodología permanente de Gestión de Sprints y Entregables (ver `HANDOFF.md` sección 16). Se agregó `page.php`, completamente funcional (soporte de contraseña, `wp_link_pages()`, comentarios condicionales), reutilizando exclusivamente componentes ya existentes — cero cambios en `inc/helpers.php`, `inc/seo.php`, `assets/css/main.css` o `assets/js/main.js`. Balance de llaves/paréntesis verificado sin errores; JS verificado sin cambios.

### Verificación de cierre de etapa (Sprint 6B, Entregable 6B.2 — single.php + comments.php)

Segundo Entregable del Sprint 6B. Se agregaron `single.php` y `comments.php` (unidad funcional acoplada), completamente funcionales (callback propio de comentarios, hilos anidados, formulario integrado, navegación entre entradas, schema BlogPosting). Se extendió de forma aditiva `inc/seo.php` (Schema `BlogPosting`) y `assets/css/main.css` (sección 23, exclusiva del árbol de comentarios). Cero cambios en `inc/helpers.php` y `assets/js/main.js`. Balance de llaves/paréntesis verificado sin errores; JS verificado sin cambios. Siguiente Entregable recomendado: 6B.3 (`404.php`), que cierra el Sprint 6B.

### Verificación de cierre (cambio estructural — nueva política de documentación incremental)

Se agregó `CURRENT_SPRINT.md` como nuevo documento permanente (referencia compacta del Sprint en curso). No se modificó ningún archivo de código. Ver `HANDOFF.md` sección 16 y `DECISIONS.md` para el detalle de la política de actualización incremental que rige a partir de ahora.
