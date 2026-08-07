# CE Construction — TODO.md

> Checklist maestro del proyecto. No se resume ni se reinicia: solo se actualizan los estados (✅ / 🟡 / ⬜) y se agregan tareas nuevas si surgen.
>
> **Nota (Sprint 7 en curso):** Entregables 7.1 (`inc/widgets.php`) y 7.2 (`archive.php`) **Completados** (aprobación interpretada de la instrucción de continuar hacia 7.3). Entregable 7.3 (corrección QA-018) **entregado**, pendiente de aprobación explícita del usuario conforme a la regla permanente D-038. El Entregable 7.4 no inicia hasta recibir esa aprobación.

---

## 1. Núcleo del tema (Bootstrap)
- ✅ `style.css` (cabecera del tema + design tokens)
- ✅ `functions.php` (carga modular)
- ✅ `inc/setup.php` (theme support, menús, sidebars)
- ✅ `inc/enqueue.php` (CSS/JS, Font Awesome, Google Fonts, localización JS) — único archivo válido de encolado de assets

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
- ✅ Adjuntos (validación real por extensión + tamaño máx. 5MB)
- ✅ Envío de correo (`wp_mail`, con adjunto y Reply-To)
- ✅ Registro administrable (CPT `cotizacion` + columnas custom en el admin)
- ✅ Markup del formulario (`template-parts/quote-form.php`)
- ✅ Validación en cliente + envío AJAX (`ModuleQuoteForm` en `main.js`)
- ✅ Modales de éxito/error enganchados al resultado del AJAX
- ⬜ Fallback funcional sin JavaScript (aceptado como fuera de alcance por ahora)

## 6. SEO
- ✅ Meta description dinámica
- ✅ Open Graph
- ✅ Schema.org JSON-LD (GeneralContractor, Service, Project, Person, Organization-cliente, BlogPosting)
- ✅ Breadcrumbs (función lista y enganchada en `header.php`)
- ⬜ Sitemap compatible (pendiente de definir: XML propio vs. delegar a plugin)
- 🟡 Breadcrumbs sin rama dedicada para categoría/etiqueta/autor/fecha y archivo de Testimonios/FAQ (ver `DECISIONS.md` D-037, observación no bloqueante del Entregable 7.2)

## 6bis. Sprint 7 — Hallazgos QA (Entregable 7.3)
- ✅ QA-018 (🟡 Medio) — Barra superior del header sin adaptación responsive — **corregido en v0.7.2** (entregado, pendiente de aprobación)
- ⬜ QA-010 a QA-017 (🟡 Medio, 8 hallazgos restantes) — fuera del alcance aprobado para este Entregable; pendientes de una futura aprobación explícita

## 7. Frontend — Sistema de diseño
- ✅ `assets/css/main.css`: 23 secciones — sin cambios en Sprint 7 (Entregables 7.1/7.2)
- ✅ `assets/js/main.js`: 13 módulos ES6 — sin cambios en Sprint 7 (Entregables 7.1/7.2)

## 8. Plantillas — Header / Footer / Front Page
- ✅ `header.php`, `footer.php`, `inc/helpers.php`, `front-page.php` + 10 template-parts del home

## 9. Plantillas — Pendientes
- ✅ `index.php` (Entregable 6A)
- ✅ `archive-servicio.php`, `single-servicio.php` (Sprint 3)
- ✅ `archive-proyecto.php`, `single-proyecto.php` (Sprint 4)
- ✅ `single.php`, `comments.php` (Entregable 6B.2)
- ✅ `page.php` (Entregable 6B.1)
- ✅ `404.php` (Entregable 6B.3, cierra el Sprint 6B)
- ✅ `archive.php` genérico — **Entregable 7.2, entregado, pendiente de aprobación** (fallback para categoría/etiqueta/autor/fecha y CPTs sin archive propio: `testimonio`, `ce_faq`)

## 10. Componentes reutilizables (del brief original)
- ✅ Hero, Cards, Buttons, Forms, Modals, Alerts, Navbar, Footer, Breadcrumb, Accordion, Gallery, Counter, Testimonials, CTA
- ⬜ Timeline (sin sección que lo use aún)
- ⬜ Sidebar de blog (no requerido aún)

## 11. Otros pendientes generales
- ✅ `inc/widgets.php` — **Entregable 7.1, entregado, pendiente de aprobación** (2 widgets custom: Contacto y Redes Sociales, orientados a dar uso real al sidebar `footer-1`)
- ⬜ `screenshot.png` del tema
- ⬜ Revisión de accesibilidad (ARIA, navegación por teclado) en todas las plantillas nuevas
- ⬜ Revisión de performance (auto-hospedar fuentes/Font Awesome, Core Web Vitals)
- ⬜ Sanitización/escaping — auditoría final cruzada de todas las plantillas antes de la entrega definitiva

## 12–21. (Sprints 1 a 6B — sin cambios respecto a la versión anterior de este documento)

Ver el historial completo de estas secciones en versiones anteriores de este archivo / `CHANGELOG.md` — no se repiten aquí por no haber cambiado durante el Sprint 7.

## 22. Backlog — Sprint 7 (en curso)
- ✅ 7.1 `inc/widgets.php` (widgets custom) — **Completado**
- ✅ 7.2 `archive.php` genérico (fallback para archivos sin plantilla dedicada) — **Completado**
- ✅ 7.3 Hallazgos QA Medios — **entregado (solo QA-018, aprobado explícitamente por el cliente), pendiente de aprobación final del usuario** conforme a `DECISIONS.md` D-038
- ⬜ 7.4 `screenshot.png` — puede depender de definiciones visuales finales del cliente; **no inicia hasta que 7.3 sea aprobado**
