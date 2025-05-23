<?php

namespace FriendsOfBotble\BarcodeGenerator\Services;

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

        $barcodeType = $this->barcodeTypes[$type];

        if ($format === 'svg') {
            return $this->generator->generateBarcodeSVG($data, $barcodeType, 200, 50);
        }

        throw new \InvalidArgumentException("Unsupported format: {$format}");
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
        $html = '<div class="label">';

        // Generate barcode
        if ($product->barcode || $product->sku) {
            $barcodeData = $product->barcode ?: $product->sku;
            $barcodeSvg = $this->generateBarcode($barcodeData, $template->barcode_type, 'svg');
            $html .= '<div class="barcode">' . $barcodeSvg . '</div>';
        }

        // Add text fields based on template configuration
        if ($template->fields && is_array($template->fields)) {
            foreach ($template->fields as $field) {
                $value = $this->getProductFieldValue($product, $field);
                if ($value) {
                    $html .= '<div class="field field-' . $field . '">' . e($value) . '</div>';
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

        $css .= '.label { ';
        $css .= 'width: ' . $template->label_width . 'mm; ';
        $css .= 'height: ' . $template->label_height . 'mm; ';
        $css .= 'padding: ' . $template->padding . 'mm; ';
        $css .= 'border: 1px solid #ccc; ';
        $css .= 'display: flex; ';
        $css .= 'flex-direction: column; ';
        $css .= 'align-items: center; ';
        $css .= 'justify-content: center; ';
        $css .= 'text-align: center; ';
        $css .= 'box-sizing: border-box; ';
        $css .= '}';

        $css .= '.barcode { ';
        $css .= 'width: ' . $template->barcode_width . 'mm; ';
        $css .= 'height: ' . $template->barcode_height . 'mm; ';
        $css .= 'margin-bottom: 2mm; ';
        $css .= '}';

        $css .= '.barcode svg { width: 100%; height: 100%; }';

        $css .= '.field { ';
        $css .= 'font-size: ' . $template->text_size . 'pt; ';
        $css .= 'line-height: 1.2; ';
        $css .= 'margin: 1mm 0; ';
        $css .= 'word-wrap: break-word; ';
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
                $html = str_replace('{barcode_image}', $barcodeDataUri, $html);
            } else {
                $html = str_replace('{barcode_image}', '', $html);
            }
        }

        // Replace product fields
        $availableFields = (new BarcodeTemplate())->getAvailableFields();
        foreach ($availableFields as $field => $label) {
            $placeholder = '{' . $field . '}';
            if (strpos($html, $placeholder) !== false) {
                $value = $this->getProductFieldValue($product, $field) ?: '';
                $html = str_replace($placeholder, e($value), $html);
            }
        }

        return $html;
    }

    protected function processCustomOrderTemplate(Order $order, BarcodeTemplate $template): string
    {
        $html = $template->template_html;

        // Replace barcode image
        if (strpos($html, '{barcode_image}') !== false) {
            $barcodeSvg = $this->generateOrderBarcode($order, 'svg');
            $barcodeDataUri = 'data:image/svg+xml;base64,' . base64_encode($barcodeSvg);
            $html = str_replace('{barcode_image}', $barcodeDataUri, $html);
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
        return '@media print { .page-break { page-break-before: always; } }
body { margin: 0; padding: 0; font-family: Arial, sans-serif; }
.label {
    width: ' . ($template->label_width ?: 70) . 'mm;
    height: ' . ($template->label_height ?: 30) . 'mm;
    padding: ' . ($template->padding ?: 2) . 'mm;
    border: 1px solid #ccc;
    margin: ' . ($template->gap_vertical ?: 2) . 'mm ' . ($template->gap_horizontal ?: 2) . 'mm;
    display: inline-block;
    text-align: center;
    box-sizing: border-box;
    vertical-align: top;
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
        $html = '<div class="label">';

        // Generate barcode for order
        $barcodeSvg = $this->generateOrderBarcode($order, 'svg');
        $html .= '<div class="barcode">' . $barcodeSvg . '</div>';

        // Add order information
        $html .= '<div class="field field-order-code">' . e($order->code) . '</div>';
        $html .= '<div class="field field-order-date">' . e($order->created_at->format('Y-m-d')) . '</div>';

        if ($order->user) {
            $html .= '<div class="field field-customer">' . e($order->user->name) . '</div>';
        }

        $html .= '</div>';

        return $html;
    }
}
