# CE Construction — QA_REPORT.md
### Sprint de QA e Integración

> Este reporte es el resultado de una revisión exhaustiva y verificada línea por línea de todo el código generado hasta el Sprint 3 (Módulo Servicios). Ningún archivo fue modificado durante esta auditoría. Cada hallazgo fue confirmado contra el código real en disco (no se listan sospechas no verificadas) — incluyendo cálculo empírico de contraste WCAG, verificación de balance de sintaxis, y trazado de flujo de datos en el formulario de cotización.

**Alcance auditado:** los 31 archivos PHP/CSS/JS del tema (Sprints 1-3), incluyendo `functions.php`, todos los `inc/*.php`, `header.php`, `footer.php`, `front-page.php`, `archive-servicio.php`, `single-servicio.php`, todos los `template-parts/*.php`, `style.css`, `assets/css/main.css` y `assets/js/main.js`.

**Metodología:** lectura completa de cada archivo, verificación de balance de llaves/paréntesis, `node --check` sobre JS, trazado manual de flujos de datos sensibles (formulario de cotización, metaboxes, Customizer), y cálculo numérico de contraste de color (fórmula WCAG 2.x) para los pares de color realmente usados en texto.

---

## Resumen ejecutivo

| Severidad | Cantidad de hallazgos |
|---|---|
| 🔴 Crítico | 1 |
| 🟠 Alto | 8 |
| 🟡 Medio | 9 |
| 🟢 Bajo | 5 |
| 🔵 Mejora futura | 6 |
| **Total** | **29** |

El hallazgo crítico es un **bypass real de validación de tipo de archivo** en el formulario de cotización. Los hallazgos altos incluyen una **fuga de disco sin límite** (archivos adjuntos nunca registrados ni limpiados), ausencia de **rate-limiting** en un endpoint público, un **fallo de contraste WCAG AA verificado numéricamente**, y una **funcionalidad de administración silenciosamente rota** (sidebar de footer sin renderizar). Ninguno de estos hallazgos ha sido corregido en esta entrega, conforme a lo solicitado.

---

## 🔴 CRÍTICO

### QA-001 — Bypass de validación de tipo de archivo en el formulario de cotización
- **Archivo afectado:** `inc/quote-form.php` (función `ce_construction_handle_quote_form()`, líneas ~133-143)
- **Descripción:** La validación de tipo de archivo adjunto compara `$_FILES['attachment']['type']` (el MIME type que el **navegador del visitante** envía en la cabecera `Content-Type` del `multipart/form-data`, completamente falsificable con herramientas como `curl -F`) contra el array `$allowed_types`. Además, `wp_check_filetype()` solo verifica que la extensión del archivo esté en la lista **global** de extensiones permitidas por WordPress (docenas de extensiones: `.zip`, `.csv`, `.doc`, `.mp3`, etc., no solo PDF/JPG/PNG/WEBP), y el código nunca compara `$file_type['ext']` contra el whitelist real (`pdf`, `jpg`, `jpeg`, `png`, `webp`) que el propio comentario del código dice implementar.
- **Riesgo:** Un atacante puede renombrar un archivo con cualquier extensión permitida globalmente por WordPress (ej. `archivo.zip`) y falsificar manualmente la cabecera `Content-Type: application/pdf` en la petición HTTP. Ambas comprobaciones del código pasarían, permitiendo subir tipos de archivo no previstos por el diseño ("Debe validar... PDF, JPG, PNG, WEBP" según el brief original). Esto amplía la superficie de ataque de almacenamiento de archivos no deseados en el servidor.
- **Recomendación:**
  1. Reemplazar `wp_check_filetype()` por `wp_check_filetype_and_ext()`, que además inspecciona la firma real del archivo (no solo el nombre) para imágenes.
  2. Comparar `$file_type['ext']` contra un whitelist explícito `array( 'pdf', 'jpg', 'jpeg', 'png', 'webp' )`, no solo verificar que no esté vacío.
  3. Eliminar por completo la dependencia del `$_FILES[...]['type']` enviado por el cliente para la decisión de seguridad; usarlo como máximo de forma informativa.
- **Impacto de corregirlo:** Cambio acotado a ~10 líneas dentro de una única función ya existente. Bajo riesgo de romper el flujo (la extensión real seguiría siendo PDF/JPG/PNG/WEBP para el 100% de los envíos legítimos). Cierra una vulnerabilidad de validación real.

---

## 🟠 ALTO

