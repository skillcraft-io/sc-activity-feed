<?php

namespace Skillcraft\ActivityFeed\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\PanelSections\PanelSectionItem;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Skillcraft\Core\PanelSections\CorePanelSection;
use Skillcraft\ActivityFeed\Facades\ActivityFeedHelper;

class ActivityFeedServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('ActivityFeedHelper', ActivityFeedHelper::class);
    }

    public function boot(): void
    {    
        if (! is_plugin_active('skillcraft-core')) {
            return;
        }

        $this->setNamespace('plugins/activity-feed')
            ->loadAndPublishConfigurations(['general', 'permissions'])
            ->loadHelpers()
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes();
            
            PanelSectionManager::default()->beforeRendering(function () {
                PanelSectionManager::registerItem(
                    CorePanelSection::class,
                    fn () => PanelSectionItem::make('activity-feed')
                        ->setTitle(trans('plugins/activity-feed::activity-feed.name'))
                        ->withDescription(trans('plugins/activity-feed::activity-feed.description'))
                        ->withIcon('ti ti-activity')
                        ->withPriority(20)
                        ->withRoute('activity-feed.index')
                );
            });


        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });
    }
}
