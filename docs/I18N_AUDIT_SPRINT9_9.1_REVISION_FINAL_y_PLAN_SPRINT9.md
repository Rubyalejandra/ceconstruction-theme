# Revisión Crítica del Entregable 9.1 (ampliado) + Plan del Sprint 9

**Estado: solo revisión y planificación. No se modificó ningún archivo, no se instaló Polylang, no se ejecutó `pll_register_string()`.**

Este documento NO repite la auditoría completa de 9.1. Verifica inconsistencias, completa lo pendiente (CPT/taxonomías/metaboxes) y cierra 9.1 con una clasificación accionable. Todo lo que sigue se apoya en el mismo código ya auditado (`ce_construction-theme.zip`), revisado puntualmente donde hacía falta más detalle.

---

## 1. Strings PHP/HTML — confirmación

**Confirmado sin cambios respecto a 9.1:** las 641 llamadas a funciones de internacionalización de WordPress están correctamente preparadas (mismo text domain `ce-construction` en el 100% de los casos, ninguna sin segundo argumento). Repetí la búsqueda de texto hardcodeado sin mecanismo (`echo 'Texto'`, atributos con literal fijo) específicamente sobre los archivos que el encargo original citaba como ejemplo (`template-parts/services.php` y equivalentes) y **no encontré ningún caso nuevo ni ninguna inconsistencia** respecto a lo ya reportado: siguen siendo 0 en PHP/HTML.

**Sobre `esc_html_e()` en `services.php` y equivalentes — respuesta directa:** no, esas cadenas **no deben convertirse en `pll_register_string()`**. Son dos mecanismos que resuelven el mismo problema en dos capas distintas de WordPress, y usar ambos a la vez sobre la misma cadena no aporta nada — al contrario, crea una fuente de verdad duplicada (la cadena viviría a la vez en el código fuente vía `.pot`/`.po`/`.mo` y en la tabla de opciones de Polylang vía `pll_register_string()`), lo que puede generar inconsistencias si un traductor actualiza una copia y no la otra. `pll_register_string()` existe específicamente para cadenas que **no** están en el código fuente del tema (viven en la base de datos: `theme_mod`, contenido de un repetidor JSON, etc.) y que por tanto un escáner de `.pot` no puede detectar. Las 641 cadenas de `__()`/`esc_html_e()`/etc. sí están en el código fuente — es exactamente el caso que `.pot`/`.po`/`.mo` ya resuelve de forma nativa.

**Veredicto: C — no requiere modificación, ya está correctamente preparado.**

---

## 2. Mecanismo técnico de traducción de las 641 cadenas

Con Polylang instalado (y activo el módulo de idiomas), el flujo correcto para estas 641 cadenas es el estándar de WordPress, **no** un mecanismo propio de Polylang:

1. **Generar un archivo `.pot`** (*Portable Object Template*) del tema. Es la plantilla que lista las 641 cadenas (texto + contexto de archivo/línea) sin traducir. Se genera con herramientas estándar (`wp i18n make-pot` de WP-CLI, o el plugin Loco Translate, o Poedit apuntando a la carpeta del tema) — no requiere tocar código, es un proceso de extracción automática sobre las llamadas `__()`/`_e()`/etc. ya existentes.
2. **Por cada idioma que Polylang tenga configurado, generar un `.po`** a partir del `.pot` (p. ej. `ce-construction-en_US.po`), traducir cada cadena, y compilarlo a su `.mo` correspondiente (`ce-construction-en_US.mo`). Esto se puede hacer con Poedit, WP-CLI o Loco Translate; no es una tarea de Polylang en sí.
3. **Colocar los `.mo` en `/languages`** dentro del tema (la carpeta que hoy no existe — habrá que crearla). `load_theme_textdomain( 'ce-construction', CE_THEME_DIR . '/languages' )`, ya presente en `inc/setup.php`, cargará automáticamente el `.mo` correspondiente al idioma activo de la petición.
4. **El rol de Polylang aquí es indirecto pero decisivo**: Polylang determina cuál es el "idioma activo" de la petición actual (vía el filtro `locale` de WordPress, que ajusta según la URL/subdominio/cookie configurados). WordPress, al resolver `__()`, ya consulta ese locale activo para elegir el `.mo` correcto. **No hace falta ninguna llamada especial de la API de Polylang para que esto funcione** — es el mecanismo de i18n de WordPress de siempre, y Polylang simplemente le dice a WordPress qué locale usar en cada petición.

**Respuesta directa a "qué se necesita":** se necesita la **combinación completa**: `.pot` (para poder generar traducciones) + `.po`/`.mo` por idioma (para que existan las traducciones) + la carpeta `/languages` + ninguna función específica de la API de Polylang para estas 641 cadenas en particular. Polylang solo aporta el locale activo; el resto es 100% el sistema de i18n nativo de WordPress, que ya está bien conectado en el tema (`inc/setup.php`).

**Nota de alcance:** no se implementa nada de esto ahora. Se documenta como parte de la Fase 2 propuesta en el plan de Sprint 9 (ver más abajo).

**Veredicto: C — no requiere cambio de código del tema; requiere trabajo de configuración/generación de archivos de idioma, no de desarrollo.**

---

## 3. JavaScript — corrección de un hallazgo incompleto en 9.1

**Al revisar de nuevo específicamente `assets/js/main.js` para esta verificación crítica, encontré una inconsistencia real en el conteo de 9.1: el informe anterior reportó "8 strings hardcodeados" pero el detalle listado sumaba 9, y además se me pasaron por alto 3 casos adicionales que no estaban en esa lista.** Los 3 casos nuevos son los `aria-label` de los botones del visor de imágenes/lightbox, que están escritos directamente dentro de una plantilla HTML en formato *template literal* que se inyecta vía `innerHTML` — un patrón distinto al de las otras 9 cadenas (que son literales de JS simples), por eso no aparecieron en la primera búsqueda dirigida a comillas simples con mayúscula inicial.

**El total real es 12 strings, no 8.** Tabla completa y corregida:

