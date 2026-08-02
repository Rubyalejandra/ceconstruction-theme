# CE Construction — DECISIONS.md

> Registro formal y acumulativo de decisiones arquitectónicas del proyecto.
> No se elimina ni se reescribe una decisión ya tomada: si cambia, se agrega una nueva entrada que referencia a la anterior.

---

### D-001 — Nonces independientes por módulo, no un nonce global
- **Fecha:** Módulo Backend inicial (CPTs, Customizer, Metaboxes, Formulario)
- **Problema:** Un tema con múltiples formularios administrativos (metaboxes de 5 CPTs + formulario público de cotización) necesita protección CSRF consistente.
- **Solución elegida:** Cada formulario/metabox usa su propio nonce con una acción (`action`) específica y descriptiva: `ce_save_servicio_meta`, `ce_save_proyecto_meta`, `ce_save_testimonio_meta`, `ce_save_equipo_meta`, `ce_save_cliente_meta`, `ce_quote_form_action`.
- **Alternativas descartadas:** Un único nonce global reutilizado en todos los formularios del admin.
- **Motivo:** Un nonce por acción específica sigue el principio de menor privilegio recomendado por el Codex de WordPress; limita el radio de impacto si un nonce se filtra o se reutiliza incorrectamente.
- **Impacto en el proyecto:** Cada nuevo metabox o formulario futuro debe declarar su propio par `wp_nonce_field()` / `wp_verify_nonce()` con acción única. Documentado en `inc/meta-boxes.php` y `inc/quote-form.php`.

---

### D-002 — CPT `cotizacion` no público pero administrable
- **Fecha:** Módulo Formulario de Cotización (backend)
- **Problema:** El requisito pedía que las cotizaciones recibidas por el formulario fueran "administrables" desde el panel, pero no deben ser accesibles públicamente como contenido del sitio (no tienen archivo/single en el frontend).
- **Solución elegida:** CPT `cotizacion` con `public => false`, `show_ui => true`, `show_in_menu => true`, y `capabilities['create_posts'] => 'do_not_allow'` para impedir creación manual accidental desde wp-admin (solo se crean vía `wp_insert_post()` en el handler AJAX).
- **Alternativas descartadas:**
  1. Guardar las cotizaciones únicamente como fila en una tabla custom de la base de datos (`$wpdb`).
  2. Usar `wp_mail()` únicamente, sin persistencia en BD.
- **Motivo:** Un CPT reutiliza la UI de administración nativa de WordPress (listado, columnas, búsqueda, papelera) sin necesidad de construir una pantalla de admin custom, cumpliendo "administrable" con el menor esfuerzo y máxima compatibilidad con backups/exportación estándar de WP.
- **Impacto en el proyecto:** Si en el futuro se requiere exportar cotizaciones a CSV o integrarlas con un CRM, se puede hacer vía `WP_Query` sobre este CPT sin cambios estructurales.

---

### D-003 — Secciones del home condicionadas a la existencia de contenido
- **Fecha:** Módulo Header/Footer/Front Page
- **Problema:** En una instalación limpia del tema (sin contenido cargado aún), secciones como Servicios, Proyectos, Testimonios o Galería se renderizarían vacías, dando una mala primera impresión.
- **Solución elegida:** Función `ce_cpt_has_posts( $post_type )` en `inc/helpers.php`, con caché estática por request, usada como guardia (`return;` temprano) al inicio de cada template-part dependiente de un CPT.
- **Alternativas descartadas:**
  1. Mostrar la sección siempre con un mensaje "Próximamente".
  2. Cargar contenido de ejemplo (dummy data) hardcodeado en PHP.
- **Motivo:** Ocultar la sección es más profesional que mostrar un placeholder vacío o contenido falso que el cliente tendría que recordar borrar. Además evita queries innecesarias si ya sabemos que no hay posts.
- **Impacto en el proyecto:** Cualquier nuevo template-part que dependa de un CPT debe usar este mismo patrón (`if ( ! ce_cpt_has_posts( 'xxx' ) ) { return; }`) para mantener consistencia.

