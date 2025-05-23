class BarcodeSetupWizard {
    constructor() {
        this.currentStep = 1;
        this.maxSteps = 4;
        this.selectedData = {};
        this.init();
    }

    init() {
        this.bindEvents();
        this.updateStepVisibility();
    }

    bindEvents() {
        // Navigation buttons
        document.getElementById('next-btn').addEventListener('click', () => this.nextStep());
        document.getElementById('prev-btn').addEventListener('click', () => this.prevStep());
        document.getElementById('skip-btn').addEventListener('click', () => this.skipSetup());

        // Printer type selection
        document.querySelectorAll('.printer-option').forEach(option => {
            option.addEventListener('click', (e) => this.selectPrinterType(e));
        });

        // Barcode type selection
        document.querySelectorAll('.barcode-option').forEach(option => {
            option.addEventListener('click', (e) => this.selectBarcodeType(e));
        });
    }

    selectPrinterType(e) {
        // Remove previous selection
        document.querySelectorAll('.printer-option').forEach(opt => opt.classList.remove('selected'));
        
        // Add selection to clicked option
        e.currentTarget.classList.add('selected');
        
        // Store selection
        this.selectedData.printer_type = e.currentTarget.dataset.value;
        
        // Update paper size options for step 2
        this.updatePaperSizeOptions();
    }

    selectBarcodeType(e) {
        // Remove previous selection
        document.querySelectorAll('.barcode-option').forEach(opt => opt.classList.remove('selected'));
        
        // Add selection to clicked option
        e.currentTarget.classList.add('selected');
        
        // Store selection
        this.selectedData.barcode_type = e.currentTarget.dataset.value;
    }

    updatePaperSizeOptions() {
        const container = document.getElementById('paper-size-options');
        const printerType = this.selectedData.printer_type;
        
        if (!printerType || !window.setupWizardData) return;

        const paperSizes = window.setupWizardData.paperSizes;
        let filteredSizes = {};

        // Filter paper sizes based on printer type
        if (printerType === 'office') {
            filteredSizes = {
                'A4': paperSizes['A4'],
                'LETTER': paperSizes['LETTER']
            };
        } else if (printerType === 'thermal') {
            filteredSizes = {
                'THERMAL_4X6': paperSizes['THERMAL_4X6'],
                'THERMAL_4X3': paperSizes['THERMAL_4X3'],
                'THERMAL_2X1': paperSizes['THERMAL_2X1']
            };
        }

        // Generate HTML for paper size options
        let html = '';
        Object.entries(filteredSizes).forEach(([value, label]) => {
            html += `
                <div class="col-md-6 mb-3">
                    <div class="paper-size-option" data-value="${value}">
                        <div class="card h-100 border-2">
                            <div class="card-body text-center">
                                <h6>${label}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;

        // Bind click events to new options
        document.querySelectorAll('.paper-size-option').forEach(option => {
            option.addEventListener('click', (e) => this.selectPaperSize(e));
        });
    }

    selectPaperSize(e) {
        // Remove previous selection
        document.querySelectorAll('.paper-size-option').forEach(opt => opt.classList.remove('selected'));
        
        // Add selection to clicked option
        e.currentTarget.classList.add('selected');
        
        // Store selection
        this.selectedData.paper_size = e.currentTarget.dataset.value;
    }

    nextStep() {
        if (!this.validateCurrentStep()) {
            return;
        }

        if (this.currentStep < this.maxSteps) {
            this.saveStepData();
            this.currentStep++;
            this.updateStepVisibility();
            this.updateProgressSteps();
        } else {
            this.completeSetup();
        }
    }

    prevStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            this.updateStepVisibility();
            this.updateProgressSteps();
        }
    }

    validateCurrentStep() {
        switch (this.currentStep) {
            case 1:
                if (!this.selectedData.printer_type) {
                    this.showError('Please select a printer type');
                    return false;
                }
                break;
            case 2:
                if (!this.selectedData.paper_size) {
                    this.showError('Please select a paper size');
                    return false;
                }
                break;
            case 3:
                if (!this.selectedData.barcode_type) {
                    this.showError('Please select a barcode type');
                    return false;
                }
                break;
            case 4:
                const templateName = document.getElementById('template-name').value.trim();
                if (!templateName) {
                    this.showError('Please enter a template name');
                    return false;
                }
                this.selectedData.template_name = templateName;
                break;
        }
        return true;
    }

    updateStepVisibility() {
        // Hide all steps
        document.querySelectorAll('.wizard-step').forEach(step => {
            step.classList.add('d-none');
        });

        // Show current step
        document.getElementById(`step-${this.currentStep}`).classList.remove('d-none');

        // Update navigation buttons
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');

        if (this.currentStep === 1) {
            prevBtn.style.display = 'none';
        } else {
            prevBtn.style.display = 'inline-block';
        }

        if (this.currentStep === this.maxSteps) {
            nextBtn.innerHTML = '<i class="ti ti-check"></i> Complete Setup';
        } else {
            nextBtn.innerHTML = 'Next <i class="ti ti-arrow-right"></i>';
        }
    }

    updateProgressSteps() {
        document.querySelectorAll('.step').forEach((step, index) => {
            const stepNumber = index + 1;
            step.classList.remove('active', 'completed');
            
            if (stepNumber < this.currentStep) {
                step.classList.add('completed');
            } else if (stepNumber === this.currentStep) {
                step.classList.add('active');
            }
        });
    }

    saveStepData() {
        if (!window.setupWizardData?.routes?.store) return;

        const formData = new FormData();
        formData.append('step', this.currentStep);
        
        switch (this.currentStep) {
            case 1:
                formData.append('printer_type', this.selectedData.printer_type);
                break;
            case 2:
                formData.append('paper_size', this.selectedData.paper_size);
                break;
            case 3:
                formData.append('barcode_type', this.selectedData.barcode_type);
                break;
        }

        fetch(window.setupWizardData.routes.store, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).catch(error => {
            console.error('Error saving step data:', error);
        });
    }

    completeSetup() {
        if (!window.setupWizardData?.routes?.store) return;

        const nextBtn = document.getElementById('next-btn');
        nextBtn.disabled = true;
        nextBtn.innerHTML = '<i class="ti ti-loader-2 animate-spin"></i> Completing...';

        const formData = new FormData();
        formData.append('step', 4);
        formData.append('template_name', this.selectedData.template_name);

        fetch(window.setupWizardData.routes.store, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.message);
            }
            
            // Show success message and redirect
            this.showSuccess('Setup completed successfully! Redirecting...');
            setTimeout(() => {
                window.location.href = window.setupWizardData.routes.index;
            }, 2000);
        })
        .catch(error => {
            this.showError(error.message || 'An error occurred during setup');
            nextBtn.disabled = false;
            nextBtn.innerHTML = '<i class="ti ti-check"></i> Complete Setup';
        });
    }

    skipSetup() {
        if (confirm('Are you sure you want to skip the setup? You can run it again later from the settings.')) {
            fetch(window.setupWizardData.routes.skip, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(() => {
                window.location.href = window.setupWizardData.routes.index;
            })
            .catch(error => {
                console.error('Error skipping setup:', error);
            });
        }
    }

    showError(message) {
        // You can implement a toast notification system here
        alert('Error: ' + message);
    }

    showSuccess(message) {
        // You can implement a toast notification system here
        alert('Success: ' + message);
    }
}

// Initialize the wizard when the page loads
document.addEventListener('DOMContentLoaded', function() {
    if (window.setupWizardData) {
        new BarcodeSetupWizard();
    }
});
