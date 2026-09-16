# Auditoría Completa de Internacionalización del Tema — CE Construction

**Sprint 9, Entregable 9.1 (ampliado)** — Diagnóstico previo a Polylang
**Estado: diagnóstico únicamente. No se ha modificado ningún archivo, no se ha instalado Polylang, no se ha ejecutado `pll_register_string()`.**

> Nota sobre continuidad de sesión: este documento retoma el Entregable 9.1 desde el punto exacto en que quedó — la Sección 1 (get_theme_mod) ya estaba cerrada y verificada; lo que sigue (Secciones 2 a 12) es la continuación, no una repetición. No se ha vuelto a revisar el proyecto completo desde cero: todo lo que aparece aquí se apoya en los mismos comandos de búsqueda ya ejecutados sobre el código real del tema (`ce_construction-theme.zip`), sin releer documentación de sprints anteriores no relacionada con i18n.

---

## A. Resumen ejecutivo

| Métrica | Valor |
|---|---|
| Líneas físicas que contienen el token `get_theme_mod(` | 102 |
| — de ellas, menciones dentro de comentarios/docblocks (no son invocaciones) | 6 |
| **Invocaciones reales de `get_theme_mod()`** | **97** |
| Claves únicas de `theme_mod` (resolviendo las 2 claves dinámicas) | 71 |
| Llamadas ya envueltas en funciones de internacionalización de WordPress (`__()`, `_e()`, `esc_html__()`, etc.) | 641 |
| Textos hardcodeados sin ningún mecanismo de traducción encontrados en PHP/HTML | 0 (ver nota) |
| Textos hardcodeados sin mecanismo de traducción encontrados en JavaScript | 8 (frontend, `main.js`) |
| URLs internas generadas con funciones de WordPress (auto-filtrables por Polylang) | ~69 (`esc_url()`) + 25 (`get_permalink()`) + 23 (`get_post_type_archive_link()`) + 19 (`home_url()`) + 2 (`get_term_link()`) |
| `theme_mod` de tipo URL configurable desde el Customizer | 12 claves concretas (ver Sección E) |
| Casos especiales sin clasificar unilateralmente (categoría E) | 3 (`ce_address`, `ce_footer_copyright`, `ce_google_reviews_embed`) |
| Estructuras JSON/repetidor detectadas | 2 (`ce_stats_custom_items`, `ce_trust_badges_items`) — más 2 estructuras técnicas afines sin texto (`ce_hero_slides`, `ce_home_sections_order`) |
| Vulnerabilidad confirmada frente a traducción automática del navegador | **Ninguna encontrada en el código auditado** (ver Sección I) |

**Nota importante sobre "0 textos hardcodeados en PHP":** la búsqueda exhaustiva de texto visible impreso directamente sin pasar por `__()`/`_e()`/`esc_html_e()`/etc. (patrones `echo 'Texto...'`, `$var = 'Texto...'` sin envolver, atributos `alt=`/`title=`/`aria-label=`/`placeholder=` con literal fijo) **no encontró ningún caso real** en los archivos PHP/HTML del tema. Esto es una diferencia relevante respecto al ejemplo hipotético del encargo (`services.php`): ese archivo, en este código, ya usa `esc_html_e()` correctamente en sus 5 apariciones de texto. La única brecha real de "hardcodeo sin mecanismo de traducción" que encontré está en JavaScript (`assets/js/main.js`), detallada en la Sección F.

---

## B. Sección 1 — Inventario y reconciliación de `get_theme_mod()`

### B.1 Metodología de conteo (para que el total sea verificable)

1. Se ejecutó `grep -rn "get_theme_mod(" --include="*.php" .` sobre la raíz del tema → **102 líneas físicas** con al menos una coincidencia.
2. De esas 102 líneas, se identificaron **6 líneas que son comentarios o docblocks** que solo *mencionan* `get_theme_mod()` como referencia explicativa, sin ejecutarlo:
   - `template-parts/financing.php:49`
   - `template-parts/cta.php:70` y `:76`
   - `template-parts/hero.php:160`
   - `inc/customizer.php:1513`
   - `inc/helpers.php:846`
3. Quedan **96 líneas físicas de código real**.
4. De esas 96, **`footer.php:100` contiene 2 invocaciones reales en la misma línea física** (`get_theme_mod( 'ce_email' )` aparece dos veces: una dentro de `esc_attr()` para el `href="mailto:"` y otra dentro de `esc_html()` para el texto visible del enlace).
5. Total de **invocaciones reales: 96 líneas − 1 línea (la doble, contada aparte) + 2 invocaciones de esa línea = 97 invocaciones reales.**
6. Dos invocaciones adicionales usan **claves dinámicas** que un grep de una sola línea no captura directamente porque el nombre de la clave depende de una variable, no de un literal fijo:
   - `inc/helpers.php:31` → `get_theme_mod( 'ce_social_' . $network, '' )` dentro de un `foreach` sobre 5 redes sociales (facebook, instagram, linkedin, youtube, tiktok) → **5 claves concretas** desde **1 invocación física**.
   - `template-parts/cta.php:57,61,65,66,67` → `get_theme_mod( $ce_cta_prefix . 'campo', ... )`, donde `$ce_cta_prefix` vale `'ce_cta_'` o `'ce_cta2_'` según la variante (primaria/secundaria/sidebar) con la que se invoque la plantilla → **5 invocaciones físicas, 10 claves concretas** (5 campos × 2 prefijos).
7. Dos invocaciones más están **formateadas en varias líneas físicas** (el nombre de la clave no está en la misma línea que el token `get_theme_mod(`), por lo que un grep de una sola línea tampoco las captura como par clave-línea directo. Se localizaron revisando el código fuente completo de `footer.php`:
   - `footer.php:27` → la apertura de la llamada; la clave `'ce_footer_about'` está en la línea 28.
   - `footer.php:134` → la apertura de la llamada; la clave `'ce_footer_copyright'` está en la línea 135.

### B.2 Reconciliación final

| Concepto | Cantidad |
|---|---:|
| Líneas físicas con el token `get_theme_mod(` | 102 |
| − Menciones en comentarios (excluidas) | 6 |
| = Líneas físicas de código real | 96 |
| + 1 invocación extra en `footer.php:100` (misma línea, 2 llamadas) | +1 |
| **= Invocaciones reales totales** | **97** |
| Claves únicas resueltas (incluyendo las 15 concretas detrás de las 2 claves dinámicas) | **71** |

Este total (97 invocaciones / 71 claves únicas) **reemplaza y corrige** cualquier conteo anterior que no distinguiera explícitamente comentarios, líneas dobles, llamadas multilínea y claves dinámicas.

### B.3 Inventario completo, invocación por invocación

