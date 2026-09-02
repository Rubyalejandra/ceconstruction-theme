# CE Construction — QA_REPORT.md
## QA MAESTRO — Auditoría histórica + auditoría integral post Sprint 7

> **Documento fuente oficial para el Sprint 8 de correcciones QA.**
>
> Este documento consolida los dos reportes QA proporcionados para el proyecto:
> 1. el `QA_REPORT.md` histórico, con los hallazgos QA-001 a QA-029;
> 2. `QA_REPORT_2.md`, con la auditoría integral posterior y los hallazgos QA-030 a QA-042.
>
> Se conservan los estados históricos y las correcciones ya realizadas. **Ningún hallazgo se considera aprobado para corrección únicamente por aparecer en este documento**: las correcciones deberán autorizarse y ejecutarse mediante los Entregables del Sprint 8 según la metodología del proyecto.

---

# 1. Resumen ejecutivo

| Severidad | Históricos | Nuevos | Total |
|---|---:|---:|---:|
| 🔴 Crítico | 1 | 0 | 1 |
| 🟠 Alto | 8 | 2 | 10 |
| 🟡 Medio | 9 | 6 | 15 |
| 🟢 Bajo | 5 | 4 | 9 |
| 🔵 Mejora futura | 6 | 1 | 7 |
| ⚪ Nota metodológica | 0 | 1 | 1 |
| **Total de IDs/documentados** | **29** | **14** | **43** |

> **Nota (sesión de cierre de UX-7):** el total pasa a 43 por la incorporación de **QA-043** (nuevo, sección 4.7), detectado durante la auditoría UX y clasificado como corrección de QA. QA-041 se recuenta como cerrado (ver abajo) en vez de nota metodológica abierta.

### Estado de correcciones

- QA-001 a QA-009: ✅ corregidos y documentados en v0.4.1.
- QA-018: ✅ corregido y documentado en v0.7.2 / Sprint 7 Entregable 7.3.
- **QA-010, QA-011, QA-014, QA-017: ✅ corregidos en Sprint 8, Entregable 8.1** — ver `DECISIONS.md` D-042.
- **QA-013: 🟡 parcialmente corregido en Sprint 8, Entregable 8.1** (solo el comentario inexacto; la unificación real queda en backlog) — ver `DECISIONS.md` D-042.
- **QA-015: ✅ verificado en Sprint 8, Entregable 8.1 — no se reproduce en el código actual, sin cambio de código.**
- **QA-030 (Alto): ✅ corregido en Sprint 8, Entregable 8.2** (cache-busting por `filemtime()` + `CE_THEME_VERSION` derivada de `wp_get_theme()`) — ver `DECISIONS.md` D-044.
- **QA-012, QA-016 (Medios):** ✅ **corregidos y aprobados** — Sprint 8, Entregable 8.5 (`DECISIONS.md` D-102, D-103). Pruebas funcionales reales ejecutadas por el usuario.
- QA-019 a QA-029: ⬜ pendientes / mejoras futuras según su clasificación.
- **QA-031 (Alto):** ✅ **corregido y aprobado** — Sprint 8, Entregable 8.3 (`DECISIONS.md` D-096, D-097). Pruebas funcionales reales ejecutadas y verificadas por el usuario en el entorno real (Apache/2.4.68, PHP 8.2.33).
- **QA-032, QA-033, QA-034 (Medios):** ✅ **corregidos y aprobados** — Sprint 8, Entregable 8.4 (`DECISIONS.md` D-098, D-099, D-101). Pruebas funcionales reales ejecutadas por el usuario.
- **QA-035, QA-038 (Medios):** ✅ **corregidos y aprobados** — Sprint 8, Entregable 8.5 (`DECISIONS.md` D-102, D-103). Pruebas funcionales reales ejecutadas por el usuario.
- **QA-036 (Medio):** ✅ **corregido y aprobado** — Sprint 8, Entregable 8.6 (`DECISIONS.md` D-104, D-105). Pruebas funcionales reales ejecutadas por el usuario. QA-037, QA-039, QA-040 permanecen abiertos según la auditoría integral (agrupados en el Entregable 8.7 de D-043).
- QA-041: ✅ **cerrado** — `page.php` no existía al momento de la verificación; fue creado después en Sprint UX-6 (Entregable UX-6.1, fuera del Sprint 8). Ver detalle sección 4.6.
- QA-042: 🔵 mejora futura.
- **QA-043:** ✅ **corregido** — `.ce-header__social` sin estilo base, resuelto en Sprint UX-11 (no en el Sprint 8, ver `docs/DECISIONS.md` D-087/D-090). Ver sección 4.7.

**Resultado operativo (actualizado, Sprint 8 Entregable 8.6):** 26 hallazgos corregidos o cerrados (10 históricos + QA-010/011/014/017/030/031 nuevos + QA-032/QA-033/QA-034 + QA-012/QA-016/QA-035/QA-038 + QA-036 + QA-013 parcial + QA-015 verificado como no reproducible); el resto permanece pendiente, sujeto a priorización y aprobación para el Entregable 8.7, último de la reorganización vigente (ver `DECISIONS.md` D-043).

---

# 2. Fuente y alcance

El presente QA maestro se construyó exclusivamente a partir de los dos reportes QA entregados para este proyecto. No se ejecutó una nueva auditoría ni se modificó código durante la consolidación.

La auditoría integral (`QA_REPORT_2.md`) declaró como alcance el estado del proyecto posterior al Sprint 7 y documentó expresamente que su revisión fue estática sobre los archivos disponibles en aquella sesión. También dejó constancia de que no se ejecutaron pruebas de carga reales ni escáneres automatizados de seguridad.

---

# 3. HISTORIAL COMPLETO — QA-001 a QA-029

## 🔴 CRÍTICO

