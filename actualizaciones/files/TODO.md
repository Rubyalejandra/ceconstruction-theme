# CE Construction — TODO.md

> Checklist maestro del proyecto. No se resume ni se reinicia: solo se actualizan los estados (✅ / 🟡 / ⬜) y se agregan tareas nuevas si surgen.
>
> **Nota (Sprint 5 completado — 3 fases, 2 sesiones):** ver secciones 14 (QA, actualizada) y 16-17 (nuevas) al final. Fase 1: los 9 hallazgos Críticos/Altos de QA quedan ✅. Fase 2: ARCHITECTURE.md creado. Fase 3: Equipo y Clientes completados.

---

## 1. Núcleo del tema (Bootstrap)
- ✅ `style.css` (cabecera del tema + design tokens)
- ✅ `functions.php` (carga modular)
- ✅ `inc/setup.php` (theme support, menús, sidebars)
- ✅ `inc/enqueue.php` (CSS/JS, Font Awesome, Google Fonts, localización JS)

## 2. Theme Customizer
- ✅ Logo (soporte `custom-logo`)
- ✅ Colores institucionales (primario, secundario, acento)
- ✅ Tipografía (heading/body)
- ✅ Redes sociales (FB, IG, LinkedIn, YouTube, TikTok)
- ✅ Datos de contacto (teléfono, correo, dirección)
- ✅ WhatsApp
- ✅ Horario de atención
- ✅ Mapa (URL embed)
- ✅ Hero (imagen, título, subtítulo, botones)
- ✅ CTA (título, texto, botón)
- ✅ Footer (about, copyright)

## 3. Custom Post Types
- ✅ Servicios + taxonomía `categoria_servicio`
- ✅ Proyectos + taxonomías `categoria_proyecto`, `estado_proyecto`
- ✅ Testimonios
- ✅ Equipo
- ✅ Clientes
- ✅ Preguntas Frecuentes (FAQ)
- ✅ Cotización (CPT interno administrable, generado por el formulario)

## 4. Metaboxes / Campos personalizados
- ✅ Servicio: icono Font Awesome, enlace externo
- ✅ Proyecto: cliente, ubicación, fecha, galería (`wp.media`)
- ✅ Testimonio: nombre, cargo, rating
- ✅ Equipo: cargo, LinkedIn
- ✅ Cliente: sitio web
- ✅ Guardado seguro (nonce + sanitización + permisos) para los 5 CPTs de contenido

## 5. Formulario de Cotización
- ✅ Backend: validación server-side (`inc/quote-form.php`)
- ✅ Nonce (`ce_quote_form_action`)
- ✅ Honeypot anti-spam
- ✅ Adjuntos (validación de tipo MIME y tamaño máx. 5MB)
- ✅ Envío de correo (`wp_mail`, con adjunto y Reply-To)
- ✅ Registro administrable (CPT `cotizacion` + columnas custom en el admin)
- ✅ Markup del formulario (`template-parts/quote-form.php`)
- ✅ Validación en cliente + envío AJAX (`ModuleQuoteForm` en `main.js`)
- ✅ Modales de éxito/error enganchados al resultado del AJAX
- ⬜ Fallback funcional sin JavaScript (aceptado como fuera de alcance por ahora, ver PROJECT_STATUS.md → Riesgos)

## 6. SEO
- ✅ Meta description dinámica
- ✅ Open Graph
- ✅ Schema.org JSON-LD (GeneralContractor)
- ✅ Breadcrumbs (función lista y **enganchada** en `header.php`)
- ✅ Schema.org `Service` + `BreadcrumbList` en single de Servicio (Sprint 3, `inc/seo.php` → `ce_construction_schema_service()`)
- ✅ Schema.org `CreativeWork`/`Project` + `BreadcrumbList` en single de Proyecto (Sprint 4, `inc/seo.php` → `ce_construction_schema_project()`)
- ⬜ Sitemap compatible (pendiente de definir: XML propio vs. delegar a plugin)
- 🟡 Revisión final de meta tags en todas las plantillas de contenido (Servicios ✅ y Proyectos ✅ cubiertos; Blog aún pendiente, módulo 15)

