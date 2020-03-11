<?php

namespace Flex360\Pilot\Pilot;

class PostPresenter extends Presenter
{
    public function getTagLinks($separator = ', ')
    {
        $tags = $this->entity->tags;

        $links = $tags->map(function ($tag) {
            return '<a href="' . $tag->url() . '">' . $tag->name . '</a>';
        });

        return implode($separator, $links->toArray());
    }

    public function published_on($format = null)
    {
        $format = empty($format) ? 'n/j/Y g:i a' : $format;

        return date($format, strtotime($this->entity->published_on));
    }
}
