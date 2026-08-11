# CE Construction — QA_REPORT.md
### Auditoría QA Integral (revisión completa, post Sprint 7)

> Este reporte reemplaza como documento vigente al `QA_REPORT.md` anterior (auditado hasta Sprint 3, con actualización de estado hasta el Entregable 7.3). Conserva **todo** el historial previo (QA-001 a QA-029) y añade una revisión integral nueva (QA-030 a QA-042) realizada sobre el estado real del proyecto a la fecha: Sprint 7 completo (4/4 Entregables, v0.7.3), incluyendo `screenshot.png` (Entregable 7.4, entregado y pendiente de aprobación).

**Nota metodológica sobre la fuente de verdad:** esta sesión no tuvo disponible una herramienta de conexión directa (clonado/fetch en vivo) al repositorio GitHub. La auditoría se realizó leyendo el contenido real de cada archivo del tema tal como fue provisto en el contexto de esta conversación (PHP, CSS, JS y los 9 documentos de control), no reconstruyendo el estado a partir de resúmenes de sesiones anteriores. Donde la documentación de control afirmaba algo que el código no respaldaba, se documenta la discrepancia (ver QA-030, el hallazgo más relevante de esta revisión). Dos archivos que `TREE.md` marca como ✅ (`page.php`, y la ausencia intencional de `search.php`) no fueron incluidos en el contexto de esta sesión y por tanto **no se auditaron directamente**; esto se documenta como limitación metodológica (QA-041), no como hallazgo de código.

**Ningún archivo de código, configuración o documentación (salvo este mismo archivo) fue modificado durante esta auditoría.**

---

## 1. Resumen ejecutivo

El tema CE Construction, en su estado v0.7.3 (Sprint 7 completo, 4/4 Entregables desarrollados, Entregable 7.4 pendiente de aprobación explícita del usuario), mantiene una arquitectura modular consistente y bien documentada, con las 9 correcciones Críticas/Altas del Sprint 5 (QA-001 a QA-009) verificadas como corregidas en el código real. Esta auditoría integral, más amplia en alcance que las anteriores (cubre concurrencia, privacidad de adjuntos, accesibilidad de componentes interactivos y consistencia de versión de assets), encontró **13 hallazgos nuevos** (QA-030 a QA-042), de los cuales **1 es de severidad ALTA con impacto operativo inmediato** (QA-030: el cache-busting de CSS/JS está roto desde antes del Sprint 7) y **1 es de severidad ALTA de privacidad/seguridad** (QA-031: adjuntos del formulario potencialmente accesibles por URL directa pese a que el CPT que los contiene es privado).

Ningún hallazgo nuevo es CRÍTICO en el sentido de "compromiso activo explotable trivialmente" (no hay inyección SQL, no hay bypass de nonce, no hay XSS reflejado nuevo), pero QA-030 y QA-031 son operacionalmente importantes y deberían resolverse antes de considerar el proyecto listo para producción con tráfico real.

## 2. Estado general del proyecto

| Aspecto | Estado |
|---|---|
| Versión de código (`CE_THEME_VERSION` / `style.css`) | **0.4.1** (ver QA-030 — desincronizada) |
| Versión funcional real (según `CHANGELOG.md`/`PROJECT_STATUS.md`) | v0.7.3 |
| Sprint activo | Sprint 7 — 4/4 Entregables desarrollados, 7.4 pendiente de aprobación |
| Hallazgos históricos Crítico/Alto (QA-001 a QA-009) | ✅ 9/9 corregidos y verificados en el código actual |
| Hallazgos históricos Medios (QA-010 a QA-018) | 🟡 1/9 corregido (QA-018) — 8 abiertos |
| Hallazgos históricos Bajos/Mejoras (QA-019 a QA-029) | ⬜ 11/11 abiertos (sin cambios) |
| Hallazgos nuevos de esta auditoría | 13 (QA-030 a QA-042) |

## 3. Metodología de auditoría

- Lectura íntegra de los ~60 archivos de código y los 9 documentos de control provistos en el contexto de la sesión.
- Verificación cruzada línea por línea entre lo que la documentación afirma (`DECISIONS.md`, `CHANGELOG.md`, `PROJECT_STATUS.md`, `TODO.md`, `TREE.md`) y el contenido real de cada archivo PHP/CSS/JS, con foco explícito en detectar discrepancias documentación↔código (instrucción explícita de esta auditoría).
- Trazado manual de los flujos sensibles: formulario de cotización (frontend → AJAX → validación → almacenamiento → email → adjuntos), carga de CSS/JS, Schema.org, breadcrumbs, ciclo de vida de metaboxes.
- Análisis conceptual (no empírico) de concurrencia y carga: no se ejecutó ninguna prueba de carga real ni se dispuso de un entorno WordPress/PHP en ejecución en esta sesión; todo lo indicado en la sección 8 (Concurrencia) es **análisis estático de código**, explícitamente etiquetado como tal, no resultado de una prueba de carga real.
- No se ejecutó ningún linter/escáner automatizado de seguridad (no disponible en el entorno); los hallazgos de seguridad provienen de revisión manual experta del código fuente real.
- Historial: se conserva íntegramente `QA_REPORT.md` anterior (QA-001 a QA-029); no se reutiliza ningún ID.

---

## 4. Arquitectura

