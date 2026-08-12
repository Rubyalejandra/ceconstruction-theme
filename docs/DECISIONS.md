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

---

### D-042 — Re-evaluación del Sprint 8 contra el estado real del repositorio; D-041 queda superseded en su agrupación
- **Fecha:** Inicio real del Sprint 8 (re-arranque solicitado explícitamente por el usuario)
- **Problema:** D-041 (arriba) fue una propuesta de agrupación generada sin verificar el hallazgo QA-015 contra el código real, y el usuario solicitó explícitamente **no reutilizar** la agrupación/numeración de esa planificación anterior si contradice el estado actual de `QA_REPORT.md` y del repositorio.
- **Verificación realizada contra el ZIP/repositorio actual (no contra la descripción de `QA_REPORT.md`):**
  - **QA-015 ya NO existe como problema real:** se verificó `inc/quote-form.php` línea por línea; `$attachment_name` sí se usa (`'post_title' => isset( $attachment_name ) ? $attachment_name : ''` al construir `$attachment_data` para `wp_insert_attachment()`). El hallazgo, tal como está descrito en `QA_REPORT.md` ("variable calculada pero nunca usada"), no se reproduce en el código actual. Se marca como **ya no aplica / falso positivo del reporte** — no requiere ningún cambio de código, solo la actualización de su estado en `QA_REPORT.md`.
  - QA-010, QA-011, QA-012, QA-013, QA-014, QA-016, QA-017 se confirmaron **vigentes** tras inspección directa del archivo correspondiente (ver detalle en `QA_REPORT.md`).
- **Solución elegida — nueva agrupación del Sprint 8 (reemplaza la de D-041):**
  - **Entregable 8.1** (este Entregable): QA-017, QA-014, QA-010, QA-011, QA-013 (solo corrección del comentario, sin unificar archivos) — todas correcciones aditivas, de bajo riesgo, sin decisión de diseño pendiente, sin tocar dos veces el mismo archivo entre Entregables. QA-015 se cierra aquí también, pero solo a nivel de documentación (sin cambio de código, por no ser un problema real).
  - **Entregable 8.2** (propuesto, no iniciado): QA-016 — requiere crear `assets/js/admin-gallery.js` nuevo y modificar `inc/meta-boxes.php` para encolarlo con `jquery` como dependencia declarada (actualmente inline, ver `QA_REPORT.md`); alcance mayor que 8.1, se aísla para no mezclar un archivo nuevo con las correcciones puntuales de 8.1.
  - **Entregable 8.3** (propuesto, no iniciado): QA-012 — cache/memoización de `ce_get_related_services()`/`ce_get_related_projects()`/`ce_get_related_services_for_project()` en `inc/helpers.php`; requiere decidir estrategia (caché estática por request vs. transient) antes de implementar.
  - **Backlog / decisión arquitectónica pendiente (no es un Entregable programado):** unificación completa de las variables `:root` duplicadas entre `style.css` y `assets/css/main.css` (parte más profunda de QA-013) — se corrigió únicamente el comentario inexacto en este Entregable 8.1; fusionar ambos bloques es un cambio estructural que toca el archivo raíz obligatorio del tema y requiere aprobación explícita previa, no se ejecuta sin ella.
- **Motivo:** Evitar corregir hallazgos que ya no existen (QA-015) y evitar repetir una agrupación generada sin verificación directa del código (D-041), conforme a la instrucción explícita del usuario de reconstruir la planificación desde `QA_REPORT.md` y el estado real del repositorio.
- **Impacto:** D-041 no se elimina ni se reescribe (dejado como registro histórico de una planificación previa), pero su agrupación queda **superseded** por la de esta decisión para efectos de ejecución real del Sprint 8. Todas las referencias a "Ver DECISIONS.md" añadidas en el código de este Entregable (`inc/enqueue.php`, `inc/customizer.php`, `inc/seo.php`) apuntan a D-042, no a D-041.

---

