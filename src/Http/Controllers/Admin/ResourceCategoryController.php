<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Jzpeepz\Dynamo\Dynamo;
use Illuminate\Support\Str;
use Jzpeepz\Dynamo\IndexTab;
use Flex360\Pilot\Pilot\Resource;
use Flex360\Pilot\Scopes\PublishedScope;
use Flex360\Pilot\Facades\Resource as ResourceFacade;
use Jzpeepz\Dynamo\Http\Controllers\DynamoController;
use Flex360\Pilot\Http\Controllers\Admin\ResourceController;
use Flex360\Pilot\Facades\ResourceCategory as ResourceCategoryFacade;

class ResourceCategoryController extends DynamoController
{
    public function getDynamo()
    {
        $dynamo = Dynamo::make(get_class(ResourceCategoryFacade::getFacadeRoot()));
                        // check if display_name is used, if so, use the dynamo alias function to change the name everywhere at once
                        if (config('pilot.plugins.resources.children.resource_category.display_name') != null) {
                            $dynamo->alias(Str::singular(config('pilot.plugins.resources.children.resource_category.display_name')));
                        }




                        /************************************************************************************
                         *  Pilot plugin: Resource form view                                               *
                         *  Check the plugins 'fields' array and attach the fields to the dynamo object    *
                         ************************************************************************************/
                        $dynamo->addIndexButton(function () {
                            return '<a href="/resources" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> View</a>';
                        })
                        ->addFormHeaderButton(function() {
                            return '<a href="/pilot/resourcecategory" class="btn btn-info btn-sm">Back to Resource Categories</a>';
                        })
                        ->addFormHeaderButton(function() {
                            return '<a href="/pilot/resource?view=published" class="btn btn-primary btn-sm">Back to Resources</a>';
                        })
                        ->addIndexButton(function () {
                            return '<a href="/pilot/resource?view=published" class="btn btn-info btn-sm">Back to Resources</a>';
                        })
                        ->addActionButton(function ($item) {
                            return '<a href="product/' . $item->id . '/delete" data-toggle="modal" data-target="#relationships-manager-modal" class="btn btn-secondary btn-sm">Delete</a>';
                        })
                        ->hideDelete();
                        
                        if (config('pilot.plugins.resources.children.resource_category.fields.name', true)) {
                            $dynamo->text('name');
                        }
                        if (config('pilot.plugins.resources.children.resource_category.fields.resources_selector', true)) {
                            $dynamo->hasManySimple('resources', [
                                'options' => Resource::withoutGlobalScope(PublishedScope::class)->orderBy('title')->pluck('title', 'id'),
                                'value' => function ($item, $field) {
                                    return $item->resources()->withoutGlobalScope(PublishedScope::class)->pluck('id')->toArray();
                                },
                                'class' => 'category-dual-list',
                                'id' => 'category-dual-list',
                                'help' => 'Select the Resources you would like to belong to this category.',
                            ]);
                        }
                        if (config('pilot.plugins.projects.children.manage_project_categories.fields.status', true)) {
                            $dynamo->select('status', [
                                'options' => ResourceCategoryFacade::getStatuses(),
                                'help' => 'Use the "Draft" status to save information as you have it. When you\'re ready for a Resource Category to
                                              show up on the front end of the website, change it to "Published" and then click the "Save Resource Category" button.',
                                'position' => 200,
                            ]);
                        }
                        $dynamo->removeBoth('deleted_at');
            

                        /************************************************************************************
                         *  Pilot plugin: Resource index view                                              *
                         *  Check the plugins 'fields' array and attach the fields to the dynamo object    *
                         ************************************************************************************/
                        $dynamo->paginate(25);
                        if (config('pilot.plugins.resources.children.resource_category.fields.sort_method') == 'manual_sort') {
                            $dynamo->addIndex('hamburger', 'Sort', function ($item) {
                                return '<i class="fas fa-bars fa-2x" ></i>';
                            });
                        }
                        if (config('pilot.plugins.resources.children.resource_category.fields.name', true)) {
                            $dynamo->addIndex('name');
                        }
                        if (config('pilot.plugins.resources.children.resource_category.fields.resource_sort_method') == 'manual_sort') {
                            $dynamo->addIndex('id', 'Order Resources', function ($item) {
                                return '<a href="' . route('admin.resourcecategory.resource', ['id' => $item->id]) . '" class="btn btn-success">Order</a>';
                            });
                        }
                        $dynamo->addIndex('numberOfResources', 'Number of resources in this category', function ($item) {
                            return $item->resources()->withoutGlobalScope(PublishedScope::class)->count();
                        });
                        if (config('pilot.plugins.resources.children.resource_category.fields.status', true)) {
                            $dynamo->addIndex('test', 'Published?', function ($item) {
                                return $item->status == 30 ? '<i class="far fa-check-circle fa-3x" style="color: green; padding-top: 10px;"></i>' : '<i class="far fa-times-circle fa-3x" style="color: red; padding-top: 10px;"></i>';
                            });
                        }
                        $dynamo->addActionButton(function($item) {
                            return '<a href="resourcecategory/' . $item->id . '/copy"  class="btn btn-secondary btn-sm">Copy</a>';
                        })
                        ->ignoredScopes([PublishedScope::class]);
                        if (config('pilot.plugins.resources.children.resource_category.fields.sort_method') == 'manual_sort') {
                            $dynamo->indexOrderBy('position');
                        } else {
                            $dynamo->indexOrderBy('name');
                        }

        return $dynamo;
    }

    /**
    *  Reorder ProjectCategories
    *
    * @param  int  $id
    * @return Response
    */
    public function reorderResourceCategories()
    {
        $ids = request()->input('ids');

        foreach ($ids as $position => $catID) {
            $cat = ResourceCategoryFacade::withoutGlobalScope(PublishedScope::class)->find($catID);

            $cat->position = $position;

            $cat->save();
        }

        return $ids;
    }

    public function resources($id)
    {
        $resourceCategory = ResourceCategoryFacade::withoutGlobalScope(PublishedScope::class)->find($id);

        $items = $resourceCategory->resources()->withoutGlobalScope(PublishedScope::class)->get();
        $dynamo = (new ResourceController)->getDynamo();

        return view('pilot::admin.dynamo.resources.reorder', compact('dynamo', 'items', 'resourceCategory'));
    }

    public function reorderResourcesWithinCategory($id)
    {
        $resourceCategory = ResourceCategoryFacade::withoutGlobalScope(PublishedScope::class)->find($id);

        $ids = request()->input('ids');

        foreach ($ids as $position => $resourceId) {
            $resource = ResourceFacade::withoutGlobalScope(PublishedScope::class)->find($resourceId);
            $resource->resource_categories()->updateExistingPivot($resourceCategory->id, compact('position'));
        }

        return $ids;
    }

    /**
     * Copy the Resource Category
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function copy($id)
    {
        $cat = ResourceCategoryFacade::withoutGlobalScope(PublishedScope::class)->find($id);

        $newCat = $cat->duplicate();

        // set success message
        \Session::flash('alert-success', 'Category copied successfully!');

        return redirect()->route('admin.resourcecategory.edit', array($newCat->id));
    }

    /**
     * Remove the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $category = ResourceCategoryFacade::withoutGlobalScope(PublishedScope::class)->find($id);

        $category->delete();

        // set success message
        \Session::flash('alert-success', 'Category deleted successfully!');

        return \Redirect::route('admin.resourcecategory.index');
    }
}
