## Activity Feed Plugin for Botble CMS

The Activity Feed Plugin is a highly versatile plugin designed for developers who wish to seamlessly integrate an activity tracking & feed system into their projects. This plugin provides an out-of-the-box solution to record, manage, and display activity feeds for any Eloquent model in a Botble powered application. Whether you are building a social network, a project management tool, or any application that requires tracking user activities, displaying application events, or just a simple private account feed, this plugin simplifies the entire process, making it efficient and developer-friendly.

## A few use cases

This plugin was inspired by my past works with many membership websites. I have used this plugin for the following use cases:

- Transaction Type Feeds
- Awards/Payouts
- User Activity Logs
- Notifications/Events
- Login annoucements

Theres many ways this could be used. Let me know how you use it.

### Plugin does not provide any interface forms/widgets. Its use is geared to developers to streamline thier own projects.

## Requirements

- Botble core 7.1.0 or higher.
- Skillcraft Core 1.2.0 or higher

**Installation**

1. Download the plugin
2. Extract the downloaded file and upload the extracted folder to the `platform/plugins` directory.
3. Go to **Admin** > **Plugins** and click on the **Activate** button.


## Usage

Register Your Model by adding the following code in your service provider.

```php
    \Skillcraft\ActivityFeed\Supports\ActivityFeedHookManager::registerHooks(YourModel::class, 'name');
```

Once done your model can now use any of the following macros

```php

/**
* All owner activities
* @return MorphMany
*/
$yourModel->ownerActivityFeed();

/**
 * All modules activities
 * @return MorphMany
 */
$yourModel->moduleActivityFeed();

/**
 * Add a Activity Feed Item via Module
 * Requires a owner
 * Optionally set public of private.
 * 
 * throws \Illuminate\Validation\ValidationException
 * returns void 
 */
$yourModel->addActivityFeedItem(
     Model $owner,
     string $title,
     string $message,
     bool $isPrivate = false
);

/**
 * Add a Activity Feed Item via Owner
 * Optionall set public of private and a module.
 * 
 * throws \Illuminate\Validation\ValidationException
 * returns void 
 */
$yourModel->addOwnerActivityFeedItem(
    string $title,
    string $message,
    bool $isPrivate = false,
    ?Model $module = null
);

/**
 * Fetch a Owners Activity Feed
 * 
 * Optionally limit the results by also a specifc module 
 * and or if its private or public
 * 
 * @return Collection
 */
$yourModel->getOwnerActivityFeedItems(
     ?Model $module = null,
     bool $isPrivate = false
);

/**
 * Fetch a Modules Activity Feed
 * 
 * Optionally limit the results by also a specifc owner 
 * and or if its private or public
 * 
 * @return Collection
 */
$yourModel->getModuleActivityFeedItems(
    ?Model $owner = null,
    bool $isPrivate = false
);

```

# Other

```php

    /**
     * Retrieves a collection of activity feed items.
     *
     * @param Model|null $owner     The owner model (default: null)
     * @param Model|null $module    The module model (default: null)
     * @param bool       $isPrivate Whether the items are private (default: false)
     * @param int        $limit     The maximum number of items to retrieve (default: 10)
     * @param int        $offset    The starting point of the items (default: 0)
     *
     * @return Collection The collection of activity feed items
     */
    \Skillcraft\ActivityFeed\Facades\ActivityFeedHelper::getActivityFeedItems(
        ?Model $owner = null,
        ?Model $module = null,
        bool $isPrivate = false,
        int $limit = 10,
        int $offet = 0,
    ):Collection;


    /**
     * Retrieves activity feed item(s) based on specified parameters.
     *
     * @param ?Model $owner The owner model of the activity feed item. Defaults to null.
     * @param ?Model $module The module model of the activity feed item. Defaults to null.
     * @param bool $isPrivate Determines if the activity feed item is private. Defaults to false.
     * @param null|int|array $id The ID or IDs of the activity feed item(s) to retrieve. Defaults to null.
     *
     * @return ?Model The activity feed item matching the specified parameters.
     */
    \Skillcraft\ActivityFeed\Facades\ActivityFeedHelper::getActivityFeedItem(
        ?Model $owner = null,
        ?Model $module = null,
        bool $isPrivate = false,
        null|int|array $id = null
    ):?Model;

    /**
     * Deletes activity feed item(s).
     *
     * @param ?Model $owner The owner of the activity feed item. Default is null.
     * @param ?Model $module The module of the activity feed item. Default is null.
     * @param bool $isPrivate Indicates if the activity feed item is private. Default is false.
     * @param null|int|array $id The ID or array of IDs of the activity feed item(s) to delete. Default is null.

     * @return void
     */
    \Skillcraft\ActivityFeed\Facades\ActivityFeedHelper::deleteActivityFeedItem(
        ?Model $owner = null,
        ?Model $module = null,
        bool $isPrivate = false,
        null|int|array $id = null
    ):void;

```


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.