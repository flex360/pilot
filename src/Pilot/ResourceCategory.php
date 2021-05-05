<?php

namespace Flex360\Pilot\Pilot;

use Illuminate\Support\Str;
use Flex360\Pilot\Pilot\Resource;
use Illuminate\Database\Eloquent\Model;
use Flex360\Pilot\Scopes\PublishedScope;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Flex360\Pilot\Pilot\Traits\UserHtmlTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Flex360\Pilot\Pilot\Traits\PilotTablePrefix;
use Flex360\Pilot\Pilot\Traits\PresentableTrait;
use Flex360\Pilot\Pilot\Traits\HasMediaAttributes;
use Flex360\Pilot\Pilot\Traits\SocialMetadataTrait;
use Flex360\Pilot\Facades\Resource as ResourceFacade;
use Flex360\Pilot\Pilot\Traits\HasEmptyStringAttributes;

class ResourceCategory extends Model implements HasMedia
{
    use PresentableTrait, HasMediaTrait, 
        SoftDeletes, HasMediaAttributes,
        SocialMetadataTrait, UserHtmlTrait,
        HasEmptyStringAttributes, PilotTablePrefix  {
        HasMediaAttributes::registerMediaConversions insteadof HasMediaTrait;
    }

    protected $table = 'resource_categories';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $emptyStrings = [
        'name',
    ];

    protected $mediaAttributes = [];

    public function resources()
    {
        if (config('pilot.plugins.resources.children.resource_category.fields.resource_sort_method') == 'manual_sort') {
            return $this->belongsToMany(root_class(ResourceFacade::class), $this->getPrefix() . 'resource_' . config('pilot.table_prefix') . 'resource_category')
                        ->withPivot('position')
                        ->orderBy(config('pilot.table_prefix') . 'resource_' . config('pilot.table_prefix') . 'resource_category.position');
        } else {
            return $this->belongsToMany(root_class(ResourceFacade::class), $this->getPrefix() . 'resource_' . config('pilot.table_prefix') . 'resource_category')
                        ->orderBy('title');
        }
    }

    public static function getSelectList()
    {
        return static::orderBy('name')
            ->get()
            ->pluck('name', 'id');
    }

    public function duplicate()
    {
        $model = $this;

        $newModel = $model->replicate();

        // append to the title to designate a copy
        $newModel->name .= ' (Copy)';
        $newModel->status = 10;

        $newModel->push();

        // copy all attached categories over to new model
        foreach ($model->resources()->withoutGlobalScope(PublishedScope::class)->get() as $resource) {
            $newModel->resources()->withoutGlobalScope(PublishedScope::class)->attach($resource);
        }

        return $newModel;
    }

    public static function getStatuses()
    {
        return array(
            10 => 'Draft',
            30 => 'Published'
        );
    }

    public function getStatus()
    {
        $status = \ResourceCategory::getStatuses();

        return (object) array(
            'id' => $this->status,
            'name' => $status[$this->status],
        );
    }

    public function url()
    {
        return route('resource.index');
    }

    public function getSlug()
    {
        return Str::slug($this->name);
    }
}