---

### D-004 — SEO propio con auto-desactivación si existe un plugin dedicado
- **Fecha:** Módulo SEO backend
- **Problema:** El brief pide meta tags, Open Graph, Schema y breadcrumbs "nativos" del tema, pero muchos clientes de WordPress terminan instalando Yoast SEO o RankMath, lo que causaría metatags y JSON-LD duplicados.
- **Solución elegida:** `ce_construction_seo_enabled()` verifica `defined('WPSEO_VERSION')` o `class_exists('RankMath')` antes de imprimir cualquier salida SEO propia del tema.
- **Alternativas descartadas:** No incluir SEO propio y delegar 100% a un plugin externo (fuera del alcance pedido explícitamente en el brief, que sí pide SEO propio del tema).
- **Motivo:** Cumple el requisito del brief sin generar conflictos si el cliente decide instalar un plugin SEO más adelante.
- **Impacto en el proyecto:** Si se agrega soporte para otro plugin SEO popular (p. ej. All in One SEO, SEOPress), solo hay que extender esta única función de verificación.

---

### D-005 — Formulario de cotización 100% dependiente de AJAX/JS (sin fallback sin-JS)
- **Fecha:** Módulo Header/Footer/Front Page (markup) — backend ya existía desde el módulo de Formulario
- **Problema:** El formulario podría implementarse con un `<form method="post">` tradicional (con recarga de página) además del envío AJAX, para soportar visitantes sin JavaScript.
- **Solución elegida:** El `<form id="ce-quote-form">` no tiene `action`/`method` de envío tradicional; toda la lógica de envío ocurre vía `fetch()` en `ModuleQuoteForm` (assets/js/main.js), consumiendo la acción AJAX `ce_submit_quote` ya expuesta en `inc/quote-form.php`.
- **Alternativas descartadas:** Formulario híbrido (funciona con y sin JS mediante `admin-post.php` + redirect).
- **Motivo:** Reduce la complejidad del handler PHP (un solo camino de respuesta, siempre JSON) y del markup; la inmensa mayoría de visitantes de un sitio corporativo moderno tienen JS habilitado.
- **Impacto en el proyecto:** Riesgo aceptado y documentado en `PROJECT_STATUS.md` → Riesgos. Si el cliente reporta usuarios sin JS (poco común), se deberá añadir un segundo camino de envío vía `admin-post.php`.

---

### D-006 — Helpers reutilizables creados antes de lo planeado originalmente
- **Fecha:** Módulo Header/Footer/Front Page
- **Problema:** `inc/helpers.php` estaba en el inventario original como módulo de prioridad baja/media, pero `header.php`, `footer.php` y varios template-parts requerían funciones concretas (`ce_render_social_icons()`, `ce_get_whatsapp_number()`, `ce_get_gallery_ids()`, etc.) para poder compilarse sin errores fatales.
- **Solución elegida:** Se adelantó la creación completa de `inc/helpers.php` como parte del módulo Header/Footer/Front Page, en vez de dejarlo como bloque de código pendiente o usar funciones inline duplicadas en cada archivo.
- **Alternativas descartadas:** Duplicar lógica pequeña directamente en `header.php`/`footer.php` y posponer la centralización.
- **Motivo:** Evitar duplicación de código (DRY) y errores de "función no definida" (fatal error de PHP) al cargar el header.
- **Impacto en el proyecto:** El orden de módulos documentado en `PROJECT_STATUS.md` se ajustó para reflejar que este archivo ya está completo, no pendiente.

---

