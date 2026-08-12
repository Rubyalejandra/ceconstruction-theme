# CE Construction — CURRENT_SPRINT.md
### Referencia oficial del Sprint en curso

## Sprint actual
**Sprint 8 — "Cierre de Hallazgos QA"** — Estado: **En curso.** Entregable 8.1 ✅ **Completado** (aprobado explícitamente por el usuario). Entregable 8.2 🟡 **Entregado, pendiente de tu aprobación**. Entregables 8.3 a 8.7 propuestos, ninguno iniciado.

> Sprint anterior (7 — "Extras y refinamiento QA"): ✅ **COMPLETADO** (4/4 Entregables aprobados).
>
> La planificación vigente del Sprint 8 es la reorganización de `DECISIONS.md` D-043 (por prioridad/dependencias/riesgo), que reemplaza las dos agrupaciones anteriores (D-041 y D-042), ambas conservadas como registro histórico pero superseded.

## Entregables (ver `DECISIONS.md` D-043)

| Entregable | QA incluidos | Objetivo | Estado |
|---|---|---|---|
| 8.1 | QA-010, QA-011, QA-013 (parcial), QA-014, QA-015 (verificación), QA-017 | Correcciones triviales de bajo riesgo | ✅ **Completado — aprobado** |
| 8.2 | QA-030 | Integridad de despliegue: versionado de assets (Alto) | 🟡 **Entregado — pendiente de tu aprobación** |
| 8.3 | QA-031 | Seguridad/privacidad: adjuntos de cotización accesibles por URL (Alto) | ⬜ Propuesto — requiere decisión arquitectónica previa |
| 8.4 | QA-032, QA-033, QA-034 | Robustez del formulario de cotización: concurrencia e integridad de datos | ⬜ Propuesto — depende de la decisión de 8.3 |
| 8.5 | QA-012, QA-016, QA-035, QA-038 | Performance, deuda técnica, accesibilidad puntual y SEO (correcciones aisladas de bajo riesgo) | ⬜ Propuesto |
| 8.6 | QA-036 | Accesibilidad: gestión de foco en overlays (menú móvil y modales) | ⬜ Propuesto — requiere decisión de diseño (R-4) |
| 8.7 | QA-019, QA-020, QA-021, QA-022, QA-037, QA-039, QA-040 | Hallazgos Bajos, impacto visual/semántico menor | ⬜ Propuesto |
| Fuera de Sprint 8 | QA-024 a QA-029, QA-042 | Mejoras futuras — **no se implementan** sin aprobación explícita de incorporarlas a un Sprint | ⬜ Backlog |
| Cerrado (sin código) | QA-041 | Verificado: `page.php` no existe en el repositorio (documental, `TREE.md` corregido) | ✅ Verificado |

**Ningún Entregable del 8.3 al 8.7 inicia** sin que apruebes explícitamente el 8.2 y el alcance de cada uno, conforme a `DECISIONS.md` D-038.

## Trabajo realizado (Entregable 8.2)
QA-030 (Alto) corregido — ver `CHANGELOG.md` v0.8.1 para el detalle completo. Resumen: nueva función `ce_construction_asset_version()` en `inc/enqueue.php` (versión por `filemtime()` real de cada archivo, aplicada a `style.css`/`assets/css/main.css`/`assets/js/main.js`); `CE_THEME_VERSION` en `functions.php` deja de ser hardcodeada y se deriva de `wp_get_theme()->get('Version')`; cabecera `Version:` de `style.css` sincronizada a `0.8.1`. Solución de mecanismo (no un simple bump de versión), conforme al encargo explícito del Entregable.

## Archivos creados / modificados (Sprint 8, Entregable 8.2)
- Modificados: `functions.php`, `inc/enqueue.php`, `style.css`.
- Creados: ninguno.

## Documentación actualizada (en este cierre)
`DECISIONS.md` (D-044), `QA_REPORT.md` (QA-030 corregido: resumen ejecutivo, hallazgo 4.1, matriz de priorización, cierre), `CHANGELOG.md` (v0.8.1, y v0.8.0 actualizado a Completado tras tu aprobación explícita del Entregable 8.1), `ARCHITECTURE.md` (sección 9 y su historial — cambio real de mecanismo), `CURRENT_SPRINT.md` (este archivo). Sin cambios en `TREE.md` (sin cambio estructural de archivos) ni en `TODO.md`/`PROJECT_STATUS.md` más allá de lo que ya reflejaban correctamente (Sprint 8 en curso).

## Próximo Entregable
**8.3** — QA-031 (Alto): adjuntos de cotización potencialmente accesibles por URL directa. Requiere que decidas la estrategia arquitectónica antes de implementar (candidatos ya identificados en `QA_REPORT.md`: endpoint PHP autenticado vía `current_user_can()`, o mover el archivo fuera de `wp-content/uploads/`). No inicia sin tu aprobación explícita del Entregable 8.2.

## Riesgos abiertos
- QA-031 (Alto) sigue sin corregir — requiere que apruebes una decisión arquitectónica antes de implementar nada.
- QA-032/033/034 dependen de la decisión de QA-031 (mismo archivo `inc/quote-form.php`, mismo flujo de almacenamiento de adjuntos).
- QA-036 requiere decidir si la gestión de foco de overlays se centraliza en una utilidad compartida o se implementa por componente (R-4 de `QA_REPORT.md`).
- QA-013 solo se corrigió parcialmente (comentario); la unificación real de `:root` entre `style.css` y `main.css` sigue pendiente de decisión.
- `page.php` no existe (QA-041, verificado) — el sitio funciona vía el fallback de `index.php`, sin bloquear nada; crear una plantilla dedicada queda como mejora futura sujeta a tu aprobación.
- `screenshot.png` sigue siendo un mockup ilustrativo, pendiente de reemplazo cuando el cliente provea fotografías reales (fuera de Sprint, sin bloquear nada).