| # | Archivo | Línea | Texto | Dónde se muestra | Por qué no es traducible hoy | Solución propuesta |
|---:|---|---:|---|---|---|---|
| 1 | `assets/js/main.js` | 899 | `Ingresa un nombre válido.` | Mensaje de validación en vivo bajo el campo "Nombre" del formulario de cotización | Literal de JS puro; nunca pasa por `wp_localize_script()` | Mover a `ceConstructionData.i18n.nombreInvalido` (o similar), generado en PHP con `__()` |
| 2 | `assets/js/main.js` | 900 | `Ingresa un correo válido.` | Validación del campo "Email" | Igual | Igual, clave `emailInvalido` |
| 3 | `assets/js/main.js` | 901 | `Ingresa un teléfono válido.` | Validación del campo "Teléfono" | Igual | Igual, clave `telefonoInvalido` |
| 4 | `assets/js/main.js` | 902 | `Selecciona el servicio requerido.` | Validación del campo "Servicio" (select) | Igual | Igual, clave `servicioRequerido` |
| 5 | `assets/js/main.js` | 903 | `Cuéntanos un poco más (mínimo 10 caracteres).` | Validación del campo "Mensaje" (textarea) | Igual | Igual, clave `mensajeCorto` |
| 6 | `assets/js/main.js` | 994 | `Revisa los campos marcados en rojo.` | Mensaje de estado general cuando la validación del formulario completo falla | Igual | Igual, clave `revisaCampos` |
| 7 | `assets/js/main.js` | 310 | `Hola, quisiera más información sobre sus servicios de remodelacion.` | *Fallback* del texto del mensaje de WhatsApp, solo si `data-message` viniera vacío (en la práctica `footer.php` siempre lo imprime vía `esc_attr_e()`, así que esta es una ruta defensiva, no la normal) | Literal de JS puro | Baja prioridad (ruta rara), pero por consistencia: mover también a `i18n.whatsappFallback` |
| 8 | `assets/js/main.js` | 585 | `dotLabel: 'Testimonio'` | Prefijo del `aria-label` de cada punto de paginación del slider de testimonios (ej. "Testimonio 1") | Literal de JS puro, definido dentro de la config del módulo, no del `wp_localize_script()` | Mover a `i18n.testimonioDotLabel` |
| 9 | `assets/js/main.js` | 595 | `pauseLabel: 'Testimonios:'` | Prefijo del `aria-label` del botón de pausa/reanudar del slider (ej. "Testimonios: Pausar") | Igual | Mover a `i18n.testimoniosPauseLabel` |
| 10 | `assets/js/main.js` | 688 | `aria-label="Cerrar"` | Botón de cerrar del lightbox/visor de imágenes de la galería de proyectos | Está dentro de un *template literal* HTML inyectado vía `this.overlay.innerHTML = \`...\`` — no pasa por PHP en ningún punto | Mover a `i18n.lightboxCerrar` e interpolarlo en el template literal |
| 11 | `assets/js/main.js` | 689 | `aria-label="Anterior"` | Botón de navegación "anterior" del lightbox | Igual | `i18n.lightboxAnterior` |
| 12 | `assets/js/main.js` | 693 | `aria-label="Siguiente"` | Botón de navegación "siguiente" del lightbox | Igual | `i18n.lightboxSiguiente` |

**Respuesta directa a "¿son realmente los únicos?":** con esta revisión, sí — repetí la búsqueda con un patrón adicional específico para `aria-label="..."` y `title="..."`/`placeholder="..."`/`alt="..."` con literal fijo dentro de template literals (no solo comillas simples de JS "normal") sobre la totalidad de `main.js`, y no aparece ningún caso más. Los 5 archivos `admin-*.js` se revisaron también con este patrón ampliado y siguen sin ningún hallazgo — su texto viene 100% de sus respectivos `wp_localize_script()`.

**Veredicto: B — requiere decisión tuya** (no es una decisión técnica compleja, pero sí una modificación de código que according to las reglas de 9.1 no se ejecuta sin tu aprobación explícita del Entregable correspondiente).

---
## 4. CPT, taxonomías y metaboxes — sección completada (pendiente de 9.1)

### 4.1 Custom Post Types registrados por el tema

| CPT (slug real) | Registrado en | `public` | `has_archive` | `rewrite slug` | `supports` | ¿Traducible por Polylang? |
|---|---|---|---|---|---|---|
| `servicio` | `inc/cpt-servicios.php` | true | true | `servicios` | title, editor, thumbnail, excerpt, page-attributes | **Sí** — contenido editorial normal (título, contenido, extracto, imagen destacada); es exactamente el tipo de CPT que Polylang traduce de forma nativa marcándolo como "traducible" en Ajustes de Polylang. |
| `proyecto` | `inc/cpt-proyectos.php` | true | true | `proyectos` | title, editor, thumbnail, excerpt | **Sí**, mismo caso. |
| `testimonio` | `inc/cpt-testimonios.php` | true | false | `testimonios` | title, editor, thumbnail | **Sí**, mismo caso — aunque conceptualmente el testimonio de un cliente real normalmente NO se traduce (son sus palabras textuales), es una decisión de negocio, no técnica (ver 4.4). |
| `miembro_equipo` | `inc/cpt-equipo.php` | true | true | `equipo` | title, thumbnail | **Sí** el `title` (nombre de la persona, normalmente NO se traduce — decisión de negocio) y el contenido de metaboxes (`_ce_equipo_cargo` sí es traducible: el cargo/puesto). |
| `cliente` | `inc/cpt-clientes.php` | true | true | `clientes` | title, thumbnail | El `title` es el nombre de la empresa cliente — normalmente NO se traduce. |
| `ce_faq` | `inc/cpt-faq.php` | true | false | `faq` | title, editor | **Sí** — pregunta y respuesta son contenido editorial puro, caso de uso típico de Polylang. |
| `cotizacion` | `inc/quote-form.php` | **false** (`public => false`) | — | — | — | **No aplica** — es un CPT interno y privado (`show_ui => true` mantiene el listado en el admin, pero `public => false` lo mantiene fuera del front-end) que almacena los envíos del formulario de cotización (leads). Es dato transaccional, no contenido editorial multilingüe. **Debe quedar explícitamente excluido** de la configuración de tipos de contenido traducibles de Polylang — no tiene sentido "traducir" un lead. |

**Nota sobre los slugs de `rewrite`:** están hardcodeados como literales en español (`'servicios'`, `'proyectos'`, `'equipo'`, `'clientes'`, `'faq'`). Esto es normal y no es un defecto — pero es relevante para la Fase de URLs multidioma: Polylang, en su configuración de "Ajustes → URLs", ofrece traducir también los *slugs* de la URL por idioma (p. ej. `/en/services/` en vez de `/en/servicios/`) mediante un filtro (`pll_get_post_types` más los ajustes de slugs de Polylang) — esto es una decisión de configuración de Polylang, no requiere tocar `register_post_type()` en el tema salvo que se quiera un control más fino.

### 4.2 Taxonomías registradas

| Taxonomía | CPT asociado | Registrada en | ¿Traducible? |
|---|---|---|---|
| `categoria_servicio` | `servicio` | `inc/cpt-servicios.php` | **Sí** — los términos (nombres de categoría) son texto editorial; Polylang traduce taxonomías término a término cuando se marcan como traducibles. |
| `categoria_proyecto` | `proyecto` | `inc/cpt-proyectos.php` | **Sí**, mismo caso. |
| `estado_proyecto` | `proyecto` | `inc/cpt-proyectos.php` | **Sí en principio**, aunque probablemente sean pocos valores fijos tipo "En curso"/"Finalizado" — de todas formas es contenido editorial (nombres de término), no un dato técnico, así que sigue el mismo mecanismo. |

No se registran taxonomías adicionales en ningún otro archivo del tema.

### 4.3 Metaboxes (`inc/meta-boxes.php`) — inventario completo de custom fields

