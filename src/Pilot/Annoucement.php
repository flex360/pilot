<?php

namespace Flex360\Pilot\Pilot;

use Spatie\Image\Manipulations;
use Flex360\Pilot\Pilot\UrlHelper;
use Spatie\MediaLibrary\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Flex360\Pilot\Pilot\Traits\HasEmptyStringAttributes;

class Annoucement extends Model
{
    use SoftDeletes, HasEmptyStringAttributes;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $emptyStrings = ['headline', 'short_description', 'button_text', 'button_link'];

    public static function boot()
    {
        parent::boot();

        Annoucement::saving(function ($annoucement) {
            // if Annoucement is being activated
            if ($annoucement->status == "1" && $annoucement->getOriginal('status') == 0) {
                Annoucement::whereRaw('1=1')->update([
                    'status' => 0
                ]);
            }
        });
    }

    public function duplicate()
    {
        $model = $this;

        $newModel = $model->replicate();

        // append to the headline to designate a copy
        $newModel->headline .= ' (Copy)';

        // make new copy a draft
        $newModel->status = false;

        $newModel->push();

        return $newModel;
    }

    public static function hasActive()
    {
        return static::where('status', 1)->count() > 0;
    }

    public static function getActive()
    {
        return static::where('status', 1)->first();
    }

    public static function shouldShow()
    {
        $annoucement = static::getActive();

        return ! empty($annoucement) && UrlHelper::isNot($annoucement->button_link) ? $annoucement : false;
    }
}
