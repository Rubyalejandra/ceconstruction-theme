# CE Construction — QA_REPORT.md
### Sprint de QA e Integración

> Este reporte es el resultado de una revisión exhaustiva y verificada línea por línea de todo el código generado hasta el Sprint 3 (Módulo Servicios). Ningún archivo fue modificado durante esta auditoría. Cada hallazgo fue confirmado contra el código real en disco (no se listan sospechas no verificadas) — incluyendo cálculo empírico de contraste WCAG, verificación de balance de sintaxis, y trazado de flujo de datos en el formulario de cotización.

**Alcance auditado:** los 31 archivos PHP/CSS/JS del tema (Sprints 1-3), incluyendo `functions.php`, todos los `inc/*.php`, `header.php`, `footer.php`, `front-page.php`, `archive-servicio.php`, `single-servicio.php`, todos los `template-parts/*.php`, `style.css`, `assets/css/main.css` y `assets/js/main.js`.

**Metodología:** lectura completa de cada archivo, verificación de balance de llaves/paréntesis, `node --check` sobre JS, trazado manual de flujos de datos sensibles (formulario de cotización, metaboxes, Customizer), y cálculo numérico de contraste de color (fórmula WCAG 2.x) para los pares de color realmente usados en texto.

---

## Resumen ejecutivo

| Severidad | Cantidad de hallazgos | Estado |
|---|---|---|
| 🔴 Crítico | 1 | ✅ Corregido en v0.4.1 |
| 🟠 Alto | 8 | ✅ Corregidos en v0.4.1 |
| 🟡 Medio | 9 | 🟡 1 corregido (QA-018, v0.7.2, Sprint 7 Entregable 7.3) / 8 sin corregir |
| 🟢 Bajo | 5 | ⬜ Sin corregir (fuera de alcance) |
| 🔵 Mejora futura | 6 | ⬜ Sin implementar |
| **Total** | **29** | **10 corregidos / 19 pendientes** |

El hallazgo crítico es un **bypass real de validación de tipo de archivo** en el formulario de cotización. Los hallazgos altos incluyen una **fuga de disco sin límite** (archivos adjuntos nunca registrados ni limpiados), ausencia de **rate-limiting** en un endpoint público, un **fallo de contraste WCAG AA verificado numéricamente**, y una **funcionalidad de administración silenciosamente rota** (sidebar de footer sin renderizar). **Actualización (Sprint 5, Fase 1, v0.4.1):** los 9 hallazgos Críticos/Altos (QA-001 a QA-009) ya fueron corregidos. **Actualización (Sprint 7, Entregable 7.3, v0.7.2):** de los 9 hallazgos Medios, se corrigió **QA-018** (barra superior del header sin adaptación responsive), con aprobación explícita del cliente limitada a ese hallazgo específico. Los 8 hallazgos Medios restantes (QA-010 a QA-017), los 5 Bajos y las 6 Mejoras futuras siguen exactamente igual que en la auditoría original, sin ninguna corrección aplicada.

---

## 🔴 CRÍTICO

### QA-001 — Bypass de validación de tipo de archivo en el formulario de cotización
- **Estado:** ✅ **CORREGIDO en v0.4.1** (Sprint 5, Fase 1) — ver `DECISIONS.md` D-017 y `CHANGELOG.md`.
- **Archivo afectado:** `inc/quote-form.php` (función `ce_construction_handle_quote_form()`, líneas ~133-143)
- **Descripción:** La validación de tipo de archivo adjunto comparaba `$_FILES['attachment']['type']` (el MIME type que el **navegador del visitante** envía en la cabecera `Content-Type` del `multipart/form-data`, completamente falsificable con herramientas como `curl -F`) contra el array `$allowed_types`. Además, `wp_check_filetype()` solo verificaba que la extensión del archivo estuviera en la lista **global** de extensiones permitidas por WordPress (docenas de extensiones: `.zip`, `.csv`, `.doc`, `.mp3`, etc., no solo PDF/JPG/PNG/WEBP), y el código nunca comparaba `$file_type['ext']` contra el whitelist real (`pdf`, `jpg`, `jpeg`, `png`, `webp`) que el propio comentario del código decía implementar.
- **Riesgo:** Un atacante podía renombrar un archivo con cualquier extensión permitida globalmente por WordPress (ej. `archivo.zip`) y falsificar manualmente la cabecera `Content-Type: application/pdf` en la petición HTTP.
- **Recomendación:** Reemplazar `wp_check_filetype()` por `wp_check_filetype_and_ext()`, comparar `$file_type['ext']` contra un whitelist explícito, y eliminar la dependencia del MIME de cliente para la decisión de seguridad.
- **Impacto de corregirlo:** Cambio acotado a ~10 líneas dentro de una única función ya existente.

