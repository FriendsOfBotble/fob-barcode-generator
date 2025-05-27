<?php

namespace FriendsOfBotble\BarcodeGenerator\Models;

use Botble\Base\Models\BaseModel;

class BarcodeTemplate extends BaseModel
{
    protected $table = 'barcode_templates';

    protected $fillable = [
        'name',
        'description',
        'paper_size',
        'orientation',
        'label_width',
        'label_height',
        'margin_top',
        'margin_bottom',
        'margin_left',
        'margin_right',
        'gap_horizontal',
        'gap_vertical',
        'padding',
        'columns_per_page',
        'rows_per_page',
        'labels_per_page',
        'barcode_type',
        'barcode_width',
        'barcode_height',
        'include_text',
        'text_position',
        'text_size',
        'template_html',
        'template_css',
        'fields',
        'custom_fields',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'label_width' => 'decimal:2',
        'label_height' => 'decimal:2',
        'margin_top' => 'decimal:2',
        'margin_bottom' => 'decimal:2',
        'margin_left' => 'decimal:2',
        'margin_right' => 'decimal:2',
        'gap_horizontal' => 'decimal:2',
        'gap_vertical' => 'decimal:2',
        'padding' => 'decimal:2',
        'barcode_width' => 'decimal:2',
        'barcode_height' => 'decimal:2',
        'columns_per_page' => 'integer',
        'rows_per_page' => 'integer',
        'labels_per_page' => 'integer',
        'text_size' => 'integer',
        'fields' => 'array',
        'custom_fields' => 'array',
        'include_text' => 'boolean',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public static function getDefaultTemplate(): ?self
    {
        return static::where('is_default', true)->where('is_active', true)->first();
    }

    public function getAvailableFields(): array
    {
        return [
            'product_name' => trans('plugins/fob-barcode-generator::barcode-generator.fields.product_name'),
            'product_sku' => trans('plugins/fob-barcode-generator::barcode-generator.fields.product_sku'),
            'product_barcode' => trans('plugins/fob-barcode-generator::barcode-generator.fields.product_barcode'),
            'product_price' => trans('plugins/fob-barcode-generator::barcode-generator.fields.product_price'),
            'product_sale_price' => trans('plugins/fob-barcode-generator::barcode-generator.fields.product_sale_price'),
            'product_price_smart' => trans('plugins/fob-barcode-generator::barcode-generator.fields.product_price_smart'),
            'product_price_with_original' => trans('plugins/fob-barcode-generator::barcode-generator.fields.product_price_with_original'),
            'product_price_sale_only' => trans('plugins/fob-barcode-generator::barcode-generator.fields.product_price_sale_only'),
            'product_price_original_only' => trans('plugins/fob-barcode-generator::barcode-generator.fields.product_price_original_only'),
            'product_brand' => trans('plugins/fob-barcode-generator::barcode-generator.fields.product_brand'),
            'product_category' => trans('plugins/fob-barcode-generator::barcode-generator.fields.product_category'),
            'product_attributes' => trans('plugins/fob-barcode-generator::barcode-generator.fields.product_attributes'),
            'product_description' => trans('plugins/fob-barcode-generator::barcode-generator.fields.product_description'),
            'product_weight' => trans('plugins/fob-barcode-generator::barcode-generator.fields.product_weight'),
            'product_dimensions' => trans('plugins/fob-barcode-generator::barcode-generator.fields.product_dimensions'),
            'product_stock' => trans('plugins/fob-barcode-generator::barcode-generator.fields.product_stock'),
            'barcode_image' => trans('plugins/fob-barcode-generator::barcode-generator.fields.barcode_image'),
            'current_date' => trans('plugins/fob-barcode-generator::barcode-generator.fields.current_date'),
            'company_name' => trans('plugins/fob-barcode-generator::barcode-generator.fields.company_name'),
        ];
    }

    public function getAvailableOrderFields(): array
    {
        return [
            'order_id' => trans('plugins/fob-barcode-generator::barcode-generator.fields.order_id'),
            'order_code' => trans('plugins/fob-barcode-generator::barcode-generator.fields.order_code'),
            'order_date' => trans('plugins/fob-barcode-generator::barcode-generator.fields.order_date'),
            'order_status' => trans('plugins/fob-barcode-generator::barcode-generator.fields.order_status'),
            'order_total' => trans('plugins/fob-barcode-generator::barcode-generator.fields.order_total'),
            'customer_name' => trans('plugins/fob-barcode-generator::barcode-generator.fields.customer_name'),
            'customer_email' => trans('plugins/fob-barcode-generator::barcode-generator.fields.customer_email'),
            'customer_phone' => trans('plugins/fob-barcode-generator::barcode-generator.fields.customer_phone'),
            'shipping_address' => trans('plugins/fob-barcode-generator::barcode-generator.fields.shipping_address'),
            'billing_address' => trans('plugins/fob-barcode-generator::barcode-generator.fields.billing_address'),
        ];
    }

    public function getAllAvailableFields(): array
    {
        return array_merge($this->getAvailableFields(), $this->getAvailableOrderFields());
    }
}
