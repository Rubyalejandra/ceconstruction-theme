# CE Construction — TREE.md

> Árbol completo del proyecto con estado por archivo.
> ✅ Implementado &nbsp;|&nbsp; 🟡 En desarrollo &nbsp;|&nbsp; ⬜ Pendiente

```
ce-construction-theme/
│
├── style.css                          ✅
├── functions.php                      ✅ 🔧 Sprint UX-6, Entregable UX-6.2 (aprobado — D-060, D-062) — 1 línea nueva en el array $modules para cargar inc/section-shortcode.php
├── header.php                         ✅
├── footer.php                         ✅
├── front-page.php                     ✅ 🔧 Sprint UX-1, Entregable UX-1.1 — deja de tener orden fijo, ahora itera el registro de inc/home-builder.php (comportamiento visual idéntico, ver DECISIONS.md D-045). 🔧 Sprint UX-5, Entregable UX-5.1 — 1 línea condicional para variant=secondary en cta_secondary (D-056). 🔧 Sprint UX-6, Entregable UX-6.2 — esa misma línea se reemplaza por una llamada a ce_construction_get_home_section_args() (inc/home-builder.php), sin cambio de comportamiento (D-060)
├── index.php                          ✅
├── page.php                           ✅ 🆕 Sprint UX-6, Entregable UX-6.1 (aprobado — D-059, D-062) — corrige QA-041 (ya no falta la plantilla dedicada para Páginas): hero interno vía template-parts/page-hero.php + the_content(), mismo patrón que single.php. 🔧 Sprint UX-6, Entregable UX-6.3 — restructuración de wrappers (<article> fuera de .ce-max-w-content, clase ce-content-breakout añadida, D-061)
├── single.php                         ✅ 🔧 Sprint UX-6, Entregable UX-6.3 (aprobado — D-061, D-062) — restructuración de wrappers (<article> fuera de .ce-max-w-content, clase ce-content-breakout añadida junto a .ce-service-content), sin cambio de contenido
├── comments.php                       ✅
├── 404.php                            ✅
├── archive.php                        ✅ Sprint 7, Entregable 7.2
├── archive-servicio.php               ✅
├── single-servicio.php                ✅ 🔧 Sprint UX-2, Entregable UX-2.2: único bloque tocado — el accordion FAQ ahora invoca template-parts/content-faq-accordion.php (partial compartido), resto del archivo sin cambios
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
│   ├── CURRENT_UX_SPRINT.md           ✅ 🆕 fase "Optimización UX / Conversión" (paralela al Sprint 8, pausado) — equivalente de CURRENT_SPRINT.md para esta fase
│   ├── UX_CONVERSION_ANALISIS_Y_PLAN.md ✅ 🆕 análisis y plan completo de Sprints UX-1 a UX-9 (D-057)
│
├── inc/
│   ├── setup.php                      ✅
│   ├── enqueue.php                    ✅ único archivo válido de encolado de assets
│   ├── customizer.php                 ✅ 🔧 Sprint UX-4, Entregables UX-4.1/UX-4.2 — controles de tipo/video/overlay/slides del Hero, ver DECISIONS.md D-054/D-055
│   ├── helpers.php                    ✅ 🔧 Sprint UX-4, Entregable UX-4.2 — `ce_get_hero_slide_ids()` nueva (aditiva, al final del archivo)
│   ├── cpt-servicios.php              ✅
│   ├── cpt-proyectos.php              ✅
│   ├── cpt-testimonios.php            ✅
│   ├── cpt-equipo.php                 ✅
│   ├── cpt-clientes.php               ✅
│   ├── cpt-faq.php                    ✅
│   ├── meta-boxes.php                 ✅ patrón de galería (`wp.media` multiple + IDs por comas) reutilizado por `CE_Customize_Hero_Slides_Control` en Sprint UX-4, Entregable UX-4.2
│   ├── quote-form.php                 ✅
│   ├── seo.php                        ✅
│   ├── widgets.php                    ✅ Sprint 7, Entregable 7.1
│   ├── home-builder.php               ✅ 🆕 Sprint UX-1, Entregable UX-1.1 — registro central de secciones del Home. 🔧 Sprint UX-2 (UX-2.1 y UX-2.2): solo comentarios actualizados (team/clients/faq ya tienen template-part), sin cambio de lógica en ningún Entregable. 🔧 Sprint UX-6, Entregable UX-6.2 (aprobado — D-060, D-062): nueva función ce_construction_get_home_section_args(), aditiva, única fuente de $args para front-page.php y el shortcode [ce_section]
│   └── section-shortcode.php          ✅ 🆕 Sprint UX-6, Entregable UX-6.2 (aprobado — D-060, D-062) — registra [ce_section key="..."], resuelve contra ce_construction_home_sections() y delega en get_template_part(), independiente del Home Builder activo. Cargado desde functions.php ($modules)
│
├── template-parts/                    ✅ (24 archivos — 🆕 Sprint UX-2 COMPLETADO: UX-2.1 team.php/clients.php (D-047); UX-2.2 faq.php/content-faq-accordion.php (D-048), partial de ítem compartido con single-servicio.php. 🔧 Sprint UX-4: hero.php — tipo imagen/video/overlay (UX-4.1, D-054) + modo slider (UX-4.2, D-055))
│
└── assets/
    ├── css/main.css                   ✅ 27 secciones — 🔧 Sprint UX-4: sección 26 (UX-4.1, hero video/overlay) + sección 27 (UX-4.2, hero slider), ambas 100% aditivas. 🔧 Sprint UX-6, Entregable UX-6.3 (aprobado — D-061, D-062): nueva clase .ce-content-breakout, 100% aditiva — .ce-max-w-content sin modificar
    ├── js/main.js                     ✅ 🔧 Sprint UX-4, Entregable UX-4.2 — `createSliderController()` (fábrica compartida) extraída de `ModuleTestimonialSlider` (refactorizado, sin cambio de comportamiento) + `ModuleHeroSlider` nuevo (D-055)
    ├── js/admin-home-builder.js       ✅ Sprint UX-1, Entregable UX-1.2 — control custom del Home Builder (drag&drop, jquery-ui-sortable)
    ├── js/admin-hero-slides.js        ✅ 🆕 Sprint UX-4, Entregable UX-4.2 — control custom `ce_hero_slides` (añadir/quitar/reordenar por botones, sin Sortable — D-055)
    └── img/                           ⬜ Vacía — pendiente de imágenes reales del cliente (no requerida para screenshot.png, que es un archivo independiente en la raíz del tema)
```

---

### Verificación de cierre de etapa (Sprint 7, Entregable 7.4 — screenshot.png)

Se agregó `screenshot.png` en la raíz del tema (único archivo nuevo de este Entregable). No requiere registro ni referencia en ningún archivo PHP — WordPress lo detecta automáticamente por convención de nombre y ubicación. Cero cambios en cualquier otro archivo del árbol. Con este archivo, **los 4 Entregables del Sprint 7 quedan desarrollados**; el Sprint se considera completado formalmente al recibir la aprobación explícita de este último Entregable (ver `DECISIONS.md` D-038).
