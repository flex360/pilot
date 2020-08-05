<?php

namespace Flex360\Pilot\Pilot;

use Illuminate\Support\Str;

class NavItem
{
    public $name;
    public $url;
    public $children;
    public $parent;
    public $activeRoutes;
    public $routePattern;
    public $sidebarPosition;
    public $linkTarget;
    private $id;

    public function __construct($name, $url, $routePattern = null, $sidebarPosition = null, $linkTarget = "_self")
    {
        $this->name = $name;
        $this->url = $url;
        $this->routePattern = $routePattern;
        $this->sidebarPosition = $sidebarPosition;
        $this->linkTarget = $linkTarget;
        $this->children = collect();
        $this->parent = null;
        $this->activeRoutes = collect();
        $this->id = uniqid(Str::slug($this->name) . '-', false);
    }

    public static function make($name, $url, $routePattern = null, $sidebarPosition = null, $linkTarget = "_self")
    {
        return new static($name, $url, $routePattern, $sidebarPosition, $linkTarget);
    }

    public function addChildren(...$children)
    {
        foreach ($children as $child) {
            $child->parent = $this;
            $this->children->push($child);
        }
        return $this;
    }

    public function addActiveRoutes(...$routes)
    {
        foreach ($routes as $route) {
            $this->activeRoutes->push($route);
        }
        return $this;
    }

    public function getCssClasses()
    {
        if ($this->sidebarPosition != null) {
            $classes = ['pilot-nav__item secondhalf sidebar list-group-item list-group-item-action'];
        } else {
            $classes = ['pilot-nav__item sidebar list-group-item list-group-item-action'];
        }

        if ($this->isActive()) {
            $classes[] = 'pilot-nav__item--active';
        }

        if ($this->hasActiveChild()) {
            $classes[] = 'pilot-nav__item--active-child';
        }

        return implode(' ', $classes);
    }

    public function isActive()
    {
        return request()->fullUrlIs($this->url)
                || $this->activeRoutes->contains(request()->route()->getName())
                || ($this->routePattern !== null && fnmatch($this->routePattern, request()->route()->getName()));
    }

    public function hasActiveChild()
    {
        return $this->children->filter->isActive()->count();
    }

    public function hasChildren()
    {
        return $this->children->isNotEmpty();
    }

    public function id()
    {
        return $this->id;
    }
}
