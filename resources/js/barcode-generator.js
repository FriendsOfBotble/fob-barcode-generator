class BarcodeGenerator {
    constructor() {
        this.init();
    }

    init() {
        this.initializeSelectors();
        this.bindEvents();
        this.initializePreselectedValues();
    }

    initializeSelectors() {
        // Initialize Select2 for better UX
        if ($.fn.select2) {
            const config = window.BarcodeGeneratorConfig || {};

            $('#products-select').select2({
                placeholder: config.messages?.selectProducts || 'Select products...',
                allowClear: true,
                width: '100%'
            });

            $('#template-select').select2({
                placeholder: config.messages?.selectTemplate || 'Select template...',
                allowClear: true,
                width: '100%'
            });
        }
    }

    initializePreselectedValues() {
        // Update counters and preview for pre-selected values
        setTimeout(() => {
            this.updateProductCount();
            this.updateTemplatePreview();
            this.updatePreviewContainer();
        }, 100); // Small delay to ensure Select2 is initialized
    }

    bindEvents() {
        // Form submission
        $(document).on('submit', '#barcode-generator-form', (e) => {
            e.preventDefault();
            this.handleFormSubmission();
        });

        // Preview button
        $(document).on('click', '#preview-btn', () => {
            this.showPreview();
        });

        // Download from modal
        $(document).on('click', '#download-from-modal', () => {
            this.downloadFromModal();
        });

        // Template selection
        $(document).on('change', '#template-select', () => {
            this.updateTemplatePreview();
            this.updatePreviewContainer();
        });

        // Product selection
        $(document).on('change', '#products-select', () => {
            this.updateProductCount();
            this.updatePreviewContainer();
        });

        // Quantity change
        $(document).on('change', 'input[name="quantity"]', () => {
            this.updateEstimatedLabels();
            this.updatePreviewContainer();
        });

        // Enhanced preview controls
        $(document).on('click', '#refresh-preview-btn', () => {
            this.refreshPreview();
        });

        $(document).on('click', '#fullscreen-preview-btn', () => {
            this.showPreview();
        });

        // Modal preview controls
        $(document).on('click', '#zoom-in-btn', () => {
            this.zoomPreview(1.2);
        });

        $(document).on('click', '#zoom-out-btn', () => {
            this.zoomPreview(0.8);
        });

        $(document).on('click', '#zoom-reset-btn', () => {
            this.resetZoom();
        });

        $(document).on('click', '#fit-to-screen-btn', () => {
            this.fitToScreen();
        });

        $(document).on('click', '#print-preview-btn', () => {
            this.printPreview();
        });

        $(document).on('click', '#refresh-modal-preview', () => {
            this.refreshModalPreview();
        });

        // Modal events
        $('#preview-modal').on('shown.bs.modal', () => {
            this.onModalShown();
        });

        $('#preview-modal').on('hidden.bs.modal', () => {
            this.onModalHidden();
        });

        // Keyboard shortcuts
        $(document).on('keydown', (e) => {
            if ($('#preview-modal').hasClass('show')) {
                this.handleKeyboardShortcuts(e);
            }
        });

        // Initialize tooltips
        this.initializeTooltips();
    }

    handleFormSubmission() {
        if (!this.validateForm()) {
            return;
        }

        const config = window.BarcodeGeneratorConfig || {};
        const formData = $('#barcode-generator-form').serialize();
        const submitBtn = $('#barcode-generator-form button[type="submit"]');

        // Show loading state
        submitBtn.prop('disabled', true).html('<i class="ti ti-loader-2 spin"></i> Generating...');

        $.ajax({
            url: config.routes?.generate || '/admin/barcode-generator/generate',
            method: 'POST',
            data: formData,
            success: (response) => {
                if (response.error) {
                    this.showError(response.message);
                } else {
                    this.showSuccess(response.message);
                    // Download the file
                    if (response.data && response.data.download_url) {
                        window.open(response.data.download_url, '_blank');
                    }
                }
            },
            error: (xhr) => {
                this.handleAjaxError(xhr);
            },
            complete: () => {
                // Reset button state
                submitBtn.prop('disabled', false).html('<i class="ti ti-barcode"></i> Generate Labels');
            }
        });
    }

    showPreview() {
        if (!this.validateForm()) {
            return;
        }

        const config = window.BarcodeGeneratorConfig || {};
        const formData = $('#barcode-generator-form').serialize();
        const previewUrl = (config.routes?.preview || '/admin/barcode-generator/preview') + '?' + formData;

        // Show loading state
        this.showPreviewLoading();

        // Update preview stats
        this.updatePreviewStats();

        // Load preview
        $('#preview-iframe').attr('src', previewUrl);
        $('#preview-modal').modal('show');
    }

    downloadFromModal() {
        $('#barcode-generator-form').submit();
        $('#preview-modal').modal('hide');
    }

    validateForm() {
        const config = window.BarcodeGeneratorConfig || {};
        const products = $('#products-select').val();
        const template = $('#template-select').val();

        if (!products || products.length === 0) {
            this.showError(config.messages?.noProductsSelected || 'Please select at least one product.');
            return false;
        }

        if (!template) {
            this.showError(config.messages?.noTemplateSelected || 'Please select a template.');
            return false;
        }

        return true;
    }

    updateTemplatePreview() {
        const templateId = $('#template-select').val();
        if (!templateId) {
            $('#template-preview').html('<p class="text-muted">Select a template to see preview</p>');
            return;
        }

        // Show template info
        const templateText = $('#template-select option:selected').text();
        $('#template-preview').html(`<p class="text-info"><i class="ti ti-check"></i> ${templateText}</p>`);
    }

    updatePreviewContainer() {
        const products = $('#products-select').val();
        const template = $('#template-select').val();
        const quantity = parseInt($('input[name="quantity"]').val()) || 1;

        if (!products || products.length === 0 || !template) {
            this.showPreviewPlaceholder();
            $('#preview-actions').addClass('d-none');
            return;
        }

        // Show loading state
        this.showPreviewLoading();

        // Generate mini preview
        setTimeout(() => {
            this.generateMiniPreview(products, template, quantity);
            $('#preview-actions').removeClass('d-none');
        }, 300);
    }

    generateMiniPreview(products, template, quantity) {
        const config = window.BarcodeGeneratorConfig || {};
        const selectedProducts = $('#products-select option:selected');
        const templateText = $('#template-select option:selected').text();
        const totalLabels = products.length * quantity;

        let previewHtml = '<div class="preview-summary slide-in">';

        // Header
        const ui = config.ui || {};
        previewHtml += `<h6><svg class="icon me-2" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 5v14h18V5H3z"/><path d="M7 8v8"/><path d="M11 8v8"/><path d="M15 8v8"/></svg> ${ui.label_preview || 'Label Preview'}</h6>`;

        // Stats grid
        previewHtml += '<div class="preview-stats">';
        previewHtml += `<div class="stat-item">`;
        previewHtml += `<span class="stat-value">${products.length}</span>`;
        previewHtml += `<span class="stat-label">${ui.products || 'Products'}</span>`;
        previewHtml += `</div>`;
        previewHtml += `<div class="stat-item">`;
        previewHtml += `<span class="stat-value">${quantity}</span>`;
        previewHtml += `<span class="stat-label">${ui.qty_each || 'Qty Each'}</span>`;
        previewHtml += `</div>`;
        previewHtml += `<div class="stat-item">`;
        previewHtml += `<span class="stat-value">${totalLabels}</span>`;
        previewHtml += `<span class="stat-label">${ui.total_labels || 'Total Labels'}</span>`;
        previewHtml += `</div>`;
        previewHtml += '</div>';

        // Template info
        previewHtml += `<div class="mb-3">`;
        previewHtml += `<strong><svg class="icon me-1" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 12h8"/><path d="M8 16h8"/><path d="M8 8h8"/></svg> ${ui.template || 'Template'}:</strong> ${templateText}`;
        previewHtml += `</div>`;

        // Selected products list
        previewHtml += '<div class="selected-products-list">';
        selectedProducts.each(function() {
            const productText = $(this).text();
            const productName = productText.split('(')[0].trim();
            const productMeta = productText.includes('(') ? productText.substring(productText.indexOf('(')) : '';

            previewHtml += `<div class="product-item">`;
            previewHtml += `<svg class="icon me-2" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16.5 9.4L7.55 4.24C7.21 4.09 6.81 4.09 6.47 4.24L3.5 5.75C2.79 6.15 2.5 7.04 2.91 7.75L7.55 15.76C7.89 16.09 8.29 16.09 8.63 15.76L16.5 9.4Z"/><path d="M14.12 6.88L8.5 3.75L5.5 5.25"/><path d="M7.5 15.5V9.5"/></svg>`;
            previewHtml += `<div class="product-details">`;
            previewHtml += `<div class="product-name">${productName}</div>`;
            if (productMeta) {
                previewHtml += `<div class="product-meta">${productMeta}</div>`;
            }
            previewHtml += `</div>`;
            previewHtml += `</div>`;
        });
        previewHtml += '</div>';

        // Action buttons
        previewHtml += '<div class="preview-actions">';
        previewHtml += `<button type="button" id="quick-preview-btn" class="btn btn-primary btn-sm">`;
        previewHtml += `<svg class="icon me-1" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> ${ui.full_preview || 'Full Preview'}`;
        previewHtml += `</button>`;
        previewHtml += `<button type="button" id="generate-now-btn" class="btn btn-success btn-sm">`;
        previewHtml += `<svg class="icon me-1" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7,10 12,15 17,10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> ${ui.generate_now || 'Generate Now'}`;
        previewHtml += `</button>`;
        previewHtml += '</div>';

        previewHtml += '</div>';

        $('#preview-container').html(previewHtml);

        // Bind action buttons
        $('#quick-preview-btn').on('click', () => {
            this.showPreview();
        });

        $('#generate-now-btn').on('click', () => {
            $('#barcode-generator-form').submit();
        });
    }

    updateProductCount() {
        const products = $('#products-select').val();
        const count = products ? products.length : 0;

        $('#selected-products-count').text(count);
        this.updateEstimatedLabels();
    }

    updateEstimatedLabels() {
        const products = $('#products-select').val();
        const quantity = parseInt($('input[name="quantity"]').val()) || 1;
        const count = products ? products.length : 0;
        const totalLabels = count * quantity;

        $('#estimated-labels-count').text(totalLabels);
    }

    // Enhanced preview utility methods
    showPreviewPlaceholder() {
        const config = window.BarcodeGeneratorConfig || {};
        const ui = config.ui || {};
        const placeholderHtml = `
            <div class="preview-placeholder">
                <div class="preview-icon">
                    <svg class="icon-xl" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 5v14h18V5H3z"/>
                        <path d="M7 8v8"/>
                        <path d="M11 8v8"/>
                        <path d="M15 8v8"/>
                    </svg>
                </div>
                <h6 class="mb-2">${ui.live_preview || 'Live Preview'}</h6>
                <p class="small mb-3">${ui.select_products_template_preview || 'Select products and template to see preview'}</p>
                <div class="preview-hint">
                    <svg class="icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 16v-4"/>
                        <path d="M12 8h.01"/>
                    </svg>
                    <span>${ui.click_preview_details || 'Click Preview to see full details'}</span>
                </div>
            </div>
        `;
        $('#preview-container').html(placeholderHtml);
    }

    showPreviewLoading() {
        const config = window.BarcodeGeneratorConfig || {};
        const ui = config.ui || {};
        const loadingHtml = `
            <div class="preview-loading">
                <div class="text-center">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">${ui.loading || 'Loading...'}</span>
                    </div>
                    <p class="text-muted">${ui.generating_preview || 'Generating preview...'}</p>
                </div>
            </div>
        `;
        $('#preview-container').html(loadingHtml);
        $('#preview-container').addClass('loading');

        // Remove loading class after animation
        setTimeout(() => {
            $('#preview-container').removeClass('loading');
        }, 1000);
    }

    refreshPreview() {
        this.updatePreviewContainer();
    }

    // Modal preview methods
    currentZoom = 1;

    zoomPreview(factor) {
        this.currentZoom *= factor;

        // Limit zoom range
        if (this.currentZoom < 0.5) this.currentZoom = 0.5;
        if (this.currentZoom > 3) this.currentZoom = 3;

        const iframe = $('#preview-iframe');
        iframe.css('transform', `scale(${this.currentZoom})`);

        // Update zoom display
        $('#zoom-level').text(`${Math.round(this.currentZoom * 100)}%`);

        // Update button states
        $('#zoom-out-btn').prop('disabled', this.currentZoom <= 0.5);
        $('#zoom-in-btn').prop('disabled', this.currentZoom >= 3);
    }

    printPreview() {
        const iframe = document.getElementById('preview-iframe');
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.print();
        }
    }

    updatePreviewStats() {
        const products = $('#products-select').val();
        const quantity = parseInt($('input[name="quantity"]').val()) || 1;
        const totalLabels = products ? products.length * quantity : 0;

        $('#preview-stats').text(`${products ? products.length : 0} products, ${totalLabels} labels total`);
    }

    onModalShown() {
        // Reset zoom when modal is shown
        this.currentZoom = 1;
        $('#zoom-level').text('100%');
        $('#zoom-out-btn').prop('disabled', false);
        $('#zoom-in-btn').prop('disabled', false);

        // Hide loading after iframe loads
        const iframe = $('#preview-iframe');
        iframe.on('load', () => {
            $('#preview-loading').addClass('d-none');
        });
    }

    onModalHidden() {
        // Reset iframe src to stop any ongoing requests
        $('#preview-iframe').attr('src', 'about:blank');
        $('#preview-loading').removeClass('d-none');
    }

    // New enhanced methods
    resetZoom() {
        this.currentZoom = 1;
        const iframe = $('#preview-iframe');
        iframe.css('transform', 'scale(1)');
        $('#zoom-level').text('100%');
        $('#zoom-out-btn').prop('disabled', false);
        $('#zoom-in-btn').prop('disabled', false);
    }

    fitToScreen() {
        // Calculate optimal zoom to fit content
        const container = $('.preview-iframe-container');
        const iframe = $('#preview-iframe');

        if (container.length && iframe.length) {
            const containerWidth = container.width();
            const containerHeight = container.height();
            const iframeWidth = iframe[0].contentWindow?.document.body?.scrollWidth || 800;
            const iframeHeight = iframe[0].contentWindow?.document.body?.scrollHeight || 600;

            const scaleX = containerWidth / iframeWidth;
            const scaleY = containerHeight / iframeHeight;
            const optimalScale = Math.min(scaleX, scaleY, 1);

            this.currentZoom = optimalScale;
            iframe.css('transform', `scale(${optimalScale})`);
            $('#zoom-level').text(`${Math.round(optimalScale * 100)}%`);

            // Update button states
            $('#zoom-out-btn').prop('disabled', this.currentZoom <= 0.5);
            $('#zoom-in-btn').prop('disabled', this.currentZoom >= 3);
        }
    }

    refreshModalPreview() {
        const config = window.BarcodeGeneratorConfig || {};
        const formData = $('#barcode-generator-form').serialize();
        const previewUrl = (config.routes?.preview || '/admin/barcode-generator/preview') + '?' + formData;

        // Show loading
        $('#preview-loading').removeClass('d-none');

        // Reload iframe
        $('#preview-iframe').attr('src', previewUrl);

        // Update stats
        this.updatePreviewStats();
    }

    handleKeyboardShortcuts(e) {
        if (e.ctrlKey || e.metaKey) {
            switch(e.key) {
                case '=':
                case '+':
                    e.preventDefault();
                    this.zoomPreview(1.2);
                    break;
                case '-':
                    e.preventDefault();
                    this.zoomPreview(0.8);
                    break;
                case '0':
                    e.preventDefault();
                    this.resetZoom();
                    break;
                case 'p':
                    e.preventDefault();
                    this.printPreview();
                    break;
            }
        }

        if (e.key === 'Escape') {
            $('#preview-modal').modal('hide');
        }
    }

    initializeTooltips() {
        // Initialize Bootstrap tooltips
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    }

    showSuccess(message) {
        if (typeof Botble !== 'undefined' && Botble.showSuccess) {
            Botble.showSuccess(message);
        } else {
            alert(message);
        }
    }

    showError(message) {
        if (typeof Botble !== 'undefined' && Botble.showError) {
            Botble.showError(message);
        } else {
            alert(message);
        }
    }

    handleAjaxError(xhr) {
        if (typeof Botble !== 'undefined' && Botble.handleError) {
            Botble.handleError(xhr);
        } else {
            const config = window.BarcodeGeneratorConfig || {};
            const ui = config.ui || {};
            let message = ui.an_error_occurred || 'An error occurred';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            this.showError(message);
        }
    }
}

