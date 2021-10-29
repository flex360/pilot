<?php

namespace Flex360\Pilot\Pilot\Traits;

trait HasTablePrefix
{
    public function getTable()
    {
        $table = parent::getTable();

        // fix for weird bug when using "Where Not" package
        if (substr($table, 0, 10) == 'where_not_') {
            return $table;
        }
        
        return $this->getPrefix() . $table;
    }

    public function setTable($table)
    {
       
        $prefix = $this->getPrefix();
        $prefixLength = strlen($prefix);
        
        if (substr($table, 0, $prefixLength) == $prefix) {
            $table = substr($table, $prefixLength);
        }

        $this->table = $table;

        return $this;
    }

    public function getPrefix()
    {
        return is_null($this->prefix) ? '' : $this->prefix;
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }
}