Se confirma que la arquitectura descrita en `ARCHITECTURE.md` corresponde razonablemente al código real: separación por responsabilidad en `inc/`, componentización progresiva en `template-parts/`, `functions.php` como único punto de carga vía `require_once`, y convención de extensión aditiva (nunca reescritura) verificada en `assets/css/main.css` (secciones 20-24, cada una con comentario de sprint de origen) y en `inc/seo.php` (bloques añadidos por sprint sin tocar los anteriores).

**Confirmado, sin cambios respecto a `ARCHITECTURE.md`:**
- CPTs registrados uno por archivo (`inc/cpt-*.php`), decisión evaluada y mantenida deliberadamente (ver QA-029 histórico).
- `inc/helpers.php` sin hooks propios, solo funciones puras — confirmado, ninguna función de `helpers.php` se invoca durante la fase de carga de `functions.php`.
- Relación heurística Servicio↔Proyecto por coincidencia de nombre de taxonomía (D-010/D-014) — confirmada en `inc/helpers.php`, sin cambios.

**Hallazgo nuevo de arquitectura (ver detalle en sección 12):** el mecanismo de versión de assets (`CE_THEME_VERSION`) no está realmente integrado al flujo de cierre de Entregable/Sprint pese a que `QA-008` (histórico) se dio por corregido exigiendo justamente esa sincronización — ver QA-030.

---

## 5. PHP / WordPress

- **Nonces:** verificados en los 3 puntos que los requieren (`inc/quote-form.php` vía `ce_quote_form_action`, `inc/meta-boxes.php` con un nonce por CPT, ninguno reutilizado entre CPTs — buena práctica confirmada).
- **Sanitización/escaping:** confirmado el patrón `sanitize_text_field(wp_unslash(...))` en entrada y `esc_html`/`esc_attr`/`esc_url` en salida, de forma consistente en los archivos revisados. Única excepción real detectada: los bloques `wp_json_encode()` en `inc/seo.php` no usan ningún flag de escape adicional para el caso de que un título/contenido contuviera literalmente `</script>` — este es el histórico QA-014, confirmado aún abierto, sin cambios.
- **`current_user_can()` / `map_meta_cap`:** confirmado en `inc/meta-boxes.php` (los 5 bloques de guardado) y en `cotizacion` (`capability_type => 'post'`, `map_meta_cap => true`, `create_posts => do_not_allow`).
- **`WP_Query`:** ningún uso de SQL directo (`$wpdb`) en todo el código revisado — superficie de inyección SQL directa: nula. Los `tax_query`/`meta_query` usados son estándar de WP, sin concatenación insegura.
- **Compatibilidad PHP 8.x:** no se detectó ningún patrón obsoleto conocido (no hay `create_function`, no hay paso de `null` a parámetros no-nullable de funciones internas de forma evidente, no hay `each()`). Esta conclusión es de **revisión estática**, no de ejecución real en PHP 8.x — el propio proyecto ya documenta esta limitación metodológica (no hay entorno WordPress/PHP real disponible) en `ARCHITECTURE.md` sección 10.
- **Funciones inexistentes / includes rotos:** no se detectó ninguna llamada a función no definida en el conjunto de archivos revisado; `functions.php` carga todos los módulos referenciados y todos existen en el árbol documentado.

---

## 6. Seguridad

### Confirmado como corregido (histórico, sin cambios en esta auditoría)
QA-001 (validación de tipo de archivo), QA-002 (adjuntos huérfanos del flujo normal), QA-003 (retención de datos), QA-004 (rate limiting básico), QA-007 (guardia de revisión en `save_post`) — los 5 verificados presentes en el código real de `inc/quote-form.php` y `inc/meta-boxes.php`.

### Nuevo hallazgo ALTO — QA-031
**Adjuntos del formulario de cotización potencialmente accesibles por URL directa, pese a que el CPT contenedor es privado.**
El CPT `cotizacion` se registra con `'public' => false` (`inc/quote-form.php`), lo cual impide que exista una URL pública para el *post* de la cotización. Sin embargo, el archivo adjunto se sube con `wp_handle_upload()` y se registra como *attachment* real vía `wp_insert_attachment()` con `post_status => 'inherit'`, vinculado por `post_parent`. En WordPress, el hecho de que el post padre sea privado **no restringe por sí mismo el acceso directo al archivo físico** en `wp-content/uploads/AAAA/MM/archivo.ext`: ese archivo es servido directamente por el servidor web (Apache/Nginx), no a través de PHP/WordPress, salvo que exista una regla adicional (`.htaccess`, plugin de protección de medios, o `X-Sendfile`/proxy autenticado) que no está presente en ningún archivo revisado. Esto significa que un adjunto de cotización (que puede incluir planos, cédulas, presupuestos u otra información potencialmente sensible del cliente) podría quedar accesible para cualquiera que conozca o adivine/enumere la URL, sin autenticación, incluso siendo el registro de la cotización en sí mismo privado.
- **Severidad:** ALTO (privacidad de datos personales/comerciales de clientes).
- **Archivo:** `inc/quote-form.php`.
- **Corrección conceptual (no implementada en esta sesión):** servir el adjunto vía un endpoint PHP autenticado (`current_user_can`) en lugar de la URL directa de Media Library, o mover el archivo fuera de `wp-content/uploads/` a una ruta no servida directamente.

