<?php

namespace Flex360\Pilot\Pilot\Traits;

trait PilotTablePrefix
{
    use HasTablePrefix;

    // protected $prefix = 'pilot_';

    public function getPrefix()
    {
        return config('pilot.table_prefix', '');
    }
}
