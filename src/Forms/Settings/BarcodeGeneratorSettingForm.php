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
                    ->content('
                        <div class="alert alert-info">
                            <h6><i class="ti ti-info-circle me-2"></i>' . trans('plugins/fob-barcode-generator::barcode-generator.settings.help_title') . '</h6>
                            <p class="mb-2">' . trans('plugins/fob-barcode-generator::barcode-generator.settings.help_description') . '</p>
                            <ul class="mb-0">
                                <li>' . trans('plugins/fob-barcode-generator::barcode-generator.settings.help_tip_1') . '</li>
                                <li>' . trans('plugins/fob-barcode-generator::barcode-generator.settings.help_tip_2') . '</li>
                                <li>' . trans('plugins/fob-barcode-generator::barcode-generator.settings.help_tip_3') . '</li>
                                <li>' . trans('plugins/fob-barcode-generator::barcode-generator.settings.help_tip_4') . '</li>
                            </ul>
                        </div>
                    ')
            )
            ->add(
                'barcode_generator_default_type',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.default_barcode_type'))
                    ->choices([
                        'C128' => trans('plugins/fob-barcode-generator::barcode-generator.barcode_types.C128'),
                        'EAN13' => trans('plugins/fob-barcode-generator::barcode-generator.barcode_types.EAN13'),
                        'EAN8' => trans('plugins/fob-barcode-generator::barcode-generator.barcode_types.EAN8'),
                        'UPCA' => trans('plugins/fob-barcode-generator::barcode-generator.barcode_types.UPCA'),
                        'UPCE' => trans('plugins/fob-barcode-generator::barcode-generator.barcode_types.UPCE'),
                    ])
                    ->defaultValue(setting('barcode_generator_default_type', 'C128'))
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
            ->addOpenCollapsible('label_settings', trans('plugins/fob-barcode-generator::barcode-generator.settings.label_settings'))
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
            ->addCloseCollapsible('label_settings')
            ->addOpenCollapsible('paper_settings', trans('plugins/fob-barcode-generator::barcode-generator.settings.paper_settings'))
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
            ->addCloseCollapsible('paper_settings')
            ->addOpenCollapsible('advanced_settings', trans('plugins/fob-barcode-generator::barcode-generator.settings.advanced_settings'))
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
            ->addCloseCollapsible('advanced_settings')
            ->addOpenCollapsible('appearance_settings', trans('plugins/fob-barcode-generator::barcode-generator.settings.appearance_settings'))
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
            ->addCloseCollapsible('appearance_settings')
            ->addOpenCollapsible('reset_settings', trans('plugins/fob-barcode-generator::barcode-generator.settings.reset_settings'))
            ->add(
                'reset_info',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content('
                        <div class="alert alert-warning">
                            <h6><i class="ti ti-alert-triangle me-2"></i>' . trans('plugins/fob-barcode-generator::barcode-generator.settings.reset_warning_title') . '</h6>
                            <p class="mb-3">' . trans('plugins/fob-barcode-generator::barcode-generator.settings.reset_warning_message') . '</p>
                            <button type="button" class="btn btn-warning btn-sm" id="reset-to-defaults-btn">
                                <i class="ti ti-refresh me-1"></i>
                                ' . trans('plugins/fob-barcode-generator::barcode-generator.settings.reset_to_defaults') . '
                            </button>
                        </div>
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                const resetBtn = document.getElementById("reset-to-defaults-btn");
                                if (resetBtn) {
                                    resetBtn.addEventListener("click", function() {
                                        if (confirm("' . trans('plugins/fob-barcode-generator::barcode-generator.settings.reset_confirmation') . '")) {
                                            const defaults = ' . json_encode(barcode_generator_default_settings()) . ';
                                            Object.keys(defaults).forEach(key => {
                                                const fieldName = "barcode_generator_" + key;
                                                const field = document.querySelector(`[name="${fieldName}"]`);
                                                if (field) {
                                                    if (field.type === "checkbox") {
                                                        field.checked = defaults[key];
                                                    } else {
                                                        field.value = defaults[key];
                                                    }
                                                }
                                            });
                                            alert("' . trans('plugins/fob-barcode-generator::barcode-generator.settings.reset_success') . '");
                                        }
                                    });
                                }
                            });
                        </script>
                    ')
            )
            ->addCloseCollapsible('reset_settings');
    }
}
