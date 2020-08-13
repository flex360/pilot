<?php

namespace Flex360\Pilot\Pilot;

class MenuItem
{
    public $children = null;

    public function __construct()
    {
        $this->children = new MenuItemCollection();
    }

    public static function hydrate($data = [])
    {
        $hydrated = new MenuItemCollection();

        foreach ($data as $item) {
            $hydrated->push(static::fromArray($item));
        }

        return $hydrated;
    }

    public static function fromArray($data)
    {
        $new = new static;

        foreach ($data as $key => $value) {
            $new->$key = $value;
        }

        if (!empty($new->page)) {
            $page = Page::find($new->page);
            $new->url = $page->path;
            if (empty($new->title)) {
                $new->title = $page->title;
            }
        }

        return $new;
    }

    public function hasChildren()
    {
        return $this->children->isNotEmpty();
    }

    public function descendants(&$carry = null)
    {
        $descendants = $carry ?: new MenuItemCollection();

        $descendants->push(...$this->children);

        foreach ($this->children as $child) {
            $child->descendants($descendants);
        }

        return $descendants;
    }
}
