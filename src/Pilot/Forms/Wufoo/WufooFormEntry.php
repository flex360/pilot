<?php

namespace Flex360\Pilot\Pilot\Forms\Wufoo;

use Illuminate\Database\Eloquent\Model;

class WufooFormEntry extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

        static::saving(function ($entry) {
            // check for url fields sent by webhooks
            foreach ($entry->attributes as $key => $value) {
                if (isset($entry->attributes[$key.'-url'])) {
                    $entry->attributes[$key] .= ' (' . $entry->attributes[$key.'-url'] . ')';
                    unset($entry->attributes[$key.'-url']);
                }
            }
        });
    }

    public function getValue($name)
    {
        $value = $this->$name;

        // detect and modify file uploads
        if (strpos($value, '.wufoo.com/cabinet/') !== false) {
            $value = trim($value, ')');
            $parts = explode(' (', $value);
            $value = '<a href="' . $parts[1] . '" target="_blank">' . $parts[0] . '</a>';
        }

        return $value;
    }
}
