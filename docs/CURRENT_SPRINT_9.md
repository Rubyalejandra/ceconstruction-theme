# CE Construction — CURRENT_SPRINT_9.md
### Referencia oficial del Sprint 9 ("Internacionalización (i18n) con Polylang")

> Track paralelo e independiente, mismo patrón ya usado por el proyecto para separar `CURRENT_SPRINT.md` (Sprint 8, QA) de `CURRENT_UX_SPRINT.md` (fase UX, cerrada por completo). Ver `docs/DECISIONS.md` D-111 a D-114 (histórico) y D-115 a D-117 (pendientes de incorporar en el próximo cierre documental, ver nota al final).

---

## Contexto ya validado (sesiones previas)

- El tema ya es compatible de fábrica con Polylang para contenido editorial (CPTs, Páginas, blog) y textos fijos de plantillas (`__()`/`_e()` con text-domain `ce-construction` en el 100% del código PHP).
- Las 12 cadenas de `assets/js/main.js` que no pasaban por `wp_localize_script()` quedaron resueltas en el **Entregable 9.3** (aprobado).
- El **Entregable 9.4** (instalación/configuración base de Polylang) se ejecutó en el WordPress real del usuario: se corrigieron 4 idiomas iniciales (3 variantes de español + inglés) a 2 (Español, predeterminado, con los 31 posts de contenido real; Inglés), modo de URL por subcarpeta (`/en/`), detección por `Accept-Language` activada. **Aprobado.**
- **Entregable 9.5 (esta entrega): 20 claves `theme_mod` de tipo TEXTO registradas ante Polylang y ya traducibles en el frontend** — ver detalle abajo.
- Plugin: Polylang, versión gratuita — decisión ya tomada.

---

## Estado de los Entregables

| Entregable | Objetivo | Estado |
|---|---|---|
| 9.1 | Auditoría completa de i18n del tema | ✅ **Aprobado** — `docs/DECISIONS.md` D-113. |
| 9.2 | Documento de decisiones (sin código) | ✅ **Aprobado.** Documento: `docs/I18N_DECISIONES_9.2.md`. |
| 9.3 | `/languages` + `.pot` + 12 claves de JS movidas a `i18n` | ✅ **Aprobado.** |
| 9.4 | Instalación y configuración base de Polylang | ✅ **Aprobado** (2 idiomas confirmados: Español predeterminado / Inglés, subcarpeta `/en/`). |
| 9.5 | Registro `pll_register_string()` de las 20 claves TEXTO + `ce_footer_copyright` (placeholder protegido) | 🟡 **Entregado — pendiente de tu aprobación explícita.** Ver detalle abajo. |
| 9.6 | CPT/taxonomías traducibles (`servicio`/`proyecto`/`ce_faq`); `testimonio`/`miembro_equipo`/`cliente` explícitamente NO traducibles | ⬜ Propuesto — no inicia sin aprobación de 9.5 (D-038). |
| 9.7 | Selector de página para `ce_testimonials_page_url` únicamente | ⬜ Propuesto. |
| 9.8 | Identificador estable por fila + registro de repetidores JSON + `translate="no"` condicional | ⬜ Propuesto. |
| 9.9 a 9.13 | SEO/hreflang, pruebas funcionales, regresión, prueba con traductor de navegador, cierre formal | ⬜ Propuestos, sin cambios de alcance. |

---

## Trabajo realizado (Entregable 9.5)

Conforme a `docs/I18N_DECISIONES_9.2.md` (punto 8, aprobado), se implementó el registro ante Polylang de las **20 claves `theme_mod` de tipo TEXTO** identificadas en la auditoría 9.1 (19 claves simples + `ce_footer_copyright` con tratamiento especial).

**Hallazgo técnico necesario, corrección del alcance de archivos originalmente previsto para este Entregable (documentado con transparencia, no oculto):** el plan original de 9.1/9.2 preveía que 9.5 solo tocaría `inc/customizer.php` o un archivo nuevo, "sin tocar plantillas de render salvo... `ce_footer_copyright`". En la práctica, **registrar una cadena con `pll_register_string()` no la traduce por sí sola** — es necesario además llamar a `pll__()` (o un envoltorio propio) exactamente en el punto donde cada valor se imprime, o el registro queda inerte y nunca se ve reflejado en el frontend. Por eso este Entregable sí tocó, de forma quirúrgica (una línea por punto, sin reestructurar nada alrededor), las 8 plantillas/archivos donde esas 20 claves se leen e imprimen.

