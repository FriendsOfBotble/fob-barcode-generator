<?php

namespace FriendsOfBotble\BarcodeGenerator\Forms\Settings;

use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Setting\Forms\SettingForm;

class BarcodeGeneratorSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setSectionTitle(trans('plugins/fob-barcode-generator::barcode-generator.settings.title'))
            ->setSectionDescription(trans('plugins/fob-barcode-generator::barcode-generator.settings.description'))
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
                    ->value(get_setting('barcode_generator_default_type', 'C128'))
            )
            ->add(
                'barcode_generator_default_width',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.default_barcode_width'))
                    ->value(get_setting('barcode_generator_default_width', 40))
                    ->attributes(['min' => 10, 'max' => 200, 'step' => 1])
            )
            ->add(
                'barcode_generator_default_height',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.default_barcode_height'))
                    ->value(get_setting('barcode_generator_default_height', 15))
                    ->attributes(['min' => 5, 'max' => 100, 'step' => 1])
            )
            ->add(
                'barcode_generator_include_text',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.include_text'))
                    ->value(get_setting('barcode_generator_include_text', true))
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
                    ->value(get_setting('barcode_generator_text_position', 'bottom'))
            )
            ->add(
                'barcode_generator_text_size',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.text_size'))
                    ->value(get_setting('barcode_generator_text_size', 8))
                    ->attributes(['min' => 6, 'max' => 20, 'step' => 1])
            )
            ->addOpenCollapsible('label_settings', trans('plugins/fob-barcode-generator::barcode-generator.settings.label_settings'))
            ->add(
                'barcode_generator_label_width',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.label_width'))
                    ->value(get_setting('barcode_generator_label_width', 50))
                    ->attributes(['min' => 10, 'max' => 200, 'step' => 0.1])
            )
            ->add(
                'barcode_generator_label_height',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.label_height'))
                    ->value(get_setting('barcode_generator_label_height', 30))
                    ->attributes(['min' => 10, 'max' => 200, 'step' => 0.1])
            )
            ->add(
                'barcode_generator_label_margin',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.label_margin'))
                    ->value(get_setting('barcode_generator_label_margin', 10))
                    ->attributes(['min' => 0, 'max' => 50, 'step' => 0.1])
            )
            ->add(
                'barcode_generator_label_padding',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.label_padding'))
                    ->value(get_setting('barcode_generator_label_padding', 2))
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
                    ->value(get_setting('barcode_generator_paper_size', 'A4'))
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
                    ->value(get_setting('barcode_generator_orientation', 'portrait'))
            )
            ->add(
                'barcode_generator_columns_per_page',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.columns_per_page'))
                    ->value(get_setting('barcode_generator_columns_per_page', 4))
                    ->attributes(['min' => 1, 'max' => 10, 'step' => 1])
            )
            ->add(
                'barcode_generator_rows_per_page',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.rows_per_page'))
                    ->value(get_setting('barcode_generator_rows_per_page', 10))
                    ->attributes(['min' => 1, 'max' => 20, 'step' => 1])
            )
            ->addCloseCollapsible('paper_settings');
    }
}
