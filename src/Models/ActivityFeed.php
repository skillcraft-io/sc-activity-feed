<?php

namespace Skillcraft\ActivityFeed\Models;

use Botble\Base\Casts\SafeContent;
use Skillcraft\Core\Models\CoreModel;
use Illuminate\Database\Eloquent\Model;
use Botble\Base\Models\BaseQueryBuilder;
use Skillcraft\ActivityFeed\Database\Factories\ActivityFeedFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityFeed extends CoreModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'sc_activity_feeds';

    protected $fillable = [
        'owner_id',
        'owner_type',
        'module_id',
        'module_type',
        'title',
        'message',
        'is_private'
    ];

    protected $casts = [
        'title' => SafeContent::class,
        'message' => SafeContent::class,
        'is_private' => 'boolean'
    ];

    public static function newFactory(): ActivityFeedFactory
    {
        return ActivityFeedFactory::new();
    }

    public function owner():MorphTo
    {
        return $this->morphTo();
    }

    public function module():MorphTo
    {
        return $this->morphTo();
    }

    public function scopeHasOwner(BaseQueryBuilder $query, Model $owner): void
    {
        $query->where('owner_id', $owner->id)
            ->where('owner_type', get_class($owner));
    }

    public function scopeHasModule(BaseQueryBuilder $query, Model $module): void
    {
        $query->where('module_id', $module->id)
            ->where('module_type', get_class($module));
    }

    public function scopeIsPrivate(BaseQueryBuilder $query): void
    {
        $query->where('is_private', true);
    }

    public function scopeIsPublic(BaseQueryBuilder $query): void
    {
        $query->where('is_private', false);
    }

    public function getOwnerName():string
    {
        if (property_exists($this->owner, 'name')) {
            return $this->owner->name;
        }

        if (method_exists($this->owner, 'getName')) {
            return $this->owner->getName();
        }

        return '#'.$this->owner->id;
    }

    public function getOwnerLink():string
    {
        if (property_exists($this->owner, 'url')) {
            return $this->owner->url;
        }

        if (method_exists($this->owner, 'getAdminUrl')) {
            return $this->owner->getAdminUrl();
        }

        return '#';
    }

    public function getOwnerAvatarLink():?string
    {
        if (property_exists($this->owner, 'avatar_url')) {
            return $this->owner->avatar_url;
        }

        if (method_exists($this->owner, 'avatarUrl')) {
            return $this->owner->avatarUrl;
        }

        return null;
    }
}
