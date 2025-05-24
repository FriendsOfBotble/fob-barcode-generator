@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="barcode-generator">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ trans('plugins/fob-barcode-generator::barcode-generator.generate.title') }}</h4>
                </div>
                <div class="card-body">
                    <form id="barcode-generator-form">
                        @csrf

                        <div class="mb-3 product-selector">
                            <label class="form-label">{{ trans('plugins/fob-barcode-generator::barcode-generator.generate.select_products') }}</label>
                            <select name="products[]" id="products-select" class="form-control" multiple required>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}"
                                        @if(isset($selectedProductIds) && in_array($product->id, $selectedProductIds)) selected @endif>
                                        {{ $product->name }}
                                        @if($product->sku)
                                            (SKU: {{ $product->sku }})
                                        @endif
                                        @if($product->barcode)
                                            (Barcode: {{ $product->barcode }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">{{ trans('plugins/fob-barcode-generator::barcode-generator.messages.no_products_selected') }}</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ trans('plugins/fob-barcode-generator::barcode-generator.generate.select_template') }}</label>
                            <select name="template_id" id="template-select" class="form-control" required>
                                <option value="">{{ trans('plugins/fob-barcode-generator::barcode-generator.messages.no_template_selected') }}</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}"
                                        @if(isset($selectedTemplateId) && $selectedTemplateId == $template->id)
                                            selected
                                        @elseif(!isset($selectedTemplateId) && $template->is_default)
                                            selected
                                        @endif>
                                        {{ $template->name }}
                                        @if($template->description)
                                            - {{ $template->description }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ trans('plugins/fob-barcode-generator::barcode-generator.generate.quantity') }}</label>
                            <input type="number" name="quantity" class="form-control" value="{{ $selectedQuantity ?? 1 }}" min="1" max="100" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex gap-2 generation-controls">
                                    <button type="button" id="preview-btn" class="btn btn-info">
                                        <x-core::icon name="ti ti-eye" />
                                        {{ trans('plugins/fob-barcode-generator::barcode-generator.generate.preview') }}
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <x-core::icon name="ti ti-barcode" />
                                        {{ trans('plugins/fob-barcode-generator::barcode-generator.generate.download_pdf') }}
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-end">
                                    <small class="text-muted">
                                        <span id="selected-products-count">0</span> {{ trans('plugins/fob-barcode-generator::barcode-generator.ui.products_selected') }},
                                        <span id="estimated-labels-count">0</span> {{ trans('plugins/fob-barcode-generator::barcode-generator.ui.labels_total') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 d-flex align-items-center gap-2">
                        <x-core::icon name="ti ti-eye" class="text-primary" />
                        {{ trans('plugins/fob-barcode-generator::barcode-generator.generate.preview') }}
                    </h4>
                    <div class="preview-actions d-none" id="preview-actions">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="refresh-preview-btn"
                                title="Refresh Preview" data-bs-toggle="tooltip">
                            <x-core::icon name="ti ti-refresh" />
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" id="fullscreen-preview-btn"
                                title="Full Preview" data-bs-toggle="tooltip">
                            <x-core::icon name="ti ti-maximize" />
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="preview-container" class="preview-container">
                        <div class="preview-placeholder">
                            <div class="preview-icon">
                                <x-core::icon name="ti ti-barcode" class="icon-xl" />
                            </div>
                            <h6 class="mb-2">{{ trans('plugins/fob-barcode-generator::barcode-generator.ui.live_preview') }}</h6>
                            <p class="small mb-3">{{ trans('plugins/fob-barcode-generator::barcode-generator.ui.select_products_template_preview') }}</p>
                            <div class="preview-hint">
                                <x-core::icon name="ti ti-info-circle" class="icon" />
                                <span>{{ trans('plugins/fob-barcode-generator::barcode-generator.ui.click_preview_details') }}</span>
                            </div>
                        </div>
                        <div class="preview-loading d-none" id="preview-loading-mini">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">{{ trans('plugins/fob-barcode-generator::barcode-generator.ui.loading') }}</span>
                            </div>
                            <div class="loading-text">{{ trans('plugins/fob-barcode-generator::barcode-generator.ui.generating_preview') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Preview Modal -->
    <div class="modal fade" id="preview-modal" tabindex="-1">
        <div class="modal-dialog modal-fullscreen-lg-down modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-light justify-content-between">
                    <div class="d-flex align-items-center">
                        <x-core::icon name="ti ti-eye" class="me-2 text-primary" />
                        <h5 class="modal-title mb-0">{{ trans('plugins/fob-barcode-generator::barcode-generator.generate.title') }} {{ trans('plugins/fob-barcode-generator::barcode-generator.generate.preview') }}</h5>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="preview-controls">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="zoom-out-btn"
                                    title="Zoom Out (Ctrl + -)" data-bs-toggle="tooltip">
                                <x-core::icon name="ti ti-zoom-out" />
                            </button>
                            <span class="zoom-level mx-2" id="zoom-level">100%</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="zoom-in-btn"
                                    title="Zoom In (Ctrl + +)" data-bs-toggle="tooltip">
                                <x-core::icon name="ti ti-zoom-in" />
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="zoom-reset-btn"
                                    title="Reset Zoom (Ctrl + 0)" data-bs-toggle="tooltip">
                                <x-core::icon name="ti ti-zoom-reset" />
                            </button>
                        </div>
                        <div class="vr"></div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="print-preview-btn"
                                title="Print Preview (Ctrl + P)" data-bs-toggle="tooltip">
                            <x-core::icon name="ti ti-printer" />
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info" id="fit-to-screen-btn"
                                title="Fit to Screen" data-bs-toggle="tooltip">
                            <x-core::icon name="ti ti-arrows-maximize" />
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-0 position-relative">
                    <div class="preview-loading d-none" id="preview-loading">
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <div class="text-center">
                                <div class="spinner-border text-primary mb-3" role="status">
                                    <span class="visually-hidden">{{ trans('plugins/fob-barcode-generator::barcode-generator.ui.loading') }}</span>
                                </div>
                                <p class="text-muted mb-2">{{ trans('plugins/fob-barcode-generator::barcode-generator.ui.generating_preview') }}</p>
                                <div class="progress" style="width: 200px; margin: 0 auto;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                                         role="progressbar" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="preview-iframe-container">
                        <iframe id="preview-iframe" class="preview-iframe"></iframe>
                        <div class="preview-overlay d-none" id="preview-overlay">
                            <div class="preview-overlay-content">
                                <x-core::icon name="ti ti-zoom-in" class="mb-2" />
                                <p class="mb-0">{{ trans('plugins/fob-barcode-generator::barcode-generator.ui.click_preview_details') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <div class="d-flex justify-content-between w-100 align-items-center">
                        <div class="preview-info d-flex align-items-center gap-3">
                            <div class="preview-stats-badge">
                                <x-core::icon name="ti ti-info-circle" class="me-1" />
                                <span id="preview-stats">Ready to generate labels</span>
                            </div>
                            <div class="preview-quality-indicator d-none" id="preview-quality">
                                <span class="badge bg-success">
                                    <x-core::icon name="ti ti-check" class="me-1" />
                                    High Quality
                                </span>
                            </div>
                        </div>
                        <div class="preview-actions d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <x-core::icon name="ti ti-x" />
                                Close
                            </button>
                            <button type="button" id="refresh-modal-preview" class="btn btn-outline-info">
                                <x-core::icon name="ti ti-refresh" />
                                Refresh
                            </button>
                            <button type="button" id="download-from-modal" class="btn btn-primary">
                                <x-core::icon name="ti ti-download" />
                                Download Labels
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('footer')
    <script>
        // Set up routes and messages for the external JavaScript
        window.BarcodeGeneratorConfig = {
            routes: {
                generate: '{{ route('barcode-generator.generate') }}',
                preview: '{{ route('barcode-generator.preview') }}'
            },
            messages: {
                noProductsSelected: '{{ trans('plugins/fob-barcode-generator::barcode-generator.messages.no_products_selected') }}',
                noTemplateSelected: '{{ trans('plugins/fob-barcode-generator::barcode-generator.messages.no_template_selected') }}',
                selectProducts: '{{ trans('plugins/fob-barcode-generator::barcode-generator.generate.select_products') }}',
                selectTemplate: '{{ trans('plugins/fob-barcode-generator::barcode-generator.generate.select_template') }}'
            },
            ui: {
                products_selected: '{{ trans('plugins/fob-barcode-generator::barcode-generator.ui.products_selected') }}',
                labels_total: '{{ trans('plugins/fob-barcode-generator::barcode-generator.ui.labels_total') }}',
                label_preview: '{{ trans('plugins/fob-barcode-generator::barcode-generator.ui.label_preview') }}',
                products: '{{ trans('plugins/fob-barcode-generator::barcode-generator.ui.products') }}',
                qty_each: '{{ trans('plugins/fob-barcode-generator::barcode-generator.ui.qty_each') }}',
                total_labels: '{{ trans('plugins/fob-barcode-generator::barcode-generator.ui.total_labels') }}',
                template: '{{ trans('plugins/fob-barcode-generator::barcode-generator.ui.template') }}',
                full_preview: '{{ trans('plugins/fob-barcode-generator::barcode-generator.ui.full_preview') }}',
                generate_now: '{{ trans('plugins/fob-barcode-generator::barcode-generator.ui.generate_now') }}',
                live_preview: '{{ trans('plugins/fob-barcode-generator::barcode-generator.ui.live_preview') }}',
                select_products_template_preview: '{{ trans('plugins/fob-barcode-generator::barcode-generator.ui.select_products_template_preview') }}',
                click_preview_details: '{{ trans('plugins/fob-barcode-generator::barcode-generator.ui.click_preview_details') }}',
                loading: '{{ trans('plugins/fob-barcode-generator::barcode-generator.ui.loading') }}',
                generating_preview: '{{ trans('plugins/fob-barcode-generator::barcode-generator.ui.generating_preview') }}',
                an_error_occurred: '{{ trans('plugins/fob-barcode-generator::barcode-generator.ui.an_error_occurred') }}'
            },
            preselected: {
                products: @json($selectedProductIds ?? []),
                templateId: {{ $selectedTemplateId ?? 'null' }},
                quantity: {{ $selectedQuantity ?? 1 }}
            }
        };
    </script>
@endpush
