<?php

namespace Flex360\Pilot\Pilot;

use Illuminate\Support\Str;
use Spatie\Image\Manipulations;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Flex360\Pilot\Scopes\PublishedScope;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Flex360\Pilot\Pilot\Traits\UserHtmlTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Flex360\Pilot\Pilot\Traits\PilotTablePrefix;
use Flex360\Pilot\Pilot\Traits\PresentableTrait;
use Flex360\Pilot\Pilot\Traits\PilotModuleCommon;
use Flex360\Pilot\Pilot\Traits\HasMediaAttributes;
use Flex360\Pilot\Facades\Product as ProductFacade;
use Flex360\Pilot\Pilot\Traits\SocialMetadataTrait;
use Flex360\Pilot\Pilot\Traits\SupportsMultipleSites;
use Flex360\Pilot\Pilot\Traits\HasEmptyStringAttributes;
use Flex360\Pilot\Facades\ProductCategory as ProductCategoryFacade;
use Flex360\Pilot\Facades\ProjectCategory as ProjectCategoryFacade;
use Flex360\Pilot\Pilot\Traits\Publishable;

class ProductCategory extends Model implements HasMedia
{
    use PresentableTrait, HasMediaTrait,
        SoftDeletes, HasMediaAttributes,
        SocialMetadataTrait, UserHtmlTrait,
        HasEmptyStringAttributes, PilotTablePrefix,
        SupportsMultipleSites, PilotModuleCommon, Publishable {
        HasMediaAttributes::registerMediaConversions insteadof HasMediaTrait;
    }

    protected $table = 'product_categories';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $emptyStrings = [
        'title', 'short_description', 'featured_image',
    ];

    protected $mediaAttributes = ['featured_image'];

    public function products()
    {
        if (config('pilot.plugins.products.children.manage_product_categories.fields.product_sort_method') == 'manual_sort') {
            return $this->belongsToMany(root_class(ProductFacade::class), $this->getPrefix() . 'product_' . config('pilot.table_prefix') . 'product_category')
                        ->withPivot('position')
                        ->orderBy(config('pilot.table_prefix') . 'product_' . config('pilot.table_prefix') . 'product_category.position');
        } else {
            return $this->belongsToMany(root_class(ProductFacade::class), $this->getPrefix() . 'product_' . config('pilot.table_prefix') . 'product_category')
                        ->orderBy('name');
        }
    }

    public function duplicate()
    {
        $model = $this;

        $newModel = $model->replicate();

        // append to the title to designate a copy
        $newModel->title .= ' (Copy)';
        $newModel->status = 10;

        // copy media items
        foreach ($model->media as $media) {
            $media->copyTo($newModel);
        }

        $newModel->push();

        // copy all attached categories over to new model
        foreach ($model->products()->withoutGlobalScope(PublishedScope::class)->get() as $product) {
            $newModel->products()->withoutGlobalScope(PublishedScope::class)->attach($product);
        }

        return $newModel;
    }

    public static function getStatuses()
    {
        return array(
            10 => 'Draft',
            30 => 'Published'
        );
    }

    public function getStatus()
    {
        $status = ProjectCategoryFacade::getStatuses();

        return (object) array(
            'id' => $this->status,
            'name' => $status[$this->status],
        );
    }

    public function url()
    {
        return route('productCategory.index', [
            'id' => $this->id,
            'slug' => $this->getSlug(),
        ]);
    }

    public function getUrlAttribute($value)
    {
        return $this->url();
    }

    public function getSlug()
    {
        return Str::slug($this->title);
    }
}
