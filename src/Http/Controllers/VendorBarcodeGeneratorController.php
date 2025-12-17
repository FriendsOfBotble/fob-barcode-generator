<?php

namespace FriendsOfBotble\BarcodeGenerator\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Models\Product;
use FriendsOfBotble\BarcodeGenerator\Models\BarcodeTemplate;
use FriendsOfBotble\BarcodeGenerator\Services\BarcodeGeneratorService;
use Illuminate\Http\Request;

class VendorBarcodeGeneratorController extends BaseController
{
    public function __construct(protected BarcodeGeneratorService $barcodeService)
    {
        abort_unless(is_plugin_active('marketplace'), 404);
    }

    protected function getStoreId(): ?int
    {
        return auth('customer')->user()->store?->id;
    }

    public function index(Request $request)
    {
        $this->pageTitle(trans('plugins/fob-barcode-generator::barcode-generator.generate.title'));

        Assets::addStylesDirectly('vendor/core/plugins/fob-barcode-generator/css/barcode-generator.css')
            ->addScriptsDirectly('vendor/core/plugins/fob-barcode-generator/js/barcode-generator.js')
            ->addScripts(['select2']);

        $storeId = $this->getStoreId();

        $products = Product::query()
            ->where('status', 'published')
            ->where('store_id', $storeId)
            ->where(function ($query) {
                $query->whereNotNull('sku')
                    ->orWhereNotNull('barcode');
            })
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'barcode']);

        $templates = BarcodeTemplate::query()
            ->where('is_active', true)
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();

        $selectedProductIds = [];
        if ($request->has('products')) {
            $selectedProductIds = (array) $request->input('products');
            $selectedProductIds = Product::whereIn('id', $selectedProductIds)
                ->where('status', 'published')
                ->where('store_id', $storeId)
                ->where(function ($query) {
                    $query->whereNotNull('sku')
                        ->orWhereNotNull('barcode');
                })
                ->pluck('id')
                ->toArray();
        }

        $selectedTemplateId = null;
        if ($request->has('template_id')) {
            $templateId = $request->input('template_id');
            if (BarcodeTemplate::where('id', $templateId)->where('is_active', true)->exists()) {
                $selectedTemplateId = $templateId;
            }
        }

        $selectedQuantity = $request->input('quantity', 1);
        if ($selectedQuantity < 1 || $selectedQuantity > 100) {
            $selectedQuantity = 1;
        }

        return view('plugins/fob-barcode-generator::vendor.generate', compact(
            'products',
            'templates',
            'selectedProductIds',
            'selectedQuantity'
        ));
    }

    public function generate(Request $request): BaseHttpResponse
    {
        $storeId = $this->getStoreId();

        $request->validate([
            'products' => 'required|array|min:1',
            'products.*' => 'exists:ec_products,id',
            'template_id' => 'required|exists:barcode_templates,id',
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $products = Product::whereIn('id', $request->input('products'))
            ->where('store_id', $storeId)
            ->get();

        if ($products->isEmpty()) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/fob-barcode-generator::barcode-generator.messages.no_products_found'));
        }

        $template = BarcodeTemplate::findOrFail($request->input('template_id'));
        $quantity = $request->input('quantity', 1);

        try {
            $html = $this->barcodeService->generateLabels($products, $template, $quantity);

            return $this
                ->httpResponse()
                ->setData([
                    'html' => $html,
                    'download_url' => route('marketplace.vendor.barcode-generator.download', [
                        'products' => $request->input('products'),
                        'template_id' => $request->input('template_id'),
                        'quantity' => $quantity,
                    ]),
                ])
                ->setMessage(trans('plugins/fob-barcode-generator::barcode-generator.messages.barcode_generated'));
        } catch (\Exception $e) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($e->getMessage());
        }
    }

    public function preview(Request $request)
    {
        $storeId = $this->getStoreId();

        $request->validate([
            'products' => 'required|array|min:1',
            'products.*' => 'exists:ec_products,id',
            'template_id' => 'required|exists:barcode_templates,id',
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $products = Product::whereIn('id', $request->input('products'))
            ->where('store_id', $storeId)
            ->get();

        if ($products->isEmpty()) {
            return response('<p>No valid products found</p>', 400);
        }

        $template = BarcodeTemplate::findOrFail($request->input('template_id'));
        $quantity = $request->input('quantity', 1);

        $html = $this->barcodeService->generateLabels($products, $template, $quantity);

        return response($html)->header('Content-Type', 'text/html');
    }

    public function download(Request $request)
    {
        $storeId = $this->getStoreId();

        $request->validate([
            'products' => 'required|array|min:1',
            'products.*' => 'exists:ec_products,id',
            'template_id' => 'required|exists:barcode_templates,id',
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $products = Product::whereIn('id', $request->input('products'))
            ->where('store_id', $storeId)
            ->get();

        if ($products->isEmpty()) {
            abort(404);
        }

        $template = BarcodeTemplate::findOrFail($request->input('template_id'));
        $quantity = $request->input('quantity', 1);

        $html = $this->barcodeService->generateLabels($products, $template, $quantity);

        $filename = 'barcode-labels-' . now()->format('Y-m-d-H-i-s') . '.html';

        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
