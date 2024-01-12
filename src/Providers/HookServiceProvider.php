<?php

namespace Skillcraft\ActivityFeed\Providers;

use Botble\Base\Supports\ServiceProvider;
use Skillcraft\ActivityFeed\Supports\ActivityFeedHookManager;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        (new ActivityFeedHookManager)->load();
    }
}
