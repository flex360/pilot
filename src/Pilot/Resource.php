<?php

namespace Flex360\Pilot\Pilot;

use Illuminate\Support\Str;
use Flex360\Pilot\Pilot\ResourceCategory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\Models\Media;
use Flex360\Pilot\Pilot\Traits\UserHtmlTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Flex360\Pilot\Pilot\Traits\PresentableTrait;
use Flex360\Pilot\Pilot\Traits\SocialMetadataTrait;
use Flex360\Pilot\Pilot\Traits\PilotTablePrefix;
use Flex360\Pilot\Pilot\Traits\HasEmptyStringAttributes;

class Resource extends Model implements HasMedia
{
    use PresentableTrait,
        SocialMetadataTrait,
        UserHtmlTrait,
        HasMediaTrait,
        SoftDeletes,
        HasEmptyStringAttributes,
        PilotTablePrefix;

    protected $table = 'resources';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $emptyStrings = [
        'title', 'short_description', 'link'
    ];

    public function registerMediaConversions(Media $media = null)
    {
        // let's always use standard names like thumb, xsmall, small, medium, large, xlarge

        $this->addMediaConversion('small')
            ->width(300)
            ->height(300);
    }

    public function getLinkAttribute($value)
    {
        $mediaItem = $this->getFirstMedia('link');

        if (!empty($mediaItem)) {
            return $mediaItem->getUrl();
        }

        return $value;
    }

    public function getLinkThumbAttribute($value)
    {
        $mediaItem = $this->getFirstMedia('link');

        if (!empty($mediaItem)) {
            return $mediaItem->getUrl('thumb');
        }

        return $value;
    }

    public function resource_categories()
    {
        return $this->belongsToMany(ResourceCategory::class, $this->getPrefix() . 'resource_' . config('pilot.table_prefix') . 'resource_category')->orderBy('name');
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
        $status = \Resource::getStatuses();

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