// Template management functionality
class TemplateManager {
    constructor() {
        this.bindEvents();
    }

    bindEvents() {
        // Template form submission
        $(document).on('submit', '#template-form', (e) => {
            this.handleTemplateSubmission(e);
        });

        // Paper size change
        $(document).on('change', 'select[name="paper_size"]', () => {
            this.updatePaperSizeFields();
        });

        // Field selection
        $(document).on('change', '.field-checkbox', () => {
            this.updateSelectedFields();
        });
    }

    handleTemplateSubmission(e) {
        // Add any custom validation or processing here
    }

    updatePaperSizeFields() {
        const paperSize = $('select[name="paper_size"]').val();

        // Update default values based on paper size
        const paperSizes = {
            'A4': { width: 210, height: 297 },
            'Letter': { width: 215.9, height: 279.4 },
            'P4': { width: 101.6, height: 152.4 },
            'thermal_4x6': { width: 101.6, height: 152.4 },
            'thermal_2x1': { width: 50.8, height: 25.4 }
        };

        if (paperSizes[paperSize]) {
            // You could update form fields here based on paper size
        }
    }

    updateSelectedFields() {
        const selectedFields = [];
        $('.field-checkbox:checked').each(function() {
            selectedFields.push($(this).val());
        });

        $('input[name="fields"]').val(JSON.stringify(selectedFields));
    }
}

// Initialize when document is ready
$(document).ready(function() {
    if ($('#barcode-generator-form').length) {
        new BarcodeGenerator();
    }

    if ($('#template-form').length) {
        new TemplateManager();
    }
});