### Mecanismo implementado
1. **`ce_construction_pll__( $string )`** (nueva función, `inc/helpers.php`) — envoltorio mínimo sobre `pll__()` de Polylang. Sin Polylang activo, o si la cadena nunca se registró, devuelve el mismo valor recibido — cero regresión.
2. **`ce_construction_register_polylang_strings()`** (nuevo archivo, `inc/polylang-strings.php`, enganchado a `init`) — registra las 20 claves con su valor actual (incluido su *fallback* por defecto) ante Polylang. Los campos opcionales aún sin configurar (Popup de Oferta, Horario) no se registran vacíos — se registran solos en cuanto el administrador los completa.
3. **`ce_footer_copyright`** (Decisión 8): se registra y traduce la **plantilla** con los placeholders `%1$d`/`%2$s` intactos — el año y el nombre del sitio se interpolan *después* de obtener la traducción, nunca antes. Nueva función `ce_construction_footer_copyright_template()` como fuente única de esa plantilla, reutilizada por el registro y por `footer.php`.
4. **20 puntos de impresión envueltos** con `ce_construction_pll__( get_theme_mod( ... ) )`: `template-parts/hero.php` (4), `template-parts/cta.php` (3, cubre variantes primaria y secundaria automáticamente vía `$ce_cta_prefix`), `template-parts/financing.php` (3), `inc/helpers.php` → `ce_get_offer_popup_data()` (4), `footer.php` (`ce_footer_about`, `ce_schedule`, `ce_footer_copyright` con su estrategia especial), `header.php` (`ce_schedule`), `inc/widgets.php` (`ce_schedule`, solo la rama de `theme_mod`; la personalización propia del widget queda fuera de alcance, no es una de las 20 claves auditadas).

**Verificado explícitamente que este Entregable no toca:** los 4 datos de contacto (`ce_phone`/`ce_email`/`ce_address`/`ce_whatsapp_number`, punto 7 de 9.2, sin registro por decisión explícita), ningún CPT/taxonomía (9.6), ninguna URL (9.7), ningún JSON dinámico (9.8).

## Archivos creados / modificados (Entregable 9.5)
- **Creado:** `inc/polylang-strings.php`.
- **Modificados:** `inc/helpers.php` (nueva función `ce_construction_pll__()`, aditiva; 4 campos del Popup de Oferta envueltos en `ce_get_offer_popup_data()`), `functions.php` (registro del nuevo módulo, después de `inc/helpers.php`), `header.php`, `footer.php` (3 puntos, incluida la reescritura del bloque de `ce_footer_copyright`), `template-parts/hero.php` (4 campos), `template-parts/cta.php` (3 campos), `template-parts/financing.php` (3 campos), `inc/widgets.php` (1 campo, rama `theme_mod`).
- **Sin cambios:** `inc/customizer.php` (ningún control ni setting del Customizer se modificó — el registro ante Polylang es independiente de cómo se capturan los valores).

## Validaciones ejecutadas
- Balance de llaves/paréntesis verificado en los 9 archivos tocados/creados — todos balanceados (`php -l` no disponible en este entorno, misma limitación metodológica ya documentada).
- Confirmado por `grep` que ninguna de las 20 claves quedó sin su envoltorio `ce_construction_pll__()` en su punto de impresión (los 2 únicos usos restantes de `get_theme_mod('ce_schedule')` sin envolver son comprobaciones de verdad/falsedad `if (...)`, que no necesitan traducción).
- Trazado lógico manual del flujo de `ce_footer_copyright`: sin personalizar (valor `''`) → usa la plantilla por defecto → se traduce la plantilla → se interpola año/sitio — idéntico resultado visual al anterior en español mientras no exista traducción registrada para el idioma activo.
- **Pendiente de prueba funcional real en WordPress** (misma limitación metodológica de todo el proyecto): confirmar que el sitio en español sigue idéntico; entrar a Idiomas → Traducción de cadenas → grupo "CE Construction" y confirmar que aparecen las ~17-20 cadenas (las que ya tengan contenido no vacío); traducir 2-3 al inglés y confirmar que se reflejan correctamente en `/en/`, incluido el copyright del footer con el año actual correcto.

## Documentación pendiente, sin cambios de alcance
`docs/TREE.md`, `docs/CHANGELOG.md`, `docs/PROJECT_STATUS.md`, `docs/TODO.md`, `docs/HANDOFF.md`, `docs/DECISIONS.md` (entradas D-115 a D-117 para 9.3/9.4/9.5) — no actualizados en esta entrega, diferidos al cierre formal del Sprint 9 (Entregable 9.13) o a un punto de cierre más significativo, mismo criterio D-034 ya vigente en el proyecto.

## Próximo paso
El Entregable 9.6 (CPT/taxonomías traducibles) **no inicia** sin que apruebes explícitamente este 9.5, y sin que confirmes en tu WordPress real que las cadenas aparecen en el panel de Traducción de cadenas de Polylang y se traducen correctamente en el frontend, conforme a D-038.
