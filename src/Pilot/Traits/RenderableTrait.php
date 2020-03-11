<?php

namespace Flex360\Pilot\Pilot\Traits;

trait RenderableTrait
{
    public function render()
    {
        $params = func_get_args();

        $field = array_shift($params);

        // return nothing if the field is empty
        if (empty($this->$field)) {
            return null;
        }

        if (empty($params)) {
            $format = '%s';
        } else {
            $format = array_shift($params);
        }

        array_unshift($params, $this->$field);

        // determine the number of replacements
        $replacementCount = substr_count($format, '%');

        // do we need more variables to match the number fo replacements?
        if (count($params) < $replacementCount) {
            $diff = $replacementCount - count($params);
            for ($i = $diff; $i > 0; $i--) {
                $params[] = $this->$field;
            }
        }

        array_unshift($params, $format);

        return call_user_func_array('sprintf', $params);
    }
}
