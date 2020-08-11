<?php

namespace Flex360\Pilot\Pilot;

use Flex360\Pilot\Pilot\Traits\HasEmptyStringAttributes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends \Eloquent
{
    use SoftDeletes, HasEmptyStringAttributes;

    protected $table = 'settings';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $emptyStrings = ['value'];

    // if no settings exist, these initial settings will be created
    public static $initialSettings = ['Google Analytics', 'Tracking Code'];

    public static function boot()
    {
        parent::boot();

        self::saving(function ($setting) {
            // set site for page
            $site = Site::getCurrent();
            $setting->site_id = isset($site->id) ? $site->id : null;

            // set key if empty
            if (empty($setting->key)) {
                $setting->key = Str::slug($setting->name);
            }

            Cache::forget('settings');
        });
    }

    public static function get($key, $default = null)
    {
        $site = Site::getCurrent();

        $settings = Cache::rememberForever('settings', function () {
            return self::whereRaw('1=1')->get();
        });

        $keyParts = explode('.', $key);

        $setting = $settings->where('key', $keyParts[0])->where('site_id', $site->id)->first();

        $value = empty($setting) ? $default : $setting->value;

        if (empty($value)) {
            if (isset($keyParts[1])) {
                $fieldValue = is_object($setting) ? $setting->getFieldValueById($keyParts[1]) : null;
                return empty($fieldValue) ? $default : $fieldValue;
            } else {
                return empty($setting->fields) ? null : new SettingFieldCollection($setting->fields);
            }
        }

        return $value;
    }

    public static function has($key)
    {
        $site = Site::getCurrent();

        $settings = Cache::rememberForever('settings', function () {
            return self::whereRaw('1=1')->get();
        });

        $setting = $settings->where('key', $key)->where('site_id', $site->id)->first();

        return !empty($setting) && (!empty($setting->value) || !empty($setting->fields));
    }

    public static function getAll()
    {
        $site = Site::getCurrent();
        $settings = self::where('site_id', '=', $site->id)->get();

        return $settings;
    }

    public static function init()
    {
        $site = Site::getCurrent();
        $settingsCount = self::getAll()->count();

        if ($settingsCount == 0 && !empty($site->id)) {
            $initial = self::$initialSettings;

            foreach ($initial as $label) {
                self::create([
                    'site_id' => $site->id,
                    'name' => $label,
                    'key' => Str::slug($label),
                ]);
            }
        }
    }

    public function getFieldsAttribute($value)
    {
        // if the setting value contains json
        if (substr($value, 0, 1) == '{') {
            return json_decode($value);
        }

        return $value;
    }

    public function getFieldValueById($id)
    {
        $fields = $this->fields;

        if (isset($fields->$id)) {
            return $fields->$id;
        }

        return null;
    }

    public static function getSelectListWYSIWYG()
    {
        return [
            10 => 'Froala',
            20 => 'Trumbow',
        ];
    }
}