---

## 🟠 ALTO

### QA-002 — Archivos adjuntos nunca registrados como adjuntos de WordPress: fuga de disco sin límite
- **Estado:** ✅ **CORREGIDO en v0.4.1** (Sprint 5, Fase 1) — ver `DECISIONS.md` D-017 y `CHANGELOG.md`.
- **Archivo afectado:** `inc/quote-form.php` (líneas ~145-155)
- **Descripción:** `wp_handle_upload()` movía el archivo físicamente a `wp-content/uploads/`, pero el código nunca llamaba a `wp_insert_attachment()` para registrarlo en la Media Library ni lo asociaba al post `cotizacion` recién creado.
- **Recomendación:** Registrar el archivo con `wp_insert_attachment()` vinculado al `post_id` de la cotización.
- **Impacto de corregirlo:** Cambio acotado a la misma función.

### QA-003 — Retención de datos personales sin límite (riesgo de cumplimiento)
- **Estado:** ✅ **CORREGIDO en v0.4.1** (Sprint 5, Fase 1) — ver `DECISIONS.md` D-018 y `CHANGELOG.md`.
- **Archivo afectado:** `inc/quote-form.php` (CPT `cotizacion`)
- **Recomendación:** Definir una política de retención implementable con un cron.
- **Impacto de corregirlo:** Requiere una decisión de negocio del cliente.

### QA-004 — Sin rate-limiting en el endpoint AJAX público del formulario
- **Estado:** ✅ **CORREGIDO en v0.4.1** (Sprint 5, Fase 1) — ver `DECISIONS.md` D-019 y `CHANGELOG.md`.
- **Archivo afectado:** `inc/quote-form.php` (`wp_ajax_nopriv_ce_submit_quote`)
- **Recomendación:** Throttle server-side vía `transient` keyed por IP.
- **Impacto de corregirlo:** Cambio acotado (~10-15 líneas).

### QA-005 — Fallo verificado de contraste WCAG AA en el color secundario sobre fondo claro
- **Estado:** ✅ **CORREGIDO en v0.4.1** (Sprint 5, Fase 1) — ver `DECISIONS.md` D-020 y `CHANGELOG.md`.
- **Archivo afectado:** `assets/css/main.css`
- **Descripción:** `--ce-color-secondary` (`#D98E29`) sobre blanco medía 2.67:1 (mínimo AA: 4.5:1).
- **Recomendación:** Variante más oscura del color secundario para texto sobre fondo claro.
- **Impacto de corregirlo:** Cambio de una sola variable CSS.

### QA-006 — Sidebar "Footer - Columna 1" registrado pero nunca renderizado
- **Estado:** ✅ **CORREGIDO en v0.4.1** (Sprint 5, Fase 1) — ver `DECISIONS.md` D-021 y `CHANGELOG.md`.
- **Archivo afectado:** `inc/setup.php` (registro) / `footer.php` (uso)
- **Recomendación:** Renderizar `dynamic_sidebar('footer-1')`.
- **Impacto de corregirlo:** Trivial (una línea).
- **Nota de continuidad (Sprint 7, Entregable 7.1):** el sidebar `footer-1`, ya renderizable desde esta corrección, ahora cuenta además con 2 widgets custom (`inc/widgets.php`) que un administrador puede asignarle — ver `DECISIONS.md` D-036.

### QA-007 — `save_post` sin guardia `wp_is_post_revision()`
- **Estado:** ✅ **CORREGIDO en v0.4.1** (Sprint 5, Fase 1) — ver `DECISIONS.md` D-022 y `CHANGELOG.md`.
- **Archivo afectado:** `inc/meta-boxes.php`
- **Recomendación:** Añadir `if ( wp_is_post_revision( $post_id ) ) { return; }`.
- **Impacto de corregirlo:** Una línea.

### QA-008 — `CE_THEME_VERSION` hardcodeada y nunca sincronizada
- **Estado:** ✅ **CORREGIDO en v0.4.1** (Sprint 5, Fase 1) — ver `DECISIONS.md` D-023 y `CHANGELOG.md`.
- **Archivo afectado:** `functions.php`, `style.css`
- **Recomendación:** Sincronizar la constante con cada entrega.
- **Impacto de corregirlo:** Cambio de bajo riesgo.