### QA-001 — Bypass de validación de tipo de archivo en el formulario de cotización
- **Estado:** ✅ **CORREGIDO en v0.4.1** — ver `DECISIONS.md` D-017.
- **Archivo afectado:** `inc/quote-form.php`
- **Descripción:** La validación comparaba `$_FILES['attachment']['type']` (MIME falsificable del cliente) y `wp_check_filetype()` solo verificaba la lista global de WordPress, no el whitelist real (pdf/jpg/png/webp).
- **Recomendación aplicada:** `wp_check_filetype_and_ext()` + whitelist explícito.

---

## 🟠 ALTO

### QA-002 — Archivos adjuntos nunca registrados como attachment: fuga de disco sin límite
- **Estado:** ✅ **CORREGIDO en v0.4.1** — ver `DECISIONS.md` D-017.

### QA-003 — Retención de datos personales sin límite
- **Estado:** ✅ **CORREGIDO en v0.4.1** — ver `DECISIONS.md` D-018.

### QA-004 — Sin rate-limiting en el endpoint AJAX público
- **Estado:** ✅ **CORREGIDO en v0.4.1** — ver `DECISIONS.md` D-019.

### QA-005 — Fallo verificado de contraste WCAG AA
- **Estado:** ✅ **CORREGIDO en v0.4.1** — ver `DECISIONS.md` D-020.

### QA-006 — Sidebar "Footer - Columna 1" registrado pero nunca renderizado
- **Estado:** ✅ **CORREGIDO en v0.4.1** — ver `DECISIONS.md` D-021.

### QA-007 — `save_post` sin guardia `wp_is_post_revision()`
- **Estado:** ✅ **CORREGIDO en v0.4.1** — ver `DECISIONS.md` D-022.

### QA-008 — `CE_THEME_VERSION` hardcodeada
- **Estado:** ✅ **CORREGIDO en v0.4.1** — ver `DECISIONS.md` D-023.

### QA-009 — CPT Servicio sin `page-attributes`
- **Estado:** ✅ **CORREGIDO en v0.4.1** — ver `DECISIONS.md` D-024.

*(Detalle completo de descripción/riesgo/recomendación de QA-001 a QA-009 sin cambios respecto a versiones previas de este documento — no se repite aquí para no exceder el alcance de esta actualización, que solo modifica el estado de QA-018.)*

---

## 🟡 MEDIO

### QA-010 — Filtro `script_loader_tag` redundante con `wp_script_add_data('defer')`
- **Estado:** ✅ **CORREGIDO en Sprint 8, Entregable 8.1** — ver `DECISIONS.md` D-042.
- **Archivo afectado:** `inc/enqueue.php`
- **Descripción:** El código usaba `wp_script_add_data('defer')` (soporte nativo WP 6.3+) **y además** un filtro manual `ce_construction_add_defer_attribute()` que hacía lo mismo vía `str_replace()`. Deuda técnica, no bug activo.
- **Corrección aplicada:** Se eliminó el filtro manual (`ce_construction_add_defer_attribute()` y su `add_filter`). `wp_script_add_data( 'ce-construction-main', 'defer', true )` queda intacto y sigue siendo el único mecanismo responsable de añadir `defer` al script principal.

### QA-011 — `transport: postMessage` en 3 colores del Customizer sin script de preview
- **Estado:** ✅ **CORREGIDO en Sprint 8, Entregable 8.1** — ver `DECISIONS.md` D-042.
- **Archivo afectado:** `inc/customizer.php`
- **Descripción:** 3 ajustes de color declaraban `postMessage` pero no había script en `customize_preview_init` que aplicara el cambio en vivo — la vista previa no se actualizaba hasta publicar (el comportamiento real ya era un refresh silencioso).
- **Corrección aplicada:** Se eliminó la clave `'transport' => 'postMessage'` de los 3 `add_setting()` de color; quedan en el default `refresh`, que es honesto con el comportamiento real. No se implementó el script de preview en vivo alternativo (mayor alcance, no solicitado).

### QA-012 — Consultas de "relacionados" sin caché (hasta 4 `WP_Query` extra por página)
- **Estado:** ✅ **Corregido y aprobado en Sprint 8, Entregable 8.5**, tras pruebas funcionales reales del usuario. Ver `DECISIONS.md` D-102, D-103.
- **Archivo afectado:** `inc/helpers.php`
- **Descripción:** `ce_get_related_services()`/`ce_get_related_projects()` no usan memoización ni transient, a diferencia de `ce_cpt_has_posts()`.
- **Corrección aplicada:** transient de 1h por id+límite (también extendida a `ce_get_related_services_for_project()`, mismo patrón sin caché aunque no nombrada en el hallazgo original), con invalidación activa vía contador de versión al guardar `servicio`/`proyecto` o editar sus taxonomías. Ver `DECISIONS.md` D-102 para el detalle técnico completo.

### QA-013 — Duplicación de variables CSS/reset entre `style.css` y `main.css`
- **Estado:** 🟡 **Parcialmente corregido en Sprint 8, Entregable 8.1** — ver `DECISIONS.md` D-042.
- **Archivo afectado:** `style.css`, `assets/css/main.css`
- **Descripción:** Ambos archivos declaran el mismo bloque `:root`/reset; el comentario de `style.css` afirmaba un `@import` inexistente.
- **Corrección aplicada (parcial):** Se corrigió el comentario de `style.css` para reflejar el comportamiento real (ambos archivos se encolan por separado, `main.css` no importa `style.css`; la duplicación de tokens es deliberada como fallback, no un error). **La unificación real de los dos bloques `:root`** sigue sin corregir — requiere decidir cuál de los dos bloques es la fuente única de verdad, una decisión arquitectónica que toca el archivo raíz obligatorio del tema y que no se ejecuta sin aprobación explícita previa (queda en backlog).