| CPT | Meta key | Tipo de dato guardado | Categoría | ¿Traducible? |
|---|---|---|---|---|
| `servicio` | `_ce_icono_fa` | Clase CSS de icono (Font Awesome) | Técnico | No |
| `servicio` | `_ce_enlace_externo` | URL externa (guardada con `esc_url_raw()`) | URL | No, salvo que el destino externo tenga versión por idioma — caso de negocio, no técnico |
| `proyecto` | `_ce_proyecto_cliente` | Texto libre (nombre del cliente escrito a mano) | Texto/dato propio | **Normalmente no** — es un nombre propio (ver 4.4); no es una relación real con el CPT `cliente` a pesar del nombre del campo (confirmado en el código: es un `<input type="text">` con `sanitize_text_field()`, no un selector de post) |
| `proyecto` | `_ce_proyecto_ubicacion` | Texto libre (p. ej. "Bogotá, Colombia") | Texto | Caso E — el nombre del lugar normalmente no se traduce, pero si el campo alguna vez incluyera algo como "Zona Norte" sí tendría equivalente en otro idioma; se documenta sin decidir |
| `proyecto` | `_ce_proyecto_fecha` | Fecha (`type="date"`) | Técnico/formato | No es texto, pero su *visualización* sí depende del idioma (formato de fecha) — hay que verificar en la plantilla que se imprima con `date_i18n()` y no con un `date()` plano (no se revisó en detalle en este documento; queda para la Fase de integración de CPT del Sprint 9) |
| `proyecto` | `_ce_proyecto_galeria` | CSV de IDs de adjuntos | Técnico (MEDIA_ID) | No |
| `testimonio` | `_ce_testimonio_nombre` | Texto libre (nombre de la persona) | Texto/dato propio | Normalmente no (nombre propio) |
| `testimonio` | `_ce_testimonio_cargo` | Texto libre (cargo/puesto de la persona, ej. "Gerente de Obra") | Texto | **Sí** — a diferencia del nombre, el cargo/puesto sí tiene equivalente en otros idiomas |
| `testimonio` | `_ce_testimonio_rating` | Número (1-5) | Técnico | No |
| `testimonio` | `_ce_testimonio_video_id` | ID de adjunto de video (validado como `video/*` al guardar) | Técnico (MEDIA_ID) | No |
| `testimonio` | `_ce_testimonio_video_url` | URL externa (video, vía oEmbed) | URL | No — un video de YouTube/Vimeo no cambia por idioma del sitio (a menos que el negocio grabe una versión distinta por idioma, decisión de negocio) |
| `miembro_equipo` | `_ce_equipo_cargo` | Texto libre (cargo del empleado) | Texto | **Sí**, traducible |
| `miembro_equipo` | `_ce_equipo_linkedin` | URL externa | URL | No |
| `cliente` | `_ce_cliente_sitio` | URL externa (sitio web del cliente) | URL | No |

### 4.4 Relaciones entre CPT — hallazgo importante

**No existe ninguna relación real (foreign key / ID) almacenada entre los CPT del tema.** Revisé específicamente esto porque el nombre del campo `_ce_proyecto_cliente` sugiere una relación con el CPT `cliente`, pero **no lo es**: es un campo de texto libre donde el administrador escribe el nombre a mano, sin ningún `wp_dropdown_pages()`/selector de post de por medio. Confirmado en `ce_render_proyecto_fields()` (`inc/meta-boxes.php:61-72`).

Los bloques de contenido "relacionado" que sí existen en el front-end (`single-servicio.php`, `single-proyecto.php`) **no usan IDs almacenados en absoluto** — se calculan dinámicamente en cada carga de página:
- **"Servicios relacionados"** y **"Proyectos relacionados con un servicio" / "Servicios relacionados con un proyecto"**: se calculan por **términos compartidos de `categoria_servicio`** (confirmado en el docblock de `inc/helpers.php:437-445`, que dice explícitamente que no existe un campo relacional directo).
- **"FAQ relacionadas"**: no están relacionadas con nada en particular — es simplemente una `WP_Query` de las 5 FAQ más recientes publicadas (`single-servicio.php:150-154`), sin ningún filtro de relación.

**Esto es una muy buena noticia para Polylang**: como no hay IDs de post guardados como "relación", no hay ningún ID que pueda "apuntar al post equivocado" al cambiar de idioma. El único mecanismo real de relación (términos de taxonomía compartidos) es precisamente el que Polylang gestiona de forma nativa al traducir taxonomías — mientras los términos traducidos se enlacen correctamente entre sí en Polylang, las secciones de "relacionados" seguirán funcionando igual en cualquier idioma, sin tocar código. Las consultas `WP_Query`/`get_posts()` que alimentan estas secciones tampoco necesitan cambios: Polylang filtra `pre_get_posts` automáticamente para limitar los resultados al idioma activo, salvo que el tema pase explícitamente `'lang' => ''` (no se encontró ningún caso así en el código revisado).

**Veredicto CPT/taxonomías: C** (el modelo de datos ya es compatible con Polylang sin cambios de código) **+ A para la configuración** (marcar los tipos de contenido correctos como traducibles es configuración de Polylang, no desarrollo, y puede hacerse sin más decisiones pendientes salvo la de negocio del punto 4.5).

### 4.5 Decisión de negocio pendiente (no técnica)

Para `testimonio.title` (si se usa), `_ce_testimonio_nombre`, `miembro_equipo.title`, `cliente.title` y `_ce_proyecto_cliente`: son todos nombres propios de personas o empresas. Técnicamente Polylang los tratará como "traducibles" si el CPT se marca como tal (tendrías que crear una "traducción" por idioma de cada testimonio/miembro/cliente), pero en la mayoría de los casos el nombre de una persona no cambia de un idioma a otro. **Esto no es algo que se pueda decidir por código — es una decisión tuya sobre si el contenido de esos CPT en sí (no solo sus cadenas) debe duplicarse por idioma o mostrarse igual en todos.** Se marca como **"Requiere confirmación del cliente"**.

---
## 5. URLs — distinción A/B y solución robusta

### A. URLs generadas por WordPress/Polylang (no requieren cambios)

`get_permalink()` (25 usos), `get_post_type_archive_link()` (23), `home_url()` (19), `get_term_link()` (2), `esc_url()` (69, como capa de escape sobre las anteriores). Todas se resuelven en el idioma correcto automáticamente en cuanto Polylang esté activo y los tipos de contenido/taxonomías correspondientes estén marcados como traducibles — es exactamente el mecanismo que Polylang engancha vía filtros del core de WordPress. **Veredicto: C.**

### B. URLs libres almacenadas en Customizer — una por una

