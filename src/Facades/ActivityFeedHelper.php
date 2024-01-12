<?php

namespace Skillcraft\ActivityFeed\Facades;

use Illuminate\Support\Facades\Facade;
use Skillcraft\ActivityFeed\Supports\ActivityFeedHelper as ActivityFeedHelperSupport;

/**
 * @method static Collection getActivityFeedItems(?Model $owner = null,?Model $module = null,bool $isPrivate = false,int $limit = 10,int $offet = 0)
 * @method static ?Model getActivityFeedItem(?Model $owner = null,?Model $module = null,bool $isPrivate = false,null|int|array $id = null)
 * @method static void deleteActivityFeedItem(?Model $owner = null,?Model $module = null,bool $isPrivate = false,null|int|array $id = null)
 * @see \Skillcraft\ActivityFeedHelper\Supports\ActivityFeedHelperSupport
 */
class ActivityFeedHelper extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ActivityFeedHelperSupport::class;
    }
}
