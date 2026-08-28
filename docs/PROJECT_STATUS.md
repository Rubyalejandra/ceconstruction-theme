# CE Construction — PROJECT STATUS

> Este documento es la fuente oficial de verdad del proyecto.
> Se actualiza al finalizar cada módulo. No se reinicia ni se resume: solo se agrega/actualiza estado.

**Última actualización (integración de código completa del Entregable 8.4):** Sprint 7 COMPLETADO. Sprint UX-11 CERRADO Y APROBADO EN SU TOTALIDAD (`DECISIONS.md` D-083 a D-094). **Sprint 8 ("Cierre de Hallazgos QA") EN CURSO: Entregables 8.1, 8.2 (QA-030) y 8.3 (QA-031) aprobados explícitamente por el usuario** (el 8.3 con aprobación final tras 4 pruebas funcionales reales en Apache/2.4.68 + PHP 8.2.33, `DECISIONS.md` D-096/D-097). **Entregable 8.4 (QA-032, QA-033, QA-034) con integración de código completa** en sus 4 archivos (`inc/form-guards.php` nuevo; `inc/quote-form.php`, `functions.php`, `template-parts/quote-form.php` modificados — `DECISIONS.md` D-098/D-099), pendiente únicamente de pruebas funcionales reales antes de aprobarse. Entregables 8.5 a 8.7 reorganizados por prioridad/dependencias/riesgo, sin iniciar (`DECISIONS.md` D-043). QA-041 verificado y cerrado (`page.php` no existía al momento de la verificación; fue creado después, en Sprint UX-6).

**Versión de proyecto correspondiente:** v0.8.5 (ver `style.css`; `docs/CHANGELOG.md` no registra entradas individuales de la fase UX-7/UX-10 más allá del resumen consolidado añadido en la sesión de cierre de esa fase).

**Nota (Fase "Optimización UX / Conversión"):** esta fase, ejecutada en paralelo al Sprint 8, quedó **formal y completamente cerrada** tras UX-7, UX-10 y ahora UX-11 (incluidos sus 3 ajustes puntuales, D-091 a D-093). Ver `docs/DECISIONS.md` D-083 a D-094. Quedan como backlog no bloqueante, sin aprobar ni iniciar: UX-5.2 (documentación de "objetivo de plantilla"), Sprint UX-8 ("Video en Proyectos") y Sprint UX-9 (registro documental de Responsive). Ver `docs/CURRENT_UX_SPRINT.md` para el detalle completo. Con la fase UX cerrada, el foco vuelve exclusivamente al Sprint 8, descrito en el resto de este documento.

---

## 1. Estado actual del proyecto

El tema tiene: backend 100% funcional, frontend completo (Home, Servicios, Proyectos, Equipo, Clientes, Blog, páginas genéricas, 404, `archive.php` genérico), `inc/widgets.php` (2 widgets custom), `screenshot.png` como vista previa del tema, y ahora **6 hallazgos QA Medios corregidos o cerrados** (QA-010, QA-011, QA-014, QA-017, QA-018 completos; QA-013 parcial; QA-015 verificado sin necesitar código) de un total de 42 hallazgos documentados en `QA_REPORT.md`.

---

## 2. Módulos terminados

| # | Módulo | Archivos | Estado |
|---|--------|----------|--------|
| 22 | Entregable 7.1 — `inc/widgets.php` | `inc/widgets.php` | ✅ Completado |
| 23 | Entregable 7.2 — `archive.php` genérico | `archive.php` | ✅ Completado |
| 24 | Entregable 7.3 — Corrección QA-018 | `assets/css/main.css` | ✅ Completado |
| 25 | Entregable 7.4 — `screenshot.png` | `screenshot.png` | ✅ Completado |
| 26 | Entregable 8.1 — QA-010, QA-011, QA-013 (parcial), QA-014, QA-015 (verificación), QA-017 | `header.php`, `inc/enqueue.php`, `inc/customizer.php`, `inc/seo.php`, `style.css` | ✅ **Aprobado explícitamente por el usuario** (`DECISIONS.md` D-095) |
| 27 | Entregable 8.2 — QA-030 (`CE_THEME_VERSION`/`style.css`: cache-busting por `filemtime()`) | `functions.php`, `inc/enqueue.php`, `style.css` | ✅ **Aprobado explícitamente por el usuario** (`DECISIONS.md` D-095) |
| 28 | Sprint UX-11 — 6 puntos base + 3 ajustes puntuales (D-091/D-092/D-093) | `template-parts/hero.php`, `template-parts/page-hero.php`, `template-parts/quote-form.php`, `inc/helpers.php`, `inc/customizer.php`, `inc/hero-image-position.php`, `assets/css/main.css`, `assets/js/main.js` | ✅ **Cerrado y aprobado en su totalidad** (`DECISIONS.md` D-083 a D-094) |
| 29 | Entregable 8.3 — QA-031 (adjuntos de cotización protegidos) | `inc/quote-attachments.php` (nuevo), `inc/quote-form.php`, `functions.php` | ✅ **Aprobado explícitamente por el usuario**, con pruebas funcionales reales verificadas (`DECISIONS.md` D-096/D-097) |

## 3. Módulos en desarrollo

Ninguno activo. Con el Entregable 8.3 aprobado de forma definitiva, el siguiente módulo recomendado es el Entregable 8.4, pendiente de que se apruebe su alcance antes de iniciar implementación.

## 4. Módulos pendientes

