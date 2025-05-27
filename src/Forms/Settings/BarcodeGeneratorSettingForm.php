<?php

namespace FriendsOfBotble\BarcodeGenerator\Forms\Settings;

use Botble\Base\Forms\FieldOptions\ColorFieldOption;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\ColorField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Setting\Forms\SettingForm;
use FriendsOfBotble\BarcodeGenerator\Enums\BarcodeTypeEnum;
use FriendsOfBotble\BarcodeGenerator\Http\Requests\Settings\BarcodeGeneratorSettingRequest;

class BarcodeGeneratorSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setSectionTitle(trans('plugins/fob-barcode-generator::barcode-generator.settings.title'))
            ->setSectionDescription(trans('plugins/fob-barcode-generator::barcode-generator.settings.description'))
            ->setValidatorClass(BarcodeGeneratorSettingRequest::class)
            ->add(
                'help_info',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content(view('plugins/fob-barcode-generator::partials.help-info')->render())
            )
            ->add(
                'barcode_generator_default_type',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.default_barcode_type'))
                    ->choices(BarcodeTypeEnum::labels())
                    ->defaultValue(setting('barcode_generator_default_type', BarcodeTypeEnum::CODE128))
            )
            ->add(
                'barcode_generator_default_width',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.default_barcode_width'))
                    ->defaultValue(setting('barcode_generator_default_width', 40))
                    ->attributes(['min' => 10, 'max' => 200, 'step' => 1])
            )
            ->add(
                'barcode_generator_default_height',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.default_barcode_height'))
                    ->defaultValue(setting('barcode_generator_default_height', 15))
                    ->attributes(['min' => 5, 'max' => 100, 'step' => 1])
            )
            ->add(
                'barcode_generator_include_text',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.include_text'))
                    ->defaultValue(setting('barcode_generator_include_text', true))
            )
            ->add(
                'barcode_generator_text_position',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.text_position'))
                    ->choices([
                        'top' => trans('plugins/fob-barcode-generator::barcode-generator.text_positions.top'),
                        'bottom' => trans('plugins/fob-barcode-generator::barcode-generator.text_positions.bottom'),
                        'none' => trans('plugins/fob-barcode-generator::barcode-generator.text_positions.none'),
                    ])
                    ->defaultValue(setting('barcode_generator_text_position', 'bottom'))
            )
            ->add(
                'barcode_generator_text_size',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.text_size'))
                    ->defaultValue(setting('barcode_generator_text_size', 8))
                    ->attributes(['min' => 6, 'max' => 20, 'step' => 1])
            )
            ->add(
                'barcode_generator_label_width',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.label_width'))
                    ->defaultValue(setting('barcode_generator_label_width', 50))
                    ->attributes(['min' => 10, 'max' => 200, 'step' => 0.1])
            )
            ->add(
                'barcode_generator_label_height',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.label_height'))
                    ->defaultValue(setting('barcode_generator_label_height', 30))
                    ->attributes(['min' => 10, 'max' => 200, 'step' => 0.1])
            )
            ->add(
                'barcode_generator_label_margin',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.label_margin'))
                    ->defaultValue(setting('barcode_generator_label_margin', 10))
                    ->attributes(['min' => 0, 'max' => 50, 'step' => 0.1])
            )
            ->add(
                'barcode_generator_label_padding',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.label_padding'))
                    ->defaultValue(setting('barcode_generator_label_padding', 2))
                    ->attributes(['min' => 0, 'max' => 20, 'step' => 0.1])
            )
            ->add(
                'barcode_generator_paper_size',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.paper_size'))
                    ->choices([
                        'A4' => trans('plugins/fob-barcode-generator::barcode-generator.paper_sizes.A4'),
                        'Letter' => trans('plugins/fob-barcode-generator::barcode-generator.paper_sizes.Letter'),
                        'P4' => trans('plugins/fob-barcode-generator::barcode-generator.paper_sizes.P4'),
                        'thermal_4x6' => trans('plugins/fob-barcode-generator::barcode-generator.paper_sizes.thermal_4x6'),
                        'thermal_2x1' => trans('plugins/fob-barcode-generator::barcode-generator.paper_sizes.thermal_2x1'),
                    ])
                    ->defaultValue(setting('barcode_generator_paper_size', 'A4'))
            )
            ->add(
                'barcode_generator_orientation',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.orientation'))
                    ->choices([
                        'portrait' => trans('plugins/fob-barcode-generator::barcode-generator.orientations.portrait'),
                        'landscape' => trans('plugins/fob-barcode-generator::barcode-generator.orientations.landscape'),
                    ])
                    ->defaultValue(setting('barcode_generator_orientation', 'portrait'))
            )
            ->add(
                'barcode_generator_columns_per_page',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.columns_per_page'))
                    ->defaultValue(setting('barcode_generator_columns_per_page', 4))
                    ->attributes(['min' => 1, 'max' => 10, 'step' => 1])
            )
            ->add(
                'barcode_generator_rows_per_page',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.rows_per_page'))
                    ->defaultValue(setting('barcode_generator_rows_per_page', 10))
                    ->attributes(['min' => 1, 'max' => 20, 'step' => 1])
            )
            ->add(
                'barcode_generator_auto_generate_sku',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.auto_generate_sku'))
                    ->defaultValue(setting('barcode_generator_auto_generate_sku', false))
                    ->helperText(trans('plugins/fob-barcode-generator::barcode-generator.settings.auto_generate_sku_help'))
            )
            ->add(
                'barcode_generator_sku_prefix',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.sku_prefix'))
                    ->defaultValue(setting('barcode_generator_sku_prefix', 'SKU'))
                    ->placeholder('SKU')
                    ->helperText(trans('plugins/fob-barcode-generator::barcode-generator.settings.sku_prefix_help'))
            )
            ->add(
                'barcode_generator_enable_batch_mode',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.enable_batch_mode'))
                    ->defaultValue(setting('barcode_generator_enable_batch_mode', true))
                    ->helperText(trans('plugins/fob-barcode-generator::barcode-generator.settings.enable_batch_mode_help'))
            )
            ->add(
                'barcode_generator_max_products_per_batch',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.max_products_per_batch'))
                    ->defaultValue(setting('barcode_generator_max_products_per_batch', 100))
                    ->attributes(['min' => 10, 'max' => 1000, 'step' => 10])
                    ->helperText(trans('plugins/fob-barcode-generator::barcode-generator.settings.max_products_per_batch_help'))
            )
            ->add(
                'barcode_generator_background_color',
                ColorField::class,
                ColorFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.background_color'))
                    ->defaultValue(setting('barcode_generator_background_color', '#FFFFFF'))
                    ->helperText(trans('plugins/fob-barcode-generator::barcode-generator.settings.background_color_help'))
            )
            ->add(
                'barcode_generator_text_color',
                ColorField::class,
                ColorFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.text_color'))
                    ->defaultValue(setting('barcode_generator_text_color', '#000000'))
                    ->helperText(trans('plugins/fob-barcode-generator::barcode-generator.settings.text_color_help'))
            )
            ->add(
                'barcode_generator_border_enabled',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.border_enabled'))
                    ->defaultValue(setting('barcode_generator_border_enabled', false))
                    ->helperText(trans('plugins/fob-barcode-generator::barcode-generator.settings.border_enabled_help'))
            )
            ->add(
                'barcode_generator_border_width',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.border_width'))
                    ->defaultValue(setting('barcode_generator_border_width', 1))
                    ->attributes(['min' => 0.1, 'max' => 5, 'step' => 0.1])
                    ->helperText(trans('plugins/fob-barcode-generator::barcode-generator.settings.border_width_help'))
            )
            ->add(
                'barcode_generator_border_color',
                ColorField::class,
                ColorFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.border_color'))
                    ->defaultValue(setting('barcode_generator_border_color', '#000000'))
                    ->helperText(trans('plugins/fob-barcode-generator::barcode-generator.settings.border_color_help'))
            )
            ->add(
                'field_display_info',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content(view('plugins/fob-barcode-generator::partials.field-display-info')->render())
            )
            ->add(
                'barcode_generator_show_product_name',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.show_product_name'))
                    ->defaultValue(setting('barcode_generator_show_product_name', false))
            )
            ->add(
                'barcode_generator_show_product_sku',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.show_product_sku'))
                    ->defaultValue(setting('barcode_generator_show_product_sku', true))
            )
            ->add(
                'barcode_generator_show_product_barcode',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.show_product_barcode'))
                    ->defaultValue(setting('barcode_generator_show_product_barcode', false))
            )
            ->add(
                'barcode_generator_show_product_price',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.show_product_price'))
                    ->defaultValue(setting('barcode_generator_show_product_price', true))
            )
            ->add(
                'barcode_generator_show_product_sale_price',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.show_product_sale_price'))
                    ->defaultValue(setting('barcode_generator_show_product_sale_price', false))
            )
            ->add(
                'barcode_generator_show_product_price_smart',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.show_product_price_smart'))
                    ->defaultValue(setting('barcode_generator_show_product_price_smart', false))
                    ->helperText(trans('plugins/fob-barcode-generator::barcode-generator.settings.show_product_price_smart_help'))
            )
            ->add(
                'barcode_generator_show_product_price_with_original',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.show_product_price_with_original'))
                    ->defaultValue(setting('barcode_generator_show_product_price_with_original', false))
                    ->helperText(trans('plugins/fob-barcode-generator::barcode-generator.settings.show_product_price_with_original_help'))
            )
            ->add(
                'barcode_generator_show_product_price_sale_only',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.show_product_price_sale_only'))
                    ->defaultValue(setting('barcode_generator_show_product_price_sale_only', false))
                    ->helperText(trans('plugins/fob-barcode-generator::barcode-generator.settings.show_product_price_sale_only_help'))
            )
            ->add(
                'barcode_generator_show_product_price_original_only',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.show_product_price_original_only'))
                    ->defaultValue(setting('barcode_generator_show_product_price_original_only', false))
                    ->helperText(trans('plugins/fob-barcode-generator::barcode-generator.settings.show_product_price_original_only_help'))
            )
            ->add(
                'barcode_generator_show_product_brand',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.show_product_brand'))
                    ->defaultValue(setting('barcode_generator_show_product_brand', false))
            )
            ->add(
                'barcode_generator_show_product_category',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.show_product_category'))
                    ->defaultValue(setting('barcode_generator_show_product_category', false))
            )
            ->add(
                'barcode_generator_show_product_attributes',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.show_product_attributes'))
                    ->defaultValue(setting('barcode_generator_show_product_attributes', false))
            )
            ->add(
                'barcode_generator_show_product_description',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.show_product_description'))
                    ->defaultValue(setting('barcode_generator_show_product_description', false))
            )
            ->add(
                'barcode_generator_show_product_weight',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.show_product_weight'))
                    ->defaultValue(setting('barcode_generator_show_product_weight', false))
            )
            ->add(
                'barcode_generator_show_product_dimensions',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.show_product_dimensions'))
                    ->defaultValue(setting('barcode_generator_show_product_dimensions', false))
            )
            ->add(
                'barcode_generator_show_product_stock',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.show_product_stock'))
                    ->defaultValue(setting('barcode_generator_show_product_stock', false))
            )
            ->add(
                'barcode_generator_show_current_date',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.show_current_date'))
                    ->defaultValue(setting('barcode_generator_show_current_date', false))
            )
            ->add(
                'barcode_generator_show_company_name',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.show_company_name'))
                    ->defaultValue(setting('barcode_generator_show_company_name', false))
            )
            ->add(
                'reset_info',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content(view('plugins/fob-barcode-generator::partials.reset-settings')->render())
            );
    }
}
