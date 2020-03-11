<?php

namespace Flex360\Pilot\Pilot\Traits;

trait HasEmptyStringAttributes
{
    public static function bootHasEmptyStringAttributes()
    {
        static::saving(function ($model) {
            $model->fixEmptyStringAttributes();
        });
    }

    public function fixEmptyStringAttributes()
    {
        foreach ($this->emptyStrings as $emptyStringAttributeName) {
            $value = isset($this->attributes[$emptyStringAttributeName])
                ? $this->attributes[$emptyStringAttributeName]
                : '';
            $this->attributes[$emptyStringAttributeName] = empty($value)
                ? ''
                : $value;
        }
    }

    public function getEmtpyStringsAttribute($value)
    {
        return [];
    }
}
