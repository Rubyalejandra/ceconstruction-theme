# CE Construction — I18N_DECISIONES_9.2.md
### Sprint 9, Entregable 9.2 — Decisiones y arquitectura de i18n

**Estado: documento de decisiones únicamente. No se modificó ningún archivo de código, no se instaló Polylang, no se ejecutó `pll_register_string()`, no se tocó ningún `theme_mod` ni contenido de la base de datos.**

> Este documento cierra los ~10 puntos marcados como "B" (requiere decisión mía) o "Requiere confirmación del cliente" en `docs/I18N_AUDIT_SPRINT9_9.1_REVISION_FINAL_y_PLAN_SPRINT9.md`. Cada punto tiene aquí una respuesta explícita, con su justificación, conforme al criterio de aceptación fijado en ese mismo documento. Ninguna decisión aquí se ejecuta todavía — habilitan el alcance de los Entregables 9.3 en adelante.

---

## 1. Strings hardcodeados en `assets/js/main.js` (12 casos)

**Decisión: Aprobado.** Se autoriza tocar `assets/js/main.js` e `inc/enqueue.php` en el Entregable 9.3 para mover las 12 cadenas (5 mensajes de validación del formulario de cotización, 1 mensaje de estado general, `dotLabel`/`pauseLabel` del slider de testimonios, 3 `aria-label` del lightbox, y el *fallback* de WhatsApp) a `ceConstructionData.i18n`, siguiendo exactamente el mismo patrón ya usado por las 6 claves existentes (`sending`, `error`, `pauseSlider`, `resumeSlider`, `openMobileNav`, `closeMobileNav`).

**Impacto:** el Entregable 9.3 procede sin ninguna reducción de alcance en este punto.

---

## 2. `theme_mod` de tipo URL — selector de página vs. texto libre

**Decisión: Se cambia únicamente `ce_testimonials_page_url` a un selector de página nativo de WordPress (`wp_dropdown_pages()` o control equivalente del Customizer). Las otras 4 URLs de riesgo real (`ce_hero_btn1_url`, `ce_hero_btn2_url`, `ce_financing_btn_url`, `ce_offer_popup_url`) permanecen como campo de texto libre, sin cambios.**

**Justificación:** de las 5 URLs señaladas con riesgo real, `ce_testimonials_page_url` es la única con evidencia directa de que casi con certeza apunta a una página interna del sitio (su propio nombre lo indica, y es precisamente el destino que UX-10.1/UX-10.2 introdujeron para la página de Testimonios). Las otras 4 tienen riesgo medio/bajo y, en el caso de `ce_hero_btn2_url`, ya cuentan con un *fallback* filtrable por Polylang (`get_post_type_archive_link()`). Cambiar las 5 a la vez multiplicaría el número de archivos tocados (5 templates + `inc/customizer.php`) para un beneficio marginal en 4 de los 5 casos, aumentando la superficie de regresión sin necesidad. Esta decisión prioriza el caso de mayor probabilidad de romperse en producción con el menor cambio de código posible.

**Impacto:** el alcance de la **Fase 4 / Entregable 9.7** queda reducido a 1 sola clave (`ce_testimonials_page_url`), no hasta 5 como contemplaba la auditoría. Las otras 4 quedan documentadas como **backlog no aprobado**, re-evaluable en un Entregable futuro si el uso real en producción lo justifica.

---

## 3. CPT `testimonio`, `miembro_equipo`, `cliente` — ¿traducibles?

**Decisión: Contenido único — ninguno de los 3 se marca como CPT traducible en Polylang.**

**Justificación:** los 3 almacenan nombres propios de personas o empresas reales (el nombre de un cliente, un miembro del equipo, o quien da un testimonio). Marcarlos como traducibles obligaría al administrador a crear y mantener manualmente una "traducción" por idioma de cada registro, con riesgo real de contenido faltante, desincronización de fechas de publicación entre versiones, o simplemente trabajo duplicado sin beneficio real (un nombre propio no cambia de idioma). Es la opción que no puede introducir ninguna regresión y es completamente reversible más adelante — si en el futuro se decide que uno de los 3 sí necesita traducción real (p. ej. si el negocio empieza a operar en mercados donde presenta testimonios distintos por idioma), es una decisión de configuración de Polylang, no de código.