| `theme_mod` | Dónde se usa | ¿Interno o externo, normalmente? | ¿Polylang la resolvería automáticamente? | Solución más robusta |
|---|---|---|---|---|
| `ce_hero_btn1_url` | `template-parts/hero.php:154`, botón principal del Hero | Puede ser cualquiera — sin *fallback* si se deja vacío | **No** — es un campo de texto libre (`esc_url_raw`/`esc_url`), Polylang no puede "adivinar" a qué página traducida corresponde una URL absoluta pegada a mano | Sustituir el campo de texto por un `wp_dropdown_pages()` (o un selector de post tipo el que usa el core para menús) que guarde un **ID de página**, y resolver la URL en tiempo de render con `get_permalink( $id )` — así Polylang traduce automáticamente el destino sin que el admin tenga que escribir `/es/...` o `/en/...` |
| `ce_hero_btn2_url` | `template-parts/hero.php:166`, botón secundario del Hero | Interno por defecto (archivo de "Proyectos") | **Sí, en el valor por defecto** (usa `get_post_type_archive_link( 'proyecto' )`) — el riesgo solo aparece si el admin lo sobrescribe con una URL absoluta | Mismo patrón que arriba, pero de menor urgencia porque el *fallback* ya es correcto |
| `ce_cta_btn_url` / `ce_cta2_btn_url` | `template-parts/cta.php`, botones de las 2 secciones CTA del Home Builder | Interno por defecto (ancla `#ce-quote-modal` al modal de cotización, disponible en todas las páginas) | El *fallback* no depende de idioma (es un ancla fija, no un permalink) | Si se deja como ancla al modal, no requiere ningún cambio. Solo si el admin lo personaliza con una URL de página propia aplicaría el mismo patrón de selector de página |
| `ce_financing_btn_url` | `template-parts/financing.php:47` | Sin *fallback*, puede ser cualquiera | No | Mismo patrón de selector de página |
| `ce_offer_popup_url` | `inc/helpers.php:285`, solo si `ce_offer_popup_action === 'link'` | Sin *fallback*, puede ser cualquiera | No | Mismo patrón de selector de página |
| `ce_testimonials_page_url` | `template-parts/testimonials.php:32` | **Muy probablemente interno** (su nombre lo indica: la propia página de testimonios del sitio) | No | **Prioridad más alta de las 7** — mismo patrón de selector de página; es el caso donde es más probable que en producción el admin haya pegado la URL de una página real del sitio, y por tanto el más urgente para que Polylang pueda resolverla por idioma sin intervención manual |

**Respuesta directa a la prioridad que planteas ("evitar que el administrador tenga que introducir manualmente `/es/...` y `/en/...`"):** la solución técnica es la misma para las 5 URLs de arriba con riesgo real (`btn1`, `btn2` si se personaliza, `financing`, `offer_popup`, `testimonials`): reemplazar el campo `type="url"` del Customizer por un control de selección de página de WordPress que guarde un ID de post, no una cadena de URL. Esto es exactamente el patrón que usa el core de WordPress para los menús de navegación, y es 100% compatible con Polylang sin necesitar ninguna función especial de su API — basta con resolver el ID a URL con `get_permalink()` en el momento del render, que ya es una función de la categoría A. **No se implementa nada de esto ahora** — se propone como el contenido concreto de la Fase de "URLs y navegación multidioma" del plan de Sprint 9.

**Veredicto theme_mod URLs: B — requiere tu decisión** (¿vale la pena cambiar el tipo de campo del Customizer de texto libre a selector de página para estas 5? Es la recomendación técnica, pero es un cambio de UX del panel de administración que debes aprobar).

---

## 6. Traducción automática del navegador — reclasificación según tu instrucción

Retiro cualquier formulación tipo "no puede romper el sitio". La conclusión, en los términos exactos que pides:

> **En el código auditado no existe evidencia de que el tema dependa del texto visible traducido para construir URLs, IDs, selectores o acciones funcionales.**

Reviso específicamente los 4 usos de `innerHTML` (ya identificados en 9.1) más `event.target`/delegación de eventos, y clasifico cada uno:

| Punto revisado | Línea(s) | ¿Depende de texto visible traducido? | Clasificación |
|---|---|---|---|
| `this.pauseBtn.innerHTML = ... '<i class="fa-solid fa-play">' ...` | 516-518 | No — inserta un icono (clase CSS), no texto | **Sin riesgo identificado** |
| `this.overlay.innerHTML = \`...\`` (markup del lightbox) | 687-694 | El markup en sí contiene los 3 `aria-label` hardcodeados (ya señalados en la Sección 3 como pendiente de i18n de WordPress) — pero esto es un problema de *cobertura de traducción*, no de *fragilidad ante el traductor del navegador*: aunque el navegador reescriba visualmente "Cerrar" a "Close", eso no rompe ninguna funcionalidad del botón (el `onclick`/listener está atado al elemento y a su clase, no al texto del `aria-label`) | **Sin riesgo identificado** (para funcionalidad) — el hallazgo relevante aquí es de i18n, ya cubierto en la Sección 3 |
| `this.embedEl.innerHTML = target.innerHTML` (copia de embed de oEmbed) | 763 | Copia markup ya generado por `wp_oembed_get()` en el servidor; no contiene lógica que dependa de texto | **Sin riesgo identificado** |
| `this.embedEl.innerHTML = ''` (limpieza del embed) | 781 | Vacía el contenedor, no depende de nada | **Sin riesgo identificado** |
| Delegación de eventos vía `e.target.closest('a[href^="#"]...')` | 157 | El selector usado en `.closest()` es un selector de **atributo** (`href`), no de texto — coincide contra el valor de `href`, que el traductor del navegador nunca modifica | **Sin riesgo identificado** |
| `e.target === this.overlay` / `e.target === overlay` (comparación de identidad de elemento) | 666, 809 | Compara la **referencia del elemento DOM**, no su texto ni ningún atributo — es inmune por diseño a cualquier reescritura de texto | **Sin riesgo identificado** |
| `e.target !== extra \|\| e.propertyName !== 'max-height'` (fin de transición CSS) | 1157 | Compara `propertyName` (nombre técnico de propiedad CSS), no texto | **Sin riesgo identificado** |

**No se encontró ningún caso clasificable como "riesgo confirmado" ni "riesgo teórico" en el código del tema.** Todos los puntos revisados usan identidad de elemento, clases CSS, o valores de atributos técnicos (`href`, `propertyName`) para su lógica — nunca el contenido textual visible que un traductor de navegador reescribiría. Esto no es una garantía general sobre el comportamiento de Google Translate como producto (fuera del alcance de un audit de código), es específicamente una conclusión sobre este código.

**Veredicto: C** para la lógica del tema tal como está — no requiere corrección de código por este motivo. (Distinto del veredicto de la Sección 3, que sí requiere corrección, pero por motivos de i18n, no de fragilidad ante traducción automática.)

---

## 7. JSON — campos, tipos y solución sin dependencia de índice

### `ce_stats_custom_items` (por fila)

| Campo | Tipo | ¿Visible? | ¿Traducible? | ¿Técnico? |
|---|---|---|---|---|
| `count` | Entero | Sí (se muestra) | No | Sí |
| `suffix` | Cadena corta (ej. `'+'`, `'%'`) | Sí | **Caso E** — normalmente un símbolo universal, pero si algún admin pusiera algo como `'años'` en vez de un símbolo, sí sería traducible; no se puede asumir automáticamente que nunca contendrá texto | Parcial |
| `label` | Cadena de texto libre | Sí | **Sí** | No |
| `icon` | Clase CSS (Font Awesome) | No (es un icono) | No | Sí |

### `ce_trust_badges_items` (por fila) — atención especial a `label` y `license`