### QA-009 — CPT Servicio no soporta `page-attributes`
- **Estado:** ✅ **CORREGIDO en v0.4.1** (Sprint 5, Fase 1) — ver `DECISIONS.md` D-024 y `CHANGELOG.md`.
- **Archivo afectado:** `inc/cpt-servicios.php` / `template-parts/services.php`
- **Recomendación:** Añadir `'page-attributes'` a `supports`.
- **Impacto de corregirlo:** Una sola línea.

---

## 🟡 MEDIO

### QA-010 — Filtro `script_loader_tag` redundante con el soporte nativo de `wp_script_add_data( 'defer' )`
- **Estado:** ⬜ Sin corregir (no incluido en la aprobación del Entregable 7.3)
- **Archivo afectado:** `inc/enqueue.php`
- **Descripción:** El código llama a `wp_script_add_data( 'ce-construction-main', 'defer', true )` y además define un filtro manual `ce_construction_add_defer_attribute()` sobre `script_loader_tag` que hace lo mismo.
- **Recomendación:** Eliminar el filtro manual.
- **Impacto de corregirlo:** Elimina ~7 líneas.

### QA-011 — Transporte `postMessage` en 3 ajustes de color sin script de vista previa correspondiente
- **Estado:** ⬜ Sin corregir (no incluido en la aprobación del Entregable 7.3)
- **Archivo afectado:** `inc/customizer.php`
- **Recomendación:** Quitar `'transport' => 'postMessage'` de estos 3 ajustes.
- **Impacto de corregirlo:** Cambio de una palabra por ajuste.

### QA-012 — Consultas de "relacionados" sin caché
- **Estado:** ⬜ Sin corregir (no incluido en la aprobación del Entregable 7.3)
- **Archivo afectado:** `inc/helpers.php`
- **Recomendación:** Envolver el resultado en un `transient`.
- **Impacto de corregirlo:** Cambio acotado a `inc/helpers.php`.

### QA-013 — Duplicación de variables CSS y reset entre `style.css` y `assets/css/main.css`
- **Estado:** ⬜ Sin corregir (no incluido en la aprobación del Entregable 7.3)
- **Archivo afectado:** `style.css`, `assets/css/main.css`
- **Recomendación:** Corregir el comentario de `style.css` y evaluar eliminar el bloque duplicado.
- **Impacto de corregirlo:** Bajo riesgo si se hace con cuidado.

### QA-014 — JSON-LD sin endurecimiento contra secuencias `</script>`
- **Estado:** ⬜ Sin corregir (no incluido en la aprobación del Entregable 7.3)
- **Archivo afectado:** `inc/seo.php`
- **Recomendación:** Usar `JSON_UNESCAPED_SLASHES` o `str_replace('</script>', '<\/script>', $json)`.
- **Impacto de corregirlo:** Cambio trivial.

### QA-015 — Variable `$attachment_name` calculada pero nunca utilizada (código muerto)
- **Estado:** ⬜ Sin corregir (no incluido en la aprobación del Entregable 7.3)
- **Archivo afectado:** `inc/quote-form.php`
- **Recomendación:** Eliminar la variable o aprovecharla realmente.
- **Impacto de corregirlo:** Trivial.

### QA-016 — `<script>` inline embebido en una función de renderizado de metabox
- **Estado:** ⬜ Sin corregir (no incluido en la aprobación del Entregable 7.3)
- **Archivo afectado:** `inc/meta-boxes.php`
- **Recomendación:** Mover el script a un archivo `assets/js/admin-gallery.js` enqueado con dependencia declarada.
- **Impacto de corregirlo:** Riesgo bajo, mejora la robustez y el cumplimiento de WPCS.

### QA-017 — Salto de foco no garantizado en el enlace "Saltar al contenido principal"
- **Estado:** ⬜ Sin corregir (no incluido en la aprobación del Entregable 7.3)
- **Archivo afectado:** `header.php`
- **Recomendación:** Añadir `tabindex="-1"` a `<main id="ce-main-content">`.
- **Impacto de corregirlo:** Trivial.