### D-043 — Reorganización completa del Sprint 8 (post Entregable 8.1) por prioridad, dependencias y riesgo
- **Fecha:** Tras la aprobación pendiente del Entregable 8.1, a solicitud explícita del usuario de reorganizar el resto del Sprint 8.
- **Problema:** La planificación de D-042 (Entregables 8.2 a 8.5) se hizo antes de aplicar un criterio explícito de priorización por seguridad/privacidad/integridad de datos/integridad de despliegue/errores funcionales/accesibilidad/performance/SEO/deuda técnica/mejoras futuras, y antes de mapear dependencias entre hallazgos de la auditoría integral (QA-030 a QA-042). El usuario solicitó una reorganización completa bajo esos criterios, evaluando además si QA-030 y QA-031 debían agruparse o separarse, si el grupo de performance/accesibilidad/SEO era demasiado grande, y verificando QA-041 contra el repositorio real.
- **Verificación QA-041 realizada:** se confirmó que `page.php` **no existe** en el repositorio actual — `TREE.md` lo marcaba incorrectamente como ✅. No es un bug (WordPress usa `index.php` como fallback funcional para páginas estáticas vía su rama `is_singular()`), pero es una inconsistencia documental real, corregida en `TREE.md`. Se deja como decisión pendiente del usuario si crear `page.php` dedicado es una mejora futura a programar.
- **Solución elegida — nueva estructura del Sprint 8 (reemplaza los Entregables 8.2 a 8.5 de D-042; el Entregable 8.1 no cambia):**
  - **8.2** — QA-030 solo (Alto, integridad de despliegue: versionado de assets). Aislado de QA-031 porque, aunque ambos son "Alto", son técnicamente independientes (uno es cache-busting de CSS/JS, el otro es control de acceso a archivos subidos) y cada uno requiere una decisión arquitectónica propia que el usuario debe poder aprobar por separado sin que una condicione a la otra.
  - **8.3** — QA-031 solo (Alto, seguridad/privacidad: adjuntos de cotización potencialmente accesibles por URL directa). Requiere decisión arquitectónica previa sobre el mecanismo de protección.
  - **8.4** — QA-032, QA-033, QA-034 (Medio, robustez del formulario de cotización: race condition del rate-limit, archivo huérfano si falla `wp_insert_post()`, sin idempotencia ante envíos duplicados). Los 3 comparten archivo (`inc/quote-form.php`) y dominio funcional (robustez del flujo de envío), por lo que se agrupan en un solo Entregable en vez de fragmentar el mismo archivo en 3 Entregables distintos. **Dependencia con 8.3:** QA-033 (archivo huérfano) toca el mismo tramo de código que la protección de adjuntos de QA-031: si 8.3 cambia dónde/cómo se almacena el adjunto, la lógica de limpieza de huérfanos de QA-033 debe alinearse con ese mecanismo. Por eso 8.4 se planifica después de 8.3, aunque la implementación de 8.3 en sí pueda posponerse tras su propia decisión arquitectónica.
  - **8.5** — QA-012, QA-016, QA-035, QA-038 (Medio/performance/accesibilidad/deuda técnica/SEO: caché de consultas relacionadas, script inline de metabox sin dependencia, autoplay de testimonios sin pausa accesible, `<link rel="canonical">` ausente). Los 4 son correcciones aisladas, de bajo riesgo individual, sin decisión de diseño mayor pendiente, y no dependen entre sí ni de otros Entregables — se agrupan por ser del mismo perfil de riesgo/tamaño, no por tema.
  - **8.6** — QA-036 solo (Medio, accesibilidad: gestión de foco ausente en menú móvil y modales). Se separó del grupo anterior porque, a diferencia de QA-012/016/035/038, no es una corrección puntual y aislada: toca 2 módulos JS (`ModuleMobileNav`, `ModuleModals`) más `header.php`/`footer.php`, y la solución recomendada (R-4 del `QA_REPORT.md`: gestión de foco centralizada) implica decidir si se implementa como una utilidad compartida (focus-trap genérico) o de forma puntual por componente — una decisión de diseño que no aplica a los hallazgos de 8.5.
  - **8.7** — Hallazgos Bajos: QA-019, QA-020, QA-021, QA-022, QA-037, QA-039, QA-040. Impacto visual/semántico menor, sin dependencias entre sí ni con Entregables anteriores.
  - **Fuera del Sprint 8, no se implementan:** QA-024 a QA-029 y QA-042 (Mejoras futuras) — se mantienen como backlog, sujeto a que el usuario decida incorporarlas a un Sprint futuro (propuesto: Sprint 9, auditoría de accesibilidad/performance).
  - **QA-041:** verificado en esta decisión (ver arriba); no genera Entregable de corrección — es una nota documental ya cerrada, con una posible mejora futura (crear `page.php`) pendiente de que el usuario la apruebe explícitamente si la quiere programar.
