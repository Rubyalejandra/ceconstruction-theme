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

### D-017 — Corrección QA-001 + QA-002: validación de archivo por extensión real y registro como attachment
- **Fecha:** Sprint 5, Fase 1 (corrección de QA_REPORT.md)
- **Problema:** `inc/quote-form.php` validaba el adjunto contra el MIME falsificable enviado por el cliente (`$_FILES[...]['type']`) y aceptaba cualquier extensión de la lista global de WordPress, no el whitelist real (QA-001, crítico). Además, el archivo subido nunca se registraba como attachment de WordPress, quedando huérfano en disco sin ciclo de vida (QA-002, alto).
- **Solución elegida:** Reemplazar `wp_check_filetype()` por `wp_check_filetype_and_ext()` (inspecciona contenido real para imágenes) y validar `$file_type_and_ext['ext']` contra `array('pdf','jpg','jpeg','png','webp')` explícito. Tras crear el post `cotizacion`, registrar el archivo con `wp_insert_attachment()` + `wp_generate_attachment_metadata()`, vinculado vía `post_parent`.
- **Alternativas descartadas:** Mantener la validación por MIME de cliente añadiendo solo una lista negra de extensiones peligrosas (rechazado: listas negras son inherentemente incompletas frente a listas blancas).
- **Motivo:** Cerrar una vulnerabilidad de validación real sin cambiar el contrato del formulario (mismos campos, misma respuesta JSON).
- **Impacto:** ~40 líneas modificadas en una función ya existente, en el mismo bloque de código para ambos hallazgos (estaban acoplados). No afecta al frontend (JS/markup del formulario intactos).

### D-018 — Corrección QA-003: cron de retención/purga de cotizaciones
- **Fecha:** Sprint 5, Fase 1
- **Problema:** Retención indefinida de datos personales (nombre, correo, teléfono, mensaje) sin política de expiración — riesgo de cumplimiento (GDPR/Ley 1581/2012).
- **Solución elegida:** Cron diario (`wp_schedule_event`, hook `ce_construction_quote_cleanup_event`) que purga cotizaciones más antiguas que `ce_construction_quote_retention_days()` (365 días por defecto, filtrable vía `add_filter`). Programado en `after_switch_theme`, cancelado en `switch_theme`.
- **Alternativas descartadas:** Fijar un plazo hardcodeado sin filtro (rechazado: la retención real es una decisión de negocio del cliente, no del código).
- **Motivo:** Dar una solución técnica funcional sin usurpar una decisión que corresponde al cliente definir con su asesor legal/de cumplimiento.
- **Impacto:** Nuevas funciones aditivas al final de `inc/quote-form.php`. Requiere que el cliente confirme o ajuste el plazo de 365 días por defecto.

### D-019 — Corrección QA-004: rate-limiting por IP vía transient
- **Fecha:** Sprint 5, Fase 1
- **Problema:** El honeypot era la única defensa anti-abuso del formulario público; un script simple podía enviar solicitudes ilimitadas.
- **Solución elegida:** Máximo 3 envíos cada 10 minutos por IP, usando `get_transient()`/`set_transient()` con clave `md5(REMOTE_ADDR)`. Sin dependencias externas ni tablas nuevas.
- **Alternativas descartadas:** Integrar un CAPTCHA de terceros (rechazado por la regla del proyecto de "no agregar dependencias externas sin autorización").
- **Motivo:** Mitigación proporcional al riesgo actual sin introducir servicios externos ni fricción para usuarios legítimos.
- **Impacto:** ~10 líneas insertadas entre el honeypot y la sanitización de campos. Limitación conocida: en redes con IP compartida (NAT corporativo) varios usuarios legítimos podrían compartir el límite — aceptado como tradeoff razonable.

