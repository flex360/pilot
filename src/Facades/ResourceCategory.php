<?php

namespace Flex360\Pilot\Facades;

use Illuminate\Support\Facades\Facade;

class ResourceCategory extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Flex360\Pilot\Pilot\ResourceCategory';
    }
}