### QA-014 — JSON-LD sin endurecimiento contra `</script>`
- **Estado:** ✅ **CORREGIDO en Sprint 8, Entregable 8.1** — ver `DECISIONS.md` D-042.
- **Archivo afectado:** `inc/seo.php`
- **Descripción:** El contenido editorial se inyectaba en 8 bloques `<script type="application/ld+json">` sin escapar una secuencia `</script>` literal que ese contenido pudiera contener.
- **Corrección aplicada:** Nueva función auxiliar `ce_construction_output_json_ld( $data )` que aplica `JSON_UNESCAPED_SLASHES` y un `str_replace( '</script', '<\/script', $json )` defensivo antes de imprimir. Los 8 puntos de salida existentes (Organización, Servicio + su BreadcrumbList, Proyecto + su BreadcrumbList, Persona, Cliente/Organization, BlogPosting) ahora llaman a esta función en vez de hacer `echo` directo — mismo schema, misma estructura de datos, sin cambios de contenido.

### QA-015 — Variable `$attachment_name` calculada pero nunca usada
- **Estado:** ✅ **Verificado en Sprint 8, Entregable 8.1 — el hallazgo ya no se reproduce, sin cambio de código.**
- **Archivo afectado:** `inc/quote-form.php`
- **Descripción:** Se inspeccionó línea por línea el código actual: `$attachment_name` **sí se usa**, en `$attachment_data['post_title']` al construir el array pasado a `wp_insert_attachment()` (dentro del bloque de registro del adjunto como Media Library attachment, ver QA-002). No es código muerto en el estado actual del repositorio — el hallazgo, tal como está descrito, no se reproduce. No se realizó ningún cambio de código para este ítem; se cierra únicamente a nivel de este reporte.

### QA-016 — `<script>` inline en metabox sin `wp_enqueue_script`/dependencia declarada
- **Estado:** ✅ **Corregido y aprobado en Sprint 8, Entregable 8.5**, tras pruebas funcionales reales del usuario. Ver `DECISIONS.md` D-102, D-103.
- **Archivo afectado:** `inc/meta-boxes.php` (`ce_render_proyecto_gallery()`)
- **Descripción:** El selector de galería imprime `<script>` jQuery inline sin declarar `jquery` como dependencia formal.
- **Corrección aplicada:** script movido a `assets/js/admin-proyecto-gallery.js` (nuevo), encolado en `inc/enqueue.php` con `array( 'jquery', 'media-editor' )` como dependencia formal, condicionado a la pantalla de edición del CPT `proyecto`. Ver `DECISIONS.md` D-102.

### QA-017 — Skip-link sin `tabindex="-1"` en `<main>`
- **Estado:** ✅ **CORREGIDO en Sprint 8, Entregable 8.1** — ver `DECISIONS.md` D-042.
- **Archivo afectado:** `header.php`
- **Descripción:** El skip-link apuntaba a `#ce-main-content`, que no tenía `tabindex="-1"` — el foco de teclado no se movía al activarlo en varios navegadores.
- **Corrección aplicada:** Se añadió `tabindex="-1"` a `<main id="ce-main-content">`. Cambio de un solo atributo, sin impacto visual ni en el resto del documento.

### QA-018 — Barra superior del header sin adaptación responsive explícita
- **Estado:** ✅ **CORREGIDO en v0.7.2** (Sprint 7, Entregable 7.3, con aprobación explícita del usuario) — ver `DECISIONS.md` D-039 y `CHANGELOG.md`.
- **Archivo afectado:** `assets/css/main.css` (`.ce-header__top`)
- **Descripción:** Se confirmó que no existía ninguna regla `@media` que adaptara `.ce-header__top` (teléfono + correo + horario + iconos sociales) para viewports pequeños (320-375px), pudiendo desbordar horizontalmente o comprimirse de forma ilegible.
- **Recomendación aplicada:** Nueva sección 24 (100% aditiva, al final de `assets/css/main.css`): `@media (max-width: 767.98px)` que aplica `flex-wrap: wrap` y centra `.ce-header__top .ce-container`, `.ce-header__contact` y `.ce-header__social`.
- **Impacto de la corrección:** Cambio acotado a un solo archivo CSS, sin riesgo de romper el layout de escritorio (la regla solo aplica por debajo de 768px). Verificado: balance de llaves/paréntesis correcto en el archivo completo tras la adición (365/365 llaves, 560/560 paréntesis).

---

## 🟢 BAJO

*(QA-019 a QA-023 — sin cambios respecto a versiones previas de este documento; ninguno fue tocado en este Entregable. Ver el detalle completo en el repositorio/entregas anteriores: reutilización de `.ce-modal__close` fuera de contexto, flechas de slider con 1 solo testimonio, discrepancia de versión ya resuelta como efecto colateral de QA-008, concatenación de HTML en JS del admin, y verificación positiva de `rel="noopener"` en todos los enlaces externos.)*

## 🔵 MEJORAS FUTURAS

*(QA-024 a QA-029 — sin cambios. Duplicación de breadcrumbs HTML/JSON-LD, `@id` compartido de Organization, preconnect de fuentes, auto-hospedaje de Google Fonts/Font Awesome, logging de fallos de `wp_mail()`, y evaluación de unificar los 4 CPTs simples — con recomendación de **no** unificarlos, ya documentada.)*

---

## Código muerto detectado (resumen)
- QA-006 (sidebar `footer-1` registrado, nunca usado) — ✅ Corregido en v0.4.1.
- QA-015 (`$attachment_name` calculado, nunca usado) — ✅ Verificado en Sprint 8, Entregable 8.1: no es código muerto, la variable sí se usa. Ver detalle en la sección MEDIO.

---

## Cierre del Sprint de QA

**Actualización (Sprint 5, v0.4.1):** los 9 hallazgos Críticos y Altos corregidos como Fase 1 del Sprint 5 — ver `DECISIONS.md` D-017 a D-024.

**Actualización (Sprint 7, Entregable 7.3, v0.7.2):** de los 9 hallazgos Medios (QA-010 a QA-018), se corrigió únicamente **QA-018**, con autorización explícita puntual del usuario para ese hallazgo específico (no para el resto de Medios). Corrección documentada en `CHANGELOG.md` (v0.7.2) y `DECISIONS.md` (D-039).

