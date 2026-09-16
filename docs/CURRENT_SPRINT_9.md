# CE Construction — CURRENT_SPRINT_9.md
### Referencia oficial del Sprint 9 ("Internacionalización (i18n) con Polylang")

> Track paralelo e independiente, mismo patrón ya usado por el proyecto para separar `CURRENT_SPRINT.md` (Sprint 8, QA) de `CURRENT_UX_SPRINT.md` (fase UX, cerrada por completo — ver Sprint UX-8 cerrado en `DECISIONS.md` D-112). Ver `docs/DECISIONS.md` D-111 a D-114.

---

## Contexto ya validado (sesiones previas)

- El tema ya es compatible de fábrica con Polylang para contenido editorial (CPTs, Páginas, blog) y textos fijos de plantillas (`__()`/`_e()` con text-domain `ce-construction` en el 100% del código PHP).
- El tema **no** era compatible de fábrica para textos vía Customizer (`get_theme_mod()`) — requieren `pll_register_string()` explícito, ni para las 12 cadenas de `assets/js/main.js` que nunca pasaban por `wp_localize_script()` — **este último punto queda resuelto en el Entregable 9.3** (ver abajo).
- Detección de idioma: por `Accept-Language` del navegador — decisión de producto ya tomada.
- Plugin: Polylang, versión gratuita — decisión ya tomada.

---

## Estado de los Entregables

| Entregable | Objetivo | Estado |
|---|---|---|
| 9.1 | Auditoría completa de `get_theme_mod()` (102 líneas / 97 invocaciones reales / 71 claves únicas), las 641+ cadenas ya internacionalizadas, las 12 cadenas de JS sin mecanismo, inventario de URLs, JSON dinámico, CPT/taxonomías/metaboxes, y riesgo de traducción automática del navegador | ✅ **Aprobado** — `docs/DECISIONS.md` D-113. Documentos: `docs/I18N_AUDIT_SPRINT9_9.1_completo.md`, `docs/I18N_AUDIT_SPRINT9_9.1_REVISION_FINAL_y_PLAN_SPRINT9.md`, anexo `i18n_appendix_C_funciones_ya_internacionalizadas.csv`. |
| 9.2 | Documento de decisiones (sin código) — cierra los 8 puntos "B"/"E" dejados abiertos por 9.1 | ✅ **Aprobado.** Documento: `docs/I18N_DECISIONES_9.2.md`. |
| 9.3 | Preparación del tema: carpeta `/languages` + `.pot` del tema + extensión de `ceConstructionData.i18n` con las 12 claves de JS (punto 1 de 9.2) | ✅ **Entregado — pendiente de tu aprobación explícita.** Ver detalle abajo. |
| 9.4 | Instalación y configuración base de Polylang | ⬜ Propuesto — no inicia sin tu aprobación de 9.3 (D-038). |
| 9.5 | Registro `pll_register_string()` de las 20 claves TEXTO + `ce_footer_copyright` (placeholder protegido, punto 8 de 9.2) | ⬜ Propuesto. |
| 9.6 | CPT/taxonomías traducibles (`servicio`/`proyecto`/`ce_faq`); `testimonio`/`miembro_equipo`/`cliente` explícitamente NO traducibles (punto 3 de 9.2) | ⬜ Propuesto. |
| 9.7 | Selector de página para `ce_testimonials_page_url` únicamente (alcance reducido, punto 2 de 9.2) | ⬜ Propuesto. |
| 9.8 | Identificador estable por fila + registro de `label`/`suffix`/`license` en Estadísticas/Insignias + `translate="no"` condicional sobre `suffix` puro (puntos 4, 5 y 6 de 9.2) | ⬜ Propuesto. |
| 9.9 a 9.13 | SEO/hreflang, pruebas funcionales, regresión, prueba con traductor de navegador, cierre formal | ⬜ Propuestos, sin cambios de alcance. |

---

## Trabajo realizado (Entregable 9.3)

Conforme al punto 1 de `docs/I18N_DECISIONES_9.2.md` (aprobado), se resolvió la brecha de i18n en JavaScript detectada en 9.1: las 12 cadenas que vivían hardcodeadas en `assets/js/main.js` sin pasar nunca por `wp_localize_script()` ahora se leen desde `ceConstructionData.i18n`, con el mismo patrón `CE.i18n.clave || 'texto de respaldo'` ya usado por `openMobileNav`/`closeMobileNav` — el texto de respaldo es idéntico al que había antes y solo se vería si el script de localización no llegara a ejecutarse (progressive enhancement, nunca en producción normal).

**Las 12 claves nuevas** (`inc/enqueue.php`, todas con `__()` y el dominio `ce-construction`): `nombreInvalido`, `emailInvalido`, `telefonoInvalido`, `servicioRequerido`, `mensajeCorto`, `revisaCampos`, `whatsappFallback`, `testimonioDotLabel`, `testimoniosPauseLabel`, `lightboxCerrar`, `lightboxAnterior`, `lightboxSiguiente`.

