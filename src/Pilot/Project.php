<?php

namespace Flex360\Pilot\Pilot;

use Illuminate\Support\Str;
use Spatie\Image\Manipulations;
use Illuminate\Support\Facades\DB;
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
use Flex360\Pilot\Pilot\Traits\HasMediaAttributes;
use Flex360\Pilot\Facades\Service as ServiceFacade;
use Flex360\Pilot\Facades\Project as ProjectFacade;
use Flex360\Pilot\Facades\ProjectCategory as ProjectCategoryFacade;

class Project extends Model implements HasMedia
{
    use PresentableTrait, HasMediaTrait, 
        SoftDeletes, HasMediaAttributes,
        SocialMetadataTrait, UserHtmlTrait,
        HasEmptyStringAttributes, PilotTablePrefix  {
        HasMediaAttributes::registerMediaConversions insteadof HasMediaTrait;
    }

    protected $table = 'projects';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $emptyStrings = [
        'title', 'summary', 'location', 'completion_date', 'featured'
    ];

    protected $mediaAttributes = ['featured_image', 'gallery'];

    public function getSummaryBackend()
    {
        if ($this->summary != null) {
            return substr(strip_tags($this->summary), 0, 60) . '...';
        } else {
            return 'N/A';
        }
    }

    public function project_categories()
    {
        return $this->belongsToMany(root_class(ProjectCategoryFacade::class), $this->getPrefix() . 'project_' . config('pilot.table_prefix') . 'project_category')
                    ->orderBy('name');
    }

    public function services()
    {
        return $this->belongsToMany(root_class(ServiceFacade::class), $this->getPrefix() . 'project_' . config('pilot.table_prefix') . 'service')
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
        foreach ($model->project_categories as $cat) {
            $newModel->project_categories()->attach($cat);
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
        $status = \Project::getStatuses();

        return (object) array(
            'id' => $this->status,
            'name' => $status[$this->status],
        );
    }

    public function url()
    {
        return route('project.detail', [
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