### D-020 — Corrección QA-005: variable de color dedicada para texto sobre fondo claro
- **Fecha:** Sprint 5, Fase 1
- **Problema:** `--ce-color-secondary` sobre blanco medía 2.67:1 (verificado numéricamente), por debajo del mínimo WCAG AA de 4.5:1, en `.ce-eyebrow` y varios estados hover de enlaces.
- **Solución elegida:** Nueva variable `--ce-color-secondary-text: #9C5F16` (verificada en 5.17:1 sobre blanco) aplicada únicamente a los selectores señalados en QA-005, mediante reglas añadidas al final de `main.css` con igual especificidad (la cascada CSS resuelve el override sin tocar las reglas originales).
- **Alternativas descartadas:** Cambiar `--ce-color-secondary` global (afectaría también los usos ya correctos sobre fondo oscuro, como `.ce-stat__number`, verificado en 5.48:1 sobre `--ce-color-primary`).
- **Motivo:** Corregir el problema real (contraste sobre fondo claro) sin degradar los casos donde el color original ya era válido.
- **Impacto:** Cambio puramente aditivo en CSS, cero riesgo de romper el layout.

### D-021 — Corrección QA-006: renderizado condicional del sidebar "Footer - Columna 1"
- **Fecha:** Sprint 5, Fase 1
- **Problema:** `footer-1` estaba registrado en `inc/setup.php` pero nunca se invocaba `dynamic_sidebar('footer-1')` en ningún archivo — funcionalidad de administración silenciosamente rota.
- **Solución elegida:** Renderizar `dynamic_sidebar('footer-1')` dentro de la columna "Enlaces" de `footer.php`, envuelto en `is_active_sidebar('footer-1')`, para no alterar el layout visual cuando esté vacío (comportamiento actual hoy, ya que ningún admin le ha asignado widgets).
- **Alternativas descartadas:** Eliminar el registro de `footer-1` en vez de usarlo (rechazado: elimina una funcionalidad ya expuesta en el panel, en vez de solo arreglarla).
- **Motivo:** Cumplir la promesa implícita de una funcionalidad ya visible en wp-admin, sin rediseñar el grid de 4 columnas del footer.
- **Impacto:** ~15 líneas insertadas en `footer.php`, condicionadas — invisibles hasta que un admin agregue widgets a esa columna.

### D-022 — Corrección QA-007: guardia `wp_is_post_revision()` en el guardado de metaboxes
- **Fecha:** Sprint 5, Fase 1
- **Problema:** `ce_construction_save_meta_boxes()` no excluía las revisiones de post que WordPress crea internamente, permitiendo escribir metadatos también sobre el ID de la revisión.
- **Solución elegida:** Añadir `if ( wp_is_post_revision( $post_id ) ) { return; }` justo después de la guardia de autosave existente, siguiendo el patrón estándar documentado por WordPress.
- **Alternativas descartadas:** Ninguna considerada — es el patrón canónico sin ambigüedad.
- **Impacto:** 1 línea + comentario, cero riesgo de regresión.

### D-023 — Corrección QA-008: sincronización de `CE_THEME_VERSION`
- **Fecha:** Sprint 5, Fase 1
- **Problema:** La constante y la cabecera de `style.css` seguían en `1.0.0` desde el Sprint 1, rompiendo el cache-busting de `main.css`/`main.js` en cada despliegue.
- **Solución elegida:** Sincronizar ambas a `0.4.1` (versión de esta corrección) con un comentario explícito indicando que debe actualizarse en cada versión que toque CSS/JS.
- **Alternativas descartadas:** Automatizar vía `wp_get_theme()->get('Version')` o `filemtime()` (documentado en `QA_REPORT.md` como mejora futura, no implementado aquí para no exceder el alcance de "corregir Críticos/Altos sin refactorizar").
- **Impacto:** 2 líneas (una por archivo). Resuelve también, como efecto colateral inevitable, QA-022 (Bajo, discrepancia de versión visible en wp-admin), ya que ambos hallazgos comparten la misma causa raíz y la misma línea de código.

