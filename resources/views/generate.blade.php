@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ trans('plugins/fob-barcode-generator::barcode-generator.generate.title') }}</h4>
                </div>
                <div class="card-body">
                    <form id="barcode-generator-form">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">{{ trans('plugins/fob-barcode-generator::barcode-generator.generate.select_products') }}</label>
                            <select name="products[]" id="products-select" class="form-control" multiple required>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">
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
                                    <option value="{{ $template->id }}" @if($template->is_default) selected @endif>
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
                            <input type="number" name="quantity" class="form-control" value="1" min="1" max="100" required>
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
                                        <span id="selected-products-count">0</span> products selected,
                                        <span id="estimated-labels-count">0</span> labels total
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
                <div class="card-header">
                    <h4 class="card-title">{{ trans('plugins/fob-barcode-generator::barcode-generator.generate.preview') }}</h4>
                </div>
                <div class="card-body">
                    <div id="preview-container" class="text-center">
                        <p class="text-muted">{{ trans('plugins/fob-barcode-generator::barcode-generator.generate.preview') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="preview-modal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('plugins/fob-barcode-generator::barcode-generator.generate.preview') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <iframe id="preview-iframe" style="width: 100%; height: 600px; border: none;"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('core/base::forms.cancel') }}</button>
                    <button type="button" id="download-from-modal" class="btn btn-primary">
                        <x-core::icon name="ti ti-download" />
                        {{ trans('plugins/fob-barcode-generator::barcode-generator.generate.download_pdf') }}
                    </button>
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
            }
        };
    </script>
@endpush