### QA-002 — Archivos adjuntos nunca registrados como adjuntos de WordPress: fuga de disco sin límite
- **Archivo afectado:** `inc/quote-form.php` (líneas ~145-155)
- **Descripción:** `wp_handle_upload()` mueve el archivo físicamente a `wp-content/uploads/`, pero el código nunca llama a `wp_insert_attachment()` para registrarlo en la Media Library ni lo asocia al post `cotizacion` recién creado (solo se guarda la ruta en un meta `_ce_attachment_path`, como string plano).
- **Riesgo:** El archivo queda huérfano en el sistema de ficheros: invisible en la Media Library, sin ningún mecanismo de borrado (ni siquiera al borrar la cotización desde el admin, ya que WordPress solo limpia adjuntos registrados como `attachment` al eliminar sus posts padre). En un sitio con tráfico sostenido, esto provoca crecimiento indefinido del uso de disco sin ninguna forma de auditar o purgar los archivos desde wp-admin.
- **Recomendación:** Registrar el archivo con `wp_insert_attachment()` vinculado al `post_id` de la cotización (`post_parent`), y usar `wp_generate_attachment_metadata()` + `wp_update_attachment_metadata()`. Esto además permite que al borrar la cotización (o con un cron de limpieza) el archivo se elimine automáticamente vía los hooks estándar de WordPress.
- **Impacto de corregirlo:** Cambio acotado a la misma función; añade ~8-10 líneas. Sin riesgo de romper el envío de correo (el adjunto seguiría funcionando igual, ya que `wp_mail()` solo necesita la ruta física, que se conserva).

### QA-003 — Retención de datos personales sin límite (riesgo de cumplimiento)
- **Archivo afectado:** `inc/quote-form.php` (CPT `cotizacion`)
- **Descripción:** Cada envío del formulario almacena nombre, correo, teléfono y mensaje de forma permanente en un CPT sin ningún mecanismo de expiración, anonimización o exportación/eliminación asistida por el propio visitante.
- **Riesgo:** En jurisdicciones con normativa de protección de datos (GDPR, Ley 1581/2012 en Colombia, etc.), la retención indefinida de datos personales sin política de expiración documentada es un riesgo de cumplimiento, no solo técnico.
- **Recomendación:** Definir junto al cliente una política de retención (ej. 12-24 meses), implementable con un cron (`wp_scheduled_delete` extendido o un evento propio) que archive/elimine cotizaciones antiguas. Documentar la política en un aviso de privacidad del sitio.
- **Impacto de corregirlo:** No es un cambio de código aislado — requiere una decisión de negocio del cliente antes de implementar. Se recomienda tratarlo como un módulo propio en un sprint futuro, no como un parche rápido.

### QA-004 — Sin rate-limiting en el endpoint AJAX público del formulario
- **Archivo afectado:** `inc/quote-form.php` (`wp_ajax_nopriv_ce_submit_quote`)
- **Descripción:** El único control anti-abuso es un honeypot (campo oculto). No existe limitación de frecuencia por IP/sesión, ni integración con un CAPTCHA.
- **Riesgo:** Un atacante con un script simple (que omita rellenar el honeypot) puede enviar cientos de solicitudes, generando spam de correos vía `wp_mail()`, saturando la bandeja de entrada configurada y creando cientos de posts `cotizacion` y (combinado con QA-002) archivos huérfanos en disco.
- **Recomendación:** Añadir un throttle server-side simple vía `transient` keyed por IP (ej. máximo 3 envíos cada 10 minutos por IP), y dejar preparado un punto de integración para reCAPTCHA/Turnstile si el volumen de spam lo justifica.
- **Impacto de corregirlo:** Cambio acotado (~10-15 líneas) al inicio de `ce_construction_handle_quote_form()`. Sin impacto en el flujo legítimo salvo el caso extremo de un usuario reenviando el formulario muy rápido repetidamente.

### QA-005 — Fallo verificado de contraste WCAG AA en el color secundario sobre fondo claro
- **Archivo afectado:** `assets/css/main.css` (`.ce-eyebrow`, `.ce-card__link:hover`, `.ce-sidebar__link:hover`, y cualquier texto que use `color: var(--ce-color-secondary)` sobre fondo blanco/`neutral-100`)
- **Descripción:** Se calculó el ratio de contraste real (fórmula WCAG 2.x) entre `--ce-color-secondary` (`#D98E29`) y blanco: **2.67:1**. El mínimo WCAG AA para texto normal es 4.5:1, y para texto grande (≥18.66px negrita) es 3:1. El texto de `.ce-eyebrow` (12.8px, negrita) no califica como "texto grande", por lo que **no cumple el mínimo AA en ningún escenario**.
- **Riesgo:** Usuarios con baja visión o daltonismo (deuteranopía/protanopía, donde el naranja pierde aún más diferenciación) pueden no leer estos textos con claridad. Es un hallazgo de accesibilidad real y medible, no una sospecha.
- **Recomendación:** Para texto sobre fondo claro, usar una variante más oscura del color secundario (ej. `--ce-color-secondary-dark: #B8721A`, que sube el contraste) o reservar `--ce-color-secondary` puro para fondos oscuros/iconos/bordes, nunca para texto pequeño sobre blanco. `--ce-color-secondary` sobre `--ce-color-primary` (fondo oscuro) sí cumple (5.48:1, verificado).
- **Impacto de corregirlo:** Cambio de una sola variable CSS o de las reglas puntuales que usan `--ce-color-secondary` como `color` sobre fondo claro (~4-5 selectores). Sin impacto estructural.

