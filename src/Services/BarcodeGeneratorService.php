<?php

namespace FriendsOfBotble\BarcodeGenerator\Services;

use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\Product;
use FriendsOfBotble\BarcodeGenerator\Libraries\BarcodeGenerator;
use FriendsOfBotble\BarcodeGenerator\Models\BarcodeTemplate;
use Illuminate\Support\Collection;

class BarcodeGeneratorService
{
    protected BarcodeGenerator $generator;

    protected array $barcodeTypes = [
        'C128' => 'CODE128',
        'EAN13' => 'EAN13',
        'EAN8' => 'EAN8',
        'UPCA' => 'EAN13', // Use EAN13 for UPC-A
        'UPCE' => 'EAN8', // Use EAN8 for UPC-E
    ];

    public function __construct()
    {
        $this->generator = new BarcodeGenerator();
    }

    public function generateBarcode(string $data, string $type = 'C128', string $format = 'svg'): string
    {
        if (! isset($this->barcodeTypes[$type])) {
            throw new \InvalidArgumentException("Unsupported barcode type: {$type}");
        }

        $barcodeType = $this->barcodeTypes[$type];

        if ($format === 'svg') {
            return $this->generator->generateBarcodeSVG($data, $barcodeType, 200, 50);
        }

        throw new \InvalidArgumentException("Unsupported format: {$format}");
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

        return $this->generateBarcode($barcodeData, 'C128', $format);
    }

    public function generateLabels(Collection $products, BarcodeTemplate $template, int $quantity = 1): string
    {
        $html = $this->generateLabelHTML($products, $template, $quantity);

        return $html;
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
            case 'name':
                return $product->name;
            case 'sku':
                return $product->sku;
            case 'barcode':
                return $product->barcode;
            case 'price':
                return format_price($product->price);
            case 'sale_price':
                return $product->sale_price ? format_price($product->sale_price) : null;
            case 'brand':
                return $product->brand?->name;
            case 'category':
                return $product->categories->first()?->name;
            case 'attributes':
                return $product->variationInfo?->variationItems
                    ->pluck('attribute_sets.title')
                    ->filter()
                    ->implode(', ');
            default:
                return null;
        }
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
            return 'EAN13';
        }

        // EAN-8: 8 digits
        if ($length === 8 && ctype_digit($data)) {
            return 'EAN8';
        }

        // UPC-A: 12 digits
        if ($length === 12 && ctype_digit($data)) {
            return 'UPCA';
        }

        // UPC-E: 6-8 digits
        if ($length >= 6 && $length <= 8 && ctype_digit($data)) {
            return 'UPCE';
        }

        // Default to Code 128 for everything else
        return 'C128';
    }
}
