<?php

namespace Skillcraft\ActivityFeed\Http\Controllers;

use Illuminate\Http\Request;
use Botble\Base\Http\Controllers\BaseController;
use Skillcraft\ActivityFeed\Models\ActivityFeed;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Skillcraft\ActivityFeed\Tables\ActivityFeedTable;

class ActivityFeedController extends BaseController
{

    public function getWidgetActivities(Request $request)
    {
        $limit = $request->integer('paginate', 10);
        $limit = $limit > 0 ? $limit : 10;

        $histories = (new ActivityFeed())->query()
            ->with('owner')
            ->orderByDesc('created_at')
            ->paginate($limit);

        return $this
            ->httpResponse()
            ->setData(view('plugins/activity-feed::widgets.activity-feed', compact('histories', 'limit'))->render());
    }

    public function index(ActivityFeedTable $dataTable)
    {
        $this->pageTitle(trans('plugins/activity-feed::activity-feed.name'));

        return $dataTable->renderTable();
    }

    public function destroy(ActivityFeed $activityFeed)
    {
        return DeleteResourceAction::make($activityFeed);
    }
}
