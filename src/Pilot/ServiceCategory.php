<?php

namespace Flex360\Pilot\Pilot;

use Illuminate\Support\Str;
use Flex360\Pilot\Pilot\Service;
use Illuminate\Database\Eloquent\Model;
use Flex360\Pilot\Scopes\PublishedScope;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Flex360\Pilot\Pilot\Traits\UserHtmlTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Flex360\Pilot\Pilot\Traits\PilotTablePrefix;
use Flex360\Pilot\Pilot\Traits\PresentableTrait;
use Flex360\Pilot\Pilot\Traits\HasMediaAttributes;
use Flex360\Pilot\Facades\Service as ServiceFacade;
use Flex360\Pilot\Pilot\Traits\SocialMetadataTrait;
use Flex360\Pilot\Pilot\Traits\HasEmptyStringAttributes;

class ServiceCategory extends Model implements HasMedia
{
    use PresentableTrait, HasMediaTrait, 
        SoftDeletes, HasMediaAttributes,
        SocialMetadataTrait, UserHtmlTrait,
        HasEmptyStringAttributes, PilotTablePrefix  {
        HasMediaAttributes::registerMediaConversions insteadof HasMediaTrait;
    }

    protected $table = 'service_categories';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $emptyStrings = [
        'name',
    ];

    protected $mediaAttributes = ['featured_image'];

    protected static function booted()
    {
        static::addGlobalScope(new PublishedScope);
    }

    public function services()
    {
        if (config('pilot.plugins.services.children.manage_service_categories.fields.service_sort_method') == 'manual_sort') {
            return $this->belongsToMany(root_class(ServiceFacade::class), $this->getPrefix() . 'service_' . config('pilot.table_prefix') . 'service_category')
                        ->withPivot('position')
                        ->orderBy(config('pilot.table_prefix') . 'service_' . config('pilot.table_prefix') . 'service_category.position');
        } else {
            return $this->belongsToMany(root_class(ServiceFacade::class), $this->getPrefix() . 'service_' . config('pilot.table_prefix') . 'service_category')
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
        foreach ($model->services()->withoutGlobalScope(PublishedScope::class)->get() as $service) {
            $newModel->services()->withoutGlobalScope(PublishedScope::class)->attach($service);
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
        $status = \ServiceCategory::getStatuses();

        return (object) array(
            'id' => $this->status,
            'name' => $status[$this->status],
        );
    }

    public function url()
    {
        return route('service.index');
    }

    public function getSlug()
    {
        return Str::slug($this->name);
    }
}