| # | Módulo | Prioridad |
|---|--------|-----------|
| 29 | Entregable 8.4 — QA-032, QA-033, QA-034 (robustez del formulario de cotización: concurrencia e integridad de datos) — 🟡 integración de código completa, pendiente únicamente de pruebas funcionales reales | Alta |
| 30 | Entregable 8.5 — QA-012, QA-016, QA-035, QA-038 (performance, deuda técnica, accesibilidad puntual, SEO) | Media |
| 31 | Entregable 8.6 — QA-036 (accesibilidad: gestión de foco en overlays) — requiere decisión de diseño | Media |
| 32 | Entregable 8.7 — QA-019, QA-020, QA-021, QA-022, QA-037, QA-039, QA-040 (Bajos) | Baja |
| 33 | Backlog fuera de Sprint 8 — QA-024 a QA-029, QA-042 (Mejoras futuras, no se implementan sin aprobación de incorporarlas a un Sprint) | Baja |
| 34 | Sprint 9 (futuro) — Auditoría de accesibilidad y performance | Media |
| 35 | Reemplazo de `screenshot.png` por fotografías reales del cliente | Baja (cliente-dependiente) |
| 36 | Backlog UX no bloqueante: UX-5.2 (doc. objetivo de plantilla), Sprint UX-8 (video en Proyectos), Sprint UX-9 (registro documental Responsive) | Baja, sujeta a aprobación |

## 5. Decisiones arquitectónicas tomadas

- **D-036 a D-040** — Sprint 7 (ver `DECISIONS.md`).
- **D-041** — Agrupación original del Sprint 8 propuesta antes de consolidar `QA_REPORT.md` con la auditoría integral — **superseded por D-042**.
- **D-042** — Re-evaluación del Sprint 8 contra el estado real del repositorio: nueva agrupación en 5 Entregables, cierre de QA-015 (ya no aplica), ejecución del Entregable 8.1 — **agrupación de 8.2 a 8.5 superseded por D-043**.
- **D-043** — Reorganización completa del Sprint 8 (8.2 a 8.7) por prioridad (seguridad/privacidad/integridad primero), dependencias (QA-033/034 dependen de la decisión de QA-031) y riesgo (QA-030 y QA-031 aislados entre sí y del resto por ser ambos "Alto" con decisiones arquitectónicas independientes). Incluye la verificación de QA-041 (`page.php` no existía en ese momento, `TREE.md` corregido).
- **D-044** — Corrección QA-030 (Entregable 8.2): cache-busting por `filemtime()` + `CE_THEME_VERSION` derivada de `wp_get_theme()`. Implementada en código, aprobada.
- **D-045 a D-081** — Fase "Optimización UX / Conversión" (Sprints UX-1 a UX-10). Ver `docs/CURRENT_UX_SPRINT.md`.
- **D-083 a D-094** — Sprint UX-11 completo ("Hero, Formulario del Hero y Header"), incluidos los 3 ajustes puntuales posteriores. Cerrado y aprobado en su totalidad en esta sesión.
- **D-095** — Resolución del estado de aprobación del Entregable 8.1 (confirmado aprobado) y aprobación explícita del Entregable 8.2. El Sprint 8 se retoma en el Entregable 8.3.
- **D-045 a D-081** — Fase "Optimización UX / Conversión" completa (Sprints UX-1 a UX-10), en paralelo al Sprint 8 pausado. Ver `docs/CURRENT_UX_SPRINT.md` para el detalle Entregable por Entregable. **Fase cerrada en esta sesión** (UX-7.10, D-079/D-080/D-081, aprobado explícitamente).

## 6. Riesgos detectados

| Riesgo | Severidad | Detalle |
|---|---|---|
| QA-030: cache-busting de CSS/JS | 🟢 Resuelto | Corregido y **aprobado** en el Entregable 8.2 (`DECISIONS.md` D-044/D-095). |
| QA-031: adjuntos de cotización potencialmente accesibles por URL directa | 🟢 Resuelto | Corregido y **aprobado de forma definitiva** en el Entregable 8.3, con pruebas funcionales reales verificadas en Apache/2.4.68 + PHP 8.2.33 (`DECISIONS.md` D-096/D-097). Limitación conocida sobre Nginx documentada para instalaciones futuras; sin acción pendiente en esta instalación. |
| QA-032/033/034: robustez del formulario de cotización (concurrencia, archivo huérfano, idempotencia) | 🟡 Media | Entregable 8.4, **integración de código completa** (`docs/DECISIONS.md` D-098/D-099); pendiente únicamente de pruebas funcionales reales antes de aprobar. |
| QA-012, QA-016, QA-035, QA-038: hallazgos Medios aislados sin corregir | 🟢 Baja | Propuestos para el Entregable 8.5. |
| QA-036: sin gestión de foco en overlays (menú móvil, modales) | 🟡 Media | Entregable 8.6, requiere decisión de diseño (R-4: utilidad centralizada vs. por componente). |
| QA-013: unificación real de `:root` entre `style.css`/`main.css` pendiente | 🟢 Baja | Solo se corrigió el comentario inexacto en 8.1; la unificación es una decisión arquitectónica en backlog. |
| `page.php` no existe (QA-041, verificado) | 🟢 Baja | Sin impacto funcional — `index.php` cubre el fallback. `TREE.md` corregido. Crear la plantilla dedicada es mejora futura sujeta a aprobación. |
| `screenshot.png` es un mockup ilustrativo, no fotografías reales | 🟢 Baja | Reversible sin cambio de código — ver `DECISIONS.md` D-040. |

## 7. Próximo módulo recomendado

Con la fase UX cerrada en su totalidad (Sprints UX-1 a UX-11) y con los Entregables 8.1, 8.2 y 8.3 del Sprint 8 ya **aprobados** (`DECISIONS.md` D-095, D-096, D-097), el **Entregable 8.4** (QA-032, QA-033, QA-034) tiene su **integración de código completa** (`DECISIONS.md` D-098, D-099) y solo espera las pruebas funcionales reales del usuario para poder aprobarse.
