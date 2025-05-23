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
        'padding',
        'columns_per_page',
        'rows_per_page',
        'barcode_type',
        'barcode_width',
        'barcode_height',
        'include_text',
        'text_position',
        'text_size',
        'fields',
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
        'padding' => 'decimal:2',
        'barcode_width' => 'decimal:2',
        'barcode_height' => 'decimal:2',
        'columns_per_page' => 'integer',
        'rows_per_page' => 'integer',
        'text_size' => 'integer',
        'fields' => 'array',
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
            'name' => trans('plugins/fob-barcode-generator::barcode-generator.fields.name'),
            'sku' => trans('plugins/fob-barcode-generator::barcode-generator.fields.sku'),
            'barcode' => trans('plugins/fob-barcode-generator::barcode-generator.fields.barcode'),
            'price' => trans('plugins/fob-barcode-generator::barcode-generator.fields.price'),
            'sale_price' => trans('plugins/fob-barcode-generator::barcode-generator.fields.sale_price'),
            'brand' => trans('plugins/fob-barcode-generator::barcode-generator.fields.brand'),
            'category' => trans('plugins/fob-barcode-generator::barcode-generator.fields.category'),
            'attributes' => trans('plugins/fob-barcode-generator::barcode-generator.fields.attributes'),
        ];
    }
}