### QA-006 — Sidebar "Footer - Columna 1" registrado pero nunca renderizado (funcionalidad de admin silenciosamente rota)
- **Archivo afectado:** `inc/setup.php` (registro) / `footer.php` (uso)
- **Descripción:** `ce_construction_widgets_init()` registra dos áreas de widgets, `footer-1` y `footer-2`. `footer.php` solo llama a `dynamic_sidebar( 'footer-2' )`. `footer-1` no se invoca en ningún archivo del tema.
- **Riesgo:** Si un administrador entra a Apariencia → Widgets y agrega contenido a "Footer - Columna 1" (una opción visible y aparentemente funcional en el panel), **ese contenido nunca se mostrará en el sitio**, sin ningún error visible. Es el tipo de bug más difícil de detectar para un cliente no técnico, porque el panel de administración no indica que la columna esté "rota".
- **Recomendación:** O bien renderizar `dynamic_sidebar( 'footer-1' )` en la posición correspondiente de `footer.php` (probablemente destinado a acompañar la columna de "Enlaces" o "Contacto"), o eliminar su registro en `inc/setup.php` si se decide que solo una columna de widgets es necesaria.
- **Impacto de corregirlo:** Trivial (una línea) si se decide renderizarlo; requiere decidir primero en qué posición del footer debería aparecer.

### QA-007 — `save_post` sin guardia `wp_is_post_revision()`: metadatos pueden escribirse en revisiones
- **Archivo afectado:** `inc/meta-boxes.php` (`ce_construction_save_meta_boxes()`)
- **Descripción:** La función verifica `DOING_AUTOSAVE` pero no verifica `wp_is_post_revision( $post_id )`. WordPress dispara el hook `save_post` también para el post de tipo `revision` que se crea internamente al actualizar un post con revisiones habilitadas (comportamiento documentado de WordPress Core, no específico de este tema).
- **Riesgo:** Cada vez que se actualiza un Servicio/Proyecto/Testimonio/Equipo/Cliente con revisiones activas, es probable que los metadatos (`_ce_icono_fa`, `_ce_proyecto_cliente`, etc.) se escriban también sobre el ID del post de revisión, no solo sobre el post real. Esto no rompe el front-end (las revisiones no se muestran), pero genera filas de meta redundantes/incorrectas en la base de datos y puede confundir a cualquier consulta futura que itere revisiones.
- **Recomendación:** Añadir `if ( wp_is_post_revision( $post_id ) ) { return; }` justo después de la comprobación de `DOING_AUTOSAVE`, siguiendo el patrón estándar recomendado por el Codex de WordPress para guardado de metaboxes.
- **Impacto de corregirlo:** Una línea, cero riesgo de romper el guardado legítimo — es exactamente el patrón que WordPress documenta como obligatorio y que aquí falta.

### QA-008 — `CE_THEME_VERSION` hardcodeada y nunca sincronizada con las versiones reales del proyecto
- **Archivo afectado:** `functions.php` (constante `CE_THEME_VERSION`), `style.css` (cabecera `Version:`)
- **Descripción:** Ambas declaran `1.0.0` desde el Sprint 1 y no se ha actualizado pese a que el proyecto ya documenta v0.3.0 en `CHANGELOG.md`. Esta constante es la que se usa como parámetro `$ver` en **todos** los `wp_enqueue_style()`/`wp_enqueue_script()` de `inc/enqueue.php`.
- **Riesgo:** Cache-busting roto: si el sitio ya está en producción con un CDN o cache de navegador agresivo, actualizar `assets/css/main.css` o `assets/js/main.js` en un despliegue **no invalida la caché** de esos archivos para visitantes que ya los tengan cacheados, porque la URL enqueada (`main.css?ver=1.0.0`) no cambia entre despliegues.
- **Recomendación:** Sincronizar `CE_THEME_VERSION` con cada entrega (idealmente automatizado leyendo la cabecera `Version` de `style.css` vía `wp_get_theme()->get('Version')` en vez de una constante duplicada), o usar `filemtime()` sobre los archivos de `assets/` durante desarrollo para invalidar caché automáticamente en cada cambio.
- **Impacto de corregirlo:** Cambio de bajo riesgo, pero requiere decidir una convención (constante manual vs. `filemtime()` vs. `wp_get_theme()`) antes de aplicarlo de forma consistente en todos los sprints futuros.

### QA-009 — CPT Servicio no soporta `page-attributes`: el ordenamiento por `menu_order` no es funcional
- **Archivo afectado:** `inc/cpt-servicios.php` (registro del CPT) / `template-parts/services.php` (consulta)
- **Descripción:** `template-parts/services.php` ordena la consulta de servicios con `'orderby' => 'menu_order', 'order' => 'ASC'`, pero `register_post_type( 'servicio', ... )` no incluye `'page-attributes'` en `supports`. Sin ese soporte, WordPress no muestra el campo "Orden" en el editor, por lo que el administrador **no tiene ninguna forma de definir ese orden** desde la interfaz.
- **Riesgo:** Todos los servicios tendrán `menu_order = 0` de forma permanente (salvo que se edite directamente en base de datos), por lo que el `ORDER BY menu_order ASC` es efectivamente un no-op, y el orden real de aparición dependerá del comportamiento no garantizado de MySQL ante valores empatados (normalmente el orden de inserción, pero no está garantizado por el estándar SQL).
- **Recomendación:** Añadir `'page-attributes'` a `supports` en `inc/cpt-servicios.php`, lo que habilita el campo "Orden" nativo de WordPress en el editor de Servicios.
- **Impacto de corregirlo:** Una sola línea en un archivo ya implementado (requeriría autorización explícita para tocarlo, dado que el proyecto tiene la regla de "no reescribir módulos ya implementados" — se documenta aquí como hallazgo, la corrección queda pendiente de tu aprobación).

