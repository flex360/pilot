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

class Faq extends Model implements HasMedia
{
    use PresentableTrait, HasMediaTrait,
        SoftDeletes, HasMediaAttributes,
        SocialMetadataTrait, UserHtmlTrait,
        HasEmptyStringAttributes, PilotTablePrefix,
        SupportsMultipleSites, PilotModuleCommon, Publishable  {
        HasMediaAttributes::registerMediaConversions insteadof HasMediaTrait;
    }

    protected $table = 'faqs';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $emptyStrings = [
        'question', 'answer'
    ];

    public function getShortAnswerBackend()
    {
        if ($this->answer != null) {
            return substr(strip_tags($this->answer), 0, 60) . '...';
        } else {
            return 'N/A';
        }
    }

    public function faq_categories()
    {
        return $this->belongsToMany(root_class(FaqCategoryFacade::class), $this->getPrefix() . 'faq_' . config('pilot.table_prefix') . 'faq_category')
                    ->orderBy('name');
    }

    public function duplicate()
    {
        $model = $this;

        $newModel = $model->replicate();

        // append to the title to designate a copy
        $newModel->question .= ' (Copy)';

        // make new copy a draft
        $newModel->status = 10;

        $newModel->push();

        // copy all attached categories over to new model
        foreach ($model->faq_categories as $cat) {
            $newModel->faq_categories()->attach($cat);
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
        $status = \Faq::getStatuses();

        return (object) array(
            'id' => $this->status,
            'name' => $status[$this->status],
        );
    }

    public function url()
    {
        return route('faq.index', [
            'faq' => $this->id,
            'slug' => $this->getSlug(),
        ]);
    }

    public function getUrlAttribute($value)
    {
        return $this->url();
    }

    public function getSlug()
    {
        return Str::slug($this->question);
    }

    public function getCategoryUrl($categoryId)
    {
        return route('faq.index', [
            'cat' => $categoryId,
            'faq' => $this->id,
        ]) . '#cat-' . $categoryId . '-faq-' . $this->id;
    }
}
