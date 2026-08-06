# CE Construction — PROJECT STATUS

> Este documento es la fuente oficial de verdad del proyecto.
> Se actualiza al finalizar cada módulo. No se reinicia ni se resume: solo se agrega/actualiza estado.

**Última actualización:** Entregables 7.1 (`inc/widgets.php`) y 7.2 (`archive.php` genérico) del Sprint 7 **entregados**. Conforme a la nueva regla permanente de la metodología (ver sección 17 y `DECISIONS.md` D-038), ambos Entregables permanecen en estado **"Entregado — pendiente de aprobación"**, no "Completado", hasta que el usuario los apruebe explícitamente. **El Entregable 7.3 no ha comenzado.**

**Versión de proyecto correspondiente:** v0.7.1 (ver `CHANGELOG.md`) — pendiente de confirmación definitiva hasta la aprobación de 7.1/7.2.

---

## 1. Estado actual del proyecto

El tema tiene:
- Backend 100% funcional (CPTs, Customizer, metaboxes, formulario de cotización con AJAX/Nonce/email/adjuntos/rate-limiting/retención, SEO backend).
- Sistema de diseño frontend completo (CSS) y capa de interactividad (JS) implementados y verificados sintácticamente.
- Capa de plantillas completa: Home, Servicios, Proyectos, Equipo, Clientes, Blog (`single.php`+`comments.php`), páginas genéricas (`page.php`), 404 dedicado, y ahora **`archive.php` genérico** (Entregable 7.2, pendiente de aprobación) como fallback dedicado para categoría/etiqueta/autor/fecha y los CPTs `testimonio`/`ce_faq`.
- **`inc/widgets.php`** (Entregable 7.1, pendiente de aprobación): 2 widgets custom (Contacto, Redes Sociales) que dan uso real al sidebar `footer-1`.

El tema cumple el mínimo exigido por WordPress (`style.css` + `index.php`) y ya cuenta con plantillas dedicadas para prácticamente todos los contextos de la Template Hierarchy salvo `search.php` (delegado a `index.php`, funcional).

---

## 2. Módulos terminados

| # | Módulo | Archivos | Estado |
|---|--------|----------|--------|
| 1 | Bootstrap del tema | `functions.php`, `inc/setup.php` | ✅ |
| 2 | Carga de assets | `inc/enqueue.php` (único archivo válido; `inc/enqueue_1.php` fue un duplicado accidental, eliminado del repositorio) | ✅ |
| 3 | Customizer | `inc/customizer.php` | ✅ |
| 4 | Custom Post Types (6) | `inc/cpt-servicios.php`, `inc/cpt-proyectos.php`, `inc/cpt-testimonios.php`, `inc/cpt-equipo.php`, `inc/cpt-clientes.php`, `inc/cpt-faq.php` | ✅ |
| 5 | Metaboxes / campos personalizados | `inc/meta-boxes.php` | ✅ |
| 6 | Formulario de Cotización (backend) | `inc/quote-form.php` | ✅ |
| 7 | SEO backend | `inc/seo.php` | ✅ |
| 8 | Helpers reutilizables | `inc/helpers.php` | ✅ |
| 9 | Sistema de diseño CSS | `assets/css/main.css` | ✅ |
| 10 | Sistema JS modular | `assets/js/main.js` | ✅ |
| 11 | Header / Footer / Front Page | `header.php`, `footer.php`, `front-page.php`, `template-parts/*` (10 archivos) | ✅ |
| 12 | Sprint 3 — Módulo Servicios | `archive-servicio.php`, `single-servicio.php`, + template-parts + extensiones aditivas | ✅ |
| 13 | Sprint QA — Auditoría de Integración | `QA_REPORT.md` (29 hallazgos) | ✅ Auditoría completa; 9 Críticos/Altos corregidos en Sprint 5 |
| 14 | Sprint 4 — Módulo Proyectos | `archive-proyecto.php`, `single-proyecto.php`, + template-parts + extensiones aditivas | ✅ |
| 15 | Sprint 5, Fase 1 — Correcciones QA (Críticos/Altos) | QA-001 a QA-009 | ✅ |
| 16 | Sprint 5, Fase 2 — Documentación de arquitectura | `ARCHITECTURE.md` | ✅ |
| 17 | Sprint 5, Fase 3 — Módulo Equipo y Clientes | `archive-equipo.php`, `single-equipo.php`, `archive-clientes.php`, `single-clientes.php`, + extensiones aditivas | ✅ |
| 18 | Entregable 6A — `index.php` | `index.php`, `template-parts/content-fallback.php`, `template-parts/no-results.php` | ✅ |
| 19 | Entregable 6B.1 — `page.php` | `page.php` | ✅ |
| 20 | Entregable 6B.2 — `single.php` + `comments.php` | `single.php`, `comments.php`, extensión de `inc/seo.php`, extensión de `assets/css/main.css` | ✅ |
| 21 | Entregable 6B.3 — `404.php` | `404.php` (cierra Sprint 6B) | ✅ |
| 22 | **Entregable 7.1 — `inc/widgets.php`** | `inc/widgets.php` (2 widgets: Contacto, Redes Sociales) | 🟡 **Entregado — pendiente de aprobación del usuario** |
| 23 | **Entregable 7.2 — `archive.php` genérico** | `archive.php` | 🟡 **Entregado — pendiente de aprobación del usuario** |

