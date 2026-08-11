# CE Construction — CHANGELOG.md

> Historial de versiones del proyecto. Este archivo es acumulativo: cada nueva versión se agrega al final, nunca se reescribe una versión anterior.
>
> **Nota:** las versiones v0.1.0 a v0.6.3 se mantienen exactamente como en las entregas previas de este documento. Esta actualización añade las entradas restantes del Sprint 7 y su cierre.

---

## v0.7.0 — Sprint 7, Entregable 7.1: inc/widgets.php
**Estado:** ✅ Completado — aprobado por el usuario.
- Añadido: `inc/widgets.php` (`CE_Construction_Widget_Contact`, `CE_Construction_Widget_Social`).
- Ver `DECISIONS.md` D-036.

## v0.7.1 — Sprint 7, Entregable 7.2: archive.php genérico
**Estado:** ✅ Completado — aprobado por el usuario.
- Añadido: `archive.php` (fallback para categoría/etiqueta/autor/fecha y CPTs `testimonio`/`ce_faq`).
- Ver `DECISIONS.md` D-037.

## v0.7.2 — Sprint 7, Entregable 7.3: corrección QA-018
**Estado:** ✅ Completado — aprobado por el usuario.
- Corregido: `assets/css/main.css` — responsive de `.ce-header__top` (sección 24, aditiva).
- Ver `DECISIONS.md` D-039.

---

## v0.7.3 — Sprint 7, Entregable 7.4: screenshot.png (Sprint 7 COMPLETADO)

**Módulo:** Vista previa del tema para wp-admin (cuarto y último Entregable del Sprint 7)
**Estado:** Entregado — pendiente de aprobación explícita del usuario (ver `DECISIONS.md` D-038)

### Añadido
- `screenshot.png`: mockup de la portada del tema (1200×900px, PNG RGB), generado con los tokens de diseño reales del proyecto (colores institucionales, tipografía Poppins, radios de borde, sombras) más una ilustración vectorial propia (skyline + grúa) para el fondo del hero. No usa fotografías reales del cliente — `assets/img/` sigue vacía, pendiente de assets reales.

### Sin cambios
- Ningún archivo PHP/CSS/JS del tema.

### Decisiones clave
- Ver `DECISIONS.md`: D-040.

### Verificaciones ejecutadas
- **Formato/dimensiones:** confirmado 1200×900px, PNG RGB de 8 bits, sin canal alfa (estándar WordPress para `screenshot.png`).
- **Sintaxis PHP/JavaScript:** no aplica — ningún archivo de código modificado en este Entregable.
- **Dependencias e includes:** no aplica — `screenshot.png` no se enqueda ni se referencia desde ningún archivo PHP; WordPress lo detecta automáticamente por convención de nombre en la raíz del tema.

---

## 🎉 SPRINT 7 COMPLETADO (pendiente de tu aprobación final del Entregable 7.4)

Los 4 Entregables del Sprint 7 están desarrollados:
- 7.1 — `inc/widgets.php` (v0.7.0) — ✅ Completado
- 7.2 — `archive.php` genérico (v0.7.1) — ✅ Completado
- 7.3 — Corrección QA-018 (v0.7.2) — ✅ Completado
- 7.4 — `screenshot.png` (v0.7.3) — 🟡 Entregado, pendiente de aprobación

Con la aprobación de 7.4, el Sprint 7 ("Extras y refinamiento QA") queda formalmente completado. El tema cubre en este punto: todo el contenido de "primera clase" (Servicios, Proyectos, Equipo, Clientes, Blog, páginas genéricas, 404, archivo genérico), widgets custom, 1 hallazgo QA Medio corregido (QA-018), y vista previa en wp-admin.

### Roadmap actualizado — próximos Sprints (propuestos, no confirmados)
- **Sprint 8 (propuesto):** resto de hallazgos QA Medios (QA-010 a QA-017), Bajos y Mejoras futuras — requiere aprobación explícita de cuáles corregir.
- **Sprint 9 (propuesto):** auditoría de accesibilidad y performance (incluye QA-026/QA-027: auto-hospedar Google Fonts/Font Awesome, Core Web Vitals).
- **Reemplazo de `screenshot.png`:** cuando el cliente provea fotografías reales de proyectos/equipo, puede sustituirse el archivo sin ningún cambio de código (ver `DECISIONS.md` D-040).
