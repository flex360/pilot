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
use Flex360\Pilot\Facades\Faq as FaqFacade;
use Flex360\Pilot\Facades\FaqCategory as FaqCategoryFacade;

class FaqCategory extends Model implements HasMedia
{
    use PresentableTrait, HasMediaTrait, 
        SoftDeletes, HasMediaAttributes,
        SocialMetadataTrait, UserHtmlTrait,
        HasEmptyStringAttributes, PilotTablePrefix  {
        HasMediaAttributes::registerMediaConversions insteadof HasMediaTrait;
    }

    protected $table = 'faq_categories';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $emptyStrings = [
        'name'
    ];

    public function faqs()
    {
        return $this->belongsToMany(root_class(FaqFacade::class), config('pilot.table_prefix') . 'faq_' . config('pilot.table_prefix') . 'faq_category')
                    ->where('status', 30)
                    ->orderBy(config('pilot.table_prefix') . 'faq_' . config('pilot.table_prefix') . 'faq_category.position');
    }

    // public function url()
    // {
    //     return route('faqcategory.detail', [
    //         'faqCategory' => $this->id,
    //         'slug' => $this->getSlug(),
    //     ]);
    // }

    // public function getUrlAttribute($value)
    // {
    //     return $this->url();
    // }

    // public function getSlug()
    // {
    //     return Str::slug($this->name);
    // }

}