**Actualización (Sprint 8, Entregable 8.1):** re-evaluados contra el código real del repositorio los 8 hallazgos Medios restantes de la auditoría histórica (QA-010 a QA-017). Corregidos: QA-010, QA-011, QA-014, QA-017 (completos) y QA-013 (parcial, solo el comentario). Verificado como ya no reproducible: QA-015 (sin cambio de código). Pendientes: QA-012 y QA-016. Ver `DECISIONS.md` D-042 para el detalle completo de la re-evaluación y de por qué la agrupación de D-041 quedó superseded. Los hallazgos de la auditoría integral (QA-030 a QA-042) permanecen sin tocar — esta actualización se limitó al Entregable 8.1, que no los incluyó.

---

# 4. AUDITORÍA INTEGRAL POST SPRINT 7 — QA-030 a QA-042

## 4.1 QA-030 — Alto

### QA-030 — `CE_THEME_VERSION` / `style.css` congelados en 0.4.1 — cache-busting roto

- **Severidad:** ALTO.
- **Estado:** ✅ **CORREGIDO en v0.8.1** (Sprint 8, Entregable 8.2) — ver `DECISIONS.md` D-044.
- **Archivo(s) afectado(s):** `style.css`, `functions.php`, `inc/enqueue.php`.
- **Descripción:** La versión usada para cache-busting de CSS/JS quedó congelada en `0.4.1` pese a que el proyecto avanzó posteriormente hasta v0.8.0. Esto provocaba que las URLs versionadas de los assets no cambiaran entre despliegues y podía hacer que navegadores o CDN con caché agresivo conservaran CSS/JS antiguos.
- **Riesgo:** Integridad de despliegue y rendimiento. Los usuarios recurrentes podían recibir CSS/JS desactualizados hasta que expirara o se purgara el caché.
- **Recomendación aplicada:** implementada la Recomendación R-1 (ver sección de recomendaciones arquitectónicas): nueva función `ce_construction_asset_version()` en `inc/enqueue.php`, que usa `filemtime()` real de cada archivo en disco como versión de cache-busting para `style.css`, `assets/css/main.css` y `assets/js/main.js` — se actualiza automáticamente en cada cambio, sin depender de que nadie recuerde subir un número a mano. `CE_THEME_VERSION` (uso informativo general, ya no de cache-busting) pasa a derivarse de `wp_get_theme()->get('Version')`, unificándose con la cabecera de `style.css` (que también se sincronizó a `0.8.1`) para que ambos valores no puedan volver a desincronizarse entre sí.
- **Cambio arquitectónico:** documentado en `ARCHITECTURE.md` sección 9 (cambio real de mecanismo de cache-busting, no solo de valor).
- **Impacto de la corrección:** cambio acotado a 3 archivos (`functions.php`, `inc/enqueue.php`, `style.css`), sin tocar `assets/css/main.css` ni `assets/js/main.js`. Resuelve la causa raíz de forma permanente — el problema no puede volver a producirse por olvido humano.

## 4.2 QA-031 a QA-034 — Seguridad, formularios y concurrencia

### Nuevo hallazgo ALTO — QA-031
**Adjuntos del formulario de cotización potencialmente accesibles por URL directa, pese a que el CPT contenedor es privado.**
El CPT `cotizacion` se registra con `'public' => false` (`inc/quote-form.php`), lo cual impide que exista una URL pública para el *post* de la cotización. Sin embargo, el archivo adjunto se sube con `wp_handle_upload()` y se registra como *attachment* real vía `wp_insert_attachment()` con `post_status => 'inherit'`, vinculado por `post_parent`. En WordPress, el hecho de que el post padre sea privado **no restringe por sí mismo el acceso directo al archivo físico** en `wp-content/uploads/AAAA/MM/archivo.ext`: ese archivo es servido directamente por el servidor web (Apache/Nginx), no a través de PHP/WordPress, salvo que exista una regla adicional (`.htaccess`, plugin de protección de medios, o `X-Sendfile`/proxy autenticado) que no está presente en ningún archivo revisado. Esto significa que un adjunto de cotización (que puede incluir planos, cédulas, presupuestos u otra información potencialmente sensible del cliente) podría quedar accesible para cualquiera que conozca o adivine/enumere la URL, sin autenticación, incluso siendo el registro de la cotización en sí mismo privado.
- **Severidad:** ALTO (privacidad de datos personales/comerciales de clientes).
- **Archivo:** `inc/quote-form.php`.
- **Estado:** ✅ **Corregido y aprobado en Sprint 8, Entregable 8.3.** Solución implementada: carpeta dedicada `uploads/cotizaciones/` bloqueada a nivel de servidor (`.htaccess`) + renombrado aleatorio del archivo + endpoint PHP autenticado (`current_user_can( 'edit_post', ... )`, nonce, verificación de ruta) para servirlo. Ver `docs/DECISIONS.md` D-096 para el detalle técnico completo y D-097 para las 4 pruebas funcionales reales ejecutadas por el usuario (envío con adjunto, descarga por admin autenticado, bloqueo tras cerrar sesión, bloqueo de acceso directo no autenticado) — todas exitosas, verificadas en un entorno real Apache/2.4.68 (Debian) + PHP 8.2.33. La limitación conocida sobre Nginx (documentada en D-096) permanece como advertencia de compatibilidad para instalaciones futuras sobre ese servidor; no aplica a esta instalación.

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

**Estado de QA-032, QA-033 y QA-034:** ✅ **Corregidos y aprobados en Sprint 8, Entregable 8.4.** Solución implementada: tabla propia `{prefix}ce_form_guards` con reclamación atómica por SQL tanto para el rate-limit (QA-032) como para la idempotencia (QA-034), y rollback del archivo subido + log sin datos personales si falla `wp_insert_post()`/`wp_insert_attachment()` (QA-033). Ver `docs/DECISIONS.md` D-098 (diseño e implementación), D-099 (integración final de `functions.php` y `template-parts/quote-form.php`) y D-101 (aprobación explícita del usuario tras ejecutar las pruebas funcionales reales).

