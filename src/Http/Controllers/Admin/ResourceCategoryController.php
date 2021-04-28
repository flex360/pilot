<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Jzpeepz\Dynamo\Dynamo;
use Illuminate\Support\Str;
use Jzpeepz\Dynamo\IndexTab;
use Flex360\Pilot\Pilot\Resource;
use Jzpeepz\Dynamo\Http\Controllers\DynamoController;
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
                            $dynamo->hasMany('resources', [
                                'options' => Resource::orderBy('title')->pluck('title', 'id'),
                                'class' => 'category-dual-list',
                                'id' => 'category-dual-list',
                                'help' => 'Select the Resources you would like to belong to this category.',
                            ]);
                        }
                        $dynamo->removeBoth('deleted_at');
            



                        /************************************************************************************
                         *  Pilot plugin: Resource index view                                              *
                         *  Check the plugins 'fields' array and attach the fields to the dynamo object    *
                         ************************************************************************************/
                        $dynamo->paginate(25);
                        if (config('pilot.plugins.resources.children.resource_category.fields.name', true)) {
                            $dynamo->addIndex('name');
                        }
                        $dynamo->addIndex('test', 'Number of resources in this category', function ($item) {
                            return $item->resources->count();
                        })
                        ->addActionButton(function($item) {
                            return '<a href="resourcecategory/' . $item->id . '/copy"  class="btn btn-secondary btn-sm">Copy</a>';
                        })
                        ->indexOrderBy('name');

        return $dynamo;
    }

    /**
     * Copy the Resource Category
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function copy($id)
    {
        $cat = ResourceCategoryFacade::find($id);

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
        $category = ResourceCategoryFacade::find($id);

        $category->delete();

        // set success message
        \Session::flash('alert-success', 'Category deleted successfully!');

        return \Redirect::route('admin.resourcecategory.index');
    }
}
