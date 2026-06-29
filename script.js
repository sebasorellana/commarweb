// Home nav contrast mode
const homeNav = document.querySelector('#home-nav');
const navContrastHero = document.querySelector('#hero-home, .about-hero, .service-detail-hero, .project-detail-hero, .article-hero');
const heroTypewriter = document.querySelector('[data-hero-typewriter]');
const heroCarousel = document.querySelector('[data-hero-carousel]');

if (heroTypewriter) {
    let words = ['arquitectura', 'pensamiento', 'estilo', 'personalización'];
    try {
        const configuredWords = JSON.parse(heroTypewriter.dataset.heroTypewriterWords || '[]');
        if (Array.isArray(configuredWords) && configuredWords.length > 0) {
            words = configuredWords.map((word) => String(word).trim()).filter(Boolean);
        }
    } catch (error) {
        words = [heroTypewriter.textContent.trim()].filter(Boolean);
    }

    if (words.length === 0) {
        words = [heroTypewriter.textContent.trim()].filter(Boolean);
    }

    const typeDelay = 74;
    const eraseDelay = 38;
    const holdDelay = 1800;
    let wordIndex = 0;
    let charIndex = heroTypewriter.textContent.length;
    let isDeleting = false;

    const tickTypewriter = () => {
        const currentWord = words[wordIndex];
        heroTypewriter.textContent = currentWord.slice(0, charIndex);

        if (!isDeleting && charIndex < currentWord.length) {
            charIndex += 1;
            window.setTimeout(tickTypewriter, typeDelay);
            return;
        }

        if (!isDeleting && charIndex === currentWord.length) {
            isDeleting = true;
            window.setTimeout(tickTypewriter, holdDelay);
            return;
        }

        if (isDeleting && charIndex > 0) {
            charIndex -= 1;
            window.setTimeout(tickTypewriter, eraseDelay);
            return;
        }

        isDeleting = false;
        wordIndex = (wordIndex + 1) % words.length;
        window.setTimeout(tickTypewriter, 260);
    };

    window.setTimeout(tickTypewriter, holdDelay);
}

if (heroCarousel) {
    const slides = Array.from(heroCarousel.querySelectorAll('.hero-reveal-image'));
    const speed = Math.max(1500, Number.parseInt(heroCarousel.dataset.heroCarouselSpeed || '5000', 10) || 5000);
    let activeIndex = slides.findIndex((slide) => slide.classList.contains('is-active'));

    if (slides.length > 1) {
        activeIndex = activeIndex >= 0 ? activeIndex : 0;
        slides.forEach((slide, index) => slide.classList.toggle('is-active', index === activeIndex));

        window.setInterval(() => {
            slides[activeIndex].classList.remove('is-active');
            activeIndex = (activeIndex + 1) % slides.length;
            slides[activeIndex].classList.add('is-active');
        }, speed);
    }
}

if (homeNav && navContrastHero) {
    const syncHomeNavState = () => {
        const heroBottom = navContrastHero.getBoundingClientRect().bottom;
        homeNav.classList.toggle('home-nav-scrolled', heroBottom <= 120);
    };

    syncHomeNavState();
    window.addEventListener('scroll', syncHomeNavState, { passive: true });
    window.addEventListener('resize', syncHomeNavState);
} else if (homeNav) {
    homeNav.classList.add('home-nav-scrolled');
}

document.querySelectorAll('[data-work-gallery]').forEach((gallery) => {
    const mainImage = gallery.querySelector('[data-work-gallery-main]');
    const thumbs = Array.from(gallery.querySelectorAll('[data-work-gallery-thumb]'));

    if (!mainImage || thumbs.length === 0) {
        return;
    }

    thumbs.forEach((thumb) => {
        thumb.addEventListener('click', () => {
            const nextSrc = thumb.dataset.src || '';
            if (nextSrc === '') {
                return;
            }

            mainImage.src = nextSrc;
            mainImage.alt = thumb.dataset.alt || '';
            thumbs.forEach((item) => item.classList.toggle('is-active', item === thumb));
        });
    });
});

// Organic scroll reveal
const scrollRevealItems = Array.from(document.querySelectorAll('[data-scroll-reveal]'));

if (scrollRevealItems.length > 0) {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (prefersReducedMotion || !('IntersectionObserver' in window)) {
        scrollRevealItems.forEach((item) => item.classList.add('is-visible'));
    } else {
        document.body.classList.add('is-scroll-reveal-ready');

        const scrollRevealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            rootMargin: '0px 0px -14% 0px',
            threshold: 0.16,
        });

        scrollRevealItems.forEach((item) => scrollRevealObserver.observe(item));
    }
}