### D-024 — Corrección QA-009: soporte `page-attributes` en CPT Servicio
- **Fecha:** Sprint 5, Fase 1
- **Problema:** `template-parts/services.php` ordena por `menu_order`, pero el CPT no soportaba `page-attributes`, por lo que ese campo no existía en el editor — el ordenamiento nunca fue configurable.
- **Solución elegida:** Añadir `'page-attributes'` al array `supports` de `register_post_type('servicio', ...)`.
- **Alternativas descartadas:** Cambiar `services.php` para ordenar por otro criterio (rechazado: `services.php` ya está aprobado y funcionando; el bug estaba en el registro del CPT, no en la plantilla).
- **Impacto:** 1 línea. Habilita el campo "Orden" nativo de WordPress sin ningún otro efecto secundario.

### D-025 — Sprint 5, Fase 3: `has_archive` habilitado para el CPT Cliente
- **Fecha:** Sprint 5, Fase 3 (Módulo Equipo y Clientes)
- **Problema:** El Sprint 5 pide `archive-clientes.php` como entregable funcional, pero `inc/cpt-clientes.php` (ya implementado en Sprint 1) registra `'has_archive' => false`, lo que hace que no exista ninguna URL amigable (`/clientes/`) que WordPress enrute a esa plantilla.
- **Solución elegida:** Cambiar `'has_archive'` a `true`. Se actualizó, como consecuencia directa y necesaria, la rama de breadcrumbs de `is_singular('cliente')` en `inc/seo.php` (que antes asumía explícitamente la ausencia de archivo) y se añadió la rama `is_post_type_archive('cliente')` que antes no existía porque era inalcanzable.
- **Alternativas descartadas:**
  1. Dejar `has_archive` en `false` y hacer que `archive-clientes.php` solo sea alcanzable vía `?post_type=cliente` (rechazado: URLs feas, incompatible con el objetivo del proyecto de ser una "plantilla profesional lista para producción").
  2. No crear `archive-clientes.php` y mostrar los clientes solo embebidos en otra página (rechazado: el Sprint 5 lo pide explícitamente como archivo entregable).
- **Motivo:** Sin este cambio, el archivo pedido explícitamente en el Sprint sería código muerto inalcanzable — violaría la regla del proyecto de que "todo archivo nuevo debe quedar completamente funcional".
- **Impacto:** Cambio de una línea en un archivo ya implementado, con su cambio acoplado correspondiente en `inc/seo.php` (documentado exactamente qué líneas cambian y por qué, según lo exigido por las reglas del proyecto para modificar archivos existentes). No afecta datos ya guardados ni el comportamiento de `single-clientes.php`, que ya funcionaba.

### D-026 — Sprint 5: alcance deliberadamente más ligero para Equipo y Clientes (sin CTA/sidebar/formulario)
- **Fecha:** Sprint 5, Fase 3
- **Problema:** A diferencia de los briefs de Sprint 3 (Servicios) y Sprint 4 (Proyectos), que listaban explícitamente CTA, formulario de cotización y sidebar opcional, el brief del Sprint 5 para Equipo y Clientes no menciona ninguno de esos elementos.
- **Solución elegida:** `archive-equipo.php`, `single-equipo.php`, `archive-clientes.php` y `single-clientes.php` se construyeron sin CTA, sin formulario de cotización integrado y sin sidebar — únicamente hero interno, contenido/metadatos propios del CPT, y (en Equipo) un enlace de vuelta al archivo.
- **Alternativas descartadas:** Replicar exactamente la misma estructura completa de Servicios/Proyectos "por consistencia visual" (rechazado: violaría la regla explícita de "no desarrolles funcionalidades fuera del Sprint solicitado").
- **Motivo:** Respetar el alcance literal de cada Sprint tal como el cliente lo definió, en vez de asumir que todos los módulos de contenido deben tener idéntica estructura.
- **Impacto:** Si en un sprint futuro el cliente pide agregar CTA/formulario a estas plantillas, es una adición de bajo riesgo (los componentes `template-parts/cta.php` y `template-parts/quote-form.php` ya existen y son reutilizables sin cambios).

