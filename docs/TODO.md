# CE Construction — TODO.md

> Checklist maestro del proyecto. No se resume ni se reinicia: solo se actualizan los estados (✅ / 🟡 / ⬜) y se agregan tareas nuevas si surgen.

---

## 22. Backlog — Sprint 7 (COMPLETADO)
- ✅ 7.1 `inc/widgets.php`
- ✅ 7.2 `archive.php` genérico
- ✅ 7.3 Corrección QA-018
- ✅ 7.4 `screenshot.png`

## 23. Backlog — Sprint 8 "Cierre de Hallazgos QA" (agrupación vigente: `DECISIONS.md` D-043, que reemplaza tanto D-041 como la agrupación intermedia de D-042)
> **Corregido en la sesión de cierre de UX-7:** esta sección seguía reflejando la agrupación intermedia de D-042 (8.2=QA-030, 8.3=QA-016, 8.4=QA-012, 8.5=QA-031), que `D-043` reemplazó explícitamente por una reorganización completa de 8.2 a 8.7 según prioridad (seguridad/privacidad primero), dependencias y riesgo. Se corrige aquí para que coincida con `DECISIONS.md` D-043 y con `docs/CURRENT_SPRINT.md` (reconstruido en esta misma sesión).
- 🟡 8.1 — Correcciones triviales de bajo riesgo — **desarrollado y entregado**: QA-010 ✅, QA-011 ✅, QA-013 🟡 (parcial), QA-014 ✅, QA-015 ✅ (verificado, sin código), QA-017 ✅. **Estado de aprobación inconsistente entre documentos — ver `docs/CURRENT_SPRINT.md`, "Punto sin resolver".**
- 🟡 8.2 — QA-030 (Alto: `CE_THEME_VERSION`/`style.css` congelados, cache-busting roto) — **desarrollado e implementado en código, entregado, pendiente de tu aprobación explícita.** El Sprint 8 está pausado exactamente en este punto.
- ⬜ 8.3 — QA-031 (Alto: adjuntos de cotización potencialmente accesibles por URL directa) — **requiere decisión arquitectónica previa**, sin iniciar.
- ⬜ 8.4 — QA-032, QA-033, QA-034 (robustez del formulario de cotización: race condition del rate-limit, archivo huérfano, sin idempotencia) — depende de la decisión de 8.3, sin iniciar.
- ⬜ 8.5 — QA-012 (caché de "relacionados"), QA-016 (script inline de metabox sin dependencia formal), QA-035 (autoplay sin pausa accesible), QA-038 (canonical ausente) — sin iniciar.
- ⬜ 8.6 — QA-036 (foco de overlays: menú móvil, modales) — requiere decisión de diseño, sin iniciar.
- ⬜ 8.7 — Hallazgos Bajos: QA-019, QA-020, QA-021, QA-022, QA-037, QA-039, QA-040 — sin iniciar. (QA-043, antes candidato a este Entregable, fue resuelto en Sprint UX-11 — ver `DECISIONS.md` D-087/D-090 — y ya no forma parte del alcance del Sprint 8.)
- ⬜ Fuera de Sprint 8 (backlog, no se implementan sin aprobación de incorporarlas a un Sprint): QA-024 a QA-029, QA-042 (Mejoras futuras).
- ✅ QA-041 (nota metodológica: `page.php` no existía al verificarse — **dato posterior: `page.php` fue creado en Sprint UX-6, Entregable UX-6.1, D-059**, ya presente en el repositorio aunque no como resultado de un Entregable del Sprint 8) — cerrado, sin acción pendiente del Sprint 8.

## 24. Backlog — Sprint 9 (propuesto, no iniciado)
- ⬜ Auditoría de accesibilidad y performance (auto-hospedar Google Fonts/Font Awesome, Core Web Vitals)

## 25. Otros pendientes generales
- ⬜ Reemplazo de `screenshot.png` por fotografías reales del cliente cuando estén disponibles (sin cambio de código requerido)

*(Secciones 1 a 21 — Sprints 1 a 7 — sin cambios respecto a versiones previas.)*

