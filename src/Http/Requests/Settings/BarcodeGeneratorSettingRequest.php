<?php

namespace FriendsOfBotble\BarcodeGenerator\Http\Requests\Settings;

use Botble\Base\Rules\OnOffRule;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class BarcodeGeneratorSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'barcode_generator_default_type' => ['required', 'string', Rule::in(['C128', 'EAN13', 'EAN8', 'UPCA', 'UPCE'])],
            'barcode_generator_default_width' => ['required', 'numeric', 'min:10', 'max:200'],
            'barcode_generator_default_height' => ['required', 'numeric', 'min:5', 'max:100'],
            'barcode_generator_include_text' => [new OnOffRule()],
            'barcode_generator_text_position' => ['required', 'string', Rule::in(['top', 'bottom', 'none'])],
            'barcode_generator_text_size' => ['required', 'integer', 'min:6', 'max:20'],
            'barcode_generator_label_width' => ['required', 'numeric', 'min:10', 'max:200'],
            'barcode_generator_label_height' => ['required', 'numeric', 'min:10', 'max:200'],
            'barcode_generator_label_margin' => ['required', 'numeric', 'min:0', 'max:50'],
            'barcode_generator_label_padding' => ['required', 'numeric', 'min:0', 'max:20'],
            'barcode_generator_paper_size' => ['required', 'string', Rule::in(['A4', 'Letter', 'P4', 'thermal_4x6', 'thermal_2x1'])],
            'barcode_generator_orientation' => ['required', 'string', Rule::in(['portrait', 'landscape'])],
            'barcode_generator_columns_per_page' => ['required', 'integer', 'min:1', 'max:10'],
            'barcode_generator_rows_per_page' => ['required', 'integer', 'min:1', 'max:20'],
            // Advanced settings
            'barcode_generator_auto_generate_sku' => [new OnOffRule()],
            'barcode_generator_sku_prefix' => ['nullable', 'string', 'max:10'],
            'barcode_generator_enable_batch_mode' => [new OnOffRule()],
            'barcode_generator_max_products_per_batch' => ['required', 'integer', 'min:10', 'max:1000'],
            // Appearance settings
            'barcode_generator_background_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'barcode_generator_text_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'barcode_generator_border_enabled' => [new OnOffRule()],
            'barcode_generator_border_width' => ['required', 'numeric', 'min:0.1', 'max:5'],
            'barcode_generator_border_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }
}
