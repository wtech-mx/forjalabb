import 'bootstrap';

document.querySelectorAll('[data-magazine]').forEach((magazine) => {
    const pages = [...magazine.querySelectorAll('[data-magazine-page]')];
    const currentLabel = magazine.querySelector('[data-magazine-current]');
    const totalLabel = magazine.querySelector('[data-magazine-total]');
    const progress = magazine.querySelector('[data-magazine-progress]');
    const prev = magazine.querySelector('[data-magazine-prev]');
    const next = magazine.querySelector('[data-magazine-next]');
    let current = 0;
    let touchStart = null;
    const format = (number) => String(number).padStart(2, '0');
    const showPage = (index, direction = 1) => {
        const target = Math.max(0, Math.min(pages.length - 1, index));
        pages.forEach((page, pageIndex) => {
            page.classList.toggle('is-active', pageIndex === target);
            page.classList.toggle('is-before', pageIndex < target);
            page.classList.toggle('is-after', pageIndex > target);
            page.style.setProperty('--flip-direction', direction);
            page.setAttribute('aria-hidden', pageIndex === target ? 'false' : 'true');
        });
        current = target;
        currentLabel.textContent = format(current + 1);
        totalLabel.textContent = format(pages.length);
        progress.style.width = `${((current + 1) / pages.length) * 100}%`;
        prev.disabled = current === 0;
        next.disabled = current === pages.length - 1;
        history.replaceState(null, '', `${location.pathname}${location.search}#pagina-${current + 1}`);
    };
    prev.addEventListener('click', () => showPage(current - 1, -1));
    next.addEventListener('click', () => showPage(current + 1, 1));
    magazine.addEventListener('keydown', (event) => { if (event.key === 'ArrowLeft') showPage(current - 1, -1); if (event.key === 'ArrowRight' || event.key === ' ') showPage(current + 1, 1); });
    magazine.addEventListener('touchstart', (event) => touchStart = event.changedTouches[0].clientX, { passive: true });
    magazine.addEventListener('touchend', (event) => { if (touchStart === null) return; const distance = event.changedTouches[0].clientX - touchStart; if (Math.abs(distance) > 55) showPage(current + (distance < 0 ? 1 : -1), distance < 0 ? 1 : -1); touchStart = null; }, { passive: true });
    magazine.querySelector('[data-magazine-fullscreen]')?.addEventListener('click', () => document.fullscreenElement ? document.exitFullscreen() : magazine.requestFullscreen?.());
    magazine.querySelector('[data-magazine-share]')?.addEventListener('click', async () => { const data = { title: 'Catálogo digital ForjaLab', text: 'Mira los productos y paquetes de ForjaLab.', url: location.href }; if (navigator.share) await navigator.share(data).catch(() => {}); else { await navigator.clipboard?.writeText(location.href); } });
    const hashPage = Number(location.hash.replace('#pagina-', ''));
    showPage(Number.isFinite(hashPage) && hashPage > 0 ? hashPage - 1 : 0);
    magazine.tabIndex = 0;
    magazine.focus({ preventScroll: true });
});

