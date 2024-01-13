<?php

namespace Skillcraft\ActivityFeed\Tables;

use Botble\Table\Columns\Column;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\CreatedAtColumn;
use Skillcraft\ActivityFeed\Models\ActivityFeed;
use Illuminate\Database\Eloquent\Builder;

class ActivityFeedTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(ActivityFeed::class)
            ->addColumns([
                FormattedColumn::make('owner_id')
                    ->label(trans('plugins/activity-feed::activity-feed.tables.owner_id'))
                    ->width(200)
                    ->orderable(false)
                    ->searchable(false)
                    ->getValueUsing(function (FormattedColumn $column) {
                        return $column->getItem()->getOwnerName();
                    }),
                Column::make('title'),
                Column::make('message'),
                CreatedAtColumn::make('created_at')->dateFormat('Y-m-d H:i')->width(150),
                
            ])
            ->addActions([
                DeleteAction::make()->route('activity-feed.destroy'),
            ])
            ->addBulkAction(DeleteBulkAction::make()->permission('activity-feed.destroy'))
            ->queryUsing(function (Builder $query) {
                $query
                    ->select([
                    'id',
                    'owner_id',
                    'owner_type',
                    'title',
                    'message',
                    'created_at'
                ]);
            });
    }
}
