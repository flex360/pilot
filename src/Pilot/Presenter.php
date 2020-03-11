<?php

namespace Flex360\Pilot\Pilot;

class Presenter
{
    public $entity;

    public function __construct($entity)
    {
        $this->entity = $entity;
    }
}
