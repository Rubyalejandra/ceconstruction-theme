# CE Construction — TODO.md

> Checklist maestro del proyecto. No se resume ni se reinicia: solo se actualizan los estados (✅ / 🟡 / ⬜) y se agregan tareas nuevas si surgen.

---

## 22. Backlog — Sprint 7 (COMPLETADO)
- ✅ 7.1 `inc/widgets.php`
- ✅ 7.2 `archive.php` genérico
- ✅ 7.3 Corrección QA-018
- ✅ 7.4 `screenshot.png`

## 23. Backlog — Sprint 8 "Cierre de Hallazgos QA" (COMPLETADO Y CERRADO EN SU TOTALIDAD)
- ✅ 8.1 — Correcciones triviales de bajo riesgo — QA-010, QA-011, QA-013 (parcial), QA-014, QA-015, QA-017. Ver `docs/DECISIONS.md` D-095.
- ✅ 8.2 — QA-030 (cache-busting de assets). Ver `docs/DECISIONS.md` D-095.
- ✅ 8.3 — QA-031 (adjuntos de cotización protegidos). Ver `docs/DECISIONS.md` D-096/D-097.
- ✅ 8.4 — QA-032, QA-033, QA-034 (robustez del formulario de cotización). Ver `docs/DECISIONS.md` D-098/D-099/D-101.
- ✅ 8.5 — QA-012, QA-016, QA-035, QA-038. Ver `docs/DECISIONS.md` D-102/D-103.
- ✅ 8.6 — QA-036 (foco de overlays). Ver `docs/DECISIONS.md` D-104/D-105.
- ✅ 8.7 — Hallazgos Bajos: QA-019, QA-020, QA-021, QA-022, QA-037, QA-039, QA-040. Ver `docs/DECISIONS.md` D-106/D-107. **Sprint 8 completo y cerrado.**
- ⬜ Fuera de Sprint 8 (backlog, no se implementan sin aprobación de incorporarlas a un Sprint): QA-024 a QA-029, QA-042 (Mejoras futuras).
- ✅ QA-041 (nota metodológica, cerrada) — `page.php` fue creado en Sprint UX-6, Entregable UX-6.1, D-059.

## 24. Backlog — Sprint 9 (futuro, no definido, no iniciado)
- ⬜ Sin alcance definido — requiere que el usuario lo defina y apruebe explícitamente (D-038).

## 25. Otros pendientes generales
- ⬜ Reemplazo de `screenshot.png` por fotografías reales del cliente cuando estén disponibles (sin cambio de código requerido)

*(Secciones 1 a 21 — Sprints 1 a 7 — sin cambios respecto a versiones previas.)*

## 26. Backlog — Fase "Optimización UX / Conversión" (COMPLETA EN SU TOTALIDAD)
- ✅ Sprint UX-1 "Home Builder: base arquitectónica" — **COMPLETADO**.
- ✅ Sprint UX-2 "Secciones de Home faltantes: Team, Clients, FAQ" — **COMPLETADO**.
- ✅ Sprint UX-3 "CTA centralizado + Modo del formulario de cotización" — **COMPLETADO**.
- ✅ Sprint UX-4 "Hero configurable" — **COMPLETADO**.
- ✅ Sprint UX-5 "Múltiples CTA / estrategias de conversión" — UX-5.1 **completado**. UX-5.2 (documentación de "objetivo de plantilla") ⬜ **sigue sin iniciar** — 100% documental, no bloqueante.
- ✅ Sprint UX-6 "Arquitectura de reutilización de secciones fuera del Home Builder" — **COMPLETADO y aprobado** (D-062).
- ✅ Sprint UX-7 "Consistencia y configurabilidad" — **CERRADO Y APROBADO** (UX-7.1 a UX-7.10). Ver `docs/DECISIONS.md` D-058, D-063 a D-081.
- ✅ **Sprint UX-8 "Galería de Proyectos: medios flexibles y curación del Home" — CERRADO Y APROBADO EN SU TOTALIDAD** (esta sesión). UX-8.1 (galería mixta imagen/video del Proyecto, `docs/DECISIONS.md` D-108) y UX-8.2 (curación de la Galería del Home, con múltiples favoritas por proyecto tras D-110, `docs/DECISIONS.md` D-109/D-110) ambos aprobados explícitamente por el usuario.
- ⬜ Sprint UX-9 (renumerado del "UX-6" original) — registro de backlog formal de Responsive, documental, sin código — sigue **sin iniciar**. No bloqueante.
- ✅ Sprint UX-10 "Página de Testimonios: CPT propio + Google Reviews" — **COMPLETADO Y APROBADO**.
- ✅ QA-043 (iconos sociales del header) — **resuelto en Sprint UX-11**.
- ✅ Sprint UX-11 "Hero, Formulario del Hero y Header" — **CERRADO Y APROBADO EN SU TOTALIDAD**.
- **Conclusión:** con UX-7, UX-8, UX-10 y UX-11 cerrados por completo, **no queda ningún Sprint ni Entregable UX obligatorio pendiente.** UX-5.2 y UX-9 son backlog recomendable/futuro, no bloqueante.

## 27. Versionado — Primera Versión Estable (v1.0.0)
- ✅ **`style.css` actualizado de `Version: 0.8.5` a `Version: 1.0.0`** — declarado por el usuario al cierre del Sprint UX-8, con el Sprint 8 de QA y la fase UX-1 a UX-11 (más UX-8) completos, sin ningún hallazgo de severidad Alta o Media abierto ni ningún Entregable pendiente de aprobación. Esquema adoptado: Semantic Versioning (SemVer, `MAYOR.MENOR.PARCHE`), formalizado como estándar del proyecto. Ver `docs/DECISIONS.md` D-111.
- Sin cambios en el mecanismo real de cache-busting de assets (`filemtime()`, D-044) ni en `CE_THEME_VERSION` (sigue derivada dinámicamente de `wp_get_theme()`).

## 28. Conclusión general del proyecto a la fecha de este documento

**No hay ningún Sprint ni Entregable en curso en todo el proyecto.** El tema se encuentra en su **Primera Versión Estable (v1.0.0)**. Cualquier trabajo nuevo (Sprint 9, backlog de Mejoras futuras de QA, backlog UX no bloqueante, u otra línea) requiere que el usuario defina y apruebe explícitamente su alcance antes de iniciar cualquier implementación, conforme a D-038.
