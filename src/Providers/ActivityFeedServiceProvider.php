<?php

namespace Skillcraft\ActivityFeed\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
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
        $this->setNamespace('plugins/activity-feed')
            ->loadAndPublishConfigurations(['general'])
            ->loadHelpers()
            ->loadMigrations()
            ->loadAndPublishTranslations();

        if (! is_plugin_active('skillcraft-core')) {
            return;
        }

        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });
    }
}
