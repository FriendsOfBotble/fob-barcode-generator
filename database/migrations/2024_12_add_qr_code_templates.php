<?php

use FriendsOfBotble\BarcodeGenerator\Models\BarcodeTemplate;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        $this->addQRCodeTemplates();
    }

    public function down(): void
    {
        // Remove QR code templates
        BarcodeTemplate::query()
            ->whereIn('name', [
                'QR Code Labels',
                'QR Code Thermal Labels',
                'QR Code Small Labels',
            ])
            ->delete();
    }

    private function addQRCodeTemplates(): void
    {
        $qrTemplates = [
            [
                'name' => 'QR Code Labels',
                'description' => 'QR code labels for product information and URLs',
                'paper_size' => 'A4',
                'orientation' => 'portrait',
                'label_width' => 50.00,
                'label_height' => 50.00,
                'margin_top' => 10.00,
                'margin_bottom' => 10.00,
                'margin_left' => 10.00,
                'margin_right' => 10.00,
                'padding' => 3.00,
                'columns_per_page' => 4,
                'rows_per_page' => 5,
                'barcode_type' => 'QRCODE',
                'barcode_width' => 40.00,
                'barcode_height' => 40.00,
                'include_text' => true,
                'text_position' => 'bottom',
                'text_size' => 8,
                'fields' => ['name', 'sku'],
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'name' => 'QR Code Thermal Labels',
                'description' => 'Large QR code labels for thermal printers',
                'paper_size' => 'thermal_4x6',
                'orientation' => 'portrait',
                'label_width' => 101.60,
                'label_height' => 152.40,
                'margin_top' => 5.00,
                'margin_bottom' => 5.00,
                'margin_left' => 5.00,
                'margin_right' => 5.00,
                'padding' => 10.00,
                'columns_per_page' => 1,
                'rows_per_page' => 1,
                'barcode_type' => 'QRCODE',
                'barcode_width' => 80.00,
                'barcode_height' => 80.00,
                'include_text' => true,
                'text_position' => 'bottom',
                'text_size' => 12,
                'fields' => ['name', 'sku', 'price'],
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'name' => 'QR Code Small Labels',
                'description' => 'Compact QR code labels for small products',
                'paper_size' => 'A4',
                'orientation' => 'portrait',
                'label_width' => 35.00,
                'label_height' => 35.00,
                'margin_top' => 10.00,
                'margin_bottom' => 10.00,
                'margin_left' => 10.00,
                'margin_right' => 10.00,
                'padding' => 2.00,
                'columns_per_page' => 5,
                'rows_per_page' => 7,
                'barcode_type' => 'QRCODE',
                'barcode_width' => 25.00,
                'barcode_height' => 25.00,
                'include_text' => true,
                'text_position' => 'bottom',
                'text_size' => 6,
                'fields' => ['sku'],
                'is_default' => false,
                'is_active' => true,
            ],
        ];

        foreach ($qrTemplates as $template) {
            BarcodeTemplate::query()->updateOrCreate(
                ['name' => $template['name']],
                $template
            );
        }
    }
};
