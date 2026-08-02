import 'bootstrap';

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
        quoteLink.href = `https://wa.me/?text=${encodeURIComponent(message)}`;
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
        quoteLink.href = `https://wa.me/?text=${encodeURIComponent(message)}`;
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
        quoteLink.href = `https://wa.me/?text=${encodeURIComponent(message)}`;
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
        quoteLink.href = `https://wa.me/?text=${encodeURIComponent(message)}`;
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
