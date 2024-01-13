<?php

namespace Skillcraft\ActivityFeed\Providers;

use Botble\Base\Facades\Assets;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Botble\Base\Supports\ServiceProvider;
use Botble\Dashboard\Events\RenderingDashboardWidgets;
use Botble\Dashboard\Supports\DashboardWidgetInstance;
use Skillcraft\ActivityFeed\Supports\ActivityFeedHookManager;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        (new ActivityFeedHookManager)->load();

        $this->app['events']->listen(RenderingDashboardWidgets::class, function () {
            add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'registerDashboardWidgets'], 28, 2);
        });
    }

    public function registerDashboardWidgets(array $widgets, Collection $widgetSettings): array
    {
        if (! Auth::guard()->user()->hasPermission('activity-feed.index')) {
            return $widgets;
        }

        Assets::addScriptsDirectly('vendor/core/plugins/activity-feed/js/activity-feed.js');

        return (new DashboardWidgetInstance())
            ->setPermission('activity-feed.index')
            ->setKey('widget_activity_feed')
            ->setTitle(trans('plugins/activity-feed::activity-feed.widget_activity_feed'))
            ->setIcon('fas fa-history')
            ->setColor('cyan')
            ->setRoute(route('activity-feed.widget.feed'))
            ->setBodyClass('')
            ->setColumn('col-md-6 col-sm-6')
            ->init($widgets, $widgetSettings);
    }
}
