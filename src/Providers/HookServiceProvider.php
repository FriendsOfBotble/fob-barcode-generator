<?php

namespace FriendsOfBotble\BarcodeGenerator\Providers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\ServiceProvider;
use Botble\Ecommerce\Models\Product;
use FriendsOfBotble\BarcodeGenerator\Services\BarcodeGeneratorService;
use Illuminate\Support\Facades\Route;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_action(BASE_ACTION_META_BOXES, [$this, 'addBarcodeMetaBox'], 55, 2);
        add_filter('ecommerce_product_extra_buttons', [$this, 'addProductBarcodeButton'], 10, 2);
    }

    protected function isVendorPanel(): bool
    {
        if (! is_plugin_active('marketplace')) {
            return false;
        }

        return request()->segment(1) === config('plugins.marketplace.general.vendor_panel_dir', 'vendor');
    }

    protected function getBarcodeGeneratorUrl(int|string $productId): string
    {
        if ($this->isVendorPanel() && Route::has('marketplace.vendor.barcode-generator.index')) {
            return route('marketplace.vendor.barcode-generator.index', ['products[]' => $productId]);
        }

        return route('barcode-generator.index', ['products[]' => $productId]);
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
            $html .= '<a href="' . $this->getBarcodeGeneratorUrl($product->id) . '" class="btn btn-sm btn-primary" target="_blank">';
            $html .= BaseHelper::renderIcon('ti ti-printer') . ' ' . trans('plugins/fob-barcode-generator::barcode-generator.print_label');
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

        $button = '<a href="' . $this->getBarcodeGeneratorUrl($product->id) . '" class="btn btn-info btn-sm" title="' . trans('plugins/fob-barcode-generator::barcode-generator.generate_barcode') . '">';
        $button .= BaseHelper::renderIcon('ti ti-barcode');
        $button .= '</a>';

        return $buttons . $button;
    }
}
