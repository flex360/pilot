<?php

namespace Flex360\Pilot\Pilot\Traits;

use Flex360\Pilot\Scopes\PublishedScope;

trait Publishable
{
    public static function bootPublishable()
    {
        static::addGlobalScope(new PublishedScope);

        static::saving(function ($model) {
            if (empty($model->status)) {
                $model->status = 10;
            }
        });
    }
}
