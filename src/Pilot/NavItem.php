<?php

namespace Flex360\Pilot\Pilot;

class NavItem
{
    public $name;
    public $url;
    public $children;
    public $parent;
    public $activeRoutes;
    public $routePattern;

    public function __construct($name, $url, $routePattern = null)
    {
        $this->name = $name;
        $this->url = $url;
        $this->routePattern = $routePattern;
        $this->children = collect();
        $this->parent = null;
        $this->activeRoutes = collect();
    }

    public static function make($name, $url, $routePattern = null)
    {
        return new static($name, $url, $routePattern);
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
        $classes = [];

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
}
