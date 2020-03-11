<?php

namespace Flex360\Pilot\Pilot;

use Illuminate\Support\Collection;

class PageCollection extends Collection
{

    public function orderBy($field, $order = 'asc')
    {
        return strtolower($order) == 'asc' ? $this->sortBy($field) : $this->sortByDesc($field);
    }
}