## 3. Módulos en desarrollo

Ninguno activo. Sprint 7 en curso: Entregables 7.1 y 7.2 **entregados y a la espera de aprobación explícita** (ver sección 17). **El Entregable 7.3 no ha comenzado** y no comenzará hasta que 7.1 y 7.2 sean aprobados, conforme a la nueva regla permanente de la metodología (`DECISIONS.md` D-038).

Los 20 hallazgos Medios/Bajos/Mejoras futuras de `QA_REPORT.md` siguen sin corregir.

## 4. Módulos pendientes

| # | Módulo | Archivos esperados | Prioridad |
|---|--------|---------------------|-----------|
| 24 | Hallazgos QA Medios (Entregable 7.3) | Selección de QA-010 a QA-018, según aprobación del usuario | Media — **bloqueado hasta aprobación de 7.1/7.2** |
| 25 | `screenshot.png` del tema (Entregable 7.4) | — | Baja (cosmético) |
| 26 | Revisión final de accesibilidad (ARIA, foco, contraste) | transversal | Media |
| 27 | Revisión final de performance (Core Web Vitals, tamaño de imágenes) | transversal | Media |

## 5. Decisiones arquitectónicas tomadas

Ver `DECISIONS.md` para el registro completo y acumulativo (D-001 a D-038 a la fecha). Las más recientes:

- **D-036** — Alcance de `inc/widgets.php`: 2 widgets orientados a dar uso real al sidebar `footer-1`, sin CSS/JS nuevo.
- **D-037** — `archive.php` como fallback dedicado para su propio alcance, sin extender los breadcrumbs de Testimonios/FAQ en el mismo Entregable.
- **D-038** — **Nueva regla permanente:** ningún Entregable se considera finalizado hasta que todos sus archivos hayan sido entregados y aprobados explícitamente por el usuario; el siguiente Entregable no inicia sin esa aprobación previa. Ver sección 17 de este documento y `HANDOFF.md` sección 16.

## 6. Riesgos detectados

Ver historial completo en versiones anteriores de este documento (sin cambios respecto al cierre del Sprint 6B, salvo la fila nueva agregada abajo). Fila nueva:

| Riesgo | Severidad | Detalle | Mitigación planeada |
|---|---|---|---|
| Breadcrumbs sin rama dedicada para categoría/etiqueta/autor/fecha y archivo de Testimonios/FAQ | 🟢 Baja | `archive.php` (Entregable 7.2) cubre estos contextos visualmente, pero `ce_construction_breadcrumbs()` no tiene rama específica para ellos — caen en "solo Inicio" sin nivel intermedio. Limitación preexistente, no introducida por este Entregable. | Candidato a un futuro Entregable de refinamiento SEO (extensión aditiva de `inc/seo.php`). Ver `DECISIONS.md` D-037. |
| `inc/enqueue_1.php` (duplicado accidental) | ✅ Resuelto | Archivo duplicado de `inc/enqueue.php`, nunca referenciado en `functions.php`, detectado durante el Entregable 7.1. | Eliminado del repositorio oficial por el usuario. `inc/enqueue.php` es y ha sido siempre el único archivo válido de encolado de assets. Sin acción de código requerida. |

## 7. Próximo módulo recomendado

**Ninguno inicia todavía.** Conforme a la nueva regla permanente (D-038), el Entregable 7.3 (Hallazgos QA Medios) queda bloqueado hasta que el usuario apruebe explícitamente los Entregables 7.1 y 7.2 ya entregados.

