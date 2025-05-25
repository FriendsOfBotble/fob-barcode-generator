<?php

namespace FriendsOfBotble\BarcodeGenerator\Providers;

use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\PanelSections\PanelSectionItem;
use Botble\Base\Supports\DashboardMenuItem;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Setting\PanelSections\SettingOthersPanelSection;
use Illuminate\Support\ServiceProvider;

class BarcodeGeneratorServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this->setNamespace('plugins/fob-barcode-generator')
            ->loadHelpers()
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->publishAssets()
            ->loadAndPublishViews()
            ->loadRoutes();

        DashboardMenu::default()->beforeRetrieving(function (): void {
            DashboardMenu::make()
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-barcode-generator')
                        ->priority(420)
                        ->name('plugins/fob-barcode-generator::barcode-generator.name')
                        ->icon('ti ti-barcode')
                        ->route('barcode-generator.index')
                        ->permissions('barcode-generator.index')
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-barcode-generator-generate')
                        ->priority(1)
                        ->parentId('cms-plugins-barcode-generator')
                        ->name('plugins/fob-barcode-generator::barcode-generator.menu.generate')
                        ->icon('ti ti-printer')
                        ->route('barcode-generator.index')
                        ->permissions('barcode-generator.generate')
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-barcode-generator-templates')
                        ->priority(2)
                        ->parentId('cms-plugins-barcode-generator')
                        ->name('plugins/fob-barcode-generator::barcode-generator.menu.templates')
                        ->icon('ti ti-template')
                        ->route('barcode-generator.templates.index')
                        ->permissions('barcode-generator.templates')
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-barcode-generator-settings')
                        ->priority(3)
                        ->parentId('cms-plugins-barcode-generator')
                        ->name('plugins/fob-barcode-generator::barcode-generator.menu.settings')
                        ->icon('ti ti-settings')
                        ->route('barcode-generator.settings')
                        ->permissions('barcode-generator.settings')
                );
        });

        PanelSectionManager::default()->beforeRendering(function (): void {
            PanelSectionManager::registerItem(
                SettingOthersPanelSection::class,
                fn () => PanelSectionItem::make('barcode-generator')
                    ->setTitle(trans('plugins/fob-barcode-generator::barcode-generator.settings.title'))
                    ->withIcon('ti ti-barcode')
                    ->withDescription(trans('plugins/fob-barcode-generator::barcode-generator.settings.description'))
                    ->withPriority(190)
                    ->withRoute('barcode-generator.settings')
            );
        });

        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });
    }
}
