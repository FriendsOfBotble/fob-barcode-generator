<?php

namespace FriendsOfBotble\BarcodeGenerator\Tables;

use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\Columns\YesNoColumn;
use FriendsOfBotble\BarcodeGenerator\Models\BarcodeTemplate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;

class BarcodeTemplateTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(BarcodeTemplate::class)
            ->addActions([
                EditAction::make()->route('barcode-generator.templates.edit'),
                DeleteAction::make()->route('barcode-generator.templates.destroy'),
            ]);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this
            ->getModel()
            ->query()
            ->select([
                'id',
                'name',
                'description',
                'paper_size',
                'barcode_type',
                'is_default',
                'is_active',
                'created_at',
            ]);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            IdColumn::make(),
            NameColumn::make()->route('barcode-generator.templates.edit'),
            Column::make('description')
                ->title(trans('plugins/fob-barcode-generator::barcode-generator.templates.description')),
            Column::make('paper_size')
                ->title(trans('plugins/fob-barcode-generator::barcode-generator.settings.paper_size')),
            Column::make('barcode_type')
                ->title(trans('plugins/fob-barcode-generator::barcode-generator.settings.default_barcode_type')),
            YesNoColumn::make('is_default')
                ->title(trans('plugins/fob-barcode-generator::barcode-generator.templates.is_default')),
            StatusColumn::make('is_active')
                ->title(trans('plugins/fob-barcode-generator::barcode-generator.templates.is_active')),
            CreatedAtColumn::make(),
        ];
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('barcode-generator.templates.create'), 'barcode-generator.templates');
    }

    public function bulkActions(): array
    {
        return [
            DeleteBulkAction::make()->permission('barcode-generator.templates'),
        ];
    }

    public function getBulkChanges(): array
    {
        return [
            NameBulkChange::make(),
            StatusBulkChange::make(),
            CreatedAtBulkChange::make(),
        ];
    }

    public function getFilters(): array
    {
        return [
            'name' => [
                'title' => trans('core/base::tables.name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];
    }
}