// Newsletter reveal
const newsletterReveal = document.querySelector('[data-newsletter-reveal]');

if (newsletterReveal) {
    const showNewsletter = () => newsletterReveal.classList.add('is-visible');
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (prefersReducedMotion || !('IntersectionObserver' in window)) {
        showNewsletter();
    } else {
        newsletterReveal.classList.add('is-reveal-ready');

        const newsletterObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    showNewsletter();
                    observer.unobserve(entry.target);
                }
            });
        }, {
            rootMargin: '0px 0px -18% 0px',
            threshold: 0.2,
        });

        newsletterObserver.observe(newsletterReveal);
    }
}

// History images reveal
const historyImagesReveal = document.querySelector('[data-history-images-reveal]');

if (historyImagesReveal) {
    const showHistoryImages = () => historyImagesReveal.classList.add('is-visible');
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (prefersReducedMotion || !('IntersectionObserver' in window)) {
        showHistoryImages();
    } else {
        historyImagesReveal.classList.add('is-reveal-ready');

        const historyImagesObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    showHistoryImages();
                    observer.unobserve(entry.target);
                }
            });
        }, {
            rootMargin: '0px 0px -18% 0px',
            threshold: 0.2,
        });

        historyImagesObserver.observe(historyImagesReveal);
    }
}

// Obra Viva timeline reveal
const obraVivaTimeline = document.querySelector('[data-obra-viva-timeline]');

if (obraVivaTimeline) {
    const timelineCards = Array.from(obraVivaTimeline.querySelectorAll('.obra-viva-line'));
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const isMobileTimeline = window.matchMedia('(max-width: 767px)').matches;
    const cardDelay = isMobileTimeline ? 280 : 180;

    const revealTimeline = () => {
        const totalCards = timelineCards.length || 1;

        timelineCards.forEach((card, index) => {
            window.setTimeout(() => {
                card.classList.add('is-visible');
                obraVivaTimeline.style.setProperty('--timeline-progress', String((index + 1) / totalCards));
            }, index * cardDelay);
        });
    };

    if (prefersReducedMotion || !('IntersectionObserver' in window)) {
        timelineCards.forEach((card) => card.classList.add('is-visible'));
        obraVivaTimeline.style.setProperty('--timeline-progress', '1');
    } else {
        obraVivaTimeline.classList.add('is-reveal-ready');

        const timelineObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    revealTimeline();
                    observer.unobserve(entry.target);
                }
            });
        }, {
            rootMargin: isMobileTimeline ? '0px 0px -8% 0px' : '0px 0px -18% 0px',
            threshold: isMobileTimeline ? 0.08 : 0.18,
        });

        timelineObserver.observe(obraVivaTimeline);
    }
}

// Fullscreen project carousel
document.querySelectorAll('[data-project-carousel]').forEach((carousel) => {
    const slides = Array.from(carousel.querySelectorAll('.shader-carousel-slide'));
    const images = Array.from(carousel.querySelectorAll('.shader-carousel-image'));
    const title = carousel.querySelector('.shader-carousel-title');
    const description = carousel.querySelector('.shader-carousel-description');
    const nextButton = carousel.querySelector('[data-project-next]');
    const prevButton = carousel.querySelector('[data-project-prev]');

    if (slides.length === 0 || images.length === 0 || !title || !description) {
        return;
    }

    let activeIndex = slides.findIndex((slide) => slide.classList.contains('is-active'));
    let startX = 0;
    let pointerDown = false;

    const syncSlide = () => {
        const slide = slides[activeIndex];

        slides.forEach((item, index) => {
            item.classList.toggle('is-active', index === activeIndex);
        });
        images.forEach((image, index) => image.classList.toggle('is-active', index === activeIndex));

        title.textContent = slide.dataset.title || '';
        description.textContent = slide.dataset.description || '';
    };

    const goTo = (index) => {
        activeIndex = (index + slides.length) % slides.length;
        syncSlide();
    };

    slides.forEach((slide, index) => {
        slide.querySelector('button')?.addEventListener('click', () => goTo(index));
    });

    nextButton?.addEventListener('click', () => goTo(activeIndex + 1));
    prevButton?.addEventListener('click', () => goTo(activeIndex - 1));

    carousel.tabIndex = 0;
    carousel.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowRight') {
            goTo(activeIndex + 1);
        } else if (event.key === 'ArrowLeft') {
            goTo(activeIndex - 1);
        }
    });

    carousel.addEventListener('pointerdown', (event) => {
        pointerDown = true;
        startX = event.clientX;
    });

    carousel.addEventListener('pointerup', (event) => {
        if (!pointerDown) {
            return;
        }

        const delta = event.clientX - startX;
        pointerDown = false;

        if (Math.abs(delta) > 44) {
            goTo(activeIndex + (delta < 0 ? 1 : -1));
        }
    });

    syncSlide();
});

