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
├── 404.php                            ✅ Entregable 6B.3 — numeral 404 estilizado, mensaje de error, enlaces rápidos (cierra el Sprint 6B, COMPLETADO)
├── archive.php                        ✅ 🆕 Entregable 7.2 — fallback genérico para categoría/etiqueta/autor/fecha y CPTs sin archive propio (`testimonio`, `ce_faq`); reutiliza `page-hero.php` y `content-fallback.php`. **Entregado, pendiente de aprobación del usuario.**
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
├── CHANGELOG.md                       ✅ Historial de versiones del proyecto (v0.1.0 → v0.7.1)
├── DECISIONS.md                       ✅ Registro formal de decisiones arquitectónicas (ID/fecha/alternativas/impacto)
├── QA_REPORT.md                       ✅ Reporte de auditoría — 29 hallazgos; 9 Críticos/Altos corregidos en v0.4.1 (Sprint 5), 20 Medios/Bajos/Mejoras futuras sin corregir
├── ARCHITECTURE.md                    ✅ Arquitectura real del proyecto (carpetas, flujos de carga/renderizado/CPT/formulario/CSS-JS, convenciones)
├── CURRENT_SPRINT.md                  ✅ Referencia compacta del Sprint en curso (Sprint 7)
│
├── inc/
│   ├── setup.php                      ✅ theme_support, menús, sidebars, excerpt
│   ├── enqueue.php                    ✅ CSS/JS, Font Awesome, Google Fonts, wp_localize_script — **único archivo válido de encolado de assets**
│   ├── customizer.php                 ✅ 7 secciones del Theme Customizer
│   ├── helpers.php                    ✅ Funciones reutilizables (redes, WhatsApp, galería, iconos) + relación Servicio↔Proyecto (Sprint 3) + relación inversa Proyecto→Servicio (Sprint 4)
│   ├── cpt-servicios.php              ✅ CPT Servicios + taxonomía — 🔧 QA-009 (Sprint 5): soporte 'page-attributes' añadido
│   ├── cpt-proyectos.php              ✅ CPT Proyectos + 2 taxonomías
│   ├── cpt-testimonios.php            ✅ CPT Testimonios
│   ├── cpt-equipo.php                 ✅ CPT Equipo
│   ├── cpt-clientes.php               ✅ CPT Clientes — 🔧 Sprint 5: `has_archive` false→true (ver DECISIONS.md D-025)
│   ├── cpt-faq.php                    ✅ CPT Preguntas Frecuentes
│   ├── meta-boxes.php                 ✅ Metaboxes + guardado seguro (5 CPTs de contenido) — 🔧 QA-007 (Sprint 5): guardia wp_is_post_revision() añadida
│   ├── quote-form.php                 ✅ CPT Cotización + handler AJAX + email + adjuntos — 🔧 Sprint 5: QA-001/QA-002/QA-003/QA-004
│   ├── seo.php                        ✅ Meta tags, OG, Schema.org (Organization/Service/Project/Person/Organization-cliente/BlogPosting), breadcrumbs HTML
│   └── widgets.php                    ✅ 🆕 Entregable 7.1 — `CE_Construction_Widget_Contact` y `CE_Construction_Widget_Social`, registrados en `widgets_init`; reutilizan `ce_get_social_links()` y clases `.ce-footer__*` ya existentes, sin CSS nuevo. **Entregado, pendiente de aprobación del usuario.**
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
│   ├── page-hero.php                  ✅ Hero interno reutilizable (parametrizable vía $args) — usado también por `archive.php` desde el Entregable 7.2
│   ├── content-servicio.php           ✅ Partial de tarjeta de servicio
│   ├── sidebar-servicios.php          ✅ Sidebar opcional (listado de servicios + tarjeta de contacto)
│   ├── content-proyecto.php           ✅ Partial de tarjeta de proyecto
│   ├── sidebar-proyectos.php          ✅ Sidebar opcional (listado de proyectos + tarjeta de contacto)
│   ├── content-equipo.php             ✅ Partial de tarjeta de miembro del equipo
│   ├── content-cliente.php            ✅ Partial de logo de cliente
│   ├── content-fallback.php           ✅ Tarjeta genérica para loops de búsqueda/archivo (usada por `index.php` y, desde el Entregable 7.2, por `archive.php`)
│   ├── no-results.php                 ✅ Estado vacío reutilizable con formulario de búsqueda propio (usado por `index.php`, `404.php` y `archive.php`)
│   ├── floating-buttons.php           ⬜ No creado como archivo aparte — implementado directamente dentro de footer.php
│   ├── modals.php                     ⬜ No creado como archivo aparte — implementado directamente dentro de footer.php
│   ├── accordion.php                  ⬜ No creado como archivo aparte — implementado inline dentro de single-servicio.php
│   └── timeline.php                   ⬜ Pendiente (sin sección del brief que lo requiera aún)
│
└── assets/
    ├── css/
    │   └── main.css                   ✅ Sistema de diseño completo (23 secciones, mobile-first) — sin cambios en Entregables 7.1/7.2
    ├── js/
    │   └── main.js                    ✅ 13 módulos ES6 — sin cambios en Entregables 7.1/7.2
    └── img/                           ⬜ Vacía — pendiente de imágenes reales del cliente
```

---

### Nota de arquitectura (desviación menor documentada)

El brief original sugería `floating-buttons.php` y `modals.php` como template-parts independientes. Se implementaron **directamente dentro de `footer.php`** en su lugar (ver `TREE.md` histórico y `ARCHITECTURE.md` sección 10 para el detalle). Pendiente de tu aprobación si quieres que se separen en archivos independientes.

---

### Verificación de cierre de etapa (Sprint 7, Entregables 7.1 y 7.2)

Se agregaron 2 archivos nuevos: `inc/widgets.php` (Entregable 7.1: 2 widgets custom, `CE_Construction_Widget_Contact` y `CE_Construction_Widget_Social`, registrados vía `widgets_init`, reutilizando `ce_get_social_links()` y las clases `.ce-footer__social`/`.ce-footer__contact-item` ya existentes) y `archive.php` (Entregable 7.2: fallback genérico para categoría/etiqueta/autor/fecha y los CPTs `testimonio`/`ce_faq`, reutilizando `template-parts/page-hero.php` y `template-parts/content-fallback.php`). **Cero cambios** en `inc/helpers.php`, `inc/seo.php`, `assets/css/main.css` y `assets/js/main.js` — ambos Entregables fueron 100% aditivos, sin tocar ningún archivo previamente aprobado. Balance de llaves/paréntesis verificado manualmente en ambos archivos nuevos (PHP no disponible en el entorno de desarrollo, ver limitación metodológica documentada en `QA_REPORT.md`).

Se corrigió, de paso, una discrepancia detectada en este mismo árbol: `404.php` seguía marcado como `⬜ Pendiente` pese a estar completamente implementado desde v0.6.3 (Sprint 6B, ya cerrado).

Se confirma además que `inc/enqueue_1.php` (archivo duplicado accidental de `inc/enqueue.php`, detectado durante el Entregable 7.1) fue eliminado del repositorio oficial. `inc/enqueue.php` es y ha sido siempre el único archivo de encolado de assets referenciado por `functions.php`; ningún archivo de este árbol requirió cambios por ese evento.

**Estado de aprobación (nueva regla permanente, ver `DECISIONS.md` D-038 y `HANDOFF.md` sección 16):** ambos Entregables (7.1 y 7.2) están **entregados** pero **no se consideran finalizados** hasta que el usuario apruebe explícitamente los archivos entregados en este cierre. El Entregable 7.3 no comienza hasta recibir esa aprobación.
