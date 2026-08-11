# CE Construction — TREE.md

> Árbol completo del proyecto con estado por archivo.
> ✅ Implementado &nbsp;|&nbsp; 🟡 En desarrollo &nbsp;|&nbsp; ⬜ Pendiente

```
ce-construction-theme/
│
├── style.css                          ✅
├── functions.php                      ✅
├── header.php                         ✅
├── footer.php                         ✅
├── front-page.php                     ✅
├── index.php                          ✅
├── page.php                           ✅
├── single.php                         ✅
├── comments.php                       ✅
├── 404.php                            ✅
├── archive.php                        ✅ Sprint 7, Entregable 7.2
├── archive-servicio.php               ✅
├── single-servicio.php                ✅
├── archive-proyecto.php               ✅
├── archive-equipo.php                 ✅
├── single-equipo.php                  ✅
├── archive-clientes.php               ✅
├── single-clientes.php                ✅
├── single-proyecto.php                ✅
├── screenshot.png                     ✅ 🆕 Sprint 7, Entregable 7.4 — mockup 1200×900px generado con los tokens de diseño reales del tema (sin fotografías del cliente, ver DECISIONS.md D-040). **Entregado, pendiente de aprobación.**
│
├── docs/
│   ├── PROJECT_STATUS.md, TODO.md, TREE.md, HANDOFF.md, CHANGELOG.md,
│   │   DECISIONS.md, QA_REPORT.md, ARCHITECTURE.md, CURRENT_SPRINT.md    ✅
│
├── inc/
│   ├── setup.php                      ✅
│   ├── enqueue.php                    ✅ único archivo válido de encolado de assets
│   ├── customizer.php                 ✅
│   ├── helpers.php                    ✅
│   ├── cpt-servicios.php              ✅
│   ├── cpt-proyectos.php              ✅
│   ├── cpt-testimonios.php            ✅
│   ├── cpt-equipo.php                 ✅
│   ├── cpt-clientes.php               ✅
│   ├── cpt-faq.php                    ✅
│   ├── meta-boxes.php                 ✅
│   ├── quote-form.php                 ✅
│   ├── seo.php                        ✅
│   └── widgets.php                    ✅ Sprint 7, Entregable 7.1
│
├── template-parts/                    ✅ (20 archivos, sin cambios en Sprint 7 salvo reutilización por archive.php)
│
└── assets/
    ├── css/main.css                   ✅ 24 secciones — 🔧 Sprint 7, Entregable 7.3: sección 24 (QA-018, responsive de .ce-header__top)
    ├── js/main.js                     ✅ 13 módulos ES6 — sin cambios en Sprint 7
    └── img/                           ⬜ Vacía — pendiente de imágenes reales del cliente (no requerida para screenshot.png, que es un archivo independiente en la raíz del tema)
```

---

### Verificación de cierre de etapa (Sprint 7, Entregable 7.4 — screenshot.png)

Se agregó `screenshot.png` en la raíz del tema (único archivo nuevo de este Entregable). No requiere registro ni referencia en ningún archivo PHP — WordPress lo detecta automáticamente por convención de nombre y ubicación. Cero cambios en cualquier otro archivo del árbol. Con este archivo, **los 4 Entregables del Sprint 7 quedan desarrollados**; el Sprint se considera completado formalmente al recibir la aprobación explícita de este último Entregable (ver `DECISIONS.md` D-038).
