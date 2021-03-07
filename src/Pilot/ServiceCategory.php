<?php

namespace Flex360\Pilot\Pilot;

use Flex360\Pilot\Pilot\Service;
use Flex360\Pilot\Facades\Service as ServiceFacade;
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

    public function services()
    {
        if (config('pilot.plugins.services.children.manage_service_categories.fields.service_sort_method') == 'manual_sort') {
            return $this->belongsToMany(root_class(ServiceFacade::class), $this->getPrefix() . 'service_' . config('pilot.table_prefix') . 'service_category')
                        ->where('status', 30)
                        ->orderBy('position');
        } else {
            return $this->belongsToMany(root_class(ServiceFacade::class), $this->getPrefix() . 'service_' . config('pilot.table_prefix') . 'service_category')
                        ->where('status', 30)
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

        $newModel->push();

        // copy all attached categories over to new model
        foreach ($model->services as $service) {
            $newModel->services()->attach($service);
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
