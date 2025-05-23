<?php

use Botble\Base\Facades\AdminHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'FriendsOfBotble\BarcodeGenerator\Http\Controllers'], function () {
    AdminHelper::registerRoutes(function (): void {
        Route::group(['prefix' => 'barcode-generator'], function (): void {
            Route::get('/', [
                'as' => 'barcode-generator.index',
                'uses' => 'BarcodeGeneratorController@index',
                'permission' => 'barcode-generator.index',
            ]);

            Route::post('generate', [
                'as' => 'barcode-generator.generate',
                'uses' => 'BarcodeGeneratorController@generate',
                'permission' => 'barcode-generator.generate',
            ]);

            Route::get('preview', [
                'as' => 'barcode-generator.preview',
                'uses' => 'BarcodeGeneratorController@preview',
                'permission' => 'barcode-generator.generate',
            ]);

            Route::get('download', [
                'as' => 'barcode-generator.download',
                'uses' => 'BarcodeGeneratorController@download',
                'permission' => 'barcode-generator.print',
            ]);

            // Test route (remove in production)
            Route::get('test', [
                'as' => 'barcode-generator.test',
                'uses' => 'TestBarcodeController@test',
                'permission' => 'barcode-generator.index',
            ]);

            // Setup Wizard routes
            Route::group(['prefix' => 'setup-wizard', 'as' => 'setup-wizard.'], function (): void {
                Route::get('/', [
                    'as' => 'index',
                    'uses' => 'SetupWizardController@index',
                    'permission' => 'barcode-generator.settings',
                ]);

                Route::post('/', [
                    'as' => 'store',
                    'uses' => 'SetupWizardController@store',
                    'permission' => 'barcode-generator.settings',
                ]);

                Route::post('complete', [
                    'as' => 'complete',
                    'uses' => 'SetupWizardController@complete',
                    'permission' => 'barcode-generator.settings',
                ]);

                Route::post('skip', [
                    'as' => 'skip',
                    'uses' => 'SetupWizardController@skip',
                    'permission' => 'barcode-generator.settings',
                ]);
            });

            // Order barcode routes
            Route::group(['prefix' => 'orders', 'as' => 'orders.'], function (): void {
                Route::post('generate', [
                    'as' => 'generate',
                    'uses' => 'BarcodeGeneratorController@generateOrderBarcodes',
                    'permission' => 'barcode-generator.generate',
                ]);

                Route::get('preview', [
                    'as' => 'preview',
                    'uses' => 'BarcodeGeneratorController@previewOrderBarcodes',
                    'permission' => 'barcode-generator.generate',
                ]);

                Route::get('download', [
                    'as' => 'download',
                    'uses' => 'BarcodeGeneratorController@downloadOrderBarcodes',
                    'permission' => 'barcode-generator.print',
                ]);
            });

            // Template routes
            Route::group(['prefix' => 'templates', 'as' => 'barcode-generator.templates.'], function (): void {
                Route::match(['GET', 'POST'], '/', [
                    'as' => 'index',
                    'uses' => 'BarcodeTemplateController@index',
                    'permission' => 'barcode-generator.templates.index',
                ]);

                Route::get('create', [
                    'as' => 'create',
                    'uses' => 'BarcodeTemplateController@create',
                    'permission' => 'barcode-generator.templates.create',
                ]);

                Route::post('/store', [
                    'as' => 'store',
                    'uses' => 'BarcodeTemplateController@store',
                    'permission' => 'barcode-generator.templates.create',
                ]);

                Route::get('{template}/edit', [
                    'as' => 'edit',
                    'uses' => 'BarcodeTemplateController@edit',
                    'permission' => 'barcode-generator.templates.edit',
                ]);

                Route::put('{template}', [
                    'as' => 'update',
                    'uses' => 'BarcodeTemplateController@update',
                    'permission' => 'barcode-generator.templates.edit',
                ]);

                Route::delete('{template}', [
                    'as' => 'destroy',
                    'uses' => 'BarcodeTemplateController@destroy',
                    'permission' => 'barcode-generator.templates.destroy',
                ]);
            });
        });

        Route::group(['prefix' => 'settings'], function (): void {
            Route::get('barcode-generator', [
                'as' => 'barcode-generator.settings',
                'uses' => 'Settings\BarcodeGeneratorSettingController@edit',
                'permission' => 'barcode-generator.settings',
            ]);

            Route::put('barcode-generator', [
                'as' => 'barcode-generator.settings.update',
                'uses' => 'Settings\BarcodeGeneratorSettingController@update',
                'permission' => 'barcode-generator.settings',
            ]);
        });
    });
});