const leadPopup = document.querySelector('[data-lead-popup]');
if (leadPopup) {
    const form = leadPopup.querySelector('[data-lead-form]');
    const status = leadPopup.querySelector('[data-lead-status]');
    const submit = leadPopup.querySelector('[data-lead-submit]');
    const completedKey = 'forjalab-lead-completed';
    const dismissedKey = 'forjalab-lead-dismissed';
    const dismissedRecently = () => {
        const value = Number(localStorage.getItem(dismissedKey) || 0);
        return value && Date.now() - value < 3 * 24 * 60 * 60 * 1000;
    };
    const openPopup = () => {
        leadPopup.classList.add('is-open');
        leadPopup.setAttribute('aria-hidden', 'false');
        document.body.classList.add('lead-popup-open');
        window.setTimeout(() => form?.querySelector('input')?.focus(), 250);
    };
    const closePopup = (remember = true) => {
        leadPopup.classList.remove('is-open');
        leadPopup.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('lead-popup-open');
        if (remember && !localStorage.getItem(completedKey)) localStorage.setItem(dismissedKey, Date.now().toString());
    };
    leadPopup.querySelectorAll('[data-lead-close]').forEach((button) => button.addEventListener('click', () => closePopup()));
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape' && leadPopup.classList.contains('is-open')) closePopup(); });

    if (!localStorage.getItem(completedKey) && !dismissedRecently()) window.setTimeout(openPopup, 1000);

    form?.addEventListener('submit', async (event) => {
        event.preventDefault();
        status.textContent = '';
        submit.disabled = true;
        submit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Registrando beneficio...';
        const payload = Object.fromEntries(new FormData(form).entries());
        try {
            const response = await fetch(leadPopup.dataset.endpoint, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }, body: JSON.stringify(payload) });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw new Error(data.message || Object.values(data.errors || {})[0]?.[0] || 'No pudimos registrar tus datos.');
            localStorage.setItem(completedKey, '1');
            leadPopup.querySelector('[data-lead-form-wrap]').hidden = true;
            leadPopup.querySelector('[data-lead-success]').hidden = false;
        } catch (error) {
            status.textContent = error.message;
            submit.disabled = false;
            submit.innerHTML = '<i class="bi bi-ticket-perforated-fill me-2"></i>Quiero mi 10% de descuento';
        }
    });
}

const orderForm = document.querySelector('[data-order-form]');
if (orderForm) {
    const itemsContainer = orderForm.querySelector('[data-order-items]');
    const template = document.querySelector('#orderItemTemplate');
    let itemIndex = 0;
    const money = (value) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value || 0);
    const calculate = () => {
        let subtotal = 0;
        itemsContainer.querySelectorAll('[data-order-item]').forEach((row) => {
            const quantity = Number(row.querySelector('[data-quantity]').value) || 0;
            const price = Number(row.querySelector('[data-price]').value) || 0;
            const line = quantity * price;
            row.querySelector('[data-line-total]').textContent = money(line);
            subtotal += line;
        });
        const discountValue = Number(orderForm.querySelector('[data-discount]').value) || 0;
        const discount = orderForm.querySelector('[data-discount-type]').value === 'percent' ? subtotal * Math.min(discountValue, 100) / 100 : Math.min(discountValue, subtotal);
        const shipping = orderForm.querySelector('[data-shipping-toggle]').checked ? Number(orderForm.querySelector('[data-shipping]').value) || 0 : 0;
        const total = Math.max(0, subtotal - discount + shipping);
        const advance = Math.min(Number(orderForm.querySelector('[data-advance]').value) || 0, total);
        orderForm.querySelector('[data-subtotal]').textContent = money(subtotal);
        orderForm.querySelector('[data-discount-total]').textContent = `-${money(discount)}`;
        orderForm.querySelector('[data-shipping-total]').textContent = money(shipping);
        orderForm.querySelector('[data-total]').textContent = money(total);
        orderForm.querySelector('[data-advance-total]').textContent = money(advance);
        orderForm.querySelector('[data-balance]').textContent = money(total - advance);
    };
    const addItem = (saved = {}) => {
        const fragment = template.content.cloneNode(true);
        const row = fragment.querySelector('[data-order-item]');
        row.innerHTML = row.innerHTML.replaceAll('__INDEX__', itemIndex++);
        const product = row.querySelector('[data-product]');
        const price = row.querySelector('[data-price]');
        if (saved.item_type && saved.item_id) product.value = `${saved.item_type}:${saved.item_id}`;
        const syncItem = () => {
            const selected = product.selectedOptions[0];
            row.querySelector('[data-item-type]').value = selected?.dataset.type ?? '';
            row.querySelector('[data-item-id]').value = selected?.dataset.id ?? '';
            const contents = selected?.dataset.contents ?? '';
            row.querySelector('[data-item-contents]').textContent = contents ? `Incluye: ${contents}` : '';
        };
        syncItem();
        price.value = saved.unit_price ?? product.selectedOptions[0]?.dataset.price ?? '';
        row.querySelector('[data-quantity]').value = saved.quantity ?? 1;
        product.addEventListener('change', () => { syncItem(); price.value = product.selectedOptions[0]?.dataset.price ?? ''; calculate(); });
        row.querySelectorAll('input').forEach((input) => input.addEventListener('input', calculate));
        row.querySelector('[data-remove-item]').addEventListener('click', () => { row.remove(); calculate(); });
        itemsContainer.append(row);
        calculate();
    };
    const saved = JSON.parse(document.querySelector('#savedOrderItems')?.textContent || '[]');
    (saved.length ? saved : [{}]).forEach(addItem);
    orderForm.querySelector('[data-add-item]').addEventListener('click', () => addItem());
    orderForm.querySelectorAll('[data-discount], [data-advance], [data-shipping]').forEach((input) => input.addEventListener('input', calculate));
    orderForm.querySelector('[data-discount-type]').addEventListener('change', calculate);
    orderForm.querySelector('[data-shipping-toggle]').addEventListener('change', (event) => { orderForm.querySelector('[data-shipping-wrap]').classList.toggle('d-none', !event.target.checked); calculate(); });
    const customerToggle = orderForm.querySelector('[data-new-customer-toggle]');
    customerToggle.addEventListener('click', (event) => {
        const newBlock = orderForm.querySelector('[data-new-customer]');
        const creating = newBlock.classList.contains('d-none');
        newBlock.classList.toggle('d-none', !creating);
        orderForm.querySelector('[data-existing-customer]').classList.toggle('d-none', creating);
        newBlock.querySelectorAll('input').forEach((input) => input.disabled = !creating);
        orderForm.querySelector('[data-customer-select]').disabled = creating;
        event.currentTarget.innerHTML = creating ? '<i class="bi bi-search me-1"></i>Buscar cliente' : '<i class="bi bi-person-plus me-1"></i>Crear cliente';
    });
    orderForm.querySelector('[data-customer-search]').addEventListener('input', (event) => {
        const search = event.target.value.toLowerCase().trim();
        orderForm.querySelectorAll('[data-customer-select] option[data-search]').forEach((option) => option.hidden = !option.dataset.search.includes(search));
    });
    if (orderForm.dataset.createCustomer === '1') customerToggle.click();
}

