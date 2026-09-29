@extends('layouts.app')

@section('title', 'Personalizador | ForjaLab')

@section('content')
    <section class="admin-section">
        <div class="container">
            <div class="admin-header">
                <div>
                    <div class="eyebrow">Produccion</div>
                    <h1 class="fw-bold mt-2 mb-0">Personalizador de productos</h1>
                </div>
                <a class="btn btn-outline-dark" href="{{ route('admin.catalog.index') }}"><i class="bi bi-bag-heart me-2"></i>Configurar productos</a>
            </div>

            @if ($products->isEmpty())
                <div class="panel-card text-center py-5">
                    <i class="bi bi-brush-fill fs-1 text-warning"></i>
                    <h2 class="h4 fw-bold mt-3">Aun no hay productos con foto</h2>
                    <p class="text-secondary mb-4">Sube una foto de portada o galeria para usarla como base del personalizador.</p>
                    <a class="btn btn-dark" href="{{ route('admin.catalog.index') }}"><i class="bi bi-arrow-right me-2"></i>Ir al catalogo</a>
                </div>
            @else
                <div class="personalizer-shell" data-personalizer>
                    <aside class="panel-card personalizer-controls">
                        <div class="form-section-title compact">
                            <i class="bi bi-sliders"></i>
                            <div>
                                <h2>Datos</h2>
                                <p>Elige producto y escribe el nombre.</p>
                            </div>
                        </div>

                        <label class="form-label" for="personalizer_product">Producto</label>
                        <select class="form-select mb-3" id="personalizer_product" data-product-select>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>

                        <label class="form-label">Foto base</label>
                        <button class="personalizer-photo-picker mb-3" type="button" data-bs-toggle="modal" data-bs-target="#personalizerGalleryModal" data-open-gallery>
                            <img src="" alt="" data-selected-image-preview>
                            <span>
                                <strong data-selected-image-label>Seleccionar foto</strong>
                                <small>Portada y galeria interna del producto</small>
                            </span>
                            <i class="bi bi-images"></i>
                        </button>
                        <a class="btn btn-outline-dark w-100 mb-3" href="#" data-edit-product>
                            <i class="bi bi-cloud-upload me-2"></i>Subir fotos a este producto
                        </a>

                        <label class="form-label" for="personalizer_upload">O subir foto temporal</label>
                        <input class="form-control mb-3" id="personalizer_upload" type="file" accept="image/*" data-upload>

                        <label class="form-label" for="personalizer_name">Nombre</label>
                        <input class="form-control form-control-lg mb-3" id="personalizer_name" value="ForjaLab" maxlength="60" data-name-input>

                        <div class="personalizer-method mb-3" data-method-pill>
                            <i class="bi bi-lightning-charge-fill"></i>
                            <span>Metodo</span>
                        </div>

                        <div class="row g-2 mb-3" data-sublimation-controls>
                            <div class="col-12">
                                <label class="form-label" for="personalizer_font">Tipografia</label>
                                <select class="form-select" id="personalizer_font" data-font-family data-font-select>
                                    @foreach (['Montserrat', 'Arial', 'Helvetica', 'Georgia', 'Times New Roman', 'Verdana', 'Trebuchet MS', 'Tahoma', 'Courier New', 'Lucida Console', 'Impact', 'Arial Black', 'Palatino Linotype', 'Garamond', 'Bookman Old Style', 'Cambria', 'Candara', 'Century Gothic', 'Consolas', 'Franklin Gothic Medium', 'Gill Sans', 'Segoe UI', 'Optima', 'Didot', 'Baskerville', 'Copperplate', 'Brush Script MT', 'Lucida Handwriting', 'Comic Sans MS'] as $font)
                                        <option value="{{ $font }}">{{ $font }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label" for="personalizer_color">Color</label>
                                <input class="form-control form-control-color w-100" id="personalizer_color" type="color" data-color>
                            </div>
                            <div class="col-6">
                                <label class="form-label" for="personalizer_size">Tamano</label>
                                <input class="form-control" id="personalizer_size" type="number" min="12" max="220" data-font-size>
                            </div>
                        </div>

                        <div class="personalizer-position">
                            <div>
                                <label class="form-label" for="personalizer_x">X</label>
                                <input class="form-range" id="personalizer_x" type="range" min="0" max="100" data-x>
                            </div>
                            <div>
                                <label class="form-label" for="personalizer_y">Y</label>
                                <input class="form-range" id="personalizer_y" type="range" min="0" max="100" data-y>
                            </div>
                            <div>
                                <label class="form-label" for="personalizer_width">Ancho</label>
                                <input class="form-range" id="personalizer_width" type="range" min="8" max="100" data-width>
                            </div>
                            <div>
                                <label class="form-label" for="personalizer_height">Alto</label>
                                <input class="form-range" id="personalizer_height" type="range" min="4" max="100" data-height>
                            </div>
                            <div>
                                <label class="form-label" for="personalizer_rotation">Rotacion <span data-rotation-label>0°</span></label>
                                <input class="form-range" id="personalizer_rotation" type="range" min="-180" max="180" data-rotation>
                            </div>
                        </div>

                        <div class="personalizer-layers mt-3">
                            <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                                <strong>Capas extra</strong>
                                <button class="btn btn-sm btn-outline-dark" type="button" data-add-extra-text>
                                    <i class="bi bi-plus-circle me-1"></i>Texto
                                </button>
                            </div>
                            <div class="personalizer-layer-list" data-extra-text-list></div>
                            <label class="btn btn-outline-dark w-100 mt-2" for="personalizer_logo_upload">
                                <i class="bi bi-image me-2"></i>Agregar logo o imagen
                            </label>
                            <input class="visually-hidden" id="personalizer_logo_upload" type="file" accept="image/*" multiple data-logo-upload>
                            <div class="form-text">Los logos se integran al archivo final; la descarga siempre sale en PNG.</div>
                            <div class="personalizer-layer-list mt-2" data-logo-list></div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button class="btn btn-dark btn-lg" type="button" data-download><i class="bi bi-download me-2"></i>Descargar PNG</button>
                            <button class="btn btn-outline-dark" type="button" data-reset><i class="bi bi-arrow-counterclockwise me-2"></i>Volver al preset</button>
                        </div>
                    </aside>

                    <div class="panel-card personalizer-preview-card">
                        <div class="personalizer-preview-head">
                            <div>
                                <strong data-preview-title>Producto</strong>
                                <small>La imagen se exporta exactamente como aparece aqui.</small>
                            </div>
                            <span data-output-size></span>
                        </div>
                        <div class="personalizer-canvas-wrap">
                            <canvas data-canvas></canvas>
                        </div>
                    </div>

                    <div class="modal fade" id="personalizerGalleryModal" tabindex="-1" aria-labelledby="personalizerGalleryTitle" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <div>
                                        <h2 class="modal-title h5 fw-bold" id="personalizerGalleryTitle">Elegir foto base</h2>
                                        <small class="text-secondary" data-gallery-product-name></small>
                                    </div>
                                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="personalizer-gallery-grid" data-image-grid></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <style>
        .personalizer-shell{display:grid;grid-template-columns:minmax(280px,360px) 1fr;gap:1.25rem;align-items:start}.personalizer-photo-picker{display:grid;grid-template-columns:74px 1fr auto;align-items:center;gap:.75rem;width:100%;padding:.55rem;text-align:left;background:#fff;border:1px solid rgba(64,40,24,.16);border-radius:.85rem}.personalizer-photo-picker img{width:74px;aspect-ratio:1;object-fit:cover;background:#eee7df;border-radius:.65rem}.personalizer-photo-picker strong,.personalizer-photo-picker small{display:block}.personalizer-photo-picker small{color:var(--muted);font-size:.72rem}.personalizer-photo-picker>i{color:#c76312;font-size:1.25rem}.personalizer-method{display:flex;align-items:center;gap:.65rem;padding:.85rem 1rem;color:#2f2117;background:#fff4de;border:1px solid #ecd6af;border-radius:.8rem;font-weight:900}.personalizer-method i{color:#c76312}.personalizer-position,.personalizer-layers{display:grid;gap:.6rem;padding:1rem;background:#faf7f2;border:1px solid rgba(64,40,24,.1);border-radius:.9rem}.personalizer-position .form-label{display:flex;justify-content:space-between;margin-bottom:.2rem;font-size:.75rem;font-weight:900}.personalizer-layer-list{display:grid;gap:.6rem}.personalizer-layer-card{padding:.75rem;background:#fff;border:1px solid rgba(64,40,24,.12);border-radius:.75rem}.personalizer-layer-card header{display:flex;align-items:center;justify-content:space-between;gap:.5rem;margin-bottom:.6rem}.personalizer-layer-card header strong{font-size:.82rem}.personalizer-layer-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.5rem}.personalizer-layer-grid label{font-size:.68rem;font-weight:900;color:var(--muted)}.personalizer-layer-grid input,.personalizer-layer-grid select{min-width:0}.personalizer-layer-slider{grid-column:1/-1}.personalizer-layer-slider span{display:flex;justify-content:space-between;margin-bottom:.1rem;font-size:.68rem;font-weight:900;color:var(--muted)}.personalizer-layer-slider input{width:100%}.personalizer-logo-thumb{width:42px;height:42px;object-fit:contain;background:#f3eee7;border-radius:.45rem}.personalizer-font-select+.select2-container{width:100%!important}.personalizer-font-select+.select2-container .select2-selection--single{height:38px;border:1px solid #dee2e6;border-radius:.375rem}.personalizer-font-select+.select2-container .select2-selection__rendered{line-height:36px!important}.personalizer-font-select+.select2-container .select2-selection__arrow{height:36px}.personalizer-preview-card{position:relative;min-width:0;align-self:start;z-index:2;will-change:transform}.personalizer-preview-head{display:flex;justify-content:space-between;gap:1rem;margin-bottom:1rem}.personalizer-preview-head strong,.personalizer-preview-head small{display:block}.personalizer-preview-head small{color:var(--muted)}.personalizer-preview-head span{color:var(--muted);font-size:.75rem;font-weight:800}.personalizer-canvas-wrap{display:grid;place-items:center;min-height:62vh;padding:1rem;background:#16120f;border-radius:1rem}.personalizer-canvas-wrap canvas{max-width:100%;max-height:calc(100vh - 14rem);background:#fff;box-shadow:0 1rem 2.5rem rgba(0,0,0,.26)}.personalizer-gallery-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:1rem}.personalizer-gallery-option{overflow:hidden;padding:0;text-align:left;background:#fff;border:2px solid transparent;border-radius:.9rem;box-shadow:0 .6rem 1.4rem rgba(57,36,22,.08)}.personalizer-gallery-option.is-active{border-color:#d76b19}.personalizer-gallery-option img{display:block;width:100%;aspect-ratio:4/3;object-fit:cover;background:#eee7df}.personalizer-gallery-option span{display:block;padding:.65rem .75rem;font-weight:900}@media(max-width:1199.98px){.personalizer-shell{grid-template-columns:1fr}.personalizer-preview-card{transform:none!important}.personalizer-canvas-wrap{min-height:50vh}.personalizer-canvas-wrap canvas{max-height:72vh}}@media(max-width:575.98px){.personalizer-gallery-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:.65rem}.personalizer-photo-picker{grid-template-columns:60px 1fr auto}.personalizer-photo-picker img{width:60px}}
    </style>

    <script>
        (() => {
            const root = document.querySelector('[data-personalizer]');
            if (!root) return;

            const templates = @json($templates);
            const byId = new Map(templates.map((template) => [String(template.id), template]));
            const state = { template: templates[0], imageUrl: null, image: null, uploadedUrl: null, extraTexts: [], logos: [] };
            const canvas = root.querySelector('[data-canvas]');
            const context = canvas.getContext('2d');
            const previewCard = root.querySelector('.personalizer-preview-card');
            const productSelect = root.querySelector('[data-product-select]');
            const imageGrid = root.querySelector('[data-image-grid]');
            const selectedImagePreview = root.querySelector('[data-selected-image-preview]');
            const selectedImageLabel = root.querySelector('[data-selected-image-label]');
            const galleryProductName = root.querySelector('[data-gallery-product-name]');
            const editProduct = root.querySelector('[data-edit-product]');
            const nameInput = root.querySelector('[data-name-input]');
            const fontFamily = root.querySelector('[data-font-family]');
            const fontSize = root.querySelector('[data-font-size]');
            const color = root.querySelector('[data-color]');
            const methodPill = root.querySelector('[data-method-pill]');
            const sublimationControls = root.querySelector('[data-sublimation-controls]');
            const title = root.querySelector('[data-preview-title]');
            const outputSize = root.querySelector('[data-output-size]');
            const fields = {
                x: root.querySelector('[data-x]'),
                y: root.querySelector('[data-y]'),
                width: root.querySelector('[data-width]'),
                height: root.querySelector('[data-height]'),
                rotation: root.querySelector('[data-rotation]'),
            };
            const rotationLabel = root.querySelector('[data-rotation-label]');
            const extraTextList = root.querySelector('[data-extra-text-list]');
            const logoList = root.querySelector('[data-logo-list]');

            const galleryModal = document.getElementById('personalizerGalleryModal');
            const fontChoices = ['Montserrat', 'Arial', 'Helvetica', 'Georgia', 'Times New Roman', 'Verdana', 'Trebuchet MS', 'Tahoma', 'Courier New', 'Lucida Console', 'Impact', 'Arial Black', 'Palatino Linotype', 'Garamond', 'Bookman Old Style', 'Cambria', 'Candara', 'Century Gothic', 'Consolas', 'Franklin Gothic Medium', 'Gill Sans', 'Segoe UI', 'Optima', 'Didot', 'Baskerville', 'Copperplate', 'Brush Script MT', 'Lucida Handwriting', 'Comic Sans MS'];
            let previewFrame = null;
            const syncPreviewPosition = () => {
                previewFrame = null;
                if (window.innerWidth < 1200) {
                    previewCard.style.transform = '';
                    return;
                }
                const offset = 108;
                const rootTop = root.getBoundingClientRect().top + window.scrollY;
                const desired = window.scrollY + offset - rootTop;
                const max = Math.max(0, root.offsetHeight - previewCard.offsetHeight);
                const y = Math.min(Math.max(0, desired), max);
                previewCard.style.transform = `translateY(${Math.round(y)}px)`;
            };
            const queuePreviewPosition = () => {
                if (previewFrame) return;
                previewFrame = window.requestAnimationFrame(syncPreviewPosition);
            };
            const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;',
            })[char]);
            const initFontSelects = (scope = root) => {
                scope.querySelectorAll('[data-font-select]').forEach((select) => {
                    if (window.$?.fn?.select2) {
                        const $select = window.$(select);
                        if ($select.data('select2')) return;
                        $select.addClass('personalizer-font-select').select2({
                            width: '100%',
                            placeholder: 'Buscar tipografia',
                            dropdownParent: window.$(select).closest('.personalizer-layer-card, .personalizer-controls'),
                            language: { noResults: () => 'Sin resultados' },
                        });
                        return;
                    }

                    if (select.dataset.fontFallback === '1') return;
                    select.dataset.fontFallback = '1';
                    const search = document.createElement('input');
                    search.className = 'form-control form-control-sm mb-1';
                    search.type = 'search';
                    search.placeholder = 'Buscar tipografia';
                    search.autocomplete = 'off';
                    search.addEventListener('input', () => {
                        const term = search.value.trim().toLowerCase();
                        [...select.options].forEach((option) => {
                            option.hidden = term && !option.text.toLowerCase().includes(term);
                        });
                    });
                    select.insertAdjacentElement('beforebegin', search);
                });
            };

            const loadImage = (url) => new Promise((resolve, reject) => {
                const image = new Image();
                image.onload = () => resolve(image);
                image.onerror = reject;
                image.src = url;
            });

            const fitText = (text, maxWidth, baseSize, family) => {
                let size = Number.parseInt(baseSize, 10) || 58;
                context.font = `800 ${size}px ${family}`;
                while (size > 10 && context.measureText(text).width > maxWidth) {
                    size -= 2;
                    context.font = `800 ${size}px ${family}`;
                }
                return size;
            };

            const drawStyledText = ({ text, x, y, maxWidth, height, size, family, colorValue, rotation, method }) => {
                const finalText = (text || '').trim();
                if (!finalText) return;
                const fitSize = fitText(finalText, maxWidth, size, family);
                const centerY = y + Math.min(fitSize * .34, height * .22);
                context.save();
                context.translate(x, centerY);
                context.rotate((Number(rotation) || 0) * Math.PI / 180);
                context.textAlign = 'center';
                context.textBaseline = 'middle';
                context.font = `800 ${fitSize}px ${family}`;

                if (method === 'laser') {
                    context.globalAlpha = .82;
                    context.lineWidth = Math.max(1, fitSize * .035);
                    context.strokeStyle = 'rgba(245,241,229,.78)';
                    context.shadowColor = 'rgba(255,255,255,.22)';
                    context.shadowBlur = Math.max(1, fitSize * .018);
                    context.shadowOffsetX = -Math.max(1, fitSize * .012);
                    context.shadowOffsetY = -Math.max(1, fitSize * .012);
                    context.strokeText(finalText, 0, 0);
                    context.fillStyle = 'rgba(196,190,176,.86)';
                    context.fillText(finalText, 0, 0);
                    context.globalAlpha = .28;
                    context.strokeStyle = 'rgba(52,48,42,.45)';
                    context.shadowColor = 'rgba(0,0,0,.28)';
                    context.shadowBlur = 0;
                    context.shadowOffsetX = Math.max(1, fitSize * .012);
                    context.shadowOffsetY = Math.max(1, fitSize * .012);
                    context.strokeText(finalText, 0, 0);
                    context.fillText(finalText, 0, 0);
                } else {
                    context.fillStyle = colorValue || '#2b2118';
                    context.shadowColor = 'rgba(255,255,255,.34)';
                    context.shadowBlur = 1;
                    context.fillText(finalText, 0, 0);
                }
                context.restore();
            };

            const drawText = () => {
                const template = state.template;
                const box = {
                    x: (Number(fields.x.value) / 100) * canvas.width,
                    y: (Number(fields.y.value) / 100) * canvas.height,
                    width: (Number(fields.width.value) / 100) * canvas.width,
                    height: (Number(fields.height.value) / 100) * canvas.height,
                };
                drawStyledText({
                    text: nameInput.value || 'Nombre',
                    x: box.x,
                    y: box.y,
                    maxWidth: box.width,
                    height: box.height,
                    size: template.method === 'laser' ? template.fontSize : fontSize.value,
                    family: template.method === 'laser' ? 'Georgia' : fontFamily.value,
                    colorValue: color.value || template.textColor,
                    rotation: fields.rotation.value,
                    method: template.method,
                });
            };

            const drawExtraLayers = () => {
                state.extraTexts.forEach((layer) => {
                    drawStyledText({
                        text: layer.text,
                        x: (layer.x / 100) * canvas.width,
                        y: (layer.y / 100) * canvas.height,
                        maxWidth: (layer.width / 100) * canvas.width,
                        height: Math.max(24, layer.size * 1.4),
                        size: layer.size,
                        family: layer.family,
                        colorValue: layer.color,
                        rotation: layer.rotation,
                        method: state.template.method,
                    });
                });
                state.logos.forEach((layer) => {
                    if (!layer.image) return;
                    const width = (layer.width / 100) * canvas.width;
                    const height = width * (layer.image.naturalHeight / layer.image.naturalWidth);
                    const x = (layer.x / 100) * canvas.width;
                    const y = (layer.y / 100) * canvas.height;
                    context.save();
                    context.translate(x, y);
                    context.rotate((Number(layer.rotation) || 0) * Math.PI / 180);
                    context.globalAlpha = Math.max(0, Math.min(100, layer.opacity)) / 100;
                    context.drawImage(layer.image, -width / 2, -height / 2, width, height);
                    context.restore();
                });
            };

            const render = () => {
                if (!state.image) {
                    canvas.width = 900;
                    canvas.height = 620;
                    context.clearRect(0, 0, canvas.width, canvas.height);
                    context.fillStyle = '#16120f';
                    context.fillRect(0, 0, canvas.width, canvas.height);
                    context.fillStyle = '#fff';
                    context.font = '800 24px Montserrat, Arial, sans-serif';
                    context.textAlign = 'center';
                    context.fillText('Selecciona una foto base', canvas.width / 2, canvas.height / 2);
                    outputSize.textContent = '';
                    return;
                }
                canvas.width = state.image.naturalWidth;
                canvas.height = state.image.naturalHeight;
                context.clearRect(0, 0, canvas.width, canvas.height);
                context.drawImage(state.image, 0, 0);
                drawText();
                drawExtraLayers();
                title.textContent = state.template.name;
                outputSize.textContent = `${canvas.width} x ${canvas.height}px`;
            };

            const id = () => `${Date.now()}-${Math.random().toString(16).slice(2)}`;
            const renderExtraTextList = () => {
                extraTextList.innerHTML = state.extraTexts.map((layer) => `
                    <article class="personalizer-layer-card" data-extra-text-id="${layer.id}">
                        <header><strong>Texto extra</strong><button class="btn btn-sm btn-outline-danger" type="button" data-remove-extra-text="${layer.id}"><i class="bi bi-trash"></i></button></header>
                        <input class="form-control form-control-sm mb-2" value="${escapeHtml(layer.text)}" data-extra-field="text" placeholder="Texto">
                        <div class="personalizer-layer-grid">
                            <label>Tipografia <select class="form-select form-select-sm" data-extra-field="family" data-font-select>${fontChoices.map((font) => `<option value="${escapeHtml(font)}" ${font === layer.family ? 'selected' : ''}>${escapeHtml(font)}</option>`).join('')}</select></label>
                            <label>Color <input class="form-control form-control-sm form-control-color w-100" type="color" value="${layer.color}" data-extra-field="color"></label>
                            <label class="personalizer-layer-slider"><span>X <b>${layer.x}</b></span><input class="form-range" type="range" min="0" max="100" value="${layer.x}" data-extra-field="x"></label>
                            <label class="personalizer-layer-slider"><span>Y <b>${layer.y}</b></span><input class="form-range" type="range" min="0" max="100" value="${layer.y}" data-extra-field="y"></label>
                            <label class="personalizer-layer-slider"><span>Ancho <b>${layer.width}</b></span><input class="form-range" type="range" min="8" max="100" value="${layer.width}" data-extra-field="width"></label>
                            <label class="personalizer-layer-slider"><span>Tamano <b>${layer.size}</b></span><input class="form-range" type="range" min="10" max="220" value="${layer.size}" data-extra-field="size"></label>
                            <label class="personalizer-layer-slider"><span>Rotacion <b>${layer.rotation}°</b></span><input class="form-range" type="range" min="-180" max="180" value="${layer.rotation}" data-extra-field="rotation"></label>
                        </div>
                    </article>
                `).join('');
                initFontSelects(extraTextList);
                queuePreviewPosition();
            };

            const renderLogoList = () => {
                logoList.innerHTML = state.logos.map((layer) => `
                    <article class="personalizer-layer-card" data-logo-id="${layer.id}">
                        <header><span class="d-flex align-items-center gap-2"><img class="personalizer-logo-thumb" src="${layer.url}" alt=""><strong>${escapeHtml(layer.name)}</strong></span><button class="btn btn-sm btn-outline-danger" type="button" data-remove-logo="${layer.id}"><i class="bi bi-trash"></i></button></header>
                        <div class="personalizer-layer-grid">
                            <label class="personalizer-layer-slider"><span>X <b>${layer.x}</b></span><input class="form-range" type="range" min="0" max="100" value="${layer.x}" data-logo-field="x"></label>
                            <label class="personalizer-layer-slider"><span>Y <b>${layer.y}</b></span><input class="form-range" type="range" min="0" max="100" value="${layer.y}" data-logo-field="y"></label>
                            <label class="personalizer-layer-slider"><span>Ancho <b>${layer.width}</b></span><input class="form-range" type="range" min="2" max="100" value="${layer.width}" data-logo-field="width"></label>
                            <label class="personalizer-layer-slider"><span>Rotacion <b>${layer.rotation}°</b></span><input class="form-range" type="range" min="-180" max="180" value="${layer.rotation}" data-logo-field="rotation"></label>
                            <label class="personalizer-layer-slider"><span>Opacidad <b>${layer.opacity}%</b></span><input class="form-range" type="range" min="0" max="100" value="${layer.opacity}" data-logo-field="opacity"></label>
                        </div>
                    </article>
                `).join('');
                queuePreviewPosition();
            };

            const refreshSelectedPhoto = () => {
                const index = Math.max(0, state.template.images.indexOf(state.imageUrl));
                selectedImagePreview.src = state.imageUrl || '';
                selectedImagePreview.alt = state.imageUrl ? `Foto ${index + 1} de ${state.template.name}` : '';
                selectedImageLabel.textContent = state.imageUrl ? `Foto ${index + 1}` : 'Seleccionar foto';
                galleryProductName.textContent = state.template.name;
                imageGrid.querySelectorAll('[data-image-url]').forEach((button) => {
                    button.classList.toggle('is-active', button.dataset.imageUrl === state.imageUrl);
                });
            };

            const renderGallery = () => {
                imageGrid.innerHTML = state.template.images.length
                    ? state.template.images.map((url, index) => `
                    <button class="personalizer-gallery-option" type="button" data-image-url="${url}">
                        <img src="${url}" alt="Foto ${index + 1} de ${state.template.name}" loading="lazy">
                        <span>Foto ${index + 1}</span>
                    </button>
                `).join('')
                    : '<div class="alert alert-light mb-0">Este producto todavia no tiene fotos de portada o galeria.</div>';
                refreshSelectedPhoto();
            };

            const applyTemplate = async (template, keepImage = false) => {
                state.template = template;
                title.textContent = template.name;
                editProduct.href = template.editUrl;
                methodPill.innerHTML = template.method === 'laser'
                    ? '<i class="bi bi-lightning-charge-fill"></i><span>Grabado laser</span>'
                    : '<i class="bi bi-droplet-half"></i><span>Sublimado</span>';
                sublimationControls.hidden = template.method === 'laser';
                fontFamily.value = template.fontFamily || 'Montserrat';
                fontSize.value = template.fontSize || 58;
                color.value = template.textColor || '#2b2118';
                fields.x.value = template.x ?? 50;
                fields.y.value = template.y ?? 52;
                fields.width.value = template.width ?? 34;
                fields.height.value = template.height ?? 12;
                fields.rotation.value = template.rotation ?? 0;
                rotationLabel.textContent = `${fields.rotation.value}°`;

                if (!keepImage || !state.imageUrl) {
                    state.imageUrl = template.images[0] || null;
                }
                state.image = null;
                renderGallery();
                if (state.imageUrl) {
                    try {
                        state.image = await loadImage(state.imageUrl);
                    } catch (error) {
                        console.error('No se pudo cargar la foto base.', error);
                    }
                }
                render();
            };

            productSelect.addEventListener('change', () => {
                const template = byId.get(productSelect.value);
                if (template) applyTemplate(template);
            });
            imageGrid.addEventListener('click', async (event) => {
                const button = event.target.closest('[data-image-url]');
                if (!button) return;
                state.imageUrl = button.dataset.imageUrl;
                state.image = null;
                refreshSelectedPhoto();
                try {
                    state.image = await loadImage(state.imageUrl);
                    render();
                    window.bootstrap?.Modal.getInstance(galleryModal)?.hide();
                } catch (error) {
                    console.error('No se pudo cargar la foto base.', error);
                    render();
                }
            });
            root.querySelector('[data-upload]').addEventListener('change', async (event) => {
                const file = event.target.files?.[0];
                if (!file) return;
                if (state.uploadedUrl) URL.revokeObjectURL(state.uploadedUrl);
                state.uploadedUrl = URL.createObjectURL(file);
                state.imageUrl = state.uploadedUrl;
                state.image = await loadImage(state.uploadedUrl);
                selectedImagePreview.src = state.uploadedUrl;
                selectedImagePreview.alt = file.name;
                selectedImageLabel.textContent = 'Foto temporal';
                imageGrid.querySelectorAll('[data-image-url]').forEach((button) => button.classList.remove('is-active'));
                render();
            });
            root.addEventListener('input', (event) => {
                if (event.target.matches('[data-name-input], [data-font-family], [data-font-size], [data-color], [data-x], [data-y], [data-width], [data-height], [data-rotation]')) {
                    if (event.target.matches('[data-rotation]')) {
                        rotationLabel.textContent = `${event.target.value}°`;
                    }
                    render();
                }
            });
            root.addEventListener('change', (event) => {
                if (event.target.matches('[data-font-family]')) {
                    render();
                }
                if (event.target.matches('[data-extra-field="family"]')) {
                    const card = event.target.closest('[data-extra-text-id]');
                    const layer = state.extraTexts.find((item) => item.id === card?.dataset.extraTextId);
                    if (!layer) return;
                    layer.family = event.target.value;
                    render();
                }
            });
            root.querySelector('[data-add-extra-text]').addEventListener('click', () => {
                state.extraTexts.push({
                    id: id(),
                    text: 'Texto extra',
                    x: 50,
                    y: 62,
                    width: 34,
                    size: Math.max(18, Number(fontSize.value) || 48),
                    family: fontFamily.value || 'Montserrat',
                    color: color.value || state.template.textColor || '#2b2118',
                    rotation: 0,
                });
                renderExtraTextList();
                render();
            });
            extraTextList.addEventListener('input', (event) => {
                const card = event.target.closest('[data-extra-text-id]');
                const field = event.target.dataset.extraField;
                if (!card || !field) return;
                const layer = state.extraTexts.find((item) => item.id === card.dataset.extraTextId);
                if (!layer) return;
                layer[field] = field === 'text' || field === 'color' || field === 'family' ? event.target.value : Number(event.target.value);
                event.target.closest('.personalizer-layer-slider')?.querySelector('b')?.replaceChildren(document.createTextNode(
                    field === 'rotation' ? `${event.target.value}°` : event.target.value
                ));
                render();
            });
            extraTextList.addEventListener('click', (event) => {
                const remove = event.target.closest('[data-remove-extra-text]');
                if (!remove) return;
                state.extraTexts = state.extraTexts.filter((layer) => layer.id !== remove.dataset.removeExtraText);
                renderExtraTextList();
                render();
            });
            root.querySelector('[data-logo-upload]').addEventListener('change', async (event) => {
                const files = [...(event.target.files || [])];
                for (const file of files) {
                    const url = URL.createObjectURL(file);
                    try {
                        const image = await loadImage(url);
                        state.logos.push({
                            id: id(),
                            name: file.name,
                            url,
                            image,
                            x: 50,
                            y: 44,
                            width: 20,
                            rotation: 0,
                            opacity: 100,
                        });
                    } catch (error) {
                        URL.revokeObjectURL(url);
                        console.error('No se pudo cargar el logo.', error);
                    }
                }
                event.target.value = '';
                renderLogoList();
                render();
            });
            logoList.addEventListener('input', (event) => {
                const card = event.target.closest('[data-logo-id]');
                const field = event.target.dataset.logoField;
                if (!card || !field) return;
                const layer = state.logos.find((item) => item.id === card.dataset.logoId);
                if (!layer) return;
                layer[field] = Number(event.target.value);
                event.target.closest('.personalizer-layer-slider')?.querySelector('b')?.replaceChildren(document.createTextNode(
                    field === 'rotation' ? `${event.target.value}°` : field === 'opacity' ? `${event.target.value}%` : event.target.value
                ));
                render();
            });
            logoList.addEventListener('click', (event) => {
                const remove = event.target.closest('[data-remove-logo]');
                if (!remove) return;
                const layer = state.logos.find((item) => item.id === remove.dataset.removeLogo);
                if (layer) URL.revokeObjectURL(layer.url);
                state.logos = state.logos.filter((item) => item.id !== remove.dataset.removeLogo);
                renderLogoList();
                render();
            });
            root.querySelector('[data-reset]').addEventListener('click', () => applyTemplate(state.template, true));
            root.querySelector('[data-download]').addEventListener('click', () => {
                render();
                const link = document.createElement('a');
                const product = state.template.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '') || 'producto';
                const name = (nameInput.value || 'nombre').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '') || 'nombre';
                link.download = `${product}-${name}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
            window.addEventListener('scroll', queuePreviewPosition, { passive: true });
            window.addEventListener('resize', queuePreviewPosition);

            applyTemplate(state.template);
            initFontSelects();
            queuePreviewPosition();
        })();
    </script>
@endsection



