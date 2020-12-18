<?php

namespace Flex360\Pilot\Pilot;

use Flex360\Pilot\Contracts\Post as PostContract;
use Illuminate\Support\Str;
use Flex360\Pilot\Pilot\Traits\HasEmptyStringAttributes;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Flex360\Pilot\Pilot\Traits\UserHtmlTrait;
use Flex360\Pilot\Pilot\Traits\PilotTablePrefix;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Flex360\Pilot\Pilot\Traits\PresentableTrait;
use Flex360\Pilot\Pilot\Traits\SocialMetadataTrait;

class Post extends Model implements HasMedia, PostContract
{
    use PresentableTrait,
        SocialMetadataTrait,
        UserHtmlTrait,
        HasMediaTrait,
        SoftDeletes,
        HasEmptyStringAttributes,
        PilotTablePrefix;

    protected $table = 'posts';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $html = ['body'];

    protected $emptyStrings = [
        'title', 'body', 'summary', 'horizontal_featured_image', 'vertical_featured_image', 'gallery',
        'external_link', 'author'
    ];

    public function getDates()
    {
        return ['created_at', 'updated_at', 'published_on'];
    }

    public static function boot()
    {
        parent::boot();

        Post::saving(function ($post) {
            // $realPost = Post::find($post->id);
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
        return $this->belongsToMany(Tag::class, $this->getPrefix() . 'post_tag');
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
                $newTag = Tag::create(['name' => $tag]);
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

    public function hasImage()
    {
        return !empty($this->image);
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
                'caption' => $item->getCustomProperty('description'),
                'extra' => '',
            ];
        })->toArray();
    }

    public function hasGallery()
    {
        return !empty($this->gallery);
    }

    public static function latest($limit = 10)
    {
        return Post::whereRaw('published_on <= NOW()')
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

    public function registerMediaConversions(Media $media = null)
    {
        // let's always use standard names like thumb, xsmall, small, medium, large, xlarge

        $this->addMediaConversion('thumb')
             ->crop(Manipulations::CROP_TOP_RIGHT, 300, 300);

        $this->addMediaConversion('small')
             ->width(300)
             ->height(300);
    }

    public function getImageAttribute($value)
    {
        $mediaItem = $this->getFirstMedia('image');

        if (!empty($mediaItem)) {
            return $mediaItem->getUrl();
        }

        return $value;
    }
}
