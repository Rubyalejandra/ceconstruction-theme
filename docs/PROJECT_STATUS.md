# CE Construction — PROJECT STATUS

> Este documento es la fuente oficial de verdad del proyecto.
> Se actualiza al finalizar cada módulo. No se reinicia ni se resume: solo se agrega/actualiza estado.

**Última actualización (sesión de cierre de UX-7):** Sprint 7 COMPLETADO. **Sprint 8 ("Cierre de Hallazgos QA") EN CURSO, PAUSADO: Entregable 8.1 desarrollado y entregado (estado de aprobación inconsistente entre documentos, ver `docs/CURRENT_SPRINT.md`); Entregable 8.2 (QA-030) desarrollado, implementado en código y entregado, pendiente de tu aprobación explícita — el Sprint 8 está pausado exactamente en este punto.** Entregables 8.3 a 8.7 reorganizados por prioridad/dependencias/riesgo, sin iniciar (`DECISIONS.md` D-043). QA-041 verificado y cerrado (`page.php` no existía al momento de la verificación; fue creado después, en Sprint UX-6). **Sprint UX-11 ("Hero, Formulario del Hero y Header") completado y aprobado** (6 puntos, `DECISIONS.md` D-083 a D-090) — QA-043 quedó resuelto ahí (D-087/D-090), no en el Sprint 8.

**Versión de proyecto correspondiente:** v0.8.5 (ver `style.css`; `docs/CHANGELOG.md` no registra entradas individuales de la fase UX-7/UX-10 más allá del resumen consolidado añadido en esta sesión de cierre).

**Nota (Fase "Optimización UX / Conversión"):** esta fase, ejecutada en paralelo al Sprint 8 (que permanece pausado, sin cerrarse, exactamente en el punto descrito arriba), quedó formalmente cerrada tras UX-7/UX-10, y desde entonces se sumó un nuevo Sprint: **Sprint UX-11 ("Hero, Formulario del Hero y Header"), completado y aprobado en un único Entregable** (6 puntos: panel del formulario del Hero, altura del Hero, Hero interno con imagen propia, posicionamiento de imágenes, overlay configurable, espaciado del Header — resuelve también QA-043). Ver `docs/DECISIONS.md` D-083 a D-090. Quedan como backlog no bloqueante, sin aprobar ni iniciar: UX-5.2 (documentación de "objetivo de plantilla"), Sprint UX-8 ("Video en Proyectos") y Sprint UX-9 (registro documental de Responsive). Ver `docs/CURRENT_UX_SPRINT.md` para el detalle completo. Ningún dato de esta nota reemplaza ni reinterpreta el estado del Sprint 8 descrito en el resto de este documento.

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
| 26 | Entregable 8.1 — QA-010, QA-011, QA-013 (parcial), QA-014, QA-015 (verificación), QA-017 | `header.php`, `inc/enqueue.php`, `inc/customizer.php`, `inc/seo.php`, `style.css` | 🟡 **Desarrollado y entregado — estado de aprobación inconsistente entre documentos, ver `docs/CURRENT_SPRINT.md`** |
| 27 | Entregable 8.2 — QA-030 (`CE_THEME_VERSION`/`style.css`: cache-busting por `filemtime()`) | `functions.php`, `inc/enqueue.php`, `style.css` | 🟡 **Desarrollado, implementado en código y entregado — pendiente de tu aprobación explícita. El Sprint 8 está pausado en este punto.** |

## 3. Módulos en desarrollo

Ninguno activo. Los Entregables 8.1 y 8.2 están desarrollados y a la espera de tu aprobación explícita (conforme a `DECISIONS.md` D-038) antes de continuar con 8.3.

## 4. Módulos pendientes

| # | Módulo | Prioridad |
|---|--------|-----------|
| 28 | Entregable 8.3 — QA-031 (Alto: adjuntos de cotización potencialmente accesibles por URL) — requiere decisión arquitectónica previa | **Alta** |
| 29 | Entregable 8.4 — QA-032, QA-033, QA-034 (robustez del formulario de cotización: concurrencia e integridad de datos) — depende de la decisión de 8.3 | Media |
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
- **D-044** — Corrección QA-030 (Entregable 8.2): cache-busting por `filemtime()` + `CE_THEME_VERSION` derivada de `wp_get_theme()`. Implementada en código, entregada, pendiente de aprobación.
- **D-045 a D-081** — Fase "Optimización UX / Conversión" completa (Sprints UX-1 a UX-10), en paralelo al Sprint 8 pausado. Ver `docs/CURRENT_UX_SPRINT.md` para el detalle Entregable por Entregable. **Fase cerrada en esta sesión** (UX-7.10, D-079/D-080/D-081, aprobado explícitamente).

## 6. Riesgos detectados

| Riesgo | Severidad | Detalle |
|---|---|---|
| QA-030: cache-busting de CSS/JS roto en todo el sitio desde v0.5.x | 🟠 Alta | `CE_THEME_VERSION` (`functions.php`) y `Version:` (`style.css`) siguen en `0.4.1` pese a que el proyecto está en v0.8.0. Propuesto como Entregable 8.2, requiere decisión arquitectónica previa (fuente única de verdad del versionado). |
| QA-031: adjuntos de cotización potencialmente accesibles por URL directa | 🟠 Alta | Requiere decisión tuya sobre el mecanismo de protección antes de implementar (Entregable 8.3). |
| QA-032/033/034: robustez del formulario de cotización (concurrencia, archivo huérfano, idempotencia) | 🟡 Media | Entregable 8.4, depende de la decisión arquitectónica de 8.3 por compartir el mismo flujo de almacenamiento de adjuntos. |
| QA-012, QA-016, QA-035, QA-038: hallazgos Medios aislados sin corregir | 🟢 Baja | Propuestos para el Entregable 8.5. |
| QA-036: sin gestión de foco en overlays (menú móvil, modales) | 🟡 Media | Entregable 8.6, requiere decisión de diseño (R-4: utilidad centralizada vs. por componente). |
| QA-013: unificación real de `:root` entre `style.css`/`main.css` pendiente | 🟢 Baja | Solo se corrigió el comentario inexacto en 8.1; la unificación es una decisión arquitectónica en backlog. |
| `page.php` no existe (QA-041, verificado) | 🟢 Baja | Sin impacto funcional — `index.php` cubre el fallback. `TREE.md` corregido. Crear la plantilla dedicada es mejora futura sujeta a aprobación. |
| `screenshot.png` es un mockup ilustrativo, no fotografías reales | 🟢 Baja | Reversible sin cambio de código — ver `DECISIONS.md` D-040. |

## 7. Próximo módulo recomendado

Con la fase UX cerrada en su totalidad (sin ningún Sprint ni Entregable UX obligatorio pendiente), el próximo módulo recomendado es **retomar el Sprint 8**: confirmar el estado real de aprobación del Entregable 8.1 (ver `docs/CURRENT_SPRINT.md`, "Punto sin resolver"), aprobar o rechazar explícitamente el Entregable 8.2 (QA-030, ya implementado en código), y solo entonces continuar con el Entregable 8.3 (QA-031), conforme a `DECISIONS.md` D-038.
