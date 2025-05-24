<?php

namespace FriendsOfBotble\BarcodeGenerator\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Setting\Facades\Setting;
use FriendsOfBotble\BarcodeGenerator\Enums\BarcodeTypeEnum;
use FriendsOfBotble\BarcodeGenerator\Enums\PaperSizeEnum;
use FriendsOfBotble\BarcodeGenerator\Models\BarcodeTemplate;
use Illuminate\Http\Request;

class SetupWizardController extends BaseController
{
    public function index()
    {
        $this->pageTitle(trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.title'));

        Assets::addStylesDirectly('vendor/core/plugins/fob-barcode-generator/css/barcode-generator.css')
            ->addScriptsDirectly('vendor/core/plugins/fob-barcode-generator/js/setup-wizard.js');

        $isCompleted = Setting::get('barcode_generator_setup_completed', false);

        if ($isCompleted) {
            return redirect()->route('barcode-generator.index');
        }

        return view('plugins/fob-barcode-generator::setup-wizard.index', [
            'barcodeTypes' => BarcodeTypeEnum::labels(),
            'paperSizes' => PaperSizeEnum::labels(),
        ]);
    }

    public function store(Request $request): BaseHttpResponse
    {
        $request->validate([
            'step' => 'required|in:1,2,3,4',
            'printer_type' => 'required_if:step,1|in:office,thermal',
            'paper_size' => 'required_if:step,2|string',
            'barcode_type' => 'required_if:step,3|string',
            'template_name' => 'required_if:step,4|string|max:255',
        ]);

        $step = (int) $request->input('step');

        switch ($step) {
            case 1:
                // Save printer type preference
                Setting::set('barcode_generator_printer_type', $request->input('printer_type'));

                break;

            case 2:
                // Save paper size preference
                Setting::set('barcode_generator_default_paper_size', $request->input('paper_size'));

                break;

            case 3:
                // Save barcode type preference
                Setting::set('barcode_generator_default_barcode_type', $request->input('barcode_type'));

                break;

            case 4:
                // Create default template and complete setup
                $this->createDefaultTemplate($request);
                Setting::set('barcode_generator_setup_completed', true);

                break;
        }

        Setting::save();

        return $this
            ->httpResponse()
            ->setData(['next_step' => $step + 1])
            ->setMessage(trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.step_completed'));
    }

    public function complete(): BaseHttpResponse
    {
        Setting::set('barcode_generator_setup_completed', true);
        Setting::save();

        return $this
            ->httpResponse()
            ->setData(['redirect_url' => route('barcode-generator.index')])
            ->setMessage(trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.completed'));
    }

    public function skip(): BaseHttpResponse
    {
        Setting::set('barcode_generator_setup_completed', true);
        Setting::save();

        return $this
            ->httpResponse()
            ->setData(['redirect_url' => route('barcode-generator.index')])
            ->setMessage(trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.skipped'));
    }

    protected function createDefaultTemplate(Request $request): void
    {
        $printerType = Setting::get('barcode_generator_printer_type', 'office');
        $paperSize = Setting::get('barcode_generator_default_paper_size', PaperSizeEnum::A4);
        $barcodeType = Setting::get('barcode_generator_default_barcode_type', BarcodeTypeEnum::CODE128);
        $templateName = $request->input('template_name', trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard_ui.default_template_value'));

        // Create template based on printer type
        $template = match ($printerType) {
            'thermal' => $this->createThermalTemplate($templateName, $paperSize, $barcodeType),
            default => $this->createOfficeTemplate($templateName, $paperSize, $barcodeType),
        };

        // Set as default template
        BarcodeTemplate::query()->update(['is_default' => false]);
        $template->update(['is_default' => true]);
    }

    protected function createOfficeTemplate(string $name, string $paperSize, string $barcodeType): BarcodeTemplate
    {
        return BarcodeTemplate::create([
            'name' => $name,
            'description' => trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.auto_generated_description'),
            'paper_size' => $paperSize,
            'barcode_type' => $barcodeType,
            'template_html' => $this->getOfficeTemplateHtml(),
            'template_css' => $this->getOfficeTemplateCss(),
            'labels_per_page' => PaperSizeEnum::getLabelsPerPage($paperSize),
            'label_width' => 70,
            'label_height' => 30,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 10,
            'margin_right' => 10,
            'gap_horizontal' => 5,
            'gap_vertical' => 5,
            'is_default' => false,
            'is_active' => true,
        ]);
    }

    protected function createThermalTemplate(string $name, string $paperSize, string $barcodeType): BarcodeTemplate
    {
        $dimensions = PaperSizeEnum::getDimensions($paperSize);

        return BarcodeTemplate::create([
            'name' => $name,
            'description' => trans('plugins/fob-barcode-generator::barcode-generator.setup_wizard.auto_generated_thermal_description'),
            'paper_size' => $paperSize,
            'barcode_type' => $barcodeType,
            'template_html' => $this->getThermalTemplateHtml(),
            'template_css' => $this->getThermalTemplateCss(),
            'labels_per_page' => 1,
            'label_width' => $dimensions['width'] ?? 4,
            'label_height' => $dimensions['height'] ?? 6,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_left' => 0,
            'margin_right' => 0,
            'gap_horizontal' => 0,
            'gap_vertical' => 0,
            'is_default' => false,
            'is_active' => true,
        ]);
    }

    protected function getOfficeTemplateHtml(): string
    {
        return '<div class="label">
    <div class="barcode-container">
        <img src="{barcode_image}" alt="Barcode" class="barcode">
    </div>
    <div class="product-info">
        <div class="product-name">{product_name}</div>
        <div class="product-sku">SKU: {product_sku}</div>
        <div class="product-price">{product_price}</div>
    </div>
</div>';
    }

    protected function getOfficeTemplateCss(): string
    {
        return '.label {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: center;
    font-family: Arial, sans-serif;
    font-size: 10px;
}

.barcode-container {
    margin-bottom: 5px;
}

.barcode {
    max-width: 100%;
    height: auto;
}

.product-info {
    line-height: 1.2;
}

.product-name {
    font-weight: bold;
    margin-bottom: 2px;
}

.product-sku {
    font-size: 8px;
    color: #666;
}

.product-price {
    font-weight: bold;
    color: #333;
}';
    }

    protected function getThermalTemplateHtml(): string
    {
        return '<div class="thermal-label">
    <div class="header">
        <div class="product-name">{product_name}</div>
    </div>
    <div class="barcode-section">
        <img src="{barcode_image}" alt="Barcode" class="barcode">
        <div class="barcode-text">{product_sku}</div>
    </div>
    <div class="footer">
        <div class="price">{product_price}</div>
    </div>
</div>';
    }

    protected function getThermalTemplateCss(): string
    {
        return '.thermal-label {
    width: 100%;
    height: 100%;
    padding: 10px;
    font-family: Arial, sans-serif;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.header {
    text-align: center;
    margin-bottom: 10px;
}

.product-name {
    font-size: 14px;
    font-weight: bold;
    line-height: 1.2;
}

.barcode-section {
    text-align: center;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.barcode {
    max-width: 100%;
    height: auto;
    margin-bottom: 5px;
}

.barcode-text {
    font-size: 12px;
    font-weight: bold;
}

.footer {
    text-align: center;
    margin-top: 10px;
}

.price {
    font-size: 16px;
    font-weight: bold;
}';
    }
}