Backlog restante del Sprint 7 (sin iniciar):
- 7.3 — Hallazgos QA Medios (QA-010 a QA-018 de `QA_REPORT.md`), con aprobación explícita de cuáles corregir.
- 7.4 — `screenshot.png` (puede depender de definiciones visuales finales del cliente).

---

## 8. Documentos de transferencia entre sesiones

`HANDOFF.md`, `CHANGELOG.md`, `DECISIONS.md`, `PROJECT_STATUS.md` (este documento), `TODO.md`, `TREE.md`, `ARCHITECTURE.md`, `QA_REPORT.md` y `CURRENT_SPRINT.md` son la fuente oficial de estado del proyecto.

---

## 9–16. (Resúmenes de Sprints 1 a 6B)

Sin cambios respecto a la versión anterior de este documento — ver el historial completo ya documentado en secciones 9 a 16 de versiones previas de `PROJECT_STATUS.md` y en `CHANGELOG.md`. No se repiten aquí por no haber cambiado durante el Sprint 7.

---

## 17. Resumen del Sprint 7, Entregables 7.1 y 7.2 (entregados, pendientes de aprobación)

- **Entregable 7.1 — `inc/widgets.php`:** 2 widgets custom (`CE_Construction_Widget_Contact`, `CE_Construction_Widget_Social`), registrados vía `widgets_init`. Reutilizan `ce_get_social_links()` (`inc/helpers.php`) y las clases `.ce-footer__social`/`.ce-footer__contact-item` ya existentes en `assets/css/main.css` — cero CSS nuevo. Orientados a dar uso real al sidebar "Footer - Columna 1" (`footer-1`), renderizable desde QA-006 (v0.4.1) pero vacío hasta este Entregable.
- **Entregable 7.2 — `archive.php` genérico:** fallback dedicado para categoría/etiqueta/autor/fecha (blog) y los CPTs `testimonio`/`ce_faq` (únicos 2 de los 6 CPTs de contenido sin `archive-{cpt}.php` propio). Reutiliza `template-parts/page-hero.php` y `template-parts/content-fallback.php` sin duplicar markup.
- **Verificaciones ejecutadas:** balance de llaves/paréntesis verificado manualmente en ambos archivos PHP (0 de diferencia en ambos casos; PHP no está instalado en el entorno de desarrollo, ver limitación metodológica de `QA_REPORT.md`). JavaScript: sin cambios en `assets/js/main.js`, no aplica `node --check`. Dependencias e includes: `functions.php` ya referenciaba `inc/widgets.php` condicionalmente (`file_exists()`) desde el bootstrap original, sin requerir ningún cambio; `archive.php` no requiere registro explícito (WordPress lo resuelve nativamente vía Template Hierarchy).
- **Ambos Entregables son 100% aditivos:** cero cambios en `inc/helpers.php`, `inc/seo.php`, `assets/css/main.css`, `assets/js/main.js` ni `functions.php`.
- **Evento de mantenimiento resuelto:** `inc/enqueue_1.php` (duplicado accidental de `inc/enqueue.php`, nunca referenciado en `functions.php`) fue confirmado como eliminado del repositorio oficial por el usuario durante este cierre. No requirió ningún cambio de código.
- **Estado formal:** conforme a la nueva regla permanente (D-038), ambos Entregables están **entregados pero no finalizados** — a la espera de la aprobación explícita del usuario antes de iniciar el Entregable 7.3.

---

## 18. Metodología permanente — Nueva regla incorporada tras el cierre de 7.1/7.2

> Ver `DECISIONS.md` D-038 y `HANDOFF.md` sección 16 para el registro formal completo.

**Regla:** ningún Entregable se considera finalizado hasta que se hayan entregado todos los archivos creados o modificados durante ese Entregable y el usuario los haya aprobado explícitamente. No debe iniciarse el siguiente Entregable sin haber realizado previamente la entrega completa del anterior y haber esperado esa aprobación. Regla obligatoria para todos los Sprints futuros, sin excepción.

**Aplicación inmediata:** esta misma actualización de `PROJECT_STATUS.md` es la entrega formal de cierre de los Entregables 7.1 y 7.2 bajo la nueva regla. El Entregable 7.3 permanece bloqueado hasta recibir la aprobación del usuario.
