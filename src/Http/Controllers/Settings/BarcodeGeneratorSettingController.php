<?php

namespace FriendsOfBotble\BarcodeGenerator\Http\Controllers\Settings;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Breadcrumb;
use Botble\Setting\Http\Controllers\SettingController;
use FriendsOfBotble\BarcodeGenerator\Forms\Settings\BarcodeGeneratorSettingForm;
use FriendsOfBotble\BarcodeGenerator\Http\Requests\Settings\BarcodeGeneratorSettingRequest;

class BarcodeGeneratorSettingController extends SettingController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(trans('plugins/fob-barcode-generator::barcode-generator.settings.title'), route('barcode-generator.settings'));
    }

    public function edit()
    {
        $this->pageTitle(trans('plugins/fob-barcode-generator::barcode-generator.settings.title'));

        return BarcodeGeneratorSettingForm::create()->renderForm();
    }

    public function update(BarcodeGeneratorSettingRequest $request): BaseHttpResponse
    {
        return $this->performUpdate($request->validated());
    }
}