---

## 🟡 MEDIO

### QA-010 — Filtro `script_loader_tag` redundante con el soporte nativo de `wp_script_add_data( 'defer' )`
- **Archivo afectado:** `inc/enqueue.php`
- **Descripción:** El código llama a `wp_script_add_data( 'ce-construction-main', 'defer', true )` (soporte nativo de WordPress Core desde la 6.3, que ya añade el atributo `defer` automáticamente al tag del script) **y además** define un filtro manual `ce_construction_add_defer_attribute()` sobre `script_loader_tag` que hace exactamente lo mismo vía `str_replace()`.
- **Riesgo:** Duplicación de responsabilidad. Si en el futuro WordPress cambia el formato interno del tag `<script>` generado (por ejemplo, para soportar `type="module"` o atributos adicionales), el `str_replace( ' src', ' defer src', $tag )` manual es frágil y podría producir un tag mal formado, mientras que el soporte nativo se mantiene por el Core. No es un bug activo hoy (ambos coinciden en el resultado), pero es deuda técnica innecesaria.
- **Recomendación:** Eliminar el filtro manual `ce_construction_add_defer_attribute` y confiar únicamente en `wp_script_add_data()`, dado que el `Requires at least: 6.0` declarado en `style.css` ya es compatible (defer vía `wp_script_add_data` funciona desde WP 6.3; si se requiere soporte para WP 6.0-6.2, debe documentarse esa excepción explícitamente).
- **Impacto de corregirlo:** Elimina ~7 líneas. Cero riesgo funcional si el mínimo de WordPress soportado se confirma en 6.3+.

### QA-011 — Transporte `postMessage` en 3 ajustes de color sin script de vista previa correspondiente
- **Archivo afectado:** `inc/customizer.php` (`ce_color_primary`, `ce_color_secondary`, `ce_color_accent`)
- **Descripción:** Estos 3 ajustes declaran `'transport' => 'postMessage'`, lo cual le dice al Customizer "no recargues el iframe completo, yo (JS) actualizaré la vista previa". Sin embargo, no existe ningún script enganchado a `customize_preview_init` que escuche `wp.customize( 'ce_color_primary', function(value){...})` para aplicar el cambio en vivo.
- **Riesgo:** Al cambiar estos 3 colores en el Customizer, la vista previa **no se actualiza en tiempo real** (a diferencia del resto de ajustes, que usan `'refresh'` por defecto y sí recargan el iframe). El administrador podría pensar que el cambio no funcionó, cuando en realidad sí se guardará correctamente al publicar (`ce_construction_customizer_css()` sí lee el valor guardado en cualquier carga de página normal).
- **Recomendación:** La corrección más simple es quitar `'transport' => 'postMessage'` de estos 3 ajustes (dejar el default `'refresh'`), a menos que se quiera invertir el esfuerzo de escribir un script de vista previa dedicado para una actualización verdaderamente instantánea sin recarga.
- **Impacto de corregirlo:** Cambio de una palabra por ajuste (quitar la línea `transport`). Mejora inmediata de la experiencia de administración sin ningún riesgo.

### QA-012 — Consultas de "relacionados" sin caché: hasta 4 `WP_Query` adicionales por carga de `single-servicio.php`
- **Archivo afectado:** `inc/helpers.php` (`ce_get_related_services()`, `ce_get_related_projects()`)
- **Descripción:** A diferencia de `ce_cpt_has_posts()` (que sí usa una caché estática por request), estas dos funciones ejecutan una consulta principal + una consulta de fallback cada vez que se invocan, sin memoización ni transient.
- **Riesgo:** En el peor caso (servicio sin categoría o sin suficientes relacionados), `single-servicio.php` puede disparar hasta 4 consultas `WP_Query` adicionales en una sola carga de página, además de las ya existentes. En un sitio con tráfico alto y sin object cache persistente, esto es una carga de base de datos evitable.
- **Recomendación:** Envolver el resultado de ambas funciones en un `transient` de corta duración (ej. 1 hora, invalidado por `save_post` del CPT correspondiente), o al menos aplicar el mismo patrón de caché estática por request que ya usa `ce_cpt_has_posts()` para evitar recalcular si la función se invoca más de una vez en el mismo request.
- **Impacto de corregirlo:** Cambio acotado a `inc/helpers.php`; requiere decidir estrategia de invalidación de caché antes de implementar (para no mostrar datos obsoletos tras editar contenido).