document.querySelectorAll('[data-social-chat]').forEach((chat) => {
    const trigger = chat.querySelector('[data-social-chat-trigger]');
    const menu = chat.querySelector('.social-chat-menu');

    const setOpen = (open) => {
        chat.classList.toggle('is-open', open);
        trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
        trigger.setAttribute('aria-label', open ? 'Cerrar redes sociales' : 'Abrir redes sociales');
        menu.setAttribute('aria-hidden', open ? 'false' : 'true');
    };

    trigger?.addEventListener('click', () => setOpen(!chat.classList.contains('is-open')));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') setOpen(false);
    });
    document.addEventListener('click', (event) => {
        if (!chat.contains(event.target)) setOpen(false);
    });
});

const analyticsEndpoint = document.querySelector('meta[name="analytics-endpoint"]')?.content;

if (analyticsEndpoint) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const query = new URLSearchParams(window.location.search);
    let analyticsSession = localStorage.getItem('forjalab_analytics_session');

    if (!analyticsSession) {
        analyticsSession = window.crypto?.randomUUID?.() || `${Date.now()}-${Math.random().toString(36).slice(2)}`;
        localStorage.setItem('forjalab_analytics_session', analyticsSession);
    }

    const trackEvent = (eventType, label = null) => {
        fetch(analyticsEndpoint, {
            method: 'POST',
            keepalive: true,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                event_type: eventType,
                path: window.location.pathname,
                label,
                session_id: analyticsSession,
                referrer: document.referrer || null,
                utm_source: query.get('utm_source'),
                utm_medium: query.get('utm_medium'),
                utm_campaign: query.get('utm_campaign'),
            }),
        }).catch(() => {});
    };

    trackEvent('page_view', document.title);

    const viewedSections = new Set();
    const sectionObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            const sectionLabel = entry.target.id || entry.target.querySelector('h2')?.textContent.trim() || entry.target.className.split(' ')[0] || 'seccion';
            if (!entry.isIntersecting || viewedSections.has(sectionLabel)) return;
            viewedSections.add(sectionLabel);
            trackEvent('section_view', sectionLabel.slice(0, 160));
            sectionObserver.unobserve(entry.target);
        });
    }, { threshold: 0.35 });

    document.querySelectorAll('main section').forEach((section) => sectionObserver.observe(section));

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');
        if (!link) return;

        const href = link.getAttribute('href') || '';
        const label = link.textContent.trim().replace(/\s+/g, ' ').slice(0, 160);

        if (href.includes('wa.me/')) {
            trackEvent('whatsapp_click', label || 'WhatsApp');
        } else if (href.includes('/catalogo/') || href.includes('/servicios/')) {
            trackEvent('product_click', label || href);
        }
    });
}