### D-007 — Bloques flotantes y modales integrados en `footer.php` en vez de template-parts separados
- **Fecha:** Módulo Header/Footer/Front Page
- **Problema:** El brief sugería componentes reutilizables como "Modals" y ventanas flotantes de forma genérica, lo que podría interpretarse como archivos `template-parts/floating-buttons.php` y `template-parts/modals.php` independientes.
- **Solución elegida:** Ese marcado se colocó directamente dentro de `footer.php`, ya que debe existir en **todas** las páginas del sitio (no solo el home) y `footer.php` ya se carga globalmente vía `get_footer()`.
- **Alternativas descartadas:** Crear los dos archivos separados y hacer `get_template_part()` de ellos dentro de `footer.php`.
- **Motivo:** Menor indirección para un bloque de marcado pequeño, estático y de alcance global; un archivo adicional no aporta valor real de mantenibilidad en este caso.
- **Impacto en el proyecto:** Documentado como desviación menor en `TREE.md`. **Reversible sin riesgo** si el cliente prefiere la separación estricta en archivos independientes — queda pendiente de aprobación explícita.

---

### D-008 — Bugfix de `ModuleModals` documentado como decisión, no como silent fix
- **Fecha:** Módulo Header/Footer/Front Page
- **Problema:** Al construir `footer.php`, los modales de éxito/error incluyen dos botones con la clase `.ce-modal__close` (la X y el botón de acción "Entendido"/"Cerrar"), pero `ModuleModals.init()` en `main.js` (entregado en el módulo anterior) usaba `querySelector` (singular), que solo enlaza el primer match.
- **Solución elegida:** Cambiar `querySelector` por `querySelectorAll` + `forEach` para enlazar todos los botones de cierre dentro de cada modal.
- **Alternativas descartadas:** Renombrar la clase del botón de acción para evitar el conflicto de selector (mantendría el bug latente para cualquier futuro modal con el mismo patrón de 2 botones de cierre).
- **Motivo:** Corregir la causa raíz (el selector) en vez de evitar el síntoma, para que el patrón sea seguro de reutilizar en futuros modales.
- **Impacto en el proyecto:** Cambio mínimo y localizado en `assets/js/main.js`. Documentado explícitamente para cumplir la regla del proyecto de "no reemplazar archivos ya implementados salvo que exista un error", indicando claramente qué cambió y por qué.

### D-009 — `template-parts/page-hero.php` como componente reutilizable, no específico de Servicios
- **Fecha:** Sprint 3 (Módulo Servicios)
- **Problema:** El brief pedía un "hero interno" para archive/single de Servicios. El hero de portada (`template-parts/hero.php`) ya existente está fuertemente acoplado a los theme_mods del Customizer (imagen de fondo, título, subtítulo, 2 botones específicos) y no es apto para reutilizarse en páginas de contenido.
- **Solución elegida:** Crear un nuevo componente `template-parts/page-hero.php`, parametrizable vía el mecanismo nativo `$args` de `get_template_part()` (disponible desde WP 5.5), que recibe `eyebrow`, `title`, `subtitle` e `image_id` como argumentos.
- **Alternativas descartadas:**
  1. Reutilizar `template-parts/hero.php` agregando condicionales internos para distinguir "modo home" vs "modo interno".
  2. Duplicar el markup del hero directamente dentro de `archive-servicio.php` y `single-servicio.php` sin componentizar.
- **Motivo:** Modificar `template-parts/hero.php` violaría la regla explícita de "no reescribir módulos ya implementados". Duplicar markup rompería DRY y complicaría el próximo módulo de Proyectos, que necesitará el mismo patrón.
- **Impacto en el proyecto:** El módulo de Proyectos (próximo sprint) debe reutilizar `template-parts/page-hero.php` en vez de crear un tercer componente de hero.

---