## 4.3 QA-035 a QA-037 — Accesibilidad

### Nuevo hallazgo MEDIO — QA-035
**Slider de testimonios: mecanismo de pausa de autoplay no accesible a teclado/touch.** `ModuleTestimonialSlider` inicia autoplay (`setInterval`) y solo lo detiene con `mouseenter`/`mouseleave` sobre el contenedor. Un usuario que navega exclusivamente con teclado, o en un dispositivo táctil (donde no existe "hover"), no tiene forma de pausar el contenido en movimiento automático. Esto incumple el criterio WCAG 2.2.2 (Pausar, Detener, Ocultar), que exige un mecanismo de pausa accesible para cualquier contenido que se mueva/actualice automáticamente por más de 5 segundos y que no sea esencial.
- **Archivo:** `assets/js/main.js` (`ModuleTestimonialSlider`).
- **Severidad:** MEDIO.
- **Estado:** ✅ **Corregido y aprobado en Sprint 8, Entregable 8.5**, tras pruebas funcionales reales del usuario. Botón de pausa/reanudación accesible (teclado + touch) más pausa automática al recibir foco de teclado, añadidos a `createSliderController()`/`ModuleTestimonialSlider`. `ModuleHeroSlider` no se modificó (decorativo, sin dots/flechas, decisión ya aprobada en D-055). Ver `DECISIONS.md` D-102, D-103.

### Nuevo hallazgo MEDIO — QA-036
**Gestión de foco ausente en menú móvil off-canvas y en modales.** Ni `ModuleMobileNav.open()`/`close()` ni `ModuleModals.open()`/`close()` mueven el foco del teclado hacia el panel/modal al abrir, ni lo devuelven al elemento disparador al cerrar, ni implementan un *focus trap* que mantenga el `Tab` dentro del panel mientras está abierto. Un usuario de teclado puede, tras abrir el menú móvil o un modal, seguir tabulando por elementos del fondo de la página (fuera del panel visible), lo cual es una violación común de accesibilidad en overlays/diálogos (relacionado con el patrón ARIA "Dialog (Modal)").
- **Archivos:** `assets/js/main.js` (`ModuleMobileNav`, `ModuleModals`), `header.php`, `footer.php`.
- **Severidad:** MEDIO.
- **Estado:** ✅ **Corregido y aprobado en Sprint 8, Entregable 8.6**, tras pruebas funcionales reales del usuario. Nueva utilidad compartida `FocusTrap` (`assets/js/main.js`), resolviendo la decisión de diseño que R-4 dejaba abierta (centralizar en vez de corregir por componente): trap de `Tab` + foco inicial + devolución de foco al disparador, aplicado a `ModuleMobileNav` y `ModuleModals`, y retrofit no disruptivo en `ModuleLightbox` (que ya movía el foco manualmente, ahora también con trap de `Tab`). Ver `DECISIONS.md` D-104, D-105.

### Nuevo hallazgo BAJO — QA-037
**`aria-label` estático del botón de menú móvil.** El botón `.ce-nav-toggle` mantiene siempre `aria-label="Abrir menú"` (definido en `header.php`), sin actualizarse a "Cerrar menú" cuando `ModuleMobileNav` lo marca como `is-active`/`aria-expanded="true"`. El estado `aria-expanded` sí se actualiza correctamente, pero el `aria-label` no acompaña ese cambio, lo cual puede confundir a usuarios de lector de pantalla que dependen más del label que del atributo `aria-expanded`.
- **Archivos:** `header.php`, `assets/js/main.js`.
- **Severidad:** BAJO.

---

## 4.4 QA-038 a QA-040 — SEO

### Nuevo hallazgo MEDIO — QA-038
**Ausencia de `<link rel="canonical">`.** `ce_construction_meta_tags()` en `inc/seo.php` emite `og:url` pero no un `<link rel="canonical" href="...">` explícito en `<head>`. Sin canonical explícito, el sitio depende enteramente de la heurística de Google para URLs canónicas, lo cual es un riesgo real de contenido duplicado indexable en escenarios con parámetros de query (paginación de comentarios, UTMs, `?s=` variantes, etc.), especialmente relevante dado que el tema sí tiene paginación (`paginate_links`) en varios archivos sin parámetro canonical explícito hacia la página base.
- **Archivo:** `inc/seo.php`.
- **Estado:** ✅ **Corregido y aprobado en Sprint 8, Entregable 8.5**, tras pruebas funcionales reales del usuario. Nueva función `ce_construction_get_canonical_url()`: delega en `wp_get_canonical_url()` nativo para contenido singular, y construye la URL base limpia por tipo de archivo (post type, taxonomía, autor, fecha, búsqueda, Home) para el resto, reaplicando paginación de forma auto-referencial. `og:url` no se tocó (fuera del alcance literal de este hallazgo). Ver `DECISIONS.md` D-102, D-103.

### Nuevo hallazgo BAJO — QA-039
**Twitter Card incompleto.** Solo se emite `<meta name="twitter:card" content="summary_large_image">`, sin `twitter:title`, `twitter:description` ni `twitter:image` explícitos. La mayoría de los parsers de Twitter/X hacen fallback a las etiquetas `og:*` equivalentes, por lo que el impacto práctico es bajo, pero no está garantizado para todos los consumidores de la Card.
- **Archivo:** `inc/seo.php`.

### Nuevo hallazgo BAJO — QA-040
**Inconsistencia de cobertura de `BreadcrumbList` JSON-LD entre tipos de contenido.** Servicio y Proyecto emiten su propio `BreadcrumbList`; Persona (Equipo), Cliente y BlogPosting no lo hacen, pese a que los 3 sí tienen breadcrumbs HTML equivalentes vía `ce_construction_breadcrumbs()`. No es un bug (nada está roto), pero es una inconsistencia de cobertura SEO entre tipos de contenido estructuralmente similares.
- **Archivo:** `inc/seo.php`.

