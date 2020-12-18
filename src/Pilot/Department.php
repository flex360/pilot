<?php

namespace Flex360\Pilot\Pilot;

use Illuminate\Support\Str;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Flex360\Pilot\Pilot\Traits\UserHtmlTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Flex360\Pilot\Pilot\Traits\PilotTablePrefix;
use Flex360\Pilot\Pilot\Traits\PresentableTrait;
use Flex360\Pilot\Pilot\Traits\SocialMetadataTrait;
use Flex360\Pilot\Pilot\Traits\HasEmptyStringAttributes;
use Flex360\Pilot\Facades\Employee as EmployeeFacade;
use Flex360\Pilot\Facades\Resource as ResourceFacade;

class Department extends Model implements HasMedia
{
    use PresentableTrait,
        SocialMetadataTrait,
        UserHtmlTrait,
        HasMediaTrait,
        SoftDeletes,
        HasEmptyStringAttributes,
        PilotTablePrefix;

    protected $table = 'department';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $emptyStrings = [
        'name', 'intro_text', 'featured_image', 'slug', 'summary',
    ];

    public function registerMediaConversions(Media $media = null)
    {
        // let's always use standard names like thumb, xsmall, small, medium, large, xlarge
        $this->addMediaConversion('thumb')
        ->crop(Manipulations::CROP_TOP_RIGHT, 300, 300);

        $this->addMediaConversion('small')
            ->width(300)
            ->height(300);
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function ($department) {
            if (empty($department->slug)) {
                $department->slug = Str::slug($department->name);
            }
        });
    }

    public function employees()
    {
        return $this->belongsToMany(root_class(EmployeeFacade::class), config('pilot.table_prefix') . 'department_' . config('pilot.table_prefix') . 'employee')->orderBy('position');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, config('pilot.table_prefix') . 'department_tag')->orderBy('name');
    }

    public function resources()
    {
        return $this->belongsToMany(root_class(ResourceFacade::class), config('pilot.table_prefix') . 'department_' . config('pilot.table_prefix') . 'resource')->orderBy('title');
    }

    public function getFullNameAttribute($value)
    {
        if (isset($this->attributes['first_name'])) {
            return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
        }
        return null;
    }

    public function getFeaturedImageAttribute($value)
    {
        $mediaItem = $this->getFirstMedia('featured_image');

        if (!empty($mediaItem)) {
            return $mediaItem->getUrl();
        }

        return $value;
    }

    public function getFeaturedImageThumbAttribute($value)
    {
        $mediaItem = $this->getFirstMedia('featured_image');

        if (!empty($mediaItem)) {
            return $mediaItem->getUrl('thumb');
        }

        return $value;
    }

    public static function getSelectList()
    {
        return static::orderBy('last_name')
            ->get()
            ->prepend(['id' => '', 'fullName' => '[No Employee Selected]'])
            ->pluck('fullName', 'id');
    }

    public function duplicate()
    {
        $model = $this;

        $newModel = $model->replicate();

        // append to the title to designate a copy
        $newModel->name .= ' (Copy)';

        // make new copy a draft
        $newModel->status = 10;

        $newModel->push();

        // copy all attached employees over to new model
        foreach ($model->employees as $cat) {
            $newModel->employees()->attach($cat);
        }

        // copy all attached tags over to new model
        foreach ($model->tags as $cat) {
            $newModel->tags()->attach($cat);
        }

        // copy all attached resources over to new model
        foreach ($model->resources as $cat) {
            $newModel->resources()->attach($cat);
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
        return route('department.index', [
            'department' => $this->id,
            'slug' => $this->getSlug(),
        ]);
    }

    public function getUrlAttribute($value)
    {
        return $this->url();
    }

    public function getSlug()
    {
        return Str::slug($this->name);
    }
}
