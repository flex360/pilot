<?php

namespace Flex360\Pilot\Pilot;

use Illuminate\Support\Collection;

class MenuItemCollection extends Collection
{
    public function toTree()
    {
        $this->addIndexes();

        for ($level = $this->getMaxLevel() - 1; $level >= 1; $level--) {
            foreach ($this->getItemsByLevel($level) as $index => $item) {
                $children = $this->getChildrenOf($item);
                $this->pruneChildrenOf($item);
                $item->children = $children;
                $this[$index] = $item;
            }
        }

        return $this;
    }

    public function getMaxLevel()
    {
        return $this->max('level');
    }

    public function addIndexes()
    {
        foreach ($this as $index => $item) {
            $item->index = $index;
        }

        return $this;
    }

    public function getChildrenOf($parent)
    {
        $children = collect();

        $childLevel = $parent->level + 1;

        foreach ($this as $index => $item) {
            if ($item->index <= $parent->index) {
                // skip items parent and above
                continue;
            }

            if ($item->level > $childLevel) {
                // skip children of children
                continue;
            }

            if ($item->level <= $parent->level) {
                // leave the loop when the level decreases
                break;
            }

            $children->push($item);
        }

        return $children;
    }

    public function pruneChildrenOf($parent)
    {
        $childrenHashes = $this->getChildrenOf($parent)->transform(function ($item) {
            return spl_object_hash($item);
        })->toArray();

        // return $this->reject(function ($item, $index) use ($childrenHashes) {
        //     return in_array(spl_object_hash($item), $childrenHashes);
        // });

        foreach ($this as $index => $item) {
            if (in_array(spl_object_hash($item), $childrenHashes)) {
                $this->forget($index);
            }
        }
    }

    public function getItemsByLevel($level)
    {
        return $this->where('level', $level);
    }
}
