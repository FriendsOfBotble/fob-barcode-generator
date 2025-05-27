<?php

namespace FriendsOfBotble\BarcodeGenerator\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\Product;
use Botble\Setting\Facades\Setting;
use FriendsOfBotble\BarcodeGenerator\Enums\BarcodeTypeEnum;
use FriendsOfBotble\BarcodeGenerator\Libraries\BarcodeGenerator;
use FriendsOfBotble\BarcodeGenerator\Models\BarcodeTemplate;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BarcodeGeneratorService
{
    protected BarcodeGenerator $generator;

    protected array $barcodeTypes = [
        BarcodeTypeEnum::CODE128 => 'CODE128',
        BarcodeTypeEnum::CODE39 => 'CODE39',
        BarcodeTypeEnum::EAN13 => 'EAN13',
        BarcodeTypeEnum::EAN8 => 'EAN8',
        BarcodeTypeEnum::UPC_A => 'EAN13', // Use EAN13 for UPC-A
        BarcodeTypeEnum::UPC_E => 'EAN8', // Use EAN8 for UPC-E
        BarcodeTypeEnum::QRCODE => 'QRCODE',
        BarcodeTypeEnum::DATAMATRIX => 'DATAMATRIX',
        BarcodeTypeEnum::GTIN => 'EAN13', // Use EAN13 for GTIN
    ];

    public function __construct()
    {
        $this->generator = new BarcodeGenerator();
    }

    public function generateBarcode(string $data, ?string $type = null, string $format = 'svg'): string
    {
        // Use default type if none provided
        if ($type === null) {
            $type = BarcodeTypeEnum::CODE128;
        }

        // Handle legacy barcode types
        $type = $this->normalizeBarcodeType($type);

        if (! isset($this->barcodeTypes[$type])) {
            // Fallback to CODE128 if type is not supported
            $type = BarcodeTypeEnum::CODE128;
        }

        // Handle QR code generation separately
        if ($type === BarcodeTypeEnum::QRCODE) {
            return $this->generateQRCode($data, $format);
        }

        $barcodeType = $this->barcodeTypes[$type];

        if ($format === 'svg') {
            return $this->generator->generateBarcodeSVG($data, $barcodeType, 200, 50);
        }

        throw new \InvalidArgumentException("Unsupported format: {$format}");
    }

    protected function generateQRCode(string $data, string $format = 'svg'): string
    {
        if ($format !== 'svg') {
            throw new \InvalidArgumentException('QR codes only support SVG format');
        }

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        return $writer->writeString($data);
    }

    protected function normalizeBarcodeType(string $type): string
    {
        // Handle legacy barcode type mappings
        $legacyMappings = [
            'C128' => BarcodeTypeEnum::CODE128,
            'CODE128' => BarcodeTypeEnum::CODE128,
            'C39' => BarcodeTypeEnum::CODE39,
            'CODE39' => BarcodeTypeEnum::CODE39,
            'EAN13' => BarcodeTypeEnum::EAN13,
            'EAN8' => BarcodeTypeEnum::EAN8,
            'UPCA' => BarcodeTypeEnum::UPC_A,
            'UPC-A' => BarcodeTypeEnum::UPC_A,
            'UPCE' => BarcodeTypeEnum::UPC_E,
            'UPC-E' => BarcodeTypeEnum::UPC_E,
            'QR' => BarcodeTypeEnum::QRCODE,
            'QRCODE' => BarcodeTypeEnum::QRCODE,
            'DATAMATRIX' => BarcodeTypeEnum::DATAMATRIX,
            'GTIN' => BarcodeTypeEnum::GTIN,
        ];

        return $legacyMappings[$type] ?? $type;
    }

    public function generateProductBarcode(Product $product, string $format = 'svg'): ?string
    {
        $barcodeData = $product->barcode ?: $product->sku;

        if (! $barcodeData) {
            return null;
        }

        $type = $this->detectBarcodeType($barcodeData);

        return $this->generateBarcode($barcodeData, $type, $format);
    }

    public function generateOrderBarcode(Order $order, string $format = 'svg'): string
    {
        $barcodeData = $order->code;

        return $this->generateBarcode($barcodeData, BarcodeTypeEnum::CODE128, $format);
    }

    public function generateLabels(Collection $products, BarcodeTemplate $template, int $quantity = 1): string
    {
        if ($template->template_html) {
            return $this->generateCustomTemplateHTML($products, $template, $quantity);
        }

        return $this->generateLabelHTML($products, $template, $quantity);
    }

    public function generateOrderLabels(Collection $orders, BarcodeTemplate $template, int $quantity = 1): string
    {
        if ($template->template_html) {
            return $this->generateCustomOrderTemplateHTML($orders, $template, $quantity);
        }

        return $this->generateOrderLabelHTML($orders, $template, $quantity);
    }

    protected function generateLabelHTML(Collection $products, BarcodeTemplate $template, int $quantity): string
    {
        $html = '<!DOCTYPE html>';
        $html .= '<html><head>';
        $html .= '<meta charset="UTF-8">';
        $html .= '<title>Barcode Labels</title>';
        $html .= $this->generateLabelCSS($template);
        $html .= '</head><body>';

        $html .= '<div class="page">';

        $labelCount = 0;
        $maxLabelsPerPage = $template->columns_per_page * $template->rows_per_page;

        foreach ($products as $product) {
            for ($i = 0; $i < $quantity; $i++) {
                if ($labelCount > 0 && $labelCount % $maxLabelsPerPage === 0) {
                    $html .= '</div><div class="page-break"></div><div class="page">';
                }

                $html .= $this->generateSingleLabel($product, $template);
                $labelCount++;
            }
        }

        $html .= '</div>';
        $html .= '</body></html>';

        return $html;
    }

    protected function generateSingleLabel(Product $product, BarcodeTemplate $template): string
    {
        $html = '<div class="label" data-barcode-type="' . e($template->barcode_type) . '">';

        // Generate barcode
        if ($product->barcode || $product->sku) {
            $barcodeData = $product->barcode ?: $product->sku;
            $barcodeSvg = $this->generateBarcode($barcodeData, $template->barcode_type, 'svg');
            $html .= '<div class="barcode">' . $barcodeSvg . '</div>';
        }

        // Add text fields based on global settings or template configuration
        $fieldsToShow = [];

        // Check if we should use global field display settings
        $enabledFields = barcode_generator_get_enabled_fields();
        if (! empty($enabledFields)) {
            $fieldsToShow = $enabledFields;
        } elseif ($template->fields && is_array($template->fields)) {
            // Fallback to template configuration if no global settings
            $fieldsToShow = $template->fields;
        }

        foreach ($fieldsToShow as $field) {
            $value = $this->getProductFieldValue($product, $field);
            if ($value) {
                // Determine if field should be multiline based on content length
                $fieldClass = strlen($value) > 30 ? 'field multiline field-' . $field : 'field single-line field-' . $field;
                // Don't escape HTML for fields that contain HTML markup
                if ($this->fieldContainsHtml($field)) {
                    $html .= '<div class="' . $fieldClass . '">' . $value . '</div>';
                } else {
                    $html .= '<div class="' . $fieldClass . '">' . e($value) . '</div>';
                }
            }
        }

        $html .= '</div>';

        return $html;
    }

    protected function getProductFieldValue(Product $product, string $field): ?string
    {
        switch ($field) {
            case 'product_name':
            case 'name':
                return $product->name;
            case 'product_sku':
            case 'sku':
                return $product->sku;
            case 'product_barcode':
            case 'barcode':
                return $product->barcode;
            case 'product_price':
            case 'price':
                return format_price($product->price);
            case 'product_sale_price':
            case 'sale_price':
                return $product->sale_price ? format_price($product->sale_price) : null;
            case 'product_price_smart':
                // Show sale price if available, otherwise original price
                return $product->sale_price ? format_price($product->sale_price) : format_price($product->price);
            case 'product_price_with_original':
                // Show both prices with proper formatting when on sale
                return $this->formatPriceWithOriginal($product);
            case 'product_price_sale_only':
                // Show only sale price (same as product_sale_price)
                return $product->sale_price ? format_price($product->sale_price) : null;
            case 'product_price_original_only':
                // Show only original price
                return format_price($product->price);
            case 'product_brand':
            case 'brand':
                return $product->brand?->name;
            case 'product_category':
            case 'category':
                return $product->categories->first()?->name;
            case 'product_attributes':
            case 'attributes':
                return $product->variationInfo?->variationItems
                    ->pluck('attribute_sets.title')
                    ->filter()
                    ->implode(', ');
            case 'product_description':
                return Str::limit(strip_tags($product->description), 100);
            case 'product_weight':
                return $product->weight ? $product->weight . ' kg' : null;
            case 'product_dimensions':
                return $this->formatDimensions($product);
            case 'product_stock':
                return $product->quantity ? (string) $product->quantity : '0';
            case 'current_date':
                return now()->format('Y-m-d');
            case 'company_name':
                return Setting::get('admin_title', config('app.name'));
            default:
                return null;
        }
    }

    protected function formatPriceWithOriginal(Product $product): ?string
    {
        if (!$product->sale_price) {
            // No sale price, just return original price as a single field
            return '<div class="field single-line field-product_price">' . format_price($product->price) . '</div>';
        }

        // Product is on sale, return both prices as separate divs
        $originalPrice = format_price($product->price);
        $salePrice = format_price($product->sale_price);

        return '<div class="field single-line field-product_price" style="text-decoration: line-through !important; font-size: 8pt !important;">' . $originalPrice . '</div>' .
               '<div class="field single-line field-product_sale_price" style="font-size: 12pt !important; font-weight: bold !important;">' . $salePrice . '</div>';
    }

    protected function formatDimensions(Product $product): ?string
    {
        $dimensions = [];
        if ($product->length) {
            $dimensions[] = $product->length;
        }
        if ($product->width) {
            $dimensions[] = $product->width;
        }
        if ($product->height) {
            $dimensions[] = $product->height;
        }

        return $dimensions ? implode(' × ', $dimensions) . ' cm' : null;
    }

    protected function generateLabelCSS(BarcodeTemplate $template): string
    {
        $css = '<style>';
        $css .= '@media print { .page-break { page-break-before: always; } }';
        $css .= 'body { margin: 0; padding: 0; font-family: Arial, sans-serif; }';
        $css .= '.page { ';
        $css .= 'margin: ' . $template->margin_top . 'mm ' . $template->margin_right . 'mm ';
        $css .= $template->margin_bottom . 'mm ' . $template->margin_left . 'mm; ';
        $css .= 'display: grid; ';
        $css .= 'grid-template-columns: repeat(' . $template->columns_per_page . ', 1fr); ';
        $css .= 'grid-template-rows: repeat(' . $template->rows_per_page . ', 1fr); ';
        $css .= 'gap: 2mm; ';
        $css .= '}';

        // Calculate responsive barcode sizing
        $labelWidth = $template->label_width;
        $labelHeight = $template->label_height;
        $padding = $template->padding;
        $barcodeWidth = $template->barcode_width;
        $barcodeHeight = $template->barcode_height;

        // Calculate available space for barcode (label size minus padding)
        $availableWidth = $labelWidth - ($padding * 2);
        $availableHeight = $labelHeight - ($padding * 2);

        // Ensure barcode doesn't exceed available space
        $maxBarcodeWidth = min($barcodeWidth, $availableWidth);
        $maxBarcodeHeight = min($barcodeHeight, $availableHeight * 0.7); // Leave space for text

        $css .= '.label { ';
        $css .= 'width: ' . $labelWidth . 'mm; ';
        $css .= 'height: ' . $labelHeight . 'mm; ';
        $css .= 'padding: ' . $padding . 'mm; ';
        $css .= 'border: 1px solid #ccc; ';
        $css .= 'display: flex; ';
        $css .= 'flex-direction: column; ';
        $css .= 'align-items: center; ';
        $css .= 'justify-content: center; ';
        $css .= 'text-align: center; ';
        $css .= 'box-sizing: border-box; ';
        $css .= 'overflow: hidden; ';
        $css .= 'position: relative; ';
        $css .= '}';

        $css .= '.barcode { ';
        $css .= 'display: flex; ';
        $css .= 'justify-content: center; ';
        $css .= 'align-items: center; ';
        $css .= 'width: 100%; ';
        $css .= 'max-height: 70%; ';
        $css .= 'margin-bottom: 2mm; ';
        $css .= '}';

        $css .= '.barcode svg, .barcode img { ';
        $css .= 'max-width: ' . $maxBarcodeWidth . 'mm; ';
        $css .= 'max-height: ' . $maxBarcodeHeight . 'mm; ';
        $css .= 'width: auto; ';
        $css .= 'height: auto; ';
        $css .= 'display: block; ';
        $css .= '}';

        $css .= '.field { ';
        $css .= 'font-size: ' . $template->text_size . 'pt; ';
        $css .= 'line-height: 1.2; ';
        $css .= 'margin: 0.5mm 0; ';
        $css .= 'word-wrap: break-word; ';
        $css .= 'overflow: hidden; ';
        $css .= 'text-overflow: ellipsis; ';
        $css .= 'white-space: nowrap; ';
        $css .= '}';

        $css .= '.field.multiline { ';
        $css .= 'white-space: normal; ';
        $css .= 'max-height: 25%; ';
        $css .= 'overflow: hidden; ';
        $css .= '}';

        $css .= '</style>';

        return $css;
    }

    protected function detectBarcodeType(string $data): string
    {
        $length = strlen($data);

        // EAN-13: 13 digits
        if ($length === 13 && ctype_digit($data)) {
            return BarcodeTypeEnum::EAN13;
        }

        // EAN-8: 8 digits
        if ($length === 8 && ctype_digit($data)) {
            return BarcodeTypeEnum::EAN8;
        }

        // UPC-A: 12 digits
        if ($length === 12 && ctype_digit($data)) {
            return BarcodeTypeEnum::UPC_A;
        }

        // UPC-E: 6-8 digits
        if ($length >= 6 && $length <= 8 && ctype_digit($data)) {
            return BarcodeTypeEnum::UPC_E;
        }

        // Default to Code 128 for everything else
        return BarcodeTypeEnum::CODE128;
    }

    protected function generateCustomTemplateHTML(Collection $products, BarcodeTemplate $template, int $quantity): string
    {
        $html = '<!DOCTYPE html>';
        $html .= '<html><head>';
        $html .= '<meta charset="UTF-8">';
        $html .= '<title>Barcode Labels</title>';
        $html .= '<style>' . ($template->template_css ?: $this->getDefaultCustomCSS($template)) . '</style>';
        $html .= '</head><body>';

        $labelCount = 0;
        $maxLabelsPerPage = $template->labels_per_page ?: 24;

        foreach ($products as $product) {
            for ($i = 0; $i < $quantity; $i++) {
                if ($labelCount > 0 && $labelCount % $maxLabelsPerPage === 0) {
                    $html .= '<div class="page-break"></div>';
                }

                $html .= $this->processCustomTemplate($product, $template);
                $labelCount++;
            }
        }

        $html .= '</body></html>';

        return $html;
    }

    protected function generateCustomOrderTemplateHTML(Collection $orders, BarcodeTemplate $template, int $quantity): string
    {
        $html = '<!DOCTYPE html>';
        $html .= '<html><head>';
        $html .= '<meta charset="UTF-8">';
        $html .= '<title>Order Barcode Labels</title>';
        $html .= '<style>' . ($template->template_css ?: $this->getDefaultCustomCSS($template)) . '</style>';
        $html .= '</head><body>';

        $labelCount = 0;
        $maxLabelsPerPage = $template->labels_per_page ?: 24;

        foreach ($orders as $order) {
            for ($i = 0; $i < $quantity; $i++) {
                if ($labelCount > 0 && $labelCount % $maxLabelsPerPage === 0) {
                    $html .= '<div class="page-break"></div>';
                }

                $html .= $this->processCustomOrderTemplate($order, $template);
                $labelCount++;
            }
        }

        $html .= '</body></html>';

        return $html;
    }

    protected function processCustomTemplate(Product $product, BarcodeTemplate $template): string
    {
        $html = $template->template_html;

        // Replace barcode image
        if (strpos($html, '{barcode_image}') !== false) {
            $barcodeData = $product->barcode ?: $product->sku;
            if ($barcodeData) {
                $barcodeSvg = $this->generateBarcode($barcodeData, $template->barcode_type, 'svg');
                $barcodeDataUri = 'data:image/svg+xml;base64,' . base64_encode($barcodeSvg);

                // Wrap barcode in container for proper sizing
                $barcodeHtml = '<div class="barcode-container"><img src="' . $barcodeDataUri . '" alt="Barcode" /></div>';
                $html = str_replace('{barcode_image}', $barcodeHtml, $html);
            } else {
                $html = str_replace('{barcode_image}', '', $html);
            }
        }

        // Replace product fields based on enabled settings
        $availableFields = (new BarcodeTemplate())->getAvailableFields();
        $enabledFields = barcode_generator_get_enabled_fields();

        foreach ($availableFields as $field => $label) {
            $placeholder = '{' . $field . '}';
            if (strpos($html, $placeholder) !== false) {
                // Check if field is enabled in settings, if not replace with empty string
                if (! empty($enabledFields) && ! in_array($field, $enabledFields)) {
                    $html = str_replace($placeholder, '', $html);
                } else {
                    $value = $this->getProductFieldValue($product, $field) ?: '';
                    // Don't escape HTML for fields that contain HTML markup
                    if ($this->fieldContainsHtml($field)) {
                        $html = str_replace($placeholder, $value, $html);
                    } else {
                        $html = str_replace($placeholder, e($value), $html);
                    }
                }
            }
        }

        return $html;
    }

    protected function fieldContainsHtml(string $field): bool
    {
        // Fields that return HTML markup and should not be escaped
        $htmlFields = [
            'product_price_with_original',
        ];

        return in_array($field, $htmlFields);
    }

    protected function processCustomOrderTemplate(Order $order, BarcodeTemplate $template): string
    {
        $html = $template->template_html;

        // Replace barcode image
        if (strpos($html, '{barcode_image}') !== false) {
            $barcodeSvg = $this->generateOrderBarcode($order, 'svg');
            $barcodeDataUri = 'data:image/svg+xml;base64,' . base64_encode($barcodeSvg);

            // Wrap barcode in container for proper sizing
            $barcodeHtml = '<div class="barcode-container"><img src="' . $barcodeDataUri . '" alt="Order Barcode" /></div>';
            $html = str_replace('{barcode_image}', $barcodeHtml, $html);
        }

        // Replace order fields
        $availableFields = (new BarcodeTemplate())->getAvailableOrderFields();
        foreach ($availableFields as $field => $label) {
            $placeholder = '{' . $field . '}';
            if (strpos($html, $placeholder) !== false) {
                $value = $this->getOrderFieldValue($order, $field) ?: '';
                $html = str_replace($placeholder, e($value), $html);
            }
        }

        return $html;
    }

    protected function getOrderFieldValue(Order $order, string $field): ?string
    {
        switch ($field) {
            case 'order_id':
                return (string) $order->id;
            case 'order_code':
                return $order->code;
            case 'order_date':
                return $order->created_at->format('Y-m-d');
            case 'order_status':
                return $order->status->label();
            case 'order_total':
                return format_price($order->amount);
            case 'customer_name':
                return $order->user?->name ?: $order->address->name;
            case 'customer_email':
                return $order->user?->email ?: $order->address->email;
            case 'customer_phone':
                return $order->user?->phone ?: $order->address->phone;
            case 'shipping_address':
                return $order->shippingAddress ? $this->formatAddress($order->shippingAddress) : null;
            case 'billing_address':
                return $order->address ? $this->formatAddress($order->address) : null;
            default:
                return null;
        }
    }

    protected function formatAddress($address): string
    {
        $parts = array_filter([
            $address->address,
            $address->city,
            $address->state,
            $address->zip_code,
            $address->country_name,
        ]);

        return implode(', ', $parts);
    }

    protected function getDefaultCustomCSS(BarcodeTemplate $template): string
    {
        $labelWidth = $template->label_width ?: 70;
        $labelHeight = $template->label_height ?: 30;
        $padding = $template->padding ?: 2;
        $barcodeWidth = $template->barcode_width ?: ($labelWidth * 0.8);
        $barcodeHeight = $template->barcode_height ?: ($labelHeight * 0.6);

        // Calculate available space for barcode (label size minus padding)
        $availableWidth = $labelWidth - ($padding * 2);
        $availableHeight = $labelHeight - ($padding * 2);

        // Ensure barcode doesn't exceed available space
        $maxBarcodeWidth = min($barcodeWidth, $availableWidth);
        $maxBarcodeHeight = min($barcodeHeight, $availableHeight * 0.7); // Leave space for text

        // Add barcode type specific styles
        $barcodeTypeClass = '';
        $maxBarcodeHeightPercent = '70%';
        $textMaxHeight = '25%';

        if ($template->barcode_type === 'QRCODE') {
            $barcodeTypeClass = 'qr-label';
            $maxBarcodeHeightPercent = '80%';
            $textMaxHeight = '15%';
        } elseif (in_array($template->barcode_type, ['CODE128', 'EAN13', 'EAN8', 'UPCA', 'UPCE'])) {
            $barcodeTypeClass = 'linear-label';
            $maxBarcodeHeightPercent = '60%';
            $textMaxHeight = '35%';
        }

        return '@media print { .page-break { page-break-before: always; } }
body { margin: 0; padding: 0; font-family: Arial, sans-serif; }
.label {
    width: ' . $labelWidth . 'mm;
    height: ' . $labelHeight . 'mm;
    padding: ' . $padding . 'mm;
    border: 1px solid #ccc;
    margin: ' . ($template->gap_vertical ?: 2) . 'mm ' . ($template->gap_horizontal ?: 2) . 'mm;
    display: inline-block;
    text-align: center;
    box-sizing: border-box;
    vertical-align: top;
    overflow: hidden;
    position: relative;
}
.label[data-barcode-type="' . $template->barcode_type . '"] .barcode-container {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    max-height: ' . $maxBarcodeHeightPercent . ';
    margin-bottom: 2mm;
}
.label[data-barcode-type="' . $template->barcode_type . '"] .barcode-container svg,
.label[data-barcode-type="' . $template->barcode_type . '"] .barcode-container img {
    max-width: ' . $maxBarcodeWidth . 'mm;
    max-height: ' . $maxBarcodeHeight . 'mm;
    width: auto;
    height: auto;
    display: block;
    object-fit: contain;
}
.label[data-barcode-type="QRCODE"] .barcode-container svg,
.label[data-barcode-type="QRCODE"] .barcode-container img {
    aspect-ratio: 1;
    max-width: min(' . $maxBarcodeWidth . 'mm, 80%);
    max-height: min(' . $maxBarcodeHeight . 'mm, 80%);
}
.label .field {
    font-size: ' . ($template->text_size ?: 8) . 'pt;
    line-height: 1.1;
    margin: 0.5mm 0;
    word-wrap: break-word;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-height: ' . $textMaxHeight . ';
}
.label .field.multiline {
    white-space: normal;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}';
    }

    protected function generateOrderLabelHTML(Collection $orders, BarcodeTemplate $template, int $quantity): string
    {
        $html = '<!DOCTYPE html>';
        $html .= '<html><head>';
        $html .= '<meta charset="UTF-8">';
        $html .= '<title>Order Barcode Labels</title>';
        $html .= $this->generateLabelCSS($template);
        $html .= '</head><body>';

        $html .= '<div class="page">';

        $labelCount = 0;
        $maxLabelsPerPage = $template->columns_per_page * $template->rows_per_page;

        foreach ($orders as $order) {
            for ($i = 0; $i < $quantity; $i++) {
                if ($labelCount > 0 && $labelCount % $maxLabelsPerPage === 0) {
                    $html .= '</div><div class="page-break"></div><div class="page">';
                }

                $html .= $this->generateSingleOrderLabel($order, $template);
                $labelCount++;
            }
        }

        $html .= '</div>';
        $html .= '</body></html>';

        return $html;
    }

    protected function generateSingleOrderLabel(Order $order, BarcodeTemplate $template): string
    {
        $html = '<div class="label" data-barcode-type="' . e($template->barcode_type) . '">';

        // Generate barcode for order
        $barcodeSvg = $this->generateOrderBarcode($order, 'svg');
        $html .= '<div class="barcode">' . $barcodeSvg . '</div>';

        // Add order information
        $html .= '<div class="field single-line field-order-code">' . e($order->code) . '</div>';
        $html .= '<div class="field single-line field-order-date">' . e($order->created_at->format('Y-m-d')) . '</div>';

        if ($order->user) {
            $customerName = $order->user->name;
            $fieldClass = strlen($customerName) > 30 ? 'field multiline field-customer' : 'field single-line field-customer';
            $html .= '<div class="' . $fieldClass . '">' . e($customerName) . '</div>';
        }

        $html .= '</div>';

        return $html;
    }
}
