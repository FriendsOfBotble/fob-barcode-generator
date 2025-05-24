@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <x-core::icon name="ti ti-wand" />
                            {{ trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.title') }}
                        </h4>
                        <p class="text-muted mb-0">{{ trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.description') }}</p>
                    </div>
                    <div class="card-body">
                        <!-- Progress Steps -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="progress-steps">
                                    <div class="step active" data-step="1">
                                        <div class="step-number">1</div>
                                        <div class="step-title">{{ trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.step1.title') }}</div>
                                    </div>
                                    <div class="step" data-step="2">
                                        <div class="step-number">2</div>
                                        <div class="step-title">{{ trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.step2.title') }}</div>
                                    </div>
                                    <div class="step" data-step="3">
                                        <div class="step-number">3</div>
                                        <div class="step-title">{{ trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.step3.title') }}</div>
                                    </div>
                                    <div class="step" data-step="4">
                                        <div class="step-number">4</div>
                                        <div class="step-title">{{ trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.step4.title') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 1: Printer Type -->
                        <div class="wizard-step" id="step-1">
                            <div class="text-center mb-4">
                                <h5>{{ trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.step1.title') }}</h5>
                                <p class="text-muted">{{ trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.step1.description') }}</p>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="printer-option" data-value="office">
                                        <div class="card h-100 border-2">
                                            <div class="card-body text-center">
                                                <x-core::icon name="ti ti-printer" class="icon-lg mb-3 text-primary" />
                                                <h6>{{ trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.printer_types.office') }}</h6>
                                                <p class="text-muted small">{{ trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.printer_types.office_desc') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="printer-option" data-value="thermal">
                                        <div class="card h-100 border-2">
                                            <div class="card-body text-center">
                                                <x-core::icon name="ti ti-device-mobile" class="icon-lg mb-3 text-warning" />
                                                <h6>{{ trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.printer_types.thermal') }}</h6>
                                                <p class="text-muted small">{{ trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.printer_types.thermal_desc') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Paper Size -->
                        <div class="wizard-step d-none" id="step-2">
                            <div class="text-center mb-4">
                                <h5>{{ trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.step2.title') }}</h5>
                                <p class="text-muted">{{ trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.step2.description') }}</p>
                            </div>
                            <div class="row" id="paper-size-options">
                                <!-- Paper size options will be populated by JavaScript -->
                            </div>
                        </div>

                        <!-- Step 3: Barcode Type -->
                        <div class="wizard-step d-none" id="step-3">
                            <div class="text-center mb-4">
                                <h5>{{ trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.step3.title') }}</h5>
                                <p class="text-muted">{{ trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.step3.description') }}</p>
                            </div>
                            <div class="row">
                                @foreach($barcodeTypes as $value => $label)
                                    <div class="col-md-4 mb-3">
                                        <div class="barcode-option" data-value="{{ $value }}">
                                            <div class="card h-100 border-2">
                                                <div class="card-body text-center">
                                                    <h6>{{ $label }}</h6>
                                                    <p class="text-muted small">{{ \FriendsOfBotble\BarcodeGenerator\Enums\BarcodeTypeEnum::getDescription($value) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Step 4: Template Name -->
                        <div class="wizard-step d-none" id="step-4">
                            <div class="text-center mb-4">
                                <h5>{{ trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.step4.title') }}</h5>
                                <p class="text-muted">{{ trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.step4.description') }}</p>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.template_name') }}</label>
                                        <input type="text" class="form-control" id="template-name" value="{{ trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard_ui.default_template_value') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary" id="prev-btn" style="display: none;">
                                <x-core::icon name="ti ti-arrow-left" />
                                {{ trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.previous') }}
                            </button>
                            <div class="ms-auto">
                                <button type="button" class="btn btn-outline-secondary me-2" id="skip-btn">
                                    {{ trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.skip') }}
                                </button>
                                <button type="button" class="btn btn-primary" id="next-btn">
                                    {{ trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.next') }}
                                    <x-core::icon name="ti ti-arrow-right" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden data -->
    <script>
        window.setupWizardData = {
            paperSizes: @json($paperSizes),
            routes: {
                store: '{{ route('barcode-generator.setup-wizard.store') }}',
                complete: '{{ route('barcode-generator.setup-wizard.complete') }}',
                skip: '{{ route('barcode-generator.setup-wizard.skip') }}',
                index: '{{ route('barcode-generator.index') }}'
            },
            translations: {
                completing: '{{ trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.completing') }}',
                completed: '{{ trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.completed') }}',
                error: '{{ trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.error') }}'
            }
        };
    </script>
@endsection

@push('footer')
    <style>
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
        }

        .step {
            text-align: center;
            flex: 1;
            position: relative;
        }

        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 20px;
            right: -50%;
            width: 100%;
            height: 2px;
            background: #dee2e6;
            z-index: 1;
        }

        .step.active:not(:last-child)::after,
        .step.completed:not(:last-child)::after {
            background: #0d6efd;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #dee2e6;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            font-weight: bold;
            position: relative;
            z-index: 2;
        }

        .step.active .step-number {
            background: #0d6efd;
            color: white;
        }

        .step.completed .step-number {
            background: #198754;
            color: white;
        }

        .step-title {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .step.active .step-title {
            color: #0d6efd;
            font-weight: 500;
        }

        .printer-option, .barcode-option, .paper-size-option {
            cursor: pointer;
        }

        .printer-option .card, .barcode-option .card, .paper-size-option .card {
            transition: all 0.3s ease;
        }

        .printer-option:hover .card, .barcode-option:hover .card, .paper-size-option:hover .card {
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .printer-option.selected .card, .barcode-option.selected .card, .paper-size-option.selected .card {
            border-color: #0d6efd !important;
            background-color: rgba(13, 110, 253, 0.1);
        }

        .icon-lg {
            font-size: 3rem;
        }
    </style>
@endpush
