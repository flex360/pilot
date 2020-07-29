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
    private $id;

    public function __construct($name, $url, $routePattern = null)
    {
        $this->name = $name;
        $this->url = $url;
        $this->routePattern = $routePattern;
        $this->children = collect();
        $this->parent = null;
        $this->activeRoutes = collect();
        $this->id = uniqid(Str::slug($this->name) . '-', false);
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
        $classes = ['pilot-nav__item'];

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