### QA-013 — Duplicación de variables CSS y reset entre `style.css` y `assets/css/main.css`
- **Archivo afectado:** `style.css`, `assets/css/main.css`
- **Descripción:** Ambos archivos definen el mismo bloque `:root { --ce-color-primary: ...; }` y reglas de reset casi idénticas (`*, *::before, *::after { box-sizing: border-box; }`, `body { margin: 0; ... }`, etc.). El comentario en `style.css` afirma que `main.css` reutiliza las variables "mediante `@import`", pero **no existe ningún `@import` real** en ninguno de los dos archivos — es documentación incorrecta.
- **Riesgo:** No es un bug funcional (ambos bloques declaran los mismos valores, por lo que el resultado visual es idéntico), pero es peso muerto: se descarga y parsea el mismo bloque de reglas CSS dos veces en cada carga de página, y el comentario desalinea a cualquier desarrollador futuro que confíe en él para entender la arquitectura.
- **Recomendación:** Corregir el comentario de `style.css` para reflejar la realidad (ambos archivos se cargan por separado vía `wp_enqueue_style`, sin `@import`), y evaluar si `main.css` debería eliminar su bloque `:root`/reset duplicado y depender exclusivamente de que `style.css` se cargue primero (ya está declarado como dependencia en `inc/enqueue.php`).
- **Impacto de corregirlo:** Bajo riesgo si se hace con cuidado (hay que verificar que ningún valor difiera sutilmente entre ambos bloques antes de eliminar uno).

### QA-014 — JSON-LD sin endurecimiento contra secuencias `</script>`
- **Archivo afectado:** `inc/seo.php` (todas las funciones que hacen `echo '<script type="application/ld+json">' . wp_json_encode(...)`)
- **Descripción:** El contenido de los bloques JSON-LD (título del servicio, descripción/excerpt) proviene de contenido editorial (confiado, escrito por el administrador), pero se inyecta en el HTML sin ninguna protección adicional contra una secuencia literal `</script>` que cerraría prematuramente el bloque `<script>` si apareciera dentro de un valor.
- **Riesgo:** Bajo en la práctica (requiere que un editor de confianza escriba deliberadamente esa secuencia en un campo de texto), pero es una buena práctica de defensa en profundidad ausente.
- **Recomendación:** Usar `wp_json_encode( $schema, JSON_UNESCAPED_SLASHES )` de forma consistente, o aplicar `str_replace( '</script>', '<\/script>', $json )` antes de imprimir.
- **Impacto de corregirlo:** Cambio trivial y de cero riesgo, aplicable a las 4 funciones de schema existentes en `inc/seo.php`.

### QA-015 — Variable `$attachment_name` calculada pero nunca utilizada (código muerto)
- **Archivo afectado:** `inc/quote-form.php` (línea ~126, ~152)
- **Descripción:** `$attachment_name = basename( $uploaded_file['file'] );` se calcula pero no se usa en ningún punto posterior del archivo (no se guarda como meta, no se incluye en el correo).
- **Riesgo:** Ninguno funcional; es deuda técnica menor y puede confundir a quien mantenga el archivo en el futuro, haciendo pensar que el nombre del archivo se está aprovechando en algún lado cuando no es así.
- **Recomendación:** Eliminar la variable, o bien aprovecharla realmente (ej. incluir el nombre original del archivo en el cuerpo del correo o como columna adicional en el admin de cotizaciones).
- **Impacto de corregirlo:** Trivial.

### QA-016 — `<script>` inline embebido directamente en una función de renderizado de metabox (sin enqueue ni dependencia declarada)
- **Archivo afectado:** `inc/meta-boxes.php` (`ce_render_proyecto_gallery()`)
- **Descripción:** El selector de galería de Proyecto imprime un bloque `<script>` con jQuery directamente dentro del HTML del metabox, en lugar de usar `wp_enqueue_script()` + `wp_add_inline_script()` con `'jquery'` declarado como dependencia explícita.
- **Riesgo:** Funciona hoy porque wp-admin carga jQuery por defecto en las pantallas de edición de posts, pero no hay ninguna declaración formal de esa dependencia. Si algún plugin de terceros desregistra o modifica la carga de jQuery en el admin (raro, pero posible), este script fallaría silenciosamente sin ningún aviso en el código que indique la causa.
- **Recomendación:** Mover el script a un archivo `assets/js/admin-gallery.js`, enqueado condicionalmente (tal como ya hace `ce_construction_admin_enqueue()` con `wp_enqueue_media()`) con `array('jquery')` como dependencia declarada.
- **Impacto de corregirlo:** Requiere crear un archivo nuevo y ajustar el enqueue condicional ya existente; riesgo bajo, mejora la robustez y el cumplimiento de WPCS (WordPress recomienda no imprimir `<script>` inline salvo con `wp_add_inline_script`).

### QA-017 — Salto de foco no garantizado en el enlace "Saltar al contenido principal"
- **Archivo afectado:** `header.php`
- **Descripción:** El skip-link apunta a `#ce-main-content`, que corresponde a `<main id="ce-main-content">`, pero ese elemento no tiene `tabindex="-1"`.
- **Riesgo:** En varios navegadores, activar un enlace `href="#id"` hacia un elemento sin `tabindex` desplaza la vista (scroll) pero **no mueve el foco de teclado** hacia ese elemento, lo cual reduce la utilidad real del skip-link para usuarios de teclado/lectores de pantalla (el objetivo principal de este patrón de accesibilidad).
- **Recomendación:** Añadir `tabindex="-1"` a `<main id="ce-main-content">` en `header.php`.
- **Impacto de corregirlo:** Trivial, un atributo. Mejora real y verificable de accesibilidad.