| # | Archivo | Línea | Clave `theme_mod` | Categoría | Motivo de la clasificación |
|---:|---|---:|---|---|---|
| 1 | `./footer.php` | 27 | ce_footer_about (llamada formateada en varias líneas) | TEXTO | Texto libre visible para el usuario final, configurado desde el Customizer; candidato directo a `pll_register_string()` (o equivalente) para tener una versión por idioma. La clave literal aparece en una línea física posterior porque la llamada a get_theme_mod() se envuelve en varias líneas. |
| 2 | `./footer.php` | 85 | ce_address | E-CASO ESPECIAL | No es evidente si debe variar por idioma; se documenta sin decidir unilateralmente. |
| 3 | `./footer.php` | 88 | ce_address | E-CASO ESPECIAL | No es evidente si debe variar por idioma; se documenta sin decidir unilateralmente. |
| 4 | `./footer.php` | 91 | ce_phone | CONTACTO | Dato de contacto del negocio (no "contenido editorial"); en principio el mismo valor en todos los idiomas, salvo que el negocio tenga datos de contacto distintos por país/idioma (caso E si aplica). |
| 5 | `./footer.php` | 94 | ce_phone | CONTACTO | Dato de contacto del negocio (no "contenido editorial"); en principio el mismo valor en todos los idiomas, salvo que el negocio tenga datos de contacto distintos por país/idioma (caso E si aplica). |
| 6 | `./footer.php` | 97 | ce_email | CONTACTO | Dato de contacto del negocio (no "contenido editorial"); en principio el mismo valor en todos los idiomas, salvo que el negocio tenga datos de contacto distintos por país/idioma (caso E si aplica). |
| 7 | `./footer.php` | 100 | ce_email (invocación 1 de 2 EN LA MISMA LÍNEA FÍSICA 100) | CONTACTO | Dato de contacto del negocio (no "contenido editorial"); en principio el mismo valor en todos los idiomas, salvo que el negocio tenga datos de contacto distintos por país/idioma (caso E si aplica). |
| 8 | `./footer.php` | 100 | ce_email (invocación 2 de 2 EN LA MISMA LÍNEA FÍSICA 100) | CONTACTO | Dato de contacto del negocio (no "contenido editorial"); en principio el mismo valor en todos los idiomas, salvo que el negocio tenga datos de contacto distintos por país/idioma (caso E si aplica). |
| 9 | `./footer.php` | 103 | ce_schedule | TEXTO | Texto libre visible para el usuario final, configurado desde el Customizer; candidato directo a `pll_register_string()` (o equivalente) para tener una versión por idioma. |
| 10 | `./footer.php` | 106 | ce_schedule | TEXTO | Texto libre visible para el usuario final, configurado desde el Customizer; candidato directo a `pll_register_string()` (o equivalente) para tener una versión por idioma. |
| 11 | `./footer.php` | 113 | ce_maps_embed_url | URL | URL de destino. No es una cadena de texto traducible per se, pero puede necesitar variar por idioma — ver auditoría de URLs (Sección 5). |
| 12 | `./footer.php` | 115 | ce_maps_embed_url | URL | URL de destino. No es una cadena de texto traducible per se, pero puede necesitar variar por idioma — ver auditoría de URLs (Sección 5). |
| 13 | `./footer.php` | 134 | ce_footer_copyright (llamada formateada en varias líneas) | E-CASO ESPECIAL | No es evidente si debe variar por idioma; se documenta sin decidir unilateralmente. La clave literal aparece en una línea física posterior porque la llamada a get_theme_mod() se envuelve en varias líneas. |
| 14 | `./footer.php` | 202 | ce_quote_form_mode | CONFIG | Valor de configuración interno (booleano, número o palabra clave de modo/layout). No es contenido de cara al usuario y no debe traducirse. |
| 15 | `./template-parts/sidebar-servicios.php` | 57 | ce_phone | CONTACTO | Dato de contacto del negocio (no "contenido editorial"); en principio el mismo valor en todos los idiomas, salvo que el negocio tenga datos de contacto distintos por país/idioma (caso E si aplica). |
| 16 | `./template-parts/sidebar-servicios.php` | 60 | ce_phone | CONTACTO | Dato de contacto del negocio (no "contenido editorial"); en principio el mismo valor en todos los idiomas, salvo que el negocio tenga datos de contacto distintos por país/idioma (caso E si aplica). |
| 17 | `./template-parts/sidebar-servicios.php` | 82 | ce_sidebar_servicios_slot | CONFIG | Valor de configuración interno (booleano, número o palabra clave de modo/layout). No es contenido de cara al usuario y no debe traducirse. |
| 18 | `./template-parts/google-reviews.php` | 59 | ce_google_reviews_embed | E-CASO ESPECIAL | No es evidente si debe variar por idioma; se documenta sin decidir unilateralmente. |
| 19 | `./template-parts/page-hero.php` | 69 | ce_hero_overlay_opacity | CONFIG | Valor de configuración interno (booleano, número o palabra clave de modo/layout). No es contenido de cara al usuario y no debe traducirse. |
| 20 | `./template-parts/financing.php` | 44 | ce_financing_title | TEXTO | Texto libre visible para el usuario final, configurado desde el Customizer; candidato directo a `pll_register_string()` (o equivalente) para tener una versión por idioma. |
| 21 | `./template-parts/financing.php` | 45 | ce_financing_text | TEXTO | Texto libre visible para el usuario final, configurado desde el Customizer; candidato directo a `pll_register_string()` (o equivalente) para tener una versión por idioma. |
| 22 | `./template-parts/financing.php` | 46 | ce_financing_btn_text | TEXTO | Texto libre visible para el usuario final, configurado desde el Customizer; candidato directo a `pll_register_string()` (o equivalente) para tener una versión por idioma. |
| 23 | `./template-parts/financing.php` | 47 | ce_financing_btn_url | URL | URL de destino. No es una cadena de texto traducible per se, pero puede necesitar variar por idioma — ver auditoría de URLs (Sección 5). |
| 24 | `./template-parts/financing.php` | 49 | (comentario, NO es invocación real) | N/A | Mención de `get_theme_mod()` dentro de un comentario o docblock explicativo — no ejecuta ninguna llamada real. |
| 25 | `./template-parts/sidebar-proyectos.php` | 57 | ce_phone | CONTACTO | Dato de contacto del negocio (no "contenido editorial"); en principio el mismo valor en todos los idiomas, salvo que el negocio tenga datos de contacto distintos por país/idioma (caso E si aplica). |
| 26 | `./template-parts/sidebar-proyectos.php` | 60 | ce_phone | CONTACTO | Dato de contacto del negocio (no "contenido editorial"); en principio el mismo valor en todos los idiomas, salvo que el negocio tenga datos de contacto distintos por país/idioma (caso E si aplica). |
| 27 | `./template-parts/sidebar-proyectos.php` | 76 | ce_sidebar_proyectos_slot | CONFIG | Valor de configuración interno (booleano, número o palabra clave de modo/layout). No es contenido de cara al usuario y no debe traducirse. |
| 28 | `./template-parts/quote-form.php` | 97 | ce_quote_form_mode | CONFIG | Valor de configuración interno (booleano, número o palabra clave de modo/layout). No es contenido de cara al usuario y no debe traducirse. |
| 29 | `./template-parts/quote-form.php` | 173 | ce_hero_quote_card_opacity | CONFIG | Valor de configuración interno (booleano, número o palabra clave de modo/layout). No es contenido de cara al usuario y no debe traducirse. |
| 30 | `./template-parts/cta.php` | 57 | $ce_cta_prefix . 'title' &rarr; ce_cta_title / ce_cta2_title | TEXTO | Clave DINÁMICA: 1 invocación física cuyo prefijo depende de $ce_cta_prefix ('ce_cta_' variante primaria / 'ce_cta2_' variante secundaria en template-parts/cta.php) — corresponde a 2 claves concretas. |
| 31 | `./template-parts/cta.php` | 61 | $ce_cta_prefix . 'text' &rarr; ce_cta_text / ce_cta2_text | TEXTO | Clave DINÁMICA: 1 invocación física cuyo prefijo depende de $ce_cta_prefix ('ce_cta_' variante primaria / 'ce_cta2_' variante secundaria en template-parts/cta.php) — corresponde a 2 claves concretas. |
| 32 | `./template-parts/cta.php` | 65 | $ce_cta_prefix . 'btn_text' &rarr; ce_cta_btn_text / ce_cta2_btn_text | TEXTO | Clave DINÁMICA: 1 invocación física cuyo prefijo depende de $ce_cta_prefix ('ce_cta_' variante primaria / 'ce_cta2_' variante secundaria en template-parts/cta.php) — corresponde a 2 claves concretas. |
| 33 | `./template-parts/cta.php` | 66 | $ce_cta_prefix . 'icon' &rarr; ce_cta_icon / ce_cta2_icon | ICONO | Clave DINÁMICA: 1 invocación física cuyo prefijo depende de $ce_cta_prefix ('ce_cta_' variante primaria / 'ce_cta2_' variante secundaria en template-parts/cta.php) — corresponde a 2 claves concretas. |
| 34 | `./template-parts/cta.php` | 67 | $ce_cta_prefix . 'btn_url' &rarr; ce_cta_btn_url / ce_cta2_btn_url | URL | Clave DINÁMICA: 1 invocación física cuyo prefijo depende de $ce_cta_prefix ('ce_cta_' variante primaria / 'ce_cta2_' variante secundaria en template-parts/cta.php) — corresponde a 2 claves concretas. |
| 35 | `./template-parts/cta.php` | 70 | (comentario, NO es invocación real) | N/A | Mención de `get_theme_mod()` dentro de un comentario o docblock explicativo — no ejecuta ninguna llamada real. |
| 36 | `./template-parts/cta.php` | 76 | (comentario, NO es invocación real) | N/A | Mención de `get_theme_mod()` dentro de un comentario o docblock explicativo — no ejecuta ninguna llamada real. |
| 37 | `./template-parts/testimonials.php` | 32 | ce_testimonials_page_url | URL | URL de destino. No es una cadena de texto traducible per se, pero puede necesitar variar por idioma — ver auditoría de URLs (Sección 5). |
| 38 | `./template-parts/hero.php` | 91 | ce_hero_image | MEDIA_ID | Almacena un ID numérico de adjunto de la Media Library, no texto. No es una cadena traducible; a lo sumo la IMAGEN podría variar por idioma (decisión de contenido, no de i18n de cadenas). |
| 39 | `./template-parts/hero.php` | 101 | ce_hero_type | CONFIG | Valor de configuración interno (booleano, número o palabra clave de modo/layout). No es contenido de cara al usuario y no debe traducirse. |
| 40 | `./template-parts/hero.php` | 120 | ce_hero_overlay_opacity | CONFIG | Valor de configuración interno (booleano, número o palabra clave de modo/layout). No es contenido de cara al usuario y no debe traducirse. |
| 41 | `./template-parts/hero.php` | 142 | ce_hero_layout | CONFIG | Valor de configuración interno (booleano, número o palabra clave de modo/layout). No es contenido de cara al usuario y no debe traducirse. |
| 42 | `./template-parts/hero.php` | 143 | ce_hero_show_quote_form | CONFIG | Valor de configuración interno (booleano, número o palabra clave de modo/layout). No es contenido de cara al usuario y no debe traducirse. |
| 43 | `./template-parts/hero.php` | 151 | ce_hero_title | TEXTO | Texto libre visible para el usuario final, configurado desde el Customizer; candidato directo a `pll_register_string()` (o equivalente) para tener una versión por idioma. |
| 44 | `./template-parts/hero.php` | 152 | ce_hero_subtitle | TEXTO | Texto libre visible para el usuario final, configurado desde el Customizer; candidato directo a `pll_register_string()` (o equivalente) para tener una versión por idioma. |
| 45 | `./template-parts/hero.php` | 153 | ce_hero_btn1_text | TEXTO | Texto libre visible para el usuario final, configurado desde el Customizer; candidato directo a `pll_register_string()` (o equivalente) para tener una versión por idioma. |
| 46 | `./template-parts/hero.php` | 154 | ce_hero_btn1_url | URL | URL de destino. No es una cadena de texto traducible per se, pero puede necesitar variar por idioma — ver auditoría de URLs (Sección 5). |
| 47 | `./template-parts/hero.php` | 160 | (comentario, NO es invocación real) | N/A | Mención de `get_theme_mod()` dentro de un comentario o docblock explicativo — no ejecuta ninguna llamada real. |
| 48 | `./template-parts/hero.php` | 165 | ce_hero_btn2_text | TEXTO | Texto libre visible para el usuario final, configurado desde el Customizer; candidato directo a `pll_register_string()` (o equivalente) para tener una versión por idioma. |
| 49 | `./template-parts/hero.php` | 166 | ce_hero_btn2_url | URL | URL de destino. No es una cadena de texto traducible per se, pero puede necesitar variar por idioma — ver auditoría de URLs (Sección 5). |
| 50 | `./inc/customizer.php` | 152 | ce_home_sections_order | ORDEN/SLUG | Clave técnica de orden de secciones (slug interno), no es texto visible. |
| 51 | `./inc/customizer.php` | 1300 | ce_color_primary | COLOR | Valor de color (hex). No es texto y no debe traducirse. |
| 52 | `./inc/customizer.php` | 1301 | ce_color_secondary | COLOR | Valor de color (hex). No es texto y no debe traducirse. |
| 53 | `./inc/customizer.php` | 1302 | ce_color_accent | COLOR | Valor de color (hex). No es texto y no debe traducirse. |
| 54 | `./inc/customizer.php` | 1303 | ce_font_heading | COLOR/FUENTE | Nombre de familia tipográfica. No es contenido traducible. |
| 55 | `./inc/customizer.php` | 1304 | ce_font_body | COLOR/FUENTE | Nombre de familia tipográfica. No es contenido traducible. |
| 56 | `./inc/customizer.php` | 1323 | ce_cta_btn_color | COLOR | Valor de color (hex). No es texto y no debe traducirse. |
| 57 | `./inc/customizer.php` | 1324 | ce_cta2_btn_color | COLOR | Valor de color (hex). No es texto y no debe traducirse. |
| 58 | `./inc/customizer.php` | 1513 | (comentario, NO es invocación real) | N/A | Mención de `get_theme_mod()` dentro de un comentario o docblock explicativo — no ejecuta ninguna llamada real. |
| 59 | `./inc/enqueue.php` | 99 | ce_whatsapp_number | CONTACTO | Dato de contacto del negocio (no "contenido editorial"); en principio el mismo valor en todos los idiomas, salvo que el negocio tenga datos de contacto distintos por país/idioma (caso E si aplica). |
| 60 | `./inc/quote-form.php` | 485 | ce_email | CONTACTO | Dato de contacto del negocio (no "contenido editorial"); en principio el mismo valor en todos los idiomas, salvo que el negocio tenga datos de contacto distintos por país/idioma (caso E si aplica). |
| 61 | `./inc/helpers.php` | 31 | 'ce_social_' . $network &rarr; ce_social_facebook, ce_social_instagram, ce_social_linkedin, ce_social_youtube, ce_social_tiktok | URL | Clave DINÁMICA: 1 invocación física dentro de un foreach cuyo sufijo depende de $network (facebook, instagram, linkedin, youtube, tiktok) — corresponde a 5 claves concretas de theme_mod distintas. |
| 62 | `./inc/helpers.php` | 68 | ce_whatsapp_number | CONTACTO | Dato de contacto del negocio (no "contenido editorial"); en principio el mismo valor en todos los idiomas, salvo que el negocio tenga datos de contacto distintos por país/idioma (caso E si aplica). |
| 63 | `./inc/helpers.php` | 100 | ce_phone | CONTACTO | Dato de contacto del negocio (no "contenido editorial"); en principio el mismo valor en todos los idiomas, salvo que el negocio tenga datos de contacto distintos por país/idioma (caso E si aplica). |
| 64 | `./inc/helpers.php` | 271 | ce_offer_popup_enabled | CONFIG | Valor de configuración interno (booleano, número o palabra clave de modo/layout). No es contenido de cara al usuario y no debe traducirse. |
| 65 | `./inc/helpers.php` | 275 | ce_offer_popup_title | TEXTO | Texto libre visible para el usuario final, configurado desde el Customizer; candidato directo a `pll_register_string()` (o equivalente) para tener una versión por idioma. |
| 66 | `./inc/helpers.php` | 276 | ce_offer_popup_text | TEXTO | Texto libre visible para el usuario final, configurado desde el Customizer; candidato directo a `pll_register_string()` (o equivalente) para tener una versión por idioma. |
| 67 | `./inc/helpers.php` | 283 | ce_offer_popup_action | CONFIG | Valor de configuración interno (booleano, número o palabra clave de modo/layout). No es contenido de cara al usuario y no debe traducirse. |
| 68 | `./inc/helpers.php` | 285 | ce_offer_popup_url | URL | URL de destino. No es una cadena de texto traducible per se, pero puede necesitar variar por idioma — ver auditoría de URLs (Sección 5). |
| 69 | `./inc/helpers.php` | 296 | ce_offer_popup_badge_text | TEXTO | Texto libre visible para el usuario final, configurado desde el Customizer; candidato directo a `pll_register_string()` (o equivalente) para tener una versión por idioma. |
| 70 | `./inc/helpers.php` | 297 | ce_offer_popup_icon | ICONO | Clase CSS de icono (Font Awesome). No es traducible. |
| 71 | `./inc/helpers.php` | 298 | ce_offer_popup_btn_text | TEXTO | Texto libre visible para el usuario final, configurado desde el Customizer; candidato directo a `pll_register_string()` (o equivalente) para tener una versión por idioma. |
| 72 | `./inc/helpers.php` | 300 | ce_offer_popup_delay | CONFIG | Valor de configuración interno (booleano, número o palabra clave de modo/layout). No es contenido de cara al usuario y no debe traducirse. |
| 73 | `./inc/helpers.php` | 301 | ce_offer_popup_dismiss_minutes | CONFIG | Valor de configuración interno (booleano, número o palabra clave de modo/layout). No es contenido de cara al usuario y no debe traducirse. |
| 74 | `./inc/helpers.php` | 302 | ce_offer_popup_convert_minutes | CONFIG | Valor de configuración interno (booleano, número o palabra clave de modo/layout). No es contenido de cara al usuario y no debe traducirse. |
| 75 | `./inc/helpers.php` | 700 | ce_quote_form_mode | CONFIG | Valor de configuración interno (booleano, número o palabra clave de modo/layout). No es contenido de cara al usuario y no debe traducirse. |
| 76 | `./inc/helpers.php` | 846 | (comentario, NO es invocación real) | N/A | Mención de `get_theme_mod()` dentro de un comentario o docblock explicativo — no ejecuta ninguna llamada real. |
| 77 | `./inc/helpers.php` | 857 | ce_hero_video | MEDIA_ID | Almacena un ID numérico de adjunto de la Media Library, no texto. No es una cadena traducible; a lo sumo la IMAGEN podría variar por idioma (decisión de contenido, no de i18n de cadenas). |
| 78 | `./inc/helpers.php` | 868 | ce_hero_slides | MEDIA_ID | Almacena un ID numérico de adjunto de la Media Library, no texto. No es una cadena traducible; a lo sumo la IMAGEN podría variar por idioma (decisión de contenido, no de i18n de cadenas). |
| 79 | `./inc/helpers.php` | 929 | ce_hero_image | MEDIA_ID | Almacena un ID numérico de adjunto de la Media Library, no texto. No es una cadena traducible; a lo sumo la IMAGEN podría variar por idioma (decisión de contenido, no de i18n de cadenas). |
| 80 | `./inc/helpers.php` | 998 | ce_hero_overlay_color | COLOR | Valor de color (hex). No es texto y no debe traducirse. |
| 81 | `./inc/helpers.php` | 999 | ce_hero_overlay_direction | CONFIG | Valor de configuración interno (booleano, número o palabra clave de modo/layout). No es contenido de cara al usuario y no debe traducirse. |
| 82 | `./inc/helpers.php` | 1000 | ce_hero_overlay_extent | CONFIG | Valor de configuración interno (booleano, número o palabra clave de modo/layout). No es contenido de cara al usuario y no debe traducirse. |
| 83 | `./inc/helpers.php` | 1109 | ce_footer_logo | MEDIA_ID | Almacena un ID numérico de adjunto de la Media Library, no texto. No es una cadena traducible; a lo sumo la IMAGEN podría variar por idioma (decisión de contenido, no de i18n de cadenas). |
| 84 | `./inc/helpers.php` | 1284 | ce_stats_custom_items | JSON | Estructura repetidora (array serializado en JSON) que mezcla texto visible con datos técnicos; no debe registrarse como una única cadena — ver Sección 10. |
| 85 | `./inc/helpers.php` | 1374 | ce_trust_badges_items | JSON | Estructura repetidora (array serializado en JSON) que mezcla texto visible con datos técnicos; no debe registrarse como una única cadena — ver Sección 10. |
| 86 | `./inc/widgets.php` | 40 | ce_phone | CONTACTO | Dato de contacto del negocio (no "contenido editorial"); en principio el mismo valor en todos los idiomas, salvo que el negocio tenga datos de contacto distintos por país/idioma (caso E si aplica). |
| 87 | `./inc/widgets.php` | 41 | ce_email | CONTACTO | Dato de contacto del negocio (no "contenido editorial"); en principio el mismo valor en todos los idiomas, salvo que el negocio tenga datos de contacto distintos por país/idioma (caso E si aplica). |
| 88 | `./inc/widgets.php` | 42 | ce_address | E-CASO ESPECIAL | No es evidente si debe variar por idioma; se documenta sin decidir unilateralmente. |
| 89 | `./inc/widgets.php` | 43 | ce_schedule | TEXTO | Texto libre visible para el usuario final, configurado desde el Customizer; candidato directo a `pll_register_string()` (o equivalente) para tener una versión por idioma. |
| 90 | `./inc/seo.php` | 173 | ce_hero_image | MEDIA_ID | Almacena un ID numérico de adjunto de la Media Library, no texto. No es una cadena traducible; a lo sumo la IMAGEN podría variar por idioma (decisión de contenido, no de i18n de cadenas). |
| 91 | `./inc/seo.php` | 178 | ce_hero_image | MEDIA_ID | Almacena un ID numérico de adjunto de la Media Library, no texto. No es una cadena traducible; a lo sumo la IMAGEN podría variar por idioma (decisión de contenido, no de i18n de cadenas). |
| 92 | `./inc/seo.php` | 230 | ce_phone | CONTACTO | Dato de contacto del negocio (no "contenido editorial"); en principio el mismo valor en todos los idiomas, salvo que el negocio tenga datos de contacto distintos por país/idioma (caso E si aplica). |
| 93 | `./inc/seo.php` | 231 | ce_email | CONTACTO | Dato de contacto del negocio (no "contenido editorial"); en principio el mismo valor en todos los idiomas, salvo que el negocio tenga datos de contacto distintos por país/idioma (caso E si aplica). |
| 94 | `./inc/seo.php` | 234 | ce_address | E-CASO ESPECIAL | No es evidente si debe variar por idioma; se documenta sin decidir unilateralmente. |
| 95 | `./header.php` | 34 | ce_phone | CONTACTO | Dato de contacto del negocio (no "contenido editorial"); en principio el mismo valor en todos los idiomas, salvo que el negocio tenga datos de contacto distintos por país/idioma (caso E si aplica). |
| 96 | `./header.php` | 37 | ce_phone | CONTACTO | Dato de contacto del negocio (no "contenido editorial"); en principio el mismo valor en todos los idiomas, salvo que el negocio tenga datos de contacto distintos por país/idioma (caso E si aplica). |
| 97 | `./header.php` | 40 | ce_email | CONTACTO | Dato de contacto del negocio (no "contenido editorial"); en principio el mismo valor en todos los idiomas, salvo que el negocio tenga datos de contacto distintos por país/idioma (caso E si aplica). |
| 98 | `./header.php` | 41 | ce_email | CONTACTO | Dato de contacto del negocio (no "contenido editorial"); en principio el mismo valor en todos los idiomas, salvo que el negocio tenga datos de contacto distintos por país/idioma (caso E si aplica). |
| 99 | `./header.php` | 43 | ce_email | CONTACTO | Dato de contacto del negocio (no "contenido editorial"); en principio el mismo valor en todos los idiomas, salvo que el negocio tenga datos de contacto distintos por país/idioma (caso E si aplica). |
| 100 | `./header.php` | 46 | ce_schedule | TEXTO | Texto libre visible para el usuario final, configurado desde el Customizer; candidato directo a `pll_register_string()` (o equivalente) para tener una versión por idioma. |
| 101 | `./header.php` | 47 | ce_schedule | TEXTO | Texto libre visible para el usuario final, configurado desde el Customizer; candidato directo a `pll_register_string()` (o equivalente) para tener una versión por idioma. |
| 102 | `./header.php` | 79 | ce_phone | CONTACTO | Dato de contacto del negocio (no "contenido editorial"); en principio el mismo valor en todos los idiomas, salvo que el negocio tenga datos de contacto distintos por país/idioma (caso E si aplica). |
| 103 | `./header.php` | 82 | ce_phone | CONTACTO | Dato de contacto del negocio (no "contenido editorial"); en principio el mismo valor en todos los idiomas, salvo que el negocio tenga datos de contacto distintos por país/idioma (caso E si aplica). |