const garmentAssets = {
    cap: {
        src: '/images/embroidery-cap.png',
        alt: 'Mockup de gorra para sublimacion o DTF',
        x: 50,
        y: 39,
        size: 24,
    },
    jacket: {
        src: '/images/embroidery-jacket.png',
        alt: 'Mockup de chamarra para sublimacion o DTF',
        x: 41,
        y: 36,
        size: 18,
    },
    shirt: {
        src: '/images/embroidery-shirt.png',
        alt: 'Mockup de playera para sublimacion o DTF',
        x: 50,
        y: 39,
        size: 22,
    },
};

document.querySelectorAll('[data-embroidery-tool]').forEach((tool) => {
    const stage = tool.querySelector('[data-mockup-stage]');
    const garmentPreview = tool.querySelector('[data-garment-preview]');
    const logoInput = tool.querySelector('[data-logo-input]');
    const logoPreview = tool.querySelector('[data-logo-preview]');
    const placeholder = tool.querySelector('[data-logo-placeholder]');
    const sizeInput = tool.querySelector('[data-logo-size]');
    const rotateInput = tool.querySelector('[data-logo-rotate]');
    const resetButton = tool.querySelector('[data-reset-logo]');
    const garmentButtons = tool.querySelectorAll('[data-garment]');

    let activeGarment = 'cap';
    let position = { x: garmentAssets.cap.x, y: garmentAssets.cap.y };
    let dragging = false;

    const updateLogoStyles = () => {
        stage.style.setProperty('--logo-x', `${position.x}%`);
        stage.style.setProperty('--logo-y', `${position.y}%`);
        stage.style.setProperty('--logo-size', `${sizeInput.value}%`);
        stage.style.setProperty('--logo-rotate', `${rotateInput.value}deg`);
    };

    const centerForGarment = (garment) => {
        const config = garmentAssets[garment];

        position = { x: config.x, y: config.y };
        sizeInput.value = config.size;
        rotateInput.value = 0;
        updateLogoStyles();
    };

    const setGarment = (garment) => {
        const config = garmentAssets[garment];

        activeGarment = garment;
        garmentPreview.src = config.src;
        garmentPreview.alt = config.alt;
        garmentButtons.forEach((button) => {
            button.classList.toggle('active', button.dataset.garment === garment);
        });
        centerForGarment(activeGarment);
    };

    const moveLogo = (event) => {
        const rect = stage.getBoundingClientRect();

        position = {
            x: Math.min(88, Math.max(12, ((event.clientX - rect.left) / rect.width) * 100)),
            y: Math.min(88, Math.max(12, ((event.clientY - rect.top) / rect.height) * 100)),
        };
        updateLogoStyles();
    };

    garmentButtons.forEach((button) => {
        button.addEventListener('click', () => setGarment(button.dataset.garment));
    });

    logoInput.addEventListener('change', () => {
        const [file] = logoInput.files;

        if (!file) {
            return;
        }

        logoPreview.src = URL.createObjectURL(file);
        logoPreview.hidden = false;
        placeholder.hidden = true;
        updateLogoStyles();
    });

    [sizeInput, rotateInput].forEach((input) => {
        input.addEventListener('input', updateLogoStyles);
    });

    resetButton.addEventListener('click', () => centerForGarment(activeGarment));

    logoPreview.addEventListener('pointerdown', (event) => {
        dragging = true;
        logoPreview.setPointerCapture(event.pointerId);
        moveLogo(event);
    });

    logoPreview.addEventListener('pointermove', (event) => {
        if (dragging) {
            moveLogo(event);
        }
    });

    logoPreview.addEventListener('pointerup', () => {
        dragging = false;
    });

    stage.addEventListener('pointerdown', (event) => {
        if (event.target === stage || event.target === garmentPreview || event.target === placeholder) {
            moveLogo(event);
        }
    });

    updateLogoStyles();
});