### Nuevo hallazgo MEDIO — QA-032
**Race condition (TOCTOU) en el rate-limiting del formulario de cotización.**
`ce_construction_handle_quote_form()` implementa el límite de 3 envíos/10 min con `get_transient()` seguido de `set_transient()` como dos operaciones separadas y no atómicas sobre `wp_options` (o el object cache si existe). Dos solicitudes concurrentes desde la misma IP pueden leer el mismo valor de `$attempts` antes de que cualquiera de las dos escriba el nuevo valor, permitiendo superar el límite de 3 en ráfagas de solicitudes simultáneas (ej. un script que dispare varias peticiones en paralelo).
- **Severidad:** MEDIO (mitigación parcial de spam/abuso, no un bypass total: solo afecta el conteo bajo concurrencia real, no elimina el honeypot ni la validación de campos).
- **Archivo:** `inc/quote-form.php`.

### Nuevo hallazgo MEDIO — QA-033
**Archivo huérfano en disco si `wp_insert_post()` falla después de `wp_handle_upload()`.**
El adjunto se mueve físicamente a `uploads/` (paso 5) **antes** de que se cree el post `cotizacion` (paso 6). Si `wp_insert_post()` devuelve `0` o `WP_Error` (ej. fallo de base de datos, plugin que aborta el insert), el bloque `if ( $post_id && ! is_wp_error( $post_id ) )` simplemente no ejecuta el registro del *attachment*, pero el archivo físico ya subido **no se elimina** en ningún punto del código — queda huérfano en disco indefinidamente. Este es un caso borde no cubierto por la corrección histórica QA-002 (que sí resolvió el caso general).
- **Severidad:** MEDIO.
- **Archivo:** `inc/quote-form.php`.

### Nuevo hallazgo MEDIO — QA-034
**Sin protección de idempotencia ante envíos duplicados del formulario.**
La única protección contra doble envío es del lado del cliente (`ModuleQuoteForm.setLoading()` deshabilita el botón de submit). No existe ningún token de idempotencia server-side ni verificación de contenido duplicado reciente (mismo email+mensaje en una ventana corta). Doble clic con latencia de red, doble pestaña abierta, o un reintento automático del navegador pueden generar cotizaciones y correos duplicados. El rate-limit de 3/10min no previene esto (2 duplicados caben dentro del límite).
- **Severidad:** MEDIO.
- **Archivo:** `inc/quote-form.php`, `assets/js/main.js` (`ModuleQuoteForm`).

### Confirmado sin cambios (histórico, abierto)
QA-011 (postMessage sin preview script), QA-014 (JSON-LD sin endurecer `</script>`), QA-015 (variable muerta `$attachment_name`... nota: sí se usa como `post_title` del attachment, por lo que se **corrige la clasificación histórica**: no es código completamente muerto, se usa en `$attachment_data['post_title']`; se mantiene el ID por continuidad documental pero se aclara su alcance real, sin volver a auditar el resto del hallazgo).

---

## 7. Formularios / AJAX (formulario de cotización — flujo completo)

Flujo verificado extremo a extremo: `template-parts/quote-form.php` (no incluido en el contexto de esta sesión, su existencia y contrato se infiere de `ARCHITECTURE.md` y `assets/js/main.js`) → `ModuleQuoteForm` (JS) → `admin-ajax.php` → `ce_construction_handle_quote_form()` → `wp_insert_post`/`wp_insert_attachment` → `wp_mail()` → respuesta JSON → modal de éxito/error.

- **Nonce:** obligatorio y verificado primero (`wp_verify_nonce`), correcto.
- **Honeypot:** campo `ce_website`, oculto vía CSS (`.ce-honeypot`, `position: absolute !important; left: -9999px`), técnica razonable (mejor que `display:none`, que algunos bots evitan rellenar por ser detectable vía CSSOM, aunque `position: absolute` fuera de viewport también es detectable por bots sofisticados — limitación aceptada, no nueva).
- **Rate limiting:** presente pero con race condition (QA-032, nuevo).
- **Validación server-side:** completa y autoritativa (nombre, email, teléfono con regex, servicio, mensaje con longitud mínima) — no depende de la validación de cliente.
- **Adjuntos:** validación de tipo real (`wp_check_filetype_and_ext`) + whitelist cerrado + límite de 5MB — correcto. Registro como *attachment* real — correcto, pero con el problema de exposición (QA-031, nuevo) y el caso borde de huérfano en fallo (QA-033, nuevo).
- **Almacenamiento:** CPT interno `cotizacion`, administrable desde wp-admin, con columnas custom — correcto.
- **Retención:** cron diario, 365 días configurables por filtro — correcto, confirmado sin cambios.
- **Email:** `wp_mail()` con `Reply-To` al remitente — correcto. Sin logging de fallos de envío (histórico QA-028, sin cambios).
- **Respuesta al usuario:** JSON estructurado con mensajes de error por campo — correcto y con buena UX.
- **Duplicación de solicitudes:** sin protección server-side (QA-034, nuevo).

---

## 8. Concurrencia y carga

**Aclaración obligatoria:** todo lo siguiente es **análisis conceptual basado en lectura de código**, no el resultado de una prueba de carga ejecutada. No hubo en esta sesión un entorno WordPress/PHP/MySQL real disponible para ejecutar dicha prueba.

