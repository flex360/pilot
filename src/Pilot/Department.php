<?php

namespace Flex360\Pilot\Pilot;

use Illuminate\Support\Str;
use Spatie\Image\Manipulations;
use Flex360\Pilot\Pilot\Employee;
use Spatie\MediaLibrary\Models\Media;
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
use Flex360\Pilot\Facades\Employee as EmployeeFacade;
use Flex360\Pilot\Facades\Resource as ResourceFacade;
use Flex360\Pilot\Pilot\Traits\HasEmptyStringAttributes;

class Department extends Model implements HasMedia
{
    use PresentableTrait, HasMediaTrait, 
        SoftDeletes, HasMediaAttributes,
        SocialMetadataTrait, UserHtmlTrait,
        HasEmptyStringAttributes, PilotTablePrefix  {
        HasMediaAttributes::registerMediaConversions insteadof HasMediaTrait;
    }

    protected $table = 'department';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $emptyStrings = [
        'name', 'intro_text', 'featured_image', 'slug', 'summary',
    ];

    protected $mediaAttributes = ['featured_image'];

    protected static function booted()
    {
        static::addGlobalScope(new PublishedScope);
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
        if (config('pilot.plugins.employees.children.departments.fields.employee_sort_method') == 'manual_sort') {
            return $this->belongsToMany(root_class(EmployeeFacade::class), $this->getPrefix() . 'department_' . config('pilot.table_prefix') . 'employee')
                            ->withPivot('position')
                            ->orderBy(config('pilot.table_prefix') . 'department_' . config('pilot.table_prefix') . 'employee.position');
        } else {
            return $this->belongsToMany(root_class(EmployeeFacade::class), $this->getPrefix() . 'department_' . config('pilot.table_prefix') . 'employee')
                        ->orderBy('first_name');
        }
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
        foreach ($model->employees()->withoutGlobalScope(PublishedScope::class)->get() as $cat) {
            $newModel->employees()->withoutGlobalScope(PublishedScope::class)->attach($cat);
        }

        // copy all attached tags over to new model
        foreach ($model->tags as $cat) {
            $newModel->tags()->attach($cat);
        }

        // copy all attached resources over to new model
        foreach ($model->resources()->withoutGlobalScope(PublishedScope::class)->get() as $cat) {
            $newModel->resources()->withoutGlobalScope(PublishedScope::class)->attach($cat);
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