## C. Inventario de textos ya internacionalizados (funciones WordPress)

Se buscaron exhaustivamente en todos los `.php` del tema las funciones: `__()`, `_e()`, `esc_html__()`, `esc_html_e()`, `esc_attr__()`, `esc_attr_e()`, `_x()`, `_ex()`, `esc_attr_x()`, `esc_html_x()`, `number_format_i18n()`, `date_i18n()`, `translate()`, `gettext()`.

| Función | Ocurrencias reales (excluyendo falsos positivos por subcadena) |
|---|---:|
| `__()` | 374 |
| `_e()` (forma "pelada", sin escapar) | **0** — el tema nunca usa `_e()` a secas, siempre la variante que escapa (`esc_html_e()`/`esc_attr_e()`). Buena práctica. |
| `esc_html__()` | 37 |
| `esc_html_e()` | 181 |
| `esc_attr__()` | 1 |
| `esc_attr_e()` | 43 |
| `_x()` | 1 |
| `_ex()`, `esc_attr_x()`, `esc_html_x()` | 0 |
| `number_format_i18n()` | 1 |
| `date_i18n()` | 3 |
| `translate()`, `gettext()` | 0 |
| **Total** | **641** |

**Verificación de text domain:** las 641 llamadas usan de forma consistente el dominio `'ce-construction'` — no se encontró ninguna cadena con un dominio distinto ni sin segundo argumento. `style.css` declara `Text Domain: ce-construction` y `inc/setup.php:14` llama a `load_theme_textdomain( 'ce-construction', CE_THEME_DIR . '/languages' )` correctamente.

