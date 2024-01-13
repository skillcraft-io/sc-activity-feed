$(() => {
    if (typeof BDashboard !== 'undefined') {
        BDashboard.loadWidget($('#widget_activity_feed').find('.widget-content'), route('activity-feed.widget.feed'))
    }
})
