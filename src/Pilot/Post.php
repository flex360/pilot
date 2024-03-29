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
use Flex360\Pilot\Contracts\Post as PostContract;
use Flex360\Pilot\Pilot\Traits\PilotModuleCommon;
use Flex360\Pilot\Pilot\Traits\HasMediaAttributes;
use Flex360\Pilot\Pilot\Traits\SocialMetadataTrait;
use Flex360\Pilot\Pilot\Traits\SupportsMultipleSites;
use Flex360\Pilot\Pilot\Traits\HasEmptyStringAttributes;
use Flex360\Pilot\Facades\Post as PostFacade;
use Flex360\Pilot\Facades\Tag as TagFacade;

class Post extends Model implements HasMedia, PostContract
{
    use PresentableTrait, HasMediaTrait,
        SoftDeletes, HasMediaAttributes,
        SocialMetadataTrait, UserHtmlTrait,
        HasEmptyStringAttributes, PilotTablePrefix,
        SupportsMultipleSites, PilotModuleCommon  {
        HasMediaAttributes::registerMediaConversions insteadof HasMediaTrait;
    }

    protected $table = 'posts';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $html = ['body'];

    protected $emptyStrings = [
        'title', 'body', 'summary', 'horizontal_featured_image', 'vertical_featured_image', 'gallery',
        'external_link', 'author', 'fi_background_color'
    ];

    protected $mediaAttributes = ['horizontal_featured_image', 'vertical_featured_image'];

    public function getDates()
    {
        return ['created_at', 'updated_at', 'published_on'];
    }

    public static function boot()
    {
        parent::boot();

        PostFacade::saving(function ($post) {
            // $realPost = PostFacade::find($post->id);
            //
            // // reformat published date
            // $post->published_on = date('Y-m-d H:i:s', strtotime($post->published_on));
            //
            // if($realPost != null) {
            //     if (empty($post->slug) or $post->title =! $realPost->title) {
            //         $post->slug = Str::slug($post->title);
            //     }
            // }
        });
    }

    public function scopePublished($query)
    {
        return $query->where('status', 30)
                    ->whereRaw('published_on <= NOW()');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 30)
                    ->whereRaw('published_on > NOW()');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 10);
    }

    public function tags()
    {
        return $this->belongsToMany(root_class(TagFacade::class), $this->getPrefix() . 'post_tag');
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
        foreach ($model->tags as $tag) {
            $newModel->tags()->attach($tag);
        }

        return $newModel;
    }

    /**
     * Gets summary of body using helper function
     *
     * @return string
     */
    public function getSummary()
    {
        if ($this->summary != null) {
            return str_limit($this->summary, 255, '...');
        } else {
            //stripe out html tags except links
            $newBody = strip_tags($this->body);
            return str_limit($newBody, 255, '...');
        }
    }

    /**
     * Gets summary of for backend view
     *
     * @return string
     */
    public function getBackendSummary()
    {
        if ($this->summary != null) {
            return str_limit($this->summary, 255, '...');
        } else {
            return '';
        }
    }

    /**
     * Formats the published on date when it is accessed
     * @param string $value
     * @return string
     */
    public function getPublishedOnAttribute($value)
    {
        return \Carbon\Carbon::createFromTimeStamp(strtotime($value));
    }

    /**
     * Converts the published on date when set
     * @param string $value
     */
    public function setPublishedOnAttribute($value)
    {
        $this->attributes['published_on'] = date('Y-m-d H:i:s', strtotime($value));
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
        $status = self::getStatuses();

        return (object) [
            'id' => $this->status,
            'name' => $status[$this->status],
        ];
    }

    public function addTags($tags)
    {
        // convert an non numeric tags into real tags
        foreach ($tags as $index => $tag) {
            if (!is_numeric($tag)) {
                $newTag = TagFacade::create(['name' => $tag]);
                $tags[$index] = $newTag->id;
            }
        }

        $this->tags()->sync($tags);
    }

    public function link()
    {
        return '<a href="' . $this->url() . '">' . $this->title . '</a>';
    }

    public function url()
    {
        $title = $this->title ?: 'New Post';

        return route('blog.post', [
            'id' => $this->id,
            'slug' => empty($this->slug) ? Str::slug($title) : $this->slug
        ]);
    }

    public function hasHorizontalImage()
    {
        return !empty($this->horizontal_featured_image);
    }

    public function hasVerticalImage()
    {
        return !empty($this->vertical_featured_image);
    }

    public function hasImage()
    {
        return $this->hasHorizontalImage() || $this->hasVerticalImage();
    }

    public function getImageBackgroundColor()
    {
        return $this->fi_background_color;
    }

    public function getGalleryAttribute($value)
    {
        $mediaItems = $this->getMedia('gallery');

        if ($mediaItems->isEmpty()) {
            $array = unserialize($value);

            return is_array($array) ? $array : [];
        }

        return $mediaItems->transform(function ($item, $key) {
            return [
                'path' => $item->getUrl(),
                'title' => $item->getCustomProperty('title'),
                'credit' => $item->getCustomProperty('credit'),
                'description' => $item->getCustomProperty('description'),
            ];
        })->toArray();
    }

    public function hasGallery()
    {
        return !empty($this->gallery);
    }

    public static function latest($limit = 10)
    {
        return PostFacade::whereRaw('published_on <= NOW()')
                    ->where('status', 30)
                    ->orderBy('published_on', 'desc')
                    ->limit($limit)
                    ->get();
    }

    public function mimicPage()
    {
        Page::mimic([
            'title' => $this->title
        ]);

        return $this;
    }

    public static function filter($query, $data = [])
    {
        if (empty($data)) {
            $data = request()->all();
        }

        // filter by keyword
        if (isset($data['keyword'])) {
            $query = $query->where('title', 'like', '%' . $data['keyword'] . '%');
        }

        return $query;
    }

    public function scopeOrderBySticky($query)
    {
        return $query->orderBy('sticky', 'desc');
    }

    public function scopeSticky($query)
    {
        return $query->where('sticky', 1);
    }
}
