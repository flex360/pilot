<?php

namespace Flex360\Pilot\Pilot\Traits;

use Flex360\Pilot\Pilot\Site;
use Flex360\Pilot\Scopes\SiteScope;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

trait PilotModuleCommon
{
    public static function bootPilotModuleCommon()
    {
        static::saved(function (Model $model) {
            // clear cached data
        });
    }
    
    public function getDraftCount()
    {
        return $this->withoutGlobalScopes()
            ->draft()
            ->where('deleted_at', null)
            ->orderBy('published_on', 'desc')
            ->belongsToSite()
            ->count();
    }

    public function scopeApplySearchFilters($query)
    {
        return $this->filter($query);
    }

    public function cacheKey($baseKey)
    {
        return $baseKey . '_' . $this->getTable() . '_site_' . Site::getCurrent()->id;
    }

    public function scopePilotIndex($query, $pageSize = null)
    {
        $query->withoutGlobalScopes()
            ->where('deleted_at', null)
            ->orderBy('published_on', 'desc')
            ->belongsToSite()
            ->applySearchFilters();
        
        if (!is_null($pageSize)) {
            return $query->paginate($pageSize);
        }

        return $query;
    }
}