- **Envíos simultáneos del formulario de cotización:** riesgo identificado y confirmado por análisis de código: la combinación de QA-032 (race condition del rate-limit) y QA-034 (sin idempotencia) hace plausible, bajo concurrencia real, tanto el bypass del límite de 3/10min como la creación de registros duplicados. Esto es un **hallazgo confirmado por análisis del código**, no una especulación: el patrón `get_transient()` + `set_transient()` no atómico es un hecho verificable en el código fuente, independientemente de si se llega a explotar en producción.
- **Múltiples `WP_Query` de "relacionados" por página vista:** en `single-servicio.php` y `single-proyecto.php` se ejecutan hasta 2-4 `WP_Query` adicionales (servicios relacionados, proyectos relacionados, FAQ, galería) sin ningún caché (histórico QA-012, confirmado sin cambios). Bajo alto tráfico concurrente a esas plantillas, esto es un **riesgo potencial** de carga de base de datos elevada — no confirmado como problema activo (depende del volumen de contenido y tráfico reales, ninguno medible en esta sesión), pero sí un patrón de código que no escala tan bien como podría.
- **Subida de adjuntos concurrente:** `wp_handle_upload()` genera nombres de archivo únicos de forma nativa (comportamiento estándar de WordPress), por lo que no se identifica riesgo de colisión de nombre de archivo entre subidas simultáneas — **sin hallazgo aquí**.
- **Cron de purga de cotizaciones (`ce_construction_purge_old_quotes`):** procesa en lotes de 50 (`posts_per_page => 50`), lo cual es una buena práctica ya presente; no se identifica riesgo nuevo de bloqueo por lote grande.
- **Memoria/PHP:** no se identifican bucles no acotados ni acumulación de arrays sin límite en el código revisado.

En síntesis: **1 riesgo confirmado por análisis de código** (QA-032/QA-034 combinados, formulario de cotización bajo concurrencia real), **1 riesgo potencial no confirmado** (consultas de "relacionados" sin caché bajo alto tráfico, ya documentado históricamente como QA-012). Ninguno de los dos requiere prueba de carga real para ser identificado como código sub-óptimo; sí la requeriría para cuantificar su impacto real en producción.

---

## 9. Performance

Confirmado sin cambios respecto al historial: CSS/JS monolíticos (decisión arquitectónica documentada y aceptada, D-016/sección 9 de `ARCHITECTURE.md`), Google Fonts + Font Awesome vía CDN externo sin auto-hospedar (histórico QA-027, abierto), filtro `script_loader_tag` redundante con `wp_script_add_data('defer')` (histórico QA-010, abierto).

### Nuevo hallazgo relacionado — QA-042 (FUTURO)
No hay `<link rel="preconnect">` ni `dns-prefetch` hacia `fonts.googleapis.com`/`fonts.gstatic.com`/`cdnjs.cloudflare.com` en `header.php` ni en `inc/enqueue.php`. Esto es un matiz distinto de QA-027 (que propone auto-hospedar): incluso manteniendo CDN externo, un `preconnect` reduce la latencia de conexión TLS antes de que el navegador descubra esos hosts al parsear el CSS. Impacto esperado: mejora menor de LCP/FCP.
- **Severidad:** FUTURO (mejora, no bloqueante).

### Hallazgo directamente relacionado con integridad de caché — ver QA-030 (sección 12)
El problema de versión de assets congelada (QA-030) tiene una faceta de performance además de operativa: si en algún momento se activa cacheo agresivo a nivel de CDN/navegador con headers de cache largos (`Cache-Control: max-age` alto) para `main.css`/`main.js` — práctica común de optimización de performance — el hecho de que la query string de versión no cambie entre v0.4.1 y v0.7.3 significa que esa optimización de performance **impediría** que los visitantes recurrentes reciban el CSS/JS actualizado del Sprint 7 hasta que expire el caché o se purgue manualmente. Es decir, QA-030 es simultáneamente un hallazgo de integridad de despliegue y un bloqueador latente para cualquier estrategia de performance basada en cache-busting por versión.

---

## 10. SEO

Confirmado sin cambios: meta description/Open Graph genéricos, Schema.org por CPT (Service, CreativeWork/Project, Person, Organization-cliente, BlogPosting), breadcrumbs HTML globales, JSON-LD `BreadcrumbList` solo para Servicio y Proyecto (histórico, documentado como limitación en D-037).

### Nuevo hallazgo MEDIO — QA-038
**Ausencia de `<link rel="canonical">`.** `ce_construction_meta_tags()` en `inc/seo.php` emite `og:url` pero no un `<link rel="canonical" href="...">` explícito en `<head>`. Sin canonical explícito, el sitio depende enteramente de la heurística de Google para URLs canónicas, lo cual es un riesgo real de contenido duplicado indexable en escenarios con parámetros de query (paginación de comentarios, UTMs, `?s=` variantes, etc.), especialmente relevante dado que el tema sí tiene paginación (`paginate_links`) en varios archivos sin parámetro canonical explícito hacia la página base.
- **Archivo:** `inc/seo.php`.

### Nuevo hallazgo BAJO — QA-039
**Twitter Card incompleto.** Solo se emite `<meta name="twitter:card" content="summary_large_image">`, sin `twitter:title`, `twitter:description` ni `twitter:image` explícitos. La mayoría de los parsers de Twitter/X hacen fallback a las etiquetas `og:*` equivalentes, por lo que el impacto práctico es bajo, pero no está garantizado para todos los consumidores de la Card.
- **Archivo:** `inc/seo.php`.

