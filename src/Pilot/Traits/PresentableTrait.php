<?php

namespace Flex360\Pilot\Pilot\Traits;

trait PresentableTrait
{

    /**
     * Create a presenter for this page
     *
     * @return Presenter
     */
    public function present()
    {
        $className = get_class($this);

        $presenterClass = $className . 'Presenter';

        return new $presenterClass($this);
    }
}
