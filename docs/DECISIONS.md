# CE Construction — DECISIONS.md

> Registro formal y acumulativo de decisiones arquitectónicas del proyecto.
> No se elimina ni se reescribe una decisión ya tomada: si cambia, se agrega una nueva entrada que referencia a la anterior.

---

> **Nota de este archivo:** las decisiones D-001 a D-035 se mantienen exactamente como en versiones previas de este documento. Esta entrega añade D-036 a D-041.

---

### D-036 — Alcance de `inc/widgets.php`: 2 widgets orientados al footer, sin CSS/JS nuevo
- **Fecha:** Sprint 7, Entregable 7.1
- **Solución elegida:** 2 widgets (`CE_Construction_Widget_Contact`, `CE_Construction_Widget_Social`) para dar uso real a `footer-1`.
- **Impacto:** Archivo 100% nuevo y aditivo.

### D-037 — `archive.php` como fallback dedicado, sin extender breadcrumbs de Testimonios/FAQ
- **Fecha:** Sprint 7, Entregable 7.2
- **Solución elegida:** `archive.php` completamente funcional para su propio alcance, sin extender `inc/seo.php`.
- **Impacto:** Breadcrumbs para esos contextos siguen mostrando solo "Inicio" — limitación preexistente, no un bug nuevo.

### D-038 — Nueva regla permanente: aprobación explícita obligatoria al cierre de cada Entregable
- **Fecha:** Tras el cierre inicial de los Entregables 7.1 y 7.2
- **Solución elegida:** Ningún Entregable se considera finalizado hasta que se hayan entregado todos sus archivos y el usuario los haya aprobado explícitamente. No debe iniciarse el siguiente Entregable sin esa señal previa. Regla obligatoria para todos los Sprints futuros.
- **Impacto:** Todo Entregable se marca "Entregado — pendiente de aprobación" hasta recibir la señal explícita.

### D-039 — Corrección QA-018: responsive de `.ce-header__top` vía `flex-wrap` + centrado
- **Fecha:** Sprint 7, Entregable 7.3
- **Solución elegida:** Regla `@media (max-width: 767.98px)` (sección 24, aditiva) en `assets/css/main.css`.
- **Impacto:** Cambio 100% aditivo, cero cambios en `header.php` u otros archivos.

### D-040 — `screenshot.png` generado como mockup propio del sistema de diseño, sin fotografías reales del cliente
- **Fecha:** Sprint 7, Entregable 7.4
- **Solución elegida:** Mockup 1200×900px con los tokens de diseño reales del tema (colores, tipografía Poppins, radios, sombras) + ilustración vectorial propia, autorizado explícitamente por el usuario ante la imposibilidad de proporcionar imágenes.
- **Impacto:** Cosmético, reversible sin cambio de código cuando existan fotografías reales.

---

### D-041 — Criterio de agrupación del Sprint 8 en 4 Entregables por nivel de riesgo/decisión requerida
- **Fecha:** Cierre del Sprint 7 / propuesta del Sprint 8
- **Problema:** Quedan 8 hallazgos QA Medios (QA-010 a QA-017), 5 Bajos (QA-019 a QA-023) y 6 Mejoras futuras (QA-024 a QA-029) sin corregir. Agruparlos todos en un solo Entregable dificultaría que el usuario apruebe selectivamente qué corregir (patrón ya establecido con QA-018 en el Entregable 7.3, donde el usuario aprobó un hallazgo específico de un conjunto de nueve).
- **Solución elegida:** Dividir el Sprint 8 en 4 Entregables agrupados por naturaleza de la corrección, no por severidad original de `QA_REPORT.md`:
  - **8.1** — Correcciones triviales y aisladas (QA-010, QA-014, QA-015, QA-017): cambios de una a pocas líneas, sin decisiones de diseño pendientes, bajo riesgo.
  - **8.2** — Correcciones que requieren una decisión de diseño/estrategia previa (QA-011: quitar `postMessage` vs. escribir script de preview; QA-012: estrategia de invalidación de caché; QA-013: qué bloque `:root` conservar; QA-016: dónde ubicar el nuevo archivo JS admin).
  - **8.3** — Hallazgos Bajos (QA-019 a QA-022), de impacto visual/semántico menor.
  - **8.4** — Mejoras futuras (QA-024 a QA-029), de naturaleza más arquitectónica/SEO avanzado, para evaluar cuáles aplicar.
- **Alternativas descartadas:** Un único Entregable "Sprint 8 completo" con los 19 hallazgos restantes — descartado porque impediría al usuario aprobar selectivamente subconjuntos pequeños, replicando el problema que ya se evitó en el Entregable 7.3 (donde solo se aprobó QA-018 de nueve candidatos).
- **Motivo:** Mantener el principio ya establecido de "unidades funcionales completas, aprobables de forma independiente" (D-030), aplicado ahora a nivel de agrupación temática de hallazgos QA en vez de módulos funcionales nuevos.
- **Impacto:** Ningún Entregable del Sprint 8 inicia sin que el usuario apruebe explícitamente qué hallazgos específicos de cada grupo corregir — la agrupación es una propuesta de organización, no una aprobación implícita de todos los hallazgos listados.
