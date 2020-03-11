<?php

namespace Flex360\Pilot\Pilot\Traits;

use Flex360\Pilot\Pilot\Site;

trait UserHtmlTrait
{
    /**
     * Get an attribute from the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        if (!Site::isBackend() && isset($this->html) && in_array($key, $this->html) !== false &&
        array_key_exists($key, $this->attributes)) {
            $this->attributes[$key] = '<div class="user-html">' . $this->attributes[$key] . '</div>';
        }

        if (array_key_exists($key, $this->attributes) || $this->hasGetMutator($key)) {
            return $this->getAttributeValue($key);
        }

        return $this->getRelationValue($key);
    }

    public function getUserHtml($key)
    {
        return '<div class="user-html">' . $this->$key . '</div>';
    }
}