## 7. Frontend — Sistema de diseño
- ✅ `assets/css/main.css`: reset, variables, tipografía, contenedor/grid, botones, cards, formularios, navbar, hero, footer, componentes flotantes, modales, lightbox, breadcrumbs, utilidades, animaciones, responsive mobile-first
- ✅ `assets/js/main.js`: menú responsive, sticky header, back-to-top, WhatsApp flotante, contadores animados, slider de testimonios, lightbox, validación + envío AJAX del formulario, modales, scroll suave, lazy loading, scroll-reveal
- ✅ Bugfix aplicado: `ModuleModals` ahora enlaza *todos* los botones `.ce-modal__close` de cada modal (antes solo enlazaba el primero por usar `querySelector`)

## 8. Plantillas — Header / Footer / Front Page
- ✅ `header.php` (doctype, barra superior, logo, menú, teléfono, botón cotizar, menú móvil off-canvas, breadcrumbs enganchados)
- ✅ `footer.php` (contacto, redes, mapa, horario, menú, copyright, botones flotantes, modales éxito/error)
- ✅ `inc/helpers.php` (funciones reutilizables: redes sociales, WhatsApp, galería, icono de servicio, excerpt corto, `ce_cpt_has_posts`)
- ✅ `front-page.php` (ensamblado de las 10 secciones)
- ✅ `template-parts/hero.php`
- ✅ `template-parts/about.php`
- ✅ `template-parts/services.php`
- ✅ `template-parts/projects.php`
- ✅ `template-parts/stats.php`
- ✅ `template-parts/why-us.php`
- ✅ `template-parts/testimonials.php`
- ✅ `template-parts/gallery.php`
- ✅ `template-parts/cta.php`
- ✅ `template-parts/quote-form.php`

## 9. Plantillas — Pendientes
- ⬜ `index.php` (🔴 crítico — requerido por WordPress)
- ✅ `archive-servicio.php` (completado en Sprint 3)
- ✅ `single-servicio.php` (completado en Sprint 3)
- ✅ `archive-proyecto.php` (completado en Sprint 4)
- ✅ `single-proyecto.php` (completado en Sprint 4, con galería completa + lightbox + metadatos de cliente/ubicación/fecha/estado)
- ⬜ `single.php` (blog)
- ⬜ `page.php`
- ⬜ `comments.php`
- ⬜ `404.php`
- ⬜ `archive.php` genérico (fallback para Equipo/Clientes/FAQ si no se crean plantillas dedicadas)

## 10. Componentes reutilizables (del brief original)
- ✅ Hero, Cards, Buttons, Forms, Modals, Alerts (implementados en CSS/JS/template-parts)
- ✅ Navbar, Footer, Breadcrumb
- ✅ Accordion (implementado en Sprint 3: CSS `.ce-accordion` + `ModuleAccordion` en `main.js`, usado en FAQ del single de Servicio)
- ✅ Gallery, Timeline (Timeline aún no requerido por ninguna sección — ⬜ pendiente si se define uso, p. ej. historia de la empresa)
- ✅ Counter, Testimonials, CTA
- ⬜ Sidebar (no requerido aún; se evaluará en plantillas de blog, módulo 15)

## 11. Otros pendientes generales
- ⬜ `inc/widgets.php` (widgets custom, baja prioridad)
- ⬜ `screenshot.png` del tema
- ⬜ Revisión de accesibilidad (ARIA, navegación por teclado) en todas las plantillas nuevas
- ⬜ Revisión de performance (auto-hospedar fuentes/Font Awesome, Core Web Vitals)
- ⬜ Sanitización/escaping — auditoría final cruzada de todas las plantillas antes de la entrega definitiva

## 12. Documentación de transferencia entre sesiones (cierre de etapa)
- ✅ `PROJECT_STATUS.md` verificado y actualizado
- ✅ `TODO.md` verificado y actualizado (este archivo)
- ✅ `TREE.md` verificado y actualizado
- ✅ `HANDOFF.md` creado (documento maestro de transferencia + prompt de continuación)
- ✅ `CHANGELOG.md` creado (v0.1.0 y v0.2.0 registradas)
- ✅ `DECISIONS.md` creado (decisiones formalizadas con ID/fecha/alternativas/impacto)