### D-010 — Relación Servicio↔Proyecto por coincidencia de nombre de taxonomía (heurística)
- **Fecha:** Sprint 3 (Módulo Servicios)
- **Problema:** El brief pide mostrar "Proyectos relacionados" dentro del single de un Servicio, pero el modelo de datos actual (definido en `inc/cpt-servicios.php` e `inc/cpt-proyectos.php`, ya implementados) no tiene ningún campo o taxonomía compartida que vincule explícitamente un `servicio` con uno o más `proyecto`.
- **Solución elegida:** `ce_get_related_projects()` (nueva, en `inc/helpers.php`) compara los nombres de los términos de `categoria_servicio` del servicio actual contra los nombres de términos de `categoria_proyecto`, y si coinciden, filtra proyectos por esos términos. Si no hay coincidencia o no hay suficientes resultados, hace fallback a los proyectos más recientes.
- **Alternativas descartadas:**
  1. Agregar un nuevo campo relacional (metabox tipo "selector de proyectos") al CPT Servicio — implicaría modificar `inc/meta-boxes.php`, ya implementado, violando la regla de no reescribir módulos existentes sin autorización explícita.
  2. Crear una taxonomía compartida nueva entre ambos CPTs — mismo problema: requeriría tocar `inc/cpt-servicios.php` e `inc/cpt-proyectos.php`.
  3. No mostrar proyectos relacionados en absoluto.
- **Motivo:** Cumplir el requisito del brief ("proyectos relacionados") sin modificar el modelo de datos ya aprobado en sprints anteriores. Es una solución de compromiso, explícitamente documentada como heurística y no como relación de datos verdadera.
- **Impacto en el proyecto:** Si el cliente aprueba en un sprint futuro modificar `inc/meta-boxes.php` para agregar un campo relacional explícito (ej. un multi-select de proyectos), esta función puede reemplazarse sin afectar el resto del código — la firma `ce_get_related_projects( $service_id, $limit )` se mantendría igual, solo cambiaría la lógica interna. Riesgo documentado en `PROJECT_STATUS.md` sección 6.

---

### D-011 — Extensión aditiva de `inc/helpers.php` (no reescritura)
- **Fecha:** Sprint 3 (Módulo Servicios)
- **Problema:** El Sprint 3 necesitaba nuevas funciones de relación (`ce_get_related_services()`, `ce_get_related_projects()`) que lógicamente pertenecen a `inc/helpers.php`, un archivo ya implementado y aprobado en el sprint anterior.
- **Solución elegida:** Las funciones nuevas se agregaron al final del archivo, después de la última función existente (`ce_cpt_has_posts()`), sin modificar ni reordenar ninguna línea previa. Se agregó un bloque de comentario explícito indicando que la sección fue "Añadida en Sprint 3".
- **Alternativas descartadas:** Crear un archivo nuevo `inc/related.php` exclusivamente para estas 2 funciones.
- **Motivo:** Ambas funciones son helpers de la misma naturaleza que las ya existentes en el archivo (consultas reutilizables sobre CPTs); crear un archivo nuevo solo por esas 2 funciones fragmentaría innecesariamente la organización ya establecida en `functions.php` → `ce_construction_require_modules()`.
- **Impacto en el proyecto:** Ningún archivo previo fue alterado; el cambio es 100% aditivo y de bajo riesgo, verificado con balance de llaves/paréntesis antes de la entrega.

---