### Nuevo hallazgo BAJO — QA-040
**Inconsistencia de cobertura de `BreadcrumbList` JSON-LD entre tipos de contenido.** Servicio y Proyecto emiten su propio `BreadcrumbList`; Persona (Equipo), Cliente y BlogPosting no lo hacen, pese a que los 3 sí tienen breadcrumbs HTML equivalentes vía `ce_construction_breadcrumbs()`. No es un bug (nada está roto), pero es una inconsistencia de cobertura SEO entre tipos de contenido estructuralmente similares.
- **Archivo:** `inc/seo.php`.

---

## 11. Accesibilidad (WCAG 2.1/2.2 AA)

Confirmado sin cambios: contraste corregido históricamente (QA-005), skip-link sin `tabindex="-1"` en destino (histórico QA-017, abierto), FAQ accordion con `aria-expanded`/`aria-controls` correctos (sin hallazgo), menú móvil con `aria-expanded` sincronizado por JS (confirmado correcto).

### Nuevo hallazgo MEDIO — QA-035
**Slider de testimonios: mecanismo de pausa de autoplay no accesible a teclado/touch.** `ModuleTestimonialSlider` inicia autoplay (`setInterval`) y solo lo detiene con `mouseenter`/`mouseleave` sobre el contenedor. Un usuario que navega exclusivamente con teclado, o en un dispositivo táctil (donde no existe "hover"), no tiene forma de pausar el contenido en movimiento automático. Esto incumple el criterio WCAG 2.2.2 (Pausar, Detener, Ocultar), que exige un mecanismo de pausa accesible para cualquier contenido que se mueva/actualice automáticamente por más de 5 segundos y que no sea esencial.
- **Archivo:** `assets/js/main.js` (`ModuleTestimonialSlider`).
- **Severidad:** MEDIO.

### Nuevo hallazgo MEDIO — QA-036
**Gestión de foco ausente en menú móvil off-canvas y en modales.** Ni `ModuleMobileNav.open()`/`close()` ni `ModuleModals.open()`/`close()` mueven el foco del teclado hacia el panel/modal al abrir, ni lo devuelven al elemento disparador al cerrar, ni implementan un *focus trap* que mantenga el `Tab` dentro del panel mientras está abierto. Un usuario de teclado puede, tras abrir el menú móvil o un modal, seguir tabulando por elementos del fondo de la página (fuera del panel visible), lo cual es una violación común de accesibilidad en overlays/diálogos (relacionado con el patrón ARIA "Dialog (Modal)").
- **Archivos:** `assets/js/main.js` (`ModuleMobileNav`, `ModuleModals`), `header.php`, `footer.php`.
- **Severidad:** MEDIO.

### Nuevo hallazgo BAJO — QA-037
**`aria-label` estático del botón de menú móvil.** El botón `.ce-nav-toggle` mantiene siempre `aria-label="Abrir menú"` (definido en `header.php`), sin actualizarse a "Cerrar menú" cuando `ModuleMobileNav` lo marca como `is-active`/`aria-expanded="true"`. El estado `aria-expanded` sí se actualiza correctamente, pero el `aria-label` no acompaña ese cambio, lo cual puede confundir a usuarios de lector de pantalla que dependen más del label que del atributo `aria-expanded`.
- **Archivos:** `header.php`, `assets/js/main.js`.
- **Severidad:** BAJO.

---

## 12. Responsive / UX

No se modificó ningún CSS; se documentan únicamente observaciones conceptuales a partir de la lectura de `assets/css/main.css`.

- **Header / top bar:** QA-018 corregido y verificado presente en el código real (sección 24 de `main.css`, `@media (max-width: 767.98px)` con `flex-wrap` y centrado) — confirmado.
- **Flechas del slider de testimonios en mobile:** `.ce-slider-arrow--prev { left: -6px; }` / `--next { right: -6px; }` son posiciones absolutas ligeramente fuera del borde del contenedor `.ce-relative`. En viewports muy angostos (320-360px) esto puede hacer que las flechas queden parcialmente recortadas por `overflow: hidden` de `.ce-testimonial-slider` o muy pegadas al borde de la pantalla, dificultando el tap en touch. Esto es consistente con el hallazgo histórico BAJO ya documentado ("flechas de slider con 1 solo testimonio") pero aplica también con múltiples testimonios en viewports angostos — se mantiene como observación conceptual, sin nuevo ID (no verificable sin renderizado real).
- **Grid de clientes (`.ce-clients-grid`):** 2 columnas por defecto en mobile (`grid-template-columns: repeat(2, 1fr)`), razonable; sin hallazgo.
- **Formularios (`.ce-form__row--2`):** correctamente colapsa a 1 columna por debajo de 576px — sin hallazgo.

---

## 13. Template Hierarchy

| Plantilla | Estado verificado en esta sesión |
|---|---|
| `front-page.php` | ✅ Código presente y coherente, resuelve correctamente la portada. |
| `index.php` | ✅ Fallback correcto para `is_search()`, `is_404()`, `is_archive()` genérico, `is_singular()`. |
| `single.php` | ✅ Código presente, prioridad correcta sobre `index.php` para `post_type=post` (comportamiento nativo de WP, confirmado por su sola existencia). |
| `archive.php` | ✅ Código presente (Entregable 7.2), cubre categoría/etiqueta/autor/fecha y CPTs sin archivo propio (`testimonio`, `ce_faq`). |
| `404.php` | ✅ Código presente, con `status_header(404)` explícito — buena práctica confirmada. |
| `comments.php` | ✅ Código presente, con guardia `post_password_required()` correcta. |
| `archive-servicio.php` / `single-servicio.php` | ✅ Verificados. |
| `archive-proyecto.php` / `single-proyecto.php` | ✅ Verificados. |
| `archive-equipo.php` / `single-equipo.php` | ✅ Verificados. |
| `archive-clientes.php` / `single-clientes.php` | ✅ Verificados, incluye el `has_archive` habilitado (D-025) confirmado en `inc/cpt-clientes.php`. |
| `page.php` | ⚠️ **No incluido en el contexto de esta sesión** — `TREE.md` lo marca ✅ pero no se auditó su contenido real (ver QA-041). |
| `search.php` | N/A por diseño — `index.php` cubre `is_search()` intencionalmente (decisión documentada, no un archivo pendiente). |

