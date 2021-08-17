<?php

namespace Flex360\Pilot\Pilot;

use Illuminate\Support\Str;
use Flex360\Pilot\Pilot\Project;
use Illuminate\Database\Eloquent\Model;
use Flex360\Pilot\Scopes\PublishedScope;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Flex360\Pilot\Pilot\Traits\UserHtmlTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Flex360\Pilot\Pilot\Traits\PilotTablePrefix;
use Flex360\Pilot\Pilot\Traits\PresentableTrait;
use Flex360\Pilot\Pilot\Traits\HasMediaAttributes;
use Flex360\Pilot\Facades\Project as ProjectFacade;
use Flex360\Pilot\Pilot\Traits\SocialMetadataTrait;
use Flex360\Pilot\Pilot\Traits\HasEmptyStringAttributes;

class ProjectCategory extends Model implements HasMedia
{
    use PresentableTrait, HasMediaTrait, 
        SoftDeletes, HasMediaAttributes,
        SocialMetadataTrait, UserHtmlTrait,
        HasEmptyStringAttributes, PilotTablePrefix  {
        HasMediaAttributes::registerMediaConversions insteadof HasMediaTrait;
    }

    protected $table = 'project_categories';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $emptyStrings = [
        'name',
    ];

    protected $mediaAttributes = ['featured_image'];

    protected static function booted()
    {
        static::addGlobalScope(new PublishedScope);
    }

    public function projects()
    {
        if (config('pilot.plugins.projects.children.manage_project_categories.fields.project_sort_method') == 'manual_sort') {
            return $this->belongsToMany(root_class(ProjectFacade::class), $this->getPrefix() . 'project_' . config('pilot.table_prefix') . 'project_category')
                        ->withPivot('position')
                        ->orderBy(config('pilot.table_prefix') . 'project_' . config('pilot.table_prefix') . 'project_category.position');
        } else {
            return $this->belongsToMany(root_class(ProjectFacade::class), $this->getPrefix() . 'project_' . config('pilot.table_prefix') . 'project_category')
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
        foreach ($model->projects()->withoutGlobalScope(PublishedScope::class)->get() as $project) {
            $newModel->projects()->withoutGlobalScope(PublishedScope::class)->attach($project);
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
        $status = \ProjectCategory::getStatuses();

        return (object) array(
            'id' => $this->status,
            'name' => $status[$this->status],
        );
    }

    public function url()
    {
        return route('projectCategory.index', [
            'id' => $this->id,
            'slug' => $this->getSlug(),
        ]);
    }

    public function getSlug()
    {
        return Str::slug($this->name);
    }
}
