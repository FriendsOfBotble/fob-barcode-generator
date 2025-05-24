<div class="alert alert-primary border-0 shadow-sm">
    <div class="d-flex align-items-start">
        <div class="flex-shrink-0 me-3">
            <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                <x-core::icon name="ti ti-settings" class="text-primary" />
            </div>
        </div>
        <div class="flex-grow-1">
            <h6 class="alert-heading mb-2 fw-semibold">
                {{ trans('plugins/fob-barcode-generator::barcode-generator.settings.field_display_settings') }}
            </h6>
            <p class="mb-0 text-muted">
                {{ trans('plugins/fob-barcode-generator::barcode-generator.settings.field_display_help') }}
            </p>
        </div>
    </div>
</div>

<style>
.alert-primary {
    border-radius: 0.5rem;
}

.alert-primary .bg-opacity-10 {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