| Campo | Tipo | ¿Visible? | ¿Traducible? | ¿Técnico? |
|---|---|---|---|---|
| `image_id` | ID de adjunto | Sí (la imagen) | No (ver Sección 10) | Sí |
| `label` | Cadena de texto libre, obligatoria | Sí (se usa como texto del badge y como `title`/alt de respaldo) | **Sí, sin ambigüedad** — es la descripción de la insignia de confianza (ej. "Certificación ISO 9001") | No |
| `license` | Cadena de texto libre | Sí (se muestra junto al badge) | **Caso E, con matiz importante**: en la mayoría de los casos será un número/código de licencia (ej. "N.º 4582-B"), que en sí mismo no se traduce carácter por carácter, **pero** el texto que lo acompaña dentro del mismo campo (si el admin escribe algo como "Licencia N.º 4582-B" en vez de solo el número) sí mezclaría una palabra traducible con un dato técnico dentro del mismo string. No se puede decidir unilateralmente sin ver qué contienen realmente los valores en producción — se recomienda revisar los valores reales guardados antes de decidir si `license` se registra como traducible o se trata como dato fijo. |
| `url` | URL externa | No (es un enlace, no texto) | No | Sí |

### Solución propuesta que NO depende del índice de la fila

El problema de fondo: hoy cada fila de estos dos repetidores no tiene un identificador propio — solo existe su posición dentro del array JSON. Si se registrara cada `label` con una clave tipo `pll_register_string( 'stats_item_0_label', ... )` basada en el índice, **reordenar, eliminar o agregar una fila desplazaría los índices** y el label traducido dejaría de corresponder a la fila correcta (la traducción del índice 2 quedaría "pegada" a una fila distinta después de un reordenamiento).

**Propuesta (a nivel de diseño, no se implementa ahora):** añadir a cada fila del repetidor, en el momento de guardarla desde el Customizer (en `ce_construction_sanitize_stats_items()` / `ce_construction_sanitize_trust_badges_items()`), un identificador único y estable generado una sola vez (ej. `wp_generate_uuid4()` o un hash corto) que persista mientras esa fila exista, independientemente de su posición. Ese identificador —no el índice— sería la parte variable de la clave de `pll_register_string()` (ej. `stats_item_{uuid}_label`). Al eliminar una fila, su cadena registrada queda huérfana (se podría limpiar en un mantenimiento periódico, no es crítico); al reordenar o agregar filas, las traducciones existentes no se ven afectadas porque no dependen de la posición. **Esto es una propuesta de diseño para la Fase correspondiente del Sprint 9 — no se implementa en esta revisión.**

**Veredicto JSON: B — requiere tu decisión** sobre: (a) si `suffix` y `license` se tratan como traducibles o fijos en cada caso real, y (b) aprobar el enfoque de identificador estable antes de que se diseñe en detalle en su propia Fase.

---
## 8. Datos de contacto — clasificación con decisión de negocio separada de la técnica

| `theme_mod` | Recomendación técnica | Decisión de negocio |
|---|---|---|
| `ce_phone` | No requiere `pll_register_string()` — es un dato, no contenido editorial | **Requiere confirmación del cliente**: ¿el negocio usa el mismo teléfono para todos los mercados/idiomas, o distinto número por país? |
| `ce_email` | No requiere registro por idioma | **Requiere confirmación del cliente**: mismo criterio que el teléfono |
| `ce_address` | Caso E ya documentado en 9.1 — la dirección en sí no se traduce, pero palabras como "Piso"/"Local" dentro del mismo campo sí tienen equivalente | **Requiere confirmación del cliente**: ¿una sola sede/dirección, o direcciones distintas por país que ya de por sí implican contenido distinto por idioma? |
| `ce_whatsapp_number` | No requiere registro por idioma | **Requiere confirmación del cliente**: mismo criterio que teléfono/email |
| `ce_schedule` | Es texto libre (ej. "Lun-Vie 8am-6pm") que sí contiene palabras traducibles (días, "am"/"pm" pueden variar de formato) — técnicamente **sí es candidato a `pll_register_string()`** a diferencia de los 4 anteriores | No requiere decisión de negocio — es una decisión técnica clara: si el horario es el mismo en todos los mercados, el texto que lo describe igual debería traducirse porque los nombres de los días cambian de idioma |

**Nota:** la diferencia entre `ce_schedule` y los otros 4 es importante y no estaba tan explícita en 9.1: `ce_schedule` es la única de este grupo que es *texto descriptivo* (por tanto va en la categoría TEXTO de la Sección D de 9.1, técnicamente sin ambigüedad), mientras que `phone`/`email`/`address`/`whatsapp` son *datos* cuya necesidad de variar por idioma depende enteramente de cómo opere el negocio, no de una regla técnica de i18n.

**Veredicto: C para `ce_schedule`** (ya está claro que es candidato técnico a registro, sin decisión pendiente) **+ "Requiere confirmación del cliente" para los otros 4**.

---

## 9. `ce_footer_copyright` — caso especial, explicación completa

- **Valor por defecto:** `sprintf( __( '&copy; %1\$d %2\$s. Todos los derechos reservados.', 'ce-construction' ), (int) date( 'Y' ), get_bloginfo( 'name' ) )` — `%1\$d` es el año actual (entero, se recalcula en cada carga de página, no se traduce ni se traduciría nunca) y `%2\$s` es el nombre del sitio (`get_bloginfo( 'name' )`, un dato de configuración de WordPress, no un `theme_mod`).
- **Traducción actual (mientras el admin no lo personalice):** ya es correcta y automática — es una cadena de código normal (categoría B de la Sección C de 9.1), Polylang la traduce vía el mismo mecanismo `.pot`/`.po`/`.mo` que las otras 640 cadenas, sin ninguna acción adicional.
- **Comportamiento cuando el administrador lo personaliza desde el Customizer:** en cuanto el admin escribe cualquier valor en el campo del Customizer, ese valor se guarda tal cual (como cadena plana) en el `theme_mod` `ce_footer_copyright`, y **dejará de pasar por `__()`/`sprintf()` en absoluto** — el `theme_mod` guardado sustituye por completo al valor por defecto, placeholders incluidos si el admin los conserva, o sin ellos si los borra.
- **Función de `%1\$d` y `%2\$s`:** son marcadores de posición de `sprintf()` — `%1\$d` inserta un número entero (el año) en la primera posición del formato, `%2\$s` inserta una cadena (el nombre del sitio) en la segunda. El `1\$`/`2\$` fija el orden explícitamente (necesario porque distintos idiomas pueden requerir invertir el orden de "año" y "nombre del sitio" en la frase).
- **Riesgos de traducir/registrar incorrectamente esta cadena con placeholders:**
  1. Si se registra el valor personalizado completo (con `%1\$d %2\$s` literales) vía `pll_register_string()` y un traductor edita el texto sin darse cuenta de que esos símbolos son marcadores de `sprintf()`, puede borrarlos, duplicarlos o invertir su orden sin cambiar los números `1\$`/`2\$` correspondientes — el resultado sería un `sprintf()` mal formado (advertencia de PHP) o un año/nombre de sitio mostrado en el lugar equivocado de la frase.
  2. Si el admin personaliza el `theme_mod` **sin** placeholders (escribe el copyright ya con año y nombre fijos, como texto plano), entonces ya no hay ningún `sprintf()` que romper — pero el año quedará **fijo** para siempre (no se recalculará cada 1 de enero) en todos los idiomas, lo cual es un problema de mantenimiento del sitio en general, no específico de i18n.
  3. La solución más segura, si en algún momento se decide registrar esta cadena por idioma, sería **no** exponer los placeholders crudos al traductor: envolver el registro de forma que el año y el nombre del sitio se sigan interpolando en PHP después de obtener la traducción de la frase (parecido a como ya funciona el valor por defecto), en vez de guardar el resultado final de `sprintf()` como si fuera texto plano traducible. **Esto es una recomendación de diseño para la Fase correspondiente — no se implementa ahora.**

