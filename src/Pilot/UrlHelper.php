<?php

namespace Flex360\Pilot\Pilot;

class UrlHelper
{
    public static function getPart($index)
    {
        if (! self::hasPart($index)) {
            return false;
        }

        $parts = self::getParts();

        return strtolower($parts[$index]);
    }

    public static function hasPart($index)
    {
        $parts = self::getParts();

        return isset($parts[$index]);
    }

    public static function isRoot()
    {
        $uri = explode('?', $_SERVER['REQUEST_URI'] ?? '');

        return $uri[0] === '/';
    }

    public static function getParts()
    {
        $uri = explode('?', $_SERVER['REQUEST_URI'] ?? '');

        return explode('/', $uri[0]);
    }

    public static function partIn($index, $parts = [])
    {
        $part = self::getPart($index);

        return array_search($part, $parts) !== false;
    }

    public static function partIs($index, $responses = [], $default)
    {
        $part = self::getPart($index);

        return isset($responses[$part]) ? $responses[$part] : $default;
    }

    public static function getLevel()
    {
        return count(self::getParts()) - 1;
    }

    public static function is($page)
    {
        $uri = explode('?', $_SERVER['REQUEST_URI'] ?? '');

        return fnmatch($page, $uri[0]);
    }

	public static function isNot($page)
	{
		return ! static::is($page);
	}
}