### QA-018 — Barra superior del header sin adaptación responsive explícita
- **Archivo afectado:** `assets/css/main.css` (`.ce-header__top`)
- **Descripción:** Se confirmó (búsqueda exhaustiva en el archivo) que no existe ninguna regla `@media` que oculte, apile o adapte `.ce-header__top` (teléfono + correo + horario + iconos sociales, todo en una fila flex) para viewports pequeños.
- **Riesgo:** En viewports de 320-375px (móviles de gama baja/media), la combinación de teléfono + correo + horario + redes sociales en una sola fila flex sin `flex-wrap` puede desbordar horizontalmente o comprimirse de forma ilegible.
- **Recomendación:** Ocultar `.ce-header__top` por debajo de un breakpoint (ej. `768px`) mostrando esa información solo en el menú móvil off-canvas (que además ya podría incluirla), o aplicar `flex-wrap: wrap` con un layout apilado en móvil.
- **Impacto de corregirlo:** Cambio acotado a `assets/css/main.css`, sin riesgo de romper el layout de escritorio.

---

## 🟢 BAJO

### QA-019 — Generación de `<img>` vía concatenación de strings en JS del admin (no explota HTML de forma segura)
- **Archivo afectado:** `inc/meta-boxes.php` (script inline en `ce_render_proyecto_gallery()`)
- **Descripción:** `preview.append('<img src="'+a.attributes.sizes.thumbnail.url+'" ...')` construye HTML por concatenación de strings en vez de usar creación de nodos DOM o al menos un helper de escape.
- **Riesgo:** Bajo — las URLs provienen de la Media Library de WordPress (contexto de administración, usuario ya autenticado con capacidad de editar), no de un visitante anónimo. Aun así, es un patrón fragil si en el futuro se reutiliza este código para datos menos confiables.
- **Recomendación:** Usar `$('<img>').attr('src', url)` o `document.createElement('img')` en vez de concatenación de strings.
- **Impacto de corregirlo:** Trivial, cambio aislado a un bloque de `<script>` dentro de un metabox.

### QA-020 — Reutilización de la clase `.ce-modal__close` fuera del contexto de modal (inconsistencia BEM)
- **Archivo afectado:** `header.php` (botón de cierre del menú móvil)
- **Descripción:** El botón que cierra el menú móvil off-canvas usa `class="ce-nav-mobile__close ce-modal__close"`, reutilizando una clase cuyo namespace BEM (`ce-modal__*`) está pensado para los modales de éxito/error de `footer.php`, no para un menú de navegación.
- **Riesgo:** Ninguno funcional hoy (los selectores JS de `ModuleModals` y `ModuleMobileNav` no colisionan porque operan sobre árboles DOM distintos), pero rompe la consistencia semántica BEM del proyecto y podría causar confusión o un bug real si en el futuro se modifica `ModuleModals` para buscar `.ce-modal__close` de forma más amplia (ej. `document.querySelectorAll` en vez de scoped a cada overlay).
- **Recomendación:** Quitar la clase `ce-modal__close` de este botón (ya tiene su propia clase `ce-nav-mobile__close` y su propio manejador en `ModuleMobileNav`), o unificar el estilo visual del botón "X" en una clase compartida de propósito genérico (ej. `.ce-close-btn`) que ambos casos puedan usar sin implicar semánticamente "esto es un modal".
- **Impacto de corregirlo:** Trivial, cambio de una clase CSS en un solo archivo.

### QA-021 — Flechas de navegación visibles en el slider de testimonios incluso con un solo testimonio
- **Archivo afectado:** `template-parts/testimonials.php` / `assets/js/main.js` (`ModuleTestimonialSlider`)
- **Descripción:** Si el sitio solo tiene 1 testimonio publicado, las flechas prev/next y el autoplay siguen renderizándose y siendo funcionales (aunque solo "avancen" hacia el mismo único slide).
- **Riesgo:** Ninguno funcional (no hay error), solo una experiencia de usuario ligeramente confusa (flechas que no hacen nada visible).
- **Recomendación:** Ocultar flechas/dots/autoplay cuando `this.slides.length <= 1` en `ModuleTestimonialSlider.init()`.
- **Impacto de corregirlo:** Trivial, una condición adicional en JS.

### QA-022 — Discrepancia de versión entre `style.css` (cabecera del tema) y la documentación del proyecto
- **Archivo afectado:** `style.css`
- **Descripción:** La cabecera declara `Version: 1.0.0`, mientras que `CHANGELOG.md`/`PROJECT_STATUS.md` ya documentan v0.3.0. Un cliente que revise "Apariencia → Temas" en wp-admin vería "1.0.0", inconsistente con la comunicación del proyecto.
- **Riesgo:** Cosmético/confusión de comunicación, no funcional (relacionado con QA-008, pero este hallazgo es específicamente sobre la cabecera visible en wp-admin, no sobre el cache-busting).
- **Recomendación:** Definir una única fuente de verdad para el número de versión del tema (recomendado: que la cabecera de `style.css` seguido de cerca al número de `CHANGELOG.md`, aunque no tienen que ser idénticos si se documenta la relación entre "versión de tema" y "versión de sprint interno").
- **Impacto de corregirlo:** Trivial, cambio de una línea de cabecera.

