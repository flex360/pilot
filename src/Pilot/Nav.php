<?php

namespace Flex360\Pilot\Pilot;

class Nav
{
    protected $items;

    public function __construct()
    {
        $this->items = collect();
    }

    public function __toString()
    {
        return view('pilot::admin.nav.sidebar.menu', ['nav' => $this])->render();
    }

    public static function make()
    {
        return new static;
    }

    public static function create(...$items)
    {
        return static::make()->add(...$items);
    }

    public function add(...$items)
    {
        foreach ($items as $item) {
            $this->items->push($item);
        }
        return $this;
    }

    public function items()
    {
        return $this->items->filter(function ($item) {
            return ! $item->hidden;
        });
    }
}
