<?php

namespace Flex360\Pilot\Pilot\Traits;

trait HasTablePrefix
{
    public function getTable()
    {
        return $this->getPrefix() . parent::getTable();
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