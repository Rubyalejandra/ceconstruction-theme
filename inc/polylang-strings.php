<?php
/**
 * inc/polylang-strings.php
 *
 * 🆕 Sprint 9, Entregable 9.5 (i18n/Polylang) — registro de las claves
 * `theme_mod` de tipo TEXTO ante Polylang, vía `pll_register_string()`.
 *
 * Por qué un archivo propio y no una extensión de inc/customizer.php:
 * misma convención ya vigente en el proyecto ("un archivo = una
 * responsabilidad concreta", ver docs/ARCHITECTURE.md §10) — este
 * archivo tiene una única responsabilidad (i18n de theme_mod ante
 * Polylang), independiente del registro/renderizado de los controles
 * del Customizer en sí, que sigue viviendo exclusivamente en
 * inc/customizer.php sin ningún cambio.
 *
 * Decisiones de diseño aplicadas aquí (ver docs/I18N_DECISIONES_9.2.md):
 * - Punto 1 (strings de JS): no aplica a este archivo — resuelto en el
 *   Entregable 9.3 (assets/js/main.js + ceConstructionData.i18n).
 * - Punto 7 (datos de contacto): ce_phone, ce_email, ce_address y
 *   ce_whatsapp_number NO se registran aquí — el usuario confirmó que
 *   el negocio usa el mismo valor en todos los idiomas.
 * - Punto 8 (ce_footer_copyright): se registra como PLANTILLA, con los
 *   placeholders %1$d/%2$s intactos, nunca como el resultado ya
 *   interpolado — ver ce_construction_footer_copyright_template() más
 *   abajo, reutilizada también por footer.php al momento de renderizar
 *   (fuente única de verdad, evita que ambos puntos se desincronicen).
 *
 * Mecanismo de traducción en el frontend (no basta con registrar aquí):
 * cada punto de la plantilla que imprime una de estas 20 claves fue
 * modificado para envolver el resultado de get_theme_mod() con
 * ce_construction_pll__() (inc/helpers.php, nueva función aditiva) —
 * sin ese envoltorio en el punto de impresión, el registro de abajo
 * quedaría inerte (una cadena solo queda disponible para traducir en el
 * panel de Polylang, nunca se traduce sola en el HTML sin ese segundo
 * paso). Archivos con ese envoltorio añadido en este mismo Entregable:
 * template-parts/hero.php, template-parts/cta.php,
 * template-parts/financing.php, footer.php, header.php, inc/widgets.php
 * (rama de theme_mod, no la personalización propia del widget),
 * inc/helpers.php (ce_get_offer_popup_data()).
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plantilla por defecto del copyright del footer, CON los placeholders
 * de sprintf() intactos (%1$d año, %2$s nombre del sitio) — nunca el
 * resultado ya interpolado. Única fuente de verdad, reutilizada tanto
 * por el registro de Polylang de abajo como por footer.php al momento
 * de renderizar — evita que ambos puntos puedan desincronizarse.
 *
 * @return string
 */
function ce_construction_footer_copyright_template() {
	return __( '&copy; %1$d %2$s. Todos los derechos reservados.', 'ce-construction' );
}

/**
 * Registra ante Polylang las 19 claves `theme_mod` de tipo TEXTO
 * identificadas en la auditoría del Sprint 9
 * (docs/I18N_AUDIT_SPRINT9_9.1_completo.md, Sección D), más
 * `ce_footer_copyright` (20ª clave, con su estrategia de plantilla
 * protegida — ver arriba y docs/I18N_DECISIONES_9.2.md punto 8).
 *
 * Se registra el VALOR ACTUAL de cada theme_mod (el que get_theme_mod()
 * ya devuelve hoy, incluido su fallback por defecto) — es el string que
 * Polylang debe ofrecer para traducir en su panel "Idiomas →
 * Traducción de cadenas". Si el administrador cambia ese valor más
 * adelante desde el Customizer, Polylang lo tratará como una cadena
 * nueva a traducir (comportamiento nativo y documentado del propio
 * plugin, no una limitación de este archivo).
 *
 * Enganchada a 'init' — mismo hook recomendado por la documentación
 * oficial de Polylang para pll_register_string(), garantizando que el
 * plugin ya esté cargado antes de invocar su función.
 */