### QA-023 — Falta `rel="noopener"` en enlaces `target="_blank"` fuera de los ya cubiertos
- **Archivo afectado:** revisión cruzada de todos los `target="_blank"` del proyecto
- **Descripción:** Se verificaron todos los usos de `target="_blank"` en el proyecto (redes sociales en `inc/helpers.php`, WhatsApp flotante, enlace de WhatsApp en `template-parts/cta.php`, LinkedIn en metaboxes de Equipo). Todos los casos revisados **sí** incluyen `rel="noopener noreferrer"` de forma correcta y consistente. No se encontró ningún caso faltante.
- **Riesgo:** Ninguno — se documenta este punto como verificación positiva, no como hallazgo de un bug. Se incluye en el reporte porque "seguridad en enlaces externos" era parte del alcance solicitado y se confirma que está correctamente resuelto en todo el proyecto.
- **Recomendación:** Ninguna acción requerida. Mantener esta convención en los módulos futuros (Proyectos, Blog).
- **Impacto de corregirlo:** N/A (no requiere corrección).

---

## 🔵 MEJORAS FUTURAS (no bugs, oportunidades de arquitectura)

### QA-024 — Duplicación de lógica de breadcrumbs entre HTML y JSON-LD
- **Archivo afectado:** `inc/seo.php` (`ce_construction_breadcrumbs()` vs. `ce_construction_schema_service()`)
- **Descripción:** La ruta de breadcrumbs (Inicio → Servicios → [Título]) se calcula dos veces de forma independiente: una para el HTML visible y otra para el JSON-LD del single de Servicio. Esta duplicación fue una decisión consciente documentada en `DECISIONS.md` D-012 (para no modificar la función HTML ya existente).
- **Recomendación:** En un sprint de refactor autorizado explícitamente, extraer la construcción del array de breadcrumbs a una función común `ce_construction_get_breadcrumb_items()` reutilizada por ambas salidas.
- **Impacto:** Reduce a la mitad el código a mantener si la estructura de breadcrumbs cambia en el futuro (ej. se agrega un cuarto nivel de jerarquía).

### QA-025 — Esquema `Organization`/`GeneralContractor` repetido inline sin `@id` compartido
- **Archivo afectado:** `inc/seo.php`
- **Descripción:** El bloque `provider` dentro del schema `Service` repite los mismos datos de la organización (`name`, `url`) que ya se declaran por separado en el schema de portada, en vez de referenciar una entidad compartida vía `@id`.
- **Recomendación:** Usar el patrón de `@id` de Schema.org (ej. `"provider": { "@id": "https://ejemplo.com/#organization" }`) para que buscadores identifiquen que es la misma entidad en todas las páginas.
- **Impacto:** Mejora de SEO técnico avanzado, no crítico para el cumplimiento básico del brief.

### QA-026 — Preconnect/preload ausente para recursos de fuentes externas
- **Archivo afectado:** `inc/enqueue.php`
- **Descripción:** Google Fonts se carga vía `<link rel="stylesheet">` estándar sin `<link rel="preconnect">` previo a `fonts.googleapis.com`/`fonts.gstatic.com`.
- **Recomendación:** Añadir preconnect vía `wp_resource_hints` o directamente en `wp_head` con prioridad temprana.
- **Impacto:** Mejora menor de performance (reduce latencia de conexión TLS a los dominios de Google Fonts).

### QA-027 — Sin variante auto-hospedada de Google Fonts / Font Awesome
- **Archivo afectado:** `inc/enqueue.php`
- **Descripción:** Ya documentado como riesgo en `PROJECT_STATUS.md` (dependencia de CDNs externos). Se reitera aquí en el contexto específico de performance y Core Web Vitals, ya que es el hallazgo de mayor impacto potencial en LCP/FCP de todo el proyecto.
- **Recomendación:** Evaluar auto-hospedar ambas dependencias con `wp_enqueue_style`/`_script` locales en un sprint de performance dedicado (ya planeado como v0.6.0 en `CHANGELOG.md`).
- **Impacto:** Alto potencial de mejora de Core Web Vitals; se clasifica como "mejora futura" y no como hallazgo de mayor severidad porque ya estaba identificado y aceptado como riesgo conocido desde el Sprint 2.

### QA-028 — Sin soporte para `WP_DEBUG`/logging estructurado en el handler del formulario
- **Archivo afectado:** `inc/quote-form.php`
- **Descripción:** Cuando `wp_mail()` falla (línea ~193), el usuario recibe un mensaje amigable, pero no queda ningún registro (log) del fallo para que el administrador lo audite.
- **Recomendación:** Añadir `error_log()` condicionado a `WP_DEBUG_LOG` cuando `wp_mail()` devuelva `false`, para facilitar diagnóstico de problemas de entrega de correo (SMTP mal configurado, etc.) sin depender de que el visitante reporte el problema.
- **Impacto:** Mejora de mantenibilidad/soporte, no afecta al visitante.