**Veredicto: B — requiere tu decisión** sobre si personalizar este campo es un caso de uso real para este proyecto (si nunca se personaliza, el caso especial desaparece solo y la cadena por defecto ya es correcta sin tocar nada).

---

## 10. Imágenes y medios

| `theme_mod` / campo | ¿Global o potencialmente distinto por idioma? | Razonamiento |
|---|---|---|
| `ce_hero_image` | Normalmente **global** | Es la imagen de fondo del Hero — salvo que el negocio quiera mostrar un proyecto/ambientación distinta según el mercado del idioma (decisión de contenido/marketing, no técnica) |
| `ce_hero_video` | Normalmente **global** | Mismo razonamiento |
| `ce_hero_slides` | Normalmente **global** | Mismo razonamiento — es una lista de IDs, la decisión de si varían por idioma es 100% de contenido, no de código: Polylang no impone nada aquí porque un `theme_mod` no es "por idioma" salvo que se registre explícitamente por separado para cada idioma (algo que normalmente no se hace con IDs de imagen, se reserva para texto) |
| `ce_footer_logo` | **Global** en la inmensa mayoría de los casos | El logo de una marca no suele cambiar por idioma |
| Imagen destacada de `servicio`/`proyecto`/`testimonio`/`miembro_equipo`/`cliente` | **Depende de si el CPT se marca como traducible** | Si un CPT se marca como traducible en Polylang, cada "traducción" del post es técnicamente un post distinto en la base de datos, con su propia imagen destacada — **puede** ser la misma imagen reutilizada o una distinta, es una decisión editorial al crear cada traducción, no algo que el tema deba resolver con código |
| `_ce_proyecto_galeria` (galería de proyecto) | Igual que la imagen destacada — depende de si `proyecto` se marca como traducible | Sin cambios de código necesarios: es solo una lista de IDs, funciona igual en cualquier idioma en que se use |
| `_ce_testimonio_video_id` | Igual | Sin cambios de código |

**Confirmación explícita a tu instrucción de "no asumir que un ID necesita traducción únicamente por cambiar de idioma":** ningún ID de adjunto en este tema necesita ningún tratamiento especial de i18n. Un ID de adjunto es simplemente un número que apunta a un archivo en la Media Library — ese archivo existe una sola vez independientemente de cuántos idiomas tenga el sitio. Lo único que puede variar por idioma es la **decisión editorial** de si dos posts-traducción (cuando el CPT es traducible) reutilizan el mismo ID de imagen o usan IDs distintos — y eso lo decide quien traduce el contenido, no el tema.

**Veredicto: C** — no requiere ningún cambio de código; la única "decisión" aquí es editorial/de contenido, no técnica, y no bloquea nada.

---
## RESULTADO DE LA REVISIÓN

| Área | Estado | ¿Código? | ¿Configuración Polylang? | ¿Decisión cliente? |
|---|---|---|---|---|
| Strings PHP/HTML (641) | Correctas, sin cambios | No | No (solo generar `.pot`/`.po`/`.mo`) | No |
| Strings JS (12, corregido de 8) | Pendiente de corrección | **Sí** | No | No |
| `theme_mod` texto (20 claves) | Listas para registrar | No | **Sí** (`pll_register_string()`) | No, salvo `ce_footer_copyright` (ver abajo) |
| `theme_mod` URLs (7 señaladas) | 5 con riesgo real, 2 de bajo riesgo | **Sí, si se decide cambiar a selector de página** | Parcial | **Sí** (¿vale la pena el cambio de UX del Customizer?) |
| URLs generadas por WP (permalink, archive link, home_url, term_link) | Correctas, sin cambios | No | No (automático al activar Polylang) | No |
| CPT (`servicio`, `proyecto`, `testimonio`, `miembro_equipo`, `cliente`, `ce_faq`, `cotizacion`) | Modelo de datos compatible, sin relaciones frágiles | No | **Sí** (marcar cuáles son traducibles) | **Sí** (¿nombres propios/testimonios se traducen o quedan iguales?) |
| Metaboxes (custom fields) | Inventariados y clasificados | No inmediato | No | No, salvo el matiz de `_ce_proyecto_ubicacion` (caso E) |
| JSON (`ce_stats_custom_items`, `ce_trust_badges_items`) | Diseño de solución propuesto, no implementado | **Sí, cuando se implemente** | Parcial | **Sí** (¿`suffix`/`license` traducibles?, aprobar enfoque de identificador estable) |
| Contacto (`phone`, `email`, `address`, `whatsapp`, `schedule`) | `schedule` técnico y claro; los otros 4 son de negocio | No | No | **Sí** (los 4 datos de contacto) |
| SEO/meta (`hreflang`, canonical, OG, Twitter Cards, JSON-LD) | Sin `hreflang` propio del tema (correcto — lo añade Polylang al activarse); canonical/OG/Twitter/JSON-LD ya usan datos dinámicos vía funciones de WP | No | Automático al activar Polylang | No |
| Traducción automática del navegador | Sin riesgo identificado en el código (reclasificado, sin lenguaje absoluto) | No | No | No |

### A — Puede pasar a implementación sin decisión adicional
- Generación de `.pot`/`.po`/`.mo` para las 641 cadenas de código (Sección 2).
- Configuración de Polylang para marcar `servicio`, `proyecto`, `ce_faq` y sus taxonomías como traducibles (contenido editorial sin ambigüedad).
- Registro con `pll_register_string()` de los 20 `theme_mod` de tipo TEXTO ya identificados en 9.1 (ninguno de ellos tiene una decisión de negocio pendiente).
- Marcar el CPT `cotizacion` como explícitamente NO traducible / excluido de Polylang.

### B — Requiere decisión mía antes de implementar
- Corrección de los 12 strings hardcodeados en `assets/js/main.js` (Sección 3) — decisión de aprobar la modificación de código, no técnica.
- Cambiar las 5 URLs de riesgo real del Customizer de campo de texto libre a selector de página (Sección 5).
- Enfoque de identificador estable (no por índice) para `ce_stats_custom_items`/`ce_trust_badges_items`, y si `suffix`/`license` se tratan como traducibles (Sección 7).
- Si se registra `ce_footer_copyright` por idioma cuando esté personalizado, y con qué estrategia frente a los placeholders (Sección 9).
- Si `testimonio`, `miembro_equipo` y `cliente` se marcan como CPT traducibles (duplicando nombres propios por idioma) o se dejan como contenido único sin traducción (Sección 4.5).

### C — No requiere modificación porque ya está correctamente preparado
- Las 641 cadenas de código PHP/HTML.
- Las URLs generadas por funciones de WordPress (`get_permalink()`, `get_post_type_archive_link()`, `home_url()`, `get_term_link()`).
- El modelo de datos de los CPT (sin relaciones frágiles por ID).
- La lógica de JavaScript frente a la traducción automática del navegador (sin riesgo identificado).
- Los IDs de medios/adjuntos (no requieren ningún tratamiento de i18n).
- `ce_schedule` (técnicamente claro que es candidato a registro, sin ambigüedad pendiente).