**Impacto:** en el **Entregable 9.6**, `servicio`, `proyecto` y `ce_faq` (y sus taxonomías) se marcan como traducibles; `testimonio`, `miembro_equipo` y `cliente` se marcan explícitamente como **no traducibles**. El CPT `cotizacion` sigue excluido por completo (dato transaccional, ya resuelto sin ambigüedad desde 9.1).

---

## 4. Campo `license` (repetidor de Insignias de Confianza)

**Decisión: Traducible.** El usuario confirmó que el campo puede incluir texto junto al número de licencia (ej. "Licencia N.º 4582-B"), no solo el número aislado.

**Justificación:** dado que el campo puede mezclar una palabra traducible con un dato técnico dentro del mismo string, se trata como texto candidato a `pll_register_string()` por fila — el identificador estable (ver punto 6) es quien hace esto viable sin romperse al reordenar insignias.

**Impacto:** en el **Entregable 9.8**, `label` y `license` de `ce_trust_badges_items` se registran ambos por fila. `image_id` y `url` siguen sin registro (no son texto).

---

## 5. Campo `suffix` (repetidor de Estadísticas) — y hallazgo nuevo de traducción automática del navegador

**Decisión: Traducible.** El usuario confirmó que el campo puede incluir una palabra (ej. "años") y no solo un símbolo fijo ("+", "%").

**Hallazgo adicional, fuera del alcance original de la auditoría 9.1, reportado por el usuario en esta sesión:** con la traducción automática del navegador activada, el símbolo `+` (valor por defecto de `suffix` en las 4 estadísticas actuales) se está reescribiendo como la palabra "más". **Esto no es un problema de Polylang** — ocurre en el cliente, sobre el HTML ya renderizado por el navegador (o una extensión de traducción), completamente al margen de qué idioma sirva el servidor; es la misma categoría de mecanismo que la Sección I de la auditoría 9.1 ya analizó (traducción automática del navegador) y para la que no encontró ningún riesgo *funcional* — pero aquí sí hay un efecto real sobre la *fidelidad visual* del contenido, aunque no rompe ninguna acción ni enlace.

**Decisión de mitigación (aprobada por criterio de menor riesgo, 100% aditiva):** cuando el valor de `suffix` de una fila no contenga ninguna letra (es decir, sea un símbolo puro como "+" o "%"), se envuelve en un `<span translate="no">` al renderizarlo — el atributo estándar de HTML que instruye a los traductores automáticos (Google Translate y equivalentes) a no tocar ese fragmento. Cuando el valor sí contenga letras (ej. "años"), se imprime sin ese atributo, permitiendo que se traduzca con normalidad (por Polylang si se registra, o por el traductor del navegador como respaldo). Esta protección es puramente de presentación, no cambia el dato guardado ni afecta al registro de `pll_register_string()` del punto 4/6.

**Impacto:** en el **Entregable 9.8**, `label` y `suffix` de `ce_stats_custom_items` se registran ambos por fila (mismo criterio que Insignias). Además, `template-parts/stats.php` recibe el ajuste aditivo de `translate="no"` condicional descrito arriba — cambio de bajo riesgo, sin afectar ningún otro archivo.

---

## 6. Identificador estable por fila (no por índice) para los repetidores JSON

**Decisión: Aprobado.**

**Justificación:** ya explicada en la auditoría 9.1 — registrar `pll_register_string()` por índice de fila haría que reordenar, agregar o eliminar una fila desplazara las traducciones ya hechas hacia la fila equivocada. Un identificador único generado una sola vez por fila (ej. `wp_generate_uuid4()` o un hash corto), persistente mientras esa fila exista, resuelve el problema sin depender de la posición.

**Impacto:** en el **Entregable 9.8**, `ce_construction_sanitize_stats_items()` y `ce_construction_sanitize_trust_badges_items()` (`inc/customizer.php`) se extienden para generar y preservar ese identificador en cada fila nueva, usado como parte de la clave de `pll_register_string()` (ej. `stats_item_{uuid}_label`).

---

## 7. Datos de contacto (`ce_phone`, `ce_email`, `ce_address`, `ce_whatsapp_number`)

**Decisión: Mismo valor en todos los idiomas.** El usuario confirmó que el negocio opera con los mismos datos de contacto sin importar el idioma del visitante.

**Justificación:** sin variación real por mercado, no hay ningún beneficio en registrar estas 4 claves con `pll_register_string()` — quedan exactamente como están hoy (`theme_mod` simples, sin registro por idioma).

