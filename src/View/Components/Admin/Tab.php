<?php

namespace Flex360\Pilot\View\Components\Admin;

use Illuminate\View\Component;

class Tab extends Component
{
    public $pane;
    
    public $active;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $pane, bool $active = false)
    {
        $this->pane = $pane;
        $this->active = $active;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('pilot::components.admin.tab');
    }
}