### QA-029 — Archivos CPT (`cpt-testimonios.php`, `cpt-equipo.php`, `cpt-clientes.php`, `cpt-faq.php`) muy pequeños y estructuralmente idénticos
- **Archivo afectado:** `inc/cpt-testimonios.php`, `inc/cpt-equipo.php`, `inc/cpt-clientes.php`, `inc/cpt-faq.php` (~30 líneas cada uno)
- **Descripción:** Los 4 archivos siguen exactamente el mismo patrón (`register_post_type` con labels + supports, sin taxonomías). Son fáciles de mantener por separado (principio de responsabilidad única ya establecido como convención del proyecto), pero también podrían unificarse en un único `inc/cpt-simple.php` con un array de configuración iterado, reduciendo de 4 archivos a 1.
- **Recomendación:** **No se recomienda unificarlos** por ahora: la convención "un archivo por CPT" ya está establecida y documentada (`HANDOFF.md` sección 11), y unificarlos sería un cambio de arquitectura que contradice la regla activa de "no reestructurar carpetas/convenciones" sin autorización explícita. Se documenta como observación, no como acción recomendada a corto plazo.
- **Impacto:** Neutral — se incluye únicamente porque el alcance de esta auditoría pedía identificar "archivos demasiado pequeños que podrían unificarse"; la recomendación técnica real es **mantenerlos separados** por consistencia con las decisiones ya tomadas.

---

## Compatibilidad — hallazgos específicos verificados

### WordPress 7.x
No se detectó uso de ninguna función marcada como *deprecated* o eliminada en el ciclo de WordPress 6.x→7.x conocido hasta la fecha de corte de conocimiento de este análisis (ene. 2026). El uso de `WP_Query`, Customizer API clásica, `register_post_type`/`register_taxonomy`, metaboxes clásicos y `wp_nav_menu` sigue siendo la API estable recomendada. **Recomendación:** re-verificar contra el changelog real de WordPress 7.x cuando esté disponible, ya que este análisis se basa en la API estable conocida, no en un entorno WordPress 7.x ejecutado realmente (el entorno de desarrollo de este proyecto no tiene PHP/WordPress instalado, ver limitación metodológica al final).

### PHP 8.x
Se buscó explícitamente: `create_function()` (eliminada en PHP 8), `each()` (eliminada en PHP 8), `extract()`/variables variables de riesgo, y llamadas que pasen `null` a parámetros no anulables de funciones internas (deprecated en PHP 8.1+). **No se encontró ningún uso de estos patrones** en el código PHP del proyecto. El código es, hasta donde se pudo verificar estáticamente, compatible con PHP 8.0-8.3.

### Limitación metodológica de este reporte
El entorno de desarrollo usado para construir este tema **no tiene PHP ni WordPress instalados** (confirmado con `which php` sin resultado en sprints anteriores), por lo que esta auditoría es 100% **estática** (lectura de código, no ejecución). No se pudo correr `phpcs` con el ruleset oficial de WordPress Coding Standards, ni un linter real de PHP, ni probar el sitio en un navegador real. Todos los hallazgos de este reporte fueron identificados por lectura manual rigurosa y verificados donde fue posible con herramientas disponibles (`node --check` para JS, cálculo numérico de contraste WCAG, balance de sintaxis). **Se recomienda** que, antes de producción, el proyecto se ejecute en un entorno WordPress real con `WP_DEBUG` activo y se corra `phpcs --standard=WordPress` para una verificación complementaria que esta auditoría no pudo realizar por limitaciones del entorno.

---

## Duplicaciones y funciones reutilizables detectadas (resumen)

- **QA-010** (script_loader_tag redundante) y **QA-013** (CSS duplicado) son las dos duplicaciones de código activo más relevantes.
- **Funciones ya bien centralizadas** (no requieren acción): `ce_get_short_excerpt()`, `ce_render_service_icon()`, `ce_cpt_has_posts()` son reutilizadas correctamente en múltiples template-parts sin duplicación — se destaca como buena práctica ya presente.
- **Oportunidad no urgente:** la lógica de "tarjeta de servicio" en `template-parts/services.php` (home) y `template-parts/content-servicio.php` (archive/relacionados) está intencionalmente duplicada (ver `DECISIONS.md`, decisión tomada en Sprint 3 para no tocar `services.php` ya aprobado). Sigue siendo una duplicación real de ~15 líneas de markup, documentada y aceptada, no un hallazgo nuevo.

## Código muerto detectado (resumen)
- QA-006 (sidebar `footer-1` registrado, nunca usado)
- QA-015 (`$attachment_name` calculado, nunca usado)

## Dependencias externas (resumen de riesgo)
- Google Fonts (CDN) — ver QA-026, QA-027.
- Font Awesome 6.5.1 (CDN, `cdnjs.cloudflare.com`) — mismo riesgo de performance/disponibilidad que Google Fonts; si el CDN cae, se pierden todos los iconos del sitio simultáneamente (sin fallback). No se detectó ningún mecanismo de fallback local. Se agrupa bajo la misma recomendación que QA-027.

---

## Cierre del Sprint de QA

Este reporte **no modifica ningún archivo del tema**. Los 29 hallazgos quedan documentados y clasificados, a la espera de tu aprobación explícita para decidir cuáles corregir y en qué orden. Ninguna corrección fue aplicada automáticamente, conforme a lo solicitado.