## 13. Sprint 3 — Módulo Servicios (completo)
- ✅ `archive-servicio.php` (hero interno, grid 2 columnas, sidebar, paginación, CTA)
- ✅ `single-servicio.php` (hero interno, contenido, navegación prev/next, relacionados, FAQ, sidebar, CTA, formulario)
- ✅ `template-parts/content-servicio.php` (partial de tarjeta reutilizable)
- ✅ `template-parts/sidebar-servicios.php` (sidebar opcional: listado + tarjeta de contacto)
- ✅ `template-parts/page-hero.php` (hero interno reutilizable, parametrizable, pensado también para Proyectos)
- ✅ Navegación entre servicios (prev/next vía `get_previous_post()`/`get_next_post()`)
- ✅ Breadcrumbs (HTML reutilizado desde `header.php`; JSON-LD `BreadcrumbList` nuevo en `inc/seo.php`)
- ✅ Sidebar opcional
- ✅ CTA al final (reutiliza `template-parts/cta.php` ya existente)
- ✅ Formulario de cotización integrado (reutiliza `template-parts/quote-form.php` ya existente)
- ✅ Proyectos relacionados (heurística por coincidencia de taxonomía, con fallback — ver `DECISIONS.md` D-010)
- ✅ Servicios relacionados (por taxonomía `categoria_servicio`, con fallback)
- ✅ FAQ relacionadas (generales, no filtradas por servicio — ver `DECISIONS.md` D-013)
- ✅ Schema.org `Service` + `BreadcrumbList` (`inc/seo.php`)
- ✅ SEO completo (reutiliza `ce_construction_meta_tags()` ya existente para `is_singular()`)
- ✅ Open Graph (idem, ya cubierto por función existente)
- ✅ Diseño responsive (mobile-first, sidebar pasa a 2 columnas desde `992px`)
- ✅ Accesibilidad (ARIA en accordion, breadcrumbs, paginación, navegación entre servicios; `aria-expanded`/`aria-controls` en FAQ)
- ✅ Animaciones (reutiliza `.ce-animate-on-scroll` + `ModuleScrollReveal` ya existente)
- ✅ Integración con el sistema CSS/JS existente (nueva sección 20 en `main.css`; nuevo `ModuleAccordion` en `main.js`, enganchado en el bootstrap)

## 14. Sprint de QA e Integración — hallazgos (ver `QA_REPORT.md` para el detalle completo)
- ✅ Auditoría completa realizada y documentada (`QA_REPORT.md`, 29 hallazgos)
- ✅ QA-001 (🔴 Crítico) — Bypass de validación de tipo de archivo en `inc/quote-form.php` — **corregido en v0.4.1**
- ✅ QA-002 (🟠 Alto) — Archivos adjuntos huérfanos, sin registro como attachment — **corregido en v0.4.1**
- ✅ QA-003 (🟠 Alto) — Política de retención de datos personales — **corregido en v0.4.1** (cron configurable, plazo por defecto sujeto a confirmación del cliente)
- ✅ QA-004 (🟠 Alto) — Sin rate-limiting en el formulario público — **corregido en v0.4.1**
- ✅ QA-005 (🟠 Alto) — Fallo de contraste WCAG AA en color secundario sobre blanco — **corregido en v0.4.1**
- ✅ QA-006 (🟠 Alto) — Sidebar "Footer Columna 1" registrado pero no renderizado — **corregido en v0.4.1**
- ✅ QA-007 (🟠 Alto) — Falta guardia `wp_is_post_revision()` en `inc/meta-boxes.php` — **corregido en v0.4.1**
- ✅ QA-008 (🟠 Alto) — `CE_THEME_VERSION` no sincronizada, cache-busting roto — **corregido en v0.4.1**
- ✅ QA-009 (🟠 Alto) — CPT Servicio sin `page-attributes`, `menu_order` no funcional — **corregido en v0.4.1**
- ⬜ QA-010 a QA-018 (🟡 Medio, 9 hallazgos) — ver `QA_REPORT.md` — **fuera de alcance del Sprint 5** (solo Críticos/Altos), pendientes de aprobación futura
- ⬜ QA-019 a QA-022 (🟢 Bajo, 4 hallazgos con acción recomendada) — ver `QA_REPORT.md` — pendientes de aprobación futura
- ✅ QA-023 (🟢 Bajo, verificación positiva) — `rel="noopener noreferrer"` confirmado correcto en todos los enlaces externos del proyecto; no requiere acción
- 🔵 QA-024 a QA-029 (Mejoras futuras, 6 hallazgos) — no urgentes, candidatas a sprints de refactor/performance futuros