### QA-018 — Barra superior del header sin adaptación responsive explícita
- **Estado:** ✅ **CORREGIDO en v0.7.2** (Sprint 7, Entregable 7.3) — ver `DECISIONS.md` D-039 y `CHANGELOG.md`.
- **Archivo afectado:** `assets/css/main.css` (`.ce-header__top`)
- **Descripción:** Se confirmó (búsqueda exhaustiva en el archivo) que no existía ninguna regla `@media` que ocultara, apilara o adaptara `.ce-header__top` (teléfono + correo + horario + iconos sociales, todo en una fila flex) para viewports pequeños.
- **Riesgo (previo a la corrección):** En viewports de 320-375px, la combinación de teléfono + correo + horario + redes sociales en una sola fila flex sin `flex-wrap` podía desbordar horizontalmente o comprimirse de forma ilegible.
- **Corrección aplicada:** Se implementó la Alternativa B propuesta en el hallazgo original: una regla `@media (max-width: 767.98px)` que aplica `flex-direction: column` al contenedor de `.ce-header__top` y `flex-wrap: wrap` a `.ce-header__contact`/`.ce-header__social`, apilando y envolviendo el contenido en móvil sin ocultar ninguna información. Añadida como sección 24 (nueva, al final) de `assets/css/main.css`, sin modificar ninguna regla existente de la sección 9 (Navbar/Header) ni afectar el layout de escritorio.
- **Impacto de la corrección:** Cambio 100% aditivo (~18 líneas), acotado a `assets/css/main.css`. Sin riesgo de romper el layout de escritorio (la regla solo aplica por debajo de 768px).

---

## 🟢 BAJO

### QA-019 — Generación de `<img>` vía concatenación de strings en JS del admin (no explota HTML de forma segura)
- **Archivo afectado:** `inc/meta-boxes.php` (script inline en `ce_render_proyecto_gallery()`)
- **Descripción:** `preview.append('<img src="'+a.attributes.sizes.thumbnail.url+'" ...')` construye HTML por concatenación de strings en vez de usar creación de nodos DOM o al menos un helper de escape.
- **Riesgo:** Bajo — las URLs provienen de la Media Library de WordPress (contexto de administración, usuario ya autenticado con capacidad de editar), no de un visitante anónimo.
- **Recomendación:** Usar `$('<img>').attr('src', url)` o `document.createElement('img')` en vez de concatenación de strings.
- **Impacto de corregirlo:** Trivial, cambio aislado a un bloque de `<script>` dentro de un metabox.

### QA-020 — Reutilización de la clase `.ce-modal__close` fuera del contexto de modal (inconsistencia BEM)
- **Archivo afectado:** `header.php` (botón de cierre del menú móvil)
- **Descripción:** El botón que cierra el menú móvil off-canvas usa `class="ce-nav-mobile__close ce-modal__close"`, reutilizando una clase cuyo namespace BEM (`ce-modal__*`) está pensado para los modales de éxito/error de `footer.php`, no para un menú de navegación.
- **Riesgo:** Ninguno funcional hoy, pero rompe la consistencia semántica BEM del proyecto.
- **Recomendación:** Quitar la clase `ce-modal__close` de este botón, o unificar el estilo visual en una clase compartida de propósito genérico.
- **Impacto de corregirlo:** Trivial, cambio de una clase CSS en un solo archivo.

### QA-021 — Flechas de navegación visibles en el slider de testimonios incluso con un solo testimonio
- **Archivo afectado:** `template-parts/testimonials.php` / `assets/js/main.js` (`ModuleTestimonialSlider`)
- **Descripción:** Si el sitio solo tiene 1 testimonio publicado, las flechas prev/next y el autoplay siguen renderizándose y siendo funcionales.
- **Riesgo:** Ninguno funcional, solo una experiencia de usuario ligeramente confusa.
- **Recomendación:** Ocultar flechas/dots/autoplay cuando `this.slides.length <= 1` en `ModuleTestimonialSlider.init()`.
- **Impacto de corregirlo:** Trivial, una condición adicional en JS.

### QA-022 — Discrepancia de versión entre `style.css` y la documentación del proyecto
- **Estado:** ✅ Resuelto como efecto colateral de QA-008 (ver `DECISIONS.md` D-023).
- **Archivo afectado:** `style.css`
- **Impacto de corregirlo:** N/A — ya resuelto.

### QA-023 — Falta `rel="noopener"` en enlaces `target="_blank"` fuera de los ya cubiertos
- **Archivo afectado:** revisión cruzada de todos los `target="_blank"` del proyecto
- **Descripción:** Se verificaron todos los usos de `target="_blank"` en el proyecto. Todos los casos revisados **sí** incluyen `rel="noopener noreferrer"` de forma correcta y consistente.
- **Riesgo:** Ninguno — se documenta este punto como verificación positiva, no como hallazgo de un bug.
- **Recomendación:** Ninguna acción requerida.
- **Impacto de corregirlo:** N/A (no requiere corrección).

---

## 🔵 MEJORAS FUTURAS (no bugs, oportunidades de arquitectura)