**Carpeta `/languages` + `.pot` del tema:** generado `languages/ce-construction.pot` extrayendo directamente del código PHP real (no de la copia del anexo CSV, para no heredar posibles imprecisiones de esa auditoría) las invocaciones de `__()`, `_e()`, `esc_html__()`, `esc_html_e()`, `esc_attr__()`, `esc_attr_e()`, `_x()`/`_ex()`/`esc_attr_x()`/`esc_html_x()` con dominio `ce-construction`. Resultado: **645 invocaciones reales, 504 cadenas únicas** en el `.pot`. `load_theme_textdomain( 'ce-construction', CE_THEME_DIR . '/languages' )` ya existía correctamente en `inc/setup.php` desde antes — **no requirió ningún cambio**.

**Reconciliación del conteo frente a la auditoría 9.1 (641 llamadas i18n reportadas, documentada aquí para trazabilidad, sin ocultar la diferencia):**
- La auditoría 9.1 incluía 4 llamadas que no generan `msgid` extraíble (`number_format_i18n()` × 1, `date_i18n()` × 3 — ninguna envuelve un string literal en `__()`, WordPress las traduce por su propio mecanismo de locale, no por `.pot`/`.po`). Excluidas del `.pot` por diseño, no por omisión.
- Se detectó y descartó **1 falso positivo real**: una mención de `__( 'Servicios', 'ce-construction' )` dentro de un docblock de ejemplo en `template-parts/page-hero.php` (comentario, no código ejecutable) — misma categoría que los falsos positivos de `get_theme_mod()` ya documentados en la propia auditoría 9.1.
- El resto de la diferencia (641 − 4 − 1 = 636 esperadas vs. 645 extraídas, +9) corresponde a invocaciones reales de `__()` adicionales presentes hoy en el código que la auditoría de 9.1 no capturó — muy probablemente por su método de extracción (grep línea por línea), que puede no detectar una llamada `__()` cuyos argumentos se envuelven en más de una línea física; el extractor de este Entregable procesa el archivo completo (no línea por línea), por lo que sí las captura. No se encontró ningún otro falso positivo al revisar manualmente los casos de mayor variación (`esc_attr_e`, `_x`) — el único `_x(` de todo el árbol resultó ser, también, una mención dentro de un comentario, sin argumentos reales (confirma que la auditoría 9.1 contaba esa mención como si fuera una invocación real; se excluye aquí correctamente).

**Verificado explícitamente que este Entregable no toca:** ningún `theme_mod`, ninguna plantilla PHP fuera de `inc/enqueue.php`, no se instaló Polylang, no se ejecutó `pll_register_string()`. Las 6 claves de `i18n` ya existentes (`sending`, `error`, `pauseSlider`, `resumeSlider`, `openMobileNav`, `closeMobileNav`) permanecen sin cambios.

## Archivos creados / modificados (Entregable 9.3)
- **Creado:** `languages/ce-construction.pot`.
- **Modificados:** `inc/enqueue.php` (12 claves nuevas en el array `i18n` de `wp_localize_script()`), `assets/js/main.js` (los 12 puntos hardcodeados reemplazados por `CE.i18n.clave || 'respaldo'`; docblock de cabecera actualizado para listar las claves vigentes).
- **Sin cambios:** `inc/setup.php` (ya cargaba `/languages` correctamente), todo el resto del tema.

## Validaciones ejecutadas
- Balance de llaves/paréntesis verificado en `inc/enqueue.php` (12/12 llaves, 186/186 paréntesis) — `php -l` no disponible en este entorno (limitación metodológica ya documentada en `ARCHITECTURE.md` §10).
- `node --check assets/js/main.js` → sintaxis válida.
- Confirmado por `grep` que ninguna de las 12 cadenas originales quedó sin su acceso `CE.i18n.*` correspondiente.
- `.pot` verificado con balance `msgid`/`msgstr` (505/505, incluyendo la cabecera) — estructura válida.
- **Pendiente de prueba funcional real en WordPress/navegador** (misma limitación metodológica ya documentada en todo el proyecto — sin entorno WordPress/navegador real disponible en este entorno de desarrollo): confirmar visualmente que el sitio en español no cambió (los 12 textos se ven idénticos a como estaban), y que `wp i18n make-pot`/Poedit/Loco Translate pueden abrir `languages/ce-construction.pot` sin errores de formato.

## Documentación pendiente, sin cambios de alcance
`docs/TREE.md`, `docs/CHANGELOG.md`, `docs/PROJECT_STATUS.md`, `docs/TODO.md`, `docs/HANDOFF.md` — no actualizados en esta entrega (se difieren al cierre formal del Sprint 9, Entregable 9.13, o a un punto de cierre más significativo, mismo criterio D-034 ya vigente en el proyecto). `docs/DECISIONS.md` sí requiere una entrada nueva (D-115) para este Entregable — pendiente de incorporar en el próximo cierre documental.

## Próximo paso
El Entregable 9.4 (instalación y configuración base de Polylang) **no inicia** sin que apruebes explícitamente el 9.3 tal como fue entregado (o indiques los ajustes que corresponda), conforme a D-038 — y sin que confirmes el comportamiento en español sin cambios, criterio de aceptación propio de este Entregable.