const tequilaFinishLabels = {
    white: 'Blanco',
    frosted: 'Satinado',
    clear: 'Transparente',
};

document.querySelectorAll('[data-tequila-configurator]').forEach((configurator) => {
    const preview = configurator.querySelector('[data-tequila-preview]');
    const previewDesign = configurator.querySelector('[data-tequila-preview-design]');
    const finishButtons = configurator.querySelectorAll('[data-tequila-finish]');
    const designOptions = configurator.querySelectorAll('[data-tequila-design]');
    const selectionText = configurator.querySelector('[data-tequila-selection]');
    const quoteLink = configurator.querySelector('a.btn');

    let activeFinish = 'white';
    let activeDesign = designOptions[0]?.dataset.tequilaName || 'Aguacate';

    const updateSelection = () => {
        const finishLabel = tequilaFinishLabels[activeFinish] || activeFinish;
        selectionText.textContent = `Vista: ${finishLabel} con diseno ${activeDesign}`;

        const message = `Hola, quiero cotizar tequileros personalizados de 3 o 6 piezas. Me interesa acabado ${finishLabel} con diseno ${activeDesign}.`;
        quoteLink.href = `https://wa.me/525564442949?text=${encodeURIComponent(message)}`;
    };

    finishButtons.forEach((button) => {
        button.addEventListener('click', () => {
            activeFinish = button.dataset.tequilaFinish;
            preview.dataset.finish = activeFinish;
            finishButtons.forEach((item) => {
                item.classList.toggle('active', item === button);
            });
            updateSelection();
        });
    });

    designOptions.forEach((option) => {
        option.addEventListener('click', () => {
            activeDesign = option.dataset.tequilaName;
            previewDesign.src = option.dataset.tequilaDesign;
            designOptions.forEach((item) => {
                item.classList.toggle('active', item === option);
            });
            updateSelection();
        });
    });

    updateSelection();
});

document.querySelectorAll('[data-coaster-configurator]').forEach((configurator) => {
    const previewDesign = configurator.querySelector('[data-coaster-preview-design]');
    const designOptions = configurator.querySelectorAll('[data-coaster-design]');
    const selectionText = configurator.querySelector('[data-coaster-selection]');
    const quoteLink = configurator.querySelector('[data-coaster-quote]');

    let activeDesign = designOptions[0]?.dataset.coasterName || 'Viva Mexico';

    const updateSelection = () => {
        selectionText.textContent = `Vista: Porta vasos con diseno ${activeDesign}`;

        const message = `Hola, quiero cotizar un set de 4 porta vasos del 15 de septiembre. Me interesa el diseno ${activeDesign}.`;
        quoteLink.href = `https://wa.me/525564442949?text=${encodeURIComponent(message)}`;
    };

    designOptions.forEach((option) => {
        option.addEventListener('click', () => {
            activeDesign = option.dataset.coasterName;
            previewDesign.src = option.dataset.coasterDesign;
            previewDesign.alt = option.dataset.coasterAlt;
            designOptions.forEach((item) => {
                item.classList.toggle('active', item === option);
            });
            updateSelection();
        });
    });

    updateSelection();
});