---

# PLAN DE ENTREGABLES DEL SPRINT 9

El 9.1 (ampliado + esta revisión) es la auditoría. A partir de sus conclusiones, propongo la siguiente secuencia. Los nombres/números son una propuesta, no una numeración definitiva.

## 9.2 — Decisiones y arquitectura de i18n
**Objetivo:** cerrar todas las decisiones marcadas como "B" o "Requiere confirmación del cliente" en esta revisión, antes de escribir una sola línea de código.
**Alcance:** documento de decisiones (no código) que registre tus respuestas a: qué CPT se marcan como traducibles, qué hacer con los 4 datos de contacto, si se personaliza `ce_footer_copyright`, si se cambian las 5 URLs a selector de página, y el diseño final del identificador estable para los JSON.
**Dependencias:** esta revisión de 9.1.
**Archivos que se tocarían:** ninguno (documento nuevo en `docs/`).
**Criterios de aceptación:** cada punto B tiene una respuesta explícita tuya, sin ambigüedad, documentada.
**Qué NO se debe modificar:** nada de código.
**Aprobación que necesitas darme:** aprobar el documento de decisiones completo antes de pasar a 9.3.

## 9.3 — Preparación del tema (infraestructura de i18n, sin Polylang activo todavía)
**Objetivo:** dejar el tema listo para recibir traducciones, sin depender aún de que Polylang esté instalado.
**Alcance:** crear la carpeta `/languages`, generar el `.pot` del tema, extender `ceConstructionData.i18n` en `inc/enqueue.php` con las 12 claves de la Sección 3 (y actualizar `main.js` para usarlas con *fallback* al texto en español, igual que ya hacen las 6 claves existentes).
**Dependencias:** 9.2 aprobado (específicamente, que apruebes tocar `main.js`/`enqueue.php`).
**Archivos que se tocarían:** `inc/enqueue.php`, `assets/js/main.js`, nueva carpeta `/languages` con el `.pot`.
**Criterios de aceptación:** el sitio funciona exactamente igual en español (cero regresión visual/funcional), las 12 cadenas antes hardcodeadas ahora salen de `ceConstructionData.i18n`, `.pot` generado y verificable con las 641+12 cadenas.
**Qué NO se debe modificar:** ningún `theme_mod`, ninguna plantilla PHP fuera de `inc/enqueue.php`, no se instala Polylang todavía.
**Aprobación que necesitas darme:** revisar que el comportamiento en español no cambió, antes de instalar Polylang.

## 9.4 — Instalación y configuración base de Polylang
**Objetivo:** activar Polylang con los idiomas del sitio configurados, sin todavía registrar ningún `theme_mod` ni tocar CPT.
**Alcance:** instalar el plugin, configurar idiomas, verificar que las 641+12 cadenas de código ya se traducen correctamente generando los `.po`/`.mo` de al menos un idioma adicional de prueba.
**Dependencias:** 9.3.
**Archivos que se tocarían:** ninguno del tema (es configuración de plugin) salvo, si hiciera falta, `functions.php`/`inc/setup.php` para algún `add_theme_support` específico que Polylang recomiende (a confirmar en el momento, no se asume ahora).
**Criterios de aceptación:** el selector de idioma de Polylang funciona, el idioma por defecto se ve exactamente igual que antes, un idioma de prueba muestra las cadenas de código traducidas.
**Qué NO se debe modificar:** `theme_mod`, CPT, metaboxes, JSON.
**Aprobación que necesitas darme:** confirmar que el idioma por defecto no sufrió ninguna regresión antes de continuar.

## 9.5 — Integración de `theme_mod` de texto
**Objetivo:** registrar con `pll_register_string()` las 20 claves TEXTO ya identificadas (Sección D de 9.1), según lo decidido en 9.2 para los casos especiales.
**Alcance:** código de registro (probablemente en `inc/customizer.php` o un archivo nuevo `inc/polylang-strings.php`) + verificación en el panel de traducción de cadenas de Polylang.
**Dependencias:** 9.2 (decisión sobre `ce_footer_copyright`), 9.4.
**Archivos que se tocarían:** archivo nuevo o `inc/customizer.php`; sin tocar plantillas de render salvo que se decida cambiar cómo se imprime `ce_footer_copyright`.
**Criterios de aceptación:** las 20 cadenas aparecen en el panel "Idiomas → Traducción de cadenas" de Polylang, y cambian correctamente al cambiar de idioma en el front-end.
**Qué NO se debe modificar:** URLs, JSON, CPT.
**Aprobación:** verificar cada una de las 20 cadenas en el front-end en 2 idiomas antes de continuar.

## 9.6 — Integración de CPT, taxonomías y metaboxes
**Objetivo:** marcar los CPT/taxonomías correspondientes como traducibles según lo decidido en 9.2, y confirmar que las secciones "relacionados" (basadas en taxonomía compartida, Sección 4.4) siguen funcionando en cada idioma.
**Alcance:** configuración de Polylang (qué tipos de contenido/taxonomías son traducibles) + traducción de prueba de al menos 1 registro de cada CPT afectado.
**Dependencias:** 9.2, 9.4.
**Archivos que se tocarían:** posiblemente ninguno de código (es configuración), salvo que aparezca algún caso no previsto en la auditoría al probar con datos reales.
**Criterios de aceptación:** un `servicio` traducido conserva sus "servicios/proyectos relacionados" correctos en ambos idiomas; el CPT `cotizacion` queda confirmado como excluido.
**Qué NO se debe modificar:** JSON, URLs del Customizer.
**Aprobación:** confirmar con datos reales que las relaciones por taxonomía no se rompen entre idiomas.

## 9.7 — URLs y navegación multidioma
**Objetivo:** implementar, para las URLs donde se haya decidido en 9.2, el reemplazo de campo de texto libre por selector de página en el Customizer.
**Alcance:** cambios en `inc/customizer.php` (definición del control) y en la plantilla correspondiente (resolver ID → `get_permalink()` en vez de imprimir la URL guardada directamente).
**Dependencias:** 9.2 (qué URLs se cambian), 9.6 (que las páginas destino ya puedan tener traducción).
**Archivos que se tocarían:** `inc/customizer.php`, y los template-parts de las URLs afectadas (`hero.php`, `cta.php`, `financing.php`, `testimonials.php`, `inc/helpers.php` para el popup).
**Criterios de aceptación:** cada botón afectado apunta a la página correcta en el idioma activo, sin que el admin tenga que escribir nada manualmente por idioma.
**Qué NO se debe modificar:** las URLs que se decidió NO cambiar en 9.2 (mapas, redes sociales, WhatsApp).
**Aprobación:** probar cada botón en cada idioma configurado antes de continuar.