## 15. Sprint 4 — Módulo Proyectos (completo)
- ✅ `archive-proyecto.php` (hero interno, grid 2 columnas, sidebar, paginación, CTA)
- ✅ `single-proyecto.php` (hero interno, ficha de metadatos, contenido, galería+lightbox, navegación prev/next, servicios relacionados, sidebar, CTA, formulario)
- ✅ `template-parts/content-proyecto.php` (partial de tarjeta reutilizable, usado en el archive)
- ✅ `template-parts/sidebar-proyectos.php` (sidebar opcional: listado de otros proyectos + tarjeta de contacto)
- ✅ Hero interno reutilizando `template-parts/page-hero.php` (sin crear un tercer componente de hero)
- ✅ Breadcrumbs (ya soportados desde la implementación original de `inc/seo.php`, sin cambios necesarios)
- ✅ Navegación entre proyectos (prev/next vía `get_previous_post()`/`get_next_post()`, reutilizando `.ce-service-nav`)
- ✅ Cliente, Fecha, Ubicación, Estado (ficha visual dedicada `.ce-project-meta-grid`, nueva sección 21 en `main.css`)
- ✅ Galería (desde `_ce_proyecto_galeria`, vía `ce_get_gallery_ids()` ya existente)
- ✅ Lightbox (sin cambios en `main.js`: reutiliza el mismo marcado `.ce-gallery-item[data-full]` que `ModuleLightbox` ya escanea globalmente)
- ✅ Servicios relacionados (`ce_get_related_services_for_project()`, heurística inversa a la de Sprint 3, con fallback)
- ✅ CTA al final (reutiliza `template-parts/cta.php`)
- ✅ Formulario de cotización integrado (reutiliza `template-parts/quote-form.php`)
- ✅ Schema.org `CreativeWork`/`Project` + `BreadcrumbList` (`inc/seo.php` → `ce_construction_schema_project()`)
- ✅ Open Graph (ya cubierto por `ce_construction_meta_tags()` genérica para `is_singular()`)
- ✅ SEO completo (idem)
- ✅ Diseño responsive (ficha de metadatos pasa a 4 columnas desde `768px`; layout con sidebar a 2 columnas desde `992px`, ya existente)
- ✅ Accesibilidad (ARIA en navegación entre proyectos, paginación, galería con `alt` desde meta de adjunto)
- ✅ Animaciones (reutiliza `.ce-animate-on-scroll` + `ModuleScrollReveal` ya existente)
- ✅ Integración completa con el sistema CSS/JS existente (nueva sección 21 en `main.css`; **cero cambios en `main.js`**)

## 16. Sprint 5, Fase 2 — Documentación de arquitectura
- ✅ `ARCHITECTURE.md` creado: estructura de carpetas, función de cada directorio/archivo, flujo de carga (functions.php → inc → templates → assets), dependencias entre módulos, flujo del Front Page, flujo de los CPT, flujo del formulario de cotización, flujo de CSS/JS, convenciones de organización

## 17. Sprint 5, Fase 3 — Módulo Equipo y Clientes (completo)
- ✅ `archive-equipo.php` (hero interno, grid de 4 columnas, paginación)
- ✅ `single-equipo.php` (foto circular, cargo, LinkedIn, biografía)
- ✅ `archive-clientes.php` (grid de logos, 2-5 columnas responsive, paginación)
- ✅ `single-clientes.php` (logo grande, enlace al sitio — sin depender de the_content(), el CPT no soporta editor)
- ✅ `template-parts/content-equipo.php` (partial de tarjeta de miembro del equipo)
- ✅ `template-parts/content-cliente.php` (partial de logo de cliente, alineado con `.ce-clients-grid__item` ya existente)
- ✅ Schema.org `Person` (Equipo, con `worksFor`/`jobTitle`/`sameAs`) y `Organization` (Clientes, con `logo`/`url`) en `inc/seo.php`
- ✅ SEO completo (reutiliza `ce_construction_meta_tags()` genérica ya existente)
- ✅ Responsive (grid de equipo 4 columnas; grid de clientes 2→3→5 columnas según viewport)
- ✅ Accesibilidad (`aria-label` en logos/enlaces, `alt` en imágenes, breadcrumbs con roles ya existentes)
- ✅ Integración completa con el resto del tema (header/footer/design system sin cambios)
- ✅ Cambio necesario documentado: `inc/cpt-clientes.php` `has_archive` de `false` a `true` (ver `DECISIONS.md` D-025)
- ⬜ CTA / sidebar / formulario de cotización — **deliberadamente no incluidos** en Equipo/Clientes (fuera del alcance explícito del brief de este sprint, ver `DECISIONS.md` D-026)
