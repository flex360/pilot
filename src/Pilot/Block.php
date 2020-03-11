<?php

namespace Flex360\Pilot\Pilot;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $guarded = array('id', 'created_at', 'updated_at');

    public function __toString()
    {
        return $this->body;
    }

    /**
     * Decodes the JSON string stored in settings property
     * @param string $value
     * @return array
     */
    public function getSettingsAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * Converts the settings property to a JSON string when set
     * @param array $value
     */
    public function setSettingsAttribute($value)
    {
        $this->attributes['settings'] = json_encode($value);
    }

    /**
     * Get setting from setting store
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public function getSetting($key, $default = null)
    {
        $value = isset($this->settings[$key]) ? $this->settings[$key] : null;

        if (empty($value)) {
            return $default;
        } else {
            return $value;
        }
    }

    public function isEmpty()
    {
        return empty($this->body);
    }

    public function fileExists()
    {
        return file_exists(public_path($this->body));
    }
}
