# CE Construction — TREE.md

> Árbol completo del proyecto con estado por archivo.
> ✅ Implementado &nbsp;|&nbsp; 🟡 En desarrollo &nbsp;|&nbsp; ⬜ Pendiente
> Se actualiza al finalizar cada módulo. No se resume ni se reinicia.

```
ce-construction-theme/
│
├── style.css                          ✅ Cabecera del tema + design tokens
├── functions.php                      ✅ Bootstrap, carga modular de inc/*
├── header.php                         ✅ Doctype, barra superior, header, menú móvil
├── footer.php                         ✅ Contacto, redes, mapa, footer, flotantes, modales
├── front-page.php                     ✅ Ensamblado de las 10 secciones del home
├── index.php                          ⬜ PENDIENTE — crítico, requerido por WordPress
├── page.php                           ⬜ Pendiente
├── single.php                         ⬜ Pendiente
├── comments.php                       ⬜ Pendiente
├── 404.php                            ⬜ Pendiente
├── archive.php                        ⬜ Pendiente (fallback genérico)
├── archive-servicio.php               ✅ Sprint 3 — listado con hero interno, sidebar, paginación, CTA
├── single-servicio.php                ✅ Sprint 3 — single completo (ver detalle en TODO.md sección 13)
├── archive-proyecto.php               ✅ Sprint 4 — listado con hero interno, sidebar, paginación, CTA
├── single-proyecto.php                ✅ Sprint 4 — single completo (ver detalle en TODO.md sección 15)
├── screenshot.png                     ⬜ Pendiente (cosmético)
│
├── PROJECT_STATUS.md                  ✅ Documento de control
├── TODO.md                            ✅ Documento de control
├── TREE.md                            ✅ Documento de control (este archivo)
├── HANDOFF.md                         ✅ Documento maestro de transferencia entre sesiones + prompt de continuación
├── CHANGELOG.md                       ✅ Historial de versiones del proyecto (v0.1.0 → v0.4.0)
├── DECISIONS.md                       ✅ Registro formal de decisiones arquitectónicas (ID/fecha/alternativas/impacto)
├── QA_REPORT.md                       ✅ Reporte de auditoría (Sprint QA) — 29 hallazgos clasificados, ninguno corregido aún
│
├── inc/
│   ├── setup.php                      ✅ theme_support, menús, sidebars, excerpt
│   ├── enqueue.php                    ✅ CSS/JS, Font Awesome, Google Fonts, wp_localize_script
│   ├── customizer.php                 ✅ 7 secciones del Theme Customizer
│   ├── helpers.php                    ✅ Funciones reutilizables (redes, WhatsApp, galería, iconos) + 🆕 relación Servicio↔Proyecto (Sprint 3) + 🆕 relación inversa Proyecto→Servicio (Sprint 4, funciones añadidas al final, nada existente modificado)
│   ├── cpt-servicios.php              ✅ CPT Servicios + taxonomía
│   ├── cpt-proyectos.php              ✅ CPT Proyectos + 2 taxonomías
│   ├── cpt-testimonios.php            ✅ CPT Testimonios
│   ├── cpt-equipo.php                 ✅ CPT Equipo
│   ├── cpt-clientes.php               ✅ CPT Clientes
│   ├── cpt-faq.php                    ✅ CPT Preguntas Frecuentes
│   ├── meta-boxes.php                 ✅ Metaboxes + guardado seguro (5 CPTs de contenido)
│   ├── quote-form.php                 ✅ CPT Cotización + handler AJAX + email + adjuntos
│   ├── seo.php                        ✅ Meta tags, OG, Schema.org (Organization), breadcrumbs HTML + 🆕 Schema `Service`/`BreadcrumbList` (Sprint 3) + 🆕 Schema `CreativeWork`/`Project`/`BreadcrumbList` (Sprint 4, funciones añadidas al final del archivo, nada existente modificado)
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
│   ├── floating-buttons.php           ⬜ No creado como archivo aparte — implementado directamente dentro de footer.php (WhatsApp + volver arriba). Ver nota de arquitectura abajo.
│   ├── modals.php                     ⬜ No creado como archivo aparte — implementado directamente dentro de footer.php (modales éxito/error). Ver nota de arquitectura abajo.
│   ├── accordion.php                  ⬜ No creado como archivo aparte — implementado inline dentro de single-servicio.php (sección FAQ) + CSS/JS genéricos en assets/. Reutilizable en futuras plantillas sin duplicar código CSS/JS.
│   └── timeline.php                   ⬜ Pendiente (sin sección del brief que lo requiera aún; candidato para "Quiénes Somos" ampliado)
│
└── assets/
    ├── css/
    │   └── main.css                   ✅ Sistema de diseño completo (19 secciones, mobile-first) + 🆕 Sección 20 (Sprint 3): page-hero interno, sidebar, accordion, navegación entre servicios, paginación + 🆕 Sección 21 (Sprint 4): ficha de metadatos de proyecto (cliente/ubicación/fecha/estado)
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
