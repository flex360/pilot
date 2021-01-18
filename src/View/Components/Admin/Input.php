<?php

namespace Flex360\Pilot\View\Components\Admin;

use Illuminate\View\Component;

class Input extends Component
{
    public $label;
    public $value;
    public $type;
    public $disabled;
    public $wrapperClass;
    public $options;
    public $multiple;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        string $label,
        $value,
        string $type = 'text',
        bool $disabled = false,
        string $wrapperClass = '',
        array $options = null,
        bool $multiple = false
    ) {
        $this->label = $label;
        $this->value = $value;
        $this->type = $type;
        $this->disabled = $disabled;
        $this->wrapperClass = $wrapperClass;
        $this->options = $options;
        $this->multiple = $multiple;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('pilot::components.admin.input');
    }

    /**
     * Determine if the given option is the current selected option.
     *
     * @param  string  $option
     * @return bool
     */
    public function isSelected($option)
    {
        if (is_array($this->value)) {
            return in_array($option, $this->value);
        } else {
            return $option == $this->value;
        }
    }
}