function ce_construction_register_polylang_strings() {
	if ( ! function_exists( 'pll_register_string' ) ) {
		// Polylang no está activo — no hay nada que registrar. Ninguna
		// otra parte del tema depende de que esta función se ejecute:
		// ce_construction_pll__() (inc/helpers.php) ya maneja el mismo
		// caso de forma independiente, devolviendo el string recibido
		// tal cual cuando pll__() no existe.
		return;
	}

	$group = 'CE Construction';

	$strings = array(
		// Hero (template-parts/hero.php) — 4 claves.
		'CE Construction — Hero: Título'                => get_theme_mod( 'ce_hero_title', __( 'Construimos con precisión, entregamos con confianza', 'ce-construction' ) ),
		'CE Construction — Hero: Subtítulo'              => get_theme_mod( 'ce_hero_subtitle', __( 'Más de una década ejecutando proyectos residenciales, comerciales e industriales con los más altos estándares de calidad y seguridad.', 'ce-construction' ) ),
		'CE Construction — Hero: Texto botón 1'          => get_theme_mod( 'ce_hero_btn1_text', __( 'Cotización Gratuita', 'ce-construction' ) ),
		'CE Construction — Hero: Texto botón 2'          => get_theme_mod( 'ce_hero_btn2_text', __( 'Ver Proyectos', 'ce-construction' ) ),

		// Footer (footer.php) — 1 clave (ce_footer_copyright se registra aparte, ver abajo).
		'CE Construction — Footer: Descripción'          => get_theme_mod( 'ce_footer_about', __( 'Empresa constructora especializada en proyectos residenciales, comerciales e industriales, comprometida con la calidad, la seguridad y el cumplimiento de cada obra.', 'ce-construction' ) ),

		// CTA primario (template-parts/cta.php, variant='primary'/'sidebar') — 3 claves.
		'CE Construction — CTA primario: Título'         => get_theme_mod( 'ce_cta_title', __( '¿Listo para construir tu próximo proyecto?', 'ce-construction' ) ),
		'CE Construction — CTA primario: Texto'          => get_theme_mod( 'ce_cta_text', __( 'Solicita una cotización gratuita y un asesor se pondrá en contacto contigo en menos de 24 horas.', 'ce-construction' ) ),
		'CE Construction — CTA primario: Texto botón'    => get_theme_mod( 'ce_cta_btn_text', __( 'Solicitar Cotización', 'ce-construction' ) ),

		// CTA secundario (template-parts/cta.php, variant='secondary') — 3 claves.
		'CE Construction — CTA secundario: Título'       => get_theme_mod( 'ce_cta2_title', __( '¿Prefieres que te contactemos nosotros?', 'ce-construction' ) ),
		'CE Construction — CTA secundario: Texto'        => get_theme_mod( 'ce_cta2_text', __( 'Déjanos tus datos y un asesor se comunicará contigo para resolver tus dudas antes de que decidas.', 'ce-construction' ) ),
		'CE Construction — CTA secundario: Texto botón'  => get_theme_mod( 'ce_cta2_btn_text', __( 'Solicitar Cotización', 'ce-construction' ) ),

		// Financiamiento (template-parts/financing.php) — 3 claves.
		'CE Construction — Financiamiento: Título'       => get_theme_mod( 'ce_financing_title', __( 'Opciones de financiamiento a tu medida', 'ce-construction' ) ),
		'CE Construction — Financiamiento: Texto'        => get_theme_mod( 'ce_financing_text', __( 'Habla con nosotros sobre planes de pago flexibles y pre-aprobación sin afectar tu historial crediticio.', 'ce-construction' ) ),
		'CE Construction — Financiamiento: Texto botón'  => get_theme_mod( 'ce_financing_btn_text', __( 'Conocer opciones de pago', 'ce-construction' ) ),

		// Popup de Oferta (inc/helpers.php, ce_get_offer_popup_data()) — 4 claves.
		// Los 3 primeros nacen vacíos por defecto (sección oculta hasta
		// que el administrador la configura) — se filtran más abajo.
		'CE Construction — Popup de oferta: Título'      => get_theme_mod( 'ce_offer_popup_title', '' ),
		'CE Construction — Popup de oferta: Texto'       => get_theme_mod( 'ce_offer_popup_text', '' ),
		'CE Construction — Popup de oferta: Insignia'    => get_theme_mod( 'ce_offer_popup_badge_text', '' ),
		'CE Construction — Popup de oferta: Texto botón' => get_theme_mod( 'ce_offer_popup_btn_text', __( 'Quiero mi cotización', 'ce-construction' ) ),

		// Horario de atención (header.php / footer.php / inc/widgets.php) — 1 clave.
		// Nace vacío por defecto — se filtra más abajo si no está configurado.
		'CE Construction — Horario de atención'          => get_theme_mod( 'ce_schedule', '' ),

		// ce_footer_copyright (20ª clave) — SIEMPRE la plantilla con
		// placeholders intactos, nunca el resultado ya interpolado.
		// Ver ce_construction_footer_copyright_template() arriba y
		// docs/I18N_DECISIONES_9.2.md punto 8.
		'CE Construction — Footer: Copyright (plantilla)' => ce_construction_footer_copyright_template(),
	);

	foreach ( $strings as $name => $value ) {
		if ( '' === $value ) {
			// Campo opcional todavía sin configurar (ej. Popup de
			// Oferta desactivado, sin horario definido) — no se
			// registra una cadena vacía ante Polylang. Se registrará
			// sola en cuanto el administrador la complete: 'init' se
			// ejecuta en cada request, sin necesitar ningún paso manual
			// adicional ni volver a activar el plugin.
			continue;
		}
		pll_register_string( $name, $value, $group );
	}
}
add_action( 'init', 'ce_construction_register_polylang_strings' );
