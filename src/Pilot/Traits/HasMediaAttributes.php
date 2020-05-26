<?php

namespace Flex360\Pilot\Pilot\Traits;

use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\Models\Media;

trait HasMediaAttributes
{
    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        // handle model that doesn't have $media defined
        if ($key == 'mediaAttributes') {
            return [];
        }

        // split up key and size suffix
        $keyParts = explode('__', $key);

        // if key is specified as a media attribute
        if (in_array($keyParts[0], $this->mediaAttributes)) {
            $mediaItem = $this->getFirstMedia($keyParts[0]);

            if (!empty($mediaItem)) {
                return $mediaItem->getUrl(isset($keyParts[1]) ? $keyParts[1] : '');
            }
        }

        return $this->getAttribute($key);
    }

    /**
     * Determine if an attribute or relation exists on the model.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        if (isset($this->mediaAttributes) && in_array($key, $this->mediaAttributes)) {
            return true;
        }
        return $this->offsetExists($key);
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
}
