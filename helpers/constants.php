<?php

if (! defined('BARCODE_TEMPLATE_MODULE_SCREEN_NAME')) {
    define('BARCODE_TEMPLATE_MODULE_SCREEN_NAME', 'barcode-template');
}

if (! function_exists('barcode_generator_setting')) {
    /**
     * Get barcode generator setting value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function barcode_generator_setting(string $key, $default = null)
    {
        return setting("barcode_generator_{$key}", $default);
    }
}

if (! function_exists('barcode_generator_default_settings')) {
    /**
     * Get default barcode generator settings
     *
     * @return array
     */
    function barcode_generator_default_settings(): array
    {
        return [
            'default_type' => 'C128',
            'default_width' => 40,
            'default_height' => 15,
            'include_text' => true,
            'text_position' => 'bottom',
            'text_size' => 8,
            'label_width' => 50,
            'label_height' => 30,
            'label_margin' => 10,
            'label_padding' => 2,
            'paper_size' => 'A4',
            'orientation' => 'portrait',
            'columns_per_page' => 4,
            'rows_per_page' => 10,
            'auto_generate_sku' => false,
            'sku_prefix' => 'SKU',
            'enable_batch_mode' => true,
            'max_products_per_batch' => 100,
            'background_color' => '#FFFFFF',
            'text_color' => '#000000',
            'border_enabled' => false,
            'border_width' => 1,
            'border_color' => '#000000',
            // Field display settings
            'show_product_name' => false,
            'show_product_sku' => true,
            'show_product_barcode' => false,
            'show_product_price' => false,
            'show_product_sale_price' => false,
            'show_product_price_smart' => false,
            'show_product_price_with_original' => true,
            'show_product_price_sale_only' => false,
            'show_product_price_original_only' => false,
            'show_product_brand' => false,
            'show_product_category' => false,
            'show_product_attributes' => false,
            'show_product_description' => false,
            'show_product_weight' => false,
            'show_product_dimensions' => false,
            'show_product_stock' => false,
            'show_current_date' => false,
            'show_company_name' => false,
        ];
    }
}

if (! function_exists('barcode_generator_get_appearance_settings')) {
    /**
     * Get appearance settings for barcode generation
     *
     * @return array
     */
    function barcode_generator_get_appearance_settings(): array
    {
        return [
            'background_color' => barcode_generator_setting('background_color', '#FFFFFF'),
            'text_color' => barcode_generator_setting('text_color', '#000000'),
            'border_enabled' => barcode_generator_setting('border_enabled', false),
            'border_width' => barcode_generator_setting('border_width', 1),
            'border_color' => barcode_generator_setting('border_color', '#000000'),
        ];
    }
}

if (! function_exists('barcode_generator_get_enabled_fields')) {
    /**
     * Get enabled product fields for barcode generation
     *
     * @return array
     */
    function barcode_generator_get_enabled_fields(): array
    {
        $enabledFields = [];
        $availableFields = [
            'product_name',
            'product_sku',
            'product_barcode',
            'product_price',
            'product_sale_price',
            'product_price_smart',
            'product_price_with_original',
            'product_price_sale_only',
            'product_price_original_only',
            'product_brand',
            'product_category',
            'product_attributes',
            'product_description',
            'product_weight',
            'product_dimensions',
            'product_stock',
            'current_date',
            'company_name',
        ];

        foreach ($availableFields as $field) {
            if (barcode_generator_setting("show_{$field}", false)) {
                $enabledFields[] = $field;
            }
        }

        return $enabledFields;
    }
}
