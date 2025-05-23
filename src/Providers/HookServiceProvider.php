<?php

namespace FriendsOfBotble\BarcodeGenerator\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\Ecommerce\Models\Product;
use FriendsOfBotble\BarcodeGenerator\Services\BarcodeGeneratorService;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_action(BASE_ACTION_META_BOXES, [$this, 'addBarcodeMetaBox'], 55, 2);
        add_filter('ecommerce_product_extra_buttons', [$this, 'addProductBarcodeButton'], 10, 2);
    }

    public function addBarcodeMetaBox(string $context, object $object): void
    {
        if ($context === 'advanced' && $object instanceof Product && $object->exists) {
            add_meta_box(
                'barcode-generator-meta-box',
                trans('plugins/fob-barcode-generator::barcode-generator.name'),
                [$this, 'renderBarcodeMetaBox'],
                get_class($object),
                'advanced'
            );
        }
    }

    public function renderBarcodeMetaBox(Product $product): string
    {
        if (! $product->barcode && ! $product->sku) {
            return '<p class="text-muted">' . trans('plugins/fob-barcode-generator::barcode-generator.no_barcode_data') . '</p>';
        }

        $barcodeService = app(BarcodeGeneratorService::class);

        try {
            $barcodeSvg = $barcodeService->generateProductBarcode($product, 'svg');

            if (! $barcodeSvg) {
                return '<p class="text-muted">' . trans('plugins/fob-barcode-generator::barcode-generator.no_barcode_data') . '</p>';
            }

            $html = '<div class="barcode-preview text-center">';
            $html .= '<div class="mb-2">' . $barcodeSvg . '</div>';
            $html .= '<p class="small text-muted">';

            if ($product->barcode) {
                $html .= trans('plugins/fob-barcode-generator::barcode-generator.barcode_value', ['value' => $product->barcode]);
            } else {
                $html .= trans('plugins/fob-barcode-generator::barcode-generator.sku_value', ['value' => $product->sku]);
            }

            $html .= '</p>';
            $html .= '<div class="d-flex gap-2 justify-content-center">';
            $html .= '<a href="' . route('barcode-generator.index', ['products[]' => $product->id]) . '" class="btn btn-sm btn-primary" target="_blank">';
            $html .= '<i class="ti ti-printer"></i> ' . trans('plugins/fob-barcode-generator::barcode-generator.print_label');
            $html .= '</a>';
            $html .= '</div>';
            $html .= '</div>';

            return $html;
        } catch (\Exception $e) {
            return '<p class="text-danger">' . trans('plugins/fob-barcode-generator::barcode-generator.generation_error') . ': ' . $e->getMessage() . '</p>';
        }
    }

    public function addProductBarcodeButton(string $buttons, Product $product): string
    {
        if (! auth()->user()->hasPermission('barcode-generator.generate')) {
            return $buttons;
        }

        if (! $product->barcode && ! $product->sku) {
            return $buttons;
        }

        $button = '<a href="' . route('barcode-generator.index', ['products[]' => $product->id]) . '" class="btn btn-info btn-sm" title="' . trans('plugins/fob-barcode-generator::barcode-generator.generate_barcode') . '">';
        $button .= '<i class="ti ti-barcode"></i>';
        $button .= '</a>';

        return $buttons . $button;
    }
}