// Menu Logic
const menuBtn = document.querySelector('#menu-toggle');
const menuClose = document.querySelector('#menu-close');
const menu = document.querySelector('#menu-content');

if (menuBtn && menuClose && menu) {
    menuBtn.onclick = () => menu.classList.add('active');
    menuClose.onclick = () => menu.classList.remove('active');
}

// Language switcher
const languageSwitchers = document.querySelectorAll('.language-switcher');

if (languageSwitchers.length > 0) {
    document.addEventListener('click', (event) => {
        languageSwitchers.forEach((switcher) => {
            if (!switcher.contains(event.target)) {
                switcher.removeAttribute('open');
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            languageSwitchers.forEach((switcher) => {
                switcher.removeAttribute('open');
            });
        }
    });
}

// COMMAR chatbot
document.querySelectorAll('[data-commar-chatbot]').forEach((chatbot) => {
    const panel = chatbot.querySelector('[data-chatbot-panel]');
    const toggle = chatbot.querySelector('[data-chatbot-toggle]');
    const close = chatbot.querySelector('[data-chatbot-close]');
    const messages = chatbot.querySelector('[data-chatbot-messages]');
    const actions = chatbot.querySelector('[data-chatbot-actions]');
    const form = chatbot.querySelector('[data-chatbot-form]');
    const input = chatbot.querySelector('[data-chatbot-input]');
    const whatsappUrl = chatbot.dataset.whatsappUrl || 'https://wa.me/';
    const contactUrl = chatbot.dataset.contactUrl || 'contacto.php';
    const contactEmail = chatbot.dataset.contactEmail || 'info@commargroup.com.ar';

    if (!panel || !toggle || !close || !messages || !actions || !form || !input) {
        return;
    }

    const topics = [
        {
            id: 'ceo-fundadora',
            label: 'CEO y fundadora',
            keywords: ['ceo', 'fundador', 'fundadora', 'fundo', 'fundó', 'director', 'directora', 'presidente', 'romina', 'lo conte', 'loconte'],
            answer: 'La CEO y fundadora de COMMAR GROUP es la Arq. Romina Lo Conte.',
        },
        {
            id: 'contacto-persona',
            label: 'Hablar con una persona',
            keywords: ['persona', 'humano', 'asesor', 'representante', 'comunicar', 'comunicarme', 'hablar', 'contactarme', 'contactar', 'atencion personalizada', 'alguien'],
            answer: 'Podés comunicarte con una persona de COMMAR GROUP desde el formulario de contacto, por email o por WhatsApp.',
            contactActions: true,
        },
        {
            id: 'presupuesto-cotizacion',
            label: 'Presupuesto o cotización',
            keywords: ['presupuesto', 'presupuestar', 'cotizacion', 'cotización', 'cotizar', 'cotizo', 'precio', 'precios', 'costo', 'costos', 'valor', 'valores', 'tarifa', 'honorarios'],
            answer: 'Para consultas sobre presupuesto, cotización, precios o costos, escribinos a través del formulario de contacto y pronto nos comunicaremos para entender el alcance de tu proyecto.',
            formAction: true,
        },
        {
            id: 'estudio',
            label: 'El estudio',
            keywords: ['estudio', 'commar', 'quienes', 'empresa', 'compania', 'equipo', 'trayectoria', 'romina', 'historia'],
            answer: 'COMMAR GROUP es un estudio multidisciplinario con base en Buenos Aires que integra arquitectura, construcción, gestión técnico-administrativa y medioambiente. El equipo trabaja con foco en compromiso, profesionalismo y resolución concreta de proyectos.',
        },
        {
            id: 'servicios',
            label: 'Servicios',
            keywords: ['servicio', 'servicios', 'arquitectura', 'proyecto', 'gerenciamiento', 'construccion', 'demolicion', 'habilitacion', 'habilitaciones', 'ambiente', 'medioambiente', 'seguridad', 'higiene'],
            answer: 'Los servicios principales incluyen Proyecto, Gerenciamiento, Demolición, Construcción, Habilitaciones y Medio ambiente / Seguridad e Higiene. COMMAR acompaña cada etapa con dirección técnica, documentación clara y coordinación entre proyecto, obra, normativa y ambiente.',
        },
        {
            id: 'proyectos',
            label: 'Proyecto',
            keywords: ['proyecto', 'proyectos', 'anteproyecto', 'ejecutivo', 'documentacion', 'planos', 'relevamiento', 'programa', 'cotizar', 'tramitar'],
            answer: 'El servicio de Proyecto transforma una necesidad inicial en documentación clara y construible. Incluye relevamiento, definición de programa, anteproyecto, proyecto ejecutivo, planos, detalles, coordinación técnica y acompañamiento para cotización, trámites y transición a obra.',
        },
        {
            id: 'obra-viva',
            label: 'Obra Viva',
            keywords: ['obra viva', 'obra', 'tad', 'portal', 'director', 'avos', 'sifer', 'intimacion', 'clausura', 'normativa', 'caba', 'administrativa', 'documental'],
            answer: 'Obra Viva es la solución 360 de COMMAR para gestión técnico-administrativa de obras en CABA. Funciona como oficina administrativa externa para empresas constructoras, estudios y profesionales: gestiona trámites, documentación, normativa, intimaciones, AVOS, SIFER, Portal DO y seguimiento de empresas.',
        },
        {
            id: 'obras',
            label: 'Obras',
            keywords: ['obra', 'obras', 'portfolio', 'experiencia', 'casa atlas', 'pabellon', 'torre prisma', 'refugio litoral', 'patio umbral', 'nucleo basalto', 'hospital aleman', 'alto palermo', 'movistar'],
            answer: 'La web presenta obras y experiencias de distintas escalas: residencial, institucional, uso mixto, hospitalidad, cultural, corporativo y equipamientos. Entre los proyectos destacados aparecen Casa Atlas, Pabellón Delta, Torre Prisma, Refugio Litoral, Patio Umbral y Núcleo Basalto.',
        },
        {
            id: 'medioambiente',
            label: 'Medio ambiente / Seguridad e Higiene',
            keywords: ['medio ambiente', 'ambiental', 'emisiones', 'aire', 'efluentes', 'ruidos', 'monitoreo', 'mitigacion', 'cabinas', 'seguridad', 'higiene'],
            answer: 'En Medio ambiente / Seguridad e Higiene damos consultoría ambiental, análisis normativo, documentación técnica y acompañamiento preventivo para proyectos preparados para su contexto.',
        },
        {
            id: 'contacto',
            label: 'Contacto',
            keywords: ['contacto', 'whatsapp', 'mail', 'email', 'telefono', 'direccion', 'ubicacion', 'consulta', 'presupuesto', 'cotizacion', 'reunion'],
            answer: 'Podés contactar a COMMAR GROUP desde el formulario de la web. La consulta se puede derivar por área: Proyecto, Gerenciamiento, Demolición, Construcción, Habilitaciones, Medio ambiente / Seguridad e Higiene u Obra Viva.',
        },
        {
            id: 'horario-atencion',
            label: 'Horario de atención',
            keywords: ['horario', 'horarios', 'atencion', 'atienden', 'lunes', 'viernes', 'abierto', 'abren', 'cierran', 'hora', 'disponible'],
            answer: 'El horario de atención de COMMAR GROUP es de lunes a viernes, de 9:00 a 18:00 hs.',
        },
    ];

    const state = {
        started: false,
        transcript: [],
        lastTopic: '',
        questionCount: 0,
    };

    const normalize = (value) => value
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');

    const addMessage = (text, type = 'bot') => {
        const message = document.createElement('p');
        message.className = `commar-chatbot-message is-${type}`;
        message.textContent = text;
        messages.appendChild(message);
        messages.scrollTop = messages.scrollHeight;
        state.transcript.push(`${type === 'user' ? 'Usuario' : 'Asistente'}: ${text}`);

        if (type === 'user') {
            state.questionCount += 1;
        }
    };

    const canOfferWhatsapp = () => state.questionCount >= 5;

    const buildWhatsappUrl = () => {
        const transcript = state.transcript.slice(-8).join('\n');
        const topic = state.lastTopic ? `Tema: ${state.lastTopic}\n` : '';
        const text = `Hola COMMAR GROUP, vengo desde la web. Quisiera continuar esta consulta.\n${topic}\n${transcript}`;

        try {
            const url = new URL(whatsappUrl);
            url.searchParams.set('text', text);
            return url.toString();
        } catch (error) {
            return `${whatsappUrl}${whatsappUrl.includes('?') ? '&' : '?'}text=${encodeURIComponent(text)}`;
        }
    };

    const renderActions = (showWhatsapp = false) => {
        actions.innerHTML = '';

        if (!showWhatsapp) {
            return;
        }

        const whatsapp = document.createElement('a');
        whatsapp.className = 'commar-chatbot-whatsapp';
        whatsapp.href = buildWhatsappUrl();
        whatsapp.target = '_blank';
        whatsapp.rel = 'noopener noreferrer';
        whatsapp.textContent = 'Seguir por WhatsApp';
        whatsapp.addEventListener('click', () => {
            whatsapp.href = buildWhatsappUrl();
        });
        actions.appendChild(whatsapp);
    };

    const renderContactActions = () => {
        actions.innerHTML = '';

        const contact = document.createElement('a');
        contact.className = 'commar-chatbot-contact-link';
        contact.href = contactUrl;
        contact.textContent = 'Formulario de contacto';
        actions.appendChild(contact);

        const email = document.createElement('a');
        email.className = 'commar-chatbot-contact-link';
        email.href = contactUrl;
        email.textContent = 'Email';
        email.setAttribute('aria-label', `Ir al formulario de contacto para escribir a ${contactEmail}`);
        actions.appendChild(email);

        const whatsapp = document.createElement('a');
        whatsapp.className = 'commar-chatbot-whatsapp';
        whatsapp.href = buildWhatsappUrl();
        whatsapp.target = '_blank';
        whatsapp.rel = 'noopener noreferrer';
        whatsapp.textContent = 'WhatsApp';
        whatsapp.addEventListener('click', () => {
            whatsapp.href = buildWhatsappUrl();
        });
        actions.appendChild(whatsapp);
    };

    const renderFormAction = () => {
        actions.innerHTML = '';

        const contact = document.createElement('a');
        contact.className = 'commar-chatbot-contact-link';
        contact.href = contactUrl;
        contact.textContent = 'Formulario de contacto';
        actions.appendChild(contact);
    };

    const answerTopic = (topic, userText) => {
        addMessage(userText, 'user');
        state.lastTopic = topic.label;
        window.setTimeout(() => {
            addMessage(topic.answer);
            if (topic.formAction) {
                renderFormAction();
                return;
            }

            if (topic.contactActions) {
                renderContactActions();
                return;
            }

            if (canOfferWhatsapp()) {
                addMessage('Si querés avanzar, te derivo a WhatsApp con esta consulta ya preparada para el equipo de COMMAR.');
                renderActions(true);
            } else {
                renderActions();
            }
        }, 220);
    };

    const findTopic = (text) => {
        const normalized = normalize(text);
        return topics.find((topic) => topic.keywords.some((keyword) => normalized.includes(normalize(keyword))));
    };

    const startChat = () => {
        if (state.started) {
            return;
        }

        state.started = true;
        addMessage('Hola. Soy el asistente virtual de COMMAR GROUP. Puedo responder consultas sobre el estudio, servicios, proyectos, Obra Viva, obras, medioambiente o contacto. Escribí tu consulta para empezar.');
        renderActions();
    };

    const openChat = () => {
        panel.hidden = false;
        toggle.setAttribute('aria-expanded', 'true');
        startChat();
        window.setTimeout(() => input.focus(), 50);
    };

    const closeChat = () => {
        panel.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');
        toggle.focus();
    };

    toggle.addEventListener('click', () => {
        if (panel.hidden) {
            openChat();
        } else {
            closeChat();
        }
    });

    close.addEventListener('click', closeChat);

    form.addEventListener('submit', (event) => {
        event.preventDefault();

        const text = input.value.trim();

        if (text === '') {
            return;
        }

        input.value = '';
        const topic = findTopic(text);

        if (topic) {
            answerTopic(topic, text);
            return;
        }

        addMessage(text, 'user');
        state.lastTopic = 'Consulta general';
        window.setTimeout(() => {
            if (canOfferWhatsapp()) {
                addMessage('Tomo tu consulta. Para responderla con precisión, te conviene continuar por WhatsApp y el equipo de COMMAR podrá darte una respuesta personalizada.');
                renderActions(true);
            } else {
                addMessage('Tomo tu consulta. Puedo responder sobre el estudio, servicios, proyectos, Obra Viva, obras, medioambiente, horarios de atención o contacto.');
                renderActions();
            }
        }, 220);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !panel.hidden) {
            closeChat();
        }
    });
});