- **Nota sobre `assets/js/main.js`:** QA-035 (8.5) y QA-036 (8.6) tocan el mismo archivo (`assets/js/main.js`) en Entregables distintos, pero módulos distintos (`ModuleTestimonialSlider` vs. `ModuleMobileNav`/`ModuleModals`). No se considera una violación de "no dividir un mismo archivo entre Entregables": `main.js` es monolítico por diseño (ver `ARCHITECTURE.md` sección 9), así que tocarlo en distintos Entregables para módulos distintos es el patrón esperado del proyecto, no una fragmentación de un mismo cambio.
- **Motivo:** Aplicar de forma explícita el criterio de priorización (seguridad/privacidad/integridad primero, luego funcional/accesibilidad/performance/SEO, luego deuda técnica, mejoras futuras al final) y de riesgo (no mezclar cambios de seguridad de alto riesgo entre sí ni con refactorizaciones o mejoras menores en el mismo Entregable), solicitado explícitamente por el usuario.
- **Impacto:** Ningún Entregable del 8.2 al 8.7 inicia sin aprobación explícita previa del Entregable 8.1 y del alcance de cada uno, conforme a D-038. No se implementó ningún hallazgo de QA-030 a QA-042 en esta decisión — es exclusivamente de planificación.

---

### D-044 — Corrección QA-030: cache-busting por `filemtime()` + `CE_THEME_VERSION` derivada de `wp_get_theme()`
- **Fecha:** Sprint 8, Entregable 8.2
- **Problema:** `CE_THEME_VERSION` (constante hardcodeada en `functions.php`) era la fuente de versión de los 3 assets propios del tema (`style.css`, `assets/css/main.css`, `assets/js/main.js`) en `inc/enqueue.php`. Al depender de que un desarrollador la actualizara manualmente en cada despliegue, quedó congelada en `0.4.1` desde el Sprint 5 pese a que el proyecto avanzó hasta v0.8.0 — el problema volvió a producirse porque la solución dependía de la memoria humana, no de un mecanismo estructural. `CURRENT_SPRINT.md` sugería como candidato `wp_get_theme()->get('Version')` (leer la cabecera de `style.css`), pero por sí solo ese candidato solo traslada el mismo problema a otro archivo: sigue exigiendo que alguien recuerde subir un número a mano.
- **Solución elegida (combinación de dos mecanismos, no uno solo):**
  1. **Cache-busting real** (`assets/css/main.css`, `assets/js/main.js`, `style.css` como asset encolado): nueva función `ce_construction_asset_version( $relative_path )` en `inc/enqueue.php`, que devuelve `filemtime()` del archivo real en disco. Se actualiza automáticamente cada vez que el archivo cambia, sin ninguna intervención manual — resuelve la causa raíz de forma permanente.
  2. **`CE_THEME_VERSION`** (versión general del tema, uso informativo): deja de ser un valor hardcodeado y pasa a derivarse de `wp_get_theme()->get( 'Version' )`, que lee la cabecera `Version:` de `style.css`. Esto unifica en una sola fuente lo que antes eran dos valores independientes que podían desincronizarse entre sí (la constante y la cabecera — ver QA-022 histórico), aunque ya no interviene en el cache-busting real.
- **Alternativas descartadas:**
  - Usar únicamente `wp_get_theme()->get('Version')` como `$ver` de los 3 assets — descartada por no resolver la causa raíz (seguiría siendo un valor manual, solo que en un archivo distinto).
  - Simplemente incrementar `CE_THEME_VERSION` de `0.4.1` a `0.8.1` sin cambiar el mecanismo — descartada explícitamente por el usuario en el encargo de este Entregable ("el objetivo no es simplemente cambiar manualmente CE_THEME_VERSION... la solución debe evitar que el problema vuelva a producirse").
  - Usar un hash del contenido de cada archivo en vez de `filemtime()` — descartada por ser una complejidad innecesaria para este proyecto (sin build step ni minificación); `filemtime()` es suficiente y es el mecanismo estándar recomendado por la documentación de WordPress para temas sin proceso de build.
- **Motivo:** Resolver la causa raíz (fuente de versión manual y olvidable) en vez de solo el síntoma (el valor `0.4.1` desactualizado), cumpliendo el requisito explícito del usuario de que el problema no vuelva a producirse al seguir evolucionando el proyecto.
- **Impacto:** Cambios en `functions.php` (definición de `CE_THEME_VERSION`), `inc/enqueue.php` (nueva función + 3 llamadas actualizadas), `style.css` (cabecera `Version:` sincronizada a `0.8.1` + comentario). Sin cambios en ningún otro archivo. `assets/css/main.css` y `assets/js/main.js` no se tocaron (no era necesario para esta corrección). Documentado también en `ARCHITECTURE.md` sección 9 (cambio real de mecanismo, no solo de valor).
