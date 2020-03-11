<?php

namespace Flex360\Pilot\Pilot\Helpers;

class Drop
{
    public $data = null;
    public $relationships = null;

    public function __construct($mixed, $relationships = [])
    {
        $this->data = $mixed;
        $this->relationships = $relationships;
    }

    public static function make($mixed, $relationships = [])
    {
        return new static($mixed, $relationships);
    }

    public function __toString()
    {
        return $this->html();
    }

    public function html()
    {
        $html = '';

        $dataClass = get_class($this->data);

        if (! in_array($dataClass, ['Illuminate\Database\Eloquent\Collection', 'Illuminate\Support\Collection'])) {
            $this->data = collect()->push($this->data);
        }

        $html .= $this->data->transform(function ($item) {
            $itemHtml = '';

            $name = $item->{$this->getNameField($item->getAttributes())};

            if (method_exists($item, 'url')) {
                $itemHtml .= "<h2><a href=\"{$item->url()}\">$name</a></h2>";
            } else {
                $itemHtml .= "<h2>$name</h2>";
            }

            foreach ($this->relationships as $relationship) {
                $item->$relationship != null ?
                    $item->setAttribute($relationship, $item->$relationship->toArray()) :
                    $item->setAttribute($relationship, null);
            }

            $itemHtml .= '<pre>' . print_r($item->getAttributes(), true) . '</pre>';

            return $itemHtml;
        })->implode('');

        return $html;
    }

    public function getNameField($attributes = [])
    {
        $keys = array_keys($attributes);

        return isset($keys[1]) ? $keys[1] : 'name';
    }
}