**Hallazgo (no bloqueante, para Fase 1):** no existe todavía una carpeta `/languages` ni ningún archivo `.pot`/`.po`/`.mo` en el tema. `load_theme_textdomain()` está bien invocado, pero no tiene nada que cargar todavía. Esto no afecta a Polylang en sí (Polylang no depende de esto para las cadenas registradas vía `pll_register_string()`), pero sí es necesario si en el futuro se quiere una traducción "clásica" por archivo `.po`/`.mo` de las 641 cadenas de código.

Dado el volumen (641 líneas), el detalle fila por fila (archivo, línea, función, fragmento de texto) se entrega como anexo aparte para no romper la legibilidad de este informe:

**`i18n_appendix_C_funciones_ya_internacionalizadas.csv`** (641 filas, columnas: `archivo`, `linea`, `funcion`, `fragmento`).

Por archivo, la distribución es (top 15):

| Archivo | Nº de llamadas i18n |
|---|---:|
| `inc/customizer.php` | 179 |
| `inc/quote-form.php` | 31 |
| `inc/meta-boxes.php` | 28 |
| `inc/enqueue.php` | 26 |
| `footer.php` | 25 |
| `template-parts/quote-form.php` | 21 |
| `inc/seo.php` | 19 |
| `inc/home-builder.php` | 18 |
| `template-parts/why-us.php` | 14 |
| `index.php` | 13 |
| `comments.php` | 13 |
| `template-parts/about.php` | 12 |
| `404.php` | 12 |
| `single-proyecto.php` | 11 |
| `inc/widgets.php` | 11 |

**Respuesta a la pregunta clave de la Sección 4 del encargo:** sí, Polylang puede aprovechar directamente estas 641 cadenas sin ningún cambio de código, porque:
- Todas usan `load_theme_textdomain()` con el mismo dominio.
- Polylang lee el idioma activo y WordPress resuelve `__()`/`_e()`/etc. contra el archivo `.mo` cargado para ese idioma — el mismo mecanismo con o sin Polylang.
- **No se necesita `pll_register_string()` para ninguna de estas 641 cadenas.** `pll_register_string()` es para cadenas que WordPress no puede recorrer con un escáner de `.pot` porque no están en el código fuente del tema (viven en la base de datos, como los `theme_mod`) — ver Sección D.

---

## D. Textos configurables desde el Customizer (`theme_mod`) — clasificación agregada

