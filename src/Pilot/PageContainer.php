<?php

namespace Flex360\Pilot\Pilot;

class PageContainer
{

    public $page = null;

    public function __construct(Page $page)
    {
        $this->page = $page;

        foreach ($page->blocks as $block) {
            $this->{$block->slug} = $block->body;
        }
    }

    public function __get($prop)
    {
        return $this->page->$prop;
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->page, $method], $args);
    }
}
