# CE Construction — TODO.md

> Checklist maestro del proyecto. No se resume ni se reinicia: solo se actualizan los estados (✅ / 🟡 / ⬜) y se agregan tareas nuevas si surgen.

---

## 22. Backlog — Sprint 7 (COMPLETADO)
- ✅ 7.1 `inc/widgets.php`
- ✅ 7.2 `archive.php` genérico
- ✅ 7.3 Corrección QA-018
- ✅ 7.4 `screenshot.png`

## 23. Backlog — Sprint 8 "Cierre de Hallazgos QA" (re-planificado contra `QA_REPORT.md` consolidado, QA-001 a QA-042 — ver `DECISIONS.md` D-042)
> La agrupación anterior de esta sección (8.1: QA-010/014/015/017, 8.2: QA-011/012/013/016, 8.3: Bajos, 8.4: Mejoras futuras) queda **superseded**: no incluía QA-030/QA-031 (Alto, de la auditoría integral) y asumía que QA-015 requería corrección de código, lo cual se verificó como falso.
- 🟡 8.1 — Correcciones triviales de bajo riesgo — **desarrollado, pendiente de tu aprobación final**: QA-010 ✅, QA-011 ✅, QA-013 🟡 (parcial), QA-014 ✅, QA-015 ✅ (verificado, sin código), QA-017 ✅.
- ⬜ 8.2 — QA-030 (Alto: `CE_THEME_VERSION`/`style.css` congelados en 0.4.1, cache-busting roto) — **pendiente de tu aprobación de alcance**
- ⬜ 8.3 — QA-016 (script inline de galería sin dependencia `jquery` formal, mover a `assets/js/admin-gallery.js`) — **pendiente de tu aprobación de alcance**
- ⬜ 8.4 — QA-012 (caché de consultas "relacionados" en `inc/helpers.php`) — **pendiente de tu aprobación de alcance y de la estrategia de caché**
- ⬜ 8.5 — QA-031 (Alto: adjuntos de cotización potencialmente accesibles por URL directa) — **requiere decisión arquitectónica previa sobre cómo servir los adjuntos, no se implementa sin ella**
- ⬜ Backlog sin Entregable asignado: QA-032 a QA-040 (auditoría integral: race condition QA-032, archivo huérfano QA-033, idempotencia QA-034, autoplay accesible QA-035, foco de overlays QA-036, `aria-label` QA-037, canonical QA-038, Twitter Card QA-039, BreadcrumbList JSON-LD QA-040), QA-019 a QA-029 (Bajos/Mejoras futuras históricos), QA-041 (nota metodológica: verificar `page.php`, no es bug), QA-042 (mejora futura: preconnect/dns-prefetch a CDNs).

## 24. Backlog — Sprint 9 (propuesto, no iniciado)
- ⬜ Auditoría de accesibilidad y performance (auto-hospedar Google Fonts/Font Awesome, Core Web Vitals)

## 25. Otros pendientes generales
- ⬜ Reemplazo de `screenshot.png` por fotografías reales del cliente cuando estén disponibles (sin cambio de código requerido)

*(Secciones 1 a 21 — Sprints 1 a 7 — sin cambios respecto a versiones previas.)*

## 26. Backlog — Fase "Optimización UX / Conversión" (paralela al Sprint 8 pausado — ver `CURRENT_UX_SPRINT.md`)
- ✅ Sprint UX-1 "Home Builder: base arquitectónica" — **COMPLETADO** (UX-1.1: registro central de secciones + refactor de `front-page.php`; UX-1.2: panel de administración en el Customizer con activar/desactivar y reordenar).
- ✅ Sprint UX-2 "Secciones de Home faltantes: Team, Clients, FAQ" — **COMPLETADO** (13/13 secciones del catálogo con template-part real):
  - ✅ UX-2.1 — `template-parts/team.php`, `template-parts/clients.php` — completado
  - ✅ UX-2.2 — `template-parts/faq.php`, `template-parts/content-faq-accordion.php` — completado
- ✅ Sprint UX-3 "CTA centralizado + Modo del formulario de cotización" — **COMPLETADO** (UX-3.1: `ce_get_quote_cta_url()` + modo configurable; UX-3.2: modal de cotización).
- ✅ Sprint UX-4 "Hero configurable" — **COMPLETADO** (UX-4.1: tipo imagen/video + overlay; UX-4.2: modo slider).
- ✅ Sprint UX-5 "Múltiples CTA / estrategias de conversión" — UX-5.1 **completado** (CTA secundario reutilizable). UX-5.2 (documentación de "objetivo de plantilla") ⬜ no iniciado — pendiente del plan original.
- ✅ Sprint UX-6 "Arquitectura de reutilización de secciones fuera del Home Builder" — **COMPLETADO y aprobado** (UX-6.1 `page.php`, UX-6.2 shortcode `[ce_section]`, UX-6.3 fix de ancho — ver `DECISIONS.md` D-062). **Nota de renumeración (D-057):** el "Sprint UX-6" original del plan (registro documental de Responsive) se renumeró a **UX-9** para no colisionar con este Sprint UX-6 nuevo (arquitectura de reutilización), surgido de la auditoría post UX-5.1 — sigue ⬜ no iniciado.
- 🟡 Sprint UX-7 "Consistencia y configurabilidad" (Hero Home/interior, columnas + Quote Form en Hero, sidebars, CTA icono/color, logo independiente + UX-7.6 a UX-7.9 del benchmark) — **propuesto, pendiente de tu aprobación explícita** (`DECISIONS.md` D-058).
- 🟡 Sprint UX-8 "Video en Proyectos" (YouTube/TikTok/oEmbed) — propuesto, futuro, prioridad baja.
- ⬜ Sprint UX-9 (renumerado del "UX-6" original) — registro de backlog formal de Responsive, documental, sin código — no iniciado.
- Ver `docs/CURRENT_UX_SPRINT.md` (seguimiento vigente) y `docs/UX_CONVERSION_ANALISIS_Y_PLAN.md` (plan completo) para el detalle de alcance de cada Entregable.

*(La sección 23, Backlog — Sprint 8, permanece sin cambios: el Sprint 8 está pausado, no cerrado, y se retoma en el Entregable 8.3 una vez apruebes el 8.2 y esta fase paralela lo permita.)*
