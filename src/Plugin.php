<?php

namespace FriendsOfBotble\BarcodeGenerator;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Facades\Setting;
use Illuminate\Support\Facades\Schema;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        // Drop all database tables created by this plugin
        Schema::dropIfExists('barcode_templates');

        // Remove all plugin settings
        Setting::delete([
            // Basic barcode settings
            'barcode_generator_default_type',
            'barcode_generator_default_width',
            'barcode_generator_default_height',
            'barcode_generator_include_text',
            'barcode_generator_text_position',
            'barcode_generator_text_size',

            // Label layout settings
            'barcode_generator_label_width',
            'barcode_generator_label_height',
            'barcode_generator_label_margin',
            'barcode_generator_label_padding',
            'barcode_generator_paper_size',
            'barcode_generator_orientation',
            'barcode_generator_columns_per_page',
            'barcode_generator_rows_per_page',

            // Advanced settings
            'barcode_generator_auto_generate_sku',
            'barcode_generator_sku_prefix',
            'barcode_generator_enable_batch_mode',
            'barcode_generator_max_products_per_batch',

            // Appearance settings
            'barcode_generator_background_color',
            'barcode_generator_text_color',
            'barcode_generator_border_enabled',
            'barcode_generator_border_width',
            'barcode_generator_border_color',

            // Field display settings
            'barcode_generator_show_product_name',
            'barcode_generator_show_product_sku',
            'barcode_generator_show_product_barcode',
            'barcode_generator_show_product_price',
            'barcode_generator_show_product_sale_price',
            'barcode_generator_show_product_price_smart',
            'barcode_generator_show_product_price_with_original',
            'barcode_generator_show_product_price_sale_only',
            'barcode_generator_show_product_price_original_only',
            'barcode_generator_show_product_brand',
            'barcode_generator_show_product_category',
            'barcode_generator_show_product_attributes',
            'barcode_generator_show_product_description',
            'barcode_generator_show_product_weight',
            'barcode_generator_show_product_dimensions',
            'barcode_generator_show_product_stock',
            'barcode_generator_show_current_date',
            'barcode_generator_show_company_name',
        ]);
    }
}