Agrupando las 71 claves únicas de la Sección B por categoría:

| Categoría | Nº de claves | Ejemplos | ¿Necesita `pll_register_string()`? |
|---|---:|---|---|
| **TEXTO** (contenido libre traducible) | 20 | `ce_hero_title`, `ce_hero_subtitle`, `ce_hero_btn1_text`, `ce_hero_btn2_text`, `ce_footer_about`, `ce_cta_title`/`ce_cta2_title`, `ce_cta_text`/`ce_cta2_text`, `ce_cta_btn_text`/`ce_cta2_btn_text`, `ce_financing_title`, `ce_financing_text`, `ce_financing_btn_text`, `ce_offer_popup_title`, `ce_offer_popup_text`, `ce_offer_popup_badge_text`, `ce_offer_popup_btn_text`, `ce_schedule` | **Sí** — son la razón de ser de Polylang en este proyecto. |
| **URL** | 12 | `ce_hero_btn1_url`, `ce_hero_btn2_url`, `ce_cta_btn_url`/`ce_cta2_btn_url`, `ce_financing_btn_url`, `ce_offer_popup_url`, `ce_testimonials_page_url`, `ce_maps_embed_url` | Depende — ver Sección E, no todas de la misma forma. |
| **CONTACTO** (dato de negocio, no editorial) | 4 | `ce_phone`, `ce_email`, `ce_address`\*, `ce_whatsapp_number` | Normalmente no, salvo que el negocio tenga datos distintos por país/idioma. |
| **MEDIA_ID** (ID de adjunto) | 4 | `ce_hero_image`, `ce_hero_video`, `ce_hero_slides`, `ce_footer_logo` | No es una cadena, pero **la imagen podría necesitar variar por idioma** (decisión editorial, no de i18n de texto). |
| **CONFIG** (booleano/numérico/modo) | 15 | `ce_hero_type`, `ce_hero_layout`, `ce_hero_show_quote_form`, `ce_quote_form_mode`, `ce_offer_popup_enabled`, `ce_offer_popup_delay`, `ce_offer_popup_dismiss_minutes`, `ce_offer_popup_convert_minutes`, `ce_offer_popup_action`, `ce_sidebar_servicios_slot`, `ce_sidebar_proyectos_slot`, `ce_hero_overlay_opacity`, `ce_hero_overlay_direction`, `ce_hero_overlay_extent`, `ce_hero_quote_card_opacity` | No. |
| **COLOR / COLOR-FUENTE** | 7 | `ce_color_primary`, `ce_color_secondary`, `ce_color_accent`, `ce_font_heading`, `ce_font_body`, `ce_cta_btn_color`, `ce_cta2_btn_color`, `ce_hero_overlay_color` | No. |
| **ICONO** (clase CSS) | 3 | `ce_offer_popup_icon`, `ce_cta_icon`, `ce_cta2_icon` | No. |
| **ORDEN/SLUG** (clave técnica) | 1 | `ce_home_sections_order` | No. |
| **JSON** (repetidor mixto) | 2 | `ce_stats_custom_items`, `ce_trust_badges_items` | Parcialmente — el `label` de cada fila sí; el resto de campos de cada fila, no. Ver Sección G. |
| **E — Caso especial (sin decidir)** | 3 | `ce_address`\*, `ce_footer_copyright`, `ce_google_reviews_embed` | Ver notas abajo. |

\* `ce_address` aparece tanto en "CONTACTO" como en "E" porque, aunque conceptualmente es un dato de contacto, contiene texto libre (p. ej. podría incluir palabras como "Calle", "Avenida", "Piso", "Oficina") que en rigor sí cambian de idioma a idioma incluso si la dirección física es la misma. No se decide aquí si debe registrarse — se deja documentado como caso E.

**Notas sobre los 3 casos E:**

1. **`ce_address`** — el valor es una dirección física. La calle/número no se traduce, pero palabras como "Piso", "Local", "Edificio" sí tienen equivalentes en otros idiomas. No hay una respuesta única sin que el usuario decida si el negocio opera en un solo país (dirección literal, no traducible) o en varios (necesitaría un valor por idioma).
2. **`ce_footer_copyright`** — el valor por defecto es una plantilla con `sprintf()`: `__( '&copy; %1$d %2$s. Todos los derechos reservados.', 'ce-construction' )`. Si el administrador nunca lo personaliza desde el Customizer, ya está correctamente internacionalizado como cadena de **código** (categoría B, no D). El caso especial es solo si el administrador **sí** lo personaliza desde el Customizer: en ese momento el valor guardado en la base de datos deja de pasar por `__()` y se convierte en una cadena de texto plano igual que cualquier otro `theme_mod` de tipo TEXTO — pero con la particularidad de que probablemente seguirá conteniendo los placeholders `%1$d`/`%2$s` de `sprintf()`, lo cual complica cualquier estrategia de `pll_register_string()` por idioma (un placeholder mal traducido rompe el `sprintf()`). Se documenta sin proponer todavía una solución.
3. **`ce_google_reviews_embed`** — contiene código de embed de terceros (script/HTML de un proveedor tipo Trustindex), no texto del tema. Cualquier texto que ese widget muestre lo renderiza el script externo en tiempo de ejecución y **no es controlable ni por el tema ni por Polylang**. Se documenta como límite conocido, no como algo a corregir en este proyecto.

---

## E. Inventario completo de URLs

### E.1 Mecanismos de construcción de URLs encontrados en el tema

| Mecanismo | Ocurrencias | ¿Filtrable por Polylang automáticamente? |
|---|---:|---|
| `esc_url()` | 69 | Escapa, no genera — depende de qué se le pase dentro. |
| `get_permalink()` | 25 | **Sí** — Polylang filtra `get_permalink()` de forma nativa para devolver el permalink en el idioma correcto. |
| `get_post_type_archive_link()` | 23 | **Sí**, si el CPT está configurado como traducible en Polylang. |
| `home_url()` | 19 | **Sí** — Polylang antepone el prefijo/dominio de idioma según la configuración elegida (subdirectorio, subdominio o parámetro). |
| `get_term_link()` | 2 | **Sí**, si la taxonomía está configurada como traducible. |
| `admin_url()` | 2 | No aplica — apunta siempre al panel de administración, no al front, no debe variar por idioma (`admin-ajax.php`, `admin-post.php`). |
| `add_query_arg()` | 2 | Depende de la URL base que reciba (ver E.3). |
| `wp_redirect()` / `wp_safe_redirect()` | 0 | No se usa en el tema. |
| `site_url()` / `get_page_link()` / `get_post_permalink()` / `remove_query_arg()` / `get_the_permalink()` | 0 | No se usa en el tema. |

**Conclusión parcial:** el tema **no concatena URLs internas a mano** en PHP — usa consistentemente las funciones de WordPress diseñadas para esto, que son exactamente las que Polylang engancha vía filtros (`post_link`, `page_link`, `post_type_link`, `term_link`, etc.). Esto es una base sólida: la mayoría de las URLs internas del tema **no necesitan ningún cambio de código** para funcionar correctamente con Polylang.

### E.2 URLs hardcodeadas absolutas encontradas (externas, no de contenido propio)

| Archivo | Línea | URL | Tipo | ¿Depende del idioma? |
|---|---:|---|---|---|
| `inc/enqueue.php` | 58 | `https://fonts.googleapis.com/css2?family=...` | Externa (Google Fonts) | No — el recurso es el mismo para todos los idiomas. |
| `inc/enqueue.php` | 66 | `https://cdnjs.cloudflare.com/ajax/libs/font-awesome/...` | Externa (CDN) | No. |
| `header.php` | 6 | `https://gmpg.org/xfn/11` | Externa (estándar `rel="profile"` de WordPress, no visible) | No. |
| `template-parts/financing.php` | 72 | `https://wa.me/<?php echo esc_attr( ce_get_whatsapp_number() ); ?>` | Externa (WhatsApp), con el número inyectado dinámicamente | No — mismo número en todos los idiomas salvo que el negocio use un número distinto por país. |
| `template-parts/cta.php` | 112 | `https://wa.me/<?php echo esc_attr( ce_get_whatsapp_number() ); ?>` | Externa (WhatsApp) | No. |

No se encontraron URLs internas absolutas hardcodeadas (del tipo `https://ceconstruction.com/...` apuntando a contenido propio) en el código del tema. `Theme URI`/`Author URI` en la cabecera de `style.css` son metadatos del tema, no URLs impresas al usuario.

### E.3 Las 7 URLs configurables señaladas en el encargo (y las que se les suman)

