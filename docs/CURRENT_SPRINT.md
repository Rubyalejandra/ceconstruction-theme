# CE Construction — CURRENT_SPRINT.md
### Referencia oficial del Sprint 8 ("Cierre de Hallazgos QA")

> **Nota de reconstrucción (sesión de cierre de UX-7):** el contenido de este archivo había sido sobrescrito por error con una copia literal de `docs/CURRENT_UX_SPRINT.md` (mismo título "CURRENT_UX_SPRINT.md" en la cabecera, mismo contenido de la fase UX salvo por el punto exacto de congelación en el que cada copia dejó de actualizarse). El Sprint 8 nunca dejó de existir ni de estar pausado; simplemente este documento de seguimiento había perdido su propio contenido. Se reconstruye aquí **exclusivamente a partir de fuentes cruzadas ya existentes y verificadas contra el código real**: `docs/DECISIONS.md` (D-041 a D-044), `docs/TODO.md` (sección 23), `docs/QA_REPORT.md`, `docs/PROJECT_STATUS.md` y `docs/HANDOFF.md`. Donde esas fuentes se contradicen entre sí, se señala explícitamente en vez de asumir un dato — ver "Punto sin resolver" más abajo.

---

## Estado del Sprint 8

**Sprint 8 — "Cierre de Hallazgos QA": EN CURSO, PAUSADO desde antes del inicio de la fase "Optimización UX / Conversión".**

| Entregable | Alcance | Estado |
|---|---|---|
| 8.1 | QA-010, QA-011, QA-013 (parcial, solo comentario), QA-014, QA-015 (verificado como no reproducible, sin cambio de código), QA-017 | ✅ **Aprobado explícitamente por el usuario** (ver "Punto sin resolver — RESUELTO" abajo). `docs/DECISIONS.md` D-095. |
| 8.2 | QA-030 (cache-busting de assets: `filemtime()` + `CE_THEME_VERSION` derivada de `wp_get_theme()`) | ✅ **Aprobado explícitamente por el usuario** en esta sesión, junto con la instrucción de continuar con el Entregable 8.3. `docs/DECISIONS.md` D-095. |
| 8.3 | QA-031 (adjuntos de cotización potencialmente accesibles por URL directa) | 🟡 **Implementado y entregado — pendiente de tu aprobación final.** Alcance (Opción 1: carpeta protegida + endpoint autenticado) aprobado explícitamente antes de escribir código; el código ya está implementado (`inc/quote-attachments.php` nuevo, `inc/quote-form.php`/`functions.php` modificados). Ver `docs/DECISIONS.md` D-096. |
| 8.4 | QA-032, QA-033, QA-034 (robustez del formulario de cotización: race condition del rate-limit, archivo huérfano si falla `wp_insert_post()`, sin idempotencia) | ⬜ Propuesto, sin iniciar — depende de la decisión arquitectónica de 8.3 (comparten el flujo de almacenamiento de adjuntos). |
| 8.5 | QA-012 (caché de consultas "relacionados"), QA-016 (script inline de metabox sin dependencia formal), QA-035 (autoplay de testimonios sin pausa accesible), QA-038 (`<link rel="canonical">` ausente) | ⬜ Propuesto, sin iniciar. |
| 8.6 | QA-036 (sin gestión de foco en overlays: menú móvil, modales) | ⬜ Propuesto, sin iniciar — requiere decisión de diseño (utilidad de foco centralizada vs. por componente). |
| 8.7 | Hallazgos Bajos: QA-019, QA-020, QA-021, QA-022, QA-037, QA-039, QA-040 | ⬜ Propuesto, sin iniciar. |
| Fuera de Sprint 8 (backlog) | QA-024 a QA-029, QA-042 (Mejoras futuras) | ⬜ No se implementan sin aprobación explícita de incorporarlas a un Sprint. |

Esta agrupación de 8.3 a 8.7 es la **vigente** (`docs/DECISIONS.md` D-043), y **reemplaza** tanto la agrupación original de D-041 (nunca ejecutada) como la agrupación intermedia de D-042 (8.2=QA-030, 8.3=QA-016, 8.4=QA-012, 8.5=QA-031), que D-043 reemplazó explícitamente por razones de prioridad/dependencia/riesgo. **`docs/TODO.md` sección 23 todavía usaba la agrupación intermedia de D-042 y fue corregida en esta misma sesión de cierre** para reflejar D-043 (ver "Reconciliación documental" en el informe de cierre entregado al usuario).

