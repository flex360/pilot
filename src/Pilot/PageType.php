<?php

namespace Flex360\Pilot\Pilot;

use Illuminate\Database\Eloquent\Model;

class PageType extends Model
{
    protected $table = 'page_types';

    protected $guarded = array('id', 'created_at', 'updated_at');

    public function __toString()
    {
        return $this->name;
    }

    public function getFingerprint($page = null)
    {
        if (is_null($page)) {
            $page = Page::find($this->page_id);
        }

        if (! is_object($page)) {
            return null;
        }

        $blocks = $page->blocks;

        $indicators = [];

        foreach ($blocks as $block) {
            $indicators[] = [$block->slug, $block->type];
        }

        usort($indicators, function ($a, $b) {
            strcmp($a[0], $b[0]);
        });

        return md5(json_encode($indicators));
        // return json_encode($indicators);
    }

    public function inSyncWith($page)
    {
        return $this->getFingerprint() == $this->getFingerprint($page);
    }

    public static function bySlug($slug)
    {
        return PageType::where('slug', $slug)->first();
    }
}
