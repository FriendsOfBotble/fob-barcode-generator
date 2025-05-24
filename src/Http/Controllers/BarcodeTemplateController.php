<?php

namespace FriendsOfBotble\BarcodeGenerator\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use FriendsOfBotble\BarcodeGenerator\Forms\BarcodeTemplateForm;
use FriendsOfBotble\BarcodeGenerator\Http\Requests\BarcodeTemplateRequest;
use FriendsOfBotble\BarcodeGenerator\Models\BarcodeTemplate;
use FriendsOfBotble\BarcodeGenerator\Tables\BarcodeTemplateTable;

class BarcodeTemplateController extends BaseController
{
    public function index(BarcodeTemplateTable $table)
    {
        $this->pageTitle(trans('plugins/fob-barcode-generator::barcode-generator.templates.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/fob-barcode-generator::barcode-generator.templates.create'));

        Assets::addStylesDirectly('vendor/core/plugins/fob-barcode-generator/css/barcode-generator.css')
            ->addScriptsDirectly('vendor/core/plugins/fob-barcode-generator/js/barcode-generator.js');

        return BarcodeTemplateForm::create()->renderForm();
    }

    public function store(BarcodeTemplateRequest $request): BaseHttpResponse
    {
        $template = BarcodeTemplate::query()->create($request->validated());

        event(new CreatedContentEvent(BARCODE_TEMPLATE_MODULE_SCREEN_NAME, $request, $template));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('barcode-generator.templates.index'))
            ->setNextUrl(route('barcode-generator.templates.edit', $template->getKey()))
            ->setMessage(trans('plugins/fob-barcode-generator::barcode-generator.messages.template_created'));
    }

    public function edit(BarcodeTemplate $template)
    {
        $this->pageTitle(trans('plugins/fob-barcode-generator::barcode-generator.templates.edit'));

        Assets::addStylesDirectly('vendor/core/plugins/fob-barcode-generator/css/barcode-generator.css')
            ->addScriptsDirectly('vendor/core/plugins/fob-barcode-generator/js/barcode-generator.js');

        return BarcodeTemplateForm::createFromModel($template)
            ->setMethod('PUT')
            ->setUrl(route('barcode-generator.templates.update', $template->getKey()))
            ->renderForm();
    }

    public function update(BarcodeTemplate $template, BarcodeTemplateRequest $request): BaseHttpResponse
    {
        $template->fill($request->validated());
        $template->save();

        event(new UpdatedContentEvent(BARCODE_TEMPLATE_MODULE_SCREEN_NAME, $request, $template));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('barcode-generator.templates.index'))
            ->setMessage(trans('plugins/fob-barcode-generator::barcode-generator.messages.template_updated'));
    }

    public function destroy(BarcodeTemplate $template): BaseHttpResponse
    {
        $template->delete();

        event(new DeletedContentEvent(BARCODE_TEMPLATE_MODULE_SCREEN_NAME, request(), $template));

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/fob-barcode-generator::barcode-generator.messages.template_deleted'));
    }
}