document.querySelectorAll('[data-drinkware-configurator]').forEach((configurator) => {
    const product = configurator.dataset.drinkwareProduct || 'producto';
    const preview = configurator.querySelector('[data-drinkware-preview]');
    const previewDesign = configurator.querySelector('[data-drinkware-preview-design]');
    const colorButtons = configurator.querySelectorAll('[data-drinkware-color]');
    const designOptions = configurator.querySelectorAll('[data-drinkware-design]');
    const selectionText = configurator.querySelector('[data-drinkware-selection]');
    const quoteLink = configurator.querySelector('[data-drinkware-quote]');

    const activeColorButton = Array.from(colorButtons).find((button) => button.classList.contains('active')) || colorButtons[0];

    let activeColor = activeColorButton?.dataset.drinkwareColor || 'green';
    let activeColorName = activeColorButton?.dataset.drinkwareColorName || 'Verde';
    let activeDesign = designOptions[0]?.dataset.drinkwareName || 'Aguacate';

    const productLabel = product.charAt(0).toUpperCase() + product.slice(1);
    const quoteSubject = product === 'taza' ? 'tazas personalizadas' : `${product} personalizado`;

    const updateSelection = () => {
        selectionText.textContent = `Vista: ${productLabel} ${activeColorName.toLowerCase()} con diseno ${activeDesign}`;

        const message = `Hola, quiero cotizar ${quoteSubject}. Me interesa color ${activeColorName} con diseno ${activeDesign}.`;
        quoteLink.href = `https://wa.me/525564442949?text=${encodeURIComponent(message)}`;
    };

    colorButtons.forEach((button) => {
        button.addEventListener('click', () => {
            activeColor = button.dataset.drinkwareColor;
            activeColorName = button.dataset.drinkwareColorName;
            preview.dataset.color = activeColor;
            colorButtons.forEach((item) => {
                item.classList.toggle('active', item === button);
            });
            updateSelection();
        });
    });

    designOptions.forEach((option) => {
        option.addEventListener('click', () => {
            activeDesign = option.dataset.drinkwareName;
            previewDesign.src = option.dataset.drinkwareDesign;
            designOptions.forEach((item) => {
                item.classList.toggle('active', item === option);
            });
            updateSelection();
        });
    });

    updateSelection();
});

document.querySelectorAll('[data-color-product-configurator]').forEach((configurator) => {
    const product = configurator.dataset.colorProduct || 'producto';
    const preview = configurator.querySelector('[data-color-product-preview]');
    const colorButtons = configurator.querySelectorAll('[data-color-product-color]');
    const selectionText = configurator.querySelector('[data-color-product-selection]');
    const quoteLink = configurator.querySelector('[data-color-product-quote]');
    const activeColorButton = Array.from(colorButtons).find((button) => button.classList.contains('active')) || colorButtons[0];

    let activeColor = activeColorButton?.dataset.colorProductColor || 'white';
    let activeColorName = activeColorButton?.dataset.colorProductColorName || 'Blanco';

    const productLabel = product.charAt(0).toUpperCase() + product.slice(1);

    const updateSelection = () => {
        selectionText.textContent = `Vista: ${productLabel} ${activeColorName.toLowerCase()}`;

        const message = `Hola, quiero cotizar ${product}. Me interesa color ${activeColorName}.`;
        quoteLink.href = `https://wa.me/525564442949?text=${encodeURIComponent(message)}`;
    };

    colorButtons.forEach((button) => {
        button.addEventListener('click', () => {
            activeColor = button.dataset.colorProductColor;
            activeColorName = button.dataset.colorProductColorName;
            preview.dataset.color = activeColor;
            colorButtons.forEach((item) => {
                item.classList.toggle('active', item === button);
            });
            updateSelection();
        });
    });

    updateSelection();
});

