# CE Construction — DECISIONS.md

> Registro formal y acumulativo de decisiones arquitectónicas del proyecto.
> No se elimina ni se reescribe una decisión ya tomada: si cambia, se agrega una nueva entrada que referencia a la anterior.

---

> **Nota de este archivo:** las decisiones D-001 a D-035 se mantienen exactamente como en la versión previa de este documento (Nonces por módulo, CPT `cotizacion`, secciones auto-ocultas, SEO auto-desactivable, `page-hero.php` reutilizable, relaciones heurísticas Servicio↔Proyecto, Schema `Project` como `@type` múltiple, correcciones QA Críticas/Altas D-017 a D-024, `has_archive` de Cliente D-025, metodología permanente de Entregables D-030, política de actualización incremental de documentación D-034, `404.php` con experiencia visual más completa D-035). Esta entrega añade D-036 a D-040, correspondientes al Sprint 7 completo.

---

### D-036 — Alcance de `inc/widgets.php`: 2 widgets orientados al footer, sin CSS/JS nuevo
- **Fecha:** Sprint 7, Entregable 7.1
- **Solución elegida:** 2 widgets (`CE_Construction_Widget_Contact`, `CE_Construction_Widget_Social`) para dar uso real a `footer-1`. Reutilizan exclusivamente `ce_get_social_links()` y clases CSS ya existentes.
- **Impacto:** Archivo 100% nuevo y aditivo; cero cambios en archivos ya aprobados.

---

### D-037 — `archive.php` como fallback dedicado, sin extender breadcrumbs de Testimonios/FAQ
- **Fecha:** Sprint 7, Entregable 7.2
- **Solución elegida:** `archive.php` completamente funcional para su propio alcance, sin extender `inc/seo.php`.
- **Impacto:** Los breadcrumbs para categoría/etiqueta/autor/fecha/Testimonios/FAQ siguen mostrando solo "Inicio" — limitación preexistente documentada, no un bug nuevo.

---

### D-038 — Nueva regla permanente: aprobación explícita obligatoria al cierre de cada Entregable
- **Fecha:** Tras el cierre inicial de los Entregables 7.1 y 7.2
- **Solución elegida:** Ningún Entregable se considera finalizado hasta que se hayan entregado todos sus archivos y el usuario los haya aprobado explícitamente (aprobando los archivos, o instruyendo directamente avanzar al siguiente Entregable). No debe iniciarse el siguiente Entregable sin esa señal previa. Regla obligatoria para todos los Sprints futuros.
- **Impacto:** Todo Entregable se marca "Entregado — pendiente de aprobación" hasta recibir esa señal explícita, momento en el cual pasa a "Completado". `HANDOFF.md` sección 16 recoge esta regla como parte del flujo obligatorio de cierre.

---

### D-039 — Corrección QA-018: responsive de `.ce-header__top` vía `flex-wrap` + centrado
- **Fecha:** Sprint 7, Entregable 7.3
- **Solución elegida:** Regla `@media (max-width: 767.98px)` (sección 24, aditiva) en `assets/css/main.css` que envuelve y centra `.ce-header__top`, `.ce-header__contact` y `.ce-header__social`.
- **Impacto:** Cambio 100% aditivo (~18 líneas), cero cambios en `header.php` ni otros archivos.

---

### D-040 — `screenshot.png` generado como mockup propio del sistema de diseño, sin fotografías reales del cliente
- **Fecha:** Sprint 7, Entregable 7.4
- **Problema:** WordPress requiere un archivo `screenshot.png` en la raíz del tema (1200×900px, ratio 4:3) para la vista previa del tema en Apariencia → Temas. El cliente no puede proporcionar fotografías reales de obras/proyectos en esta etapa, y `assets/img/` sigue vacía (ver `TREE.md`, pendiente de assets reales desde el Sprint 1).
- **Solución elegida:** Se autorizó explícitamente a Claude a crear una imagen propia para este Entregable. Se generó un mockup ilustrativo de la portada del tema (barra de contacto, header con logo/menú, hero con título/subtítulo/CTA, inicio de la sección de Servicios con 3 tarjetas) usando **exclusivamente los tokens de diseño reales del tema** (`--ce-color-primary: #0F2A43`, `--ce-color-secondary: #D98E29`, tipografía Poppins para encabezados, radios de borde `999px`/`12px`, sombras suaves ya definidas en `assets/css/main.css`), más una ilustración vectorial propia (skyline abstracto + grúa de construcción, sin fotografías) como fondo del hero. Renderizado a 1200×900px, PNG RGB de 8 bits.
- **Alternativas descartadas:**
  1. Dejar `screenshot.png` pendiente indefinidamente hasta que el cliente provea fotografías reales — descartado porque el archivo es parte del entregable explícito del Sprint 7 (7.4) y su ausencia no bloquea ninguna funcionalidad del tema, pero sí la presentación en wp-admin.
  2. Usar una captura de pantalla genérica sin relación con la identidad visual del proyecto — descartado por no aportar valor real y no representar el tema.
- **Motivo:** Entregar un `screenshot.png` funcional, coherente con la identidad visual ya aprobada del proyecto (mismos colores/tipografía/radios que el CSS real), sin inventar contenido fotográfico que pudiera confundirse con material real del cliente.
- **Impacto:** Archivo nuevo, cosmético, sin efecto en la funcionalidad del tema. **Reversible en cualquier momento:** cuando el cliente provea fotografías reales de proyectos/equipo, `screenshot.png` puede reemplazarse directamente sin ningún cambio de código, ya que WordPress solo lo usa como vista previa estática en el panel de administración.
