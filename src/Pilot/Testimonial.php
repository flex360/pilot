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
use Flex360\Pilot\Pilot\Traits\SocialMetadataTrait;
use Flex360\Pilot\Scopes\TestimonialsWithMediaScope;
use Flex360\Pilot\Pilot\Traits\SupportsMultipleSites;
use Flex360\Pilot\Pilot\Traits\HasEmptyStringAttributes;
use Flex360\Pilot\Pilot\Traits\Publishable;
use Flex360\Pilot\Facades\Site as SiteFacade;

class Testimonial extends Model implements HasMedia
{
    use PresentableTrait, HasMediaTrait,
        SoftDeletes, HasMediaAttributes,
        SocialMetadataTrait, UserHtmlTrait,
        HasEmptyStringAttributes, PilotTablePrefix,
        SupportsMultipleSites, PilotModuleCommon, Publishable  {
        HasMediaAttributes::registerMediaConversions insteadof HasMediaTrait;
    }

    protected $table = 'testimonials';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $emptyStrings = [
        'name', 'city', 'state', 'country', 'quote', 'attribution'
    ];

    protected $mediaAttributes = ['photo'];

    protected static function booted()
    {
        if (SiteFacade::isNotBackend()) {
            static::addGlobalScope(new TestimonialsWithMediaScope);
        }
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public static function getSelectList()
    {
        return static::orderBy('name')
            ->get()
            ->prepend(['id' => '', 'name' => '[No Testimonial Selected]'])
            ->pluck('name', 'id');
    }

    public function getQuoteDisplayBackend()
    {
        $newQuote = $this->quote;

        $newQuote = strip_tags($this->newQuote);

        return strlen($newQuote) > 50 ? substr($newQuote, 0, 50) . '...' : $newQuote;
    }

    public function duplicate()
    {
        $model = $this;

        $newModel = $model->replicate();

        // append to the title to designate a copy
        $newModel->name .= ' (Copy)';

        // copy media items
        // foreach ($model->media as $media) {
        //     $media->copyTo($newModel);
        // }

        // make new copy a draft
        $newModel->status = 10;

        $newModel->push();

        // copy all attached categories over to new model
        // foreach ($model->departments as $cat) {
        //     $newModel->departments()->attach($cat);
        // }

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
        return route('testimonial.index');
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