const laserAssets = {
    thermo: {
        src: '/images/laser-thermo.png',
        alt: 'Mockup de termo para grabado laser',
        x: 50,
        y: 47,
        size: 24,
    },
    case: {
        src: '/images/laser-phone-case.png',
        alt: 'Mockup de carcasa para grabado laser',
        x: 50,
        y: 49,
        size: 25,
    },
    patch: {
        src: '/images/laser-shirt-patch.png',
        alt: 'Mockup de playera con parche para grabado laser',
        x: 67,
        y: 61,
        size: 22,
    },
};

document.querySelectorAll('[data-laser-tool]').forEach((tool) => {
    const stage = tool.querySelector('[data-laser-stage]');
    const surfacePreview = tool.querySelector('[data-laser-preview]');
    const logoInput = tool.querySelector('[data-laser-logo-input]');
    const logoPreview = tool.querySelector('[data-laser-logo-preview]');
    const mark = tool.querySelector('[data-laser-mark]');
    const textInput = tool.querySelector('[data-laser-text]');
    const textPreview = tool.querySelector('[data-laser-text-preview]');
    const sizeInput = tool.querySelector('[data-laser-size]');
    const rotateInput = tool.querySelector('[data-laser-rotate]');
    const resetButton = tool.querySelector('[data-laser-reset]');
    const surfaceButtons = tool.querySelectorAll('[data-laser-surface]');

    let activeSurface = 'thermo';
    let position = { x: laserAssets.thermo.x, y: laserAssets.thermo.y };
    let dragging = false;

    const updateLaserStyles = () => {
        stage.style.setProperty('--laser-x', `${position.x}%`);
        stage.style.setProperty('--laser-y', `${position.y}%`);
        stage.style.setProperty('--laser-size', `${sizeInput.value}%`);
        stage.style.setProperty('--laser-rotate', `${rotateInput.value}deg`);
    };

    const centerForSurface = (surface) => {
        const config = laserAssets[surface];

        position = { x: config.x, y: config.y };
        sizeInput.value = config.size;
        rotateInput.value = 0;
        updateLaserStyles();
    };

    const setSurface = (surface) => {
        const config = laserAssets[surface];

        activeSurface = surface;
        surfacePreview.src = config.src;
        surfacePreview.alt = config.alt;
        surfaceButtons.forEach((button) => {
            button.classList.toggle('active', button.dataset.laserSurface === surface);
        });
        centerForSurface(activeSurface);
    };

    const moveMark = (event) => {
        const rect = stage.getBoundingClientRect();

        position = {
            x: Math.min(88, Math.max(12, ((event.clientX - rect.left) / rect.width) * 100)),
            y: Math.min(88, Math.max(12, ((event.clientY - rect.top) / rect.height) * 100)),
        };
        updateLaserStyles();
    };

    surfaceButtons.forEach((button) => {
        button.addEventListener('click', () => setSurface(button.dataset.laserSurface));
    });

    logoInput.addEventListener('change', () => {
        const [file] = logoInput.files;

        if (!file) {
            return;
        }

        logoPreview.src = URL.createObjectURL(file);
        logoPreview.hidden = false;
        textPreview.hidden = true;
        updateLaserStyles();
    });

    textInput.addEventListener('input', () => {
        textPreview.textContent = textInput.value || 'TU TEXTO';

        if (!textInput.value.trim() && logoPreview.src) {
            return;
        }

        if (textInput.value.trim()) {
            logoPreview.hidden = true;
            textPreview.hidden = false;
        }
    });

    [sizeInput, rotateInput].forEach((input) => {
        input.addEventListener('input', updateLaserStyles);
    });

    resetButton.addEventListener('click', () => centerForSurface(activeSurface));

    mark.addEventListener('pointerdown', (event) => {
        dragging = true;
        mark.setPointerCapture(event.pointerId);
        moveMark(event);
    });

    mark.addEventListener('pointermove', (event) => {
        if (dragging) {
            moveMark(event);
        }
    });

    mark.addEventListener('pointerup', () => {
        dragging = false;
    });

    stage.addEventListener('pointerdown', (event) => {
        if (event.target === stage || event.target === surfacePreview) {
            moveMark(event);
        }
    });

    updateLaserStyles();
});
