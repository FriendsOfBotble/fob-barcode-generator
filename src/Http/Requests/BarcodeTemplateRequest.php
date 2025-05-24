<?php

namespace FriendsOfBotble\BarcodeGenerator\Http\Requests;

use Botble\Base\Rules\OnOffRule;
use Botble\Support\Http\Requests\Request;
use FriendsOfBotble\BarcodeGenerator\Enums\BarcodeTypeEnum;
use Illuminate\Validation\Rule;

class BarcodeTemplateRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'paper_size' => ['required', 'string', Rule::in(['A4', 'Letter', 'P4', 'thermal_4x6', 'thermal_2x1'])],
            'orientation' => ['required', 'string', Rule::in(['portrait', 'landscape'])],
            'label_width' => ['required', 'numeric', 'min:10', 'max:200'],
            'label_height' => ['required', 'numeric', 'min:10', 'max:200'],
            'margin_top' => ['required', 'numeric', 'min:0', 'max:50'],
            'margin_bottom' => ['required', 'numeric', 'min:0', 'max:50'],
            'margin_left' => ['required', 'numeric', 'min:0', 'max:50'],
            'margin_right' => ['required', 'numeric', 'min:0', 'max:50'],
            'padding' => ['required', 'numeric', 'min:0', 'max:20'],
            'columns_per_page' => ['required', 'integer', 'min:1', 'max:10'],
            'rows_per_page' => ['required', 'integer', 'min:1', 'max:20'],
            'barcode_type' => ['required', 'string', Rule::in(array_keys(BarcodeTypeEnum::labels()))],
            'barcode_width' => ['required', 'numeric', 'min:10', 'max:200'],
            'barcode_height' => ['required', 'numeric', 'min:5', 'max:100'],
            'include_text' => [new OnOffRule()],
            'text_position' => ['required', 'string', Rule::in(['top', 'bottom', 'none'])],
            'text_size' => ['required', 'integer', 'min:6', 'max:20'],
            'fields' => ['nullable', 'array'],
            'fields.*' => ['string', Rule::in(['name', 'sku', 'barcode', 'price', 'sale_price', 'brand', 'category', 'attributes'])],
            'is_default' => [new OnOffRule()],
            'is_active' => [new OnOffRule()],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => trans('plugins/fob-barcode-generator::barcode-generator.templates.name'),
            'description' => trans('plugins/fob-barcode-generator::barcode-generator.templates.description'),
            'paper_size' => trans('plugins/fob-barcode-generator::barcode-generator.settings.paper_size'),
            'orientation' => trans('plugins/fob-barcode-generator::barcode-generator.settings.orientation'),
            'label_width' => trans('plugins/fob-barcode-generator::barcode-generator.settings.label_width'),
            'label_height' => trans('plugins/fob-barcode-generator::barcode-generator.settings.label_height'),
            'columns_per_page' => trans('plugins/fob-barcode-generator::barcode-generator.settings.columns_per_page'),
            'rows_per_page' => trans('plugins/fob-barcode-generator::barcode-generator.settings.rows_per_page'),
            'barcode_type' => trans('plugins/fob-barcode-generator::barcode-generator.settings.default_barcode_type'),
            'barcode_width' => trans('plugins/fob-barcode-generator::barcode-generator.settings.default_barcode_width'),
            'barcode_height' => trans('plugins/fob-barcode-generator::barcode-generator.settings.default_barcode_height'),
            'include_text' => trans('plugins/fob-barcode-generator::barcode-generator.settings.include_text'),
            'text_position' => trans('plugins/fob-barcode-generator::barcode-generator.settings.text_position'),
            'text_size' => trans('plugins/fob-barcode-generator::barcode-generator.settings.text_size'),
            'is_default' => trans('plugins/fob-barcode-generator::barcode-generator.templates.is_default'),
            'is_active' => trans('plugins/fob-barcode-generator::barcode-generator.templates.is_active'),
        ];
    }
}