| `theme_mod` | Archivo(s) que la leen | ¿Interna o externa? | ¿Configurable desde Customizer? | ¿Debe cambiar por idioma? | ¿Se construye vía función de WP filtrable? | ¿Absoluta hardcodeada / concatenada a mano? | Riesgo | Acción propuesta (futura, NO ahora) |
|---|---|---|---|---|---|---|---|---|
| `ce_hero_btn1_url` | `template-parts/hero.php:154` | Depende — el admin puede pegar cualquier URL, interna o externa. Si queda vacío, no hay *fallback* automático (a diferencia de `btn2`). | Sí | **Si el destino es una página del propio sitio (p. ej. la página de Servicios), sí.** Si es externa (p. ej. una landing de campaña), no necesariamente. | No — es un campo de texto libre del Customizer (`esc_url_raw` al guardar, `esc_url()` al imprimir), no pasa por `get_permalink()`. | No concatenada, pero si el admin pega una URL absoluta de una página del sitio (en vez de dejar que WordPress la resuelva), esa URL quedará "fija" en el idioma en que se pegó. | **Riesgo medio** — depende enteramente de qué pegue el administrador, el tema no puede garantizar nada aquí. | Documentar para el administrador; evaluar en Fase 4 si conviene reemplazar el campo de URL libre por un selector de página de WordPress (`wp_dropdown_pages` o similar) para que Polylang pueda resolver el destino por idioma automáticamente. |
| `ce_hero_btn2_url` | `template-parts/hero.php:166` | Igual que arriba, **pero con fallback**: `post_type_exists( 'proyecto' ) ? get_post_type_archive_link( 'proyecto' ) : '#proyectos'`. | Sí | Sí, si apunta al archivo de Proyectos (caso por defecto). | **Sí, el fallback usa `get_post_type_archive_link()`** — ya es filtrable por Polylang. | Solo si el admin sobrescribe el campo con una URL absoluta propia. | **Riesgo bajo** en el valor por defecto; **riesgo medio** si el admin lo personaliza con una URL absoluta. | Igual que `btn1_url`. |
| `ce_cta_btn_url` / `ce_cta2_btn_url` | `template-parts/cta.php:67` (y su par para la variante secundaria) | Igual patrón: campo libre con fallback a `ce_get_quote_cta_url()` (que devuelve el ancla interna `#ce-quote-modal`, no una URL de WordPress). | Sí | El fallback (`#ce-quote-modal`) es un ancla interna a un modal que existe en todas las páginas — no depende del idioma. | El fallback no usa una función de permalink (es un ancla fija, no un post/page). | Solo si el admin lo personaliza. | **Riesgo bajo** en el valor por defecto. | Igual. |
| `ce_financing_btn_url` | `template-parts/financing.php:47` | Campo libre, sin fallback a función de WP (default `''`). | Sí | Depende de a qué apunte. | No. | Solo si el admin pega una URL absoluta. | **Riesgo medio**, mismo patrón que `hero_btn1_url`. | Igual. |
| `ce_offer_popup_url` | `inc/helpers.php:285` | Solo se lee si `ce_offer_popup_action === 'link'` (si es `'quote_form'`, no se usa esta URL, se abre el modal). Campo libre. | Sí | Depende. | No. | Solo si el admin pega una URL absoluta. | **Riesgo medio**, mismo patrón. | Igual. |
| `ce_testimonials_page_url` | `template-parts/testimonials.php:32` | Campo libre, sin fallback. | Sí | Muy probablemente sí — su propio nombre indica que apunta a "la página de testimonios", que en un sitio multilingüe con Polylang **tendría una versión por idioma** con URLs distintas. | No. | Solo si el admin pega una URL absoluta. | **Riesgo alto** — este es, de los 7, el caso donde es más probable que el valor real en producción sea justamente la URL de una página interna del sitio, y por tanto el que más se rompería si no se resuelve por idioma. | **Prioridad alta para Fase 4**: evaluar reemplazar por un selector de página (`wp_dropdown_pages`) en vez de un campo de texto libre, precisamente porque Polylang sí sabe resolver "la página X en el idioma actual" pero no sabe qué hacer con una URL absoluta pegada a mano. |
| `ce_maps_embed_url` | `footer.php:113`, `inc/helpers.php` | Externa (URL de embed de Google Maps). | Sí | No — el mapa embebido apunta a una ubicación física, no a contenido traducible; Google Maps ya gestiona su propio idioma de interfaz según el `hl=` de la URL o la configuración del navegador, no según Polylang. | No aplica. | No aplica (es correcto que sea absoluta, es un embed externo). | **Sin riesgo.** | Ninguna. |
| `ce_social_facebook` / `instagram` / `linkedin` / `youtube` / `tiktok` | `inc/helpers.php:31` | Externas (perfiles sociales). | Sí | No — el mismo perfil social sirve para todos los idiomas del sitio (salvo que el negocio tenga cuentas separadas por país, caso de negocio, no de i18n). | No aplica. | No aplica. | **Sin riesgo.** | Ninguna. |

### E.4 URLs construidas en JavaScript

| Archivo | Línea | Construcción | ¿Depende de texto traducible? | Riesgo |
|---|---:|---|---|---|
| `assets/js/main.js` | 314 | `` `https://wa.me/${CE.whatsapp.replace(/\D/g, '')}?text=${message}` `` | El parámetro `text` es el mensaje de WhatsApp (`data-message`, ya traducible vía `esc_attr_e()` en `footer.php`), pasado por `encodeURIComponent()`. El número (`CE.whatsapp`) es un dato técnico, no texto. | **Sin riesgo** — `encodeURIComponent()` codifica el texto sin importar el idioma/alfabeto, y el número nunca se mezcla con el texto sin escapar. |
| `assets/js/main.js` | 160 y 1415 | `link.getAttribute('href')` | Lee el **valor** del atributo `href` (un ancla `#id` o una URL), no el texto visible del enlace. | **Sin riesgo** — ver Sección I. |

No se encontraron construcciones de URL en JavaScript vía `fetch()`/AJAX que incorporen texto visible sin codificar, ni usos de `window.location`/`location.href`/`location.pathname`/`URLSearchParams` en ningún archivo JS del tema.

---

## F. Auditoría JavaScript

Se revisaron los 6 archivos de `assets/js/`: `main.js` (frontend, 1471 líneas), `admin-hero-slides.js`, `admin-home-builder.js`, `admin-proyecto-gallery.js`, `admin-stats-items.js`, `admin-trust-badges.js` (backend/Customizer, 81–209 líneas cada uno).

### F.1 Mecanismo de localización de JS ya existente

`inc/enqueue.php` ya usa `wp_localize_script()` para pasar datos de PHP a JS en 5 puntos: `ceConstructionData` (frontend), `ceHeroSlidesData`, `ceStatsItemsData`, `ceTrustBadgesData`, `ceProyectoGalleryData` (los 4 últimos, admin/Customizer). Dentro de `ceConstructionData` existe una sub-clave `i18n` con **6 cadenas ya correctamente traducibles** porque se generan con `__()` en PHP antes de pasarlas a JS:

| Clave JS | Cadena (PHP) | Uso en `main.js` |
|---|---|---|
| `i18n.sending` | `__( 'Enviando...', 'ce-construction' )` | Estado del botón de envío del formulario de cotización. |
| `i18n.error` | `__( 'Ocurrió un error. Intenta nuevamente.', 'ce-construction' )` | Mensaje de error genérico de AJAX. |
| `i18n.pauseSlider` | `__( 'Pausar', 'ce-construction' )` | Botón de pausa del slider de testimonios. |
| `i18n.resumeSlider` | `__( 'Reanudar', 'ce-construction' )` | Botón de reanudar del slider. |
| `i18n.openMobileNav` | `__( 'Abrir menú', 'ce-construction' )` | `aria-label` dinámico del botón hamburguesa. |
| `i18n.closeMobileNav` | `__( 'Cerrar menú', 'ce-construction' )` | `aria-label` dinámico al cerrar el menú móvil. |

Este patrón es exactamente el correcto para que Polylang (a través del mecanismo estándar de WordPress) también traduzca los textos que vive del lado del cliente. **El problema no es la falta de mecanismo — el mecanismo existe y funciona —, es que no se aplicó a todas las cadenas visibles.**

### F.2 Textos hardcodeados en JS SIN pasar por el mecanismo anterior (Categoría C)

| Archivo | Línea | Texto | Contexto | Riesgo (para i18n) | Acción propuesta |
|---|---:|---|---|---|---|
| `assets/js/main.js` | 899 | `'Ingresa un nombre válido.'` | Mensaje de validación en vivo del campo "nombre" del formulario de cotización | El mensaje NUNCA se traduce aunque el sitio esté en otro idioma — queda en español fijo. | Mover a `ceConstructionData.i18n` (Fase 1). |
| `assets/js/main.js` | 900 | `'Ingresa un correo válido.'` | Validación del campo "email" | Igual | Igual |
| `assets/js/main.js` | 901 | `'Ingresa un teléfono válido.'` | Validación del campo "teléfono" | Igual | Igual |
| `assets/js/main.js` | 902 | `'Selecciona el servicio requerido.'` | Validación del campo "servicio" | Igual | Igual |
| `assets/js/main.js` | 903 | `'Cuéntanos un poco más (mínimo 10 caracteres).'` | Validación del campo "mensaje" | Igual | Igual |
| `assets/js/main.js` | 994 | `'Revisa los campos marcados en rojo.'` | Mensaje de estado cuando falla la validación general del formulario | Igual | Igual |
| `assets/js/main.js` | 310 | `'Hola, quisiera más información sobre sus servicios de remodelacion.'` | *Fallback* del mensaje de WhatsApp, solo se usa si `data-message` viniera vacío en el HTML (en la práctica, `footer.php` siempre lo imprime vía `esc_attr_e()`, así que este fallback es defensivo, no la ruta normal) | Riesgo bajo en la práctica (ruta rara), pero sigue siendo un texto en español fijo si alguna vez se activa. | Igual, por consistencia. |
| `assets/js/main.js` | 585 y 595 | `dotLabel: 'Testimonio'` y `pauseLabel: 'Testimonios:'` | Etiquetas de accesibilidad (`aria-label`) generadas dinámicamente para los puntos/controles del slider de testimonios | Igual — quedan fijas en español. | Igual |