WordPress podrá resolver correctamente todos los contextos verificados. Para `page.php`, la confirmación depende de la nota de QA-041.

---

## 14. Compatibilidad

- **WordPress:** el código usa exclusivamente APIs estables y de larga data (`register_post_type`, `WP_Query`, `WP_Widget`, `wp_nav_menu`, Customizer API clásica) — sin uso de APIs de bloques/Gutenberg ni Full Site Editing. Esto es compatible con cualquier WordPress moderno (6.x/7.x) siempre que el sitio use un tema clásico (no bloques), que es exactamente el caso. No se detecta ninguna llamada a función deprecada de WordPress en el código revisado.
- **Discrepancia de versión objetivo:** ni la documentación de control ni el código especifican en ningún punto una versión mínima probada más allá de `style.css` (`Requires at least: 6.0`, `Requires PHP: 7.4`). Esos valores del header de `style.css` **no fueron actualizados** junto con el resto del proyecto (coherente con el hallazgo QA-030: el header completo de `style.css`, incluida la versión, quedó congelado desde Sprint 5). No se identifica ninguna incompatibilidad real con WordPress 7.x ni PHP 8.x en el código en sí — la discrepancia es de **metadatos no actualizados**, no de compatibilidad funcional real.
- **PHP 8.x:** ver sección 5 — sin hallazgos de incompatibilidad detectados por revisión estática.
- **Navegadores modernos:** el JS usa sintaxis ES6 estándar (`const`/`let`, arrow functions, `fetch`, `IntersectionObserver` con *fallbacks* explícitos para navegadores sin soporte) — buena práctica confirmada, sin hallazgos.

---

## 15. Hallazgos críticos

Ninguno nuevo en esta auditoría. El único histórico (QA-001) ya está corregido y verificado.

## 16. Hallazgos altos

| ID | Título | Estado |
|---|---|---|
| QA-002 a QA-009 | (histórico, Sprint 5) | ✅ Corregidos, verificados sin cambios |
| **QA-030** | `CE_THEME_VERSION`/`style.css` congelados en 0.4.1 pese a 3 versiones de CSS lanzadas — cache-busting roto | 🆕 Abierto |
| **QA-031** | Adjuntos de cotización potencialmente accesibles por URL directa pese a CPT privado | 🆕 Abierto |

## 17. Hallazgos medios

| ID | Título | Estado |
|---|---|---|
| QA-010 a QA-017 | (histórico) | ⬜ Abiertos, sin cambios |
| QA-018 | Responsive `.ce-header__top` | ✅ Corregido (Sprint 7, Entregable 7.3) |
| **QA-032** | Race condition en rate-limiting del formulario | 🆕 Abierto |
| **QA-033** | Adjunto huérfano si `wp_insert_post()` falla | 🆕 Abierto |
| **QA-034** | Sin idempotencia ante envíos duplicados | 🆕 Abierto |
| **QA-035** | Autoplay de testimonios sin pausa accesible (WCAG 2.2.2) | 🆕 Abierto |
| **QA-036** | Sin gestión de foco en menú móvil/modales | 🆕 Abierto |
| **QA-038** | Sin `<link rel="canonical">` | 🆕 Abierto |

## 18. Hallazgos bajos

| ID | Título | Estado |
|---|---|---|
| QA-019 a QA-023 | (histórico) | ⬜ Abiertos, sin cambios |
| **QA-037** | `aria-label` estático del botón de menú móvil | 🆕 Abierto |
| **QA-039** | Twitter Card incompleto | 🆕 Abierto |
| **QA-040** | `BreadcrumbList` JSON-LD inconsistente entre tipos de contenido | 🆕 Abierto |
| **QA-041** | `page.php` no verificable en esta sesión (limitación metodológica, no hallazgo de código) | 🆕 Nota metodológica |

## 19. Mejoras futuras

| ID | Título | Estado |
|---|---|---|
| QA-024 a QA-029 | (histórico) | ⬜ Sin implementar, sin cambios |
| **QA-042** | Sin `preconnect`/`dns-prefetch` hacia CDNs de fuentes/iconos | 🆕 Propuesta nueva |

---

## 20. Cambios de arquitectura recomendados

> Ninguno de estos cambios fue implementado en esta sesión. Se listan exclusivamente como recomendación para planificación futura.

