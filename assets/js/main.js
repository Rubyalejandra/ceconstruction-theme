/**
 * CE CONSTRUCTION — MAIN JS
 * Arquitectura: módulos ES6 independientes, cada uno se auto-inicializa
 * solo si encuentra su marcado en el DOM (evita errores en páginas
 * donde un componente no está presente).
 *
 * Depende de `ceConstructionData` (localizado desde inc/enqueue.php):
 *   - ajaxUrl
 *   - quoteNonce
 *   - whatsapp
 *   - i18n { sending, error }
 *
 * @package CE_Construction
 */

(() => {
	'use strict';

	/* ============================================================
	 * UTILIDADES COMPARTIDAS
	 * ============================================================ */
	const $  = (sel, ctx = document) => ctx.querySelector(sel);
	const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
	const on = (el, ev, fn, opts) => el && el.addEventListener(ev, fn, opts);
	// 🆕 QA-036 (Sprint 8, Entregable 8.6): hasta ahora ningún módulo
	// necesitaba quitar un listener dinámico — `on()` (arriba) nunca
	// tuvo su pareja. FocusTrap sí la necesita: el listener de `Tab`
	// que atrapa el foco dentro de un overlay debe desactivarse al
	// cerrarlo, o quedaría escuchando `keydown` sobre un contenedor ya
	// oculto para siempre.
	const off = (el, ev, fn, opts) => el && el.removeEventListener(ev, fn, opts);

	const CE = window.ceConstructionData || {
		ajaxUrl: '/wp-admin/admin-ajax.php',
		quoteNonce: '',
		whatsapp: '',
		i18n: { sending: 'Enviando...', error: 'Ocurrió un error. Intenta nuevamente.' },
	};

	/* ============================================================
	 * UTILIDAD COMPARTIDA: FOCUS TRAP
	 *
	 * QA-036 (Sprint 8, Entregable 8.6) / R-4 (QA_REPORT.md, "Gestión
	 * de foco centralizada para overlays"): antes de esta corrección,
	 * ningún overlay del tema (menú móvil, modales genéricos, popup de
	 * oferta, lightbox) atrapaba el foco de teclado dentro de sí mismo
	 * mientras estaba abierto — un usuario de teclado podía seguir
	 * tabulando hacia elementos del fondo de la página, invisibles
	 * detrás del overlay. `ModuleLightbox` ya movía el foco al abrir y
	 * lo devolvía al cerrar (ver su open()/close() más abajo, ahora
	 * migrados a usar esta misma utilidad en vez de su lógica manual
	 * propia); `ModuleMobileNav` y `ModuleModals` no hacían ninguna de
	 * las dos cosas.
	 *
	 * Diseño (R-4 pide explícitamente "centralizar", no una corrección
	 * por componente): un único objeto `FocusTrap` con `.activate()`/
	 * `.deactivate()`, reutilizado por los 3 overlays del tema en vez
	 * de 3 implementaciones independientes del mismo patrón ARIA
	 * "Dialog (Modal)". Solo puede haber un trap activo a la vez (este
	 * tema nunca abre overlays anidados de verdad — cuando
	 * `ModuleQuoteForm` cierra el modal de cotización para abrir el de
	 * éxito/error, son secuenciales, no simultáneos — `activate()`
	 * maneja ese reemplazo sin intentar restaurar el foco hacia el
	 * overlay saliente, ya que el foco se va a mover de inmediato hacia
	 * el entrante).
	 * ============================================================ */
	const FocusTrap = {
		active: null, // { container, trigger, onKeydown }

		/** Elementos realmente enfocables y visibles dentro de `container`. */
		getFocusable(container) {
			return $$(
				'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])',
				container
			).filter((el) => el.offsetParent !== null);
		},

		/**
		 * Activa el trap sobre `container`: mueve el foco a
		 * `options.initialFocus` (o al primer elemento enfocable si no
		 * se indica) y mantiene el `Tab`/`Shift+Tab` circulando solo
		 * entre los elementos enfocables de `container` mientras esté
		 * activo.
		 *
		 * `options.trigger`: elemento al que se devuelve el foco en
		 * `deactivate()`. Si no se indica, se usa `document.activeElement`
		 * en el momento de activar — cubre el caso de `ModuleOfferPopup`
		 * (se abre por temporizador, sin ningún disparador de usuario:
		 * ahí `document.activeElement` normalmente es `<body>`, y
		 * "devolver el foco a body" es un no-op inofensivo).
		 */
		activate(container, options = {}) {
			if (!container) return;

			// Reemplazo de un trap activo sobre OTRO contenedor (ver
			// docblock arriba): se desmonta su listener sin restaurar
			// foco, porque el foco va a moverse de inmediato al nuevo
			// contenedor unas líneas más abajo.
			if (this.active && this.active.container !== container) {
				off(this.active.container, 'keydown', this.active.onKeydown);
				this.active = null;
			}

			const trigger = 'trigger' in options ? options.trigger : document.activeElement;

			const onKeydown = (e) => {
				if (e.key !== 'Tab') return;
				const focusable = this.getFocusable(container);
				if (!focusable.length) {
					e.preventDefault();
					return;
				}
				const first = focusable[0];
				const last = focusable[focusable.length - 1];
				if (e.shiftKey && document.activeElement === first) {
					e.preventDefault();
					last.focus();
				} else if (!e.shiftKey && document.activeElement === last) {
					e.preventDefault();
					first.focus();
				}
			};

			on(container, 'keydown', onKeydown);
			this.active = { container, trigger, onKeydown };

			const initialFocus = options.initialFocus || this.getFocusable(container)[0];
			if (initialFocus && typeof initialFocus.focus === 'function') {
				initialFocus.focus();
			}
		},

		/**
		 * Desactiva el trap de `container` — sin efecto si `container`
		 * no es el que tiene el trap activo ahora mismo (por ejemplo,
		 * una llamada a `close()` sobre un overlay que ya no estaba
		 * activo). Devuelve el foco al `trigger` registrado en
		 * `activate()`, si sigue existiendo en el documento.
		 */
		deactivate(container) {
			if (!this.active || this.active.container !== container) return;
			const { trigger, onKeydown } = this.active;
			off(container, 'keydown', onKeydown);
			this.active = null;
			if (trigger && typeof trigger.focus === 'function' && document.body.contains(trigger)) {
				trigger.focus();
			}
		},
	};

	/* ============================================================
	 * MÓDULO: SCROLL SUAVE PARA ENLACES INTERNOS (#anchor)
	 * ============================================================ */
	const ModuleSmoothScroll = {
		init() {
			on(document, 'click', (e) => {
				const link = e.target.closest('a[href^="#"]:not([href="#"])');
				if (!link) return;

				const targetId = link.getAttribute('href').slice(1);
				const target = document.getElementById(targetId);
				if (!target) return;

				e.preventDefault();

				// 🆕 Sprint UX-3, Entregable UX-3.2: un ancla que apunta a un
				// modal (p. ej. los CTA de cotización cuando
				// ce_quote_form_mode = 'modal', href="#ce-quote-modal")
				// debe ABRIR ese modal vía ModuleModals — no hacer scroll
				// hacia un elemento `position:fixed` oculto por CSS, que no
				// tendría ningún efecto visible. Reutiliza el mismo
				// ModuleModals.open() que ya usa ModuleQuoteForm para
				// success/error; no se crea ningún mecanismo nuevo de
				// apertura/cierre — ver DECISIONS.md D-051.
				if (target.classList.contains('ce-modal-overlay')) {
					// QA-036: se pasa `link` (el propio <a> clicado) como
					// trigger explícito — ver FocusTrap/ModuleModals.open()
					// más abajo.
					ModuleModals.open(targetId, { trigger: link });
					document.dispatchEvent(new CustomEvent('ce:closeMobileNav'));
					return;
				}

				const header = $('.ce-header');
				const offset = header ? header.offsetHeight : 0;
				const top = target.getBoundingClientRect().top + window.pageYOffset - offset - 16;

				window.scrollTo({ top, behavior: 'smooth' });

				// Cierra el menú móvil si estaba abierto al navegar por ancla.
				document.dispatchEvent(new CustomEvent('ce:closeMobileNav'));
			});
		},
	};

	/* ============================================================
	 * MÓDULO: MENÚ RESPONSIVE (mobile nav + overlay)
	 * ============================================================ */
	const ModuleMobileNav = {
		init() {
			this.toggle  = $('.ce-nav-toggle');
			this.nav     = $('.ce-nav-mobile');
			this.overlay = $('.ce-nav-overlay');
			this.closeBtn = $('.ce-nav-mobile__close');

			if (!this.toggle || !this.nav) return;

			on(this.toggle, 'click', () => this.open());
			on(this.overlay, 'click', () => this.close());
			on(this.closeBtn, 'click', () => this.close());
			on(document, 'keydown', (e) => {
				if (e.key === 'Escape') this.close();
			});
			on(document, 'ce:closeMobileNav', () => this.close());

			// Cierra al hacer click en cualquier link del menú móvil.
			$$('a', this.nav).forEach((a) => on(a, 'click', () => this.close()));
		},
		open() {
			this.nav.classList.add('is-open');
			this.overlay && this.overlay.classList.add('is-visible');
			this.toggle.classList.add('is-active');
			this.toggle.setAttribute('aria-expanded', 'true');
			document.body.classList.add('ce-no-scroll');

			// QA-036: trap de foco dentro del panel. `this.toggle` es
			// siempre el disparador real (es el único elemento que abre
			// este menú), así que se pasa explícito en vez de confiar en
			// document.activeElement.
			FocusTrap.activate(this.nav, { trigger: this.toggle, initialFocus: this.closeBtn });
		},
		close() {
			this.nav.classList.remove('is-open');
			this.overlay && this.overlay.classList.remove('is-visible');
			this.toggle.classList.remove('is-active');
			this.toggle.setAttribute('aria-expanded', 'false');
			document.body.classList.remove('ce-no-scroll');

			// QA-036: sin efecto si el menú no era el trap activo (p. ej.
			// close() disparado por 'ce:closeMobileNav' con el menú ya
			// cerrado) — ver FocusTrap.deactivate() arriba.
			FocusTrap.deactivate(this.nav);
		},
	};

	/* ============================================================
	 * MÓDULO: STICKY HEADER (añade sombra/estado al hacer scroll)
	 * ============================================================ */
	const ModuleStickyHeader = {
		init() {
			this.header = $('.ce-header');
			if (!this.header) return;

			this.threshold = 24;
			this.onScroll = this.onScroll.bind(this);
			on(window, 'scroll', this.onScroll, { passive: true });
			this.onScroll();
		},
		onScroll() {
			if (window.scrollY > this.threshold) {
				this.header.classList.add('is-scrolled');
			} else {
				this.header.classList.remove('is-scrolled');
			}
		},
	};

	/* ============================================================
	 * MÓDULO: BOTÓN VOLVER ARRIBA
	 * ============================================================ */
	const ModuleBackToTop = {
		init() {
			this.btn = $('.ce-float-btn--top');
			if (!this.btn) return;

			this.onScroll = this.onScroll.bind(this);
			on(window, 'scroll', this.onScroll, { passive: true });
			on(this.btn, 'click', () => {
				window.scrollTo({ top: 0, behavior: 'smooth' });
			});
			this.onScroll();
		},
		onScroll() {
			if (window.scrollY > 480) {
				this.btn.classList.add('is-visible');
			} else {
				this.btn.classList.remove('is-visible');
			}
		},
	};

	/* ============================================================
	 * MÓDULO: WHATSAPP FLOTANTE
	 * Construye el enlace wa.me dinámicamente con el número
	 * configurado en el Customizer (pasado vía ceConstructionData).
	 * ============================================================ */
	const ModuleWhatsAppFloat = {
		init() {
			this.link = $('.ce-float-btn--whatsapp');
			if (!this.link || !CE.whatsapp) return;

			const message = encodeURIComponent(
				this.link.dataset.message || 'Hola, quisiera más información sobre sus servicios de construcción.'
			);
			this.link.setAttribute(
				'href',
				`https://wa.me/${CE.whatsapp.replace(/\D/g, '')}?text=${message}`
			);
			this.link.setAttribute('target', '_blank');
			this.link.setAttribute('rel', 'noopener noreferrer');
		},
	};

	/* ============================================================
	 * MÓDULO: CONTADORES ANIMADOS (stats)
	 * Usa IntersectionObserver para animar solo al entrar en viewport.
	 * ============================================================ */
	const ModuleCounters = {
		init() {
			this.counters = $$('.ce-stat__number[data-count]');
			if (!this.counters.length) return;

			if (!('IntersectionObserver' in window)) {
				this.counters.forEach((el) => this.animate(el));
				return;
			}

			const observer = new IntersectionObserver((entries) => {
				entries.forEach((entry) => {
					if (entry.isIntersecting) {
						this.animate(entry.target);
						observer.unobserve(entry.target);
					}
				});
			}, { threshold: 0.4 });

			this.counters.forEach((el) => observer.observe(el));
		},
		animate(el) {
			const target = parseFloat(el.dataset.count) || 0;
			const duration = parseInt(el.dataset.duration, 10) || 1800;
			const suffix = el.dataset.suffix || '';
			const startTime = performance.now();
			const numberEl = el.querySelector('.ce-stat__number-value') || el;
			const isInt = Number.isInteger(target);

			const step = (now) => {
				const progress = Math.min((now - startTime) / duration, 1);
				// easeOutCubic
				const eased = 1 - Math.pow(1 - progress, 3);
				const current = target * eased;
				numberEl.textContent = (isInt ? Math.floor(current) : current.toFixed(1)) + suffix;

				if (progress < 1) {
					requestAnimationFrame(step);
				} else {
					numberEl.textContent = (isInt ? target : target.toFixed(1)) + suffix;
				}
			};
			requestAnimationFrame(step);
		},
	};

	/* ============================================================
	 * FÁBRICA COMPARTIDA: CONTROLADOR DE SLIDER (autoplay + swipe +
	 * flechas + dots opcionales).
	 *
	 * 🆕 Sprint UX-4, Entregable UX-4.2 (fase "Optimización UX /
	 * Conversión"). Antes de este Entregable, esta lógica vivía
	 * duplicada dentro del objeto `ModuleTestimonialSlider` de forma
	 * no reutilizable. Se extrae aquí para que tanto
	 * `ModuleTestimonialSlider` (abajo, refactorizado para usar esta
	 * fábrica, MISMO comportamiento de antes — ver DECISIONS.md
	 * D-055) como el nuevo `ModuleHeroSlider` la reutilicen sin
	 * copiar/pegar ninguna línea de la mecánica de slide (mitigación
	 * de R-4 ya prevista en docs/UX_CONVERSION_ANALISIS_Y_PLAN.md).
	 *
	 * @param {Object} config
	 * @param {string}  config.rootSelector    Selector del contenedor raíz (lee data-autoplay).
	 * @param {string}  config.trackSelector   Selector del track (dentro de root).
	 * @param {string}  config.slideSelector   Selector de cada slide (dentro de track).
	 * @param {string}  [config.navSelector]   Selector del contenedor de dots (dentro de root.parentElement). Si se omite, no se crean dots.
	 * @param {string}  [config.prevSelector]  Selector de la flecha "anterior" (dentro de root.parentElement).
	 * @param {string}  [config.nextSelector]  Selector de la flecha "siguiente" (dentro de root.parentElement).
	 * @param {string}  [config.dotLabel]      Prefijo de aria-label de cada dot (recibe el número, 1-indexed).
	 * @param {number}  [config.defaultDelay]  Delay de autoplay (ms) si el root no trae data-autoplay. Por defecto 6000.
	 * @param {boolean} [config.swipe]         Si es `false`, no se activa el swipe táctil. Por defecto `true`.
	 * @param {boolean} [config.pauseOnHover]  Si es `false`, el autoplay no se detiene con mouseenter/mouseleave. Por defecto `true`.
	 * @param {boolean} [config.pausable]      🆕 QA-035. Si es `true`, se construye un botón de pausa/reanudación
	 *                                         accesible por teclado/touch (ver buildPauseToggle() abajo) y el foco de
	 *                                         teclado dentro del slider también detiene el autoplay mientras dure
	 *                                         (equivalente por teclado al pauseOnHover del mouse). Por defecto `false`
	 *                                         — no cambia el comportamiento de sliders que no lo activen explícitamente
	 *                                         (ModuleHeroSlider, fondo decorativo sin dots/flechas, D-055).
	 * @param {string}  [config.pauseLabel]    Prefijo de aria-label del botón de pausa (recibe el texto de "Pausar"/
	 *                                         "Reanudar" de ceConstructionData.i18n — ver CE arriba). Requerido si
	 *                                         `pausable` es `true`.
	 * @returns {Object} Controlador con `.init()`, listo para usarse como módulo del bootstrap.
	 * ============================================================ */
	function createSliderController(config) {
		return {
			init() {
				this.root = $(config.rootSelector);
				if (!this.root) return;

				this.track = $(config.trackSelector, this.root);
				this.slides = $$(config.slideSelector, this.track);
				if (!this.slides.length) return;

				this.current = 0;
				this.autoplayDelay = parseInt(this.root.dataset.autoplay, 10) || config.defaultDelay || 6000;
				this.timer = null;
				// 🆕 QA-035: bandera de pausa MANUAL (botón), independiente de
				// pauseOnHover/foco — una vez que el usuario pulsa "Pausar" a
				// propósito, ni mouseleave ni blur deben reanudar el autoplay
				// por su cuenta; solo lo reanuda una acción explícita (pulsar
				// de nuevo el mismo botón). Sin esto, un usuario de teclado que
				// pausa y luego tabula fuera del slider vería el autoplay
				// reanudarse solo por el evento focusout, contradiciendo su
				// intención explícita.
				this.userPaused = false;

				this.buildNav();
				this.bindArrows();
				this.bindSwipe();
				if (config.pausable) this.buildPauseToggle();
				this.goTo(0);
				this.startAutoplay();

				if (config.pauseOnHover !== false) {
					on(this.root, 'mouseenter', () => this.stopAutoplay());
					on(this.root, 'mouseleave', () => { if (!this.userPaused) this.startAutoplay(); });
				}

				// 🆕 QA-035: equivalente por teclado de pauseOnHover. Un
				// usuario que navega exclusivamente con teclado no dispara
				// mouseenter/mouseleave, así que sin esto no tendría ninguna
				// forma de detener el movimiento automático salvo el botón
				// de pausa explícito (buildPauseToggle(), abajo) — se añaden
				// ambos mecanismos, no uno en lugar del otro, para cubrir
				// tanto a quien tabula hasta un control interno del slider
				// (flechas, dots, el propio botón de pausa) como a quien
				// prefiere pulsar el botón directamente sin necesidad de
				// tener el foco dentro. `focusin`/`focusout` sí burbujean
				// (a diferencia de `focus`/`blur`), por eso se usan aquí.
				if (config.pausable) {
					on(this.root, 'focusin', () => this.stopAutoplay());
					on(this.root, 'focusout', () => { if (!this.userPaused) this.startAutoplay(); });
				}
			},
			buildNav() {
				if (!config.navSelector) return;
				this.nav = $(config.navSelector, this.root.parentElement);
				if (!this.nav) return;
				this.dots = this.slides.map((_, i) => {
					const dot = document.createElement('button');
					dot.className = 'ce-slider-dot';
					dot.setAttribute('aria-label', `${config.dotLabel || 'Slide'} ${i + 1}`);
					on(dot, 'click', () => this.goTo(i));
					this.nav.appendChild(dot);
					return dot;
				});
			},
			/**
			 * 🆕 QA-035: botón de pausa/reanudación del autoplay, operable
			 * por teclado (es un <button> nativo, focusable por Tab y
			 * activable con Enter/Espacio sin JS adicional) y por touch
			 * (evento 'click' estándar, sin depender de hover). Se añade al
			 * mismo contenedor `.ce-slider-nav` que ya usan los dots
			 * (buildNav(), arriba) cuando existe; si el slider no tiene nav
			 * de dots (config.navSelector ausente), se crea un contenedor
			 * propio para no depender de un marcado que el slider en
			 * cuestión no tiene por diseño.
			 */
			buildPauseToggle() {
				this.nav = this.nav || (() => {
					const nav = document.createElement('div');
					nav.className = 'ce-slider-nav';
					this.root.parentElement.appendChild(nav);
					return nav;
				})();

				this.pauseBtn = document.createElement('button');
				this.pauseBtn.type = 'button';
				this.pauseBtn.className = 'ce-slider-pause';
				this.nav.appendChild(this.pauseBtn);

				on(this.pauseBtn, 'click', () => {
					this.userPaused = !this.userPaused;
					if (this.userPaused) {
						this.stopAutoplay();
					} else {
						this.startAutoplay();
					}
					this.updatePauseToggle();
				});

				this.updatePauseToggle();
			},
			/** Sincroniza icono/aria-label/aria-pressed del botón de pausa con this.userPaused. */
			updatePauseToggle() {
				if (!this.pauseBtn) return;
				const i18n = CE.i18n || {};
				const label = this.userPaused
					? (i18n.resumeSlider || 'Reanudar')
					: (i18n.pauseSlider || 'Pausar');
				this.pauseBtn.setAttribute('aria-label', `${config.pauseLabel || ''} ${label}`.trim());
				this.pauseBtn.setAttribute('aria-pressed', String(this.userPaused));
				this.pauseBtn.innerHTML = this.userPaused
					? '<i class="fa-solid fa-play" aria-hidden="true"></i>'
					: '<i class="fa-solid fa-pause" aria-hidden="true"></i>';
			},
			bindArrows() {
				const prev = config.prevSelector ? $(config.prevSelector, this.root.parentElement) : null;
				const next = config.nextSelector ? $(config.nextSelector, this.root.parentElement) : null;
				on(prev, 'click', () => this.goTo(this.current - 1));
				on(next, 'click', () => this.goTo(this.current + 1));
			},
			bindSwipe() {
				if (config.swipe === false) return;
				let startX = 0;
				on(this.track, 'touchstart', (e) => { startX = e.touches[0].clientX; }, { passive: true });
				on(this.track, 'touchend', (e) => {
					const diff = e.changedTouches[0].clientX - startX;
					if (Math.abs(diff) > 40) {
						this.goTo(this.current + (diff < 0 ? 1 : -1));
					}
				}, { passive: true });
			},
			goTo(index) {
				const total = this.slides.length;
				this.current = (index + total) % total;
				this.track.style.transform = `translateX(-${this.current * 100}%)`;
				if (this.dots) {
					this.dots.forEach((d, i) => d.classList.toggle('is-active', i === this.current));
				}
			},
			startAutoplay() {
				this.stopAutoplay();
				this.timer = setInterval(() => this.goTo(this.current + 1), this.autoplayDelay);
			},
			stopAutoplay() {
				if (this.timer) clearInterval(this.timer);
			},
		};
	}

	/* ============================================================
	 * MÓDULO: SLIDER DE TESTIMONIOS
	 * Slider automático con soporte de swipe, flechas y dots.
	 * 🆕 Sprint UX-4, Entregable UX-4.2: refactorizado para usar
	 * createSliderController() (arriba) — mismos selectores, mismo
	 * dotLabel, mismo defaultDelay (6000ms) y mismas opciones
	 * (swipe/pauseOnHover no se tocan, sus valores por defecto ya
	 * reproducen el comportamiento anterior a este Entregable
	 * byte a byte). Ver DECISIONS.md D-055.
	 * ============================================================ */
	const ModuleTestimonialSlider = createSliderController({
		rootSelector: '.ce-testimonial-slider',
		trackSelector: '.ce-testimonial-track',
		slideSelector: '.ce-testimonial-slide',
		navSelector: '.ce-slider-nav',
		prevSelector: '.ce-slider-arrow--prev',
		nextSelector: '.ce-slider-arrow--next',
		dotLabel: 'Testimonio',
		defaultDelay: 6000,
		// 🆕 QA-035 (Sprint 8, Entregable 8.5): mecanismo de pausa
		// accesible por teclado/touch (WCAG 2.2.2) — ver
		// buildPauseToggle()/createSliderController() arriba. No se
		// activa en ModuleHeroSlider (abajo): ese slider ya es puramente
		// decorativo, sin dots ni flechas, sin pauseOnHover, y esa
		// ausencia de controles fue una decisión de diseño explícita y
		// ya aprobada (D-055) que QA-035 no menciona ni pide revisar.
		pausable: true,
		pauseLabel: 'Testimonios:',
	});

	/* ============================================================
	 * MÓDULO: SLIDER DE HERO (modo "slider" del Hero configurable)
	 * 🆕 Sprint UX-4, Entregable UX-4.2. Reutiliza la misma fábrica
	 * que ModuleTestimonialSlider (arriba) — cero lógica de slide
	 * duplicada. Fondo decorativo/de ambiente: sin dots ni flechas
	 * (el brief solo exige "funcional con autoplay", ver
	 * docs/UX_CONVERSION_ANALISIS_Y_PLAN.md §5), sin pausa por hover
	 * y sin swipe táctil (evita interferir con el scroll normal de
	 * la página al deslizar sobre el Hero en móvil) — ver
	 * DECISIONS.md D-055 para el detalle de estas 3 decisiones.
	 * Auto-inicializable: si el marcado `.ce-hero-slider` no existe
	 * en la página (Hero en modo imagen/video, o sin ninguna imagen
	 * configurada), `init()` retorna de inmediato sin efecto, igual
	 * que el resto de módulos de este archivo.
	 * ============================================================ */
	const ModuleHeroSlider = createSliderController({
		rootSelector: '.ce-hero-slider',
		trackSelector: '.ce-hero-slider__track',
		slideSelector: '.ce-hero-slider__slide',
		defaultDelay: 6000,
		swipe: false,
		pauseOnHover: false,
	});

	/* ============================================================
	 * MÓDULO: LIGHTBOX (GALERÍA DE IMÁGENES + VIDEO DE TESTIMONIOS)
	 *
	 * Sprint UX-7, Entregable UX-7.8 (D-077): módulo existente
	 * extendido para representar, además de la imagen de siempre
	 * (`.ce-gallery-item[data-full]`, sin cambios de comportamiento
	 * ni de markup interno para este caso), dos tipos nuevos de medio:
	 * video local (`<video controls>` nativo) y video externo
	 * embebido vía oEmbed (`[data-lightbox-video][data-lightbox-type]`,
	 * ver template-parts/content-testimonio-card.php). NO es un
	 * lightbox nuevo ni un segundo overlay: es el mismo `ModuleLightbox`
	 * de siempre, con `show()` ahora condicional según
	 * `dataset.lightboxType` en vez de asumir siempre imagen.
	 *
	 * Navegación prev/next: se calcula sobre un subconjunto — solo los
	 * triggers del MISMO tipo que el que se abrió (`this.groupItems`,
	 * ver open()) — para que una galería de imágenes nunca navegue
	 * hacia un video de testimonio ni viceversa, aunque ambos
	 * existieran en la misma página. Para una página con un único tipo
	 * de trigger (el caso real de gallery.php y de
	 * testimonials-full.php hoy) el resultado es idéntico al
	 * comportamiento anterior: `groupItems` termina siendo la lista
	 * completa de ese tipo.
	 * ============================================================ */
	const ModuleLightbox = {
		init() {
			// Selector combinado: imágenes de galería (sin cambios,
			// mismo atributo `data-full` de siempre) + triggers de
			// video de testimonio (`data-lightbox-video`, nuevos en
			// UX-7.8). Si una página no tiene ninguno de los dos, el
			// módulo no hace nada, igual que antes.
			this.items = $$('.ce-gallery-item[data-full], [data-lightbox-video]');
			if (!this.items.length) return;

			this.buildMarkup();
			this.groupItems  = this.items;
			this.groupIndex  = 0;

			this.items.forEach((item) => {
				on(item, 'click', () => this.open(item));
			});

			on(this.closeBtn, 'click', () => this.close());
			on(this.overlay, 'click', (e) => {
				if (e.target === this.overlay) this.close();
			});
			on(this.prevBtn, 'click', () => this.show(this.groupIndex - 1));
			on(this.nextBtn, 'click', () => this.show(this.groupIndex + 1));
			on(document, 'keydown', (e) => {
				if (!this.overlay.classList.contains('is-open')) return;
				if (e.key === 'Escape') this.close();
				if (e.key === 'ArrowRight') this.show(this.groupIndex + 1);
				if (e.key === 'ArrowLeft') this.show(this.groupIndex - 1);
			});
		},
		buildMarkup() {
			this.overlay = document.createElement('div');
			this.overlay.className = 'ce-lightbox';
			// Nota: <img>, <video> y el contenedor de embed quedan como
			// hijos DIRECTOS del overlay (mismo nivel que en la versión
			// anterior de este módulo, sin ningún <div> envolvente
			// nuevo) para que el centrado/tamaño flex existente de
			// `.ce-lightbox` no cambie para el caso de imagen — solo se
			// añaden dos elementos nuevos, ocultos por defecto
			// (`hidden`), que conviven con `.ce-lightbox__img` de siempre.
			this.overlay.innerHTML = `
				<button class="ce-lightbox__close" aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button>
				<button class="ce-lightbox__nav ce-lightbox__nav--prev" aria-label="Anterior"><i class="fa-solid fa-chevron-left"></i></button>
				<img class="ce-lightbox__img" src="" alt="">
				<video class="ce-lightbox__video" controls playsinline hidden></video>
				<div class="ce-lightbox__embed" hidden></div>
				<button class="ce-lightbox__nav ce-lightbox__nav--next" aria-label="Siguiente"><i class="fa-solid fa-chevron-right"></i></button>
			`;
			document.body.appendChild(this.overlay);
			this.closeBtn = $('.ce-lightbox__close', this.overlay);
			this.prevBtn  = $('.ce-lightbox__nav--prev', this.overlay);
			this.nextBtn  = $('.ce-lightbox__nav--next', this.overlay);
			this.imgEl    = $('.ce-lightbox__img', this.overlay);
			this.videoEl  = $('.ce-lightbox__video', this.overlay);
			this.embedEl  = $('.ce-lightbox__embed', this.overlay);
		},
		open(item) {
			// Grupo de navegación = mismo tipo que el item que abrió
			// (ver comentario del módulo). Tipo por defecto 'image'
			// preserva el comportamiento de gallery.php (sin
			// `data-lightbox-type`, todos caen en 'image').
			const type = item.dataset.lightboxType || 'image';
			this.groupItems = this.items.filter((i) => (i.dataset.lightboxType || 'image') === type);
			this.groupIndex = this.groupItems.indexOf(item);
			if (this.groupIndex === -1) this.groupIndex = 0;

			const multiple = this.groupItems.length > 1;
			this.prevBtn.style.display = multiple ? '' : 'none';
			this.nextBtn.style.display = multiple ? '' : 'none';

			this.show(this.groupIndex);
			this.overlay.classList.add('is-open');
			document.body.classList.add('ce-no-scroll');

			// QA-036 (Sprint 8, Entregable 8.6): antes, este módulo ya
			// movía el foco al botón de cerrar al abrir y lo devolvía al
			// disparador al cerrar (ver `this.lastTrigger` en versiones
			// previas) — pero sin ningún trap de `Tab` dentro del
			// overlay. Se migra ambos comportamientos a la utilidad
			// compartida `FocusTrap` (arriba en este archivo), que ahora
			// también añade el trap que faltaba, en vez de mantener una
			// segunda implementación manual del mismo patrón que
			// `ModuleMobileNav`/`ModuleModals` ya usan (R-4: gestión de
			// foco centralizada).
			FocusTrap.activate(this.overlay, { trigger: item, initialFocus: this.closeBtn });
		},
		show(index) {
			const total = this.groupItems.length;
			this.groupIndex = (index + total) % total;
			const item = this.groupItems[this.groupIndex];
			const type = item.dataset.lightboxType || 'image';

			// Detiene/limpia cualquier medio de la vista anterior antes
			// de mostrar el siguiente (evita que un video siga sonando
			// de fondo al navegar entre items, o al reabrir).
			this.stopMedia();

			this.imgEl.hidden   = true;
			this.videoEl.hidden = true;
			this.embedEl.hidden = true;

			if (type === 'video-local') {
				this.videoEl.hidden = false;
				this.videoEl.src = item.dataset.videoSrc || '';
				if (item.dataset.caption) {
					this.videoEl.setAttribute('aria-label', item.dataset.caption);
				}
			} else if (type === 'video-embed') {
				this.embedEl.hidden = false;
				const target = item.dataset.embedTarget ? document.getElementById(item.dataset.embedTarget) : null;
				// El <template> ya trae el marcado que wp_oembed_get()
				// generó en el servidor (ver content-testimonio-card.php);
				// aquí solo se copia su innerHTML al contenedor visible,
				// no se construye ni se solicita ningún embed nuevo desde
				// el cliente.
				if (target) {
					this.embedEl.innerHTML = target.innerHTML;
				}
			} else {
				this.imgEl.hidden = false;
				this.imgEl.src = item.dataset.full || '';
				this.imgEl.alt = item.dataset.caption || '';
			}
		},
		stopMedia() {
			if (this.videoEl) {
				this.videoEl.pause();
				this.videoEl.removeAttribute('src');
				this.videoEl.load();
			}
			if (this.embedEl) {
				// Vacía el embed externo (p. ej. un iframe de YouTube)
				// para que un video externo tampoco continúe
				// reproduciéndose en segundo plano tras navegar o cerrar.
				this.embedEl.innerHTML = '';
			}
		},
		close() {
			this.overlay.classList.remove('is-open');
			document.body.classList.remove('ce-no-scroll');
			this.stopMedia();
			// QA-036: FocusTrap.deactivate() ya devuelve el foco al
			// disparador (el `trigger` pasado en open(), arriba) — punto
			// 10 del alcance de UX-7.8 (gestión de foco al cerrar), ahora
			// resuelto por la utilidad compartida en vez de la lógica
			// manual anterior de este mismo método.
			FocusTrap.deactivate(this.overlay);
		},
	};

	/* ============================================================
	 * MÓDULO: MODALES (éxito / error / genérico)
	 * ============================================================ */
	const ModuleModals = {
		init() {
			this.overlays = $$('.ce-modal-overlay');
			if (!this.overlays.length) return;

			this.overlays.forEach((overlay) => {
				const closeBtns = $$('.ce-modal__close', overlay);
				closeBtns.forEach((btn) => on(btn, 'click', () => this.close(overlay)));
				on(overlay, 'click', (e) => {
					if (e.target === overlay) this.close(overlay);
				});
			});

			on(document, 'keydown', (e) => {
				if (e.key !== 'Escape') return;
				this.overlays.forEach((overlay) => {
					if (overlay.classList.contains('is-open')) this.close(overlay);
				});
			});
		},
		open(id, options = {}) {
			const overlay = document.getElementById(id);
			if (!overlay) return;
			overlay.classList.add('is-open');
			document.body.classList.add('ce-no-scroll');

			// QA-036: trap de foco dentro del modal. `options.trigger`
			// permite a quien llama indicar explícitamente el elemento
			// que originó la apertura (más fiable que
			// document.activeElement: un clic de mouse sobre un <a> no
			// siempre deja ese enlace como activeElement, según el
			// navegador) — ver los 4 call sites de ModuleModals.open()
			// más abajo en este archivo. Sin `options.trigger`
			// (ModuleOfferPopup, que abre por temporizador sin ningún
			// disparador de usuario), FocusTrap usa su propio fallback a
			// document.activeElement.
			FocusTrap.activate(overlay, options.trigger !== undefined ? { trigger: options.trigger } : {});
		},
		close(overlay) {
			overlay.classList.remove('is-open');
			document.body.classList.remove('ce-no-scroll');

			FocusTrap.deactivate(overlay);
		},
	};

	/* ============================================================
	 * MÓDULO: FORMULARIO DE COTIZACIÓN
	 * Validación en cliente (UX) + envío AJAX con Nonce.
	 * La validación real y autoritativa vive en el servidor
	 * (inc/quote-form.php); esto solo mejora la experiencia
	 * y reduce solicitudes inválidas.
	 * ============================================================ */
	const ModuleQuoteForm = {
		// 🆕 Sprint UX-3, Entregable UX-3.2 (D-053): puede existir más
		// de una instancia del formulario de cotización en la misma
		// página al mismo tiempo — la integrada (id="ce-quote-form") y
		// la del modal (id="ce-quote-form-modal" cuando hay colisión,
		// ver template-parts/quote-form.php). Antes este módulo asumía
		// un único formulario fijo por ID (#ce-quote-form); ahora
		// localiza TODAS las instancias por la clase marcadora
		// `.ce-quote-form-instance` y crea un controlador independiente
		// por cada una, sin asumir cuántas hay ni sus IDs concretos.
		// Mismo flujo AJAX/nonce/validación de siempre, sin cambios de
		// comportamiento para páginas con una sola instancia (el caso
		// más común) — solo cambia cómo se localizan.
		init() {
			$$('.ce-quote-form-instance').forEach((formEl) => this.createInstance(formEl));
		},
		createInstance(formEl) {
			const instance = {
				form: formEl,
				submitBtn: $('button[type="submit"]', formEl),
				statusEl: $('.ce-form-status', formEl),
				fileInput: $('input[type="file"]', formEl),
				fileZone: $('.ce-field--file', formEl),
				// QA-044 (Sprint 8, corrección puntual — hallazgo reportado
				// tras el Entregable 8.4): texto original de la etiqueta del
				// campo de archivo, capturado una sola vez al crear la
				// instancia. form.reset() sí vacía el <input type="file">
				// (los navegadores nunca permiten lo contrario por
				// seguridad), pero NO dispara su evento 'change' — así que
				// el <span> de la etiqueta, que solo se actualiza en ese
				// evento, se quedaba mostrando el último nombre de archivo
				// aunque el campo real ya estuviera vacío. Se restaura este
				// texto en updateFileLabel() cuando no hay archivo seleccionado.
				fileLabelDefaultText: null,

				// Sprint UX-3, Entregable UX-3.2: cuando el formulario se
				// reutiliza dentro de #ce-quote-modal (template-parts/quote-form.php
				// con $args['context']='modal'), .ce-modal__close cierra el
				// overlay que lo contiene, pero al mostrar el modal de éxito/error
				// (handleSubmit) ese overlay del formulario debe cerrarse primero
				// — si no, quedarían dos .ce-modal-overlay.is-open superpuestos.
				// Reutiliza ModuleModals.close(), ya existente; no se crea
				// ningún mecanismo nuevo — ver DECISIONS.md D-051/D-053.
				parentModalOverlay: formEl.closest('.ce-modal-overlay'),

				rules: {
					name:    (v) => v.trim().length >= 2 || 'Ingresa un nombre válido.',
					email:   (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) || 'Ingresa un correo válido.',
					phone:   (v) => /^[0-9+\-\s()]{7,20}$/.test(v) || 'Ingresa un teléfono válido.',
					service: (v) => v.trim().length > 0 || 'Selecciona el servicio requerido.',
					message: (v) => v.trim().length >= 10 || 'Cuéntanos un poco más (mínimo 10 caracteres).',
				},

				bindLiveValidation() {
					Object.keys(this.rules).forEach((name) => {
						const field = this.form.elements[name];
						if (!field) return;
						on(field, 'blur', () => this.validateField(name));
					});
				},
				validateField(name) {
					const field = this.form.elements[name];
					const wrapper = field.closest('.ce-field');
					const result = this.rules[name](field.value);

					if (result === true) {
						wrapper.classList.remove('is-invalid');
						wrapper.classList.add('is-valid');
						return true;
					}

					wrapper.classList.add('is-invalid');
					wrapper.classList.remove('is-valid');
					const errorEl = $('.ce-field__error', wrapper);
					if (errorEl) errorEl.textContent = result;
					return false;
				},
				validateAll() {
					return Object.keys(this.rules)
						.map((name) => this.validateField(name))
						.every(Boolean);
				},
				bindFileZone() {
					if (!this.fileZone || !this.fileInput) return;

					const defaultLabel = $('.ce-field--file__label', this.fileZone);
					if (defaultLabel) {
						this.fileLabelDefaultText = defaultLabel.textContent;
					}

					on(this.fileZone, 'click', () => this.fileInput.click());
					on(this.fileInput, 'change', () => this.updateFileLabel());

					['dragover', 'dragenter'].forEach((evName) => {
						on(this.fileZone, evName, (e) => {
							e.preventDefault();
							this.fileZone.classList.add('is-dragover');
						});
					});
					['dragleave', 'drop'].forEach((evName) => {
						on(this.fileZone, evName, (e) => {
							e.preventDefault();
							this.fileZone.classList.remove('is-dragover');
						});
					});
					on(this.fileZone, 'drop', (e) => {
						const file = e.dataTransfer.files[0];
						if (file) {
							this.fileInput.files = e.dataTransfer.files;
							this.updateFileLabel();
						}
					});
				},
				updateFileLabel() {
					const label = $('.ce-field--file__label', this.fileZone);
					if (!label) return;
					const file = this.fileInput.files[0];
					if (file) {
						label.textContent = file.name;
					} else if (this.fileLabelDefaultText !== null) {
						// QA-044: sin archivo seleccionado (incluido el caso de
						// un form.reset() que vació el input sin disparar su
						// evento 'change') — se restaura el texto original en
						// vez de dejar el último nombre de archivo mostrado.
						label.textContent = this.fileLabelDefaultText;
					}
				},
				setLoading(isLoading) {
					this.form.classList.toggle('is-loading', isLoading);
					if (this.submitBtn) this.submitBtn.disabled = isLoading;
				},
				showStatus(message, type) {
					if (!this.statusEl) return;
					this.statusEl.textContent = message;
					this.statusEl.classList.remove('ce-form-status--success', 'ce-form-status--error');
					this.statusEl.classList.add('is-visible', `ce-form-status--${type}`);
				},
				async handleSubmit(e) {
					e.preventDefault();

					if (!this.validateAll()) {
						this.showStatus('Revisa los campos marcados en rojo.', 'error');
						return;
					}

					this.setLoading(true);

					const formData = new FormData(this.form);
					formData.append('action', 'ce_submit_quote');
					formData.append('ce_quote_nonce', CE.quoteNonce);

					try {
						const response = await fetch(CE.ajaxUrl, {
							method: 'POST',
							credentials: 'same-origin',
							body: formData,
						});
						const data = await response.json();

						if (data.success) {
							this.showStatus(data.data.message, 'success');
							this.form.reset();
							// QA-044: form.reset() vacía el <input type="file"> pero
							// no dispara su evento 'change', así que la etiqueta
							// visual no se actualizaba sola — se sincroniza aquí
							// explícitamente, ahora que this.fileInput.files ya
							// está vacío tras el reset.
							this.updateFileLabel();
							$$('.ce-field', this.form).forEach((f) => f.classList.remove('is-valid', 'is-invalid'));
							if (this.parentModalOverlay) ModuleModals.close(this.parentModalOverlay);
							// QA-036: this.submitBtn como trigger explícito —
							// al cerrar el modal de éxito, el foco vuelve al
							// botón de envío, no a document.activeElement
							// (que en este punto ya podría ser otra cosa,
							// tras el reset del formulario).
							ModuleModals.open('ce-modal-success', { trigger: this.submitBtn });
							// 🆕 Sprint UX-7, Entregable UX-7.10 (D-079): evento
							// desacoplado de "envío exitoso", sin tocar la lógica
							// de validación/AJAX/nonce de este módulo. Lo escucha
							// ModuleOfferPopup para saber si ESTE envío en
							// particular corresponde a una conversión originada
							// por su propio CTA (ver ModuleOfferPopup.pendingConversion
							// más abajo) — cualquier otro envío del Formulario de
							// Cotización en el sitio dispara el mismo evento sin
							// ningún efecto adicional si nadie lo escucha.
							document.dispatchEvent(new CustomEvent('ce:quoteFormSuccess'));
						} else {
							const msg = (data.data && data.data.message) || CE.i18n.error;
							this.showStatus(msg, 'error');
							if (this.parentModalOverlay) ModuleModals.close(this.parentModalOverlay);
							ModuleModals.open('ce-modal-error', { trigger: this.submitBtn }); // QA-036

							if (data.data && data.data.fields) {
								Object.entries(data.data.fields).forEach(([name, msgField]) => {
									const field = this.form.elements[name];
									if (!field) return;
									const wrapper = field.closest('.ce-field');
									wrapper.classList.add('is-invalid');
									const errorEl = $('.ce-field__error', wrapper);
									if (errorEl) errorEl.textContent = msgField;
								});
							}
						}
					} catch (err) {
						this.showStatus(CE.i18n.error, 'error');
						if (this.parentModalOverlay) ModuleModals.close(this.parentModalOverlay);
						ModuleModals.open('ce-modal-error', { trigger: this.submitBtn }); // QA-036
					} finally {
						this.setLoading(false);
					}
				},
			};

			instance.bindLiveValidation();
			instance.bindFileZone();
			on(instance.form, 'submit', (e) => instance.handleSubmit(e));

			return instance;
		},
	};

	/* ============================================================
	 * MÓDULO: EXPANSIÓN PROGRESIVA DEL FORMULARIO DEL HERO
	 * Ajuste puntual dentro de UX-11 (ver DECISIONS.md D-091, D-093).
	 *
	 * Colapsa, ÚNICAMENTE vía JS, la parte final del formulario de
	 * cotización embebido en el Hero (Mensaje, Adjuntar archivo,
	 * botón de envío — wrapper `.ce-hero-quote-form__extra`, ver
	 * template-parts/quote-form.php, impreso solo en el contexto
	 * 'hero'). Se expande al entrar al formulario (foco en cualquier
	 * campo, visible o no) y vuelve a compactarse al salir por
	 * completo de él, sin haber enviado nada (D-093) — puede
	 * expandirse y compactarse tantas veces como el usuario entre y
	 * salga del formulario.
	 *
	 * Progressive enhancement OBLIGATORIO: si este módulo no llega a
	 * ejecutarse (JS deshabilitado, error, script no cargado), el
	 * wrapper nunca recibe ningún `max-height` ni clase que lo oculte
	 * — el HTML/CSS de origen ya lo muestran completo por defecto
	 * (ver assets/css/main.css, sección 28 bis, punto 6). Este módulo
	 * solo AÑADE el colapso cuando confirma que puede ejecutarse; en
	 * ningún punto el formulario depende de JS para mostrar sus
	 * campos obligatorios.
	 *
	 * Módulo independiente de ModuleQuoteForm, no una extensión
	 * suya: el comportamiento de mostrar/ocultar esta sección es
	 * puramente visual y ortogonal al envío/validación del
	 * formulario (mismo criterio de desacople que ya usa
	 * ModuleOfferPopup frente a ModuleQuoteForm — ver D-079). Mezclar
	 * ambas responsabilidades en un único módulo habría acoplado la
	 * lógica de envío AJAX/nonce (crítica, compartida por las 3
	 * instancias) con un detalle de presentación exclusivo de una
	 * sola de ellas; con módulos separados, un fallo o cambio futuro
	 * en la expansión progresiva no puede romper el envío del
	 * formulario en ningún contexto, ni viceversa.
	 * ============================================================ */
	const ModuleHeroFormProgressive = {
		init() {
			$$('.ce-hero-quote-card .ce-hero-quote-form__extra').forEach((extra) => this.createInstance(extra));
		},
		createInstance(extra) {
			const form = extra.closest('form');
			if (!form) return;

			const isCollapsed = () => extra.classList.contains('is-collapsed');

			// 🆕 D-093: colapso INICIAL sin animación — evita un
			// "parpadeo" visible de colapso justo después de cargar la
			// página (con la transición de la sección 28 bis activa, fijar
			// max-height a 0 directamente SÍ se animaría). Se desactiva la
			// transición en línea por un instante, se colapsa, se fuerza
			// reflow, y solo entonces se restaura la transición normal del
			// CSS para que las expansiones/colapsos posteriores —
			// disparados por el usuario al entrar/salir del formulario—
			// sí se animen.
			extra.style.transition = 'none';
			extra.classList.add('is-collapsed');
			extra.style.maxHeight = '0px';
			void extra.offsetHeight; // fuerza reflow
			extra.style.transition = '';

			const expand = () => {
				if (!isCollapsed()) return;
				extra.classList.remove('is-collapsed');
				extra.style.maxHeight = extra.scrollHeight + 'px';
			};

			// 🆕 D-093: recompacta el wrapper (mismo mecanismo que el
			// colapso inicial, pero SÍ animado: aquí no se toca
			// `transition`, así que corre la definida en el CSS). Antes de
			// colapsar se fija el alto real actual en píxeles como punto
			// de partida — necesario porque, tras una expansión completa
			// (ver `transitionend` más abajo), `max-height` queda en
			// `none`, y una transición nunca anima correctamente desde
			// `none`.
			const collapse = () => {
				if (isCollapsed()) return;
				extra.style.maxHeight = extra.scrollHeight + 'px';
				void extra.offsetHeight; // fuerza reflow
				extra.classList.add('is-collapsed');
				extra.style.maxHeight = '0px';
			};

			on(extra, 'transitionend', (e) => {
				if (e.target !== extra || e.propertyName !== 'max-height') return;
				// Solo al terminar de EXPANDIR se libera el `max-height`
				// fijo (a `none`), para que el wrapper fluya con
				// normalidad mientras está abierto — por ejemplo, si un
				// mensaje de error de validación (ModuleQuoteForm)
				// aumenta su altura después, o si cambia el tamaño de la
				// ventana. Al terminar de COLAPSAR no hace falta liberar
				// nada: `0px` ya es el estado de reposo correcto.
				if (!isCollapsed()) {
					extra.style.maxHeight = 'none';
				}
			});

			// `focus`/`blur` no burbujean; `focusin`/`focusout` sí — un
			// único par de listeners en el propio `<form>` cubre
			// cualquier campo (visible o, si el usuario llega a él
			// tabulando mientras está colapsado, dentro del propio
			// wrapper) sin tener que enumerarlos ni duplicar esa lista
			// frente al markup de template-parts/quote-form.php.
			on(form, 'focusin', expand);

			// 🆕 D-093: "salir del formulario" se resuelve con un
			// `setTimeout` de 0ms en `focusout`, no con el `relatedTarget`
			// del propio evento — `relatedTarget` no es fiable en todos
			// los navegadores (p. ej. al perder el foco por un clic fuera
			// de cualquier elemento enfocable, o al cambiar de pestaña).
			// Esperar un ciclo permite leer `document.activeElement` ya
			// actualizado: si en ese momento sigue siendo un elemento
			// DENTRO de este mismo `<form>` (el usuario solo se movió de
			// un campo a otro del formulario), no se compacta; si quedó
			// fuera (o no quedó ninguno, p. ej. Escape/clic en el fondo),
			// se compacta.
			on(form, 'focusout', () => {
				setTimeout(() => {
					if (form.contains(document.activeElement)) return;
					collapse();
				}, 0);
			});
		},
	};

	/* ============================================================
	 * MÓDULO: LAZY LOADING (fallback para navegadores sin soporte
	 * nativo de loading="lazy", y animación de entrada al viewport)
	 * ============================================================ */
	const ModuleLazyLoading = {
		init() {
			this.images = $$('img[data-src]');
			if ('loading' in HTMLImageElement.prototype) {
				// Soporte nativo: solo movemos data-src -> src.
				this.images.forEach((img) => {
					img.src = img.dataset.src;
					img.removeAttribute('data-src');
				});
			} else if ('IntersectionObserver' in window) {
				const observer = new IntersectionObserver((entries) => {
					entries.forEach((entry) => {
						if (!entry.isIntersecting) return;
						const img = entry.target;
						img.src = img.dataset.src;
						img.removeAttribute('data-src');
						observer.unobserve(img);
					});
				});
				this.images.forEach((img) => observer.observe(img));
			} else {
				this.images.forEach((img) => { img.src = img.dataset.src; });
			}
		},
	};

	/* ============================================================
	 * MÓDULO: ANIMACIONES AL HACER SCROLL (reveal on scroll)
	 * ============================================================ */
	const ModuleScrollReveal = {
		init() {
			this.elements = $$('.ce-animate-on-scroll');
			if (!this.elements.length) return;

			if (!('IntersectionObserver' in window)) {
				this.elements.forEach((el) => el.classList.add('is-in-view'));
				return;
			}

			const observer = new IntersectionObserver((entries) => {
				entries.forEach((entry) => {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-in-view');
						observer.unobserve(entry.target);
					}
				});
			}, { threshold: 0.15 });

			this.elements.forEach((el) => observer.observe(el));
		},
	};

	/* ============================================================
	 * MÓDULO: ACCORDION (usado por FAQ relacionadas — Sprint 3)
	 * Añadido en el módulo Servicios. No modifica ningún módulo
	 * anterior; se limita a agregarse a la lista de inicialización
	 * en el bootstrap final de este archivo.
	 * ============================================================ */
	const ModuleAccordion = {
		init() {
			this.items = $$('.ce-accordion__item');
			if (!this.items.length) return;

			this.items.forEach((item) => {
				const question = $('.ce-accordion__question', item);
				const answer = $('.ce-accordion__answer', item);
				if (!question || !answer) return;

				on(question, 'click', () => this.toggle(item, answer));
			});
		},
		toggle(item, answer) {
			const isOpen = item.classList.contains('is-open');
			const question = $('.ce-accordion__question', item);

			// Cierra los demás items del mismo acordeón (comportamiento tipo "single open").
			const parent = item.closest('.ce-accordion');
			if (parent) {
				$$('.ce-accordion__item.is-open', parent).forEach((openItem) => {
					if (openItem !== item) {
						openItem.classList.remove('is-open');
						$('.ce-accordion__answer', openItem).style.maxHeight = null;
						$('.ce-accordion__question', openItem).setAttribute('aria-expanded', 'false');
					}
				});
			}

			if (isOpen) {
				item.classList.remove('is-open');
				answer.style.maxHeight = null;
				question.setAttribute('aria-expanded', 'false');
			} else {
				item.classList.add('is-open');
				answer.style.maxHeight = `${answer.scrollHeight}px`;
				question.setAttribute('aria-expanded', 'true');
			}
		},
	};

	/* ============================================================
	 * MÓDULO: POPUP DE OFERTA (Sprint UX-7, Entregable UX-7.10, D-079/D-081)
	 *
	 * Componente independiente del Formulario de Cotización: reutiliza
	 * `ModuleModals.open()`/`close()` tal cual para la mecánica de
	 * apertura/cierre (mismo overlay `.ce-modal-overlay`, así que el
	 * botón de cerrar, el clic en el fondo y la tecla Escape ya
	 * funcionan gratis vía `ModuleModals.init()` — no se reimplementa
	 * nada de eso aquí). Este módulo solo añade:
	 *   1) el temporizador de aparición (retraso configurable);
	 *   2) las 2 cookies de supresión (cierre/clic-sin-conversión, y
	 *      conversión exitosa) y su comprobación al cargar la página;
	 *   3) cerrar el popup ANTES de que un clic en su CTA abra el
	 *      modal de Cotización (nunca deben quedar 2 overlays
	 *      abiertos a la vez — mismo criterio ya usado por
	 *      `parentModalOverlay` en ModuleQuoteForm);
	 *   4) escuchar `ce:quoteFormSuccess` (evento nuevo, ver el envío
	 *      exitoso dentro de ModuleQuoteForm más arriba) para saber si
	 *      la conversión pertenece a este popup;
	 *   5) (D-081) el efecto de movimiento de `show()`: rebote de
	 *      entrada del modal + 2 nudges acotados del icono — ambos
	 *      disparados una única vez por apertura, nunca en bucle, y
	 *      omitidos por completo si `prefers-reduced-motion: reduce`
	 *      está activo.
	 * ============================================================ */
	const ModuleOfferPopup = {
		init() {
			this.overlay = document.getElementById('ce-offer-popup');
			// Sin marcado en la página: el popup está desactivado o mal
			// configurado (ce_get_offer_popup_data() no imprimió nada,
			// ver template-parts/offer-popup.php) — nada que hacer.
			if (!this.overlay) return;

			// Ya convertido o ya cerrado dentro de la ventana de
			// supresión vigente: no se arma el temporizador. Se
			// comprueba primero la conversión y luego el cierre simple,
			// tal como se especificó (D-079).
			if (this.getCookie('ce_offer_popup_converted') !== null) return;
			if (this.getCookie('ce_offer_popup_dismissed') !== null) return;

			this.delaySeconds   = parseInt(this.overlay.dataset.delay, 10) || 6;
			this.dismissMinutes = parseInt(this.overlay.dataset.dismissMinutes, 10) || 1440;
			this.convertMinutes = parseInt(this.overlay.dataset.convertMinutes, 10) || 10080;
			this.pendingConversion = false;
			this.shown = false;

			this.ctaLink = $('#ce-offer-popup-cta', this.overlay);
			on(this.ctaLink, 'click', () => this.handleCtaClick());

			// Detecta cualquier cierre (X, clic en el fondo, Escape —
			// los 3 ya resueltos por ModuleModals) observando la propia
			// clase del overlay, en vez de reimplementar esos 3
			// mecanismos de cierre en este módulo.
			this.observer = new MutationObserver(() => {
				if (this.shown && !this.overlay.classList.contains('is-open')) {
					this.onClosed();
				}
			});
			this.observer.observe(this.overlay, { attributes: true, attributeFilter: ['class'] });

			on(document, 'ce:quoteFormSuccess', () => {
				if (this.pendingConversion) {
					this.setCookie('ce_offer_popup_converted', '1', this.convertMinutes);
					this.pendingConversion = false;
				}
			});

			this.timer = window.setTimeout(() => this.show(), this.delaySeconds * 1000);
		},
		show() {
			this.shown = true;
			ModuleModals.open('ce-offer-popup');

			// 🆕 Sprint UX-7, Entregable UX-7.10 (D-081): efecto de
			// movimiento vía JS, ligado al instante exacto de apertura
			// — no un bucle infinito adicional (para no sobrecargar,
			// ver D-081). Se omite por completo si el visitante tiene
			// activada la preferencia de sistema "reducir movimiento".
			const prefersReducedMotion = window.matchMedia
				&& window.matchMedia('(prefers-reduced-motion: reduce)').matches;
			if (prefersReducedMotion) return;

			// Rebote de entrada del modal completo (una sola vez, ~0.6s,
			// ver @keyframes ce-offer-modal-bounce-in en main.css). La
			// clase se retira sola; no depende de que nada más la limpie.
			this.overlay.classList.add('ce-offer-popup--bounce-in');
			window.setTimeout(() => this.overlay.classList.remove('ce-offer-popup--bounce-in'), 700);

			// 2 "nudges" (meneo breve) del icono, acotados en el tiempo
			// — no infinitos — para reforzar la llamada de atención sin
			// sumar un tercer movimiento continuo junto al pulso ya
			// existente del botón CTA.
			const icon = $('.ce-offer-popup__icon', this.overlay);
			if (icon) {
				[4000, 9000].forEach((delay) => {
					window.setTimeout(() => {
						// Si ya se cerró mientras tanto, no animar en
						// segundo plano sobre un popup invisible.
						if (!this.overlay.classList.contains('is-open')) return;
						icon.classList.add('is-nudging');
						window.setTimeout(() => icon.classList.remove('is-nudging'), 650);
					}, delay);
				});
			}
		},
		handleCtaClick() {
			// Un clic en el CTA nunca es, por sí mismo, la conversión
			// (respuesta explícita del usuario, D-079): si el destino es
			// el modal de Cotización, la conversión real llega después,
			// vía `ce:quoteFormSuccess`; si el destino es una URL, no
			// existe ninguna señal de conversión fiable disponible en
			// esta página tras la navegación — limitación documentada
			// en D-079, no se fabrica una señal falsa. En ambos casos
			// `onClosed()` (más abajo) ya aplica la supresión corta.
			const href = this.ctaLink.getAttribute('href') || '';
			const opensModal = href.startsWith('#')
				&& document.getElementById(href.slice(1))
				&& document.getElementById(href.slice(1)).classList.contains('ce-modal-overlay');

			if (opensModal) {
				this.pendingConversion = true;
			}

			// Cierra ESTE overlay antes de que el listener delegado de
			// ModuleSmoothScroll (que también reacciona a este mismo
			// clic, ver arriba en el archivo) abra `#ce-quote-modal` —
			// nunca deben coexistir los 2 overlays abiertos.
			ModuleModals.close(this.overlay);
		},
		onClosed() {
			this.shown = false;
			this.setCookie('ce_offer_popup_dismissed', '1', this.dismissMinutes);
			if (this.observer) {
				this.observer.disconnect();
				this.observer = null;
			}
		},
		setCookie(name, value, minutes) {
			const expires = new Date(Date.now() + minutes * 60 * 1000).toUTCString();
			document.cookie = `${name}=${value}; expires=${expires}; path=/; SameSite=Lax`;
		},
		getCookie(name) {
			const match = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
			return match ? decodeURIComponent(match[1]) : null;
		},
	};

	/* ============================================================
	 * BOOTSTRAP: inicializa todos los módulos cuando el DOM
	 * está listo. Cada módulo es responsable de verificar si
	 * su marcado existe antes de operar.
	 * ============================================================ */
	document.addEventListener('DOMContentLoaded', () => {
		ModuleSmoothScroll.init();
		ModuleMobileNav.init();
		ModuleStickyHeader.init();
		ModuleBackToTop.init();
		ModuleWhatsAppFloat.init();
		ModuleCounters.init();
		ModuleTestimonialSlider.init();
		ModuleHeroSlider.init(); // 🆕 Sprint UX-4, Entregable UX-4.2 (ver DECISIONS.md D-055).
		ModuleLightbox.init();
		ModuleModals.init();
		ModuleQuoteForm.init();
		ModuleHeroFormProgressive.init(); // 🆕 Sprint UX-11 (ver DECISIONS.md D-091). Después de ModuleQuoteForm.init() por orden de lectura (sin dependencia real entre ambos: son módulos independientes, ver comentario del módulo).
		ModuleLazyLoading.init();
		ModuleScrollReveal.init();
		ModuleAccordion.init();
		ModuleOfferPopup.init(); // 🆕 Sprint UX-7, Entregable UX-7.10 (ver DECISIONS.md D-079). Después de ModuleModals.init() para que su overlay ya esté registrado (cierre por fondo/Escape).
	});
})();
