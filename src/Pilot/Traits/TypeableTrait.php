<?php

namespace Flex360\Pilot\Pilot\Traits;

use Flex360\Pilot\Pilot\PageType;
use Flex360\Pilot\Pilot\Block;
use Flex360\Pilot\Pilot\PageContainer;
use Flex360\Pilot\Pilot\Page;
use Flex360\Pilot\Pilot\PageCollection;

trait TypeableTrait
{
    public function getBlock($slug)
    {
        $block = Block::where('page_id', $this->id)
                    ->where('slug', $slug)
                    ->first();

        if (empty($block)) {
            $block = new Block;
        }

        return $block;
    }

    public function updateBlocks($data = [], $order = [], $settings = [])
    {
        foreach ($data as $id => $body) {
            $block = Block::find($id);

            $block->body = $body;

            $block->settings = isset($settings[$id]) ? $settings[$id] : [];

            // update sort order if supplied
            if (! empty($order) && in_array($id, $order)) {
                $block->position = array_search($id, $order);
            }

            $block->save();
        }
    }

    public function hasBlocks()
    {
        return $this->blocks->isEmpty() ? false : true;
    }

    public function hasBlock($slug)
    {
        return $this->getBlock($slug)->exists;
    }

    public function hasPopulatedBlock($slug)
    {
        return $this->hasBlock($slug) && ! $this->getBlock($slug)->isEmpty();
    }

    public function isType($slug)
    {
        if (! isset($this->type->slug)) {
            return false;
        }

        return $this->type->slug == $slug;
    }

    public function changeType($page_type_id)
    {
        // dd([$page_type_id, $this->type_id]);

        // check to see if the type has really changed
        if ($page_type_id == $this->type_id && $this->hasBlocks()) {
            return false;
        }

        $newType = PageType::find($page_type_id);

        // dd([$this->type->getFingerprint($this), $newType->getFingerprint()]);

        if (is_null($this->type) || is_null($newType)) {
            return false;
        }

        // check again to make sure the blocks actaully match
        if ($this->type->getFingerprint($this) == $newType->getFingerprint()) {
            return false;
        }

        $blocks = Block::where('page_id', $newType->page_id)->orderBy('position')->get();

        foreach ($blocks as $block) {
            $newBlock = $block->replicate();
            $newBlock->page_id = $this->id;
            $newBlock->body = '';
            $newBlock->save();
        }

        return true;
    }

    public function hasType()
    {
        return is_object($this->type);
    }

    public static function byType($slug)
    {
        $type = PageType::bySlug($slug);

        $pages = Page::where('type_id', $type->id)->get();

        // dd($pages);

        $collection = new PageCollection;

        foreach ($pages as $page) {
            $collection->push(new PageContainer($page));
        }

        return $collection;
    }
}
