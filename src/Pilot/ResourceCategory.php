<?php

namespace Flex360\Pilot\Pilot;

use Flex360\Pilot\Pilot\Resource;
use Flex360\Pilot\Facades\Resource as ResourceFacade;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Flex360\Pilot\Pilot\Traits\UserHtmlTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Flex360\Pilot\Pilot\Traits\PresentableTrait;
use Flex360\Pilot\Pilot\Traits\SocialMetadataTrait;
use Flex360\Pilot\Pilot\Traits\PilotTablePrefix;
use Flex360\Pilot\Pilot\Traits\HasEmptyStringAttributes;
use Flex360\Pilot\Pilot\Traits\HasMediaAttributes;
use Illuminate\Support\Str;

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
        return $this->belongsToMany(root_class(ResourceFacade::class), $this->getPrefix() . 'resource_' . config('pilot.table_prefix') . 'resource_category')->orderBy('title');
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

        $newModel->push();

        // copy all attached categories over to new model
        foreach ($model->resources as $resource) {
            $newModel->resources()->attach($resource);
        }

        return $newModel;
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