### R-1 — Automatizar el versionado de assets (`CE_THEME_VERSION`)
- **Problema actual:** QA-030 — la versión usada para cache-busting de CSS/JS se actualiza manualmente y quedó desincronizada desde Sprint 5.
- **Módulo afectado:** `functions.php`, `style.css`.
- **Arquitectura actual:** constante `define('CE_THEME_VERSION', '0.4.1')` fijada a mano.
- **Arquitectura propuesta:** derivar la versión automáticamente de `filemtime()` de `main.css`/`main.js` en `inc/enqueue.php` (patrón común: `filemtime(get_theme_file_path('assets/css/main.css'))` como argumento de versión), o al menos añadir la verificación de sincronización de versión como paso obligatorio y explícito en la checklist de cierre de cada Entregable que toque CSS/JS.
- **Beneficio:** elimina por completo la clase de bug QA-030/QA-008 de forma estructural, sin depender de disciplina manual.
- **Riesgo:** bajo — cambio aislado a `inc/enqueue.php`.
- **Impacto en compatibilidad:** ninguno.
- **Dificultad estimada:** baja.
- **Clasificación:** Cambio menor.
- **Cuándo:** puede hacerse en cualquier momento, idealmente antes del próximo Sprint que modifique CSS/JS.

### R-2 — Servir adjuntos del formulario de cotización por endpoint autenticado
- **Problema actual:** QA-031 — archivos potencialmente sensibles servidos por URL pública directa.
- **Módulo afectado:** `inc/quote-form.php`.
- **Arquitectura actual:** `wp_insert_attachment()` estándar, servido directamente desde `wp-content/uploads/`.
- **Arquitectura propuesta:** mover los adjuntos a una ruta protegida (fuera del *document root* servible, o dentro de `uploads/` con una regla de servidor que exija autenticación) y exponerlos solo mediante un *callback* PHP con `current_user_can('edit_post', $post_id)`.
- **Beneficio:** cierra una exposición real de datos potencialmente sensibles de clientes.
- **Riesgo:** medio — cambia el flujo de descarga de adjuntos desde wp-admin; requiere pruebas.
- **Impacto en compatibilidad:** bajo, pero requiere control sobre configuración del servidor web (no solo PHP) para la variante de ruta protegida.
- **Dificultad estimada:** media.
- **Clasificación:** Cambio arquitectónico moderado.
- **Cuándo:** recomendado antes de manejar adjuntos con datos sensibles reales en producción.

### R-3 — Rate-limiting atómico y caché de "relacionados"
- **Problema actual:** QA-032 (race condition) y QA-012 histórico (consultas de "relacionados" sin caché).
- **Módulo afectado:** `inc/quote-form.php`, `inc/helpers.php`.
- **Arquitectura actual:** transients de WordPress sin operación atómica; `WP_Query` repetida sin memoización más allá del request actual.
- **Arquitectura propuesta:** para el rate-limit, considerar un *lock* corto (ej. `wp_cache_add()` con expiración, que sí es atómico si hay object cache persistente; o aceptar el riesgo residual documentándolo como limitación conocida si no se dispone de object cache). Para "relacionados", envolver `ce_get_related_*()` con `wp_cache_get`/`set` de corta duración (ej. 5-15 min) por `post_id`.
- **Beneficio:** reduce carga de BD y cierra el bypass de rate-limit bajo concurrencia real.
- **Riesgo:** bajo-medio — requiere invalidación de caché correcta si el contenido relacionado cambia.
- **Impacto en compatibilidad:** ninguno si se usa la Transients API/Object Cache API estándar.
- **Dificultad estimada:** media.
- **Clasificación:** Cambio menor (rate-limit) / Cambio arquitectónico moderado (caché de relacionados, por el manejo de invalidación).
- **Cuándo:** puede planificarse para el Sprint de performance/accesibilidad ya propuesto en `PROJECT_STATUS.md`.

### R-4 — Gestión de foco centralizada para overlays (menú móvil, modales, lightbox)
- **Problema actual:** QA-035, QA-036 — sin *focus trap* ni retorno de foco en ningún overlay del tema.
- **Módulo afectado:** `assets/js/main.js` (`ModuleMobileNav`, `ModuleModals`, `ModuleLightbox`, `ModuleTestimonialSlider`).
- **Arquitectura actual:** cada módulo de overlay implementa su propia lógica de apertura/cierre de forma independiente, sin utilidad compartida.
- **Arquitectura propuesta:** extraer un pequeño *helper* compartido (`ModuleFocusTrap` o similar) reutilizado por los 3-4 módulos que abren overlays, encargado de: mover el foco al abrir, atrapar `Tab`/`Shift+Tab` dentro del contenedor, devolver el foco al elemento disparador al cerrar, y (para el slider) exponer un botón de pausa visible/accesible.
- **Beneficio:** resuelve de una sola vez varios hallazgos de accesibilidad relacionados, evita duplicar la lógica de foco en cada módulo nuevo futuro.
- **Riesgo:** bajo — aditivo, no reemplaza el comportamiento visual existente.
- **Impacto en compatibilidad:** ninguno.
- **Dificultad estimada:** media (por la cantidad de puntos de integración, no por complejidad individual).
- **Clasificación:** Cambio arquitectónico moderado.
- **Cuándo:** recomendable agrupar con el Sprint de accesibilidad ya propuesto en `PROJECT_STATUS.md`/`TODO.md` sección 23.