**Todos los hallazgos de esta tabla son Categoría C** (texto visible al usuario, hardcodeado, sin ningún mecanismo de traducción) trasladada al contexto de JavaScript. No se corrige nada ahora — se deja documentado para la Fase 1.

### F.3 Archivos de admin (`admin-*.js`)

Los 5 archivos de administración **no contienen texto de usuario hardcodeado** — todo el texto visible que manipulan (botones "Mover antes/después/Quitar", etc.) se recibe ya traducido vía sus respectivos `wp_localize_script()`, siguiendo el mismo patrón correcto que las 6 claves de `i18n` de `ceConstructionData`. No requieren acción.

### F.4 Otros patrones de riesgo buscados y su resultado

| Patrón buscado | Resultado |
|---|---|
| `innerHTML` | 4 usos (líneas 687, 763, 781 y el condicional 516 con `innerHTML`); todos insertan HTML propio de la plantilla (markup del modal, copia de un embed) o el resultado de `i18n`/`pauseLabel`/`resumeLabel`, nunca construyen selectores ni lógica a partir de ese HTML. Sin riesgo funcional. |
| `textContent` / `innerText` como identificador de lógica | No se encontró ningún caso donde el `textContent` de un elemento se use como clave de comparación, selector o para tomar una decisión de negocio — los usos son puramente de presentación (mostrar el nombre del archivo elegido, restaurar una etiqueta). |
| Selectores CSS/JS dependientes de texto visible (`:contains`, comparación de `.text()`) | Ninguno. |
| `window.location` / `location.href` / `location.pathname` / `URLSearchParams` | Ninguno en ningún archivo JS del tema. |
| IDs/clases/slugs generados a partir de texto visible | Ninguno — `sanitize_title()`/`remove_accents()` no se usan en el tema (WordPress ya gestiona los slugs de posts en el core, al margen del idioma de visualización). |
| `.replace()` sobre URLs o HTML con texto traducible | El único `.replace()` relevante es `CE.whatsapp.replace(/\\D/g, '')`, que limpia el número de teléfono de caracteres no numéricos — no toca texto traducible. |

---

## G. JSON / estructuras dinámicas

### G.1 `ce_stats_custom_items`

Estructura por fila (definida en `ce_construction_default_stats_items()`, `inc/helpers.php:1166`):

```php
array(
    'count'  => 350,                                   // Categoría D — número, no traducible
    'suffix' => '+',                                   // Categoría D — símbolo técnico, no traducible
    'label'  => __( 'Proyectos realizados', ... ),      // Categoría A/B — texto traducible
    'icon'   => 'fa-solid fa-building',                 // Categoría D — clase CSS, no traducible
)
```

- Los valores **por defecto** (4 filas predefinidas) usan `__()` correctamente — son Categoría B mientras el administrador no las toque.
- **En cuanto el administrador edita o añade una fila desde el repetidor del Customizer**, el array completo se serializa a JSON y se guarda tal cual en `ce_stats_custom_items` (`ce_construction_sanitize_stats_items()` sanea con `sanitize_text_field()`, no con `__()` — no podría, es contenido dinámico, no código). A partir de ahí, cada `label` guardado es una cadena de texto plano sin ningún mecanismo de traducción — necesitaría una estrategia de registro **por fila**, no un único registro para todo el JSON.
- **No se debe registrar el JSON completo como una sola cadena traducible** (el encargo ya lo señala explícitamente) — habría que decodificar el array y registrar cada `label` de forma individual, algo que técnicamente es posible con `pll_register_string()` pero que requiere diseño cuidadoso (claves estables por fila, no por índice, para no romper la traducción si el admin reordena filas). Se documenta como pendiente de diseño en Fase 5, sin proponer la solución todavía.

### G.2 `ce_trust_badges_items`

Estructura por fila (`ce_construction_decode_trust_badges()`, `inc/helpers.php:1329`):

```php
array(
    'image_id' => 123,        // Categoría D/MEDIA_ID — no traducible
    'label'    => '...',      // Categoría A — obligatorio, traducible, además se usa como alt de la imagen si existe
    'license'  => '...',      // Categoría E — número de licencia; probablemente no traducible, pero no es evidente si en algún caso incluye texto (p. ej. "Lic. N.º 12345")
    'url'      => '...',      // Categoría URL — enlace de verificación externo (organismo licenciador); no debería depender del idioma
)
```

Sin valores por defecto (a diferencia de `ce_stats_custom_items`, este `theme_mod` nace vacío). Mismo razonamiento que en G.1: el campo `label` de cada insignia es candidato a registro individual por fila; `license` se documenta como caso E (no se decide); `url` normalmente no varía por idioma (apunta a un organismo externo de licencias, no a contenido del propio sitio).

### G.3 Otras estructuras técnicas sin texto (para descartar falsos positivos)

| `theme_mod` | Estructura | ¿Contiene texto visible? |
|---|---|---|
| `ce_hero_slides` | Cadena de IDs de adjunto separados por coma (`ce_get_hero_slide_ids()`, `inc/helpers.php:814`) | No — solo IDs numéricos. |
| `ce_home_sections_order` | Array de `{key, enabled}` por sección del Home Builder | No — `key` es un slug técnico interno (p. ej. `'hero'`, `'services'`), no texto de usuario. |

No se encontró ningún otro JSON generado por el tema fuera de estas 4 estructuras (se revisaron también `inc/quote-attachments.php`, `inc/quote-form.php` y las respuestas AJAX del formulario de cotización — devuelven `wp_send_json_success()`/`wp_send_json_error()` con mensajes que ya pasan por `__()` en PHP antes de serializarse, así que están cubiertos por la Sección C, no son un JSON nuevo que auditar aquí).

---

## H. Compatibilidad con Polylang — resumen por tipo de dato

| Tipo de dato | Cantidad | Mecanismo recomendado (a decidir/aprobar en su momento, NO ahora) | ¿Requiere cambio de código? |
|---|---:|---|---|
| 1. Cadenas de código ya internacionalizadas (`__()`, `esc_html_e()`, etc.) | 641 llamadas | Ninguno adicional — Polylang usa el mismo sistema de `.mo` de WordPress. | No. |
| 2. Cadenas de código que deberían corregirse (hardcodeadas sin mecanismo) | 8 (todas en `assets/js/main.js`) | Extender `wp_localize_script()` / el array `i18n` ya existente en `inc/enqueue.php`. | Sí — Fase 1. |
| 3. `theme_mod` de tipo TEXTO | 20 claves | `pll_register_string()` por clave (o el registro que Polylang ofrezca para Customizer). | Sí — Fase 3. |
| 4. URLs que necesitan estrategia por idioma | Hasta 6 de las 12 URL (`ce_hero_btn1_url`, `ce_hero_btn2_url`, `ce_cta_btn_url`, `ce_cta2_btn_url`, `ce_financing_btn_url`, `ce_offer_popup_url`), con **prioridad alta en `ce_testimonials_page_url`** | Evaluar selector de página en vez de campo de texto libre. Las otras 6 URL (mapas, redes sociales, WhatsApp) no necesitan nada. | Sí, si se decide — Fase 4. |
| 5. Datos de contacto | 4 claves (`ce_phone`, `ce_email`, `ce_address`, `ce_whatsapp_number`) | Ninguno por defecto; revisar solo si el negocio opera en varios países con datos distintos. | No, salvo decisión explícita del usuario. |
| 6. JSON dinámico | 2 claves (`ce_stats_custom_items`, `ce_trust_badges_items`) | Registro individual del campo `label` de cada fila (diseño pendiente). | Sí — Fase 5. |
| 7. Contenido de CPT/ACF/repeaters | No hay ACF instalado en este tema. Los 6 CPT propios (`servicio`, `proyecto`, `testimonios`, `equipo`, `clientes`, y la taxonomía/CPT de FAQ) ya son contenido nativo de WordPress (`post_title`, `post_content`, metaboxes propios) | Polylang traduce CPT de forma nativa una vez que cada tipo se marca como "traducible" en su configuración — no requiere cambios en el tema salvo registrar los custom fields de las metaboxes propias (`inc/meta-boxes.php`) si contienen texto traducible. **Esto no se auditó en detalle en este Entregable** porque el encargo original se centró en `get_theme_mod()` y en el tema en general, no en el contenido de los CPT — se señala como posible alcance adicional a confirmar contigo antes de la Fase 6. | A confirmar. |

