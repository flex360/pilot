<?php

namespace Flex360\Pilot\Pilot;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Flex360\Pilot\Pilot\Traits\HasEmptyStringAttributes;
use Flex360\Pilot\Pilot\Traits\SupportsMultipleSites;

class Menu extends Model
{
    // use App\Pilot\Crud\CrudableTrait;
    use HasEmptyStringAttributes, SupportsMultipleSites;

    protected $table = 'menus';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $emptyStrings = ['flex_data', 'items'];

    public function __toString()
    {
        return $this->html();
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($menu) {
            if (empty($menu->slug)) {
                $menu->slug = Str::slug($menu->name);
            }
        });

        static::saved(function ($menu) {
            Cache::forget('pilot-menu-' . $menu->slug);
        });

        static::deleted(function ($menu) {
            Cache::forget('pilot-menu-' . $menu->slug);
        });
    }

    public function pages()
    {
        return $this->belongsToMany('Page')->withPivot('position')->orderBy('pivot_position');
    }

    public static function findBySlug($slug)
    {
        $menu = static::where('slug', $slug)->first();

        if (empty($menu)) {
            $menu = new Menu;
            $menu->slug = $slug;
            return $menu;
        }

        return $menu;
    }

    public function html()
    {
        $menu = $this;

        return view('pilot::partials.menus.default', compact('menu'))->render();
    }

    /**
     * Reorder pages based on order of page ids passed
     *
     * @param Array $ids
     */
    public function reorder($ids)
    {
        foreach ($ids as $position => $id) {
            DB::table('menu_page')
                    ->where('menu_id', '=', $this->id)
                    ->where('page_id', '=', $id)
                    ->update(['position' => $position]);
        }
    }

    public function getMenuItems()
    {
        if (!$this->exists) {
            throw new Exception('Menu with slug \'' . $this->slug . '\' does not exist.');
        }

        $items = json_decode($this->items);

        return MenuItem::hydrate($items);
    }

    public function items()
    {
        return $this->getMenuItems();
    }

    public function tree()
    {
        return $this->getMenuItems()->toTree();
    }
}
