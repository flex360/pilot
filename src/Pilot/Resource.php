<?php

namespace Flex360\Pilot\Pilot;

use Illuminate\Support\Str;
use Flex360\Pilot\Pilot\Department;
use Spatie\MediaLibrary\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Flex360\Pilot\Scopes\PublishedScope;
use Flex360\Pilot\Pilot\ResourceCategory;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Flex360\Pilot\Pilot\Traits\UserHtmlTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Flex360\Pilot\Pilot\Traits\PilotTablePrefix;
use Flex360\Pilot\Pilot\Traits\PresentableTrait;
use Flex360\Pilot\Pilot\Traits\HasMediaAttributes;
use Flex360\Pilot\Pilot\Traits\SocialMetadataTrait;
use Flex360\Pilot\Pilot\Traits\HasEmptyStringAttributes;
use Flex360\Pilot\Facades\Department as DepartmentFacade;
use Flex360\Pilot\Facades\ResourceCategory as ResourceCategoryFacade;

class Resource extends Model implements HasMedia
{
    use PresentableTrait, HasMediaTrait, 
        SoftDeletes, HasMediaAttributes,
        SocialMetadataTrait, UserHtmlTrait,
        HasEmptyStringAttributes, PilotTablePrefix  {
        HasMediaAttributes::registerMediaConversions insteadof HasMediaTrait;
    }

    protected $table = 'resources';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $emptyStrings = [
        'title', 'short_description', 'link'
    ];

    protected $mediaAttributes = ['link'];

    protected static function booted()
    {
        static::addGlobalScope(new PublishedScope);
    }

    public function resource_categories()
    {
        return $this->belongsToMany(root_class(ResourceCategoryFacade::class), $this->getPrefix() . 'resource_' . config('pilot.table_prefix') . 'resource_category')
                    ->orderBy('name');
    }

    public function departments()
    {
        return $this->belongsToMany(root_class(DepartmentFacade::class), config('pilot.table_prefix') . 'department_' . config('pilot.table_prefix') . 'resource')
                    ->orderBy('name');
    }

    public static function getSelectList()
    {
        return static::orderBy('title')
            ->get()
            ->prepend(['id' => '', 'title' => '[No Category Selected]'])
            ->pluck('title', 'id');
    }

    public function duplicate()
    {
        $model = $this;

        $newModel = $model->replicate();

        // append to the title to designate a copy
        $newModel->title .= ' (Copy)';

        // copy media items
        foreach ($model->media as $media) {
            $media->copyTo($newModel);
        }

        // make new copy a draft
        $newModel->status = 10;

        $newModel->push();

        // copy all attached categories over to new model
        foreach ($model->resource_categories as $cat) {
            $newModel->resource_categories()->attach($cat);
        }

        return $newModel;
    }

    public static function getStatuses()
    {
        return [
            10 => 'Draft',
            30 => 'Published'
        ];
    }

    public function getStatus()
    {
        $status = static::getStatuses();

        return (object) [
            'id' => $this->status,
            'name' => $status[$this->status],
        ];
    }

    public function url()
    {
        return route('resource.index', [
            'resource' => $this->id,
            'slug' => $this->getSlug(),
        ]);
    }

    public function getUrlAttribute($value)
    {
        return $this->url();
    }

    public function getSlug()
    {
        return Str::slug($this->title);
    }
}