## 26. Backlog — Fase "Optimización UX / Conversión" (paralela al Sprint 8 pausado — ver `CURRENT_UX_SPRINT.md`) — **actualizado en la sesión de cierre de UX-7**
- ✅ Sprint UX-1 "Home Builder: base arquitectónica" — **COMPLETADO**.
- ✅ Sprint UX-2 "Secciones de Home faltantes: Team, Clients, FAQ" — **COMPLETADO**.
- ✅ Sprint UX-3 "CTA centralizado + Modo del formulario de cotización" — **COMPLETADO**.
- ✅ Sprint UX-4 "Hero configurable" — **COMPLETADO**.
- ✅ Sprint UX-5 "Múltiples CTA / estrategias de conversión" — UX-5.1 **completado**. UX-5.2 (documentación de "objetivo de plantilla") ⬜ **sigue sin iniciar** — 100% documental, no bloqueante para ningún otro Sprint.
- ✅ Sprint UX-6 "Arquitectura de reutilización de secciones fuera del Home Builder" — **COMPLETADO y aprobado** (D-062).
- ✅ **Sprint UX-7 "Consistencia y configurabilidad" — CERRADO Y APROBADO EN ESTA SESIÓN** (UX-7.1 a UX-7.10, los 10 Entregables aprobados explícitamente por el usuario; UX-7.10 con D-079/D-080/D-081, aprobado sin reversión de D-081). Ver `docs/DECISIONS.md` D-058, D-063 a D-081.
- 🟡 Sprint UX-8 "Video en Proyectos" (YouTube/TikTok/oEmbed) — sigue **propuesto, futuro, sin iniciar, sin aprobar**. No bloqueante (prioridad baja, backlog futuro).
- ⬜ Sprint UX-9 (renumerado del "UX-6" original) — registro de backlog formal de Responsive, documental, sin código — sigue **sin iniciar**. No bloqueante.
- ✅ **Sprint UX-10 "Página de Testimonios: CPT propio + Google Reviews" — COMPLETADO Y APROBADO** (UX-10.1 + UX-10.2, D-073; UX-10.3, D-075/D-076; UX-10.4 absorbido por el Home Builder existente, sin Entregable propio).
- ✅ **QA-043** (iconos sociales del header sin estilo base) — detectado durante la auditoría UX, clasificado desde su origen como corrección de QA (no funcionalidad UX), registrado formalmente en `docs/QA_REPORT.md` en la sesión de cierre de UX-7 y **resuelto en Sprint UX-11** (ver `docs/DECISIONS.md` D-087/D-090) — no forma parte del Sprint 8.
- ✅ **Sprint UX-11 "Hero, Formulario del Hero y Header" — COMPLETADO Y APROBADO.** Plan de 6 puntos aprobado y ejecutado en un único Entregable: (1) corrección del recorte del formulario embebido en el Hero + panel visualmente separado, (2) altura del Hero reducida ~15%, (3) Hero interno usa siempre imagen destacada propia (revierte parte de D-063), (4) posición de imagen configurable por adjunto (verificado que no existe punto focal nativo; alternativa implementada), (5) overlay/gradiente configurable (color/dirección/extensión/intensidad), (6) espaciado del Header corregido (resuelve QA-043, incorporado a este Sprint). Ver `docs/DECISIONS.md` D-083 a D-090.
- **Conclusión de la auditoría UX de cierre:** con UX-7, UX-10 y UX-11 cerrados, **no queda ningún Sprint ni Entregable UX obligatorio pendiente antes de retomar el Sprint 8.** UX-5.2, UX-8 y UX-9 son backlog recomendable/futuro, no bloqueante.
- Ver `docs/CURRENT_UX_SPRINT.md` (seguimiento vigente, con el bloque "ESTADO FINAL" de esta sesión) y `docs/UX_CONVERSION_ANALISIS_Y_PLAN.md` (plan completo) para el detalle de alcance de cada Entregable.

*(La sección 23, Backlog — Sprint 8, fue corregida en esta misma sesión para reflejar la agrupación vigente de `DECISIONS.md` D-043 — el Sprint 8 sigue pausado, no cerrado, en el Entregable 8.2.)*
