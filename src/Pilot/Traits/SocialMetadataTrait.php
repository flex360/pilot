<?php

namespace Flex360\Pilot\Pilot\Traits;

use Illuminate\Support\Str;
use Flex360\Pilot\Pilot\Site;

trait SocialMetadataTrait
{
    public function getOpenGraphDescription($limit = 200)
    {
        return empty($this->summary) ? Str::limit(strip_tags($this->body), $limit) : $this->summary;
    }

    public function getTwitterDescription($limit = 100)
    {
        return empty($this->summary) ? Str::limit(strip_tags($this->body), $limit) : $this->summary;
    }

    public function getTwitterCardType()
    {
        if ($this->hasSocialImage() && $this->isSocialImageLandscape()) {
            return 'summary_large_image';
        }

        return 'summary';
    }

    public function hasSocialImage()
    {
        return !empty($this->image);
    }

    public function getSocialImage()
    {
        if (substr($this->image, 0, 4) == 'http') {
            return $this->image;
        }

        return 'http://' . Site::getCurrent()->getDefaultDomain() . $this->image;
    }

    public function getSocialImageWidth()
    {
        $dimensions = $this->getSocialImageDimensions();

        return $dimensions[0];
    }

    public function getSocialImageHeight()
    {
        $dimensions = $this->getSocialImageDimensions();

        return $dimensions[1];
    }

    public function isSocialImageLandscape()
    {
        return $this->getSocialImageWidth() > $this->getSocialImageHeight();
    }

    public function getSocialImageDimensions()
    {
        if (isset($this->socialImageDimensions)) {
            return $this->socialImageDimensions;
        } else {
            if (file_exists($this->getSocialImage())) {
                $this->attributes['socialImageDimensions'] = getimagesize($this->getSocialImage());
            } else {
                $this->attributes['socialImageDimensions'] = [0 => null, 1 => null];
            }
        }

        return $this->socialImageDimensions;
    }
}
