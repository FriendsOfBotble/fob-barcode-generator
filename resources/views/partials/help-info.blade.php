<div class="alert alert-info border-0 shadow-sm">
    <div class="d-flex align-items-start">
        <div class="flex-shrink-0 me-3">
            <div class="bg-info bg-opacity-10 rounded-circle p-2">
                <x-core::icon name="ti ti-info-circle" />
            </div>
        </div>
        <div class="flex-grow-1">
            <h6 class="alert-heading mb-2 fw-semibold">
                {{ trans('plugins/fob-barcode-generator::barcode-generator.settings.help_title') }}
            </h6>
            <p class="mb-3 text-muted">
                {{ trans('plugins/fob-barcode-generator::barcode-generator.settings.help_description') }}
            </p>
            <ul class="list-unstyled mb-0">
                <li class="d-flex align-items-start mb-2">
                    <x-core::icon name="ti ti-check" class="text-success me-2 mt-1 flex-shrink-0" size="sm" />
                    <span>{{ trans('plugins/fob-barcode-generator::barcode-generator.settings.help_tip_1') }}</span>
                </li>
                <li class="d-flex align-items-start mb-2">
                    <x-core::icon name="ti ti-check" class="text-success me-2 mt-1 flex-shrink-0" size="sm" />
                    <span>{{ trans('plugins/fob-barcode-generator::barcode-generator.settings.help_tip_2') }}</span>
                </li>
                <li class="d-flex align-items-start mb-2">
                    <x-core::icon name="ti ti-check" class="text-success me-2 mt-1 flex-shrink-0" size="sm" />
                    <span>{{ trans('plugins/fob-barcode-generator::barcode-generator.settings.help_tip_3') }}</span>
                </li>
                <li class="d-flex align-items-start">
                    <x-core::icon name="ti ti-check" class="text-success me-2 mt-1 flex-shrink-0" size="sm" />
                    <span>{{ trans('plugins/fob-barcode-generator::barcode-generator.settings.help_tip_4') }}</span>
                </li>
            </ul>
        </div>
    </div>
</div>
