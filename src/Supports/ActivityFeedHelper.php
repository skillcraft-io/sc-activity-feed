<?php

namespace Skillcraft\ActivityFeed\Supports;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Skillcraft\ActivityFeed\Models\ActivityFeed;
use Illuminate\Support\Collection as SupportCollection;

class ActivityFeedHelper
{
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
    public function getActivityFeedItems(
        ?Model $owner = null,
        ?Model $module = null,
        bool $isPrivate = false,
        int $limit = 10,
        int $offet = 0,
    ):Collection {
        return (new ActivityFeed)
            ->query()
            ->when($owner, function ($query) use ($owner) {
                $query->HasOwner($owner);
            })
            ->when($isPrivate, function ($query) {
                $query->IsPrivate();
            })
            ->when(!$isPrivate, function ($query) {
                $query->IsPublic();
            })
            ->when($module, function ($query) use ($module) {
                $query->HasModule($module);
            })
            ->limit($limit)
            ->offset($offet)
            ->get();
    }


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
    public function getActivityFeedItem(
        ?Model $owner = null,
        ?Model $module = null,
        bool $isPrivate = false,
        null|int|array $id = null
    ):?Model {
        return (new ActivityFeed)
            ->query()
            ->when($id && !is_array($id), function ($query) use ($id) {
                $query->where('id', $id);
            })
            ->when($id && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->when($owner, function ($query) use ($owner) {
                $query->HasOwner($owner);
            })
            ->when($isPrivate, function ($query) {
                $query->IsPrivate();
            })
            ->when(!$isPrivate, function ($query) {
                $query->IsPublic();
            })
            ->when($module, function ($query) use ($module) {
                $query->HasModule($module);
            })
            ->first();
    }

    /**
     * Deletes activity feed item(s).
     *
     * @param ?Model $owner The owner of the activity feed item. Default is null.
     * @param ?Model $module The module of the activity feed item. Default is null.
     * @param bool $isPrivate Indicates if the activity feed item is private. Default is false.
     * @param null|int|array $id The ID or array of IDs of the activity feed item(s) to delete. Default is null.

     * @return void
     */
    public function deleteActivityFeedItem(
        ?Model $owner = null,
        ?Model $module = null,
        bool $isPrivate = false,
        null|int|array $id = null
    ):void {
        (new ActivityFeed)
            ->query()
            ->when($id && !is_array($id), function ($query) use ($id) {
                $query->where('id', $id);
            })
            ->when($id && is_array($id), function ($query) use ($id) {
                $query->whereIn('id', $id);
            })
            ->when($owner, function ($query) use ($owner) {
                $query->HasOwner($owner);
            })
            ->when($module, function ($query) use ($module) {
                $query->HasModule($module);
            })
            ->when($isPrivate, function ($query) {
                $query->IsPrivate();
            })
            ->when(!$isPrivate, function ($query) {
                $query->IsPublic();
            })
            ->delete();
    }

 
    /**
     * Retrieves the validation rules for the validator.
     *
     * @return array The validation rules for the validator.
     */
    public function getValidatorRules():array
    {
        return [
            'owner_id' => 'nullable|integer',
            'owner_type' => 'nullable|string',
            'module_id' => 'nullable|integer',
            'module_type' => 'nullable|string',
            'title' => 'required|string|max:50',
            'message' => 'required|string',
            'is_private' => 'required|boolean'
        ];
    }

    
    /**
     * Validates and returns the sanitized input data for the given parameters.
     *
     * @param string $title The title of the data.
     * @param string $message The message of the data.
     * @param bool $isPrivate (optional) Whether the data is private. Defaults to `false`.
     * @param Model|null $owner (optional) The owner of the data. Defaults to `null`.
     * @param Model|null $module (optional) The module of the data. Defaults to `null`.
     * @throws ValidationException If the input data fails validation.
     * @return SupportCollection The sanitized input data.
     */
    public function getValidatedData(
        string $title,
        string $message,
        bool $isPrivate = false,
        ?Model $owner = null,
        ?Model $module = null
    ): SupportCollection {
        $validator = Validator::make([
            'title' => $title,
            'message' => $message,
            'is_private' => $isPrivate,
            'owner_id' => $owner ? $owner->id : null,
            'owner_type' => $owner ? get_class($owner) : null,
            'module_id' => $module ? $module->id : null,
            'module_type' => $module ? get_class($module) : null,
        ], $this->getValidatorRules());
 
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return collect($validator->validated());
    }
}