### D-012 — Extensión aditiva de `inc/seo.php` para Schema.org de Servicio (no reescritura)
- **Fecha:** Sprint 3 (Módulo Servicios)
- **Problema:** El brief pide "schema.org correspondiente" para el módulo de Servicios, pero `inc/seo.php` (ya implementado) solo tenía Schema `GeneralContractor` para la portada y una función de breadcrumbs HTML genérica sin JSON-LD.
- **Solución elegida:** Se agregó `ce_construction_schema_service()` al final de `inc/seo.php`, enganchada a `wp_head` de forma independiente, que emite dos bloques `<script type="application/ld+json">`: uno de tipo `Service` (con `provider`, `serviceType`, imagen) y otro `BreadcrumbList` con la ruta Inicio → Servicios → [Nombre del servicio], calculada localmente dentro de esta nueva función (sin modificar `ce_construction_breadcrumbs()`, la función HTML existente).
- **Alternativas descartadas:** Refactorizar `ce_construction_breadcrumbs()` para que ambas (HTML y JSON-LD) compartan un helper común de construcción del array de items.
- **Motivo:** Refactorizar la función existente implicaría "reescribir" código ya aprobado. Se aceptó una pequeña duplicación de lógica (la ruta de breadcrumbs se calcula dos veces, una para HTML y otra para JSON-LD) a cambio de no tocar código previamente entregado.
- **Impacto en el proyecto:** Si en un sprint futuro se autoriza refactorizar `inc/seo.php` para unificar ambas rutas de breadcrumbs en un solo helper, es un cambio de bajo riesgo y aislado a ese archivo. Mientras tanto, cualquier cambio a la estructura de breadcrumbs debe replicarse manualmente en ambas funciones.

---

### D-013 — FAQ relacionadas sin filtro por servicio (alcance aceptado)
- **Fecha:** Sprint 3 (Módulo Servicios)
- **Problema:** El brief pide "FAQ relacionados (si existen)" en el single de Servicio, pero el CPT `ce_faq` (ya implementado en un sprint anterior) no tiene ninguna taxonomía ni campo que lo vincule a un servicio específico.
- **Solución elegida:** La sección de FAQ en `single-servicio.php` muestra las 5 preguntas frecuentes más recientes del sitio de forma general, envueltas en un componente accordion accesible, en vez de intentar filtrarlas por relación inexistente.
- **Alternativas descartadas:**
  1. Agregar una taxonomía o campo relacional a `ce_faq` — requeriría modificar `inc/cpt-faq.php`, ya implementado.
  2. Ocultar la sección de FAQ por completo en el single de Servicio.
- **Motivo:** Mostrar FAQ generales aporta valor real al visitante (respuestas a dudas comunes) sin necesidad de modificar un CPT ya aprobado. Ocultar la sección por completo habría incumplido el requisito del brief sin necesidad.
- **Impacto en el proyecto:** Documentado como limitación conocida (no bug) en `PROJECT_STATUS.md` sección 6 y `TODO.md`. Si se requiere una relación real en el futuro, se puede añadir una taxonomía compartida `categoria_servicio` también a `ce_faq` sin romper lo ya construido.

### D-014 — Schema.org `Project` implementado como `@type` múltiple `["CreativeWork", "Project"]`
- **Fecha:** Sprint 4 (Módulo Proyectos)
- **Problema:** El brief pide explícitamente "Schema.org Project" para el single de Proyecto, pero el vocabulario estándar de Schema.org **no define ningún tipo llamado `Project`**. Usar un `@type` inventado/no estándar en solitario reduciría la elegibilidad del contenido para resultados enriquecidos en buscadores, que validan contra tipos reconocidos.
- **Solución elegida:** `ce_construction_schema_project()` (en `inc/seo.php`) usa `"@type": ["CreativeWork", "Project"]`. JSON-LD permite que `@type` sea un array de strings; `CreativeWork` es un tipo válido y ampliamente reconocido para portafolios de trabajos/proyectos, mientras que `Project` se conserva como segundo valor para honrar literalmente el requisito del brief.
- **Alternativas descartadas:**
  1. Usar únicamente `"@type": "Project"` — técnicamente inválido contra el vocabulario oficial de schema.org, sin garantía de reconocimiento por parte de Google/Bing.
  2. Usar únicamente `"@type": "CreativeWork"` — cumple el estándar pero ignora la palabra literal "Project" que pidió el cliente.
  3. Usar `"@type": "Product"` (otro tipo a veces usado para portafolios) — se descartó porque `Product` implica semánticamente un artículo comercializable/comprable, lo cual no representa con precisión un proyecto de construcción ya ejecutado.