### D-027 — Sprint 5: reconciliación de `content-cliente.php` con la convención CSS ya existente
- **Fecha:** Sprint 5, Fase 3
- **Problema:** Antes de la interrupción de la sesión anterior, `template-parts/content-cliente.php` se había creado usando clases (`.ce-client-card`, `.ce-client-card__logo`, etc.) distintas a las ya definidas en `assets/css/main.css` (`.ce-clients-grid__item`), generando un riesgo real de duplicación de CSS si se hubiera añadido un segundo set de clases equivalentes.
- **Solución elegida:** Sobrescribir `content-cliente.php` (nunca entregado ni aprobado por el cliente) para usar `.ce-clients-grid__item`, alineado con el CSS ya existente, en vez de duplicar estilos.
- **Alternativas descartadas:** Añadir las clases `.ce-client-card*` faltantes al CSS (rechazado: crearía dos sistemas de tarjetas de cliente visualmente equivalentes, violando la regla "no generes código duplicado").
- **Motivo:** Un archivo no entregado/aprobado puede corregirse sin restricción de "no reescribir"; se prioriza la convención ya establecida en CSS por ser la que ya tenía más desarrollo (incluía también el estilo de `single-cliente.php`).
- **Impacto:** Ninguno sobre código ya aprobado — `content-cliente.php` se entrega en su primera versión funcional real en esta misma continuación de sprint.

### D-028 — `index.php` diseñado como fallback genérico completo, no un placeholder
- **Fecha:** Entregable 6A
- **Problema:** `index.php` es la plantilla de respaldo final de WordPress. Sin `single.php`, `page.php`, `archive.php`, `search.php` ni `404.php` propios todavía, `index.php` recibiría en la práctica 4 contextos muy distintos (single de blog/página, archivos genéricos, búsqueda, 404), y las reglas del proyecto prohíben entregar archivos "placeholder" no funcionales.
- **Solución elegida:** Construir `index.php` con ramas condicionales explícitas (`is_search()`, `is_404()`, `is_archive()`, `is_singular()`) que cubren cada uno de esos 4 contextos con una experiencia visual completa (hero/título contextual, tarjetas, paginación, o mensaje de error apropiado), en vez de un `if (have_posts())` genérico único.
- **Alternativas descartadas:**
  1. Un `index.php` mínimo de un solo loop genérico sin distinguir contextos (rechazado: no seria "completamente funcional" para 404/búsqueda, que necesitan mensajes y UI distintos a un archivo normal).
  2. Esperar a construir `single.php`/`archive.php`/`search.php`/`404.php` en el mismo entregable (rechazado: excede explícitamente el alcance de "desarrolla únicamente index.php").
- **Motivo:** Cumplir la regla del proyecto de que todo archivo nuevo debe quedar completamente funcional, sin exceder el alcance de este entregable puntual.
- **Impacto:** Cuando se construyan `single.php`/`page.php`/`archive.php`/`search.php`/`404.php` en un sprint futuro, WordPress les dará prioridad automática sobre `index.php` para sus contextos específicos (comportamiento nativo de la Template Hierarchy) — no se requiere ningún cambio en `index.php` para que eso ocurra, simplemente dejará de ser invocado en esos casos y seguirá existiendo como el fallback final que WordPress exige.