---

## 4.5 QA-042 — Mejora futura / Performance

### Nuevo hallazgo relacionado — QA-042 (FUTURO)
No hay `<link rel="preconnect">` ni `dns-prefetch` hacia `fonts.googleapis.com`/`fonts.gstatic.com`/`cdnjs.cloudflare.com` en `header.php` ni en `inc/enqueue.php`. Esto es un matiz distinto de QA-027 (que propone auto-hospedar): incluso manteniendo CDN externo, un `preconnect` reduce la latencia de conexión TLS antes de que el navegador descubra esos hosts al parsear el CSS. Impacto esperado: mejora menor de LCP/FCP.
- **Severidad:** FUTURO (mejora, no bloqueante).

### Hallazgo directamente relacionado con integridad de caché — ver QA-030 (sección 12)
El problema de versión de assets congelada (QA-030) tiene una faceta de performance además de operativa: si en algún momento se activa cacheo agresivo a nivel de CDN/navegador con headers de cache largos (`Cache-Control: max-age` alto) para `main.css`/`main.js` — práctica común de optimización de performance — el hecho de que la query string de versión no cambie entre v0.4.1 y v0.7.3 significa que esa optimización de performance **impediría** que los visitantes recurrentes reciban el CSS/JS actualizado del Sprint 7 hasta que expire el caché o se purgue manualmente. Es decir, QA-030 es simultáneamente un hallazgo de integridad de despliegue y un bloqueador latente para cualquier estrategia de performance basada en cache-busting por versión.

---

## 4.6 QA-041 — Nota metodológica

### QA-041 — `page.php` no verificable en la auditoría integral

- **Severidad:** Nota metodológica / no es un defecto confirmado.
- **Estado:** ✅ **Verificado en la re-planificación del Sprint 8: `page.php` NO existe en el repositorio actual.**
- **Archivo:** `page.php` (ausente).
- **Descripción:** `TREE.md` marcaba `page.php` como existente y aprobado (✅), pero al verificar directamente el repositorio/ZIP no se encontró ningún archivo `page.php` en la raíz del tema. La discrepancia de `QA-041` (archivo no disponible en la sesión de la auditoría integral) se confirma ahora como una discrepancia real de `TREE.md`, no solo una limitación de aquella sesión: el archivo simplemente no existe.
- **Impacto funcional real:** Ninguno crítico. Sin `page.php`, WordPress recurre a `index.php` para renderizar páginas estáticas (`is_page()`), y `index.php` sí contempla explícitamente la rama `is_singular() && have_posts()` que cubre páginas — el sitio no se rompe, pero las páginas no tienen una plantilla dedicada (sin las secciones/estilos específicos que sí tienen `single.php`, `single-servicio.php`, etc.).
- **Importante:** Esto **no se corrige como código en esta sesión** (regla explícita: QA-041 solo requiere verificación, no corrección automática sin decisión previa). Se corrige únicamente la documentación (`TREE.md`, que afirmaba incorrectamente que el archivo existía) — ver Sprint 8, sección de hallazgos fuera de Entregable.
- **Decisión pendiente para el usuario:** si `page.php` debe crearse como plantilla dedicada (mejora futura, no un bug) o si se acepta permanentemente el fallback de `index.php` para páginas estáticas. No se incluye en ningún Entregable del Sprint 8 sin tu instrucción explícita.
- **✅ Actualización (sesión de cierre de UX-7):** `page.php` **fue creado** en la fase paralela "Optimización UX / Conversión" (Sprint UX-6, Entregable UX-6.1, aprobado — ver `docs/DECISIONS.md` D-059/D-062), no como resultado de un Entregable del Sprint 8. El archivo ya existe hoy en el repositorio. **QA-041 queda cerrado**, sin ninguna acción pendiente del Sprint 8.

---

## 4.7 QA-043 — Nuevo (registrado en la sesión de cierre del Sprint UX-7)

### QA-043 — `.ce-header__social` sin estilo base (`display`/`gap`/tamaño)

- **Severidad:** 🟢 Bajo / visual.
- **Estado:** ✅ **Corregido en Sprint UX-11** (ver `docs/DECISIONS.md` D-087 y D-090) — no en un Entregable del Sprint 8.
- **Archivo afectado:** `assets/css/main.css`.
- **Origen:** detectado durante la auditoría UX/Arquitectura de la fase "Optimización UX / Conversión" (no durante una revisión del Sprint 8) — ver `docs/DECISIONS.md` D-058 (Decisión 4).
- **Descripción:** `.ce-header__social` (iconos sociales de la barra superior del header) no tenía reglas base de `display`/`gap`/tamaño, a diferencia de `.ce-footer__social`, que sí las tiene — confirmado visualmente en captura, los iconos aparecían apelmazados en la barra superior del header.
- **Clasificación:** corrección de QA (bug visual), no funcionalidad UX nueva — el usuario aprobó explícitamente incorporarla al alcance de UX-11 en vez de esperar al Sprint 8 (D-087), dado que ambos Sprints estaban pausados y el fix es de bajo riesgo.
- **Corrección aplicada:** mismo patrón visual que `.ce-footer__social` (`display:flex`, `gap`, iconos circulares), adaptado al tamaño de la barra superior. Ver D-090 para el detalle técnico completo.

---

# 5. Matriz maestra de priorización

## 🔴 CRÍTICO

| ID | Hallazgo | Estado |
|---|---|---|
| QA-001 | Bypass de validación de tipo de archivo | ✅ Corregido |

## 🟠 ALTO