### R-5 — Consolidar `<link rel="canonical">` y completar Twitter Card / BreadcrumbList
- **Problema actual:** QA-038, QA-039, QA-040.
- **Módulo afectado:** `inc/seo.php`.
- **Arquitectura actual:** `ce_construction_meta_tags()` cubre OG pero no canonical ni Twitter Card completo; `BreadcrumbList` JSON-LD implementado solo para 2 de 5 tipos de contenido.
- **Arquitectura propuesta:** ampliar `ce_construction_meta_tags()` con canonical y campos Twitter faltantes; extraer la generación de `BreadcrumbList` a una función reutilizable (`ce_construction_get_breadcrumb_schema()`) invocada desde los 5 callbacks de Schema por CPT, reutilizando la misma estructura de `$items` que ya arma `ce_construction_breadcrumbs()`.
- **Beneficio:** consistencia SEO entre todos los tipos de contenido, sin duplicar lógica de breadcrumbs (HTML vs JSON-LD) como ocurre hoy.
- **Riesgo:** bajo.
- **Impacto en compatibilidad:** ninguno.
- **Dificultad estimada:** baja-media.
- **Clasificación:** Cambio menor.
- **Cuándo:** puede incluirse en cualquier Sprint de SEO/contenido futuro; no es bloqueante.

---

## 21. Riesgos para producción

Antes de considerar el proyecto listo para recibir tráfico real de producción con datos reales de clientes, se recomienda resolver como mínimo:

1. **QA-030** (ALTO) — sin esto, cualquier despliegue con cache de CDN/navegador puede servir CSS/JS desactualizado a usuarios recurrentes de forma indefinida.
2. **QA-031** (ALTO) — sin esto, documentos potencialmente sensibles de clientes (adjuntos de cotización) quedan expuestos por URL directa.
3. **QA-032 + QA-034** (MEDIO, combinados) — bajo un pico de tráfico o un intento de abuso automatizado, el formulario puede generar más carga/spam del esperado por el diseño del rate-limit.

El resto de hallazgos (accesibilidad, SEO, performance) son mejoras de calidad importantes pero no bloqueantes para un lanzamiento inicial.

---

## 22. Recomendaciones priorizadas

1. QA-030 — sincronizar versión de assets (Cambio menor, alto impacto).
2. QA-031 — proteger adjuntos de cotización (Cambio arquitectónico moderado, alto impacto en privacidad).
3. QA-032 / QA-034 — endurecer rate-limit + idempotencia del formulario (Cambio menor-moderado).
4. QA-035 / QA-036 — accesibilidad de overlays (Cambio arquitectónico moderado, agrupable en un solo esfuerzo — ver R-4).
5. QA-038 — canonical (Cambio menor).
6. Resto de hallazgos Medios/Bajos/Futuros — según prioridad de negocio, ninguno bloqueante.

---

## 23. Resumen final

Esta auditoría integral confirma que el proyecto mantiene la disciplina de calidad documentada en sprints anteriores (9/9 hallazgos Críticos/Altos históricos siguen corregidos, ninguna regresión detectada en esas correcciones). Se identificaron **13 hallazgos nuevos**: 2 de severidad ALTA (uno operativo — cache-busting roto, QA-030 — y uno de privacidad — exposición de adjuntos, QA-031), 6 de severidad MEDIA, 4 de severidad BAJA, y 1 mejora FUTURA, además de una nota metodológica (QA-041) sobre un archivo no verificable en esta sesión por no haber estado disponible en el contexto.

**Ningún hallazgo nuevo requiere un cambio arquitectónico "importante"**; el más significativo (R-4, gestión de foco centralizada) se clasifica como **moderado**, y el resto son **menores**. Ninguno de los 5 cambios recomendados fue implementado — quedan documentados exclusivamente como propuesta.

## 24. Próximos pasos sugeridos

1. Revisar este reporte y decidir, con aprobación explícita (conforme a la regla D-038 ya vigente en el proyecto), cuáles hallazgos se abordarán y en qué Sprint.
2. Si se aprueba, dividir la corrección en Entregables siguiendo la metodología ya establecida (`HANDOFF.md` sección 16), priorizando QA-030 y QA-031 por su severidad ALTA.
3. Ninguna corrección debe iniciarse sin esa aprobación explícita — conforme a las reglas de esta sesión, aquí no se ha iniciado ningún Sprint ni corregido ningún hallazgo.

---

## Cierre de la auditoría

1. **No se modificó ningún archivo de código, configuración ni documentación** salvo la generación de este `QA_REPORT.md`.
2. **Hallazgos nuevos encontrados:** 13 (QA-030 a QA-042).
3. **Hallazgos históricos que siguen abiertos:** 8 Medios (QA-010 a QA-017) + 5 Bajos (QA-019 a QA-023) + 6 Mejoras futuras (QA-024 a QA-029) = 19.
4. **Hallazgos confirmados como corregidos (verificados en el código real de esta sesión):** 9 Críticos/Altos históricos (QA-001 a QA-009) + 1 Medio histórico (QA-018) = 10.
5. **Hallazgos CRÍTICOS de esta auditoría:** ninguno nuevo. **Hallazgos ALTOS de esta auditoría:** QA-030, QA-031.
6. **Requieren cambio arquitectónico (moderado):** R-2 (QA-031), R-3 parcial (caché de relacionados), R-4 (QA-035/QA-036).
7. **Pueden corregirse sin cambio arquitectónico** (cambio menor, dentro del código existente): QA-030, QA-032 (parcial), QA-037, QA-038, QA-039, QA-040, QA-042.
8. Se entrega `QA_REPORT.md` completo a continuación como archivo descargable.
9. **No se corrigió ningún hallazgo.**
10. **No se inició ningún Sprint.**
11. **No se desarrolló código.**
