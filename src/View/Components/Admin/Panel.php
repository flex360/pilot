<?php

namespace Flex360\Pilot\View\Components\Admin;

use Illuminate\View\Component;

class Panel extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('pilot::components.admin.panel');
    }
}