### QA-024 — Duplicación de lógica de breadcrumbs entre HTML y JSON-LD
- **Archivo afectado:** `inc/seo.php`
- **Recomendación:** Extraer la construcción del array de breadcrumbs a una función común, en un sprint de refactor autorizado.

### QA-025 — Esquema `Organization`/`GeneralContractor` repetido inline sin `@id` compartido
- **Archivo afectado:** `inc/seo.php`
- **Recomendación:** Usar el patrón de `@id` de Schema.org.

### QA-026 — Preconnect/preload ausente para recursos de fuentes externas
- **Archivo afectado:** `inc/enqueue.php`
- **Recomendación:** Añadir preconnect vía `wp_resource_hints`.

### QA-027 — Sin variante auto-hospedada de Google Fonts / Font Awesome
- **Archivo afectado:** `inc/enqueue.php`
- **Recomendación:** Evaluar auto-hospedar ambas dependencias en un sprint de performance dedicado.

### QA-028 — Sin soporte para `WP_DEBUG`/logging estructurado en el handler del formulario
- **Archivo afectado:** `inc/quote-form.php`
- **Recomendación:** Añadir `error_log()` condicionado a `WP_DEBUG_LOG`.

### QA-029 — Archivos CPT muy pequeños y estructuralmente idénticos
- **Archivo afectado:** `inc/cpt-testimonios.php`, `inc/cpt-equipo.php`, `inc/cpt-clientes.php`, `inc/cpt-faq.php`
- **Recomendación:** **No se recomienda unificarlos** — la convención "un archivo por CPT" ya está establecida.

---

## Compatibilidad — hallazgos específicos verificados

### WordPress 7.x
No se detectó uso de ninguna función marcada como *deprecated* o eliminada en el ciclo de WordPress 6.x→7.x conocido hasta la fecha de corte de conocimiento de este análisis (ene. 2026). **Recomendación:** re-verificar contra el changelog real de WordPress 7.x cuando esté disponible.

### PHP 8.x
Se buscó explícitamente `create_function()`, `each()`, `extract()`, y llamadas que pasen `null` a parámetros no anulables. **No se encontró ningún uso de estos patrones** en el código PHP del proyecto.

### Limitación metodológica de este reporte
El entorno de desarrollo usado para construir este tema **no tiene PHP ni WordPress instalados**, por lo que esta auditoría es 100% **estática**. Todos los hallazgos de este reporte fueron identificados por lectura manual rigurosa y verificados donde fue posible con herramientas disponibles (`node --check` para JS, cálculo numérico de contraste WCAG, balance de sintaxis). **Se recomienda** que, antes de producción, el proyecto se ejecute en un entorno WordPress real con `WP_DEBUG` activo y se corra `phpcs --standard=WordPress`.

---

## Duplicaciones y funciones reutilizables detectadas (resumen)

- **QA-010** (script_loader_tag redundante) y **QA-013** (CSS duplicado) son las dos duplicaciones de código activo más relevantes, ambas aún sin corregir.
- **Funciones ya bien centralizadas** (no requieren acción): `ce_get_short_excerpt()`, `ce_render_service_icon()`, `ce_cpt_has_posts()`.
- **Oportunidad no urgente:** la lógica de "tarjeta de servicio" en `template-parts/services.php` y `template-parts/content-servicio.php` está intencionalmente duplicada (ver `DECISIONS.md`).

## Código muerto detectado (resumen)
- QA-015 (`$attachment_name` calculado, nunca usado) — aún sin corregir.

## Dependencias externas (resumen de riesgo)
- Google Fonts (CDN) — ver QA-026, QA-027.
- Font Awesome 6.5.1 (CDN) — mismo riesgo de performance/disponibilidad.

---

## Cierre del Sprint de QA

**Actualización (Sprint 5, v0.4.1):** los 9 hallazgos Críticos y Altos de este reporte (QA-001 a QA-009) fueron corregidos, con autorización explícita, como Fase 1 del Sprint 5.

**Actualización (Sprint 7, Entregable 7.3, v0.7.2):** de los 9 hallazgos Medios (QA-010 a QA-018), se corrigió únicamente **QA-018** (barra superior del header sin adaptación responsive), conforme a la aprobación explícita del cliente limitada a ese hallazgo. Los 8 hallazgos Medios restantes (QA-010 a QA-017), los 5 Bajos y las 6 Mejoras futuras permanecen exactamente como en la auditoría original — no fueron tocados, a la espera de una futura aprobación explícita si se decide corregirlos.
