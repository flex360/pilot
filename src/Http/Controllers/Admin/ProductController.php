<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use App\Http\Requests;
use Jzpeepz\Dynamo\Dynamo;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Jzpeepz\Dynamo\IndexTab;
use Jzpeepz\Dynamo\FieldGroup;
use App\Http\Controllers\Controller;
use Flex360\Pilot\Scopes\PublishedScope;
use Flex360\Pilot\Facades\Product as ProductFacade;
use Jzpeepz\Dynamo\Http\Controllers\DynamoController;
use Flex360\Pilot\Facades\ProductCategory as ProductCategoryFacade;

class ProductController extends DynamoController
{
    public function getDynamo()
    {
        $dynamo = Dynamo::make(get_class(ProductFacade::getFacadeRoot()));
                    // check if display_name is used, if so, use the dynamo alias function to change the name everywhere at once
                    if (config('pilot.plugins.products.display_name') != null) {
                        $dynamo->alias(Str::singular(config('pilot.plugins.products.display_name')));
                    }


                    /************************************************************************************
                     *  Pilot plugin: Product form view                                                *
                     *  Check the plugins 'fields' array and attach the fields to the dynamo object    *
                     ************************************************************************************/
                    if (config('pilot.plugins.products.fields.categories', true)) {
                        $dynamo->addIndexButton(function() {
                            return '<a href="/pilot/productcategory" class="btn btn-primary btn-sm">Product Categories</a>';
                        });
                    }
                    
                    $dynamo->addIndexButton(function () {
                        return '<a href="'. route('product.index') . '" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> View Products</a>';
                    });
                    if (config('pilot.plugins.products.fields.name', true)) {
                        $dynamo->text('name');
                    }
                    if (config('pilot.plugins.products.fields.price', true)) {
                        $dynamo->text('price');
                    }
                    if (config('pilot.plugins.products.fields.short_description', true)) {
                        $dynamo->text('short_description');
                    }
                    if (config('pilot.plugins.products.fields.full_description', true)) {
                        $dynamo->textarea('full_description', [
                            'class' => 'wysiwyg-editor'
                        ]);
                    }
                    if (config('pilot.plugins.products.fields.featured_image', true)) {
                        $dynamo->singleImage('featured_image', [
                            'help' => 'Upload photo. Once selected, hover over the image and select the edit icon (paper & pencil) to manage metadata title, photo credit, and description.',
                            'maxWidth' => 1000,
                        ]);
                    }
                    if (config('pilot.plugins.products.fields.gallery', true)) {
                        $dynamo->gallery('gallery', [
                            'label' => 'Gallery',
                            'help' => 'Use uploader or browse option to select multiple images. Re-order images by clicking on them, dragging, and dropping to the desired order',
                        ]);
                    }
                    if (config('pilot.plugins.products.fields.categories', true)) {
                        $dynamo->hasManySimple('product_categories', [
                            'nameField' => 'title',
                            'modelClass' => ProductCategoryFacade::class,
                            'options' => ProductCategoryFacade::withoutGlobalScope(PublishedScope::class)->orderBy('title')->pluck('title', 'id'),
                            'value' => function ($item, $field) {
                                return $item->{$field->key}()->withoutGlobalScopes()->pluck('id')->toArray();
                            },
                            'label' => 'Product Categories',
                            'help' => 'Categories must already exist. If they don\'t, please save this product as a draft without assigned categories
                                          and go to the <a href="/pilot/productcategory?view=published" target="_blank">Product Category Manager</a> to create the desired category.',
                            'position' => 40,
                        ]);
                    }
                    if (config('pilot.plugins.products.fields.status', true)) {
                        $dynamo->select('status', [
                            'options' => ProductFacade::getStatuses(),
                            'help' => 'Use the \'\'Draft\'\' status to save information as you have it. When you\'re ready for a Product to
                                          show up on the front end of the website, change it to \'\'Published\'\' and then click the \'\'Save Product\'\' button.',
                            'position' => 200,
                        ]);
                    }
                    $dynamo->removeField('deleted_at');

                    /************************************************************************************
                     *  Pilot plugin: Product index view                                               *
                     *  Check the plugins 'fields' array and set the index view for this module        *
                     ************************************************************************************/
                    $dynamo->clearIndexes()
                    ->applyScopes()
                    ->paginate(25)
                    ->indexTab(IndexTab::make('Published', function ($query) {
                            return $query->where('status', 30)->whereNull('deleted_at');
                        })
                        ->setBadgeColor('blue') // default is red if you don't supply
                        ->showCount()
                    )

                    ->indexTab(IndexTab::make('Drafts', function ($query) {
                            return $query->where('status', 10)->whereNull('deleted_at');
                        })
                        ->showCount()
                    )
                    ->searchable('name')
                    ->searchOptions([
                        'placeholder' => 'Search By Name',
                    ]);
                    if (config('pilot.plugins.products.fields.featured_image', true)) {
                        $dynamo->addIndex('featured_image', 'Featured Image', function ($item) {
                            if (empty($item->featured_image)) {
                                return '';
                            }
                            return '<img style="width: 100px  " src="' . $item->featured_image__thumb . '" class="" style="width: 60px;">';
                        });
                    }
                    if (config('pilot.plugins.products.fields.name', true)) {
                        $dynamo->addIndex('name');
                    }
                    if (config('pilot.plugins.products.fields.short_description', true)) {
                        $dynamo->addIndex('short_description');
                    }
                    if (config('pilot.plugins.products.fields.categories', true)) {
                        $dynamo->addIndex('category', 'Applicable Categories', function ($item) {
                            return $item->product_categories->transform(function($cat) {
                              return $cat->title;
                            })
                            ->implode (', ');
    
                        });
                    }
                    if (config('pilot.plugins.products.fields.price', true)) {
                        $dynamo->addIndex('price');
                    }
                    $dynamo->addIndex('updated_at', 'Last Edited')
                    ->addActionButton(function($item) {
                        return '<a href="'.$item->url().'" target="_blank"  class="btn btn-secondary btn-sm">View</a>';
                    })
                    ->addActionButton(function($item) {
                        return '<a href="product/' . $item->id . '/copy"  class="btn btn-secondary btn-sm">Copy</a>';
                    })
                    ->addActionButton(function ($item) {
                        return '<a href="product/' . $item->id . '/delete" onclick="return confirm(\'Are you sure you want to delete this? This action cannot be undone and will be deleted forever.\')"  class="btn btn-secondary btn-sm">Delete</a>';
                    })
                    ->ignoredScopes([PublishedScope::class])
                    ->indexOrderBy('name');

        return $dynamo;

    }

        /**
         * Copy the Product
         *
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
        public function copy($id)
        {
            $product = ProductFacade::withoutGlobalScope(PublishedScope::class)->find($id);

            $newProduct = $product->duplicate();

            // set success message
            \Session::flash('alert-success', 'Product copied successfully!');

            return redirect()->route('admin.product.edit', array($newProduct->id));
        }

        /**
        * Remove the specified resource from storage.
        *
        * @param  int  $id
        * @return \Illuminate\Http\Response
        */
        public function destroy($id)
        {
            $product = ProductFacade::withoutGlobalScope(PublishedScope::class)->find($id);

            $product->delete();

            // set success message
            \Session::flash('alert-success', 'Product deleted successfully!');

            return \Redirect::to('/pilot/product?view=published');
        }
}
