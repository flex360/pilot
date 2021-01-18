<?php

namespace Flex360\Pilot\View\Components\Admin;

use Illuminate\View\Component;

class TabPane extends Component
{
    public $tab;
    
    public $active;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $tab, bool $active = false)
    {
        $this->tab = $tab;
        $this->active = $active;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('pilot::components.admin.tab-pane');
    }
}
