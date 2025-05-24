<?php

namespace FriendsOfBotble\BarcodeGenerator\Forms;

use Botble\Base\Forms\FieldOptions\CheckboxFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\CheckboxField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use FriendsOfBotble\BarcodeGenerator\Enums\BarcodeTypeEnum;
use FriendsOfBotble\BarcodeGenerator\Http\Requests\BarcodeTemplateRequest;
use FriendsOfBotble\BarcodeGenerator\Models\BarcodeTemplate;

class BarcodeTemplateForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(BarcodeTemplate::class)
            ->setValidatorClass(BarcodeTemplateRequest::class)
            ->add(
                'name',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.templates.name'))
                    ->required()
            )
            ->add(
                'description',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.templates.description'))
            )
            ->add(
                'paper_size',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.paper_size'))
                    ->choices([
                        'A4' => trans('plugins/fob-barcode-generator::barcode-generator.paper_sizes.A4'),
                        'Letter' => trans('plugins/fob-barcode-generator::barcode-generator.paper_sizes.Letter'),
                        'P4' => trans('plugins/fob-barcode-generator::barcode-generator.paper_sizes.P4'),
                        'thermal_4x6' => trans(
                            'plugins/fob-barcode-generator::barcode-generator.paper_sizes.thermal_4x6'
                        ),
                        'thermal_2x1' => trans(
                            'plugins/fob-barcode-generator::barcode-generator.paper_sizes.thermal_2x1'
                        ),
                    ])
                    ->required()
            )
            ->add(
                'orientation',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.orientation'))
                    ->choices([
                        'portrait' => trans('plugins/fob-barcode-generator::barcode-generator.orientations.portrait'),
                        'landscape' => trans('plugins/fob-barcode-generator::barcode-generator.orientations.landscape'),
                    ])
                    ->required()
            )
            ->addOpenFieldset('dimensions')
            ->add(
                'label_width',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.label_width'))
                    ->attributes(['step' => '0.1', 'min' => '10', 'max' => '200'])
                    ->required()
            )
            ->add(
                'label_height',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.label_height'))
                    ->attributes(['step' => '0.1', 'min' => '10', 'max' => '200'])
                    ->required()
            )
            ->add(
                'margin_top',
                NumberField::class,
                NumberFieldOption::make()
                    ->label('Margin Top (mm)')
                    ->attributes(['step' => '0.1', 'min' => '0', 'max' => '50'])
            )
            ->add(
                'margin_bottom',
                NumberField::class,
                NumberFieldOption::make()
                    ->label('Margin Bottom (mm)')
                    ->attributes(['step' => '0.1', 'min' => '0', 'max' => '50'])
            )
            ->add(
                'margin_left',
                NumberField::class,
                NumberFieldOption::make()
                    ->label('Margin Left (mm)')
                    ->attributes(['step' => '0.1', 'min' => '0', 'max' => '50'])
            )
            ->add(
                'margin_right',
                NumberField::class,
                NumberFieldOption::make()
                    ->label('Margin Right (mm)')
                    ->attributes(['step' => '0.1', 'min' => '0', 'max' => '50'])
            )
            ->add(
                'padding',
                NumberField::class,
                NumberFieldOption::make()
                    ->label('Padding (mm)')
                    ->attributes(['step' => '0.1', 'min' => '0', 'max' => '20'])
            )
            ->addCloseFieldset('dimensions')
            ->addOpenFieldset('layout')
            ->add(
                'columns_per_page',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.columns_per_page'))
                    ->attributes(['min' => '1', 'max' => '10'])
                    ->required()
            )
            ->add(
                'rows_per_page',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.rows_per_page'))
                    ->attributes(['min' => '1', 'max' => '20'])
                    ->required()
            )
            ->addCloseFieldset('layout')
            ->addOpenFieldset('barcode')
            ->add(
                'barcode_type',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.default_barcode_type'))
                    ->choices(BarcodeTypeEnum::labels())
                    ->required()
            )
            ->add(
                'barcode_width',
                NumberField::class,
                NumberFieldOption::make()
                    ->label('Barcode Width (mm)')
                    ->attributes(['step' => '0.1', 'min' => '10', 'max' => '200'])
                    ->required()
            )
            ->add(
                'barcode_height',
                NumberField::class,
                NumberFieldOption::make()
                    ->label('Barcode Height (mm)')
                    ->attributes(['step' => '0.1', 'min' => '5', 'max' => '100'])
                    ->required()
            )
            ->add(
                'include_text',
                CheckboxField::class,
                CheckboxFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.include_text'))
            )
            ->add(
                'text_position',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.text_position'))
                    ->choices([
                        'top' => trans('plugins/fob-barcode-generator::barcode-generator.text_positions.top'),
                        'bottom' => trans('plugins/fob-barcode-generator::barcode-generator.text_positions.bottom'),
                        'none' => trans('plugins/fob-barcode-generator::barcode-generator.text_positions.none'),
                    ])
            )
            ->add(
                'text_size',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.settings.text_size'))
                    ->attributes(['min' => '6', 'max' => '20'])
            )
            ->addCloseFieldset('barcode')
            ->add(
                'is_default',
                CheckboxField::class,
                CheckboxFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.templates.is_default'))
            )
            ->add(
                'is_active',
                CheckboxField::class,
                CheckboxFieldOption::make()
                    ->label(trans('plugins/fob-barcode-generator::barcode-generator.templates.is_active'))
                    ->defaultValue(true)
            );
    }
}