### D-029 — Corrección del bug preexistente `.ce-mt-6`/`.ce-mb-6` (detectado, no introducido, en este entregable)
- **Fecha:** Entregable 6A
- **Problema:** Al verificar las clases CSS usadas por `index.php` contra `assets/css/main.css`, se detectó que `.ce-mt-6` y `.ce-mb-6` ya estaban en uso desde el Sprint 3 en 10 archivos aprobados (`single-servicio.php`, `single-proyecto.php`, `archive-servicio.php`, `archive-proyecto.php`, `archive-equipo.php`, `archive-clientes.php`, `single-clientes.php`, `single-equipo.php`), pero nunca se habían definido — el margen correspondiente simplemente no se aplicaba en ninguno de esos casos, un defecto visual silencioso no detectado en ninguna auditoría previa (incluyendo `QA_REPORT.md`).
- **Solución elegida:** Añadir `.ce-mt-6{margin-top:var(--ce-space-6);}` y `.ce-mb-6{margin-bottom:var(--ce-space-6);}` junto a la familia `.ce-mt-2` a `.ce-mt-5`/`.ce-mb-2` a `.ce-mb-5` ya existente, siguiendo exactamente el mismo patrón numérico y usando el design token `--ce-space-6` (ya definido, ya usado en otras reglas del sistema).
- **Alternativas descartadas:** Ninguna — es la corrección mínima y obvia dado el patrón ya establecido; no había ambigüedad de diseño que resolver.
- **Motivo:** `index.php` (este entregable) también necesita estas clases; corregirlas de paso beneficia retroactivamente a los 10 archivos que ya las usaban sin saber que no tenían efecto.
- **Impacto:** Cambio puramente aditivo (2 líneas), cero riesgo de romper nada. Mejora visual inmediata y automática en los 10 archivos ya aprobados que usaban estas clases, sin necesidad de tocarlos.

### D-030 — Adopción de la metodología permanente "Gestión automática de Sprints y Entregables"
- **Fecha:** Tras el Entregable 6A
- **Problema:** El Sprint 5 se interrumpió por límite de mensajes a mitad de desarrollo (ver sección de continuidad en `PROJECT_STATUS.md`/`CHANGELOG.md` v0.5.0), requiriendo una sesión adicional de verificación y continuación. Aunque el trabajo se recuperó sin pérdida ni duplicación gracias a la disciplina de verificación ya establecida, la interrupción en sí fue evitable si el trabajo se hubiera planificado en unidades más pequeñas desde el inicio.
- **Solución elegida:** Adoptar como regla permanente del proyecto que todo Sprint se divida automáticamente en **Entregables** — unidades funcionales completas, terminables en una sola sesión, con un flujo de cierre obligatorio (verificación de sintaxis/dependencias, actualización de documentación, marcado como Completado, propuesta del siguiente Entregable y su prompt de continuación) antes de detenerse a esperar aprobación. La calidad del código nunca se reduce por reducir el alcance de un Entregable.
- **Alternativas descartadas:**
  1. Mantener el enfoque de "un Sprint = una sola entrega monolítica" y solo dividir reactivamente si ocurre una interrupción (rechazado: es exactamente el patrón que ya falló una vez en el Sprint 5).
  2. Definir un límite arbitrario de líneas/archivos por entrega sin atender a si constituye una unidad funcional completa (rechazado: podría forzar a dejar un archivo a medias, violando la regla explícita de "nunca dejar un archivo parcialmente implementado").
- **Motivo:** Formalizar como práctica preventiva y permanente lo que ya se venía aplicando de forma implícita e intuitiva en los últimos dos entregables del proyecto (Sprint 5 dividido en 3 fases; Entregable 6A tratado como unidad independiente de `index.php`), en vez de depender de que una futura interrupción vuelva a forzar ese mismo patrón de forma reactiva.
- **Impacto:** A partir de este punto, todo Sprint del proyecto (empezando por el que continúe después de esta decisión) se planifica y comunica ya dividido en Entregables desde su inicio, con su propio flujo de cierre documentado en `HANDOFF.md` sección 16 y `PROJECT_STATUS.md` sección 13. No afecta ningún código ya entregado — es una decisión de proceso, no de arquitectura de software.