---

## I. Riesgos de traducción automática del navegador — conclusión técnica

**Conclusión general: no se encontró en el código del tema ninguna evidencia de que la traducción automática del navegador (p. ej. Google Translate) pueda romper una URL, un enlace o una acción del sitio.**

Esta conclusión se basa en el examen específico de los patrones que sí podrían causar ese problema, con resultado en cada uno:

| Patrón investigado | ¿Se encontró en el código? | Riesgo |
|---|---|---|
| URLs construidas concatenando texto visible | No | Sin riesgo |
| IDs generados a partir de texto visible | No (`sanitize_title()`/slugs los gestiona WordPress en el guardado del post, no en el render) | Sin riesgo |
| Clases CSS generadas a partir de texto | No | Sin riesgo |
| Nombres de campos generados dinámicamente a partir de texto | No | Sin riesgo |
| Valores `data-*` derivados de texto visible | El único `data-*` relevante es `data-message` (footer.php:156), pero es al revés: contiene el texto ya traducible (`esc_attr_e()`) y JS solo lo **lee** para construir un parámetro de URL correctamente codificado (`encodeURIComponent()`) — no deriva nada del texto, lo usa como valor. | Sin riesgo |
| JS que busca elementos por texto visible | No — todas las búsquedas de `main.js` son por clase CSS o `id` | Sin riesgo |
| JS que usa `innerHTML` | Sí existen usos (4), pero insertan markup propio del tema o texto ya i18n, nunca a la inversa | Sin riesgo |
| JS que usa `textContent` como identificador de lógica | No — los usos son puramente de presentación | Sin riesgo |
| JS que construye URLs a partir del contenido de un elemento | No | Sin riesgo |
| Formularios que dependen del texto del botón | No — el envío del formulario de cotización se dispara por el evento `submit` del `<form>`, no por comparar el texto de ningún botón | Sin riesgo |
| Selectores CSS/JS que dependan de etiquetas visibles | No | Sin riesgo |
| `href`/`action` generados mediante concatenación de texto | No — los 2 usos de `.getAttribute('href')` en `main.js` leen el valor del atributo (un ancla `#id` o una URL), nunca el texto visible del enlace | Sin riesgo |
| JSON que mezcle texto visible y valores técnicos de forma ambigua | Los 2 JSON detectados (`ce_stats_custom_items`, `ce_trust_badges_items`) sí mezclan texto y datos técnicos **dentro de la base de datos**, pero eso no tiene relación con la traducción del navegador — es relevante para Polylang (Sección G), no para este punto | No aplica a este riesgo específico |
| `window.location` / `location.href` / `location.pathname` / `URLSearchParams` | No se usa en ningún archivo JS del tema | Sin riesgo |

**Diferenciación explícita pedida en el encargo:**

1. **Traducción del navegador** (Google Translate y similares): reescribe *nodos de texto* visibles en el DOM ya renderizado, en el navegador del visitante, después de que la página cargó. No modifica atributos HTML (`href`, `src`, `data-*`, `action`, `alt`, `value`) ni el DOM subyacente que usa JavaScript para su lógica — solo el texto que se muestra en pantalla. Es exactamente por esto que no se encontró riesgo: el tema nunca lee "lo que el usuario ve en pantalla" para tomar una decisión, siempre lee valores de atributos o variables de datos.
2. **Traducción de Polylang**: ocurre en el servidor, antes de enviar el HTML al navegador — sirve un HTML ya completo en el idioma correspondiente. No tiene el mismo tipo de riesgo que la traducción del navegador porque no reescribe nada después de renderizado; en todo caso, los riesgos de Polylang son los ya cubiertos en las Secciones D, E, G y H (qué cadenas/URLs necesitan registrarse por idioma).
3. **Lógica propia del tema**: como se detalla arriba, no depende del texto visible para ninguna decisión funcional.
4. **Comportamiento de JavaScript**: revisado exhaustivamente en la Sección F — el único hallazgo real (strings hardcodeados sin traducir) es un problema de *cobertura de i18n*, no de *fragilidad ante traducción del navegador*. Son dos problemas distintos y no deben mezclarse: un texto hardcodeado en español simplemente no se traduce (por Polylang ni por el navegador tampoco, si el navegador respeta el atributo `lang` — ver más abajo); eso no es lo mismo que "algo se rompe".
5. **Comportamiento de plugins externos**: Polylang no está instalado todavía, así que no hay nada que auditar en este punto. El único "plugin" externo activo hoy es el embed de reseñas de Google (`ce_google_reviews_embed`, Trustindex u otro proveedor similar) — su comportamiento frente a la traducción del navegador **no es auditable** desde este código porque el script vive fuera del tema.

**Sobre el recuerdo del usuario ("algunas acciones/enlaces dejaron de funcionar" con la traducción automática activada):** no se afirma que ese recuerdo sea incorrecto, pero **no se encontró en el código actual del tema ningún mecanismo que explique ese comportamiento**. Posibles explicaciones alternativas que quedan fuera del alcance de este audit de código (no se puede confirmar ni descartar ninguna sin más contexto):
- Pudo tratarse de una versión anterior del tema, ya corregida en refactorizaciones posteriores (varias notas de `DECISIONS.md`/`QA_REPORT.md` sugieren iteraciones sobre el JS de navegación y modales).
- Pudo ser un efecto conocido y genérico de Google Translate al reescribir el DOM alrededor de elementos con manejadores de eventos delegados agresivamente (un problema documentado de Google Translate en general, no específico de este tema, que a veces duplica o envuelve nodos de forma que interfiere con `event.target` en delegación de eventos — pero esto no se puede confirmar mirando el código fuente, solo observando el comportamiento real en un navegador).
- Pudo deberse a una extensión o configuración del navegador ajena tanto al tema como a WordPress.

No se afirma ninguna de estas causas como cierta — se listan solo para diferenciarlas explícitamente de "el tema tiene un bug de i18n", que es lo que sí se puede confirmar o descartar desde el código, y en este caso se descarta.

---

## J. Plan de implementación propuesto (NO ejecutar todavía)

### Fase 1 — Correcciones de internacionalización del código
Extender `ceConstructionData.i18n` en `inc/enqueue.php` para cubrir los 8 hallazgos de la Sección F.2 (5 mensajes de validación, 1 mensaje de estado, `dotLabel`, `pauseLabel`; el fallback de WhatsApp es opcional/de bajo impacto). Sin tocar ninguna otra cadena — las 641 ya están correctas.

### Fase 2 — Integración con Polylang
Instalar y activar Polylang. Configurar idiomas del sitio. Confirmar que las 641 cadenas de código (Sección C) se traducen correctamente vía el flujo estándar de `.pot`/`.po`/`.mo` (puede requerir generar el `.pot` del tema, ya que hoy no existe carpeta `/languages`).

### Fase 3 — Traducción de `theme_mod`
Registrar con `pll_register_string()` las 20 claves de categoría TEXTO (Sección D), más decidir explícitamente sobre los 3 casos E (`ce_address`, `ce_footer_copyright`, `ce_google_reviews_embed`) antes de incluirlos o excluirlos.

### Fase 4 — Resolución de URLs internas por idioma
Evaluar (no implementar sin aprobación) sustituir el campo de texto libre por un selector de página de WordPress en `ce_testimonials_page_url` (prioridad alta) y, si el usuario lo decide, en `ce_hero_btn1_url`, `ce_hero_btn2_url`, `ce_cta_btn_url`/`ce_cta2_btn_url` y `ce_financing_btn_url`.

### Fase 5 — JSON/repetidores
Diseñar (no implementar) el mecanismo de registro individual por fila para el campo `label` de `ce_stats_custom_items` y `ce_trust_badges_items`, con claves estables que sobrevivan a reordenamientos.

### Fase 6 — Pruebas de idioma
Verificar en un entorno real, con Polylang activo y al menos 2 idiomas configurados, que: (a) las 641 cadenas de código cambian de idioma correctamente, (b) los `theme_mod` registrados en Fase 3 muestran el valor correcto por idioma, (c) las URLs resueltas en Fase 4 apuntan a la página correcta en cada idioma, (d) los CPT (si se confirma que están en alcance — ver Sección H, punto 7) se traducen correctamente.

### Fase 7 — Pruebas con traducción automática del navegador activada
Repetir las pruebas de la Fase 6 con la traducción automática del navegador activada simultáneamente, prestando atención específica a los 7 puntos de CTA del tema (los mismos que ya se mencionan en `inc/helpers.php` como "los 7 puntos de CTA del tema" — `ce_get_quote_cta_url()`) y al flujo completo de envío del formulario de cotización, ya que es el flujo funcional más crítico del sitio.

---

## Regla crítica — recordatorio de cumplimiento

Durante esta auditoría **no se modificó ningún archivo de código del tema**, no se instaló Polylang, no se ejecutó `pll_register_string()` y no se "arregló" ninguno de los hallazgos anteriores. El único archivo generado es este documento de diagnóstico y su anexo CSV.

**Este Entregable 9.1 (ampliado) queda pendiente de tu aprobación explícita antes de iniciar la Fase 1.**
