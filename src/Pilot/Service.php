<?php

namespace Flex360\Pilot\Pilot;

use Illuminate\Support\Str;
use Spatie\Image\Manipulations;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Flex360\Pilot\Scopes\PublishedScope;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Flex360\Pilot\Pilot\Traits\UserHtmlTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Flex360\Pilot\Pilot\Traits\PilotTablePrefix;
use Flex360\Pilot\Pilot\Traits\PresentableTrait;
use Flex360\Pilot\Pilot\Traits\PilotModuleCommon;
use Flex360\Pilot\Pilot\Traits\HasMediaAttributes;
use Flex360\Pilot\Facades\Project as ProjectFacade;
use Flex360\Pilot\Facades\Service as ServiceFacade;
use Flex360\Pilot\Pilot\Traits\SocialMetadataTrait;
use Flex360\Pilot\Pilot\Traits\SupportsMultipleSites;
use Flex360\Pilot\Pilot\Traits\HasEmptyStringAttributes;
use Flex360\Pilot\Facades\ServiceCategory as ServiceCategoryFacade;
use Flex360\Pilot\Pilot\Traits\Publishable;

class Service extends Model implements HasMedia
{
    use PresentableTrait, HasMediaTrait,
        SoftDeletes, HasMediaAttributes,
        SocialMetadataTrait, UserHtmlTrait,
        HasEmptyStringAttributes, PilotTablePrefix,
        SupportsMultipleSites, PilotModuleCommon, Publishable  {
        HasMediaAttributes::registerMediaConversions insteadof HasMediaTrait;
    }

    protected $table = 'services';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $emptyStrings = [
        'title', 'subservices', 'description', 'icon', 'featured_image',
    ];

    protected $mediaAttributes = ['icon', 'featured_image'];

    public function getDescriptionBackend()
    {
        if ($this->description != null) {
            return substr(strip_tags($this->description), 0, 60) . '...';
        } else {
            return 'N/A';
        }
    }

    public function service_categories()
    {
        return $this->belongsToMany(root_class(ServiceCategoryFacade::class), $this->getPrefix() . 'service_' . config('pilot.table_prefix') . 'service_category')
                    ->orderBy('name');
    }

    public function projects()
    {
        return $this->belongsToMany(root_class(ProjectFacade::class), $this->getPrefix() . 'project_' . config('pilot.table_prefix') . 'service')
                    ->orderBy('title');
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
        foreach ($model->service_categories as $cat) {
            $newModel->service_categories()->attach($cat);
        }

        return $newModel;
    }

    public function setSubservicesAttribute($value)
    {
        if (!empty($value)) {
            $subservices = explode("\r\n", $value);
            $this->attributes['subservices'] = json_encode($subservices);
        } else {
            $this->attributes['subservices'] = '';
        }
    }

    public function getSubservicesAttribute($value)
    {
        if ($value != null) {
            $subservices = json_decode($value);
            return implode("\r\n", $subservices);
        } else {
            return '';
        }
    }

    public function getSubservices()
    {
        if ($this->subservices != null) {
            return json_decode($this->attributes['subservices']);
        } else {
            return '';
        }
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
        $status = ServiceFacade::getStatuses();

        return (object) array(
            'id' => $this->status,
            'name' => $status[$this->status],
        );
    }

    public function url()
    {
        return route('service.detail', [
            'id' => $this->id,
            'slug' => $this->getSlug(),
        ]);
    }

    public function getUrlAttribute()
    {
        return $this->url();
    }

    public function getSlug()
    {
        return Str::slug($this->title);
    }
}
