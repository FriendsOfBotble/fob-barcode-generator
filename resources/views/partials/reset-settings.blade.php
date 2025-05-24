<div class="alert alert-warning border-0 shadow-sm">
    <div class="d-flex align-items-start">
        <div class="flex-shrink-0 me-3">
            <div class="bg-warning bg-opacity-10 rounded-circle p-2">
                <x-core::icon name="ti ti-alert-triangle" class="text-warning" />
            </div>
        </div>
        <div class="flex-grow-1">
            <h6 class="alert-heading mb-2 fw-semibold">
                {{ trans('plugins/fob-barcode-generator::barcode-generator.settings.reset_warning_title') }}
            </h6>
            <p class="mb-3 text-muted">
                {{ trans('plugins/fob-barcode-generator::barcode-generator.settings.reset_warning_message') }}
            </p>
            <button type="button" class="btn btn-warning btn-sm d-inline-flex align-items-center" id="reset-to-defaults-btn">
                <x-core::icon name="refresh" class="me-1" size="sm" />
                {{ trans('plugins/fob-barcode-generator::barcode-generator.settings.reset_to_defaults') }}
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const resetBtn = document.getElementById("reset-to-defaults-btn");
    if (resetBtn) {
        resetBtn.addEventListener("click", function() {
            // Show modern confirmation dialog
            if (confirm("{{ trans('plugins/fob-barcode-generator::barcode-generator.settings.reset_confirmation') }}")) {
                const defaults = @json(barcode_generator_default_settings());
                let fieldsReset = 0;

                // Add loading state
                resetBtn.disabled = true;
                resetBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>{{ trans('plugins/fob-barcode-generator::barcode-generator.settings.resetting') }}';

                // Reset fields with animation
                Object.keys(defaults).forEach((key, index) => {
                    setTimeout(() => {
                        const fieldName = "barcode_generator_" + key;
                        const field = document.querySelector(`[name="${fieldName}"]`);

                        if (field) {
                            // Add highlight animation
                            field.style.transition = 'all 0.3s ease';
                            field.style.backgroundColor = '#fff3cd';

                            if (field.type === "checkbox") {
                                field.checked = defaults[key];
                                // Trigger change event for any listeners
                                field.dispatchEvent(new Event('change', { bubbles: true }));
                            } else {
                                field.value = defaults[key];
                                // Trigger input event for any listeners
                                field.dispatchEvent(new Event('input', { bubbles: true }));
                            }

                            // Remove highlight after animation
                            setTimeout(() => {
                                field.style.backgroundColor = '';
                            }, 500);

                            fieldsReset++;
                        }
                    }, index * 50); // Stagger the animations
                });

                // Show success message after all fields are reset
                setTimeout(() => {
                    resetBtn.disabled = false;
                    resetBtn.innerHTML = '<x-core::icon name="refresh" class="me-1" size="sm" />{{ trans('plugins/fob-barcode-generator::barcode-generator.settings.reset_to_defaults') }}';

                    // Show success notification
                    if (typeof Botble !== 'undefined' && Botble.showSuccess) {
                        Botble.showSuccess("{{ trans('plugins/fob-barcode-generator::barcode-generator.settings.reset_success') }}");
                    } else {
                        alert("{{ trans('plugins/fob-barcode-generator::barcode-generator.settings.reset_success') }}");
                    }
                }, Object.keys(defaults).length * 50 + 500);
            }
        });
    }
});
</script>

<style>
#reset-to-defaults-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
}

.alert {
    border-radius: 0.5rem;
}

.alert .bg-opacity-10 {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