- **Motivo:** El array de tipos es una técnica estándar y válida en JSON-LD (no es un hack); permite satisfacer simultáneamente la validez ante motores de búsqueda y el requisito literal del brief.
- **Impacto en el proyecto:** Si en el futuro se determina que el campo `Project` causa advertencias en el validador de Schema.org de Google, se puede remover ese segundo valor del array sin ningún otro cambio — el resto del schema (`creator`, `sourceOrganization`, `contentLocation`, `dateCreated`, `image`, `keywords`) permanece intacto y válido con solo `CreativeWork`.

---

### D-015 — Relación inversa Proyecto→Servicio simétrica a D-010, sin nueva heurística
- **Fecha:** Sprint 4 (Módulo Proyectos)
- **Problema:** El brief pide "Servicios relacionados" en el single de Proyecto — el mismo problema de fondo que D-010 (Sprint 3), pero en dirección inversa: no existe campo relacional explícito entre `proyecto` y `servicio`.
- **Solución elegida:** `ce_get_related_services_for_project()` (nueva, en `inc/helpers.php`) replica exactamente la misma heurística de `ce_get_related_projects()` (coincidencia de nombre entre `categoria_proyecto` y `categoria_servicio`, con fallback a los más recientes), en dirección inversa.
- **Alternativas descartadas:** Las mismas que en D-010 (campo relacional explícito o taxonomía compartida), descartadas por la misma razón: requerirían modificar `inc/meta-boxes.php`, `inc/cpt-servicios.php` o `inc/cpt-proyectos.php`, todos ya implementados y fuera del alcance autorizado de este sprint.
- **Motivo:** Consistencia arquitectónica — dos funciones que resuelven el mismo tipo de problema en direcciones opuestas deben usar la misma estrategia, no dos enfoques distintos, para que el comportamiento sea predecible y fácil de razonar para cualquier desarrollador futuro.
- **Impacto en el proyecto:** Si en un sprint futuro se autoriza agregar un campo relacional explícito, ambas funciones (`ce_get_related_projects()` y `ce_get_related_services_for_project()`) deberían migrarse juntas, en el mismo sprint, para no dejar el proyecto con una relación "mitad heurística, mitad explícita".

---

### D-016 — Reutilización deliberada de clases CSS con nombre "de Servicios" en el módulo de Proyectos
- **Fecha:** Sprint 4 (Módulo Proyectos)
- **Problema:** `single-proyecto.php` necesita navegación anterior/siguiente y tipografía de cuerpo de contenido, estructuralmente idénticas a las ya construidas para `single-servicio.php` en el Sprint 3 (`.ce-service-nav`, `.ce-service-content`), pese a que el nombre de esas clases sugiere que son exclusivas de Servicios.
- **Solución elegida:** Reutilizar `.ce-service-nav` y `.ce-service-content` tal cual en `single-proyecto.php`, sin crear `.ce-project-nav`/`.ce-project-content` duplicadas en `assets/css/main.css`.
- **Alternativas descartadas:** Duplicar las mismas ~40 líneas de CSS bajo un nombre distinto solo para que el namespace de la clase coincida semánticamente con "proyecto".
- **Motivo:** El brief pide explícitamente evitar código duplicado ("No generes código duplicado"). Ambas reglas de la regla activa del proyecto son igual de importantes: la instrucción de no duplicar código pesa más que la pureza semántica del nombre de una clase CSS puramente estructural (navegación prev/next, tipografía de cuerpo) que no tiene ningún significado visual específico de "Servicios".
- **Impacto en el proyecto:** Cambio de bajo riesgo. Si en un sprint de refactor futuro se autoriza, estas clases podrían renombrarse a un nombre neutro (ej. `.ce-content-nav`, `.ce-entry-content`) reutilizable explícitamente por ambos módulos y por los futuros de Blog — ver también QA-020 en `QA_REPORT.md`, que ya señalaba una inconsistencia BEM similar en otro contexto del proyecto.
