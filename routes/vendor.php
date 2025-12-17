<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'FriendsOfBotble\BarcodeGenerator\Http\Controllers',
    'prefix' => config('plugins.marketplace.general.vendor_panel_dir', 'vendor'),
    'as' => 'marketplace.vendor.',
    'middleware' => ['web', 'core', 'vendor'],
], function (): void {
    Route::group(['prefix' => 'barcode-generator', 'as' => 'barcode-generator.'], function (): void {
        Route::get('/', [
            'as' => 'index',
            'uses' => 'VendorBarcodeGeneratorController@index',
        ]);

        Route::post('generate', [
            'as' => 'generate',
            'uses' => 'VendorBarcodeGeneratorController@generate',
        ]);

        Route::get('preview', [
            'as' => 'preview',
            'uses' => 'VendorBarcodeGeneratorController@preview',
        ]);

        Route::get('download', [
            'as' => 'download',
            'uses' => 'VendorBarcodeGeneratorController@download',
        ]);
    });
});
