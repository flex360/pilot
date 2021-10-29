<?php

namespace Flex360\Pilot\Pilot;

use Illuminate\Support\Str;
use Spatie\Image\Manipulations;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Flex360\Pilot\Scopes\PublishedScope;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Flex360\Pilot\Facades\Faq as FaqFacade;
use Flex360\Pilot\Pilot\Traits\UserHtmlTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Flex360\Pilot\Pilot\Traits\PilotTablePrefix;
use Flex360\Pilot\Pilot\Traits\PresentableTrait;
use Flex360\Pilot\Pilot\Traits\PilotModuleCommon;
use Flex360\Pilot\Pilot\Traits\HasMediaAttributes;
use Flex360\Pilot\Pilot\Traits\SocialMetadataTrait;
use Flex360\Pilot\Pilot\Traits\SupportsMultipleSites;
use Flex360\Pilot\Pilot\Traits\HasEmptyStringAttributes;
use Flex360\Pilot\Facades\FaqCategory as FaqCategoryFacade;
use Flex360\Pilot\Pilot\Traits\Publishable;

class FaqCategory extends Model implements HasMedia
{
    use PresentableTrait, HasMediaTrait,
        SoftDeletes, HasMediaAttributes,
        SocialMetadataTrait, UserHtmlTrait,
        HasEmptyStringAttributes, PilotTablePrefix,
        SupportsMultipleSites, PilotModuleCommon, Publishable  {
        HasMediaAttributes::registerMediaConversions insteadof HasMediaTrait;
    }

    protected $table = 'faq_categories';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $emptyStrings = [
        'name'
    ];

    public function faqs()
    {
        if (config('pilot.plugins.faqs.children.manage_faq_categories.fields.faq_sort_method') == 'manual_sort') {
            return $this->belongsToMany(root_class(FaqFacade::class), $this->getPrefix() . 'faq_' . config('pilot.table_prefix') . 'faq_category')
                        ->withPivot('position')
                        ->orderBy(config('pilot.table_prefix') . 'faq_' . config('pilot.table_prefix') . 'faq_category.position');
        } else {
            return $this->belongsToMany(root_class(FaqFacade::class), $this->getPrefix() . 'faq_' . config('pilot.table_prefix') . 'faq_category')
                        ->orderBy('question');
        }
    }

    public function duplicate()
    {
        $model = $this;

        $newModel = $model->replicate();

        // append to the title to designate a copy
        $newModel->name .= ' (Copy)';
        $newModel->status = 10;

        // copy media items
        foreach ($model->media as $media) {
            $media->copyTo($newModel);
        }

        $newModel->push();

        // copy all attached categories over to new model
        foreach ($model->faqs()->withoutGlobalScope(PublishedScope::class)->get() as $faq) {
            $newModel->faqs()->withoutGlobalScope(PublishedScope::class)->attach($faq);
        }

        return $newModel;
    }

    public function url()
    {
        return route('faq.index');
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