| ID | Hallazgo | Estado |
|---|---|---|
| QA-002 | Adjuntos no registrados / fuga de disco | ✅ Corregido |
| QA-003 | Retención de datos personales sin límite | ✅ Corregido |
| QA-004 | Sin rate-limiting | ✅ Corregido |
| QA-005 | Contraste WCAG AA | ✅ Corregido |
| QA-006 | Sidebar footer no renderizado | ✅ Corregido |
| QA-007 | `save_post` sin guardia de revisión | ✅ Corregido |
| QA-008 | `CE_THEME_VERSION` hardcodeada | ✅ Corregido históricamente |
| QA-009 | CPT Servicio sin `page-attributes` | ✅ Corregido |
| QA-030 | Cache-busting congelado en 0.4.1 | ✅ Corregido (v0.8.1, Entregable 8.2) |
| QA-031 | Adjuntos potencialmente accesibles por URL directa | ✅ Corregido y aprobado (Sprint 8, Entregable 8.3 — `DECISIONS.md` D-096/D-097) |

## 🟡 MEDIO

| ID | Hallazgo | Estado |
|---|---|---|
| QA-010 | Filtro `defer` redundante | ✅ Corregido (Entregable 8.1) |
| QA-011 | `postMessage` sin preview JS | ✅ Corregido (Entregable 8.1) |
| QA-012 | Consultas relacionadas sin caché | ✅ Corregido y aprobado (Entregable 8.5, `DECISIONS.md` D-102/D-103) |
| QA-013 | Duplicación CSS/reset | 🟡 Parcial (Entregable 8.1 — solo comentario) |
| QA-014 | JSON-LD sin endurecimiento contra `</script>` | ✅ Corregido (Entregable 8.1) |
| QA-015 | `$attachment_name` sin uso | ✅ Verificado — ya no se reproduce (Entregable 8.1) |
| QA-016 | Script inline de metabox sin dependencia formal | ✅ Corregido y aprobado (Entregable 8.5, `DECISIONS.md` D-102/D-103) |
| QA-017 | Skip-link sin `tabindex="-1"` | ✅ Corregido (Entregable 8.1) |
| QA-018 | Header top sin adaptación responsive | ✅ Corregido |
| QA-032 | Race condition del rate-limit | ✅ Corregido y aprobado (Entregable 8.4, `DECISIONS.md` D-098/D-099/D-101) |
| QA-033 | Archivo huérfano si falla `wp_insert_post()` | ✅ Corregido y aprobado (Entregable 8.4, `DECISIONS.md` D-098/D-099/D-101) |
| QA-034 | Sin idempotencia de envíos | ✅ Corregido y aprobado (Entregable 8.4, `DECISIONS.md` D-098/D-099/D-101) |
| QA-035 | Autoplay sin pausa accesible | ✅ Corregido y aprobado (Entregable 8.5, `DECISIONS.md` D-102/D-103) |
| QA-036 | Sin gestión de foco en overlays | ✅ Corregido y aprobado (Entregable 8.6, `DECISIONS.md` D-104/D-105) |
| QA-038 | Sin `<link rel="canonical">` | ✅ Corregido y aprobado (Entregable 8.5, `DECISIONS.md` D-102/D-103) |

## 🟢 BAJO

| ID | Hallazgo | Estado |
|---|---|---|
| QA-019 | Reutilización de `.ce-modal__close` fuera de contexto | ⬜ Abierto |
| QA-020 | Flechas de slider con un solo testimonio | ⬜ Abierto |
| QA-021 | Discrepancia de versión documentada históricamente | ⬜/impacto relacionado con QA-030 |
| QA-022 | Concatenación de HTML en JS del admin | ⬜ Abierto |
| QA-023 | Verificación positiva de `rel="noopener"` | ℹ️ Sin defecto confirmado |
| QA-037 | `aria-label` estático del menú móvil | ⬜ Abierto |
| QA-039 | Twitter Card incompleto | ⬜ Abierto |
| QA-040 | `BreadcrumbList` JSON-LD inconsistente | ⬜ Abierto |
| QA-041 | `page.php` no existe en el repositorio (verificado) | ✅ Cerrado — `page.php` fue creado después, en Sprint UX-6 (fuera del Sprint 8) |
| QA-043 | `.ce-header__social` sin estilo base | ✅ Corregido en Sprint UX-11 (D-087/D-090) |

## 🔵 MEJORAS FUTURAS

| ID | Hallazgo | Estado |
|---|---|---|
| QA-024 | Breadcrumbs HTML/JSON-LD duplicados | ⬜ Mejora |
| QA-025 | `@id` compartido de Organization | ⬜ Mejora |
| QA-026 | Preconnect de fuentes | ⬜ Mejora |
| QA-027 | Auto-hospedaje de Google Fonts | ⬜ Mejora |
| QA-028 | Auto-hospedaje de Font Awesome | ⬜ Mejora |
| QA-029 | Evaluar unificación de CPTs simples | ⬜ Mejora; recomendación histórica: no unificar |
| QA-042 | Sin `preconnect`/`dns-prefetch` hacia CDNs | ⬜ Mejora futura |

---

# 6. Cambios de arquitectura recomendados

Las siguientes recomendaciones proceden de la auditoría integral. **No fueron implementadas durante la auditoría.**

### R-1 — Automatizar el versionado de assets
Relacionado con QA-030. Eliminar la dependencia de actualizar manualmente `CE_THEME_VERSION` para cache-busting.

### R-2 — Servir adjuntos del formulario mediante endpoint autenticado
Relacionado con QA-031. Evitar que documentos sensibles de cotizaciones queden expuestos directamente desde `wp-content/uploads/`.

### R-3 — Rate-limiting atómico y caché de "relacionados"
Relacionado con QA-032 y QA-012. Evaluar una estrategia que reduzca condiciones de carrera y consultas repetidas.

### R-4 — Gestión de foco centralizada para overlays
Relacionado con QA-036. Centralizar el comportamiento de foco para menú móvil, modales y lightbox.

### R-5 — Consolidar canonical y completar Twitter Card / BreadcrumbList
Relacionado con QA-038, QA-039 y QA-040.

---

# 7. Concurrencia y carga

La auditoría integral indicó que el análisis de concurrencia fue **estático**, no una prueba de carga real.

Hallazgos relevantes:

- QA-032: condición de carrera en el rate-limit del formulario.
- El flujo general no mostró un bypass total del nonce ni una superficie evidente de SQL injection.
- No debe interpretarse este QA como una certificación de capacidad para un número concreto de usuarios simultáneos.

Para una validación real de concurrencia será necesario un entorno WordPress/PHP operativo y una prueba de carga controlada.

---

# 8. Seguridad

La revisión histórica verificó como corregidos QA-001 a QA-004 y QA-007.

La auditoría integral añade especialmente:

- **QA-031:** privacidad de archivos adjuntos.
- **QA-032:** condición de carrera del rate-limit.
- **QA-033:** archivo huérfano ante fallo de inserción.
- **QA-034:** ausencia de idempotencia.

No se reportó una nueva vulnerabilidad crítica de SQL injection, bypass de nonce o XSS reflejado en la auditoría integral.

---

# 9. Accesibilidad

Hallazgos relevantes:

- QA-005: contraste histórico corregido.
- QA-017: skip-link corregido (Sprint 8, Entregable 8.1).
- QA-018: header responsive corregido.
- QA-035: autoplay de testimonios — pausa accesible corregida y aprobada (Sprint 8, Entregable 8.5).
- QA-036: gestión de foco de overlays corregida y aprobada (Sprint 8, Entregable 8.6).
- QA-037: `aria-label` estático del menú móvil.

---

# 10. SEO

Hallazgos relevantes:

- QA-014: endurecimiento de JSON-LD corregido (Sprint 8, Entregable 8.1).
- QA-038: canonical implementado (Sprint 8, Entregable 8.5, pendiente de pruebas funcionales).
- QA-039: Twitter Card incompleto.
- QA-040: BreadcrumbList JSON-LD inconsistente.
- QA-024/QA-025: mejoras históricas relacionadas con breadcrumbs y `Organization`.

---

# 11. Limitaciones de la auditoría integral

La auditoría fuente `QA_REPORT_2.md` dejó explícitamente documentado:

1. No hubo conexión directa al repositorio GitHub durante esa auditoría.
2. La revisión fue realizada sobre los archivos disponibles en el contexto de aquella sesión.
3. No se ejecutó una prueba de carga real.
4. No se ejecutó un escáner automatizado de seguridad.
5. `page.php` no estuvo disponible para revisión directa, generando QA-041 como nota metodológica.
6. `search.php` se consideró una ausencia intencional porque `index.php` cubre `is_search()`.

Por tanto, este QA maestro **no debe presentarse como una certificación de seguridad, performance o concurrencia real**. Es una auditoría de código/documentación con análisis estático.

---

# 12. Regla para el Sprint 8

Este `QA_REPORT.md` es la **fuente única de referencia para priorizar el Sprint 8**.

Reglas:

- No corregir automáticamente todos los hallazgos.
- Cada Entregable debe definir exactamente qué QA corrige.
- Los cambios arquitectónicos deben identificarse antes de implementarse.
- No realizar refactorizaciones generales bajo la excusa de un hallazgo QA.
- Mantener las correcciones históricas que ya están verificadas.
- QA-041 requiere verificación, no corrección automática.
- Las mejoras futuras permanecen fuera del alcance hasta aprobación.

---

# 13. Cierre

El QA maestro consolida los dos reportes entregados y mantiene la trazabilidad de los hallazgos históricos y de la auditoría integral posterior al Sprint 7.

---

# 14. QA-044 — Hallazgo puntual post Sprint 8 (reportado por el usuario)

### QA-044 — Etiqueta visual del adjunto no se limpiaba tras un envío exitoso del formulario

- **Severidad:** Bajo (visual/UX, sin impacto de seguridad ni de datos — el archivo real sí se limpiaba, solo su representación visual no).
- **Estado:** ✅ Corregido — ver `docs/DECISIONS.md` D-100.
- **Archivo:** `assets/js/main.js` (`ModuleQuoteForm`).
- **Descripción:** tras un envío exitoso, sin recargar la página, la etiqueta del campo de adjunto seguía mostrando el nombre del último archivo seleccionado aunque `form.reset()` ya había vaciado el input real — `form.reset()` no dispara el evento `change` del que dependía la etiqueta visual.
- **Corrección:** `updateFileLabel()` ahora también restaura el texto por defecto cuando no hay archivo seleccionado, y se invoca explícitamente tras `form.reset()`.
- **Reportado por:** el usuario, al ejecutar pruebas manuales sobre el Entregable 8.4 (no relacionado con QA-032/033/034).

**Total documentado:** QA-001 a QA-042.

**Correcciones históricas confirmadas:** QA-001 a QA-009 y QA-018.

**Hallazgos de prioridad ALTA de la auditoría integral:** QA-030 corregido en Sprint 8, Entregable 8.2 (`DECISIONS.md` D-044); QA-031 corregido y aprobado en Sprint 8, Entregable 8.3 (`DECISIONS.md` D-096/D-097). No quedan hallazgos Altos abiertos a la fecha de esta actualización.

**Nuevos hallazgos MEDIOS:** QA-032, QA-033 y QA-034 corregidos y aprobados en Sprint 8, Entregable 8.4 (`DECISIONS.md` D-098/D-099/D-101). QA-012, QA-016, QA-035 y QA-038 corregidos y aprobados en Sprint 8, Entregable 8.5 (`DECISIONS.md` D-102/D-103). QA-036 corregido y aprobado en Sprint 8, Entregable 8.6 (`DECISIONS.md` D-104/D-105). No quedan hallazgos Medios abiertos del Sprint 8 — el resto de hallazgos pendientes (QA-019 a QA-022, QA-037, QA-039, QA-040) son de severidad Baja, agrupados en el Entregable 8.7.

**Nuevos hallazgos BAJOS:** QA-037, QA-039 y QA-040.

**Nota metodológica:** QA-041.

**Mejora futura nueva:** QA-042.

No se implementa ninguna corrección como parte de esta consolidación documental.
