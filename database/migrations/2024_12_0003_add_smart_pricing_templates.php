<?php

use FriendsOfBotble\BarcodeGenerator\Enums\BarcodeTypeEnum;
use FriendsOfBotble\BarcodeGenerator\Models\BarcodeTemplate;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        $this->seedSmartPricingTemplates();
    }

    public function down(): void
    {
        BarcodeTemplate::query()
            ->whereIn('name', [
                'Price Labels with Sale Display',
                'Smart Price Thermal Labels',
            ])
            ->delete();
    }

    private function seedSmartPricingTemplates(): void
    {
        $templates = [
            [
                'name' => 'Price Labels with Sale Display',
                'description' => 'Labels with smart pricing that shows sale prices with strikethrough original prices',
                'paper_size' => 'A4',
                'orientation' => 'portrait',
                'label_width' => 60.00,
                'label_height' => 40.00,
                'margin_top' => 10.00,
                'margin_bottom' => 10.00,
                'margin_left' => 10.00,
                'margin_right' => 10.00,
                'gap_horizontal' => 2.00,
                'gap_vertical' => 2.00,
                'padding' => 3.00,
                'columns_per_page' => 3,
                'rows_per_page' => 6,
                'labels_per_page' => 18,
                'barcode_type' => BarcodeTypeEnum::EAN13,
                'barcode_width' => 50.00,
                'barcode_height' => 20.00,
                'include_text' => true,
                'text_position' => 'bottom',
                'text_size' => 10,
                'fields' => ['product_sku', 'product_price_with_original'],
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Smart Price Thermal Labels',
                'description' => 'Thermal labels with smart pricing that automatically shows the best price',
                'paper_size' => 'thermal_4x6',
                'orientation' => 'portrait',
                'label_width' => 101.60,
                'label_height' => 152.40,
                'margin_top' => 5.00,
                'margin_bottom' => 5.00,
                'margin_left' => 5.00,
                'margin_right' => 5.00,
                'gap_horizontal' => 2.00,
                'gap_vertical' => 2.00,
                'padding' => 8.00,
                'columns_per_page' => 1,
                'rows_per_page' => 1,
                'labels_per_page' => 1,
                'barcode_type' => BarcodeTypeEnum::CODE128,
                'barcode_width' => 85.00,
                'barcode_height' => 25.00,
                'include_text' => true,
                'text_position' => 'bottom',
                'text_size' => 14,
                'fields' => ['product_sku', 'product_price_smart'],
                'is_default' => false,
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            BarcodeTemplate::query()->updateOrCreate(
                ['name' => $template['name']],
                $template
            );
        }
    }
};
