<?php

namespace FriendsOfBotble\BarcodeGenerator\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Models\Product;
use FriendsOfBotble\BarcodeGenerator\Models\BarcodeTemplate;
use FriendsOfBotble\BarcodeGenerator\Services\BarcodeGeneratorService;
use Illuminate\Http\Request;

class BarcodeGeneratorController extends BaseController
{
    public function __construct(protected BarcodeGeneratorService $barcodeService)
    {
    }

    public function index()
    {
        $this->pageTitle(trans('plugins/fob-barcode-generator::barcode-generator.generate.title'));

        Assets::addStylesDirectly('vendor/core/plugins/fob-barcode-generator/css/barcode-generator.css')
            ->addScriptsDirectly('vendor/core/plugins/fob-barcode-generator/js/barcode-generator.js')
            ->addScripts(['select2']);

        $products = Product::query()
            ->where('status', 'published')
            ->whereNotNull('sku')
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'barcode']);

        $templates = BarcodeTemplate::query()
            ->where('is_active', true)
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();

        return view('plugins/fob-barcode-generator::generate', compact('products', 'templates'));
    }

    public function generate(Request $request): BaseHttpResponse
    {
        $request->validate([
            'products' => 'required|array|min:1',
            'products.*' => 'exists:ec_products,id',
            'template_id' => 'required|exists:barcode_templates,id',
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $products = Product::whereIn('id', $request->input('products'))->get();
        $template = BarcodeTemplate::findOrFail($request->input('template_id'));
        $quantity = $request->input('quantity', 1);

        try {
            $html = $this->barcodeService->generateLabels($products, $template, $quantity);

            return $this
                ->httpResponse()
                ->setData([
                    'html' => $html,
                    'download_url' => route('barcode-generator.download', [
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
        $request->validate([
            'products' => 'required|array|min:1',
            'products.*' => 'exists:ec_products,id',
            'template_id' => 'required|exists:barcode_templates,id',
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $products = Product::whereIn('id', $request->input('products'))->get();
        $template = BarcodeTemplate::findOrFail($request->input('template_id'));
        $quantity = $request->input('quantity', 1);

        $html = $this->barcodeService->generateLabels($products, $template, $quantity);

        return response($html)->header('Content-Type', 'text/html');
    }

    public function download(Request $request)
    {
        $request->validate([
            'products' => 'required|array|min:1',
            'products.*' => 'exists:ec_products,id',
            'template_id' => 'required|exists:barcode_templates,id',
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $products = Product::whereIn('id', $request->input('products'))->get();
        $template = BarcodeTemplate::findOrFail($request->input('template_id'));
        $quantity = $request->input('quantity', 1);

        $html = $this->barcodeService->generateLabels($products, $template, $quantity);

        $filename = 'barcode-labels-' . now()->format('Y-m-d-H-i-s') . '.html';

        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