### D-031 — `page.php` reutiliza el hero interno sin condicionarlo a la presencia de imagen destacada
- **Fecha:** Sprint 6B, Entregable 6B.1
- **Problema:** A diferencia de los CPTs de contenido (Servicios, Proyectos, etc.), las páginas genéricas de WordPress varían mucho en propósito (una página "Política de Privacidad" no necesita el mismo tratamiento visual que una página "Nuestra Historia"). Había que decidir si `page.php` siempre muestra el hero interno (`template-parts/page-hero.php`) o solo condicionalmente.
- **Solución elegida:** Mostrar siempre el hero interno (con imagen destacada si existe, o el degradado de fondo por defecto si no), usando el excerpt de la página como subtítulo solo si existe (`has_excerpt()`).
- **Alternativas descartadas:** Omitir el hero para páginas sin imagen destacada, mostrando solo un `<h1>` simple (rechazado: generaría dos experiencias visuales distintas para "página con imagen" vs. "página sin imagen", inconsistente con el resto del sitio, que siempre muestra el mismo componente de hero interno independientemente de si hay imagen).
- **Motivo:** Consistencia visual total en todo el tema — el mismo componente (`page-hero.php`) ya maneja correctamente el caso sin imagen (fondo con gradiente sobre `--ce-color-primary`), por lo que no hay necesidad de una rama condicional adicional en `page.php`.
- **Impacto:** Ninguno sobre código existente; es una decisión de uso de un componente ya aprobado (D-009), no una modificación de ese componente.

### D-032 — `single.php` y `comments.php` desarrollados como un único Entregable acoplado
- **Fecha:** Sprint 6B, Entregable 6B.2
- **Problema:** Conforme a la metodología permanente de Entregables (D-030), cabía la duda de si `single.php` y `comments.php` debían tratarse como dos Entregables separados o uno solo.
- **Solución elegida:** Tratarlos como un único Entregable (6B.2), ya que `single.php` invoca `comments_template()` y, sin `comments.php`, los comentarios de blog seguirían mostrando el fallback de compatibilidad nativo de WordPress — dejando el Entregable incompleto en la práctica pese a que `single.php` en sí mismo funcionaría.
- **Alternativas descartadas:** Entregar `single.php` solo y posponer `comments.php` a un Entregable 6B.2b separado (rechazado: violaría la regla de la metodología permanente de que cada Entregable debe ser una "unidad funcional completa", no un archivo técnicamente funcional pero con una experiencia de usuario a medias en un aspecto tan visible como los comentarios).
- **Motivo:** Coherencia con la regla ya establecida en `HANDOFF.md` sección 16: "nunca se deja un archivo parcialmente implementado ni se divide un mismo archivo entre varias entregas, salvo razón técnica excepcional" — aquí se extiende el mismo criterio a una pareja de archivos con dependencia funcional directa.
- **Impacto:** Ninguno negativo — ambos archivos se entregaron completos y verificados en la misma sesión, sin fragmentar el trabajo.

### D-033 — Callback de renderizado de comentarios definido localmente en `comments.php`, no en `inc/helpers.php`
- **Fecha:** Sprint 6B, Entregable 6B.2
- **Problema:** `wp_list_comments()` requiere una función de callback para personalizar el marcado de cada comentario. Había que decidir dónde definir esa función.
- **Solución elegida:** Definir `ce_construction_render_comment()` directamente dentro de `comments.php` (envuelta en `function_exists()` para evitar redeclaración si el archivo se incluyera más de una vez), en vez de añadirla a `inc/helpers.php`.
- **Alternativas descartadas:** Añadirla a `inc/helpers.php` junto a las demás funciones reutilizables del proyecto (rechazado: esta función es marcado de presentación específico y acoplado a `comments.php` — no hay ningún otro archivo del tema que la reutilice ni se prevé que la reutilice, a diferencia de funciones genuinamente transversales como `ce_get_short_excerpt()`).
- **Motivo:** `inc/helpers.php` está reservado, por convención ya establecida en el proyecto, para funciones reutilizables entre múltiples archivos — añadir código de un solo uso ahí iría en contra de esa convención sin aportar ningún beneficio real de reutilización.
- **Impacto:** Si en el futuro se necesitara reutilizar esta función de renderizado en otro contexto (poco probable, ya que `comments.php` es único por diseño de WordPress), se podría mover a `inc/helpers.php` en ese momento sin romper nada, ya que su firma no depende de ningún estado específico de `comments.php`.