## 9.8 — JSON y datos dinámicos
**Objetivo:** implementar el identificador estable (no por índice) para `ce_stats_custom_items` y `ce_trust_badges_items`, y registrar el campo `label` (y `suffix`/`license` si se decidió que sí) por idioma.
**Alcance:** modificar las funciones de sanitización (`ce_construction_sanitize_stats_items()`, `ce_construction_sanitize_trust_badges_items()`) para generar/preservar el identificador estable, y el código de registro/lectura de las cadenas traducidas.
**Dependencias:** 9.2 (decisión sobre `suffix`/`license`), 9.4.
**Archivos que se tocarían:** `inc/customizer.php` y/o `inc/helpers.php`, `assets/js/admin-stats-items.js`, `assets/js/admin-trust-badges.js` (si el JS de admin necesita conocer el identificador).
**Criterios de aceptación:** reordenar, agregar o eliminar una fila en cualquiera de los 2 repetidores NO desplaza ninguna traducción existente de otra fila.
**Qué NO se debe modificar:** el resto de campos de cada repetidor (`count`, `icon`, `image_id`, `url`) — siguen siendo globales.
**Aprobación:** prueba explícita de reordenar filas con traducciones ya cargadas, confirmando que no se mezclan.

## 9.9 — SEO / hreflang
**Objetivo:** confirmar que Polylang añade correctamente las etiquetas `hreflang` (nativo del plugin) y que el `<link rel="canonical">`/`og:url`/`twitter:*`/JSON-LD ya existentes en `inc/seo.php` siguen siendo correctos por idioma, sin duplicar contenido para buscadores.
**Alcance:** verificación, no necesariamente código nuevo — según lo encontrado en 9.1, `inc/seo.php` ya usa `esc_url( $url )` dinámico, así que probablemente no requiera cambios; se confirma en este Entregable, no se asume.
**Dependencias:** 9.4, 9.6.
**Archivos que se tocarían:** `inc/seo.php`, solo si la verificación encuentra algo que corregir.
**Criterios de aceptación:** cada página en cada idioma tiene su propio `canonical` correcto y las etiquetas `hreflang` de Polylang enlazan correctamente entre las versiones traducidas.
**Qué NO se debe modificar:** nada si la verificación no encuentra problemas.
**Aprobación:** revisar el `<head>` renderizado de al menos 3 páginas por idioma.

## 9.10 — Pruebas funcionales
**Objetivo:** probar el sitio completo, en cada idioma configurado, siguiendo los 7 puntos de CTA (`ce_get_quote_cta_url()`) y el flujo completo del formulario de cotización.
**Alcance:** solo pruebas, sin código nuevo salvo corrección de bugs puntuales que aparezcan.
**Dependencias:** todas las anteriores.
**Criterios de aceptación:** los 7 CTA funcionan en cada idioma; el formulario de cotización se envía y valida correctamente (incluyendo los 5 mensajes de validación ya traducidos desde 9.3) en cada idioma.

## 9.11 — Pruebas de regresión
**Objetivo:** confirmar que el idioma por defecto (español) no sufrió ningún cambio visual ni funcional a lo largo de todo el Sprint 9.
**Alcance:** comparación antes/después con el QA_REPORT.md existente del proyecto como línea base.
**Dependencias:** todas las anteriores.
**Criterios de aceptación:** cero regresiones en español.

## 9.12 — Prueba con traducción automática del navegador activada
**Objetivo:** repetir 9.10 con la traducción automática del navegador activada simultáneamente con Polylang, dado el antecedente que mencionaste al inicio del Sprint.
**Alcance:** solo pruebas.
**Dependencias:** 9.10, 9.11.
**Criterios de aceptación:** ningún CTA ni el formulario de cotización deja de funcionar con la traducción del navegador activada en ningún idioma.

## 9.13 — Validación final del Sprint
**Objetivo:** cierre formal del Sprint 9, documento de resumen de lo implementado vs. lo auditado en 9.1, y actualización de `docs/` correspondiente (`CHANGELOG.md`, `DECISIONS.md`, y `CURRENT_SPRINT.md` si el proyecto lo sigue usando para Sprint 9 — no `CURRENT_UX_SPRINT.md`, que pertenece a un sprint distinto).
**Dependencias:** todas las anteriores.
**Criterios de aceptación:** documentación del proyecto refleja el estado real post-Polylang.

---

# PROMPT PARA INICIAR EL SIGUIENTE ENTREGABLE

```
Vamos a ejecutar el Entregable 9.2 del Sprint 9 (CE Construction): Decisiones y
arquitectura de i18n.

CONCLUSIONES DE 9.1 (auditoría) QUE ESTE ENTREGABLE UTILIZA:
- 12 strings hardcodeados en assets/js/main.js sin mecanismo de traducción
  (líneas 310, 585, 595, 688, 689, 693, 899-903, 994).
- 20 claves theme_mod de tipo TEXTO listas para pll_register_string() sin
  ambigüedad (ver Sección D del informe 9.1).
- 3 casos especiales (categoría E) sin decidir: ce_address, ce_footer_copyright,
  ce_google_reviews_embed.
- 7 URLs del Customizer señaladas, de las cuales 5 tienen riesgo real de
  necesitar selector de página en vez de campo de texto libre (ce_hero_btn1_url,
  ce_hero_btn2_url si se personaliza, ce_financing_btn_url, ce_offer_popup_url,
  ce_testimonials_page_url — esta última con prioridad más alta).
- 2 JSON (ce_stats_custom_items, ce_trust_badges_items) que necesitan un
  identificador estable por fila (no por índice) para registrar el campo
  `label` sin romperse al reordenar/eliminar/agregar filas; suffix y license
  quedan como caso E pendiente de revisar valores reales.
- 4 datos de contacto (ce_phone, ce_email, ce_address, ce_whatsapp_number)
  marcados como "Requiere confirmación del cliente".
- Decisión pendiente sobre si testimonio, miembro_equipo y cliente se marcan
  como CPT traducibles (duplicando nombres propios por idioma) o quedan sin
  traducción.
- El CPT cotizacion debe quedar explícitamente excluido de Polylang (dato
  transaccional, no editorial).

ALCANCE EXACTO DE 9.2:
Este Entregable es EXCLUSIVAMENTE un documento de decisiones. No es
implementación. Debe registrar, para cada punto de la lista anterior marcado
como "B" o "Requiere confirmación del cliente" en la revisión de 9.1, una
respuesta explícita y sin ambigüedad de mi parte.

ARCHIVOS PERMITIDOS PARA MODIFICAR:
- Ninguno de código. Solo se crea un documento nuevo en docs/ (por ejemplo
  docs/I18N_DECISIONES_9.2.md).

ARCHIVOS QUE NO DEBEN TOCARSE:
- Absolutamente ningún archivo PHP, JS o CSS del tema.
- No instalar Polylang.
- No ejecutar pll_register_string().
- No modificar ningún theme_mod ni contenido de la base de datos.

CRITERIOS DE ACEPTACIÓN:
- Cada uno de los ~10 puntos de decisión pendientes tiene una respuesta
  explícita mía, documentada con su justificación.
- No quedan casos "E" (especiales) sin una decisión registrada.

OBLIGACIÓN DE DETENERSE:
Si al redactar las preguntas de decisión aparece alguna ambigüedad o
información que 9.1 no cubrió, debes detenerte y preguntarme antes de asumir
una respuesta en mi nombre.

APROBACIÓN REQUERIDA:
No debes iniciar el Entregable 9.3 (preparación del tema, que sí modifica
inc/enqueue.php y assets/js/main.js) hasta que yo apruebe explícitamente el
documento de decisiones de 9.2 completo.
```
