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

	const CE = window.ceConstructionData || {
		ajaxUrl: '/wp-admin/admin-ajax.php',
		quoteNonce: '',
		whatsapp: '',
		i18n: { sending: 'Enviando...', error: 'Ocurrió un error. Intenta nuevamente.' },
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
					ModuleModals.open(targetId);
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
		},
		close() {
			this.nav.classList.remove('is-open');
			this.overlay && this.overlay.classList.remove('is-visible');
			this.toggle.classList.remove('is-active');
			this.toggle.setAttribute('aria-expanded', 'false');
			document.body.classList.remove('ce-no-scroll');
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

				this.buildNav();
				this.bindArrows();
				this.bindSwipe();
				this.goTo(0);
				this.startAutoplay();

				if (config.pauseOnHover !== false) {
					on(this.root, 'mouseenter', () => this.stopAutoplay());
					on(this.root, 'mouseleave', () => this.startAutoplay());
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
			this.lastTrigger = null;

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

			this.lastTrigger = item;
			this.show(this.groupIndex);
			this.overlay.classList.add('is-open');
			document.body.classList.add('ce-no-scroll');
			this.closeBtn.focus();
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
			// Devuelve el foco al botón/elemento que abrió el lightbox
			// (Play del testimonio, o miniatura de la galería) — punto
			// 10 del alcance de UX-7.8 (gestión de foco al cerrar).
			if (this.lastTrigger && typeof this.lastTrigger.focus === 'function') {
				this.lastTrigger.focus();
			}
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
		open(id) {
			const overlay = document.getElementById(id);
			if (!overlay) return;
			overlay.classList.add('is-open');
			document.body.classList.add('ce-no-scroll');
		},
		close(overlay) {
			overlay.classList.remove('is-open');
			document.body.classList.remove('ce-no-scroll');
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
					const file = this.fileInput.files[0];
					if (label && file) {
						label.textContent = file.name;
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
							$$('.ce-field', this.form).forEach((f) => f.classList.remove('is-valid', 'is-invalid'));
							if (this.parentModalOverlay) ModuleModals.close(this.parentModalOverlay);
							ModuleModals.open('ce-modal-success');
						} else {
							const msg = (data.data && data.data.message) || CE.i18n.error;
							this.showStatus(msg, 'error');
							if (this.parentModalOverlay) ModuleModals.close(this.parentModalOverlay);
							ModuleModals.open('ce-modal-error');

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
						ModuleModals.open('ce-modal-error');
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
		ModuleLazyLoading.init();
		ModuleScrollReveal.init();
		ModuleAccordion.init();
	});
})();
