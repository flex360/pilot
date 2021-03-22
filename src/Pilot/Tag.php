<?php

namespace Flex360\Pilot\Pilot;

use Illuminate\Support\Str;
use Flex360\Pilot\Pilot\Post;
use Flex360\Pilot\Pilot\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;

    protected $table = 'tags';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public static function boot()
    {
        parent::boot();

        Tag::saving(function ($tag) {
            //
        });
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, config('pilot.table_prefix') . 'post_tag')
                    ->where('status', 30)
                    ->orderBy('published_on', 'desc');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, config('pilot.table_prefix') . 'event_tag')
                    ->where('status', 30)
                    ->orderBy('published_on', 'desc');
    }

    public function url()
    {
        return route('blog.tagged', ['id' => $this->id, 'slug' => Str::slug($this->name)]);
    }

    public static function getSelectList()
    {
        return static::orderBy('name')
            ->get()
            // ->prepend(['id' => '', 'name' => '[No Tag Selected]'])
            ->pluck('name', 'id');
    }
}
