<?php

use Botble\Base\Facades\AdminHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Skillcraft\ActivityFeed\Http\Controllers'], function () {
    AdminHelper::registerRoutes(function () {

        Route::group(['prefix' => 'activity-feeds', 'as' => 'activity-feed.'], function () {
            Route::resource('', 'ActivityFeedController')
                ->parameters(['' => 'activity-feed']);

                Route::get('widgets/activities', [
                    'as' => 'widget.feed',
                    'uses' => 'ActivityFeedController@getWidgetActivities',
                    'permission' => 'activity-feed.index',
                ]);
        });
    });
});
