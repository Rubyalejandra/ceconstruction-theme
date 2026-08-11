# CE Construction — HANDOFF.md
### Documento oficial de transferencia entre sesiones

> Este documento, junto con `PROJECT_STATUS.md`, `TODO.md`, `TREE.md`, `CHANGELOG.md`, `DECISIONS.md`, `QA_REPORT.md`, `ARCHITECTURE.md` y `CURRENT_SPRINT.md`, es la fuente oficial del estado del proyecto.

**Versión de referencia:** v0.7.3 (ver `CHANGELOG.md`)
**Última sesión de trabajo:** Sprint 7 ("Extras y refinamiento QA") — **4/4 Entregables desarrollados**, pendiente de tu aprobación final del Entregable 7.4 (`screenshot.png`) para declarar el Sprint formalmente COMPLETADO. Esta actualización de `HANDOFF.md` corresponde a la finalización completa de un Sprint — uno de los 3 únicos disparadores de esta plantilla (ver sección 16).

---

## 1. Resumen ejecutivo

CE Construction es un tema profesional de WordPress a medida. A la fecha, además de todo lo ya documentado en versiones previas de este archivo (backend completo, frontend completo de Servicios/Proyectos/Equipo/Clientes/Blog/páginas genéricas/404), el Sprint 7 agregó:

- **`inc/widgets.php`** (Entregable 7.1): 2 widgets custom (Contacto, Redes Sociales), dando uso real al sidebar `footer-1`.
- **`archive.php`** (Entregable 7.2): fallback genérico para categoría/etiqueta/autor/fecha y los CPTs `testimonio`/`ce_faq`.
- **Corrección QA-018** (Entregable 7.3): responsive de `.ce-header__top` vía `flex-wrap`, único hallazgo Medio corregido hasta la fecha (aprobación explícita puntual del usuario; QA-010 a QA-017 siguen sin corregir).
- **`screenshot.png`** (Entregable 7.4): vista previa del tema para wp-admin, generada como mockup propio del sistema de diseño (sin fotografías reales, ver `DECISIONS.md` D-040), entregado y pendiente de aprobación final.

---

## 2. Nueva regla permanente incorporada en este Sprint

Ver `DECISIONS.md` D-038: **ningún Entregable se considera finalizado hasta que se hayan entregado todos sus archivos y el usuario los haya aprobado explícitamente.** No se inicia el siguiente Entregable sin esa aprobación previa. Regla obligatoria para todos los Sprints futuros, incorporada a la metodología permanente de la sección 16 de este mismo documento.

---

## 3. Estructura del tema (cambios del Sprint 7)

```
ce-construction-theme/
├── archive.php                        ✅ Entregable 7.2
├── screenshot.png                     ✅ Entregable 7.4 (mockup propio, pendiente de aprobación final)
├── inc/widgets.php                    ✅ Entregable 7.1
├── assets/css/main.css                🔧 sección 24 añadida (QA-018, Entregable 7.3)
```

Ver `TREE.md` para el árbol completo actualizado.

---

## 4. Bugs conocidos

Sin bugs abiertos conocidos a la fecha de este documento. Los 9 bugs históricos (BUG-001 a BUG-005) siguen resueltos, sin cambios.

---

## 5. Decisiones arquitectónicas importantes (Sprint 7)

- **D-036** — Alcance de `inc/widgets.php`.
- **D-037** — `archive.php` como fallback dedicado.
- **D-038** — Nueva regla permanente de aprobación explícita al cierre de cada Entregable.
- **D-039** — Corrección QA-018.
- **D-040** — `screenshot.png` como mockup propio del sistema de diseño, reversible sin cambio de código.

Registro completo y acumulativo en `DECISIONS.md` (D-001 a D-040 a la fecha).

---

## 6. Orden recomendado para continuar

Con la aprobación del Entregable 7.4, el Sprint 7 queda COMPLETADO. Sprint 8 propuesto (pendiente de definición de alcance con el usuario):
1. Hallazgos QA Medios restantes (QA-010 a QA-017), con aprobación explícita de cuáles corregir.
2. Hallazgos QA Bajos y Mejoras futuras, si se autoriza.
3. Auditoría de accesibilidad y performance (auto-hospedar Google Fonts/Font Awesome, Core Web Vitals).

---

## 16. Metodología permanente: Gestión automática de Sprints y Entregables (actualizada)

> Ver versiones previas de este documento para el detalle completo original (D-030) y su refinamiento de actualización incremental de documentación (D-034). Esta sección añade la regla incorporada en este Sprint.

### Regla de aprobación explícita (D-038, nueva)

Ningún Entregable se considera finalizado hasta que:
1. Se hayan entregado todos los archivos creados o modificados durante ese Entregable, como archivos completos descargables (nunca solo diffs, salvo solicitud expresa).
2. El usuario los haya aprobado explícitamente — ya sea aprobando los archivos directamente, o instruyendo avanzar al siguiente Entregable (lo cual constituye la señal de aprobación de los Entregables ya entregados y recibidos).

No debe iniciarse el siguiente Entregable sin haber completado ambos pasos. Esta regla es obligatoria para todos los Sprints futuros del proyecto, sin excepción, y se suma —sin reemplazarlo— al flujo de cierre ya establecido por D-030 (verificación de sintaxis, actualización incremental de documentación, marcado como Completado, propuesta del siguiente Entregable, generación del prompt de continuación).

---

# Prompt para continuar el proyecto

```
Estoy retomando el desarrollo del tema de WordPress "CE Construction".
Te adjunto los archivos de control del proyecto: PROJECT_STATUS.md, TODO.md,
TREE.md, CHANGELOG.md, DECISIONS.md, QA_REPORT.md, ARCHITECTURE.md,
CURRENT_SPRINT.md y este mismo HANDOFF.md.

El Sprint 7 quedó COMPLETADO (4/4 Entregables aprobados). Aplica la
metodología permanente de Gestión de Sprints y Entregables (HANDOFF.md
sección 16), incluida la regla de aprobación explícita obligatoria
(D-038): no inicies ningún Entregable nuevo sin mi aprobación explícita
del anterior, y entrega siempre los archivos completos como artifacts
descargables, no diffs.

El siguiente Sprint a desarrollar es: [indícalo aquí, o escribe "el que
consideres más prioritario según PROJECT_STATUS.md sección 7" para que
propongas el Sprint 8 dividido en Entregables].
```
