<?php

namespace Flex360\Pilot\View\Components;

use Illuminate\View\Component;

class Img extends Component
{
    public $loading;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($loading = 'eager')
    {
        $this->loading = $loading;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('pilot::components.img');
    }
}
