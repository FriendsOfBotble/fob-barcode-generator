<?php

namespace FriendsOfBotble\BarcodeGenerator\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use FriendsOfBotble\BarcodeGenerator\Enums\BarcodeTypeEnum;
use FriendsOfBotble\BarcodeGenerator\Services\BarcodeGeneratorService;

class TestBarcodeController extends BaseController
{
    public function test(BarcodeGeneratorService $service)
    {
        try {
            // Test different barcode types
            $testData = [
                ['data' => '123456789012', 'type' => BarcodeTypeEnum::CODE128, 'name' => 'CODE128'],
                ['data' => '1234567890123', 'type' => BarcodeTypeEnum::EAN13, 'name' => 'EAN13'],
                ['data' => '12345678', 'type' => BarcodeTypeEnum::EAN8, 'name' => 'EAN8'],
                ['data' => 'TEST123', 'type' => BarcodeTypeEnum::CODE39, 'name' => 'CODE39'],
                ['data' => 'https://example.com', 'type' => BarcodeTypeEnum::QRCODE, 'name' => 'QR Code'],
            ];

            $results = [];
            foreach ($testData as $test) {
                try {
                    $barcode = $service->generateBarcode($test['data'], $test['type']);
                    $results[] = [
                        'name' => $test['name'],
                        'type' => $test['type'],
                        'data' => $test['data'],
                        'success' => true,
                        'barcode' => $barcode,
                    ];
                } catch (\Exception $e) {
                    $results[] = [
                        'name' => $test['name'],
                        'type' => $test['type'],
                        'data' => $test['data'],
                        'success' => false,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            // Test legacy type mapping
            try {
                $legacyBarcode = $service->generateBarcode('TEST123', 'C128');
                $results[] = [
                    'name' => 'Legacy C128',
                    'type' => 'C128',
                    'data' => 'TEST123',
                    'success' => true,
                    'barcode' => $legacyBarcode,
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'name' => 'Legacy C128',
                    'type' => 'C128',
                    'data' => 'TEST123',
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }

            return response()->json([
                'message' => 'Barcode generation test completed',
                'results' => $results,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Test failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
