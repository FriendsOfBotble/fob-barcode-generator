class BarcodeGenerator {
    constructor() {
        this.init();
    }

    init() {
        this.initializeSelectors();
        this.bindEvents();
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
            $('#preview-container').html('<p class="text-muted">Select products and template to see preview</p>');
            return;
        }

        // Show loading state
        $('#preview-container').html('<div class="text-center"><i class="ti ti-loader-2 spin"></i> Loading preview...</div>');

        // Generate mini preview
        this.generateMiniPreview(products, template, quantity);
    }

    generateMiniPreview(products, template, quantity) {
        const config = window.BarcodeGeneratorConfig || {};
        const selectedProducts = $('#products-select option:selected');
        const templateText = $('#template-select option:selected').text();

        let previewHtml = '<div class="preview-summary">';
        previewHtml += `<h6><i class="ti ti-barcode"></i> Preview</h6>`;
        previewHtml += `<p><strong>Template:</strong> ${templateText}</p>`;
        previewHtml += `<p><strong>Products:</strong> ${products.length}</p>`;
        previewHtml += `<p><strong>Quantity each:</strong> ${quantity}</p>`;
        previewHtml += `<p><strong>Total labels:</strong> ${products.length * quantity}</p>`;

        previewHtml += '<div class="mt-3"><strong>Selected Products:</strong></div>';
        previewHtml += '<div class="selected-products-list">';

        selectedProducts.each(function() {
            const productName = $(this).text();
            previewHtml += `<div class="product-item"><i class="ti ti-package"></i> ${productName}</div>`;
        });

        previewHtml += '</div>';
        previewHtml += '<div class="mt-3">';
        previewHtml += `<button type="button" id="quick-preview-btn" class="btn btn-sm btn-outline-primary">`;
        previewHtml += `<i class="ti ti-eye"></i> Quick Preview`;
        previewHtml += `</button>`;
        previewHtml += '</div>';
        previewHtml += '</div>';

        $('#preview-container').html(previewHtml);

        // Bind quick preview button
        $('#quick-preview-btn').on('click', () => {
            this.showPreview();
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
            let message = 'An error occurred';
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
