<?php

namespace FriendsOfBotble\BarcodeGenerator;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Facades\Setting;
use Illuminate\Support\Facades\Schema;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Schema::dropIfExists('barcode_templates');

        Setting::delete([
            'barcode_generator_default_type',
            'barcode_generator_default_width',
            'barcode_generator_default_height',
            'barcode_generator_include_text',
            'barcode_generator_text_position',
            'barcode_generator_text_size',
            'barcode_generator_label_width',
            'barcode_generator_label_height',
            'barcode_generator_label_margin',
            'barcode_generator_label_padding',
            'barcode_generator_paper_size',
            'barcode_generator_orientation',
            'barcode_generator_columns_per_page',
            'barcode_generator_rows_per_page',
        ]);
    }
}