**Impacto:** ninguna acción en el Entregable 9.5. `ce_schedule` (horario) es distinto de este grupo — ya estaba clasificado sin ambigüedad desde la auditoría 9.1 como candidato técnico claro a registro (es texto descriptivo, con nombres de días que sí cambian de idioma), y esa clasificación no cambia con esta decisión.

---

## 8. `ce_footer_copyright` — personalización futura y placeholders de `sprintf()`

**Decisión: Sí es un caso real a futuro** — el usuario confirmó que prevé personalizar este campo desde el Customizer en algún momento.

**Estrategia aprobada para cuando se implemente (Entregable 9.5):** en vez de registrar con `pll_register_string()` el resultado final ya interpolado (año + nombre del sitio ya insertados en el texto), se registra la **plantilla con los placeholders `%1$d %2$s` intactos** — igual que ya funciona el valor por defecto del tema (`__( '&copy; %1$d %2$s. Todos los derechos reservados.', 'ce-construction' )`). El año y el nombre del sitio se siguen interpolando en PHP *después* de obtener la cadena ya traducida del panel de Polylang, nunca antes. Esto evita el riesgo ya documentado en la auditoría: que un traductor borre, duplique o invierta el orden de los placeholders sin saber que son marcadores de posición de `sprintf()`.

**Mitigación adicional aprobada:** el campo de traducción de esta cadena en el panel de Polylang debe incluir una nota visible explicando qué son `%1$d`/`%2$s` y advirtiendo no eliminarlos, para reducir el riesgo de que un traductor humano los rompa por desconocimiento.

**Impacto:** en el **Entregable 9.5**, si el administrador personaliza `ce_footer_copyright`, el valor se registra con esta estrategia de placeholder protegido, no como texto plano final.

---

## Resultado — verificación de cierre

| Punto | Estado |
|---|---|
| 1. Strings JS (12) | ✅ Decidido — Aprobado |
| 2. URLs de riesgo (5) | ✅ Decidido — Solo `ce_testimonials_page_url` |
| 3. CPT testimonio/equipo/cliente | ✅ Decidido — Contenido único |
| 4. `license` (Insignias) | ✅ Decidido — Traducible |
| 5. `suffix` (Estadísticas) + hallazgo de traductor de navegador | ✅ Decidido — Traducible + mitigación `translate="no"` aprobada |
| 6. Identificador estable por fila | ✅ Decidido — Aprobado |
| 7. Datos de contacto (4 claves) | ✅ Decidido — Mismo valor, sin registro |
| 8. `ce_footer_copyright` | ✅ Decidido — Caso real, estrategia de placeholder protegido aprobada |

**No queda ningún punto "B" ni caso "E" sin una decisión explícita registrada.** Conforme al criterio de aceptación de este Entregable, queda cumplido en su totalidad.

---

## Alcance actualizado de los Entregables siguientes (resultado de estas decisiones)

- **9.3** — sin cambios de alcance: `.pot`, carpeta `/languages`, las 12 claves de JS en `ceConstructionData.i18n`.
- **9.4** — sin cambios: instalación y configuración base de Polylang.
- **9.5** — registra las 20 claves TEXTO ya identificadas + `ce_footer_copyright` (con la estrategia de placeholder protegido del punto 8, solo si el admin ya lo ha personalizado o lo personaliza durante este Entregable). Sin registro para los 4 datos de contacto (punto 7).
- **9.6** — marca `servicio`/`proyecto`/`ce_faq` (+ taxonomías) como traducibles; **`testimonio`/`miembro_equipo`/`cliente` quedan explícitamente sin marcar como traducibles** (punto 3); `cotizacion` excluido (sin cambios).
- **9.7** — alcance reducido: solo `ce_testimonials_page_url` pasa a selector de página (punto 2). Las otras 4 URLs quedan en backlog no aprobado.
- **9.8** — identificador estable por fila (punto 6); registro de `label`+`suffix` (Estadísticas) y `label`+`license` (Insignias) por fila; **nuevo ajuste aditivo**: `translate="no"` condicional sobre `suffix` cuando sea un símbolo puro, en `template-parts/stats.php` (punto 5).
- **9.9 a 9.13** — sin cambios de alcance respecto al plan ya revisado.

---

## Próximo paso

Conforme a D-038, el **Entregable 9.3** (primer Entregable con cambios reales de código: `.pot`, `/languages`, `inc/enqueue.php`, `assets/js/main.js`) no inicia sin tu aprobación explícita de este documento completo.
