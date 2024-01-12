<?php

namespace Skillcraft\ActivityFeed\Supports;

use Illuminate\Database\Eloquent\Model;
use Botble\Base\Facades\MacroableModels;
use Illuminate\Database\Eloquent\Collection;
use Skillcraft\ActivityFeed\Models\ActivityFeed;
use Skillcraft\Core\Abstracts\HookRegistrarAbstract;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Skillcraft\ActivityFeed\Facades\ActivityFeedHelper;

class ActivityFeedHookManager extends HookRegistrarAbstract
{
    public static function getScreenName(): string
    {
        return FILTER_HOOK_SKILLCRAFT_ACTIVITY_FEEDS_MODULE_SUPPORTED_FILTER;
    }

    public static function getModuleName(): string
    {
        return 'skillcraft-activity-feed';
    }

    public static function getSupportedKey():string
    {
        return 'supported';
    }

    public static function addMacroHooks():void
    {
        foreach (self::getSupportedHooks() as $model => $name) {
            MacroableModels::addMacro($model, 'ownerActivityFeed', function () {
                 /**
                  * @var Model $this
                  * @return MorphMany
                  */
                return $this->morphMany(ActivityFeed::class, 'owner');
            });

            MacroableModels::addMacro($model, 'moduleActivityFeed', function () {
                /**
                 * @var Model $this
                 * @return MorphMany
                 */
                return $this->morphMany(ActivityFeed::class, 'module');
            });

            MacroableModels::addMacro($model, 'addActivityFeedItem', function (
                Model $owner,
                string $title,
                string $message,
                bool $isPrivate = false
            ) {
                /**
                 * @var Model $this
                 * throws \Illuminate\Validation\ValidationException
                 * returns void
                 */
                $this->moduleActivityFeed()->create(
                    ActivityFeedHelper::getValidatedData(
                        $title,
                        $message,
                        $isPrivate,
                        $owner,
                        $this
                    )->toArray()
                );
            });

            MacroableModels::addMacro($model, 'addOwnerActivityFeedItem', function (
                string $title,
                string $message,
                bool $isPrivate = false,
                ?Model $module = null
            ) {
                /**
                 * @var Model $this
                 * throws \Illuminate\Validation\ValidationException
                 * returns void
                 */
                $this->ownerActivityFeed()->create(
                    ActivityFeedHelper::getValidatedData(
                        $title,
                        $message,
                        $isPrivate,
                        $this,
                        $module
                    )->toArray()
                );
            });

            MacroableModels::addMacro($model, 'getOwnerActivityFeedItems', function (
                ?Model $module = null,
                bool $isPrivate = false
            ) {
                /**
                 * @var Model $this
                 *
                 * @return Collection
                 */
                return $this->ownerActivityFeed()
                    ->when($module, function ($query) use ($module) {
                        $query->HasModule($module);
                    })
                    ->when($isPrivate, function ($query) {
                        $query->IsPrivate();
                    })
                    ->when(!$isPrivate, function ($query) {
                        $query->IsPublic();
                    })
                    ->get();
            });

            MacroableModels::addMacro($model, 'getModuleActivityFeedItems', function (
                ?Model $owner = null,
                bool $isPrivate = false
            ) {
                /**
                 * @var Model $this
                 *
                 * @return Collection
                 */
                return $this->moduleActivityFeed()
                    ->when($owner, function ($query) use ($owner) {
                        $query->HasOwner($owner);
                    })
                    ->when($isPrivate, function ($query) {
                        $query->IsPrivate();
                    })
                    ->when(!$isPrivate, function ($query) {
                        $query->IsPublic();
                    })
                    ->get();
            });
        }
    }
}
