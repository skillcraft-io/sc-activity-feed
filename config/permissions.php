<?php

return [
    [
        'name' => 'Activity Feed',
        'flag' => 'activity-feed.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'activity-feed.destroy',
        'parent_flag' => 'activity-feed.index',
    ],
];