### QA-041 (nota metodológica, ya verificada — sin Entregable de corrección)
`page.php` no existía en el repositorio en el momento de D-043 (`TREE.md` lo marcaba incorrectamente como existente). **Dato posterior relevante:** `page.php` fue creado en la fase UX paralela (Sprint UX-6, Entregable UX-6.1, aprobado — ver `docs/DECISIONS.md` D-059/D-062), por lo que el archivo **sí existe hoy** en el repositorio, aunque no como resultado de ningún Entregable del Sprint 8. QA-041 queda cerrado como nota metodológica: no requiere ninguna acción del Sprint 8.

### QA-043 (nuevo, registrado formalmente en esta sesión de cierre)
Detectado durante la auditoría UX (no durante una revisión del Sprint 8): `.ce-header__social` (iconos sociales de la barra superior del header) no tiene reglas base de `display`/`gap`/tamaño en `assets/css/main.css`, a diferencia de `.ce-footer__social`, que sí las tiene — confirmado visualmente, los iconos aparecen apelmazados. Clasificado desde su origen como corrección de QA (bug visual), no como funcionalidad UX — ver `docs/DECISIONS.md` D-058 (Decisión 4) y `docs/UX_CONVERSION_ANALISIS_Y_PLAN.md` §8.5. **Formalmente incorporado a `docs/QA_REPORT.md` en esta sesión de cierre**, sin asignar todavía a un Entregable específico del Sprint 8 — pendiente de que decidas si se agrupa con 8.7 (Bajos, por similitud de impacto visual) o se resuelve de forma aislada.

---

## ✅ Punto sin resolver — RESUELTO: estado de aprobación del Entregable 8.1

Existía una contradicción real entre fuentes (`docs/CHANGELOG.md` lo marcaba aprobado; `docs/DECISIONS.md` D-043, `docs/HANDOFF.md`, `docs/PROJECT_STATUS.md` y `docs/QA_REPORT.md` lo describían como pendiente). **El usuario confirmó explícitamente que el Entregable 8.1 sí quedó aprobado**, resolviendo la contradicción a favor de esa confirmación directa por encima de las cuatro fuentes documentales que asumían lo contrario. Ver `docs/DECISIONS.md` D-095.

## ✅ Entregable 8.2 aprobado

El usuario aprobó explícitamente el Entregable 8.2 (QA-030, ya implementado en código) en la misma sesión, junto con la instrucción de continuar con el Entregable 8.3. Ver `docs/DECISIONS.md` D-095.

---

## Trabajo realizado (histórico, sin cambios desde la pausa)

Ver `docs/DECISIONS.md` D-041 a D-044 y `docs/CHANGELOG.md` (entradas v0.8.0 y v0.8.1) para el detalle técnico completo de los Entregables 8.1 y 8.2, ya entregados. Ningún archivo de estos dos Entregables fue tocado por la fase UX paralela (UX-1 a UX-10) — verificado explícitamente en cada cierre de Entregable UX contra la lista de archivos del Sprint 8 (`functions.php` en la parte de `CE_THEME_VERSION`, `inc/enqueue.php`, `header.php`, `inc/customizer.php`/color settings, `inc/seo.php`/JSON-LD, `style.css`/comentario de cabecera — más allá de los bumps de versión, ya documentados, que no alteran la lógica de QA-030).

## Documentación actualizada (esta sesión de cierre)

`docs/TODO.md` (sección 23, corregida a la agrupación de D-043), `docs/PROJECT_STATUS.md` (estado del Sprint 8 realineado), `docs/QA_REPORT.md` (QA-043 registrado; referencia stale a "QA-016 propuesto para Entregable 8.2" corregida a 8.5), este mismo archivo (reconstrucción completa). Sin cambios de código.

## Próximo paso

El Entregable 8.3 (QA-031) ya está **implementado y entregado**, tras aprobación explícita del alcance (Opción 1) antes de escribir código. **Pendiente de tu aprobación final** del código entregado y de las pruebas funcionales reales listadas en `docs/DECISIONS.md` D-096 (sin entorno WordPress disponible para ejecutarlas en esta sesión). Con esa aprobación, el Sprint 8 continúa con el **Entregable 8.4** (QA-032, QA-033, QA-034 — dependían de la decisión arquitectónica de 8.3, ya tomada).
