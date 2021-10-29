<?php

namespace Flex360\Pilot\Pilot\Traits;

use Flex360\Pilot\Pilot\Site;
use Flex360\Pilot\Scopes\SiteScope;
use Illuminate\Database\Eloquent\Model;

trait SupportsMultipleSites
{
    public static function bootSupportsMultipleSites()
    {
        static::addGlobalScope(new SiteScope);
        
        static::creating(function (Model $model) {
            $model->site_id = Site::getCurrent()->id;
        });
    }
    
    public function scopeBelongsToSite($query, $site = null)
    {
        if (empty($site)) {
            $site = Site::getCurrent();
        }
        return $query->where('site_id', $site->id);
    }
}
